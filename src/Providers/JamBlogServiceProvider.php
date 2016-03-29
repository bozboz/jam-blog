<?php

namespace Bozboz\JamBlog\Providers;

use Bozboz\JamBlog\Categories\Category;
use Bozboz\JamBlog\Posts\Post;
use Bozboz\Jam\Fields\Field;
use Bozboz\Jam\Fields\FieldMapper;
use Bozboz\Jam\Providers\JamServiceProvider;
use Bozboz\Jam\Types\Type;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;


class JamBlogServiceProvider extends JamServiceProvider
{
    protected $blogs;

    public function register()
    {
    }

    public function boot()
    {
        $packageRoot = __DIR__ . '/../../';

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

        $this->fetchConfiguredBlogs();

        $this->registerFieldTypes();

        $this->registerEntityTypes();

        if (! $this->app->routesAreCached()) {
            require "{$packageRoot}/src/Http/routes.php";
        }
    }

    protected function fetchConfiguredBlogs()
    {
        $blogs = [];
        try {
            if (starts_with($this->app['request']->path(), 'admin')) {
                $results = DB::table('entity_values as ev')
                    ->distinct()
                    ->select(
                        'e.name',
                        DB::raw('coalesce(ep.path, e.slug) as slug_root'),
                        'etfo.field_id',
                        'etfo.key',
                        'etfo.value'
                    )
                    ->join('entity_template_field_options as etfo', 'ev.field_id', '=', 'etfo.field_id')
                    ->join('entities as e', 'ev.revision_id', '=', 'e.revision_id')
                    ->leftJoin('entity_paths as ep', function($join) {
                        $join->on('e.id', '=', 'ep.entity_id')
                            ->whereNull('ep.deleted_at')
                            ->whereNull('ep.canonical_id');
                    })
                    ->where('ev.type_alias', 'blog')->get();

                foreach ($results as $row) {
                    $blogs[$row->name]['name'] = $row->name;
                    $blogs[$row->name]['slug_root'] = $row->slug_root;
                    $blogs[$row->name][$row->key] = $row->value;
                }
            }
        } catch (QueryException $e) {
            throw $e;
            // swallow and continue...
        }

        $this->blogs = $blogs;
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
                $mapper->register($blogConfig['posts_type'], new \Bozboz\JamBlog\Types\Post([
                    'menu_title' => $blogConfig['name'],
                    'name' => str_plural(ucwords(str_replace('-', ' ', $blogConfig['posts_type']))),
                    'slug_root' => $blogConfig['slug_root']
                ]));
            }
            if (array_key_exists('categories_type', $blogConfig)) {
                $mapper->register($blogConfig['categories_type'], new \Bozboz\JamBlog\Types\Category([
                    'menu_title' => $blogConfig['name'],
                    'name' => str_plural(ucwords(str_replace('-', ' ', $blogConfig['categories_type']))),
                    'slug_root' => implode('/', [$blogConfig['slug_root'], 'categories'])
                ]));
            }
        }
    }
}
