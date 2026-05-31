@extends('frontend.layouts.master')

@section('title', 'دفع الطلب #' . $order->order_number . ' - ' . config('app.name'))
@section('body-class', 'checkout')
@section('header-class', 'header-border')

@section('content')
    @include('frontend.partials.page.page-header', [
        'title' => 'دفع الطلب #' . $order->order_number,
        'icon' => 'w-icon-cart',
    ])

    <div class="page-content store-checkout-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card p-4">
                        <p class="mb-2">الإجمالي: <strong>{{ number_format((float) $order->total, 2) }} {{ $order->currency }}</strong></p>
                        <p class="text-muted small mb-4">اختر طريقة الدفع لإكمال الطلب.</p>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <p class="mb-0">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('frontend.orders.pay.store', $order) }}">
                            @csrf
                            @forelse($paymentMethods as $method)
                                <label class="d-flex align-items-center gap-2 mb-2">
                                    <input type="radio" name="payment_method_id" value="{{ $method->id }}" @checked($loop->first) required>
                                    <span>{{ $method->name }}</span>
                                </label>
                            @empty
                                <p class="text-muted">لا توجد طرق دفع متاحة.</p>
                            @endforelse

                            @if($paymentMethods->isNotEmpty())
                                <button type="submit" class="btn btn-primary w-100 mt-3">متابعة الدفع</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
