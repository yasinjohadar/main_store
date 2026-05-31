@php
    $selected = $selectedPermissions ?? [];
    $accordionId = $accordionId ?? 'rolePermissionsAccordion';
@endphp

<div class="role-permissions">
    <div class="role-permissions__toolbar">
        <label class="form-label fw-bold mb-0">الصلاحيات حسب التصنيف</label>
        <div class="role-permissions__toolbar-actions">
            <button type="button" class="btn btn-sm btn-outline-primary" data-perm-select-all>تحديد الكل</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-perm-clear-all>إلغاء الكل</button>
        </div>
    </div>

    <div class="accordion role-permissions__accordion" id="{{ $accordionId }}">
        @foreach($permissionGroups as $group)
            @php
                $groupChecked = collect($group['permissions'])->filter(fn ($p) => in_array($p['name'], $selected))->count();
                $groupTotal = count($group['permissions']);
                $collapseId = $accordionId . '_group_' . $group['key'];
                $isOpen = $loop->first || ($groupChecked > 0 && $groupChecked < $groupTotal);
            @endphp
            <div class="accordion-item role-permissions__group" data-perm-group="{{ $group['key'] }}">
                <h2 class="accordion-header" id="heading_{{ $collapseId }}">
                    <button class="accordion-button {{ $isOpen ? '' : 'collapsed' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $collapseId }}"
                            aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                            aria-controls="{{ $collapseId }}">
                        <span class="role-permissions__group-head">
                            <span class="role-permissions__group-icon"><i class="las {{ $group['icon'] }}"></i></span>
                            <span class="role-permissions__group-title">{{ $group['label'] }}</span>
                            <span class="admin-table__badge admin-table__badge--secondary role-permissions__count"
                                  data-group-count="{{ $group['key'] }}">{{ $groupChecked }}/{{ $groupTotal }}</span>
                        </span>
                    </button>
                </h2>
                <div id="{{ $collapseId }}"
                     class="accordion-collapse collapse {{ $isOpen ? 'show' : '' }}"
                     aria-labelledby="heading_{{ $collapseId }}"
                     data-bs-parent="#{{ $accordionId }}">
                    <div class="accordion-body">
                        <div class="role-permissions__group-actions">
                            <label class="form-check role-permissions__select-group mb-0">
                                <input type="checkbox"
                                       class="form-check-input"
                                       data-perm-group-toggle="{{ $group['key'] }}"
                                       {{ $groupChecked === $groupTotal && $groupTotal > 0 ? 'checked' : '' }}
                                       {{ $groupChecked > 0 && $groupChecked < $groupTotal ? 'data-indeterminate=1' : '' }}>
                                <span class="form-check-label fw-semibold">تحديد كل صلاحيات «{{ $group['label'] }}»</span>
                            </label>
                        </div>
                        <div class="role-permissions__grid">
                            @foreach($group['permissions'] as $permission)
                                <label class="role-permissions__item form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="permissions[{{ $permission['name'] }}]"
                                           value="{{ $permission['name'] }}"
                                           id="perm_{{ $permission['id'] }}"
                                           data-perm-group="{{ $group['key'] }}"
                                           {{ in_array($permission['name'], $selected) ? 'checked' : '' }}>
                                    <span class="form-check-label">
                                        <span class="role-permissions__item-label">{{ $permission['label'] }}</span>
                                        <code class="role-permissions__item-code">{{ $permission['name'] }}</code>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
