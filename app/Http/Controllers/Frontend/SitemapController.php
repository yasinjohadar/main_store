<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        $urls[] = $this->entry(route('frontend.home'), now(), 'daily', '1.0');
        $urls[] = $this->entry(route('frontend.shop.index'), now(), 'daily', '0.9');
        $urls[] = $this->entry(route('frontend.blog.index'), now(), 'daily', '0.8');

        foreach (['about', 'contact', 'faq', 'privacy', 'terms'] as $route) {
            if (\Route::has('frontend.' . $route)) {
                $urls[] = $this->entry(route('frontend.' . $route), now(), 'monthly', '0.6');
            }
        }

        Product::query()
            ->where('status', 'active')
            ->where('is_visible', true)
            ->orderBy('updated_at', 'desc')
            ->chunk(100, function ($products) use (&$urls) {
                foreach ($products as $product) {
                    $urls[] = $this->entry(
                        route('frontend.product.show', $product->slug),
                        $product->updated_at,
                        'weekly',
                        '0.8'
                    );
                }
            });

        Category::active()->orderBy('updated_at', 'desc')->each(function ($category) use (&$urls) {
            $urls[] = $this->entry(
                route('frontend.category.show', $category->slug),
                $category->updated_at,
                'weekly',
                '0.7'
            );
        });

        BlogPost::published()->orderBy('updated_at', 'desc')->each(function ($post) use (&$urls) {
            $urls[] = $this->entry(
                route('frontend.blog.show', $post->slug),
                $post->updated_at,
                'weekly',
                '0.7'
            );
        });

        Page::active()->each(function ($page) use (&$urls) {
            $loc = $page->route_name ? route($page->route_name) : url('/' . $page->slug);
            $urls[] = $this->entry($loc, $page->updated_at, 'monthly', '0.6');
        });

        $xml = view('frontend.sitemap', compact('urls'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    protected function entry(string $loc, $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod?->toAtomString() ?? now()->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
