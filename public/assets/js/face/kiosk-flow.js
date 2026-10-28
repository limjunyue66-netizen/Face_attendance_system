/**
 * Face Attendance System - Kiosk Flow Controller
 * Continuous reception camera scanning, multi-face tracking, duplicate prevention, and audio chime.
 */

document.addEventListener('DOMContentLoaded', async () => {
    const configEl = document.getElementById('kioskConfigData');
    if (!configEl) return;

    const modelsUrl = configEl.dataset.modelsUrl;
    const descriptorsApi = configEl.dataset.descriptorsApi;
    const scanApi = configEl.dataset.scanApi;
    const settingsApi = configEl.dataset.settingsApi;

    const video = document.getElementById('videoElement');
    const canvas = document.getElementById('overlayCanvas');
    const cameraGuide = document.getElementById('cameraGuide');
    const scannerLaser = document.getElementById('scannerLaser');
    const kioskFaceCount = document.getElementById('kioskFaceCount');
    const kioskEngineState = document.getElementById('kioskEngineState');
    const kioskNoticeTitle = document.getElementById('kioskNoticeTitle');
    const kioskNoticeDesc = document.getElementById('kioskNoticeDesc');
    const kioskResultBox = document.getElementById('kioskResultBox');
    const kioskResultName = document.getElementById('kioskResultName');
    const kioskResultCode = document.getElementById('kioskResultCode');
    const kioskResultBadge = document.getElementById('kioskResultBadge');
    const kioskResultConfidence = document.getElementById('kioskResultConfidence');
    const kioskResultMessage = document.getElementById('kioskResultMessage');
    const kioskRecentFeed = document.getElementById('kioskRecentFeed');

    const camera = new CameraManager(video, canvas);
    const engine = new FaceEngine(modelsUrl);
    const matcher = new FaceMatcherService();
    const qualityChecker = new FaceQualityChecker();

    let isScanning = true;
    let isProcessing = false;
    let recentScannedStudents = new Map(); // studentId -> lastScannedTimestamp
    let duplicateCooldownMs = 300000; // 5 min default

    // Web Audio Chimes
    function playChime(type = 'success') {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();

            if (type === 'success') {
                // Two-tone pleasant ascending chime (523Hz -> 659Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(523.25, ctx.currentTime);
                osc1.frequency.setValueAtTime(659.25, ctx.currentTime + 0.12);
                gain1.gain.setValueAtTime(0.2, ctx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start();
                osc1.stop(ctx.currentTime + 0.35);
            } else if (type === 'warning') {
                // Low alert double pulse (440Hz -> 349Hz)
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(440, ctx.currentTime);
                osc.frequency.setValueAtTime(349.23, ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.3);
            }
        } catch (e) {
            // Audio context policy fallback
        }
    }

    try {
        kioskEngineState.innerHTML = '<i class="bi bi-camera"></i> Starting Camera...';
        await camera.start({ video: { width: { ideal: 1280 }, height: { ideal: 720 }, facingMode: 'user' }, audio: false });

        kioskEngineState.innerHTML = '<i class="bi bi-cpu"></i> Loading AI Models...';
        await engine.loadModels();

        // Load Settings
        try {
            const sResp = await fetch(settingsApi);
            const sData = await sResp.json();
            if (sData.success && sData.settings) {
                qualityChecker.updateSettings(sData.settings);
                if (sData.settings.face_match_threshold) {
                    matcher.setThreshold(parseFloat(sData.settings.face_match_threshold));
                }
                if (sData.settings.duplicate_scan_interval) {
                    duplicateCooldownMs = parseInt(sData.settings.duplicate_scan_interval) * 1000;
                }
            }
        } catch (e) {}

        // Load Descriptors
        kioskEngineState.innerHTML = '<i class="bi bi-people"></i> Loading Student Descriptors...';
        const dResp = await fetch(descriptorsApi);
        const dData = await dResp.json();

        if (dData.success && dData.students) {
            matcher.loadStudentsData(dData.students);
            kioskEngineState.innerHTML = `<i class="bi bi-check2-all text-success"></i> ${dData.count} Registered`;
        } else {
            kioskEngineState.innerHTML = '<i class="bi bi-exclamation-triangle text-warning"></i> No Students';
        }

        updateNotice('Position Face Inside Frame', 'The system is scanning continuously for registered students.', 'info');
        requestAnimationFrame(kioskLoop);
    } catch (err) {
        kioskEngineState.innerHTML = '<i class="bi bi-x-circle text-danger"></i> Init Error';
        updateNotice('Kiosk Error', err.message, 'danger');
    }

    async function kioskLoop() {
        if (!isScanning) return;

        if (isProcessing) {
            requestAnimationFrame(kioskLoop);
            return;
        }

        try {
            const detections = await engine.detectAllFaces(video);
            const count = detections ? detections.length : 0;
            if (kioskFaceCount) kioskFaceCount.innerHTML = `<i class="bi bi-person"></i> Faces: ${count}`;

            if (count === 0) {
                cameraGuide.className = 'camera-guide';
                engine.clearOverlay(canvas);
                updateNotice('Please Step Forward', 'Look directly at the camera to record attendance.', 'info');
                requestAnimationFrame(kioskLoop);
                return;
            }

            if (count > 1) {
                cameraGuide.className = 'camera-guide warning';
                engine.clearOverlay(canvas);
                updateNotice('Multiple Faces Detected', 'Please approach one person at a time for secure verification.', 'warning');
                requestAnimationFrame(kioskLoop);
                return;
            }

            // Exactly one face detected
            const detection = detections[0];
            const dims = { width: video.videoWidth, height: video.videoHeight };

            // Quality Check
            const quality = qualityChecker.checkQuality(detection, video);
            if (!quality.passed) {
                cameraGuide.className = 'camera-guide warning';
                engine.drawDetectionOverlay(canvas, detection, dims, ['Adjusting...']);
                updateNotice('Quality Check', quality.message, 'warning');
                requestAnimationFrame(kioskLoop);
                return;
            }

            // Match against registered student descriptors
            const matchResult = matcher.match(detection.descriptor);

            if (!matchResult.matched) {
                cameraGuide.className = 'camera-guide warning';
                engine.drawDetectionOverlay(canvas, detection, dims, ['Unknown Face']);
                updateNotice('Unknown Face', 'Face not recognized in student registry or match threshold not met.', 'danger');
                requestAnimationFrame(kioskLoop);
                return;
            }

            // Face Recognized!
            const student = matchResult.student;
            const studentId = matchResult.studentId;
            const now = Date.now();

            // Check client-side cooldown
            if (recentScannedStudents.has(studentId)) {
                const lastTime = recentScannedStudents.get(studentId);
                if (now - lastTime < duplicateCooldownMs) {
                    const remainingSec = Math.ceil((duplicateCooldownMs - (now - lastTime)) / 1000);
                    cameraGuide.className = 'camera-guide warning';
                    engine.drawDetectionOverlay(canvas, detection, dims, [student.full_name]);
                    updateNotice('Attendance Already Recorded', `${student.full_name}: Please wait ${remainingSec}s before scanning again.`, 'warning');
                    playChime('warning');
                    await new Promise(r => setTimeout(r, 1500));
                    requestAnimationFrame(kioskLoop);
                    return;
                }
            }

            // Send Scan to API
            cameraGuide.className = 'camera-guide active';
            engine.drawDetectionOverlay(canvas, detection, dims, [student.full_name]);
            isProcessing = true;

            updateNotice('Student Recognized', `Verifying attendance for ${student.full_name}...`, 'success');

            const snap = camera.captureFrameBase64();
            await recordKioskScan(studentId, matchResult.confidence, snap, student);

            recentScannedStudents.set(studentId, Date.now());
            isProcessing = false;

            // Pause 2 seconds so the student sees their confirmation card clearly
            await new Promise(r => setTimeout(r, 2000));
        } catch (err) {
            console.error('Kiosk loop error:', err);
            isProcessing = false;
        }

        requestAnimationFrame(kioskLoop);
    }

    async function recordKioskScan(studentId, confidence, photo, student) {
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
                playChime('success');
                displayKioskConfirmation(data);
                prependToRecentFeed(data);
                updateNotice('Attendance Verified!', data.message, 'success');
            } else {
                playChime('warning');
                displayKioskNotice(data);
                updateNotice('Scan Notice', data.message, 'warning');
            }
        } catch (err) {
            updateNotice('Server Error', 'Failed to communicate with attendance server: ' + err.message, 'danger');
        }
    }

    function displayKioskConfirmation(data) {
        if (!kioskResultBox) return;
        kioskResultBox.style.display = 'block';
        if (kioskResultName) kioskResultName.textContent = data.student_name;
        if (kioskResultCode) kioskResultCode.textContent = `${data.student_code} • ${data.class_name}`;
        if (kioskResultBadge) {
            kioskResultBadge.textContent = data.type === 'check_in' ? (data.status === 'late' ? 'LATE CHECK-IN' : 'CHECKED IN') : 'CHECKED OUT';
            kioskResultBadge.className = data.status === 'late' ? 'badge bg-warning text-dark mb-1' : 'badge bg-success text-white mb-1';
        }
        if (kioskResultConfidence) kioskResultConfidence.textContent = `Match Confidence: ${data.confidence}% at ${data.time}`;
        if (kioskResultMessage) kioskResultMessage.textContent = data.message;
    }

    function displayKioskNotice(data) {
        if (!kioskResultBox) return;
        kioskResultBox.style.display = 'block';
        if (kioskResultName) kioskResultName.textContent = data.student_name || 'Notice';
        if (kioskResultCode) kioskResultCode.textContent = data.student_code || '';
        if (kioskResultBadge) {
            kioskResultBadge.textContent = 'NOTICE';
            kioskResultBadge.className = 'badge bg-warning text-dark mb-1';
        }
        if (kioskResultConfidence) kioskResultConfidence.textContent = '';
        if (kioskResultMessage) kioskResultMessage.textContent = data.message;
    }

    function prependToRecentFeed(data) {
        if (!kioskRecentFeed) return;
        const emptyEl = document.getElementById('kioskFeedEmpty');
        if (emptyEl) emptyEl.remove();

        const item = document.createElement('div');
        item.className = 'kiosk-feed-item';
        item.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <div class="avatar bg-primary text-white" style="width: 32px; height: 32px; font-size: 0.85rem;">
                    ${(data.student_name || 'S').charAt(0).toUpperCase()}
                </div>
                <div>
                    <div class="fw-bold text-white small">${data.student_name}</div>
                    <div class="small text-light text-opacity-50" style="font-size: 0.72rem;">${data.class_name}</div>
                </div>
            </div>
            <div class="text-end">
                <span class="badge-status status-${data.status}" style="font-size: 0.72rem;">
                    ${data.type === 'check_in' ? 'Check In' : 'Check Out'}
                </span>
                <div class="small text-light text-opacity-50 font-monospace" style="font-size: 0.7rem;">${data.time}</div>
            </div>
        `;

        kioskRecentFeed.insertBefore(item, kioskRecentFeed.firstChild);
    }

    function updateNotice(title, desc, status = 'info') {
        if (kioskNoticeTitle) {
            kioskNoticeTitle.textContent = title;
            kioskNoticeTitle.className = `h5 fw-bold mb-1 text-${status}`;
        }
        if (kioskNoticeDesc) {
            kioskNoticeDesc.textContent = desc;
        }
    }
});
