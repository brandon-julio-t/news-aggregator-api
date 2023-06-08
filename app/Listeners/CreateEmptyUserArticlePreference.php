<?php

namespace App\Listeners;

use App\Models\UserArticlePreference;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateEmptyUserArticlePreference
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        UserArticlePreference::create([
            'user_id' => $event->user->id,
            'liked_categories' => collect(),
            'liked_authors' => collect(),
            'liked_sources' => collect(),
        ]);
    }
}
