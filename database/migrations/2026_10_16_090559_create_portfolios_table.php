<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('full_name', 150);
            $table->string('email');
            $table->string('contact_number', 30)->nullable();
            $table->text('address')->nullable();
            $table->text('about_me')->nullable();

            $table->json('education')->nullable();
            $table->json('skills')->nullable();
            $table->json('projects')->nullable();
            $table->json('experience')->nullable();
            $table->json('social_links')->nullable();

            $table->string('profile_picture')->nullable();
            $table->string('slug')->unique();
            $table->string('template')->default('simple');
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};