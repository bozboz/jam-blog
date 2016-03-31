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
    protected $blogs;

    public function register()
    {
    }

    public function boot()
    {
        $permissions = $this->app['permission.handler'];
        foreach ($this->app['config']->get('jam-blog.blogs') as $blogName => $blogConfig) {
            foreach ($blogConfig['entities'] as $entityName => $menuName) {
                $permissions->define([
                    "create_{$blogName}_{$entityName}" => 'Bozboz\Permissions\Rules\ModelRule',
                    "view_{$blogName}_{$entityName}" => 'Bozboz\Permissions\Rules\ModelRule',
                    "edit_{$blogName}_{$entityName}" => 'Bozboz\Permissions\Rules\ModelRule',
                    "delete_{$blogName}_{$entityName}" => 'Bozboz\Permissions\Rules\ModelRule',
                ]);
            }
        }

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
            $this->blogs = $this->app['FieldMapper']->get('blog')->fetchConfig();
        } catch (QueryException $e) {
            // 99.9% of the time this will be because the package has only just
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

        foreach ($this->blogs as $blogConfig) {
            if (array_key_exists('posts_type', $blogConfig)) {
                $mapper->register($blogConfig['posts_type'], new \Bozboz\Jam\Types\Type([
                    'menu_title' => $blogConfig['name'],
                    'name' => str_plural(ucwords(str_replace('-', ' ', $blogConfig['posts_type']))),
                    'slug_root' => $blogConfig['slug_root'],
                    'entity' => \Bozboz\JamBlog\Posts\Post::class,
                    'report' => \Bozboz\Admin\Reports\Report::class,
                    'link_builder' => \Bozboz\Jam\Entities\LinkBuilder::class,
                    'menu_builder' => \Bozboz\Jam\Types\Menu\Standalone::class,
                ]));
            }
            if (array_key_exists('categories_type', $blogConfig)) {
                $mapper->register($blogConfig['categories_type'], new \Bozboz\Jam\Types\Type([
                    'menu_title' => $blogConfig['name'],
                    'name' => str_plural(ucwords(str_replace('-', ' ', $blogConfig['categories_type']))),
                    'slug_root' => implode('/', [$blogConfig['slug_root'], 'categories']),
                    'entity' => \Bozboz\Jam\Entities\SortableEntity::class,
                    'link_builder' => \Bozboz\Jam\Entities\LinkBuilder::class,
                    'menu_builder' => \Bozboz\Jam\Types\Menu\Standalone::class,
                ]));
            }
        }
    }
}
