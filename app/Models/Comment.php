<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id',
        'author_name',
        'author_email',
        'content',
    ];
}
