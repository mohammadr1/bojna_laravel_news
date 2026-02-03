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

            // polymorphic
            $table->morphs('commentable'); 
            // commentable_id
            // commentable_type

            $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();

            $table->string('author');
            $table->string('email')->nullable();

            $table->text('content');

            $table->boolean('approved')->default(false);

            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('dislikes')->default(0);

            $table->boolean('is_admin')->default(false);

            $table->text('admin_content')->nullable();


            $table->softDeletes();

            $table->timestamps();
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
