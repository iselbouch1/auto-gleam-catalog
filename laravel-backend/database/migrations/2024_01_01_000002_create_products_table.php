<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('specs')->nullable();
            $table->timestamps();

            $table->index(['is_visible', 'is_featured']);
            $table->index(['sort_order']);
            $table->fullText(['name', 'short_description', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};