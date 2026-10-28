<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <?php if (!empty($student['preview_photo'])): ?>
            <img src="<?= Url::asset($student['preview_photo']) ?>" class="avatar avatar-lg shadow-sm" alt="Avatar">
        <?php else: ?>
            <div class="avatar avatar-lg bg-primary text-white shadow-sm">
                <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div>
            <h3 class="fw-bold text-dark mb-0"><?= htmlspecialchars($student['full_name']) ?></h3>
            <div class="text-muted small">
                Student ID: <strong><?= htmlspecialchars($student['student_code']) ?></strong> &bull; Class: <?= htmlspecialchars($student['class_name'] ?? 'Unassigned') ?>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= Url::to('/student/face-register') ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-camera me-1"></i> <?= $student['face_registered'] ? 'Update Face Model' : 'Enroll Face' ?>
        </a>
        <a href="<?= Url::to('/change-password') ?>" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-key me-1"></i> Change Password
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Student Details -->
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-person-badge text-primary me-1"></i> Personal Profile
            </h5>
            <ul class="list-unstyled mb-0">
                <li class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Student Code:</span>
                    <span class="font-monospace text-dark"><?= htmlspecialchars($student['student_code']) ?></span>
                </li>
                <li class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Full Legal Name:</span>
                    <span class="fw-semibold text-dark"><?= htmlspecialchars($student['full_name']) ?></span>
                </li>
                <li class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Official Email:</span>
                    <span class="text-dark"><?= htmlspecialchars($student['email']) ?></span>
                </li>
                <li class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Contact Phone:</span>
                    <span class="text-dark"><?= htmlspecialchars($student['phone'] ?? '-') ?></span>
                </li>
                <li class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Gender:</span>
                    <span class="text-capitalize text-dark"><?= htmlspecialchars($student['gender'] ?? 'N/A') ?></span>
                </li>
                <li class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Date of Birth:</span>
                    <span class="text-dark"><?= htmlspecialchars($student['date_of_birth'] ?? '-') ?></span>
                </li>
                <li class="d-flex justify-content-between">
                    <span class="text-muted small">Account Status:</span>
                    <span class="badge bg-success-subtle text-success"><?= ucfirst($student['status']) ?></span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Biometrics & Version History -->
    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between">
                <span><i class="bi bi-shield-check text-success me-1"></i> Biometric Status</span>
                <?php if ($student['face_registered']): ?>
                    <span class="badge bg-success text-white">Active (v<?= $student['registration_version'] ?>)</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">Enrollment Needed</span>
                <?php endif; ?>
            </h5>

            <p class="text-muted small">
                Face attendance uses mathematical facial landmarks processed entirely in your browser. If your appearance changes significantly, click below to re-register.
            </p>

            <div class="table-responsive mt-3">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Version</th>
                            <th>Captured Samples</th>
                            <th>Date Registered</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($registrations)): ?>
                            <tr><td colspan="4" class="text-center py-3 text-muted">No registration history recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($registrations as $reg): ?>
                                <tr>
                                    <td class="fw-bold text-dark">v<?= $reg['version'] ?></td>
                                    <td><?= $reg['sample_count'] ?> samples</td>
                                    <td><?= htmlspecialchars($reg['created_at']) ?></td>
                                    <td>
                                        <?php if (!empty($reg['is_active']) || (!isset($reg['is_active']) && ($reg['version'] ?? 0) == ($student['registration_version'] ?? 0))): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border">Archived</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
