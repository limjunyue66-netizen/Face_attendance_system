/**
 * Face Attendance System - Registration Flow Controller
 * Handles camera capture loop, quality validation, multi-sample collection, and backend storage.
 */

document.addEventListener('DOMContentLoaded', async () => {
    const configEl = document.getElementById('faceConfigData');
    if (!configEl) return;

    const studentId = parseInt(configEl.dataset.studentId);
    const modelsUrl = configEl.dataset.modelsUrl;
    const registerApi = configEl.dataset.registerApi;
    const settingsApi = configEl.dataset.settingsApi;
    const redirectUrl = configEl.dataset.redirectUrl;

    const video = document.getElementById('videoElement');
    const canvas = document.getElementById('overlayCanvas');
    const cameraGuide = document.getElementById('cameraGuide');
    const scannerLaser = document.getElementById('scannerLaser');
    const startBtn = document.getElementById('startCaptureBtn');
    const resetBtn = document.getElementById('resetCaptureBtn');
    const manualSnapBtn = document.getElementById('manualSnapBtn');
    const snapCountText = document.getElementById('snapCountText');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const feedbackTitle = document.getElementById('feedbackTitle');
    const feedbackDesc = document.getElementById('feedbackDesc');
    const cameraStatus = document.getElementById('cameraStatus');
    const hudBrightness = document.getElementById('hudBrightness');
    const hudSharpness = document.getElementById('hudSharpness');

    const camera = new CameraManager(video, canvas);
    const engine = new FaceEngine(modelsUrl);
    const qualityChecker = new FaceQualityChecker();

    let isCapturing = false;
    let targetSamples = 3;
    let collectedSamples = [];
    let previewImageBase64 = null;
    let animationFrameId = null;
    let latestDetection = null;
    let latestQuality = null;

    // Fetch dynamic quality settings
    try {
        const resp = await fetch(settingsApi);
        const data = await resp.json();
        if (data.success && data.settings) {
            qualityChecker.updateSettings(data.settings);
            if (data.settings.registration_samples) {
                targetSamples = parseInt(data.settings.registration_samples);
            }
        }
    } catch (e) {
        console.warn('Using default quality settings');
    }

    // Initialize Camera and FaceEngine
    try {
        cameraStatus.textContent = 'Starting camera stream...';
        await camera.start();
        cameraStatus.textContent = 'Loading AI face models...';
        await engine.loadModels();
        cameraStatus.textContent = 'Camera & AI Ready';
        startBtn.disabled = false;
    } catch (err) {
        cameraStatus.textContent = 'Error: ' + err.message;
        updateFeedback('Camera or AI Error', err.message, 'danger');
        return;
    }

    startBtn.addEventListener('click', () => {
        if (!isCapturing) {
            startRegistration();
        }
    });

    resetBtn.addEventListener('click', () => {
        resetRegistration();
    });

    if (manualSnapBtn) {
        manualSnapBtn.addEventListener('click', async () => {
            if (!isCapturing) return;
            if (latestDetection) {
                await processSampleCapture(latestDetection, latestQuality || { metrics: { brightness: 120, sharpness: 10 }, faceSize: 150 });
            } else {
                updateFeedback('Position Face in Frame', 'Please ensure your face is clearly visible to the camera.', 'warning');
            }
        });
    }

    function startRegistration() {
        isCapturing = true;
        collectedSamples = [];
        startBtn.style.display = 'none';
        if (manualSnapBtn) {
            manualSnapBtn.style.display = 'inline-block';
            updateManualSnapText();
        }
        resetBtn.style.display = 'inline-block';
        if (scannerLaser) scannerLaser.style.display = 'block';
        updateProgress();
        loop();
    }

    function resetRegistration() {
        isCapturing = false;
        collectedSamples = [];
        latestDetection = null;
        if (animationFrameId) cancelAnimationFrame(animationFrameId);
        engine.clearOverlay(canvas);
        startBtn.style.display = 'inline-block';
        startBtn.disabled = false;
        startBtn.innerHTML = '<i class="bi bi-camera me-1"></i> Start Biometric Capture';
        if (manualSnapBtn) manualSnapBtn.style.display = 'none';
        resetBtn.style.display = 'none';
        if (scannerLaser) scannerLaser.style.display = 'none';
        cameraGuide.className = 'camera-guide';

        // Clear preview sample boxes
        for (let i = 1; i <= 3; i++) {
            const box = document.getElementById(`sampleBox${i}`);
            if (box) box.innerHTML = `<span class="text-muted small">Sample ${i}</span>`;
        }
        updateProgress();
        updateFeedback('Position your face inside the oval guide', 'Look directly at the camera with clear lighting.', 'info');
    }

    function updateManualSnapText() {
        if (snapCountText) {
            snapCountText.textContent = Math.min(collectedSamples.length + 1, targetSamples);
        }
    }

    async function loop() {
        if (!isCapturing) return;

        try {
            // Detect single face with landmarks & descriptor
            const detection = await engine.detectSingleFace(video);

            if (!detection) {
                latestDetection = null;
                latestQuality = null;
                cameraGuide.className = 'camera-guide';
                engine.clearOverlay(canvas);
                updateFeedback('No Face Detected', 'Please step into the camera view and align inside the guide.', 'warning');
                animationFrameId = requestAnimationFrame(loop);
                return;
            }

            latestDetection = detection;

            // Draw bounding box
            const dims = { width: video.videoWidth, height: video.videoHeight };
            engine.drawDetectionOverlay(canvas, detection, dims);

            // Check Quality
            const quality = qualityChecker.checkQuality(detection, video);
            latestQuality = quality;

            if (quality.metrics) {
                if (hudBrightness) hudBrightness.innerHTML = `<i class="bi bi-brightness-high"></i> Light: ${quality.metrics.brightness}`;
                if (hudSharpness) hudSharpness.innerHTML = `<i class="bi bi-eye"></i> Sharp: ${quality.metrics.sharpness}`;
            }

            if (!quality.passed) {
                cameraGuide.className = 'camera-guide warning';
                updateFeedback('Quality Adjustment Required', quality.message, 'warning');
                animationFrameId = requestAnimationFrame(loop);
                return;
            }

            // Pose orientation check
            if (typeof PoseEstimator !== 'undefined') {
                const pose = PoseEstimator.isFrontFacing(detection.landmarks);
                if (!pose.isFront) {
                    cameraGuide.className = 'camera-guide warning';
                    updateFeedback('Orientation Check', pose.reason, 'warning');
                    animationFrameId = requestAnimationFrame(loop);
                    return;
                }
            }

            // Passed all checks! Auto capture sample
            await processSampleCapture(detection, quality);
            if (!isCapturing) return;

            // Brief delay between samples so user has slight movement/expression variation
            await new Promise(r => setTimeout(r, 600));
        } catch (err) {
            console.error('Detection loop error:', err);
        }

        if (isCapturing) {
            animationFrameId = requestAnimationFrame(loop);
        }
    }

    async function processSampleCapture(detection, quality) {
        if (!isCapturing) return;
        if (collectedSamples.length >= targetSamples) return;

        cameraGuide.className = 'camera-guide active';
        updateFeedback('Sample Captured', `Recording sample ${collectedSamples.length + 1} of ${targetSamples}...`, 'success');

        const descriptorArray = Array.from(detection.descriptor);
        collectedSamples.push({
            descriptor: descriptorArray,
            brightness: quality.metrics?.brightness || 120,
            sharpness: quality.metrics?.sharpness || 10,
            face_size: quality.faceSize || 150
        });

        // If first sample, save as student preview photo
        if (collectedSamples.length === 1) {
            previewImageBase64 = camera.captureFrameBase64();
        }

        renderSampleThumbnail(collectedSamples.length);
        updateProgress();
        updateManualSnapText();

        if (collectedSamples.length >= targetSamples) {
            // All samples collected!
            isCapturing = false;
            if (manualSnapBtn) manualSnapBtn.style.display = 'none';
            if (scannerLaser) scannerLaser.style.display = 'none';
            updateFeedback('Processing Registration', 'Uploading biometric descriptors to secure database...', 'info');
            await submitRegistration();
        }
    }

    function renderSampleThumbnail(sampleNumber) {
        const box = document.getElementById(`sampleBox${sampleNumber}`);
        if (!box) return;

        const snap = camera.captureFrameBase64();
        if (snap) {
            box.innerHTML = `<img src="${snap}" style="width:100%; height:100%; object-fit:cover; border-radius:6px;" alt="Sample">`;
            box.style.border = '2px solid #10b981';
        }
    }

    function updateProgress() {
        const count = collectedSamples.length;
        const pct = Math.round((count / targetSamples) * 100);
        if (progressBar) progressBar.style.width = `${pct}%`;
        if (progressText) progressText.textContent = `${count} / ${targetSamples} Samples`;
    }

    function updateFeedback(title, desc, status = 'info') {
        if (feedbackTitle) {
            feedbackTitle.textContent = title;
            feedbackTitle.className = `fw-bold fs-6 mb-1 text-${status}`;
        }
        if (feedbackDesc) {
            feedbackDesc.textContent = desc;
        }
    }

    async function submitRegistration() {
        try {
            startBtn.style.display = 'inline-block';
            startBtn.disabled = true;
            startBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

            const payload = {
                student_id: studentId,
                descriptors: collectedSamples,
                preview_image: previewImageBase64
            };

            const resp = await fetch(registerApi, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await resp.json();

            if (result.success) {
                updateFeedback('Registration Successful!', result.message, 'success');
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1500);
            } else {
                updateFeedback('Registration Failed', result.message || 'Server error occurred.', 'danger');
                startBtn.disabled = false;
                startBtn.innerHTML = 'Retry Registration';
            }
        } catch (err) {
            updateFeedback('Network Error', 'Failed to communicate with registration server: ' + err.message, 'danger');
            startBtn.disabled = false;
            startBtn.innerHTML = 'Retry Registration';
        }
    }
});
