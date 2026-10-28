/**
 * Face Attendance System - Camera Manager Module
 * Handles WebRTC camera streaming, canvas resolution scaling, and cleanup.
 */

class CameraManager {
    constructor(videoElement, overlayCanvas) {
        this.video = videoElement;
        this.canvas = overlayCanvas;
        this.stream = null;
        this.isStreaming = false;
    }

    async start(constraints = { video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' }, audio: false }) {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Camera access (getUserMedia) is not supported in this browser. Please use Chrome, Edge, or Firefox over localhost or HTTPS.');
        }

        try {
            this.stream = await navigator.mediaDevices.getUserMedia(constraints);
            this.video.srcObject = this.stream;

            return new Promise((resolve) => {
                this.video.onloadedmetadata = () => {
                    this.video.play();
                    this.isStreaming = true;
                    this.resizeCanvas();
                    resolve(this.stream);
                };
            });
        } catch (err) {
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                throw new Error('Camera access permission was denied. Please allow camera permissions in your browser bar.');
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                throw new Error('No camera device detected on this computer.');
            } else {
                throw new Error('Camera error: ' + err.message);
            }
        }
    }

    resizeCanvas() {
        if (this.canvas && this.video) {
            this.canvas.width = this.video.videoWidth || this.video.clientWidth;
            this.canvas.height = this.video.videoHeight || this.video.clientHeight;
        }
    }

    captureFrameBase64() {
        if (!this.video || !this.isStreaming) return null;
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = this.video.videoWidth;
        tempCanvas.height = this.video.videoHeight;
        const ctx = tempCanvas.getContext('2d');
        // Un-mirror if mirrored
        ctx.drawImage(this.video, 0, 0, tempCanvas.width, tempCanvas.height);
        return tempCanvas.toDataURL('image/jpeg', 0.85);
    }

    stop() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        if (this.video) {
            this.video.srcObject = null;
        }
        this.isStreaming = false;
    }
}

window.CameraManager = CameraManager;
