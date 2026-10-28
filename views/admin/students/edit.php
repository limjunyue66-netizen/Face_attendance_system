<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon primary">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Edit Student Record</h4>
                        <p class="text-muted small mb-0">Update information for <?= htmlspecialchars($student['full_name']) ?></p>
                    </div>
                </div>
                <a href="<?= Url::to('/admin/students') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <form action="<?= Url::to("/admin/students/{$student['id']}") ?>" method="POST">
                <?= Csrf::field() ?>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="student_code" class="form-label small fw-semibold text-dark">Student Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="student_code" name="student_code" value="<?= htmlspecialchars($student['student_code']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="full_name" class="form-label small fw-semibold text-dark">Full Legal Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-semibold text-dark">Student Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-semibold text-dark">Contact Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($student['phone'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label for="class_id" class="form-label small fw-semibold text-dark">Assigned Class <span class="text-danger">*</span></label>
                        <select class="form-select" id="class_id" name="class_id" required>
                            <option value="">-- Select Class --</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ((int)$student['class_id'] === (int)$c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['class_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="gender" class="form-label small fw-semibold text-dark">Gender <span class="text-danger">*</span></label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="male" <?= ($student['gender'] === 'male') ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= ($student['gender'] === 'female') ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= ($student['gender'] === 'other') ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_of_birth" class="form-label small fw-semibold text-dark">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($student['date_of_birth'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label small fw-semibold text-dark">Account Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= ($student['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($student['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="<?= Url::to('/admin/students') ?>" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Update Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
