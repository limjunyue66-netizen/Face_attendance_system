/**
 * Face Attendance System - Face Matcher Module
 * Builds labeled face descriptors, compares vectors, and computes Euclidean confidence.
 */

class FaceMatcherService {
    constructor(distanceThreshold = 0.55) {
        this.distanceThreshold = distanceThreshold;
        this.faceMatcher = null;
        this.studentsMap = new Map();
        this.isReady = false;
    }

    setThreshold(threshold) {
        this.distanceThreshold = parseFloat(threshold) || 0.55;
        if (this.labeledDescriptors && this.labeledDescriptors.length > 0) {
            this.faceMatcher = new faceapi.FaceMatcher(this.labeledDescriptors, this.distanceThreshold);
        }
    }

    loadStudentsData(studentsList) {
        this.studentsMap.clear();
        const labeledDescriptors = [];

        studentsList.forEach(student => {
            const sid = String(student.id);
            this.studentsMap.set(sid, student);

            if (student.descriptors && student.descriptors.length > 0) {
                const floatDescriptors = student.descriptors.map(d => {
                    return (d instanceof Float32Array) ? d : new Float32Array(Object.values(d));
                });

                labeledDescriptors.push(
                    new faceapi.LabeledFaceDescriptors(sid, floatDescriptors)
                );
            }
        });

        this.labeledDescriptors = labeledDescriptors;

        if (labeledDescriptors.length > 0) {
            this.faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, this.distanceThreshold);
            this.isReady = true;
        } else {
            this.faceMatcher = null;
            this.isReady = false;
        }

        return this.isReady;
    }

    match(queryDescriptor) {
        if (!this.faceMatcher || !this.isReady) {
            return {
                matched: false,
                reason: 'NO_REGISTERED_STUDENTS',
                label: 'unknown',
                distance: 1.0,
                confidence: 0,
                student: null
            };
        }

        const bestMatch = this.faceMatcher.findBestMatch(queryDescriptor);
        const label = bestMatch.label;
        const distance = bestMatch.distance;

        if (label === 'unknown' || distance > this.distanceThreshold) {
            return {
                matched: false,
                reason: 'UNKNOWN_FACE',
                label: 'unknown',
                distance: distance,
                confidence: Math.max(0, 1 - (distance / this.distanceThreshold)),
                student: null
            };
        }

        const student = this.studentsMap.get(label);
        // Confidence calculation: 1.0 - (distance / 1.0)
        const confidenceScore = Math.max(0, Math.min(1.0, 1.0 - (distance * 0.75)));

        return {
            matched: true,
            reason: 'MATCH_FOUND',
            label: label,
            studentId: parseInt(label),
            distance: distance,
            confidence: confidenceScore,
            student: student
        };
    }
}

window.FaceMatcherService = FaceMatcherService;
