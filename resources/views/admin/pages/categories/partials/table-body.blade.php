<div class="table-responsive admin-table-wrap">
    <table class="table admin-table table-striped table-hover align-middle table-nowrap mb-0">
        <thead>
            <tr>
                <th scope="col" style="width: 48px;">#</th>
                <th scope="col">التصنيف</th>
                <th scope="col">الرابط</th>
                <th scope="col">التصنيف الأب</th>
                <th scope="col" style="width: 80px;">الترتيب</th>
                <th scope="col" style="width: 100px;">الحالة</th>
                <th scope="col" style="width: 140px;">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <th scope="row" class="admin-table__index">{{ $categories->firstItem() + $loop->index }}</th>

                    <td>
                        <div class="admin-table__user">
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="admin-table__thumb">
                            <a href="{{ route('admin.categories.show', $category->id) }}" class="admin-table__user-name">
                                {{ $category->name }}
                            </a>
                        </div>
                    </td>

                    <td>
                        @if($category->slug)
                            <code class="admin-table__slug">{{ $category->slug }}</code>
                        @else
                            <span class="admin-table__muted">—</span>
                        @endif
                    </td>

                    <td>
                        @if($category->parent)
                            <span class="admin-table__badge admin-table__badge--role">{{ $category->parent->name }}</span>
                        @else
                            <span class="admin-table__muted">—</span>
                        @endif
                    </td>

                    <td class="admin-table__index">{{ $category->order }}</td>

                    <td>
                        @if($category->status == 'active')
                            <span class="admin-table__badge admin-table__badge--success">نشط</span>
                        @else
                            <span class="admin-table__badge admin-table__badge--danger">غير نشط</span>
                        @endif
                    </td>

                    <td>
                        <div class="admin-table__actions">
                            @can('category-show')
                                <a class="admin-table__action admin-table__action--view"
                                    href="{{ route('admin.categories.show', $category->id) }}"
                                    title="عرض التصنيف">
                                    <i class="las la-eye"></i>
                                </a>
                            @endcan
                            @can('category-edit')
                                <a class="admin-table__action admin-table__action--edit"
                                    href="{{ route('admin.categories.edit', $category->id) }}"
                                    title="تعديل التصنيف">
                                    <i class="las la-pen"></i>
                                </a>
                            @endcan
                            @can('category-delete')
                                <x-admin.delete-trigger
                                    :action="route('admin.categories.destroy', $category)"
                                    title="حذف التصنيف"
                                    :item="$category->name"
                                    message="هل أنت متأكد من حذف هذا التصنيف؟"
                                    confirm-text="حذف التصنيف"
                                    button-class="btn-danger btn-sm"
                                    icon="las la-trash"
                                    title-attr="حذف التصنيف"
                                />
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="admin-table-empty">
                            <i class="las la-folder-open"></i>
                            <p class="mb-0 fw-medium">لا توجد تصنيفات</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-3 border-top admin-table-pagination">
    {{ $categories->withQueryString()->links() }}
</div>
