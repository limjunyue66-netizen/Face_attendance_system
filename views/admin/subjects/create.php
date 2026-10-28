<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon info">
                        <i class="bi bi-journal-plus"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Add New Subject</h4>
                        <p class="text-muted small mb-0">Register course or module into syllabus</p>
                    </div>
                </div>
                <a href="<?= Url::to('/admin/subjects') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <form action="<?= Url::to('/admin/subjects') ?>" method="POST">
                <?= Csrf::field() ?>

                <div class="mb-3">
                    <label for="subject_code" class="form-label small fw-semibold text-dark">Subject Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subject_code" name="subject_code" placeholder="e.g. CS101" required>
                </div>

                <div class="mb-3">
                    <label for="subject_name" class="form-label small fw-semibold text-dark">Subject Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subject_name" name="subject_name" placeholder="e.g. Introduction to Programming" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label small fw-semibold text-dark">Course Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief outline of syllabus and requirements..."></textarea>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label small fw-semibold text-dark">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= Url::to('/admin/subjects') ?>" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Save Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
