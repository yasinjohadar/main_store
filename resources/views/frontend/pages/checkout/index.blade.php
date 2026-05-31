@extends('frontend.layouts.master')



@section('title', 'إتمام الشراء - ' . config('app.name'))

@section('body-class', '')

@section('main-class', 'checkout')

@section('header-class', 'header-border')



@push('styles')

    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style-rtl.min.css') }}">

@endpush



@section('content')

    @include('frontend.partials.page.page-header', [
        'title' => 'إتمام الشراء',
        'icon' => 'w-icon-cart',
    ])

    @include('frontend.partials.checkout.breadcrumb')



    <div class="page-content store-checkout-page">

        <div class="container">

            @if(request()->boolean('cancelled'))
                <div class="store-cart-alert store-cart-alert--warning mb-4" role="alert">
                    <p class="mb-0">تم إلغاء عملية الدفع. يمكنك اختيار وسيلة دفع أخرى أو المحاولة مرة أخرى.</p>
                </div>
            @endif

            @include('frontend.partials.checkout.form')

        </div>

    </div>

@endsection



@push('scripts')

    <script src="{{ asset('frontend/assets/vendor/sticky/sticky.js') }}"></script>

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            var toggle = document.querySelector('[data-checkout-coupon-toggle]');

            var panel = document.querySelector('[data-checkout-coupon-panel]');

            if (toggle && panel) {

                toggle.addEventListener('click', function () {

                    var open = panel.hasAttribute('hidden');

                    panel.toggleAttribute('hidden', !open);

                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

                    toggle.classList.toggle('is-open', open);

                });

            }



            document.querySelectorAll('.store-checkout-payment input[type="radio"]').forEach(function (radio) {

                radio.addEventListener('change', function () {

                    document.querySelectorAll('.store-checkout-payment').forEach(function (el) {

                        el.classList.remove('is-selected');

                    });

                    if (radio.checked) {

                        radio.closest('.store-checkout-payment').classList.add('is-selected');

                    }

                });

            });

            var checkoutForm = document.querySelector('.store-checkout-form');
            var submitBtn = document.querySelector('[data-checkout-submit]');
            if (checkoutForm && submitBtn) {
                checkoutForm.addEventListener('submit', function () {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('is-loading');
                    submitBtn.querySelector('span').textContent = 'جاري تأكيد الطلب...';
                });
            }
        });

    </script>

@endpush

