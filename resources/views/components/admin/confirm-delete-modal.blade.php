{{-- Singleton delete confirmation modal — included once in admin master layout --}}
<div class="modal fade admin-confirm-delete" id="adminConfirmDeleteModal" tabindex="-1"
     aria-labelledby="adminConfirmDeleteTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-confirm-delete__content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4 px-4">
                <div class="admin-confirm-delete__icon-wrap mx-auto mb-3">
                    <i class="las la-trash-alt admin-confirm-delete__icon" id="adminConfirmDeleteIcon" aria-hidden="true"></i>
                </div>
                <h5 class="admin-confirm-delete__title mb-2" id="adminConfirmDeleteTitle">تأكيد الحذف</h5>
                <p class="admin-confirm-delete__message text-muted mb-1" id="adminConfirmDeleteMessage">
                    هل أنت متأكد من حذف هذا العنصر؟
                </p>
                <p class="admin-confirm-delete__item fw-semibold mb-2 d-none" id="adminConfirmDeleteItem"></p>
                <p class="admin-confirm-delete__warning small mb-0" id="adminConfirmDeleteWarning">
                    <i class="las la-exclamation-triangle me-1" aria-hidden="true"></i>
                    <span>لن يمكن التراجع عن هذا الإجراء.</span>
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center gap-2 pb-4 px-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="adminConfirmDeleteCancel">
                    <i class="las la-times me-1" aria-hidden="true"></i>
                    <span id="adminConfirmDeleteCancelText">إلغاء</span>
                </button>
                <button type="button" class="btn btn-danger" id="adminConfirmDeleteSubmit">
                    <i class="las la-trash-alt me-1" aria-hidden="true"></i>
                    <span id="adminConfirmDeleteSubmitText">نعم، احذف</span>
                </button>
            </div>
            <form id="adminConfirmDeleteForm" method="POST" class="d-none">
                @csrf
                <input type="hidden" name="_method" id="adminConfirmDeleteMethod" value="DELETE">
            </form>
        </div>
    </div>
</div>
