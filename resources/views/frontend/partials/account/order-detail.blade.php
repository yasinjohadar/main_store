<div class="tab-pane active in order" id="account-order-view">
    <div class="account-panel">
        @include('frontend.partials.account.section-header', [
            'icon' => 'w-icon-orders',
            'title' => 'طلب #' . $order->order_number,
            'subtitle' => 'تم التقديم في ' . $order->created_at->locale('ar')->translatedFormat('j F Y') . ' — الحالة: ' . ($order->status->name ?? 'قيد الانتظار'),
            'theme' => 'theme-orders',
        ])

        <div class="account-panel__body">
            <div class="account-table-wrap mb-5">
                <table class="account-table account-order-detail-table">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th class="text-left">السعر</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td data-label="المنتج">
                                    @if($item->product)
                                        <a href="{{ route('frontend.product.show', $item->product->slug) }}" class="account-product-link">
                                            {{ $item->product_name }}
                                        </a>
                                    @else
                                        {{ $item->product_name }}
                                    @endif
                                    <span class="account-order-meta d-block">× {{ $item->quantity }}</span>
                                    @if($item->sku)
                                        <span class="account-order-meta d-block">SKU: {{ $item->sku }}</span>
                                    @endif
                                </td>
                                <td data-label="السعر" class="account-order-total">{{ number_format($item->total, 2) }} ر.س</td>
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
                                <td class="text-success">-{{ number_format($order->discount_amount, 2) }} ر.س</td>
                            </tr>
                        @endif
                        @if($order->tax_amount > 0)
                            <tr>
                                <th>الضريبة</th>
                                <td>{{ number_format($order->tax_amount, 2) }} ر.س</td>
                            </tr>
                        @endif
                        <tr>
                            <th>طريقة الدفع</th>
                            <td>{{ $order->payments->first()->method ?? '—' }}</td>
                        </tr>
                        <tr class="account-table-total">
                            <th>الإجمالي</th>
                            <td>{{ number_format($order->total, 2) }} ر.س</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($order->shipping_address || $order->billing_address)
                <div class="row gutter-sm mb-4">
                    @if($order->billing_address)
                        <div class="col-md-6 mb-4">
                            <div class="account-info-card theme-addresses">
                                <div class="account-info-card__head">
                                    <span class="account-info-card__icon"><i class="w-icon-map-marker"></i></span>
                                    <h5 class="account-info-card__title">عنوان الفوترة</h5>
                                </div>
                                <p class="mb-0">{{ $order->billing_address->full_name }}</p>
                                <p class="mb-0 text-muted">{{ $order->billing_address->full_address }}</p>
                            </div>
                        </div>
                    @endif
                    @if($order->shipping_address)
                        <div class="col-md-6 mb-4">
                            <div class="account-info-card theme-account">
                                <div class="account-info-card__head">
                                    <span class="account-info-card__icon"><i class="w-icon-truck"></i></span>
                                    <h5 class="account-info-card__title">عنوان الشحن</h5>
                                </div>
                                <p class="mb-0">{{ $order->shipping_address->full_name }}</p>
                                <p class="mb-0 text-muted">{{ $order->shipping_address->full_address }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('frontend.checkout.invoice', $order) }}" class="account-btn account-btn--primary" target="_blank" rel="noopener noreferrer">
                    عرض الفاتورة / الوصل<i class="w-icon-download"></i>
                </a>
                <a href="{{ route('frontend.account.orders') }}" class="account-btn account-btn--outline">
                    العودة للطلبات<i class="w-icon-long-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>
</div>
