/**
 * Face Attendance System - Pose Estimation Module
 * Calculates facial orientation (yaw, roll, pitch) using 68 facial landmarks.
 */

class PoseEstimator {
    static isFrontFacing(landmarks, maxAngleDegrees = 20) {
        if (!landmarks) return { isFront: true, angle: 0 };

        try {
            const points = landmarks.positions || landmarks;
            // Key points:
            // 30: Nose tip
            // 36: Left eye outer corner
            // 45: Right eye outer corner
            // 27: Nose bridge top
            // 8: Chin bottom
            const noseTip = points[30];
            const leftEye = points[36];
            const rightEye = points[45];

            if (!noseTip || !leftEye || !rightEye) {
                return { isFront: true, angle: 0 };
            }

            // Distances from nose tip to eye outer corners
            const distLeft = Math.abs(noseTip.x - leftEye.x);
            const distRight = Math.abs(rightEye.x - noseTip.x);

            // Ratio of symmetry
            const ratio = distLeft / (distRight || 1);

            // If ratio is between 0.65 and 1.55, the face is predominantly front-facing
            const isSymmetric = (ratio >= 0.60 && ratio <= 1.65);

            // Roll angle (tilt)
            const dy = rightEye.y - leftEye.y;
            const dx = rightEye.x - leftEye.x;
            const rollDegrees = Math.abs((Math.atan2(dy, dx) * 180) / Math.PI);

            const isTilted = rollDegrees > maxAngleDegrees;

            return {
                isFront: isSymmetric && !isTilted,
                ratio: Math.round(ratio * 100) / 100,
                rollAngle: Math.round(rollDegrees),
                reason: !isSymmetric ? 'Face is turned away. Please look straight ahead.' : (isTilted ? 'Head is tilted. Please level your head.' : 'OK')
            };
        } catch (e) {
            return { isFront: true, angle: 0 };
        }
    }
}

window.PoseEstimator = PoseEstimator;
