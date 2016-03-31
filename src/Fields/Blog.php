<?php

namespace Bozboz\JamBlog\Fields;

use Bozboz\Admin\Fields\FieldGroup;
use Bozboz\Admin\Fields\HiddenField;
use Bozboz\JamBlog\Categories\Category;
use Bozboz\JamBlog\Posts\Post;
use Bozboz\Jam\Entities\CurrentValue;
use Bozboz\Jam\Entities\Entity;
use Bozboz\Jam\Entities\EntityDecorator;
use Bozboz\Jam\Entities\Revision;
use Bozboz\Jam\Entities\Value;
use Bozboz\Jam\Fields\Field;
use Bozboz\Jam\Fields\TypeSelectField;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Blog extends Field
{
    public static function boot()
    {
        parent::boot();

        static::saved([__CLASS__, 'clearConfigCache']);
    }

    public static function clearConfigCache()
    {
        Cache::forget('blogConfig');
    }

    /**
     * Fetch any "Blog" fields on entities with their config and cache it forever
     * Cache is cleared whenever new config is saved
     *
     * @return array or blog config
     */
    public static function fetchConfig()
    {
        return Cache::rememberForever('blogConfig', function() {
            return CurrentValue::selectFields(['blog'])
                ->addSelect('entities.name')
                ->addSelect(DB::raw('coalesce(entity_paths.path, entities.slug) as slug_root'))
                ->join('entities', 'entities.revision_id', '=', 'entity_values.revision_id')
                ->leftJoin('entity_paths', function($join) {
                    $join->on('entities.id', '=', 'entity_paths.entity_id')
                        ->whereNull('entity_paths.deleted_at')
                        ->whereNull('entity_paths.canonical_id');
                })
                ->get()->map(function($blog) {
                    return array_merge([
                        'name' => $blog->name,
                        'slug_root' => $blog->slug_root
                    ], array_filter($blog->getOptions()));
                });
        });
    }

    public function getAdminField(Entity $instance, EntityDecorator $decorator, Value $value)
    {
        return new FieldGroup($this->getInputLabel(), [
            new HiddenField($this->name),
            new LinkField($this->getInputName(), [
                'route' => 'admin.entities.index',
                'blogName' => $instance->slug,
                'entityTypeAlias' => $this->getOption('posts_type')
            ])
        ]);
    }

    public function getOptionFields()
    {
        return [
            new TypeSelectField('Posts'),
            new TypeSelectField('Categories'),
        ];
    }

    public function injectValue(Entity $entity, Value $value)
    {
        $entity->setValue($value);
        $blog = $this->getValue($value);
        $repository = app(\Bozboz\Jam\Contracts\EntityRepository::class);
        $repository->loadCurrentListingValues($blog['posts']);
        $entity->setAttribute($value->key, $blog);
    }

    public function getValue(Value $value)
    {
        $categoriesType = $this->getOption('categories_type');
        return collect([
            'posts' => Post::whereHas('template', function($query) {
                    $query->whereTypeAlias($this->getOption('posts_type'));
                })->with(['template', 'currentRevision', 'paths' => function($query) {
                    $query->whereNull('canonical_id');
                }])->ordered()->active()->simplePaginate(),
            'categories' => ($categoriesType
                    ? Category::whereHas('template', function($query) use ($categoriesType) {
                        $query->whereTypeAlias($categoriesType);
                    })->with('template')->ordered()->active()->get()
                    : []
                )
        ]);
    }
}
