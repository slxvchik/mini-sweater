<?php

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
        Schema::create('comments', function (Blueprint $table) {
            
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->foreignId('tweet_id')->constrained('tweets')->onDelete('cascade');
            
            // Ответ на твит
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');

            $table->text('text');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
