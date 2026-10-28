<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - Access Denied</title>
    <link rel="stylesheet" href="<?= \App\Helpers\Url::asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= \App\Helpers\Url::asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= \App\Helpers\Url::asset('assets/css/app.css') ?>">
</head>
<body class="bg-slate-100 d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center p-5 card shadow-sm border-0 rounded-4" style="max-width: 480px;">
        <div class="mb-4 text-danger">
            <i class="bi bi-shield-lock-fill display-1"></i>
        </div>
        <h1 class="h2 fw-bold text-dark mb-2">403 - Access Forbidden</h1>
        <p class="text-muted mb-4"><?= !empty($message) ? htmlspecialchars($message) : "You do not have permission to access this protected area." ?></p>
        <div>
            <a href="<?= \App\Helpers\Url::to('/') ?>" class="btn btn-primary px-4 py-2 rounded-pill">
                <i class="bi bi-house-door me-1"></i> Return to Safety
            </a>
        </div>
    </div>
</body>
</html>
