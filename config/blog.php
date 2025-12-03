<?php

/**
 * Blog Configuration
 * 
 * Central place for all blog-related constants
 * Use: config('blog.post.statuses') or config('blog.post.default_status')
 */

return [
  'post' => [
    // Post statuses
    'statuses' => [
      'draft'     => 'draft',
      'published' => 'published',
      'archived'  => 'archived',
    ],
    'default_status' => 'draft',
    'status_labels' => [
      'draft'     => 'Nháp',
      'published' => 'Công bố',
      'archived'  => 'Lưu trữ',
    ],

    // Thumbnail configuration
    'thumbnail' => [
      'path'       => 'img/post',
      'max_size'   => 5048,      // KB
      'width'      => 1200,      // pixels
      'height'     => 630,       // pixels
      'thumb_quality' => 75,     // percentage
      'allowed_formats' => ['jpeg', 'png', 'gif', 'webp'],
    ],

    // SEO settings
    'seo' => [
      'meta_title_min'  => 30,
      'meta_title_max'  => 60,
      'meta_desc_min'   => 120,
      'meta_desc_max'   => 160,
    ],

    // Pagination
    'per_page' => 10,

    // Default values
    'defaults' => [
      'view_count' => 0,
      'like_count' => 0,
    ],
  ],

  'comment' => [
    'statuses' => [
      'pending'  => 'pending',
      'approved' => 'approved',
      'spam'     => 'spam',
      'hidden'   => 'hidden',
    ],
    'default_status' => 'pending',
    'status_labels' => [
      'pending'  => 'Chờ duyệt',
      'approved' => 'Đã duyệt',
      'spam'     => 'Spam',
      'hidden'   => 'Ẩn',
    ],
    'per_page' => 15,
  ],

  'category' => [
    'per_page' => 10,
  ],

  'tag' => [
    'per_page' => 20,
  ],
];
