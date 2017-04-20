<?php

Route::group(array('middleware' => ['web'], 'namespace' => 'Bozboz\JamBlog\Http\Controllers'), function() {

    $config = Config::get('jam-blog');

    /**
     * Archive
     */

    // // Archive Post
    // Route::get('{blog_root}/archive/{date}/post/{post_id}{slug?}', [
    //     'as' => 'jam-blog.archive.post',
    //     'uses' => 'ArchiveController@post'
    // ])->where([
    //     'date' => '\d{4}/?(\d{2})?/?(\d{2})?',
    //     'post_id' => '\d+',
    //     'slug' => '/.+',
    // ]);

    // Archive Listing
    Route::get('{blog_root}/' . $config['archive_slug'] . '/{date?}', [
        'as' => 'jam-blog.archive',
        'uses' => 'ArchiveController@show'
    ])->where([
        'date' => '\d{4}/?(\d{2})?/?(\d{2})?',
    ]);



    /**
     * Categories
     */

    // // Category Post
    // Route::get('{blog_root}/categories/{category}/post/{post_id}{slug?}', [
    //     'as' => 'jam-blog.category.post',
    //     'uses' => 'PostController@post'
    // ])->where([
    //     'category' => '.+/?.+?',
    //     'post_id' => '\d+',
    //     'slug' => '/.+',
    // ]);

    // Category show
    Route::get('{blog_root}/' . $config['categories_slug'] . '/{category?}', [
        'as' => 'jam-blog.category',
        'uses' => 'CategoryController@show'
    ])->where([
        'category' => '(.+)?',
    ]);


});

