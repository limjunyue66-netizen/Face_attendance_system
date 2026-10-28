<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon primary">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Create New Class</h4>
                        <p class="text-muted small mb-0">Configure schedule timings, grace period, and rest window</p>
                    </div>
                </div>
                <a href="<?= Url::to('/admin/classes') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <form action="<?= Url::to('/admin/classes') ?>" method="POST">
                <?= Csrf::field() ?>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="class_code" class="form-label small fw-semibold text-dark">Class Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="class_code" name="class_code" placeholder="e.g. CS-Y1A" required>
                    </div>
                    <div class="col-md-6">
                        <label for="class_name" class="form-label small fw-semibold text-dark">Class Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="class_name" name="class_name" placeholder="e.g. Computer Science Year 1 A" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="start_time" class="form-label small fw-semibold text-dark">Class Start Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="start_time" name="start_time" value="08:00" required>
                        <div class="form-text small">Check-ins starting after this will evaluate the grace period.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="late_grace_minutes" class="form-label small fw-semibold text-dark">Late Grace Period (Minutes) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="late_grace_minutes" name="late_grace_minutes" value="15" min="0" max="120" required>
                        <div class="form-text small">e.g. 15 mins (arrival after 08:15 will be marked as Late).</div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="rest_start_time" class="form-label small fw-semibold text-dark">Rest / Break Start Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="rest_start_time" name="rest_start_time" value="12:00" required>
                    </div>
                    <div class="col-md-6">
                        <label for="rest_end_time" class="form-label small fw-semibold text-dark">Rest / Break End Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="rest_end_time" name="rest_end_time" value="13:00" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label small fw-semibold text-dark">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= Url::to('/admin/classes') ?>" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Save Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
