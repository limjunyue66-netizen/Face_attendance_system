<?php
use App\Helpers\Url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= !empty($title) ? htmlspecialchars($title) : 'Sign In - Face Attendance System' ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232563eb'><path d='M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 14.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-4.5a1 1 0 0 1-2 0V7a1 1 0 0 1 2 0z'/></svg>">
    
    <link rel="stylesheet" href="<?= Url::asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= Url::asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= Url::asset('assets/css/app.css') ?>">
    <style>
        body.auth-bg {
            background: radial-gradient(circle at 50% 10%, #1e293b 0%, #0f172a 60%, #020617 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            position: relative;
            overflow-x: hidden;
        }
        body.auth-bg::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.18) 0%, transparent 70%);
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            pointer-events: none;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            position: relative;
            z-index: 10;
        }
        .auth-header {
            padding: 2.25rem 2.25rem 1.25rem 2.25rem;
            text-align: center;
        }
        .auth-body {
            padding: 0 2.25rem 2.25rem 2.25rem;
        }
    </style>
</head>
<body class="auth-bg">
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-icon-wrapper mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.75rem;">
                <i class="bi bi-person-bounding-box"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.03em;">FacePass Portal</h3>
            <p class="text-muted small mb-0">Biometric Attendance & School Management</p>
        </div>

        <div class="auth-body">
            <?php if (!empty($flashSuccess)): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 small mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <div><?= htmlspecialchars($flashSuccess) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($flashError)): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                    <div><?= htmlspecialchars($flashError) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </div>
    </div>

    <script src="<?= Url::asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
