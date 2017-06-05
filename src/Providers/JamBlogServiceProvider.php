<?php

namespace Bozboz\JamBlog\Providers;

use Bozboz\Jam\Fields\FieldMapper;
use Illuminate\Support\ServiceProvider;

class JamBlogServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
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
