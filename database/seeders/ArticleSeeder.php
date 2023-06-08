<?php

namespace Database\Seeders;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Article::factory(1000)->create();

            $responses = Http::pool(fn (Pool $pool) => [
                $pool->get('https://newsapi.org/v2/everything', ['apiKey' => config('app.newsapi_api_key'), 'q' => 'software', 'language' => 'en']),
                $pool->get('https://newsapi.org/v2/everything', ['apiKey' => config('app.newsapi_api_key'), 'q' => 'tech', 'language' => 'en']),
                $pool->get('https://newsapi.org/v2/everything', ['apiKey' => config('app.newsapi_api_key'), 'q' => 'science', 'language' => 'en']),
            ]);

            foreach ([
                'software' => $responses[0]->json()['articles'],
                'tech' => $responses[1]->json()['articles'],
                'science' => $responses[2]->json()['articles'],
            ] as $category => $articleDataList) {
                foreach ($articleDataList as $articleData) {
                    $article = new Article();
                    $article->title = $articleData['title'];
                    $article->description = $articleData['content'];
                    $article->author = $articleData['author'] ?? $articleData['source']['name'];
                    $article->category = $category;
                    $article->source = $articleData['url'];
                    $article->created_at = Carbon::parse($articleData['publishedAt'])->toDateTimeString();
                    $article->save();
                }
            }
        });
    }
}
