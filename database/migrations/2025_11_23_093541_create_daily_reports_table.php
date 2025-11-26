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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();

            $table->timestamp('report_time')->useCurrent()->comment('زمان دقیق اجرای گزارش');
            
            // معیارهای آماری
            $table->unsignedSmallInteger('total_new_items')->default(0);
            $table->unsignedSmallInteger('news_count')->default(0);
            $table->unsignedSmallInteger('daily_media_count')->default(0);
            $table->unsignedSmallInteger('sliders_count')->default(0);
            $table->unsignedSmallInteger('messages_count')->default(0);
            $table->unsignedSmallInteger('messages_answered_count')->default(0);
            
            // ذخیره جزئیات خبرها به صورت JSON
            $table->json('new_news_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
