
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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('on_titr')->nullable();
            $table->string('title')->unique();
            // $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->string('content_type')->nullable();
            $table->string('media_path')->nullable();
            $table->enum('media_type', ['image', 'video'])->nullable();
            $table->string('thumbnailVideo', 500)->nullable()->comment('URL تصویر شاخص ویدیو');
            // $table->string('image')->nullable();
            $table->string('short_link')->unique()->nullable();
            $table->unsignedBigInteger('news_code')->unique()->nullable();
            $table->text('body')->nullable();
            
            $table->string('meta_description')->nullable();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->timestamp('published_at')->nullable();
            // $table->json('tags')->nullable(); // ✅ فیلد تگ‌ها به صورت JSON
            $table->enum('position', ['slider', 'slider_side', 'slider_bottom'])->default('slider_bottom');
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('views')->default(0);
            
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
