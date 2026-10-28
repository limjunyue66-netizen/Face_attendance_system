<?php
use App\Helpers\Url;
?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card card-custom p-4 p-md-5 mb-4">
            <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="stat-icon primary" style="width: 60px; height: 60px; font-size: 1.75rem;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-dark mb-1">Biometric Privacy & Data Policy</h3>
                    <p class="text-muted small mb-0">Transparency and data protection standards for facial attendance verification</p>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-cpu text-primary"></i> 1. On-Device Mathematical Descriptors
                </h5>
                <p class="text-muted leading-relaxed">
                    Our system processes facial landmarks entirely in the local client browser via <code>face-api.js</code>. Rather than storing sensitive raw biometric video feeds, the algorithm extracts a 128-dimensional floating point numerical descriptor (a mathematical vector). This vector cannot be reverse-engineered into an original photograph.
                </p>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i> 2. Purpose Limitation: Attendance Verification
                </h5>
                <p class="text-muted leading-relaxed">
                    Facial descriptors and capture timestamps are utilized exclusively for educational attendance management, punctuality verification, and campus security auditing. Biometric information is never shared, marketed, or transferred to third-party commercial databases.
                </p>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-file-lock text-primary"></i> 3. Role-Based Access Control (RBAC)
                </h5>
                <p class="text-muted leading-relaxed">
                    Access to student records and attendance archives is strictly enforced:
                </p>
                <ul class="text-muted">
                    <li><strong>Students:</strong> Can only view their own attendance records, register their face, and submit personal leave applications.</li>
                    <li><strong>Teachers:</strong> Can only view attendance logs belonging to students currently enrolled in their assigned classes.</li>
                    <li><strong>Administrators:</strong> Maintain system oversight, quality threshold adjustments, and audit trail records.</li>
                </ul>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-repeat text-primary"></i> 4. Re-Registration and Data Retention
                </h5>
                <p class="text-muted leading-relaxed">
                    Students and authorized administrators may re-register facial descriptors at any time to account for natural appearance changes. When a re-registration occurs, previous active descriptors are archived with version tags, ensuring verifiable audit logs while keeping matching operations fast and accurate.
                </p>
            </div>

            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small"><i class="bi bi-check2-circle text-success me-1"></i> Compliant with campus data protection protocols.</span>
                <a href="<?= Url::to('/') ?>" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
