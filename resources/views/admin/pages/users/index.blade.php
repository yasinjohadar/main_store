@extends('admin.layouts.master')

@section('page-title')
    قائمة المستخدمون
@stop



@section('css')
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if (\Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!! \Session::get('error') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة المستخدمين</h5>

                </div>


            </div>
            <!-- Page Header Close -->



            <!-- Start::row-1 -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card" data-users-toggle-url="{{ route('users.toggle-status', ['id' => 0]) }}">
                        <div class="card-header align-items-center d-flex gap-3">
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">إنشاء مستخدم جديد</a>

                            <div class="flex-shrink-0">
                                <div class="form-check form-switch form-switch-right form-switch-md">
                                    <form action="{{ route('users.index') }}" method="GET"
                                        class="d-flex align-items-center gap-2">
                                        {{-- حقل البحث --}}
                                        <input style="width: 300px" type="text" name="query" class="form-control"
                                            placeholder="بحث بالاسم أو الإيميل أو الهاتف" value="{{ request('query') }}">

                                        {{-- فلتر الحالة النشطة --}}
                                        <select name="is_active" class="form-select">
                                            <option value="">كل الحالات النشطة</option>
                                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                        </select>

                                        <select name="status" class="form-select">
                                            <option value="">كل الحالات</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>فعال
                                            </option>
                                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>معلق
                                            </option>
                                            <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>محظور
                                                مؤقتاً
                                            </option>
                                            <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>محظور
                                                نهائياً
                                            </option>
                                        </select>

                                        {{-- عدد العناصر في الصفحة --}}
                                        <label class="text-nowrap text-muted small align-self-center mb-0">عرض:</label>
                                        <select name="per_page" class="form-select" style="width: auto;" title="عدد العناصر في الصفحة" onchange="this.form.submit()">
                                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                        </select>

                                        <button type="submit" class="btn btn-secondary">بحث</button>
                                        <a href="{{ route('users.index') }}" class="btn btn-danger">مسح </a>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <div class="card-body">
                            <p class="text-muted">
                            <div class="">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col" style="width: 40px;">#</th>
                                                <th scope="col" style="min-width: 150px;">اسم المستخدم</th>
                                                <th scope="col" style="min-width: 200px;">البريد</th>
                                                <th scope="col" style="min-width: 120px;">الهاتف</th>
                                                <th scope="col" style="min-width: 130px;">اخر دخول</th>
                                                <th scope="col" style="min-width: 150px;">الأدوار</th>
                                                <th scope="col" style="min-width: 110px;">الحالة</th>
                                                <th scope="col" style="min-width: 120px;">الحالة النشطة</th>
                                                <th scope="col" style="min-width: 200px;">العمليات</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @forelse ($users as $user)
                                                @php
                                                    $userSessions = $sessions->get($user->id);
                                                    $lastSession = $userSessions ? $userSessions->first() : null;
                                                @endphp
                                                <tr>
                                                    <th scope="row">{{ $loop->iteration }}</th>

                                                    <td>
                                                        <a href="{{ route('users.show', $user->id) }}"
                                                            class="text-decoration-none">
                                                            {{ $user->name }}
                                                        </a>
                                                    </td>

                                                    <td>
                                                        @if ($user->email)
                                                            <a href="mailto:{{ $user->email }}"
                                                                class="text-primary text-decoration-none"
                                                                title="إرسال بريد إلكتروني">
                                                                {{ $user->email }}
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if ($user->phone)
                                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}"
                                                                target="_blank"
                                                                class="text-success text-decoration-none me-1"
                                                                title="فتح WhatsApp">
                                                                <i class="fab fa-whatsapp"></i>
                                                            </a>
                                                            {{ $user->phone }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if ($lastSession)
                                                            {{ \Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->diffForHumans() }}
                                                        @else
                                                            لا توجد جلسات
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @foreach ($user->getRoleNames() as $role)
                                                            <span class="badge bg-primary me-1">{{ $role }}</span>
                                                        @endforeach
                                                    </td>

                                                    <td>
                                                        @if ($user->status === 'active')
                                                            <span class="badge bg-success">مفعل</span>
                                                        @elseif($user->status === 'inactive')
                                                            <span class="badge bg-warning text-dark">موقوف</span>
                                                        @elseif($user->status === 'banned')
                                                            <span class="badge bg-danger">محظور</span>
                                                        @else
                                                            <span class="badge bg-secondary">غير معروف</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <button type="button"
                                                                class="btn btn-sm btn-toggle-status {{ $user->is_active ? 'btn-success' : 'btn-danger' }}"
                                                                data-user-id="{{ $user->id }}"
                                                                data-user-name="{{ e($user->name) }}"
                                                                data-is-active="{{ $user->is_active ? '1' : '0' }}"
                                                                title="{{ $user->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                                                        </button>
                                                    </td>

                                                    <td>
                                                        <a class="btn btn-info btn-sm me-1"
                                                            href="{{ route('users.edit', $user->id) }}"
                                                            title="تعديل المستخدم">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                        <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal"
                                                            data-bs-target="#delete{{ $user->id }}"
                                                            title="حذف المستخدم">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </a>
                                                        <a href="#" class="btn btn-warning btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#change_password{{ $user->id }}"
                                                            title="تعديل كلمة السر">
                                                            <i class="fa-solid fa-key"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                @include('admin.pages.users.delete')
                                                @include('admin.pages.users.change_password')
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-danger fw-bold">لا توجد
                                                        بيانات متاحة
                                                    </td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                    </table>

                                    <div class="mt-3">
                                        {{ $users->withQueryString()->links() }}
                                    </div>
                                </div>
                            </div>



                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div>
            </div>
            <!--End::row-1 -->


        </div>
    </div>
    <!-- End::app-content -->

    <!-- مودال تأكيد تغيير الحالة النشطة -->
    <div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="toggleStatusModalLabel">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>تأكيد تغيير الحالة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body pt-0">
                    <p id="toggleStatusModalMessage" class="mb-0"></p>
                    <p class="text-muted small mb-0 mt-2">
                        <i class="bi bi-info-circle me-1"></i>سيتم تحديث حالة المستخدم فور التأكيد.
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" id="toggleStatusModalConfirm">تأكيد</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let pendingToggleButton = null;
    const modal = document.getElementById('toggleStatusModal');
    const modalMessage = document.getElementById('toggleStatusModalMessage');
    const modalConfirmBtn = document.getElementById('toggleStatusModalConfirm');

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
        alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>';
        const container = document.querySelector('.main-content');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
        } else {
            document.body.insertBefore(alertDiv, document.body.firstChild);
        }
        setTimeout(function() {
            if (alertDiv.parentNode) alertDiv.remove();
        }, 3000);
    }

    function buildToggleUrl(userId) {
        const card = document.querySelector('[data-users-toggle-url]');
        const baseUrl = card ? card.getAttribute('data-users-toggle-url') : '';
        return baseUrl ? baseUrl.replace(/\/0\/toggle-status/, '/' + userId + '/toggle-status') : '/users/' + userId + '/toggle-status';
    }

    document.querySelectorAll('.btn-toggle-status').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name') || '';
            const isActive = this.getAttribute('data-is-active') === '1';
            if (!userId) return;
            pendingToggleButton = this;
            if (isActive) {
                modalMessage.textContent = 'هل أنت متأكد من إلغاء تفعيل المستخدم «' + userName + '»؟ لن يتمكّن من تسجيل الدخول حتى إعادة التفعيل.';
            } else {
                modalMessage.textContent = 'هل أنت متأكد من تفعيل المستخدم «' + userName + '»؟';
            }
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        });
    });

    modalConfirmBtn.addEventListener('click', function() {
        if (!pendingToggleButton) return;
        const userId = pendingToggleButton.getAttribute('data-user-id');
        const url = buildToggleUrl(userId);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        modalConfirmBtn.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(function(response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                const isActive = Boolean(data.is_active);
                pendingToggleButton.textContent = isActive ? 'نشط' : 'غير نشط';
                pendingToggleButton.setAttribute('data-is-active', isActive ? '1' : '0');
                pendingToggleButton.classList.remove('btn-success', 'btn-danger');
                pendingToggleButton.classList.add(isActive ? 'btn-success' : 'btn-danger');
                pendingToggleButton.title = isActive ? 'إلغاء التفعيل' : 'تفعيل';
                showAlert(data.message || 'تم تحديث حالة المستخدم بنجاح', 'success');
                bootstrap.Modal.getInstance(modal).hide();
            } else {
                showAlert(data.message || 'حدث خطأ', 'error');
            }
        })
        .catch(function(err) {
            showAlert('حدث خطأ أثناء تحديث حالة المستخدم.', 'error');
        })
        .finally(function() {
            modalConfirmBtn.disabled = false;
            pendingToggleButton = null;
        });
    });
});
</script>
@stop
