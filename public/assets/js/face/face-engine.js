/**
 * Face Attendance System - Face Engine Module
 * Orchestrates face-api.js model loading from local assets, detections, and visual overlay.
 */

class FaceEngine {
    constructor(modelsUri = '/Face_attendance_system/public/assets/models') {
        this.modelsUri = modelsUri;
        this.isLoaded = false;
        this.detectorOptions = null;
    }

    async loadModels(scoreThreshold = 0.50) {
        if (this.isLoaded) return true;

        if (typeof faceapi === 'undefined') {
            throw new Error('face-api.js library is not loaded. Please verify script inclusion.');
        }

        try {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(this.modelsUri),
                faceapi.nets.faceLandmark68Net.loadFromUri(this.modelsUri),
                faceapi.nets.faceRecognitionNet.loadFromUri(this.modelsUri)
            ]);

            this.detectorOptions = new faceapi.TinyFaceDetectorOptions({
                inputSize: 416,
                scoreThreshold: scoreThreshold
            });

            this.isLoaded = true;
            return true;
        } catch (err) {
            console.error('Failed to load local face recognition models:', err);
            throw new Error('Failed to load face recognition models from local assets: ' + err.message);
        }
    }

    setScoreThreshold(scoreThreshold) {
        if (this.detectorOptions) {
            this.detectorOptions = new faceapi.TinyFaceDetectorOptions({
                inputSize: 416,
                scoreThreshold: scoreThreshold
            });
        }
    }

    async detectSingleFace(videoElement) {
        if (!this.isLoaded) await this.loadModels();
        return await faceapi
            .detectSingleFace(videoElement, this.detectorOptions)
            .withFaceLandmarks()
            .withFaceDescriptor();
    }

    async detectAllFaces(videoElement) {
        if (!this.isLoaded) await this.loadModels();
        return await faceapi
            .detectAllFaces(videoElement, this.detectorOptions)
            .withFaceLandmarks()
            .withFaceDescriptors();
    }

    drawDetectionOverlay(canvas, detections, dims, customLabels = []) {
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (!detections) return;

        const resized = faceapi.resizeResults(detections, dims);
        const list = Array.isArray(resized) ? resized : [resized];

        list.forEach((item, index) => {
            const box = item.box || item.detection?.box;
            if (!box) return;

            const label = customLabels[index] || '';

            // Draw glowing bounding box
            ctx.save();
            ctx.lineWidth = 3;
            ctx.strokeStyle = label.includes('Unknown') ? '#ef4444' : '#10b981';
            ctx.strokeRect(box.x, box.y, box.width, box.height);

            // Draw Label badge if present
            if (label) {
                ctx.font = '600 14px "Plus Jakarta Sans", sans-serif';
                const textWidth = ctx.measureText(label).width;
                const pad = 6;
                ctx.fillStyle = label.includes('Unknown') ? 'rgba(239, 68, 68, 0.9)' : 'rgba(16, 185, 129, 0.9)';
                ctx.fillRect(box.x, Math.max(0, box.y - 28), textWidth + pad * 2, 26);
                ctx.fillStyle = '#ffffff';
                ctx.fillText(label, box.x + pad, Math.max(18, box.y - 9));
            }
            ctx.restore();
        });
    }

    clearOverlay(canvas) {
        if (canvas) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }
}

window.FaceEngine = FaceEngine;
