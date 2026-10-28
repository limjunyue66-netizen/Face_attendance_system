<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Academic Curriculum & Teacher Assignments</h3>
        <p class="text-muted small mb-0">Map subjects to classes and allocate faculty teachers to course modules</p>
    </div>
</div>

<div class="row g-4">
    <!-- Section 1: Class to Subject Mapping -->
    <div class="col-lg-6">
        <div class="card card-custom h-100">
            <div class="p-3 border-bottom bg-light-subtle d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-diagram-2 text-primary"></i> 1. Class-Subject Assignments
                </h6>
                <button class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="collapse" data-bs-target="#addClassSubjectCollapse">
                    <i class="bi bi-plus me-1"></i> Assign Subject
                </button>
            </div>

            <!-- Form Collapse -->
            <div class="collapse p-3 bg-light border-bottom" id="addClassSubjectCollapse">
                <form action="<?= Url::to('/admin/assignments/class-subject') ?>" method="POST" class="row g-2">
                    <?= Csrf::field() ?>
                    <div class="col-sm-6">
                        <label class="form-label small text-muted">Select Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select form-select-sm" required>
                            <option value="">-- Choose Class --</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['class_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label small text-muted">Select Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select form-select-sm" required>
                            <option value="">-- Choose Subject --</option>
                            <?php foreach ($subjects as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['subject_name']) ?> (<?= $s['subject_code'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 text-end mt-2">
                        <button type="submit" class="btn btn-sm btn-primary px-3">Assign to Class</button>
                    </div>
                </form>
            </div>

            <!-- List Table -->
            <div class="table-responsive flex-1">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Subject</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($classSubjects)): ?>
                            <tr><td colspan="3" class="text-center py-4 text-muted">No class-subject relationships mapped.</td></tr>
                        <?php else: ?>
                            <?php foreach ($classSubjects as $cs): ?>
                                <tr>
                                    <td class="fw-semibold text-dark">
                                        <i class="bi bi-mortarboard text-muted me-1"></i> <?= htmlspecialchars($cs['class_name']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($cs['subject_code']) ?></span>
                                        <?= htmlspecialchars($cs['subject_name']) ?>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="deleteAssignment('class-subject', <?= $cs['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 2: Teacher to Class & Subject Assignment -->
    <div class="col-lg-6">
        <div class="card card-custom h-100">
            <div class="p-3 border-bottom bg-light-subtle d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-person-video3 text-info"></i> 2. Teacher Allocations
                </h6>
                <button class="btn btn-sm btn-info text-white rounded-pill" data-bs-toggle="collapse" data-bs-target="#addTeacherAssignmentCollapse">
                    <i class="bi bi-plus me-1"></i> Allocate Teacher
                </button>
            </div>

            <!-- Form Collapse -->
            <div class="collapse p-3 bg-light border-bottom" id="addTeacherAssignmentCollapse">
                <form action="<?= Url::to('/admin/assignments/teacher') ?>" method="POST" class="row g-2">
                    <?= Csrf::field() ?>
                    <div class="col-sm-4">
                        <label class="form-label small text-muted">Teacher <span class="text-danger">*</span></label>
                        <select name="teacher_id" class="form-select form-select-sm" required>
                            <option value="">-- Choose Teacher --</option>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small text-muted">Class <span class="text-danger">*</span></label>
                        <select name="class_id" class="form-select form-select-sm" required>
                            <option value="">-- Choose Class --</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['class_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label small text-muted">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select form-select-sm" required>
                            <option value="">-- Choose Subject --</option>
                            <?php foreach ($subjects as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['subject_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 text-end mt-2">
                        <button type="submit" class="btn btn-sm btn-info text-white px-3">Allocate Teacher</button>
                    </div>
                </form>
            </div>

            <!-- List Table -->
            <div class="table-responsive flex-1">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Teacher</th>
                            <th>Assigned Class</th>
                            <th>Subject</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($teacherAssignments)): ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">No teacher assignments recorded.</td></tr>
                        <?php else: ?>
                            <?php foreach ($teacherAssignments as $ta): ?>
                                <tr>
                                    <td class="fw-semibold text-dark">
                                        <i class="bi bi-person-workspace text-info me-1"></i> <?= htmlspecialchars($ta['teacher_name']) ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($ta['class_name']) ?></span></td>
                                    <td class="small text-muted"><?= htmlspecialchars($ta['subject_name']) ?></td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="deleteAssignment('teacher', <?= $ta['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
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

<form id="genericDeleteForm" method="POST" style="display: none;">
    <?= Csrf::field() ?>
</form>

<script>
    function deleteAssignment(type, id) {
        if (confirm('Are you sure you want to remove this assignment mapping?')) {
            const form = document.getElementById('genericDeleteForm');
            form.action = `<?= Url::to('/admin/assignments') ?>/${type}/${id}/delete`;
            form.submit();
        }
    }
</script>
