<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Teacher Dashboard</h3>
        <p class="text-muted small mb-0">Welcome back, <strong><?= htmlspecialchars($teacher['full_name']) ?></strong> (<?= htmlspecialchars($teacher['department']) ?>)</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= Url::to('/kiosk') ?>" target="_blank" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-camera-video me-1"></i> Open Kiosk
        </a>
        <a href="<?= Url::to('/teacher/attendance') ?>" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-calendar2-check me-1"></i> Class Attendance
        </a>
    </div>
</div>

<!-- Teacher Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
                <div class="stat-title">Assigned Classes</div>
                <div class="stat-value"><?= count($classIds) ?></div>
                <div class="small text-muted mt-1">Teaching sections</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-title">Total Enrolled Students</div>
                <div class="stat-value"><?= number_format($studentCount) ?></div>
                <div class="small text-success mt-1">Across your classes</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="stat-title">Scanned Today</div>
                <div class="stat-value"><?= count($todayAttendance) ?></div>
                <div class="small text-muted mt-1">In your classes today</div>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Classes & Subjects Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card card-custom h-100 p-4">
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-diagram-3-fill text-primary"></i> My Assigned Courses
            </h5>

            <?php if (empty($assignments)): ?>
                <p class="text-muted small py-4 text-center">No classes or subjects have been assigned to your profile yet.</p>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($assignments as $a): ?>
                        <div class="list-group-item px-0 py-3 d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($a['class_name']) ?></div>
                                <div class="small text-muted">
                                    <i class="bi bi-journal-text me-1"></i> <?= htmlspecialchars($a['subject_name']) ?> 
                                    <span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($a['subject_code']) ?></span>
                                </div>
                            </div>
                            <a href="<?= Url::to("/teacher/attendance?class_id={$a['class_id']}") ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                View Roll &rarr;
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Today's Attendance in Teacher Classes -->
    <div class="col-lg-7">
        <div class="card card-custom h-100">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-calendar2-day text-success"></i> Today's Attendance in Your Classes
                </h6>
                <a href="<?= Url::to('/teacher/attendance') ?>" class="small text-muted text-decoration-none">Full Records</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Check-In</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($todayAttendance)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No attendance records scanned in your classes today.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($todayAttendance as $r): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($r['preview_photo'])): ?>
                                                <img src="<?= Url::asset($r['preview_photo']) ?>" class="avatar" alt="Avatar">
                                            <?php else: ?>
                                                <div class="avatar bg-primary text-white">
                                                    <?= strtoupper(substr($r['student_name'] ?? 'S', 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <a href="<?= Url::to("/teacher/students/{$r['student_id']}") ?>" class="fw-semibold text-dark text-decoration-none">
                                                    <?= htmlspecialchars($r['student_name']) ?>
                                                </a>
                                                <div class="small text-muted font-monospace"><?= htmlspecialchars($r['student_code']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($r['class_name']) ?></span></td>
                                    <td>
                                        <div class="fw-medium text-dark"><?= htmlspecialchars($r['check_in_time'] ?? '-') ?></div>
                                        <?php if (!empty($r['check_in_confidence'])): ?>
                                            <span class="small text-success"><?= round($r['check_in_confidence'] * 100, 1) ?>%</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge-status status-<?= $r['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $r['status'])) ?>
                                        </span>
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
