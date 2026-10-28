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
                <span class="font-monospace text-primary fw-semibold"><?= htmlspecialchars($student['student_code']) ?></span> &bull; 
                Class: <strong><?= htmlspecialchars($student['class_name'] ?? 'Unassigned') ?></strong>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="<?= Url::to("/admin/students/{$student['id']}/face-register") ?>" class="btn btn-outline-primary rounded-pill">
            <i class="bi bi-camera me-1"></i> <?= $student['face_registered'] ? 'Re-register Face' : 'Register Face' ?>
        </a>
        <a href="<?= Url::to("/admin/students/{$student['id']}/edit") ?>" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="<?= Url::to('/admin/students') ?>" class="btn btn-light rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Student Profile Information & Attendance Statistics -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-person-lines-fill text-primary me-1"></i> Profile Information
            </h6>
            <ul class="list-unstyled mb-0">
                <li class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Student ID:</span>
                    <span class="fw-semibold text-dark">#<?= $student['id'] ?></span>
                </li>
                <li class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Matriculation Code:</span>
                    <span class="font-monospace text-dark"><?= htmlspecialchars($student['student_code']) ?></span>
                </li>
                <li class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Email Address:</span>
                    <span class="text-dark"><?= htmlspecialchars($student['email']) ?></span>
                </li>
                <li class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Phone:</span>
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
                <li class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Status:</span>
                    <span class="badge bg-<?= ($student['status'] === 'active') ? 'success' : 'danger' ?>-subtle text-<?= ($student['status'] === 'active') ? 'success' : 'danger' ?>">
                        <?= ucfirst($student['status']) ?>
                    </span>
                </li>
                <li class="d-flex justify-content-between">
                    <span class="text-muted small">Portal Username:</span>
                    <span class="font-monospace text-muted"><?= htmlspecialchars($student['username'] ?? 'No portal link') ?></span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Attendance Performance Stats -->
    <div class="col-lg-8">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-graph-up-arrow text-success me-1"></i> Attendance Performance
            </h6>

            <div class="row g-3 text-center mb-3">
                <div class="col-sm-3 col-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted fw-semibold">Attendance Rate</div>
                        <div class="h3 fw-bold text-primary my-1"><?= $statistics['percentage'] ?>%</div>
                        <span class="small text-muted"><?= $statistics['present_days'] ?> / <?= $statistics['total_days'] ?> Days</span>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted fw-semibold">On-Time Days</div>
                        <div class="h3 fw-bold text-success my-1"><?= $statistics['on_time_days'] ?></div>
                        <span class="small text-success">Punctual</span>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted fw-semibold">Late Arrivals</div>
                        <div class="h3 fw-bold text-warning my-1"><?= $statistics['late_days'] ?></div>
                        <span class="small text-warning">Grace Exceeded</span>
                    </div>
                </div>
                <div class="col-sm-3 col-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted fw-semibold">Leave Days</div>
                        <div class="h3 fw-bold text-info my-1"><?= $statistics['leave_days'] ?></div>
                        <span class="small text-info">Approved Leaves</span>
                    </div>
                </div>
            </div>

            <!-- Biometric Enrollment Status -->
            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between bg-light-subtle">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon <?= $student['face_registered'] ? 'success' : 'secondary' ?>" style="width: 44px; height: 44px; font-size: 1.25rem;">
                        <i class="bi bi-person-bounding-box"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">
                            <?= $student['face_registered'] ? 'Facial Biometric Enrolled' : 'Face Biometric Not Enrolled' ?>
                        </div>
                        <div class="small text-muted">
                            Active Registration Version: <strong>v<?= $student['registration_version'] ?></strong>
                        </div>
                    </div>
                </div>
                <a href="<?= Url::to("/admin/students/{$student['id']}/face-register") ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-camera me-1"></i> <?= $student['face_registered'] ? 'Re-enroll Face' : 'Enroll Face' ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Tabs: Recent Attendance Records & Face Registration History -->
<div class="card card-custom">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-primary"></i> Recent Attendance Records
        </h6>
        <span class="text-muted small">Showing last 10 entries</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Match Confidence</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentAttendance)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No attendance entries recorded for this student.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentAttendance as $r): ?>
                        <tr>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($r['attendance_date']) ?></td>
                            <td><?= htmlspecialchars($r['check_in_time'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($r['check_out_time'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($r['check_in_confidence'])): ?>
                                    <span class="badge bg-success-subtle text-success">
                                        <?= round($r['check_in_confidence'] * 100, 1) ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-status status-<?= $r['status'] ?>">
                                    <?= ucfirst(str_replace('_', ' ', $r['status'])) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= htmlspecialchars($r['remarks'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Face Registration History Audit -->
<div class="card card-custom mt-4">
    <div class="p-3 border-bottom">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-shield-check text-info"></i> Face Registration Version History
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Version</th>
                    <th>Samples Captured</th>
                    <th>Registered At</th>
                    <th>Registered By</th>
                    <th>State</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($registrations)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted">No facial registration history recorded.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($registrations as $reg): ?>
                        <tr>
                            <td class="fw-bold text-dark">v<?= $reg['version'] ?></td>
                            <td><?= $reg['sample_count'] ?> facial vectors</td>
                            <td><?= htmlspecialchars($reg['created_at']) ?></td>
                            <td><?= htmlspecialchars($reg['registered_by'] ?? 'System / Student') ?></td>
                            <td>
                                <?php if ($reg['is_active']): ?>
                                    <span class="badge bg-success text-white">Active Version</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border">Superseded / Archived</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
