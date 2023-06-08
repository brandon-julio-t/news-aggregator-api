<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\UserArticlePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Article::query();

        if ($q = $request->q) {
            $query->where(
                fn (Builder $qb) => $qb->where('title', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%')
            );
        }

        if ($category = $request->category) {
            $query->where('category', $category);
        }

        $from = $request->from;
        $to = $request->to;
        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        } else if ($from && !$to) {
            $query->where('created_at', '>=', $from);
        } else if (!$from && $to) {
            $query->where('created_at', '<=', $to);
        }

        return $query->paginate();
    }

    public function categories()
    {
        return Article::query()
            ->select('category')
            ->get()
            ->map->category
            ->unique()
            ->sort()
            ->values();
    }

    public function forYou()
    {
        $preferences = UserArticlePreference::query()
            ->whereBelongsTo(auth()->user())
            ->firstOrFail();


        /** @var array<string, string[]> */
        $columnToDataMappings = [
            'author' => $preferences->liked_authors,
            'category' => $preferences->liked_categories,
            'source' => $preferences->liked_sources,
        ];

        $query = Article::query();

        foreach ($columnToDataMappings as $column => $likedData) {
            if ($column === 'source') {
                foreach ($likedData as $data) {
                    $query->orWhere($column, 'like', '%' . $data . '%');
                }
            } else {
                $query->orWhereIn($column, $likedData);
            }
        }

        return $query->paginate();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return $article;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        //
    }
}
