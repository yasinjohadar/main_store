<div class="table-responsive">
    <table class="admin-data-table">
        <thead>
            <tr>
                <th scope="col" style="width: 48px;">#</th>
                <th scope="col">التصنيف</th>
                <th scope="col">الرابط</th>
                <th scope="col">التصنيف الأب</th>
                <th scope="col" style="width: 80px;">الترتيب</th>
                <th scope="col" style="width: 100px;">الحالة</th>
                <th scope="col" style="width: 70px;">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <th scope="row" class="row-number">{{ $categories->firstItem() + $loop->index }}</th>

                    <td>
                        <div class="admin-user-cell">
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="admin-thumb">
                            <a href="{{ route('admin.categories.show', $category->id) }}" class="admin-user-link">
                                {{ $category->name }}
                            </a>
                        </div>
                    </td>

                    <td>
                        @if($category->slug)
                            <code>{{ $category->slug }}</code>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td>
                        @if($category->parent)
                            <span class="admin-badge admin-badge-role">{{ $category->parent->name }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td class="text-muted">{{ $category->order }}</td>

                    <td>
                        @if($category->status == 'active')
                            <span class="admin-badge admin-badge-success">نشط</span>
                        @else
                            <span class="admin-badge admin-badge-danger">غير نشط</span>
                        @endif
                    </td>

                    <td>
                        <div class="admin-row-actions dropdown">
                            <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="las la-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('category-show')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.categories.show', $category->id) }}">
                                            <i class="las la-eye text-info me-2"></i>عرض التصنيف
                                        </a>
                                    </li>
                                @endcan
                                @can('category-edit')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.categories.edit', $category->id) }}">
                                            <i class="las la-pen text-primary me-2"></i>تعديل
                                        </a>
                                    </li>
                                @endcan
                                @can('category-delete')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start"
                                            data-confirm-delete
                                            data-action="{{ route('admin.categories.destroy', $category) }}"
                                            data-method="DELETE"
                                            data-title="حذف التصنيف"
                                            data-item="{{ $category->name }}"
                                            data-message="هل أنت متأكد من حذف هذا التصنيف؟"
                                            data-warning="لن يمكن التراجع عن هذا الإجراء.">
                                            <i class="las la-trash me-2"></i>حذف التصنيف
                                        </button>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="admin-empty-state">
                            <i class="las la-folder-open"></i>
                            <p class="mb-0 fw-medium">لا توجد تصنيفات</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="admin-table-footer">
    {{ $categories->withQueryString()->links() }}
</div>
