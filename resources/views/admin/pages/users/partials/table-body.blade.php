<div class="table-responsive">
    <table class="admin-data-table">
        <thead>
            <tr>
                <th scope="col" style="width: 48px;">#</th>
                <th scope="col">المستخدم</th>
                <th scope="col">البريد</th>
                <th scope="col">الهاتف</th>
                <th scope="col">آخر دخول</th>
                <th scope="col">الأدوار</th>
                <th scope="col" style="width: 130px;">التفعيل</th>
                <th scope="col" style="width: 70px;">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                @php
                    $userSessions = $sessions->get($user->id);
                    $lastSession = $userSessions ? $userSessions->first() : null;
                @endphp
                <tr>
                    <th scope="row" class="row-number">{{ $users->firstItem() + $loop->index }}</th>

                    <td>
                        <div class="admin-user-cell">
                            @if ($user->photo)
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="" class="admin-avatar-img">
                            @else
                                <span class="admin-avatar-initial">{{ mb_substr($user->name, 0, 1) }}</span>
                            @endif
                            <a href="{{ route('users.show', $user->id) }}" class="admin-user-link">
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
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>
                        @if ($user->phone)
                            <span class="admin-phone-cell">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}"
                                    target="_blank" rel="noopener" title="فتح WhatsApp">
                                    <i class="fab fa-whatsapp me-1"></i>{{ $user->phone }}
                                </a>
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td class="text-muted">
                        @if ($lastSession)
                            {{ \Carbon\Carbon::createFromTimestamp($lastSession->last_activity)->diffForHumans() }}
                        @else
                            لا توجد جلسات
                        @endif
                    </td>

                    <td>
                        @forelse ($user->getRoleNames() as $role)
                            <span class="admin-badge admin-badge-role">{{ $role }}</span>
                        @empty
                            <span class="text-muted">—</span>
                        @endforelse
                    </td>

                    <td>
                        <div class="admin-status-switch">
                            <input type="checkbox"
                                   class="form-check-input user-active-switch"
                                   role="switch"
                                   id="user-active-{{ $user->id }}"
                                   {{ $user->is_active ? 'checked' : '' }}
                                   data-user-id="{{ $user->id }}"
                                   data-user-name="{{ e($user->name) }}"
                                   data-toggle-url="{{ route('users.toggle-status', $user) }}"
                                   @if($user->id === auth()->id()) disabled title="لا يمكنك تغيير تفعيل حسابك" @endif>
                            <label class="status-label {{ $user->is_active ? 'is-active' : '' }}" for="user-active-{{ $user->id }}">
                                {{ $user->is_active ? 'مفعّل' : 'موقوف' }}
                            </label>
                        </div>
                    </td>

                    <td>
                        <div class="admin-row-actions dropdown">
                            <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="las la-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.show', $user->id) }}">
                                        <i class="las la-eye text-info me-2"></i>عرض المستخدم
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                        <i class="las la-pen text-primary me-2"></i>تعديل
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#change_password{{ $user->id }}">
                                        <i class="las la-key text-warning me-2"></i>تغيير كلمة المرور
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                        data-confirm-delete
                                        data-action="{{ route('users.destroy', $user) }}"
                                        data-method="DELETE"
                                        data-title="حذف المستخدم"
                                        data-item="{{ $user->name }}"
                                        data-message="هل تريد حذف هذا المستخدم نهائياً؟"
                                        data-warning="لن يمكن التراجع عن هذا الإجراء.">
                                        <i class="las la-trash me-2"></i>حذف المستخدم
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="admin-empty-state">
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

<div class="admin-table-footer">
    {{ $users->withQueryString()->links() }}
</div>
