<?php
use App\Helpers\Url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= !empty($title) ? htmlspecialchars($title) : 'Attendance Kiosk - Face Recognition' ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2338bdf8'><path d='M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 14.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-4.5a1 1 0 0 1-2 0V7a1 1 0 0 1 2 0z'/></svg>">
    
    <!-- Offline CSS -->
    <link rel="stylesheet" href="<?= Url::asset('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= Url::asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= Url::asset('assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= Url::asset('assets/css/kiosk.css') ?>">
</head>
<body class="kiosk-mode">
    <?= $content ?>

    <!-- Offline Vendor & Face API Libraries -->
    <script src="<?= Url::asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= Url::asset('assets/vendor/face-api/face-api.min.js') ?>"></script>
</body>
</html>
