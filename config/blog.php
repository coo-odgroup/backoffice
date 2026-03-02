<?php

return [

    'category_banner' => [
        'path' => env('BLOG_CATEGORY_BANNER_PATH', 'uploads/blog/categories'),
        'width' => env('BLOG_CATEGORY_BANNER_WIDTH', 1600),
        'height' => env('BLOG_CATEGORY_BANNER_HEIGHT', 500),
        'max_size' => env('BLOG_CATEGORY_BANNER_MAX_SIZE', 2048),
    ],

];