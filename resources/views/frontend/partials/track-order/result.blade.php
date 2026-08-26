@php
    $currentStatusOrder = $order->status?->order ?? 1;
    $isCancelled = in_array($order->status?->slug, ['cancelled', 'refunded'], true);
@endphp

<div class="store-track-order-result">
    <div class="store-track-order-summary">
        <div class="store-track-order-summary__item">
            <span class="store-track-order-summary__label">رقم الطلب</span>
            <strong class="store-track-order-summary__value">#{{ $order->order_number }}</strong>
        </div>
        <div class="store-track-order-summary__item">
            <span class="store-track-order-summary__label">الحالة</span>
            <strong class="store-track-order-summary__value store-track-order-summary__value--status"
                style="--status-color: {{ $order->status?->color ?? '#336699' }}">
                {{ $order->status?->name ?? 'قيد الانتظار' }}
            </strong>
        </div>
        <div class="store-track-order-summary__item">
            <span class="store-track-order-summary__label">تاريخ الطلب</span>
            <strong class="store-track-order-summary__value">
                {{ $order->created_at->locale('ar')->translatedFormat('j F Y') }}
            </strong>
        </div>
        <div class="store-track-order-summary__item">
            <span class="store-track-order-summary__label">الإجمالي</span>
            <strong class="store-track-order-summary__value store-track-order-summary__value--total">
                {{ number_format($order->total, 2) }} ر.س
            </strong>
        </div>
        @if($paymentMethodLabel)
            <div class="store-track-order-summary__item">
                <span class="store-track-order-summary__label">طريقة الدفع</span>
                <strong class="store-track-order-summary__value">{{ $paymentMethodLabel }}</strong>
            </div>
        @endif
        @if($order->tracking_number)
            <div class="store-track-order-summary__item">
                <span class="store-track-order-summary__label">رقم التتبع</span>
                <strong class="store-track-order-summary__value">
                    {{ $order->tracking_number }}
                    @if($order->tracking_carrier)
                        <span class="text-muted">({{ $order->tracking_carrier }})</span>
                    @endif
                </strong>
            </div>
        @endif
    </div>

    @if(! $isCancelled && $statusTimeline->isNotEmpty())
        <div class="store-track-order-timeline">
            <h3 class="store-track-order-timeline__title">مراحل الطلب</h3>
            <ol class="store-track-order-timeline__steps">
                @foreach($statusTimeline->whereNotIn('slug', ['cancelled', 'refunded']) as $step)
                    @php
                        $isDone = $currentStatusOrder >= $step->order;
                        $isCurrent = ($order->status?->id ?? null) === $step->id;
                    @endphp
                    <li class="store-track-order-timeline__step{{ $isDone ? ' is-done' : '' }}{{ $isCurrent ? ' is-current' : '' }}">
                        <span class="store-track-order-timeline__dot"></span>
                        <span class="store-track-order-timeline__label">{{ $step->name }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    @elseif($isCancelled)
        <div class="store-track-order-notice store-track-order-notice--muted">
            <i class="w-icon-exclamation-triangle"></i>
            <span>هذا الطلب {{ $order->status?->name }} ولا يمكن متابعة الشحن.</span>
        </div>
    @endif

    <div class="store-track-order-detail">
        @include('frontend.partials.order.detail', [
            'backUrl' => route('frontend.track-order'),
            'backLabel' => 'بحث عن طلب آخر',
        ])
    </div>
</div>
