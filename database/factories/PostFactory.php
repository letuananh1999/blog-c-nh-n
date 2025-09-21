<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'category_id' => Category::factory(),     // tự tạo category nếu chưa có
            'user_id'     => User::factory(),         // tự tạo user nếu chưa có
            'title'       => $title,
            'slug'        => Str::slug($title) . '-' . Str::random(5), // tránh trùng slug
            'excerpt'     => $this->faker->text(150),
            'content'     => $this->faker->paragraphs(5, true),
            'thumbnail'   => $this->faker->imageUrl(640, 480, 'blog', true),

            // SEO
            'meta_title'       => $this->faker->sentence(),
            'meta_description' => $this->faker->text(160),

            // Trạng thái (enum)
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),

            // Thống kê
            'views_count' => $this->faker->numberBetween(0, 5000),
            'likes_count' => $this->faker->numberBetween(0, 1000),

            // Ngày xuất bản (chỉ khi status = published)
            'published_at' => $this->faker->optional(0.7)->dateTimeThisYear(),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
