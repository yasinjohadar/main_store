@props(['items' => []])

<nav class="breadcrumb-nav">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="{{ route('frontend.home') }}">الرئيسية</a></li>
            @foreach($items as $item)
                @if(! empty($item['url']))
                    <li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @else
                    <li>{{ $item['label'] }}</li>
                @endif
            @endforeach
        </ul>
    </div>
</nav>
