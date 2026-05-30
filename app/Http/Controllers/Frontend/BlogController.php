<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\BlogComment;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['author', 'category', 'tags'])->published();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        $posts = $query->latest('published_at')->paginate(8)->withQueryString();
        $categories = BlogCategory::withCount('posts')->where('is_active', true)->orderBy('name')->get();
        $tags = BlogTag::popular()->limit(20)->get();
        $recentPosts = BlogPost::published()->latest('published_at')->limit(5)->get();

        $activeCategory = $request->filled('category')
            ? $categories->firstWhere('slug', $request->category)
            : null;
        $activeTag = $request->filled('tag')
            ? BlogTag::where('slug', $request->tag)->first()
            : null;

        $pageTitle = 'المدونة';
        $breadcrumbItems = [['label' => 'المدونة']];

        if ($activeCategory) {
            $pageTitle = $activeCategory->name;
            $breadcrumbItems = [
                ['label' => 'المدونة', 'url' => route('frontend.blog.index')],
                ['label' => $activeCategory->name],
            ];
        } elseif ($activeTag) {
            $pageTitle = $activeTag->name;
            $breadcrumbItems = [
                ['label' => 'المدونة', 'url' => route('frontend.blog.index')],
                ['label' => $activeTag->name],
            ];
        } elseif ($request->filled('search')) {
            $pageTitle = 'نتائج البحث';
            $breadcrumbItems = [
                ['label' => 'المدونة', 'url' => route('frontend.blog.index')],
                ['label' => 'نتائج البحث'],
            ];
        }

        $totalPosts = BlogPost::published()->count();

        return view('frontend.pages.blog.index', compact(
            'posts',
            'categories',
            'tags',
            'recentPosts',
            'activeCategory',
            'activeTag',
            'pageTitle',
            'breadcrumbItems',
            'totalPosts'
        ));
    }

    public function show($slug)
    {
        $post = BlogPost::with(['author', 'category', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $post->incrementViews();

        $comments = BlogComment::where('blog_post_id', $post->id)
            ->where('is_approved', true)
            ->latest()
            ->get();

        $categories = BlogCategory::withCount('posts')->where('is_active', true)->get();
        $tags = BlogTag::popular()->limit(20)->get();
        $recentPosts = BlogPost::published()->where('id', '!=', $post->id)->latest('published_at')->limit(5)->get();
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('blog_category_id', $post->blog_category_id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('frontend.pages.blog.show', compact('post', 'comments', 'categories', 'tags', 'recentPosts', 'relatedPosts'));
    }

    public function storeComment(Request $request, $slug)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $post = BlogPost::where('slug', $slug)->published()->firstOrFail();

        BlogComment::create([
            'blog_post_id' => $post->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'is_approved' => false,
        ]);

        return back()->with('success', 'تم إرسال تعليقك وسيتم مراجعته قبل النشر.');
    }

    public function category(string $slug)
    {
        $request = request()->duplicate(
            array_merge(request()->query(), ['category' => $slug])
        );

        return $this->index($request);
    }

    public function tag(string $slug)
    {
        $request = request()->duplicate(
            array_merge(request()->query(), ['tag' => $slug])
        );

        return $this->index($request);
    }
}
