<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Leave Applications</h3>
        <p class="text-muted small mb-0">Submit justified absence requests and upload supporting documentation</p>
    </div>
</div>

<div class="row g-4">
    <!-- Application Submission Form -->
    <div class="col-lg-5">
        <div class="card card-custom p-4">
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-plus text-primary"></i> Submit Leave Request
            </h5>

            <form action="<?= Url::to('/student/leaves') ?>" method="POST" enctype="multipart/form-data">
                <?= Csrf::field() ?>

                <div class="mb-3">
                    <label for="leave_date" class="form-label small fw-semibold text-dark">Anticipated Date of Absence <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="leave_date" name="leave_date" min="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label small fw-semibold text-dark">Reason for Absence <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Provide clear explanation (medical appointment, emergency, etc.)..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="document" class="form-label small fw-semibold text-dark">Supporting Document (PDF, JPG, PNG - Max 5MB)</label>
                    <input type="file" class="form-control" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="form-text small text-muted">Upload doctor's medical certificate or official letter.</div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary py-2 rounded-pill fw-semibold">
                        <i class="bi bi-send-fill me-1"></i> Submit Leave Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Application Status List -->
    <div class="col-lg-7">
        <div class="card card-custom h-100">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-muted"></i> My Leave Request History
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Leave Date</th>
                            <th>Reason</th>
                            <th>Document</th>
                            <th>Status</th>
                            <th>Admin Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leaves)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-envelope-paper fs-2 d-block mb-2"></i>
                                    You have not submitted any leave applications yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($leaves as $l): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($l['leave_date']) ?></td>
                                    <td class="small text-muted" style="max-width: 200px;">
                                        <?= htmlspecialchars($l['reason']) ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($l['document_path'])): ?>
                                            <a href="<?= Url::asset($l['document_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill small">
                                                <i class="bi bi-file-earmark-arrow-down me-1"></i> View
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge-status status-<?= $l['status'] ?>">
                                            <?= ucfirst($l['status']) ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        <?= htmlspecialchars($l['review_note'] ?? '-') ?>
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
