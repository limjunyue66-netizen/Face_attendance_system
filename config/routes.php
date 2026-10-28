<?php

use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\TeacherMiddleware;
use App\Middleware\StudentMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Helpers\Auth;
use App\Core\Response;

return function(Router $router) {
    // Root redirect
    $rootHandler = function() {
        if (!Auth::check()) {
            Response::redirect('/login');
        }
        $role = Auth::role();
        if ($role === 'admin') Response::redirect('/admin/dashboard');
        if ($role === 'teacher') Response::redirect('/teacher/dashboard');
        Response::redirect('/student/dashboard');
    };
    $router->get('/', $rootHandler);
    $router->get('/index.php', $rootHandler);

    // Public Kiosk & Privacy
    $router->get('/kiosk', 'KioskController@index');
    $router->get('/privacy', 'PrivacyController@index');

    // APIs (Face matching & Attendance scan)
    $router->get('/api/face/descriptors', 'FaceController@getDescriptors');
    $router->get('/api/face/settings', 'FaceController@getSettings');
    $router->post('/api/face/register', 'FaceController@register');
    $router->post('/api/attendance/scan', 'KioskController@scan');

    // Guest Auth routes
    $router->group(['middleware' => [GuestMiddleware::class]], function(Router $r) {
        $r->get('/login', 'AuthController@showLogin');
        $r->post('/login', 'AuthController@login', [CsrfMiddleware::class]);
    });

    // Authenticated generic routes
    $router->group(['middleware' => [AuthMiddleware::class]], function(Router $r) {
        $r->get('/logout', 'AuthController@logout');
        $r->get('/change-password', 'AuthController@showChangePassword');
        $r->post('/change-password', 'AuthController@changePassword', [CsrfMiddleware::class]);
    });

    // ==========================================
    // Administrator Portal Routes
    // ==========================================
    $router->group(['middleware' => [AuthMiddleware::class, AdminMiddleware::class]], function(Router $r) {
        $r->get('/admin/dashboard', 'Admin\DashboardController@index');

        // Students
        $r->get('/admin/students', 'Admin\StudentController@index');
        $r->get('/admin/students/create', 'Admin\StudentController@create');
        $r->post('/admin/students', 'Admin\StudentController@store', [CsrfMiddleware::class]);
        $r->get('/admin/students/export', 'Admin\StudentController@export');
        $r->get('/admin/students/{id}', 'Admin\StudentController@show');
        $r->get('/admin/students/{id}/edit', 'Admin\StudentController@edit');
        $r->post('/admin/students/{id}', 'Admin\StudentController@update', [CsrfMiddleware::class]);
        $r->post('/admin/students/{id}/delete', 'Admin\StudentController@delete', [CsrfMiddleware::class]);
        $r->get('/admin/students/{id}/face-register', 'Admin\StudentController@faceRegister');

        // Teachers
        $r->get('/admin/teachers', 'Admin\TeacherController@index');
        $r->get('/admin/teachers/create', 'Admin\TeacherController@create');
        $r->post('/admin/teachers', 'Admin\TeacherController@store', [CsrfMiddleware::class]);
        $r->get('/admin/teachers/{id}/edit', 'Admin\TeacherController@edit');
        $r->post('/admin/teachers/{id}', 'Admin\TeacherController@update', [CsrfMiddleware::class]);
        $r->post('/admin/teachers/{id}/delete', 'Admin\TeacherController@delete', [CsrfMiddleware::class]);

        // Classes
        $r->get('/admin/classes', 'Admin\ClassController@index');
        $r->get('/admin/classes/create', 'Admin\ClassController@create');
        $r->post('/admin/classes', 'Admin\ClassController@store', [CsrfMiddleware::class]);
        $r->get('/admin/classes/{id}/edit', 'Admin\ClassController@edit');
        $r->post('/admin/classes/{id}', 'Admin\ClassController@update', [CsrfMiddleware::class]);
        $r->post('/admin/classes/{id}/delete', 'Admin\ClassController@delete', [CsrfMiddleware::class]);

        // Subjects
        $r->get('/admin/subjects', 'Admin\SubjectController@index');
        $r->get('/admin/subjects/create', 'Admin\SubjectController@create');
        $r->post('/admin/subjects', 'Admin\SubjectController@store', [CsrfMiddleware::class]);
        $r->get('/admin/subjects/{id}/edit', 'Admin\SubjectController@edit');
        $r->post('/admin/subjects/{id}', 'Admin\SubjectController@update', [CsrfMiddleware::class]);
        $r->post('/admin/subjects/{id}/delete', 'Admin\SubjectController@delete', [CsrfMiddleware::class]);

        // Assignments
        $r->get('/admin/assignments', 'Admin\AssignmentController@index');
        $r->post('/admin/assignments/class-subject', 'Admin\AssignmentController@storeClassSubject', [CsrfMiddleware::class]);
        $r->post('/admin/assignments/class-subject/{id}/delete', 'Admin\AssignmentController@deleteClassSubject', [CsrfMiddleware::class]);
        $r->post('/admin/assignments/teacher', 'Admin\AssignmentController@storeTeacherAssignment', [CsrfMiddleware::class]);
        $r->post('/admin/assignments/teacher/{id}/delete', 'Admin\AssignmentController@deleteTeacherAssignment', [CsrfMiddleware::class]);

        // Attendance Records
        $r->get('/admin/attendance', 'Admin\AttendanceController@index');
        $r->get('/admin/attendance/rest', 'Admin\AttendanceController@rest');
        $r->get('/admin/attendance/export', 'Admin\AttendanceController@export');

        // Leave Applications
        $r->get('/admin/leaves', 'Admin\LeaveController@index');
        $r->get('/admin/leaves/{id}', 'Admin\LeaveController@show');
        $r->post('/admin/leaves/{id}/review', 'Admin\LeaveController@review', [CsrfMiddleware::class]);

        // Settings
        $r->get('/admin/settings', 'Admin\SettingsController@index');
        $r->post('/admin/settings', 'Admin\SettingsController@update', [CsrfMiddleware::class]);

        // Audit Logs
        $r->get('/admin/audit', 'Admin\AuditController@index');

        // User Accounts
        $r->get('/admin/users', 'Admin\UserController@index');
        $r->get('/admin/users/create', 'Admin\UserController@create');
        $r->post('/admin/users', 'Admin\UserController@store', [CsrfMiddleware::class]);
        $r->get('/admin/users/{id}/edit', 'Admin\UserController@edit');
        $r->post('/admin/users/{id}', 'Admin\UserController@update', [CsrfMiddleware::class]);
        $r->post('/admin/users/{id}/delete', 'Admin\UserController@delete', [CsrfMiddleware::class]);
    });

    // ==========================================
    // Teacher Portal Routes
    // ==========================================
    $router->group(['middleware' => [AuthMiddleware::class, TeacherMiddleware::class]], function(Router $r) {
        $r->get('/teacher/dashboard', 'Teacher\DashboardController@index');
        $r->get('/teacher/classes', 'Teacher\AttendanceController@classes');
        $r->get('/teacher/attendance', 'Teacher\AttendanceController@index');
        $r->get('/teacher/students/{id}', 'Teacher\AttendanceController@studentDetails');
    });

    // ==========================================
    // Student Portal Routes
    // ==========================================
    $router->group(['middleware' => [AuthMiddleware::class, StudentMiddleware::class]], function(Router $r) {
        $r->get('/student/dashboard', 'Student\DashboardController@index');
        $r->get('/student/profile', 'Student\ProfileController@index');
        $r->get('/student/face-register', 'Student\ProfileController@faceRegister');
        $r->get('/student/scan', 'Student\AttendanceController@scan');
        $r->get('/student/history', 'Student\AttendanceController@history');
        $r->get('/student/statistics', 'Student\AttendanceController@statistics');
        $r->get('/student/leaves', 'Student\LeaveController@index');
        $r->post('/student/leaves', 'Student\LeaveController@store', [CsrfMiddleware::class]);
    });
};
