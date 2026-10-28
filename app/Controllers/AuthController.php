<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\AuditLog;
use App\Helpers\Auth;
use App\Helpers\Validator;
use App\Helpers\Session;

class AuthController extends Controller {
    public function showLogin(): void {
        $this->view('auth.login', ['title' => 'Sign In - Face Attendance System'], 'auth');
    }

    public function login(): void {
        $validator = Validator::make($this->request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect('/login');
        }

        $username = trim((string)$this->request->input('username'));
        $password = (string)$this->request->input('password');

        $user = User::findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            AuditLog::log('LOGIN_FAILED', null, null, null, null, ['identifier' => $username]);
            Session::setFlash('error', 'Invalid username/email or password.');
            $this->redirect('/login');
        }

        if ($user['status'] !== 'active') {
            Session::setFlash('error', 'Your account has been deactivated. Please contact an administrator.');
            $this->redirect('/login');
        }

        // Login successful
        Auth::login($user);
        AuditLog::log('LOGIN_SUCCESS', $user['id'], null, null, null, ['role' => $user['role']]);

        Session::setFlash('success', "Welcome back, {$user['username']}!");

        // Redirect based on role
        if ($user['role'] === 'admin') {
            $this->redirect('/admin/dashboard');
        } elseif ($user['role'] === 'teacher') {
            $this->redirect('/teacher/dashboard');
        } else {
            $this->redirect('/student/dashboard');
        }
    }

    public function logout(): void {
        $userId = Auth::id();
        if ($userId) {
            AuditLog::log('LOGOUT', $userId);
        }
        Auth::logout();
        Session::setFlash('info', 'You have been successfully signed out.');
        $this->redirect('/login');
    }

    public function showChangePassword(): void {
        $this->view('auth.change_password', ['title' => 'Change Password']);
    }

    public function changePassword(): void {
        $validator = Validator::make($this->request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|min:6',
            'confirm_password' => 'required'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect('/change-password');
        }

        $currentPass = (string)$this->request->input('current_password');
        $newPass = (string)$this->request->input('new_password');
        $confirmPass = (string)$this->request->input('confirm_password');

        if ($newPass !== $confirmPass) {
            Session::setFlash('error', 'New password and confirmation do not match.');
            $this->redirect('/change-password');
        }

        $user = Auth::user();
        if (!password_verify($currentPass, $user['password'])) {
            Session::setFlash('error', 'Current password entered is incorrect.');
            $this->redirect('/change-password');
        }

        User::update($user['id'], [
            'password' => password_hash($newPass, PASSWORD_DEFAULT)
        ]);

        AuditLog::log('PASSWORD_CHANGED', $user['id']);
        Session::setFlash('success', 'Your password has been updated successfully.');

        if ($user['role'] === 'admin') {
            $this->redirect('/admin/dashboard');
        } elseif ($user['role'] === 'teacher') {
            $this->redirect('/teacher/dashboard');
        } else {
            $this->redirect('/student/dashboard');
        }
    }
}
