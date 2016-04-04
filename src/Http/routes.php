<?php

/*
|--------------------------------------------------------------------------
| Admin Package Routes
|--------------------------------------------------------------------------
*/

$blogs = $this->app['config']->get('jam-blog.blogs');

Route::group(array('middleware' => ['web'], 'namespace' => 'Bozboz\JamBlog\Http\Controllers'), function() use ($blogs) {

    $blogs->map(function($blog) {

        /**
         * Archive
         */
        if (array_key_exists('archive_enabled', $blog)) {

            // Archive Post
            Route::get('{blog_root}/'.$blog['archive_slug'].'/{date}/post/{post_id}{slug?}', [
                'as' => 'jam-blog.archive.post',
                'uses' => 'ArchiveController@post'
            ])->where([
                'blog_root' => $blog['slug_root'],
                'date' => '\d{4}/?(\d{2})?/?(\d{2})?',
                'post_id' => '\d+',
                'slug' => '/.+',
            ]);

            // Archive Listing
            Route::get('{blog_root}/'.$blog['archive_slug'].'/{date?}', [
                'as' => 'jam-blog.archive',
                'uses' => 'ArchiveController@listing'
            ])->where([
                'blog_root' => $blog['slug_root'],
                'date' => '\d{4}/?(\d{2})?/?(\d{2})?',
            ]);

        }


        /**
         * Categories
         */
        if (array_key_exists('categories_enabled', $blog)) {

            // Category Post
            Route::get('{blog_root}/'.$blog['categories_slug'].'/{category}/post/{post_id}{slug?}', [
                'as' => 'jam-blog.category.post',
                'uses' => 'PostController@post'
            ])->where([
                'blog_root' => $blog['slug_root'],
                'category' => '.+/?.+?',
                'post_id' => '\d+',
                'slug' => '/.+',
            ]);

            // Category Listing
            Route::get('{blog_root}/'.$blog['categories_slug'].'/{category?}', [
                'as' => 'jam-blog.category',
                'uses' => 'CategoryController@listing'
            ])->where([
                'blog_root' => $blog['slug_root'],
                'category' => '(.+)?',
            ]);

        }

        Route::get('{blog_root}/{postSlug}', [
            'as' => 'post',
            'uses' => 'PostController@post'
        ])->where([
            'blog_root' => $blog['slug_root'],
            'postSlug' => '(.+)?'
        ]);

        Route::get('{blog_root}', [
            'as' => 'post',
            'uses' => 'PostController@listing'
        ])->where([
            'blog_root' => $blog['slug_root'],
        ]);

    });


});

