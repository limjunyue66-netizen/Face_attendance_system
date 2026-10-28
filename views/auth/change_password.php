<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                <div class="stat-icon primary">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Change Password</h5>
                    <p class="text-muted small mb-0">Update your account authentication credentials</p>
                </div>
            </div>

            <form action="<?= Url::to('/change-password') ?>" method="POST">
                <?= Csrf::field() ?>

                <div class="mb-3">
                    <label for="current_password" class="form-label small fw-semibold text-dark">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label small fw-semibold text-dark">New Password (Min 6 chars)</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" required>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label small fw-semibold text-dark">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= Url::to('/') ?>" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
