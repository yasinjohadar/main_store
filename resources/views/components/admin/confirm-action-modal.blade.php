{{-- Singleton action confirmation modal (toggle, etc.) — included once in admin master layout --}}
<div class="modal fade admin-confirm-action" id="adminConfirmActionModal" tabindex="-1"
     aria-labelledby="adminConfirmActionTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-confirm-action__content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4 px-4">
                <div class="admin-confirm-action__icon-wrap mx-auto mb-3">
                    <i class="las la-power-off admin-confirm-action__icon" id="adminConfirmActionIcon" aria-hidden="true"></i>
                </div>
                <h5 class="admin-confirm-action__title mb-2" id="adminConfirmActionTitle">تأكيد الإجراء</h5>
                <p class="admin-confirm-action__message text-muted mb-1" id="adminConfirmActionMessage">
                    هل أنت متأكد من تنفيذ هذا الإجراء؟
                </p>
                <p class="admin-confirm-action__item fw-semibold mb-2 d-none" id="adminConfirmActionItem"></p>
                <p class="admin-confirm-action__hint small mb-0 d-none" id="adminConfirmActionHint">
                    <i class="las la-info-circle me-1" aria-hidden="true"></i>
                    <span></span>
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2 pb-4 px-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="adminConfirmActionCancel">
                    <i class="las la-times me-1" aria-hidden="true"></i>
                    <span id="adminConfirmActionCancelText">إلغاء</span>
                </button>
                <button type="button" class="btn btn-primary" id="adminConfirmActionSubmit">
                    <i class="las la-check me-1" aria-hidden="true"></i>
                    <span id="adminConfirmActionSubmitText">تأكيد</span>
                </button>
            </div>
        </div>
    </div>
</div>
