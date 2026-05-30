@props(['post' => null, 'title' => null])

@include('frontend.partials.page.breadcrumb', [
    'items' => [
        ['label' => 'المدونة', 'url' => route('frontend.blog.index')],
        ['label' => $post?->title ?? $title ?? ''],
    ],
])
