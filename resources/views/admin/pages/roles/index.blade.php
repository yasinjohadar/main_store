@extends('admin.layouts.master')

@section('page-title')
   الأدوار
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
        <div class="container-fluid admin-page-shell">

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="las la-user-shield"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>جدول الأدوار</h1>
                        <p>إدارة الأدوار والصلاحيات في النظام</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('roles.create') }}" class="admin-btn admin-btn-light">
                        <i class="las la-plus"></i>
                        إضافة دور جديد
                    </a>
                </div>
            </div>

            <div class="admin-report-stats">
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-blue">
                    <span class="admin-report-stat-icon"><i class="las la-user-shield"></i></span>
                    <span class="admin-report-stat-label">إجمالي الأدوار</span>
                    <span class="admin-report-stat-value">{{ $roleStats['total'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-cyan">
                    <span class="admin-report-stat-icon"><i class="las la-key"></i></span>
                    <span class="admin-report-stat-label">إجمالي الصلاحيات</span>
                    <span class="admin-report-stat-value">{{ $roleStats['permissions'] ?? 0 }}</span>
                </div>
                <div class="admin-report-stat admin-report-stat-static admin-report-stat-amber">
                    <span class="admin-report-stat-icon"><i class="las la-exclamation-triangle"></i></span>
                    <span class="admin-report-stat-label">أدوار بدون صلاحيات</span>
                    <span class="admin-report-stat-value">{{ $roleStats['without_permissions'] ?? 0 }}</span>
                </div>
            </div>

            <div class="admin-page-card">
                <div class="card-toolbar">
                    <div class="admin-filters">
                        <div class="search-input-wrap">
                            <i class="las la-search"></i>
                            <input type="text" id="roles-search" class="form-control" placeholder="بحث باسم الدور..." autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="admin-table-wrap">
                    <div class="table-responsive">
                        <table class="admin-data-table" id="roles-table">
                            <thead>
                                <tr>
                                    <th style="width: 48px;">#</th>
                                    <th>اسم الدور</th>
                                    <th>الصلاحيات</th>
                                    <th style="width: 70px;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <td class="row-number">{{ $loop->iteration }}</td>
                                        <td class="fw-bold">{{ $role->name }}</td>
                                        <td>
                                            <span class="admin-badge admin-badge-role">{{ $role->permissions_count ?? $role->permissions->count() }} صلاحية</span>
                                        </td>
                                        <td>
                                            <div class="admin-row-actions dropdown">
                                                <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="las la-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('roles.edit', $role->id) }}">
                                                            <i class="las la-pen text-primary me-2"></i>تعديل
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                                            data-confirm-delete
                                                            data-action="{{ route('roles.destroy', $role) }}"
                                                            data-method="DELETE"
                                                            data-title="حذف الدور"
                                                            data-item="{{ $role->name }}"
                                                            data-message="هل تريد حذف هذا الدور نهائياً؟"
                                                            data-warning="لن يمكن التراجع عن هذا الإجراء.">
                                                            <i class="las la-trash me-2"></i>حذف الدور
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="admin-empty-state">
                                                <i class="las la-user-shield"></i>
                                                لا توجد بيانات متاحة
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- End::app-content -->
@stop

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var search = document.getElementById('roles-search');
        var table = document.getElementById('roles-table');
        if (!search || !table) return;

        search.addEventListener('input', function () {
            var term = this.value.trim().toLowerCase();
            table.querySelectorAll('tbody tr').forEach(function (row) {
                var name = row.children[1] ? row.children[1].textContent.toLowerCase() : '';
                row.style.display = !term || name.includes(term) ? '' : 'none';
            });
        });
    });
</script>
@stop
