<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Face Recognition & Quality Parameters</h3>
        <p class="text-muted small mb-0">Fine-tune biometric detection sensitivity, threshold matching, anti-spoofing, and scan intervals</p>
    </div>
</div>

<form action="<?= Url::to('/admin/settings') ?>" method="POST">
    <?= Csrf::field() ?>

    <div class="row g-4">
        <!-- Card 1: Camera Face Quality Checks -->
        <div class="col-lg-6">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-camera-fill text-primary"></i> 1. Frame Quality & Positioning Checks
                </h5>
                <p class="text-muted small">
                    Frames falling outside these boundaries will trigger immediate user-guidance warnings (e.g. "Lighting is too low", "Image too blurry", "Move closer").
                </p>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-dark">Min Face Size (px)</label>
                        <input type="number" name="min_face_size" class="form-control" value="<?= htmlspecialchars($settings['min_face_size'] ?? '80') ?>" min="40" max="300" required>
                        <div class="form-text small">Rejects faces positioned too far away.</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-dark">Max Face Size (px)</label>
                        <input type="number" name="max_face_size" class="form-control" value="<?= htmlspecialchars($settings['max_face_size'] ?? '450') ?>" min="200" max="800" required>
                        <div class="form-text small">Rejects faces positioned too close.</div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-dark">Min Brightness (0-255)</label>
                        <input type="number" name="min_brightness" class="form-control" value="<?= htmlspecialchars($settings['min_brightness'] ?? '40') ?>" min="0" max="100" required>
                        <div class="form-text small">Ensures adequate lighting conditions.</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-dark">Max Brightness (0-255)</label>
                        <input type="number" name="max_brightness" class="form-control" value="<?= htmlspecialchars($settings['max_brightness'] ?? '220') ?>" min="180" max="255" required>
                        <div class="form-text small">Filters harsh overexposure glare.</div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-dark">Min Sharpness Score</label>
                        <input type="number" name="min_sharpness" class="form-control" value="<?= htmlspecialchars($settings['min_sharpness'] ?? '20') ?>" min="5" max="100" required>
                        <div class="form-text small">Filters motion blur and unfocused frames.</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-dark">Detection Confidence</label>
                        <input type="number" step="0.05" name="detection_confidence" class="form-control" value="<?= htmlspecialchars($settings['detection_confidence'] ?? '0.50') ?>" min="0.1" max="0.99" required>
                        <div class="form-text small">TinyFaceDetector minimum score.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Recognition Matching & Anti-Duplicate Parameters -->
        <div class="col-lg-6">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock text-success"></i> 2. Matching & Scan Protection
                </h5>
                <p class="text-muted small">
                    Control biometric vector distance metrics and prevent repeated accidental attendance scans.
                </p>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-dark">Face Match Threshold (Distance)</label>
                        <input type="number" step="0.01" name="face_match_threshold" class="form-control" value="<?= htmlspecialchars($settings['face_match_threshold'] ?? '0.55') ?>" min="0.30" max="0.75" required>
                        <div class="form-text small">Euclidean distance cutoff (Lower = stricter match).</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-dark">Min Attendance Confidence</label>
                        <input type="number" step="0.05" name="min_attendance_confidence" class="form-control" value="<?= htmlspecialchars($settings['min_attendance_confidence'] ?? '0.45') ?>" min="0.30" max="0.95" required>
                        <div class="form-text small">Minimum similarity required to save record.</div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-dark">Stable Frame Count</label>
                        <input type="number" name="stable_frame_count" class="form-control" value="<?= htmlspecialchars($settings['stable_frame_count'] ?? '3') ?>" min="1" max="10" required>
                        <div class="form-text small">Consecutive good frames before capture.</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small fw-semibold text-dark">Registration Samples</label>
                        <input type="number" name="registration_samples" class="form-control" value="<?= htmlspecialchars($settings['registration_samples'] ?? '3') ?>" min="1" max="5" required>
                        <div class="form-text small">Descriptors collected during enrollment.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">Duplicate Scan Interval (Seconds)</label>
                    <input type="number" name="duplicate_scan_interval" class="form-control" value="<?= htmlspecialchars($settings['duplicate_scan_interval'] ?? '300') ?>" min="10" max="3600" required>
                    <div class="form-text small">Default 300 seconds (5 mins) cooldown to prevent duplicate repeated scans.</div>
                </div>
            </div>
        </div>

        <!-- Card 3: Attendance Kiosk Customization -->
        <div class="col-12">
            <div class="card card-custom p-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-display text-info"></i> 3. Kiosk Station Display Branding
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">Kiosk Screen Header Title</label>
                        <input type="text" name="kiosk_title" class="form-control" value="<?= htmlspecialchars($settings['kiosk_title'] ?? 'EduVision Facial Attendance Kiosk') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-dark">Institution / Campus Name</label>
                        <input type="text" name="kiosk_school_name" class="form-control" value="<?= htmlspecialchars($settings['kiosk_school_name'] ?? 'Metropolitan Institute of Technology') ?>" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 shadow-sm fw-semibold">
            <i class="bi bi-check-circle me-1"></i> Save Configuration Settings
        </button>
    </div>
</form>
