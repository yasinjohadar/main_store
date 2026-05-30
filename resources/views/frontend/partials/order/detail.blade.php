<div class="store-order-detail">
    <div class="store-order-detail__head">
        <h3 class="store-order-detail__title">تفاصيل الطلب</h3>
        <span class="store-order-detail__count">{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'منتج' : 'منتجات' }}</span>
    </div>

    <div class="store-order-detail__table-wrap">
        <table class="store-order-detail__table">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>السعر</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td data-label="المنتج">
                            @if($item->product)
                                <a href="{{ route('frontend.product.show', $item->product->slug) }}" class="store-order-detail__product-link">
                                    {{ $item->product_name }}
                                </a>
                            @else
                                {{ $item->product_name }}
                            @endif
                            <small class="store-order-detail__meta">× {{ $item->quantity }}</small>
                        </td>
                        <td data-label="السعر">{{ number_format($item->total, 2) }} ر.س</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>المجموع الفرعي</th>
                    <td>{{ number_format($order->subtotal, 2) }} ر.س</td>
                </tr>
                <tr>
                    <th>الشحن</th>
                    <td>{{ number_format($order->shipping_amount, 2) }} ر.س</td>
                </tr>
                @if($order->discount_amount > 0)
                    <tr>
                        <th>الخصم</th>
                        <td class="store-order-detail__discount">-{{ number_format($order->discount_amount, 2) }} ر.س</td>
                    </tr>
                @endif
                @if($order->tax_amount > 0)
                    <tr>
                        <th>الضريبة</th>
                        <td>{{ number_format($order->tax_amount, 2) }} ر.س</td>
                    </tr>
                @endif
                <tr class="store-order-detail__total-row">
                    <th>الإجمالي</th>
                    <td>{{ number_format($order->total, 2) }} ر.س</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($order->shipping_address || $order->billing_address)
        <div class="row gutter-sm store-order-detail__addresses">
            @if($order->shipping_address)
                <div class="col-md-6 mb-4">
                    <div class="store-order-detail__address">
                        <span class="store-order-detail__address-icon"><i class="w-icon-truck"></i></span>
                        <div>
                            <h4 class="store-order-detail__address-title">عنوان الشحن</h4>
                            <p class="mb-0">{{ $order->shipping_address->full_name }}</p>
                            <p class="mb-0 store-order-detail__address-text">{{ $order->shipping_address->full_address }}</p>
                            @if($order->shipping_address->phone)
                                <p class="mb-0 store-order-detail__address-text">{{ $order->shipping_address->phone }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if($order->billing_address)
                <div class="col-md-6 mb-4">
                    <div class="store-order-detail__address">
                        <span class="store-order-detail__address-icon"><i class="w-icon-map-marker"></i></span>
                        <div>
                            <h4 class="store-order-detail__address-title">عنوان الفوترة</h4>
                            <p class="mb-0">{{ $order->billing_address->full_name }}</p>
                            <p class="mb-0 store-order-detail__address-text">{{ $order->billing_address->full_address }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if(! empty($backUrl))
        <a href="{{ $backUrl }}" class="store-order-detail__back">
            <i class="w-icon-long-arrow-left"></i>
            <span>{{ $backLabel ?? 'رجوع' }}</span>
        </a>
    @endif
</div>
