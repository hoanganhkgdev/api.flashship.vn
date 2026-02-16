<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Get list of active and published news.
     */
    public function index(Request $request)
    {
        $query = News::active()->published()->orderBy('published_at', 'desc');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('featured')) {
            $query->featured();
        }

        $limit = $request->get('limit', 10);
        $news = $query->limit($limit)->get();

        return NewsResource::collection($news);
    }

    /**
     * Get a single news article.
     */
    public function show($id)
    {
        $news = News::active()->published()->findOrFail($id);
        return new NewsResource($news);
    }
}
