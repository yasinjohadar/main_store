<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('eyebrow')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('home_features', function (Blueprint $table) {
            $table->id();
            $table->string('icon', 100);
            $table->string('tone', 20)->default('blue');
            $table->string('title');
            $table->string('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('home_promo_banners', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('background_color', 20)->nullable();
            $table->string('tone', 20)->default('light');
            $table->string('badge')->nullable();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('price_label')->nullable();
            $table->string('price_text')->nullable();
            $table->string('button_url', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('home_product_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('block_key')->unique();
            $table->string('section_title')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_bg_color', 20)->nullable();
            $table->string('banner_subtitle')->nullable();
            $table->text('banner_title')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('button_url', 500)->nullable();
            $table->string('wrapper_class')->nullable();
            $table->json('settings')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('home_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('url', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_brands');
        Schema::dropIfExists('home_product_blocks');
        Schema::dropIfExists('home_promo_banners');
        Schema::dropIfExists('home_features');
        Schema::dropIfExists('home_sections');
    }
};
