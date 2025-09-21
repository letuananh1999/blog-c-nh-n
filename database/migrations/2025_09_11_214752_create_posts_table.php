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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // Quan hệ: một post thuộc về một category, một user (tác giả)
            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // Thông tin bài viết
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt')->nullable();              // mô tả ngắn
            $table->longText('content');                       // nội dung chính
            $table->string('thumbnail')->nullable();           // ảnh đại diện

            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            // Trạng thái và thống kê
            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('draft');
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
