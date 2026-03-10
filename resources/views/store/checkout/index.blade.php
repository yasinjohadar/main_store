@extends('store.layouts.master')

@section('title', 'إتمام الطلب')

@section('content')
    <h4>إتمام الطلب</h4>
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('store.checkout.store') }}">
                @csrf
                <div class="card mb-3">
                    <div class="card-header">عنوان الشحن</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">الاسم الأول</label>
                                <input type="text" name="shipping_first_name" class="form-control" value="{{ old('shipping_first_name', auth()->user()->name ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">اسم العائلة</label>
                                <input type="text" name="shipping_last_name" class="form-control" value="{{ old('shipping_last_name') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">الهاتف</label>
                                <input type="text" name="shipping_phone" class="form-control" value="{{ old('shipping_phone') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">العنوان</label>
                                <input type="text" name="shipping_address_line_1" class="form-control" value="{{ old('shipping_address_line_1') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المدينة</label>
                                <input type="text" name="shipping_city" class="form-control" value="{{ old('shipping_city') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الدولة</label>
                                <input type="text" name="shipping_country" class="form-control" value="{{ old('shipping_country', 'SA') }}" required maxlength="2">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المنطقة / الولاية</label>
                                <input type="text" name="shipping_state" class="form-control" value="{{ old('shipping_state') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الرمز البريدي</label>
                                <input type="text" name="shipping_postal_code" class="form-control" value="{{ old('shipping_postal_code') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">إنشاء الطلب</button>
            </form>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">ملخص السلة</div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($cart->items as $item)
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span>
                                    {{ $item->product->name }}
                                    @if($item->variant)<br><small class="text-muted">{{ $item->variant->display_name }}</small>@endif
                                </span>
                                <span>{{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} ر.س</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-2 mb-0"><strong>المجموع: {{ number_format($cart->subtotal, 2) }} ر.س</strong></p>
                </div>
            </div>
        </div>
    </div>
@endsection
