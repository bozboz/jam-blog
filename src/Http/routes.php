<?php

/*
|--------------------------------------------------------------------------
| Admin Package Routes
|--------------------------------------------------------------------------
*/

$blogs = $this->app['config']->get('jam-blog.blogs');

Route::group(array('middleware' => ['web']), function() use ($blogs) {

});

