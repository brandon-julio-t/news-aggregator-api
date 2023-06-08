<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_article_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->json('liked_categories');
            $table->json('liked_authors');
            $table->json('liked_sources');
            $table->timestamps();

            $table->unique(['id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_article_preferences');
    }
};
