@foreach($products as $product)
    <x-frontend.shop-product-list-item :product="$product" />
@endforeach
