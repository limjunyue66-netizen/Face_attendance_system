<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<form action="<?= Url::to('/login') ?>" method="POST" id="loginForm">
    <?= Csrf::field() ?>

    <div class="mb-3">
        <label for="username" class="form-label small fw-semibold text-dark">Username or Email</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control bg-light border-start-0" id="username" name="username" placeholder="e.g. admin, teacher1, student1" required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label small fw-semibold text-dark mb-0">Password</label>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control bg-light border-start-0 border-end-0" id="password" name="password" placeholder="••••••••" required>
            <button class="btn btn-light border border-start-0 text-muted" type="button" id="togglePasswordBtn">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <div class="d-grid mt-4">
        <button type="submit" class="btn btn-primary py-2 rounded-3 fw-semibold">
            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Dashboard
        </button>
    </div>
</form>

<!-- Quick Demo Account Fill -->
<div class="mt-4 pt-3 border-top">
    <div class="text-center text-muted small mb-2 fw-medium">Quick Demo Accounts (Click to Fill)</div>
    <div class="d-flex flex-column gap-1">
        <button type="button" class="btn btn-sm btn-outline-primary text-start d-flex align-items-center justify-content-between py-1 px-2 rounded-2" onclick="fillCredentials('admin', 'admin123')">
            <span><i class="bi bi-shield-check me-1"></i> <strong>Administrator</strong></span>
            <span class="text-muted small">admin / admin123</span>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary text-start d-flex align-items-center justify-content-between py-1 px-2 rounded-2" onclick="fillCredentials('teacher1', 'teacher123')">
            <span><i class="bi bi-person-workspace me-1"></i> <strong>Teacher</strong></span>
            <span class="text-muted small">teacher1 / teacher123</span>
        </button>
        <button type="button" class="btn btn-sm btn-outline-info text-start d-flex align-items-center justify-content-between py-1 px-2 rounded-2" onclick="fillCredentials('student1', 'student123')">
            <span><i class="bi bi-mortarboard me-1"></i> <strong>Student</strong></span>
            <span class="text-muted small">student1 / student123</span>
        </button>
    </div>
</div>

<div class="text-center mt-3 pt-2">
    <a href="<?= Url::to('/kiosk') ?>" target="_blank" class="small text-decoration-none text-primary fw-medium">
        <i class="bi bi-camera-video me-1"></i> Open Attendance Kiosk Station
    </a>
    <span class="mx-2 text-muted">&bull;</span>
    <a href="<?= Url::to('/privacy') ?>" class="small text-decoration-none text-muted">
        <i class="bi bi-shield-lock me-1"></i> Privacy Policy
    </a>
</div>

<script>
    function fillCredentials(user, pass) {
        document.getElementById('username').value = user;
        document.getElementById('password').value = pass;
    }

    document.getElementById('togglePasswordBtn')?.addEventListener('click', function() {
        const passInput = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passInput.type === 'password') {
            passInput.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            passInput.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });
</script>
