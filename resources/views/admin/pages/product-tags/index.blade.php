@extends('admin.layouts.master')

@section('page-title') وسوم المنتجات @stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <h5 class="my-4">وسوم المنتجات</h5>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="row">
            <div class="col-md-4">
                <div class="card"><div class="card-body">
                    <form method="POST" action="{{ route('admin.product-tags.store') }}">
                        @csrf
                        <input type="text" name="name" class="form-control mb-2" placeholder="اسم الوسم" required>
                        <button class="btn btn-primary w-100">إضافة</button>
                    </form>
                </div></div>
            </div>
            <div class="col-md-8">
                <div class="card"><div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>الوسم</th><th>المنتجات</th><th></th></tr></thead>
                        <tbody>
                            @foreach($tags as $tag)
                                <tr>
                                    <td>{{ $tag->name }}</td>
                                    <td>{{ $tag->products_count }}</td>
                                    <td>
                                        <x-admin.delete-trigger
                                            :action="route('admin.product-tags.destroy', $tag)"
                                            title="حذف الوسم"
                                            :item="$tag->name"
                                            message="هل تريد حذف هذا الوسم؟"
                                            button-class="btn-sm btn-outline-danger"
                                            label="حذف"
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $tags->links() }}
                </div></div>
            </div>
        </div>
    </div>
</div>
@stop
