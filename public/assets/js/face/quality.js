/**
 * Face Attendance System - Biometric Quality Check Module
 * Verifies face dimensions, lighting/brightness, image sharpness, and detection confidence.
 */

class FaceQualityChecker {
    constructor(settings = {}) {
        this.settings = Object.assign({
            minFaceSize: 80,
            maxFaceSize: 450,
            minBrightness: 40,
            maxBrightness: 220,
            minSharpness: 3,
            detectionConfidence: 0.50,
            stableFrameCount: 2
        }, settings);

        this.consecutiveStableFrames = 0;
        this.lastBox = null;
    }

    updateSettings(newSettings) {
        if (!newSettings) return;
        if (newSettings.min_face_size) this.settings.minFaceSize = parseFloat(newSettings.min_face_size);
        if (newSettings.max_face_size) this.settings.maxFaceSize = parseFloat(newSettings.max_face_size);
        if (newSettings.min_brightness) this.settings.minBrightness = parseFloat(newSettings.min_brightness);
        if (newSettings.max_brightness) this.settings.maxBrightness = parseFloat(newSettings.max_brightness);
        if (newSettings.min_sharpness) this.settings.minSharpness = parseFloat(newSettings.min_sharpness);
        if (newSettings.detection_confidence) this.settings.detectionConfidence = parseFloat(newSettings.detection_confidence);
        if (newSettings.stable_frame_count) this.settings.stableFrameCount = parseInt(newSettings.stable_frame_count);
    }

    checkQuality(detection, videoElement) {
        if (!detection) {
            this.consecutiveStableFrames = 0;
            return {
                passed: false,
                reason: 'NO_FACE',
                message: 'No face detected. Please position your face inside the camera frame.'
            };
        }

        const box = detection.box || detection.detection?.box;
        if (!box) {
            this.consecutiveStableFrames = 0;
            return { passed: false, reason: 'NO_BOX', message: 'Unable to frame face.' };
        }

        const faceWidth = box.width;
        const faceHeight = box.height;
        const faceDimension = Math.max(faceWidth, faceHeight);

        // 1. Face Size Check
        if (faceDimension < this.settings.minFaceSize) {
            this.consecutiveStableFrames = 0;
            return {
                passed: false,
                reason: 'FACE_TOO_SMALL',
                message: 'Face too small / far away. Please step closer to the camera.',
                faceSize: faceDimension
            };
        }

        if (faceDimension > this.settings.maxFaceSize) {
            this.consecutiveStableFrames = 0;
            return {
                passed: false,
                reason: 'FACE_TOO_LARGE',
                message: 'Face too close. Please step back slightly.',
                faceSize: faceDimension
            };
        }

        // 2. Brightness & Sharpness Analysis using Canvas
        const metrics = this.computeCanvasMetrics(videoElement, box);

        if (metrics.brightness < this.settings.minBrightness) {
            this.consecutiveStableFrames = 0;
            return {
                passed: false,
                reason: 'POOR_LIGHTING',
                message: 'Lighting is too low. Please face a light source.',
                metrics
            };
        }

        if (metrics.brightness > this.settings.maxBrightness) {
            this.consecutiveStableFrames = 0;
            return {
                passed: false,
                reason: 'TOO_BRIGHT',
                message: 'Image is too bright or overexposed.',
                metrics
            };
        }

        if (metrics.sharpness < this.settings.minSharpness) {
            this.consecutiveStableFrames = 0;
            return {
                passed: false,
                reason: 'BLURRY',
                message: 'Image is blurry. Please hold steady.',
                metrics
            };
        }

        // 3. Stability Check
        if (this.lastBox) {
            const dx = Math.abs(box.x - this.lastBox.x);
            const dy = Math.abs(box.y - this.lastBox.y);
            if (dx < 15 && dy < 15) {
                this.consecutiveStableFrames++;
            } else {
                this.consecutiveStableFrames = 1;
            }
        } else {
            this.consecutiveStableFrames = 1;
        }

        this.lastBox = { x: box.x, y: box.y, width: box.width, height: box.height };

        if (this.consecutiveStableFrames < this.settings.stableFrameCount) {
            return {
                passed: false,
                reason: 'NOT_STABLE',
                message: `Hold steady... (${this.consecutiveStableFrames}/${this.settings.stableFrameCount})`,
                stableCount: this.consecutiveStableFrames,
                metrics
            };
        }

        return {
            passed: true,
            reason: 'OK',
            message: 'Face quality optimal.',
            metrics,
            faceSize: faceDimension
        };
    }

    computeCanvasMetrics(video, box) {
        try {
            const canvas = document.createElement('canvas');
            canvas.width = Math.min(box.width, 160);
            canvas.height = Math.min(box.height, 160);
            const ctx = canvas.getContext('2d');

            // Draw cropped face region
            ctx.drawImage(
                video,
                Math.max(0, box.x),
                Math.max(0, box.y),
                Math.min(video.videoWidth - box.x, box.width),
                Math.min(video.videoHeight - box.y, box.height),
                0,
                0,
                canvas.width,
                canvas.height
            );

            const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imgData.data;
            const len = data.length;

            let totalBrightness = 0;
            let grayValues = new Uint8Array(canvas.width * canvas.height);

            let gIdx = 0;
            for (let i = 0; i < len; i += 4) {
                // Perceived luminance formula: 0.299R + 0.587G + 0.114B
                const gray = Math.round(data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114);
                grayValues[gIdx++] = gray;
                totalBrightness += gray;
            }

            const pixelCount = canvas.width * canvas.height;
            const avgBrightness = pixelCount > 0 ? (totalBrightness / pixelCount) : 100;

            // Sharpness estimation using Laplacian gradient variance
            let sharpness = 0;
            const w = canvas.width;
            const h = canvas.height;

            for (let y = 1; y < h - 1; y++) {
                for (let x = 1; x < w - 1; x++) {
                    const idx = y * w + x;
                    // Laplacian kernel: 4*center - top - bottom - left - right
                    const lap = Math.abs(
                        4 * grayValues[idx] -
                        grayValues[idx - w] -
                        grayValues[idx + w] -
                        grayValues[idx - 1] -
                        grayValues[idx + 1]
                    );
                    sharpness += lap;
                }
            }

            sharpness = pixelCount > 0 ? Math.round(sharpness / pixelCount) : 30;

            return {
                brightness: Math.round(avgBrightness),
                sharpness: sharpness
            };
        } catch (e) {
            return { brightness: 100, sharpness: 30 };
        }
    }
}

window.FaceQualityChecker = FaceQualityChecker;
