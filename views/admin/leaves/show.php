<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon warning">
                        <i class="bi bi-envelope-paper-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Review Leave Request #<?= $leave['id'] ?></h4>
                        <p class="text-muted small mb-0">Submitted by <strong><?= htmlspecialchars($leave['student_name']) ?></strong> (<?= htmlspecialchars($leave['student_code']) ?>)</p>
                    </div>
                </div>
                <a href="<?= Url::to('/admin/leaves') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <!-- Application Details Summary -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">Requested Leave Date:</div>
                        <div class="h5 fw-bold text-dark mb-0"><?= htmlspecialchars($leave['leave_date']) ?></div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="small text-muted">Current Application Status:</div>
                        <span class="badge-status status-<?= $leave['status'] ?> fs-6 mt-1">
                            <?= ucfirst($leave['status']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-semibold text-dark">Reason for Leave Absence:</label>
                <div class="p-3 rounded-3 border bg-light-subtle text-dark">
                    <?= nl2br(htmlspecialchars($leave['reason'])) ?>
                </div>
            </div>

            <!-- Supporting Document Preview -->
            <div class="mb-4">
                <label class="form-label small fw-semibold text-dark">Attached Supporting Document:</label>
                <?php if (!empty($leave['document_path'])): ?>
                    <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-pdf fs-3 text-danger"></i>
                            <div>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($leave['original_filename'] ?: 'Document') ?></div>
                                <div class="small text-muted">Uploaded by student for absence justification</div>
                            </div>
                        </div>
                        <a href="<?= Url::asset($leave['document_path']) ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Open Document
                        </a>
                    </div>
                <?php else: ?>
                    <div class="p-3 bg-light rounded-3 text-muted small">
                        <i class="bi bi-info-circle me-1"></i> No medical or supporting documents were attached.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Action Form -->
            <div class="p-4 bg-light rounded-3 border mt-4">
                <h6 class="fw-bold text-dark mb-3">Review Decision</h6>
                <form action="<?= Url::to("/admin/leaves/{$leave['id']}/review") ?>" method="POST">
                    <?= Csrf::field() ?>

                    <div class="mb-3">
                        <label for="review_note" class="form-label small fw-semibold text-dark">Administrator Note / Remarks (Optional)</label>
                        <textarea class="form-control" id="review_note" name="review_note" rows="2" placeholder="Provide reason or remarks for student..."><?= htmlspecialchars($leave['review_note'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" name="action" value="rejected" class="btn btn-outline-danger px-4 rounded-pill">
                            <i class="bi bi-x-circle me-1"></i> Reject Application
                        </button>
                        <button type="submit" name="action" value="approved" class="btn btn-success px-4 rounded-pill">
                            <i class="bi bi-check-circle me-1"></i> Approve Leave Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
