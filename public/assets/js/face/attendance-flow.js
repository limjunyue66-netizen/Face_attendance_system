/**
 * Face Attendance System - Student Attendance Flow Controller
 * Live single-student verification and attendance check-in/out recording.
 */

document.addEventListener('DOMContentLoaded', async () => {
    const configEl = document.getElementById('attendanceConfigData');
    if (!configEl) return;

    const studentId = parseInt(configEl.dataset.studentId);
    const modelsUrl = configEl.dataset.modelsUrl;
    const descriptorsApi = configEl.dataset.descriptorsApi;
    const scanApi = configEl.dataset.scanApi;
    const settingsApi = configEl.dataset.settingsApi;

    const video = document.getElementById('videoElement');
    const canvas = document.getElementById('overlayCanvas');
    const cameraGuide = document.getElementById('cameraGuide');
    const scannerStatus = document.getElementById('scannerStatus');
    const scanFeedbackTitle = document.getElementById('scanFeedbackTitle');
    const scanFeedbackDesc = document.getElementById('scanFeedbackDesc');
    const realtimeResult = document.getElementById('realtimeResult');
    const resultName = document.getElementById('resultName');
    const resultTypeMessage = document.getElementById('resultTypeMessage');
    const resultConfidence = document.getElementById('resultConfidence');

    const camera = new CameraManager(video, canvas);
    const engine = new FaceEngine(modelsUrl);
    const matcher = new FaceMatcherService();
    const qualityChecker = new FaceQualityChecker();

    let isScanning = true;
    let isProcessing = false;
    let lastScanTime = 0;
    const scanCooldown = 5000; // 5s UI cooldown after successful scan

    try {
        scannerStatus.textContent = 'Starting camera...';
        await camera.start();

        scannerStatus.textContent = 'Loading AI models...';
        await engine.loadModels();

        // Load settings
        try {
            const sResp = await fetch(settingsApi);
            const sData = await sResp.json();
            if (sData.success && sData.settings) {
                qualityChecker.updateSettings(sData.settings);
                if (sData.settings.face_match_threshold) {
                    matcher.setThreshold(parseFloat(sData.settings.face_match_threshold));
                }
            }
        } catch (e) {
            console.warn('Using default settings');
        }

        scannerStatus.textContent = 'Loading biometric vectors...';
        const dResp = await fetch(descriptorsApi);
        const dData = await dResp.json();

        if (dData.success && dData.students) {
            matcher.loadStudentsData(dData.students);
        }

        scannerStatus.textContent = 'Scanner Active & Ready';
        updateFeedback('Position your face inside the guide', 'The system will automatically record your attendance once identified.', 'info');

        requestAnimationFrame(attendanceLoop);
    } catch (err) {
        scannerStatus.textContent = 'Error: ' + err.message;
        updateFeedback('Scanner Initialization Error', err.message, 'danger');
    }

    async function attendanceLoop() {
        if (!isScanning) return;

        const now = Date.now();
        if (isProcessing || (now - lastScanTime < scanCooldown)) {
            requestAnimationFrame(attendanceLoop);
            return;
        }

        try {
            const detection = await engine.detectSingleFace(video);

            if (!detection) {
                cameraGuide.className = 'camera-guide';
                engine.clearOverlay(canvas);
                updateFeedback('No Face Detected', 'Please look directly at the camera inside the oval frame.', 'warning');
                requestAnimationFrame(attendanceLoop);
                return;
            }

            const dims = { width: video.videoWidth, height: video.videoHeight };
            engine.drawDetectionOverlay(canvas, detection, dims);

            // Quality check
            const quality = qualityChecker.checkQuality(detection, video);
            if (!quality.passed) {
                cameraGuide.className = 'camera-guide warning';
                updateFeedback('Quality Check', quality.message, 'warning');
                requestAnimationFrame(attendanceLoop);
                return;
            }

            // Perform matching
            const matchResult = matcher.match(detection.descriptor);

            if (!matchResult.matched) {
                cameraGuide.className = 'camera-guide warning';
                updateFeedback('Unknown Face', 'Face not recognized or confidence below threshold. Please ensure your face is registered.', 'danger');
                requestAnimationFrame(attendanceLoop);
                return;
            }

            // Verify that the detected face matches the current logged in student
            if (matchResult.studentId !== studentId) {
                cameraGuide.className = 'camera-guide warning';
                updateFeedback('Identity Mismatch', `Detected face (${matchResult.student.full_name}) does not match your logged-in student account.`, 'danger');
                requestAnimationFrame(attendanceLoop);
                return;
            }

            // Confirmed match!
            cameraGuide.className = 'camera-guide active';
            isProcessing = true;
            updateFeedback('Identity Verified', `Recognized ${matchResult.student.full_name}. Recording attendance...`, 'success');

            const snapPhoto = camera.captureFrameBase64();
            await submitScan(matchResult.studentId, matchResult.confidence, snapPhoto);

            lastScanTime = Date.now();
            isProcessing = false;
        } catch (err) {
            console.error('Attendance loop error:', err);
            isProcessing = false;
        }

        requestAnimationFrame(attendanceLoop);
    }

    async function submitScan(studentId, confidence, photo) {
        try {
            const resp = await fetch(scanApi, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    student_id: studentId,
                    confidence: confidence,
                    photo: photo
                })
            });

            const data = await resp.json();

            if (data.success) {
                showResultBadge(data.student_name, data.message, data.confidence, 'success');
                updateFeedback('Attendance Confirmed!', data.message, 'success');
                // Refresh status card after 2s
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showResultBadge(data.student_name || 'Notice', data.message, null, 'warning');
                updateFeedback('Notice', data.message, 'warning');
            }
        } catch (err) {
            updateFeedback('Communication Error', 'Failed to submit attendance scan to server.', 'danger');
        }
    }

    function showResultBadge(name, message, confidence, type = 'success') {
        if (!realtimeResult) return;
        realtimeResult.style.display = 'block';
        realtimeResult.className = `p-3 rounded-3 border mb-3 bg-${type}-subtle border-${type}-subtle`;
        if (resultName) resultName.textContent = name;
        if (resultTypeMessage) resultTypeMessage.textContent = message;
        if (resultConfidence) {
            resultConfidence.textContent = confidence ? `AI Confidence: ${confidence}%` : '';
        }
    }

    function updateFeedback(title, desc, status = 'info') {
        if (scanFeedbackTitle) {
            scanFeedbackTitle.textContent = title;
            scanFeedbackTitle.className = `fw-bold fs-6 mb-1 text-${status}`;
        }
        if (scanFeedbackDesc) {
            scanFeedbackDesc.textContent = desc;
        }
    }
});
