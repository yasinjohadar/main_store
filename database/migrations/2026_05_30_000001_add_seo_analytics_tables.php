<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $seoColumns = function (Blueprint $table) {
            $table->string('canonical_url')->nullable()->after('meta_keywords');
            $table->string('focus_keyword')->nullable();
            $table->string('focus_keyword_synonyms')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('robots_meta')->default('index,follow');
            $table->string('schema_type')->nullable();
        };

        Schema::table('products', $seoColumns);
        Schema::table('categories', $seoColumns);
        Schema::table('brands', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('slug');
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('focus_keyword')->nullable();
            $table->string('focus_keyword_synonyms')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('robots_meta')->default('index,follow');
            $table->string('schema_type')->nullable();
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->text('tldr')->nullable()->after('excerpt');
            $table->json('key_takeaways')->nullable()->after('tldr');
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('route_name')->nullable()->unique();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('focus_keyword')->nullable();
            $table->string('focus_keyword_synonyms')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('robots_meta')->default('index,follow');
            $table->string('schema_type')->default('WebPage');
            $table->timestamps();
        });

        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('viewable');
            $table->string('url', 500);
            $table->string('referrer', 500)->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('session_id', 64)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['viewable_type', 'viewable_id', 'created_at']);
            $table->index('created_at');
        });

        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name', 64);
            $table->json('payload')->nullable();
            $table->string('url', 500)->nullable();
            $table->string('session_id', 64)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['event_name', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('pages');

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['tldr', 'key_takeaways']);
        });

        foreach (['products', 'categories'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn([
                    'canonical_url', 'focus_keyword', 'focus_keyword_synonyms',
                    'og_title', 'og_description', 'og_image', 'robots_meta', 'schema_type',
                ]);
            });
        }

        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
                'focus_keyword', 'focus_keyword_synonyms', 'og_title', 'og_description',
                'og_image', 'robots_meta', 'schema_type',
            ]);
        });
    }
};
