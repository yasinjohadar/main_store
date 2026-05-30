@foreach($products as $product)
    <x-frontend.shop-product-card :product="$product" />
@endforeach
