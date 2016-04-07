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
        $this->publishes([
            __DIR__ . '/../../config/jam-blog.php' => config_path('jam-blog.php'),
        ]);

        $this->app->bind(
            \Bozboz\Jam\Entities\Contracts\LinkBuilder::class,
            \Bozboz\JamBlog\Entities\LinkBuilder::class
        );

        $this->registerFieldTypes();

        $this->fetchConfiguredBlogs();

        $this->registerEntityTypes();

        if (! $this->app->routesAreCached()) {
            require __DIR__ . "/../Http/routes.php";
        }
    }

    protected function fetchConfiguredBlogs()
    {
        try {
            $config = $this->app['config']->get('jam-blog') ?: [];
            $this->app['config']->set(['jam-blog' => array_merge_recursive($config, [
                'blogs' => collect($this->app['FieldMapper']->get('blog')->fetchConfig())
            ])]);
        } catch (QueryException $e) {
            // 99.99% of the time this will be because the package has only just
            // been installed and the db tables don't exist yet.
            // swallow and continue...
        }
    }

    protected function registerFieldTypes()
    {
        $mapper = $this->app['FieldMapper'];

        $mapper->register([
            'blog' => \Bozboz\JamBlog\Fields\Blog::class,
        ]);
    }

    protected function registerEntityTypes()
    {
        $mapper = $this->app['EntityMapper'];

        foreach ($this->app['config']->get('jam-blog.blogs') as $blogConfig) {

            $mapper->register($blogConfig['posts_type'], new \Bozboz\Jam\Types\Type([
                'menu_title' => $blogConfig['name'],
                'name' => $blogConfig['posts_name'],
                'slug_root' => $blogConfig['slug_root'],
                'entity' => \Bozboz\JamBlog\Posts\Post::class,
                'report' => \Bozboz\Admin\Reports\Report::class,
                'link_builder' => \Bozboz\JamBlog\Posts\LinkBuilder::class,
                'menu_builder' => \Bozboz\Jam\Types\Menu\Standalone::class,
            ]));

            if (array_key_exists('categories_enabled', $blogConfig)) {

                $mapper->register($blogConfig['categories_type'], new \Bozboz\Jam\Types\NestedType([
                    'menu_title' => $blogConfig['name'],
                    'name' => $blogConfig['categories_name'],
                    'slug_root' => implode('/', [$blogConfig['slug_root'], 'categories']),
                    'entity' => \Bozboz\Jam\Entities\SortableEntity::class,
                    'link_builder' => \Bozboz\JamBlog\Categories\LinkBuilder::class,
                    'menu_builder' => \Bozboz\Jam\Types\Menu\Standalone::class,
                ]));

            }

        }
    }
}
