@if(! empty($variantOptions))
    <div class="store-product-variations">
        @foreach($variantOptions as $option)
            @php
                $attribute = $option['attribute'];
                $values = $option['values'];
                $swatchClass = match ($attribute->type) {
                    'color' => 'color',
                    'image' => 'image',
                    default => 'size',
                };
            @endphp
            <div class="store-product-variation product-variation-form product-{{ $swatchClass }}-swatch mb-3"
                data-attribute-id="{{ $attribute->id }}"
                data-attribute-name="{{ $attribute->name }}">
                <label class="store-product-variation__label mb-1">{{ $attribute->name }}:</label>
                <div class="d-flex flex-wrap align-items-center product-variations store-product-variation__options">
                    @foreach($values as $value)
                        @if($attribute->type === 'color' && $value->color_hex)
                            <a href="#"
                                class="color store-variant-option"
                                data-attribute-id="{{ $attribute->id }}"
                                data-value-id="{{ $value->id }}"
                                style="background-color: {{ $value->color_hex }};"
                                title="{{ $value->value }}"
                                aria-label="{{ $value->value }}"></a>
                        @elseif($attribute->type === 'image' && $value->image_url)
                            <a href="#"
                                class="image store-variant-option"
                                data-attribute-id="{{ $attribute->id }}"
                                data-value-id="{{ $value->id }}"
                                title="{{ $value->value }}"
                                aria-label="{{ $value->value }}">
                                <img src="{{ $value->image_url }}" alt="{{ $value->value }}" width="40" height="40" loading="lazy">
                            </a>
                        @else
                            <a href="#"
                                class="size store-variant-option"
                                data-attribute-id="{{ $attribute->id }}"
                                data-value-id="{{ $value->id }}">{{ $value->value }}</a>
                        @endif
                    @endforeach
                </div>
                <p class="store-product-variation__hint d-none" aria-live="polite"></p>
            </div>
        @endforeach
    </div>
@endif
