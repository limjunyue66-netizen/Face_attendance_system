<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="stat-icon primary">
            <i class="bi bi-person-bounding-box"></i>
        </div>
        <div>
            <h3 class="fw-bold text-dark mb-0">Biometric Attendance Scanner</h3>
            <p class="text-muted small mb-0">Align your face with the camera to record today's check-in or check-out</p>
        </div>
    </div>
    <a href="<?= Url::to('/student/dashboard') ?>" class="btn btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Dashboard
    </a>
</div>

<div class="row g-4 justify-content-center">
    <!-- Camera Section -->
    <div class="col-lg-7">
        <div class="card card-custom p-3 bg-dark border-0 shadow-lg text-white">
            <div class="d-flex align-items-center justify-content-between mb-2 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger d-inline-flex align-items-center gap-1">
                        <span class="spinner-grow spinner-grow-sm" role="status" style="width: 8px; height: 8px;"></span> SCANNER LIVE
                    </span>
                    <span class="small text-light font-monospace" id="scannerStatus">Starting scanner...</span>
                </div>
                <div class="d-flex gap-1">
                    <span class="hud-pill" id="scanLightIndicator"><i class="bi bi-brightness-high"></i> Light: --</span>
                </div>
            </div>

            <!-- Viewport -->
            <div class="camera-container" id="cameraContainer" style="min-height: 400px;">
                <video id="videoElement" class="camera-video" autoplay muted playsinline></video>
                <canvas id="overlayCanvas" class="camera-overlay-canvas"></canvas>
                <div class="camera-guide" id="cameraGuide"></div>
                <div class="scanner-laser" id="scannerLaser"></div>
            </div>

            <!-- Live Feedback Banner -->
            <div class="mt-3 p-3 rounded-3 text-center" id="scanFeedbackBanner" style="background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(255, 255, 255, 0.1);">
                <div class="fw-bold fs-6 mb-1 text-info" id="scanFeedbackTitle">Look into the camera frame</div>
                <div class="small text-light text-opacity-75" id="scanFeedbackDesc">Hold steady. The AI engine will automatically identify and verify your attendance.</div>
            </div>
        </div>
    </div>

    <!-- Right Side Status & Confirmation Panel -->
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-calendar2-check text-success"></i> Today's Attendance Record
            </h5>

            <div class="mb-4" id="attendanceStatusCard">
                <?php if (!empty($todayAttendance)): ?>
                    <div class="alert alert-success border-0 rounded-3 p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                            <div>
                                <strong class="d-block text-success">Attendance Recorded Today</strong>
                                <span class="small text-muted">Status: <?= ucfirst(str_replace('_', ' ', $todayAttendance['status'])) ?></span>
                            </div>
                        </div>
                        <div class="small text-dark mt-2 pt-2 border-top">
                            <div><i class="bi bi-box-arrow-in-right text-muted me-1"></i> Check In: <strong><?= $todayAttendance['check_in_time'] ?></strong></div>
                            <?php if (!empty($todayAttendance['check_out_time'])): ?>
                                <div><i class="bi bi-box-arrow-right text-muted me-1"></i> Check Out: <strong><?= $todayAttendance['check_out_time'] ?></strong></div>
                            <?php else: ?>
                                <div class="text-muted mt-1"><i class="bi bi-arrow-clockwise me-1"></i> Ready for check-out after duplicate cooldown.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-3 bg-light rounded-3 text-center border">
                        <i class="bi bi-clock-history fs-3 text-muted d-block mb-1"></i>
                        <span class="text-muted small">No check-in recorded for today yet. Scan your face now to check in.</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Real-time Scan Result Pop-in -->
            <div id="realtimeResult" style="display: none;" class="p-3 rounded-3 border mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-lg bg-success text-white" id="resultAvatar">
                        <i class="bi bi-check-lg fs-3"></i>
                    </div>
                    <div>
                        <div class="h6 fw-bold text-dark mb-0" id="resultName">-</div>
                        <div class="small text-success fw-semibold" id="resultTypeMessage">-</div>
                        <div class="small text-muted" id="resultConfidence">-</div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info border-0 rounded-3 small mt-auto">
                <i class="bi bi-info-circle me-1"></i>
                <strong>Notice:</strong> Consecutive scans within 5 minutes will show duplicate scan notification. Rest break attendance is automatically applied during scheduled lunch recess.
            </div>
        </div>
    </div>
</div>

<!-- Configuration Block for JS -->
<div id="attendanceConfigData"
     data-student-id="<?= $student['id'] ?>"
     data-student-name="<?= htmlspecialchars($student['full_name']) ?>"
     data-models-url="<?= Url::asset('assets/models') ?>"
     data-descriptors-api="<?= Url::to('/api/face/descriptors') ?>"
     data-scan-api="<?= Url::to('/api/attendance/scan') ?>"
     data-settings-api="<?= Url::to('/api/face/settings') ?>"
     style="display: none;"></div>

<!-- JS Modules -->
<script src="<?= Url::asset('assets/vendor/face-api/face-api.min.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/camera.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/quality.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/face-engine.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/matcher.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/attendance-flow.js') ?>"></script>
