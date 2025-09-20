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
            $table->foreignId('post_id')->constrained()->cascadeOnDelete(); // comment thuộc post nào
            //$table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete(); // nếu có hệ thống user
            $table->string('author_name');
            $table->string('author_email');
            $table->text('content'); // nội dung comment
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete(); // comment cha (nếu có)
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
