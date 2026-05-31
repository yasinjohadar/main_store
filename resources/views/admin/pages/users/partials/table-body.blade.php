<div class="table-responsive admin-table-wrap">
    <table class="table admin-table table-striped table-hover align-middle table-nowrap mb-0">
        <thead>
            <tr>
                <th scope="col" style="width: 48px;">#</th>
                <th scope="col">المستخدم</th>
                <th scope="col">البريد</th>
                <th scope="col">الهاتف</th>
                <th scope="col">آخر دخول</th>
                <th scope="col">الأدوار</th>
                <th scope="col" style="width: 130px;">التفعيل</th>
                <th scope="col" style="width: 140px;">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                @php
                    $userSessions = $sessions->get($user->id);
                    $lastSession = $userSessions ? $userSessions->first() : null;
                @endphp
                <tr>
                    <th scope="row" class="admin-table__index">{{ $users->firstItem() + $loop->index }}</th>

                    <td>
                        <div class="admin-table__user">
                            <span class="admin-table__avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                            <a href="{{ route('users.show', $user->id) }}" class="admin-table__user-name">
                                {{ $user->name }}
                            </a>
                        </div>
                    </td>

                    <td>
                        @if ($user->email)
                            <a href="mailto:{{ $user->email }}" class="text-decoration-none" title="إرسال بريد إلكتروني">
                                {{ $user->email }}
                            </a>
                        @else
                            <span class="admin-table__muted">—</span>
                        @endif
                    </td>

                    <td>
                        @if ($user->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}"
                                target="_blank" class="text-success text-decoration-none" title="فتح WhatsApp">
                                <i class="fab fa-whatsapp me-1"></i>{{ $user->phone }}
                            </a>
                        @else
                            <span class="admin-table__muted">—</span>
                        @endif
                    </td>

                    <td class="admin-table__muted">
                        @if ($lastSession)
                            {{ \Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->diffForHumans() }}
                        @else
                            لا توجد جلسات
                        @endif
                    </td>

                    <td>
                        @forelse ($user->getRoleNames() as $role)
                            <span class="admin-table__badge admin-table__badge--role me-1">{{ $role }}</span>
                        @empty
                            <span class="admin-table__muted">—</span>
                        @endforelse
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
                        <div class="admin-table__actions">
                            <a class="admin-table__action admin-table__action--view"
                                href="{{ route('users.show', $user->id) }}"
                                title="عرض المستخدم">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="admin-table__action admin-table__action--edit"
                                href="{{ route('users.edit', $user->id) }}"
                                title="تعديل المستخدم">
                                <i class="las la-pen"></i>
                            </a>
                            <x-admin.delete-trigger
                                :action="route('users.destroy', $user)"
                                title="حذف المستخدم"
                                :item="$user->name"
                                message="هل تريد حذف هذا المستخدم نهائياً؟"
                                button-class="btn-danger btn-sm"
                                icon="las la-trash"
                                title-attr="حذف المستخدم"
                            />
                            <a href="#" class="admin-table__action admin-table__action--warning"
                                data-bs-toggle="modal"
                                data-bs-target="#change_password{{ $user->id }}"
                                title="تعديل كلمة السر">
                                <i class="las la-key"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="admin-table-empty">
                            <i class="las la-users"></i>
                            <p class="mb-0 fw-medium">لا توجد بيانات متاحة</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@foreach ($users as $user)
    @include('admin.pages.users.change_password')
@endforeach

<div class="p-3 border-top admin-table-pagination">
    {{ $users->withQueryString()->links() }}
</div>
