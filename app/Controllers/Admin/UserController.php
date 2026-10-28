<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;
use App\Models\AuditLog;
use App\Helpers\Validator;
use App\Helpers\Session;
use App\Helpers\Auth;

class UserController extends Controller {
    public function index(): void {
        $users = User::all('id DESC');
        $this->view('admin.users.index', [
            'title' => 'User Account Management',
            'users' => $users
        ]);
    }

    public function create(): void {
        $this->view('admin.users.create', ['title' => 'Create User Account']);
    }

    public function store(): void {
        $validator = Validator::make($this->request->all(), [
            'username' => 'required|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,teacher,student',
            'status'   => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect('/admin/users/create');
        }

        $id = User::create([
            'username' => trim((string)$this->request->input('username')),
            'email'    => trim((string)$this->request->input('email')),
            'password' => password_hash((string)$this->request->input('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->input('role'),
            'status'   => $this->request->input('status')
        ]);

        AuditLog::log('USER_CREATED', Auth::id(), null, null, null, [
            'created_user_id' => $id,
            'username'        => $this->request->input('username'),
            'role'            => $this->request->input('role')
        ]);

        Session::setFlash('success', "User account '{$this->request->input('username')}' created.");
        $this->redirect('/admin/users');
    }

    public function edit(int $id): void {
        $user = User::find($id);
        if (!$user) {
            Session::setFlash('error', 'User not found.');
            $this->redirect('/admin/users');
        }

        $this->view('admin.users.edit', [
            'title' => "Edit User - {$user['username']}",
            'user'  => $user
        ]);
    }

    public function update(int $id): void {
        $user = User::find($id);
        if (!$user) {
            Session::setFlash('error', 'User not found.');
            $this->redirect('/admin/users');
        }

        $validator = Validator::make($this->request->all(), [
            'username' => "required|unique:users,username,{$id}",
            'email'    => "required|email|unique:users,email,{$id}",
            'role'     => 'required|in:admin,teacher,student',
            'status'   => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect("/admin/users/{$id}/edit");
        }

        $data = [
            'username' => trim((string)$this->request->input('username')),
            'email'    => trim((string)$this->request->input('email')),
            'role'     => $this->request->input('role'),
            'status'   => $this->request->input('status')
        ];

        // Optional password change
        $newPass = (string)$this->request->input('password');
        if (!empty($newPass)) {
            $data['password'] = password_hash($newPass, PASSWORD_DEFAULT);
        }

        User::update($id, $data);

        AuditLog::log('USER_UPDATED', Auth::id(), null, null, null, ['target_user_id' => $id]);
        Session::setFlash('success', 'User account updated successfully.');
        $this->redirect('/admin/users');
    }

    public function delete(int $id): void {
        if ($id === Auth::id()) {
            Session::setFlash('error', 'You cannot delete your own active administrator account.');
            $this->redirect('/admin/users');
        }

        $user = User::find($id);
        if ($user) {
            User::delete($id);
            AuditLog::log('USER_DELETED', Auth::id(), null, null, null, ['username' => $user['username']]);
            Session::setFlash('success', "User '{$user['username']}' deleted.");
        }
        $this->redirect('/admin/users');
    }
}
