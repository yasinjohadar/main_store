<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

class ShopCatalogService
{
    public const LAYOUT_KEYS = ['view', 'cols', 'sidebar', 'filters', 'banner', 'load'];

    public const DEFAULTS = [
        'view' => 'grid',
        'cols' => 4,
        'sidebar' => 'left',
        'filters' => 'vertical',
        'banner' => 'sidebar',
        'load' => 'pagination',
    ];

    public function resolveLayout(Request $request): array
    {
        $session = session('shop_layout', []);
        $layout = self::DEFAULTS;

        foreach (self::LAYOUT_KEYS as $key) {
            if ($request->has($key)) {
                $value = $request->input($key);
                if ($this->isValidLayoutValue($key, $value)) {
                    $layout[$key] = $key === 'cols' ? (int) $value : $value;
                }
            } elseif (isset($session[$key])) {
                $layout[$key] = $session[$key];
            }
        }

        session(['shop_layout' => $layout]);

        return $layout;
    }

    public function buildQuery(Request $request, ?Category $category = null): Builder|Relation
    {
        if ($category) {
            $query = $category->products()
                ->with(['category', 'brand', 'images', 'reviews'])
                ->withAvg('reviews', 'rating')
                ->where('status', 'active')
                ->where('is_visible', true);
        } else {
            $query = Product::query()
                ->with(['category', 'brand', 'images', 'reviews'])
                ->withAvg('reviews', 'rating')
                ->where('status', 'active')
                ->where('is_visible', true);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category') && ! $category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        return $this->applySort($query, $request);
    }

    public function paginate(Request $request, ?Category $category = null): LengthAwarePaginator
    {
        $perPage = (int) ($request->per_page ?? 12);
        $perPage = in_array($perPage, [9, 12, 24, 36], true) ? $perPage : 12;

        return $this->buildQuery($request, $category)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function gridClasses(array $layout): string
    {
        if (($layout['view'] ?? 'grid') === 'list') {
            return 'cols-xl-2 cols-md-1 cols-xs-2 cols-1';
        }

        $colsMap = [
            3 => 'cols-md-3 cols-sm-2 cols-2',
            4 => 'cols-lg-4 cols-md-3 cols-sm-2 cols-2',
            5 => 'cols-lg-5 cols-md-4 cols-sm-3 cols-2',
            6 => 'cols-xl-6 cols-lg-5 cols-md-4 cols-sm-3 cols-2',
        ];

        $cols = (int) ($layout['cols'] ?? 4);

        return $colsMap[$cols] ?? $colsMap[4];
    }

    public function layoutPartial(array $layout): string
    {
        $sidebar = $layout['sidebar'] ?? 'left';

        return match ($sidebar) {
            'right' => 'frontend.partials.shop.layouts.sidebar-right',
            'offcanvas' => 'frontend.partials.shop.layouts.offcanvas',
            'none' => 'frontend.partials.shop.layouts.full-width',
            default => 'frontend.partials.shop.layouts.sidebar-left',
        };
    }

    public function catalogClasses(array $layout): string
    {
        $classes = ['shop-content'];

        if (($layout['filters'] ?? 'vertical') === 'horizontal') {
            $classes[] = 'toolbox-horizontal';
        }

        if (in_array($layout['sidebar'] ?? 'left', ['left', 'right'], true)) {
            $classes[] = 'row gutter-lg mb-10';
        } else {
            $classes[] = 'mb-10';
        }

        if (($layout['sidebar'] ?? 'left') === 'right') {
            $classes[] = 'store-shop-layout--right-sidebar';
        }

        if (($layout['sidebar'] ?? 'left') === 'offcanvas') {
            $classes[] = 'store-shop-layout--offcanvas';
        }

        return implode(' ', $classes);
    }

    public function getCategories()
    {
        return Category::active()->ordered()->withCount('products')->get();
    }

    public function getBrands()
    {
        return Brand::all();
    }

    public function renderProductItems($products, array $layout): string
    {
        $view = ($layout['view'] ?? 'grid') === 'list'
            ? 'frontend.partials.shop.products-items-list'
            : 'frontend.partials.shop.products-items-grid';

        return view($view, compact('products', 'layout'))->render();
    }

    public function infiniteScrollUrl(Request $request, ?Category $category = null): string
    {
        $params = array_merge(
            session('shop_layout', self::DEFAULTS),
            $request->query()
        );
        unset($params['page']);

        if ($category) {
            $params['category_slug'] = $category->slug;
        }

        return route('frontend.shop.products', $params);
    }

    protected function applySort(Builder|Relation $query, Request $request): Builder|Relation
    {
        switch ($request->sort) {
            case 'popularity':
                $query->orderByDesc('order');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
                break;
            case 'date':
                $query->orderByDesc('created_at');
                break;
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        return $query;
    }

    protected function isValidLayoutValue(string $key, mixed $value): bool
    {
        return match ($key) {
            'view' => in_array($value, ['grid', 'list'], true),
            'cols' => in_array((int) $value, [3, 4, 5, 6], true),
            'sidebar' => in_array($value, ['left', 'right', 'offcanvas', 'none'], true),
            'filters' => in_array($value, ['vertical', 'horizontal'], true),
            'banner' => in_array($value, ['sidebar', 'boxed', 'fullwidth'], true),
            'load' => in_array($value, ['pagination', 'infinite'], true),
            default => false,
        };
    }
}
