<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="stat-icon primary">
            <i class="bi bi-camera-video-fill"></i>
        </div>
        <div>
            <h3 class="fw-bold text-dark mb-0">Student Face Enrollment</h3>
            <p class="text-muted small mb-0">Self-service facial biometric registration for attendance scanning</p>
        </div>
    </div>
    <a href="<?= Url::to('/student/profile') ?>" class="btn btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Back to Profile
    </a>
</div>

<div class="row g-4 justify-content-center">
    <!-- Camera Viewport -->
    <div class="col-lg-7">
        <div class="card card-custom p-3 bg-dark border-0 shadow-lg text-white">
            <div class="d-flex align-items-center justify-content-between mb-2 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger d-inline-flex align-items-center gap-1">
                        <span class="spinner-grow spinner-grow-sm" role="status" style="width: 8px; height: 8px;"></span> CAMERA ACTIVE
                    </span>
                    <span class="small text-light font-monospace" id="cameraStatus">Starting camera...</span>
                </div>
                <div class="d-flex gap-1">
                    <span class="hud-pill" id="hudBrightness"><i class="bi bi-brightness-high"></i> Light: --</span>
                    <span class="hud-pill" id="hudSharpness"><i class="bi bi-eye"></i> Sharp: --</span>
                </div>
            </div>

            <div class="camera-container" id="cameraContainer" style="min-height: 400px;">
                <video id="videoElement" class="camera-video" autoplay muted playsinline></video>
                <canvas id="overlayCanvas" class="camera-overlay-canvas"></canvas>
                <div class="camera-guide" id="cameraGuide"></div>
                <div class="scanner-laser" id="scannerLaser" style="display: none;"></div>
            </div>

            <div class="mt-3 p-3 rounded-3 text-center" id="feedbackBanner" style="background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(255, 255, 255, 0.1);">
                <div class="fw-bold fs-6 mb-1 text-info" id="feedbackTitle">Position your face within the guide</div>
                <div class="small text-light text-opacity-75" id="feedbackDesc">Ensure steady lighting and remove sunglasses or obscuring headwear.</div>
            </div>
        </div>
    </div>

    <!-- Steps & Control -->
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-person-bounding-box text-primary"></i> 3-Sample Face Capture
            </h5>

            <p class="text-muted small">
                The browser will automatically verify lighting, sharpness, and stability, then record 3 samples to build your profile.
            </p>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center small mb-1">
                    <span class="fw-semibold text-dark">Enrollment Progress:</span>
                    <span class="fw-bold text-primary" id="progressText">0 / 3 Samples</span>
                </div>
                <div class="progress" style="height: 10px; border-radius: 999px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="progressBar" role="progressbar" style="width: 0%;"></div>
                </div>
            </div>

            <!-- Samples Containers -->
            <div class="mb-4">
                <label class="form-label small fw-semibold text-dark mb-2">Captured Samples:</label>
                <div class="row g-2 text-center" id="samplesContainer">
                    <div class="col-4">
                        <div class="border rounded-3 p-2 bg-light sample-box" id="sampleBox1" style="height: 100px; display:flex; align-items:center; justify-content:center;">
                            <span class="text-muted small">Sample 1</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded-3 p-2 bg-light sample-box" id="sampleBox2" style="height: 100px; display:flex; align-items:center; justify-content:center;">
                            <span class="text-muted small">Sample 2</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded-3 p-2 bg-light sample-box" id="sampleBox3" style="height: 100px; display:flex; align-items:center; justify-content:center;">
                            <span class="text-muted small">Sample 3</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-auto">
                <button type="button" class="btn btn-primary py-2 rounded-pill fw-semibold" id="startCaptureBtn">
                    <i class="bi bi-camera me-1"></i> Start Biometric Capture
                </button>
                <button type="button" class="btn btn-success py-2 rounded-pill fw-bold shadow-sm" id="manualSnapBtn" style="display: none;">
                    <i class="bi bi-camera-fill me-1"></i> Take Photo Now (<span id="snapCountText">1</span>/3)
                </button>
                <button type="button" class="btn btn-outline-secondary py-2 rounded-pill" id="resetCaptureBtn" style="display: none;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Retake Samples
                </button>
            </div>
        </div>
    </div>
</div>

<div id="faceConfigData" 
     data-student-id="<?= $student['id'] ?>"
     data-student-name="<?= htmlspecialchars($student['full_name']) ?>"
     data-models-url="<?= Url::asset('assets/models') ?>"
     data-register-api="<?= Url::to('/api/face/register') ?>"
     data-settings-api="<?= Url::to('/api/face/settings') ?>"
     data-redirect-url="<?= Url::to('/student/profile') ?>"
     style="display: none;"></div>

<script src="<?= Url::asset('assets/vendor/face-api/face-api.min.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/camera.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/quality.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/face-engine.js') ?>"></script>
<script src="<?= Url::asset('assets/js/face/registration-flow.js') ?>"></script>
