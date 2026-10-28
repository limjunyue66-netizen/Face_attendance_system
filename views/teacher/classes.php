<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">My Assigned Classes & Students</h3>
        <p class="text-muted small mb-0">Roster of students currently enrolled in your allocated course sections</p>
    </div>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Student Name & Code</th>
                    <th>Class</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Biometric Face</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2"></i>
                            No students currently enrolled in your assigned classes.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($s['preview_photo'])): ?>
                                        <img src="<?= Url::asset($s['preview_photo']) ?>" class="avatar" alt="Avatar">
                                    <?php else: ?>
                                        <div class="avatar bg-primary text-white">
                                            <?= strtoupper(substr($s['full_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($s['full_name']) ?></div>
                                        <div class="small text-muted font-monospace"><?= htmlspecialchars($s['student_code']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($s['class_name']) ?></span></td>
                            <td><?= htmlspecialchars($s['email']) ?></td>
                            <td><?= htmlspecialchars($s['phone'] ?? '-') ?></td>
                            <td>
                                <?php if ($s['face_registered']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i> Registered (v<?= $s['registration_version'] ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= Url::to("/teacher/students/{$s['id']}") ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i> Attendance Log
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
