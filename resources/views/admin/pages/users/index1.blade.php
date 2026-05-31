@extends('admin.layouts.master')

@section('title')
    المستخدمين
@stop


@section('page_name')
    المستخدمين
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
    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex gap-3">
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">إنشاء مستخدم جديد</a>



                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            <form action="{{ route('users.index') }}" method="GET"
                                class="d-flex align-items-center gap-2">
                                {{-- حقل البحث --}}
                                <input style="width: 300px" type="text" name="query" class="form-control"
                                    placeholder="بحث بالاسم أو الإيميل أو الهاتف" value="{{ request('query') }}">

                                <select name="is_active" class="form-select">
                                    <option value="">كل حالات التفعيل</option>
                                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>مفعّل</option>
                                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>موقوف</option>
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
                                        <th scope="col" style="min-width: 130px;">التفعيل</th>
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
                                                        target="_blank" class="text-success text-decoration-none me-1"
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
                                                <div class="form-check form-switch admin-table__switch mb-0">
                                                    <input type="checkbox"
                                                           class="form-check-input user-active-switch"
                                                           role="switch"
                                                           id="user-active-{{ $user->id }}"
                                                           {{ $user->is_active ? 'checked' : '' }}
                                                           data-user-id="{{ $user->id }}"
                                                           data-user-name="{{ e($user->name) }}"
                                                           data-toggle-url="{{ route('users.toggle-status', $user) }}"
                                                           @if($user->id === auth()->id()) disabled title="لا يمكنك تغيير تفعيل حسابك" @endif>
                                                    <label class="form-check-label" for="user-active-{{ $user->id }}">
                                                        {{ $user->is_active ? 'مفعّل' : 'موقوف' }}
                                                    </label>
                                                </div>
                                            </td>

                                            <td>
                                                <a class="btn btn-info btn-sm me-1"
                                                    href="{{ route('users.edit', $user->id) }}">
                                                    <i class="fa-solid fa-pen-to-square"></i> تعديل
                                                </a>
                                                <x-admin.delete-trigger
                                                    :action="route('users.destroy', $user)"
                                                    title="حذف المستخدم"
                                                    :item="$user->name"
                                                    message="هل تريد حذف هذا المستخدم نهائياً؟"
                                                    button-class="btn-danger btn-sm me-1"
                                                    label="حذف"
                                                />
                                                <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#change_password{{$user->id}}">
                                                    <i class="fa-solid fa-key"></i> تعديل كلمة السر
                                                </a>
                                            </td>
                                        </tr>

                                        @include('admin.pages.users.change_password')
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-danger fw-bold">لا توجد بيانات متاحة
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
    </div><!-- end card-body -->
    </div><!-- end card -->
    </div>
    <!-- end col -->
    </div>

@stop




@section('script')
    <script src="{{ asset('assets/js/admin-user-toggle.js') }}"></script>
@stop

