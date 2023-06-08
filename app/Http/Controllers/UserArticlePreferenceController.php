<?php

namespace App\Http\Controllers;

use App\Models\UserArticlePreference;
use App\Http\Requests\StoreUserArticlePreferenceRequest;
use App\Http\Requests\UpdateUserArticlePreferenceRequest;

class UserArticlePreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserArticlePreference::query()
            ->whereBelongsTo(auth()->user())
            ->firstOrFail();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserArticlePreferenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserArticlePreference $userArticlePreference)
    {
        return $userArticlePreference;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserArticlePreferenceRequest $request, string $_userId, UserArticlePreference $userArticlePreference)
    {
        $data = $request->validated();
        $action = $data['action'];
        $attribute = $data['attribute'];
        $value = $data['value'];

        $preferences = collect($userArticlePreference[$attribute]);

        if ($action === 'like') {
            $preferences = $preferences->push($value);
        } else if ($action === 'dislike') {
            $preferences = $preferences->filter(fn (string $v) => $v !== $value);
        }

        $userArticlePreference[$attribute] = $preferences->sort()->values();
        $userArticlePreference->save();
        return $userArticlePreference;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserArticlePreference $userArticlePreference)
    {
        //
    }
}
