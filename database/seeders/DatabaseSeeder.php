<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    public function run(): void
    {
        // Tạo 5 category trước
        $categories = Category::factory(20)->create();

        // Mỗi category có 10 post
        $categories->each(function ($category) {
            $posts = Post::factory(10)->create([
                'category_id' => $category->id
            ]);

            // Mỗi post có 3-5 comment
            $posts->each(function ($post) {
                Comment::factory(rand(3, 5))->create([
                    'post_id' => $post->id
                ]);
            });
        });
    }
}
