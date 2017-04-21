<?php

namespace Bozboz\JamBlog\Providers;

use Bozboz\JamBlog\Categories\Category;
use Bozboz\JamBlog\Posts\Post;
use Bozboz\Jam\Fields\Field;
use Bozboz\Jam\Fields\FieldMapper;
use Bozboz\Jam\Providers\JamServiceProvider;
use Bozboz\Jam\Types\Type;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;


class JamBlogServiceProvider extends JamServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        // $this->publishes([
        //     __DIR__ . '/../../config/jam-blog.php' => config_path('jam-blog.php'),
        // ]);
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/jam-blog.php', 'jam-blog'
        );

        if (! $this->app->routesAreCached()) {
            require __DIR__ . "/../Http/routes.php";
        }

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'jam-blog');

        $this->registerFieldTypes();
    }

    protected function registerFieldTypes()
    {
        $this->app['FieldMapper']->register([
            'blog' => \Bozboz\JamBlog\Fields\Blog::class,
        ]);
    }
}
