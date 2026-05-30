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

        });

    </script>

@endpush

