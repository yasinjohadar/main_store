<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Seo\AiSeoService;
use App\Services\Seo\SeoScoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeoScoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function score(Request $request, SeoScoreService $seoScore): JsonResponse
    {
        $result = $seoScore->score($request->only([
            'meta_title',
            'title',
            'meta_description',
            'description',
            'focus_keyword',
            'og_image',
            'canonical_url',
        ]));

        return response()->json($result);
    }

    public function suggest(Request $request, AiSeoService $aiSeo): JsonResponse
    {
        $validated = $request->validate([
            'entity_type' => 'required|string|in:product,category,brand,page,blog',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
        ]);

        return response()->json(
            $aiSeo->suggestForEntity(
                $validated['entity_type'],
                $validated['title'],
                $validated['description'] ?? null,
                $validated['content'] ?? null,
            )
        );
    }
}
