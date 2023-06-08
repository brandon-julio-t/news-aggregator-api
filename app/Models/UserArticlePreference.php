<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserArticlePreference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'liked_categories',
        'liked_authors',
        'liked_sources',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'liked_categories' => 'array',
        'liked_authors' => 'array',
        'liked_sources' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
