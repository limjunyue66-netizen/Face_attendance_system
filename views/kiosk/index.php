<?php
use App\Helpers\Url;
?>
<div class="kiosk-wrapper d-flex flex-column min-vh-100">
    <!-- Kiosk Top Navigation Bar -->
    <header class="kiosk-navbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-icon-wrapper" style="width: 48px; height: 48px; font-size: 1.5rem;">
                <i class="bi bi-person-bounding-box"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-white"><?= htmlspecialchars($title) ?></h4>
                <div class="small text-light text-opacity-75"><?= htmlspecialchars($schoolName) ?> &bull; Automated Reception Station</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="kiosk-clock-card">
                <i class="bi bi-clock text-info fs-5"></i>
                <div class="kiosk-clock-time" id="kioskClock">00:00:00</div>
                <div class="small text-light text-opacity-75 d-none d-md-block ms-2 border-start ps-3" id="kioskDate"><?= date('l, M j, Y') ?></div>
            </div>

            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" onclick="toggleFullscreen()" title="Toggle Fullscreen">
                <i class="bi bi-fullscreen" id="fullscreenIcon"></i>
            </button>
            <a href="<?= Url::to('/') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-x-lg me-1"></i> Exit
            </a>
        </div>
    </header>

    <!-- Main Kiosk Body -->
    <main class="container-fluid p-4 flex-1 d-flex flex-column justify-content-center">
        <div class="row g-4 align-items-center justify-content-center">
            
            <!-- Camera Scanning Viewport Column -->
            <div class="col-xl-7 col-lg-8">
                <div class="kiosk-video-frame">
                    <!-- Live Header Badges -->
                    <div class="position-absolute top-0 start-0 w-100 p-3 d-flex justify-content-between align-items-center" style="z-index: 10;">
                        <span class="badge bg-danger rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 shadow">
                            <span class="spinner-grow spinner-grow-sm" role="status" style="width: 8px; height: 8px;"></span>
                            <span class="fw-bold tracking-wider">AI CAMERA SCANNER</span>
                        </span>
                        <div class="d-flex gap-2">
                            <span class="hud-pill" id="kioskFaceCount"><i class="bi bi-person"></i> Faces: 0</span>
                            <span class="hud-pill" id="kioskEngineState"><i class="bi bi-cpu"></i> Loading AI...</span>
                        </div>
                    </div>

                    <!-- Video & Canvas Elements -->
                    <div style="position: relative; width: 100%; aspect-ratio: 4/3; background: #000;">
                        <video id="videoElement" class="kiosk-video" autoplay muted playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                        <canvas id="overlayCanvas" class="kiosk-overlay"></canvas>
                        <div class="camera-guide" id="cameraGuide"></div>
                        <div class="scanner-laser" id="scannerLaser"></div>
                    </div>

                    <!-- Dynamic Feedback Notice -->
                    <div class="p-3 bg-dark border-top border-secondary border-opacity-25 text-center">
                        <div class="h5 fw-bold mb-1 text-info" id="kioskNoticeTitle">Please Look Directly Into The Camera</div>
                        <div class="small text-light text-opacity-75" id="kioskNoticeDesc">Attendance will automatically register upon facial identification.</div>
                    </div>
                </div>
            </div>

            <!-- Right Telemetry & Instant Result Column -->
            <div class="col-xl-5 col-lg-4">
                <!-- Result Confirmation Card (Appears on scan match) -->
                <div class="kiosk-card mb-4" id="kioskResultBox" style="display: none;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-xl bg-primary text-white flex-shrink-0" id="kioskResultAvatar">
                            <i class="bi bi-person-check fs-1"></i>
                        </div>
                        <div class="overflow-hidden">
                            <span class="badge bg-success-subtle text-success border border-success-subtle mb-1" id="kioskResultBadge">CHECKED IN</span>
                            <h4 class="fw-bold text-white mb-0 text-truncate" id="kioskResultName">Student Name</h4>
                            <div class="text-light text-opacity-75 small font-monospace" id="kioskResultCode">STU-2026-001</div>
                            <div class="small text-info mt-1" id="kioskResultConfidence">Confidence: 98.4%</div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top border-secondary border-opacity-25 small text-light text-opacity-75" id="kioskResultMessage">
                        Check-in recorded successfully.
                    </div>
                </div>

                <!-- Recent Scans Live Feed -->
                <div class="kiosk-card">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-secondary border-opacity-25 pb-2">
                        <h6 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-info"></i> Recent Attendance Feed
                        </h6>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Live Updates</span>
                    </div>

                    <div id="kioskRecentFeed" style="max-height: 380px; overflow-y: auto;">
                        <?php if (empty($recentAttendance)): ?>
                            <div class="text-center py-4 text-light text-opacity-50 small" id="kioskFeedEmpty">
                                <i class="bi bi-person-x fs-3 d-block mb-1"></i>
                                Ready for scans. No attendees logged today yet.
                            </div>
                        <?php else: ?>
                            <?php foreach ($recentAttendance as $r): ?>
                                <div class="kiosk-feed-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar bg-primary text-white" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            <?= strtoupper(substr($r['student_name'] ?? 'S', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white small"><?= htmlspecialchars($r['student_name']) ?></div>
                                            <div class="small text-light text-opacity-50" style="font-size: 0.72rem;"><?= htmlspecialchars($r['class_name'] ?? 'N/A') ?></div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge-status status-<?= $r['status'] ?>" style="font-size: 0.72rem;">
                                            <?= ucfirst(str_replace('_', ' ', $r['status'])) ?>
                                        </span>
                                        <div class="small text-light text-opacity-50 font-monospace" style="font-size: 0.7rem;"><?= htmlspecialchars($r['check_in_time'] ?? '-') ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Kiosk Footer -->
    <footer class="p-3 text-center small text-light text-opacity-50 border-top border-secondary border-opacity-10">
        <?= htmlspecialchars($schoolName) ?> &bull; Automated Attendance Verification &bull; Browser-Powered Local Face Recognition
    </footer>
</div>

<!-- Configuration Block for JS Kiosk Flow -->
<div id="kioskConfigData"
     data-models-url="<?= Url::asset('assets/models') ?>"
     data-descriptors-api="<?= Url::to('/api/face/descriptors') ?>"
     data-scan-api="<?= Url::to('/api/attendance/scan') ?>"
     data-settings-api="<?= Url::to('/api/face/settings') ?>"
     style="display: none;"></div>

<!-- Audio Feedback Synth for Kiosk (Success / Warning / Duplicate Chimes) -->
<script>
    // Live Digital Clock
    function updateKioskClock() {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('kioskClock');
        if (clockEl) clockEl.textContent = `${hrs}:${mins}:${secs}`;
    }
    setInterval(updateKioskClock, 1000);
    updateKioskClock();

    // Fullscreen Toggle
    function toggleFullscreen() {
        const icon = document.getElementById('fullscreenIcon');
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().then(() => {
                if (icon) icon.className = 'bi bi-fullscreen-exit';
            });
        } else {
            document.exitFullscreen().then(() => {
                if (icon) icon.className = 'bi bi-fullscreen';
            });
        }
    }
</script>

<!-- Face Recognition Kiosk Scripts -->
<script src="<?= Url::asset('assets/js/face/camera.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/quality.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/face-engine.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/matcher.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/kiosk-flow.js') ?>"></script>
