<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'excerpt'     => $this->excerpt,
            'content'     => $this->content,
            'thumbnail'   => $this->thumbnail,
            'status'      => $this->status,
            'published_at' => $this->published_at,
            'views'       => $this->views_count,
            'likes'       => $this->likes_count,

            // Quan hệ
            // 'category'    => new CategoryResource($this->whenLoaded('category')),
            // 'tags'        => TagResource::collection($this->whenLoaded('tags')),
            // 'author'      => new UserResource($this->whenLoaded('user')),
        ];
    }
}
