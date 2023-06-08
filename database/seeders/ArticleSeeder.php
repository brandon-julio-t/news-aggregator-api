<?php

namespace Database\Seeders;

use App\Events\AggregateArticlesFromApisSuccess;
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
                $pool->get(
                    'https://newsapi.org/v2/everything',
                    ['apiKey' => config('app.newsapi_api_key'), 'language' => 'en', 'domains' => 'techcrunch.com']
                ),

                $pool->get(
                    'https://content.guardianapis.com/search',
                    ['api-key' => config('app.the_guardian_api_key'), 'page-size' => 100]
                ),

                $pool->get(
                    'https://api.nytimes.com/svc/search/v2/articlesearch.json',
                    ['api-key' => config('app.new_york_times_api_key')]
                ),
            ]);

            $articles = collect();

            logger(self::class . '@newsapi', [$responses[0]]);
            logger(self::class . '@guardiansapi', [$responses[1]]);
            logger(self::class . '@nytimes', [$responses[2]]);

            foreach ($responses[0]->json()['articles'] as $articleData) {
                $article = new Article();
                $article->title = $articleData['title'];
                $article->description = '';
                $article->author = $articleData['author'] ?? $articleData['source']['name'];
                $article->category = 'Technology';
                $article->source = $articleData['url'];
                $article->created_at = Carbon::parse($articleData['publishedAt'])->toDateTimeString();
                $articles->push($article);
            }

            foreach ($responses[1]->json()['response']['results'] as $articleData) {
                $article = new Article();
                $article->title = $articleData['webTitle'];
                $article->description = '';
                $article->author = 'The Guardian';
                $article->category = $articleData['sectionName'];
                $article->source = $articleData['webUrl'];
                $article->created_at = Carbon::parse($articleData['webPublicationDate'])->toDateTimeString();
                $articles->push($article);
            }


            foreach ($responses[2]->json()['response']['docs'] as $articleData) {
                $article = new Article();
                $article->title = collect([
                    $articleData['headline']['main'],
                    $articleData['abstract'],
                    $articleData['snippet'],
                ])->filter()->first();
                $article->description = '';
                $article->author = collect([
                    str($articleData['byline']['original'])->replace('By ', '')->value,
                    $articleData['source'],
                ])->filter()->first();
                $article->category = isset($articleData['subsection_name']) ? $articleData['subsection_name'] : $articleData['section_name'];
                $article->source = $articleData['web_url'];
                $article->created_at = Carbon::parse($articleData['pub_date'])->toDateTimeString();
                $articles->push($article);
            }

            $articles->each->save();
        });
    }
}
