<?php

namespace Bozboz\JamBlog\Fields;

use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\FieldGroup;
use Bozboz\Admin\Fields\HiddenField;
use Bozboz\Admin\Fields\TextField;
use Bozboz\JamBlog\Categories\Category;
use Bozboz\JamBlog\Posts\Post;
use Bozboz\Jam\Entities\CurrentValue;
use Bozboz\Jam\Entities\Entity;
use Bozboz\Jam\Entities\EntityDecorator;
use Bozboz\Jam\Entities\Revision;
use Bozboz\Jam\Entities\Value;
use Bozboz\Jam\Fields\Field;
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
            return collect(DB::table('entity_template_fields')->select(
                'entity_template_fields.id as field_id',
                'entities.name',
                DB::raw('coalesce(entity_paths.path, entities.slug) as slug_root'),
                DB::raw("group_concat(entity_template_field_options.key separator ',') as option_keys"),
                DB::raw("group_concat(entity_template_field_options.value separator ',') as option_values")
            )
            ->join(
                'entity_template_field_options',
                'entity_template_fields.id', '=', 'entity_template_field_options.field_id'
            )
            ->join(
                'entity_templates',
                'entity_templates.id', '=', 'entity_template_fields.template_id'
            )
            ->join(
                'entities',
                'entities.template_id', '=', 'entity_templates.id'
            )
            ->leftJoin('entity_paths', function($join) {
                $join->on('entities.id', '=', 'entity_paths.entity_id')
                    ->whereNull('entity_paths.deleted_at')
                    ->whereNull('entity_paths.canonical_id');
            })
            ->where('entity_template_fields.type_alias', 'blog')
            ->groupBy('entity_template_fields.id')
            ->get())->map(function($blog) {
                $config = array_merge(config('jam-blog.defaults'), [
                        'field_id' => $blog->field_id,
                        'name' => $blog->name,
                        'slug_root' => $blog->slug_root
                    ], array_filter(
                        array_combine(
                            explode(',', $blog->option_keys),
                            explode(',', $blog->option_values)
                        )
                    )
                );
                if (!array_key_exists('posts_name', $config)) {
                    $config['posts_name'] = config('jam-blog.defaults.posts_type');
                }
                $config['posts_type'] = str_slug("{$config['name']}-{$config['posts_name']}");

                if (!array_key_exists('categories_type', $config)) {
                    $config['categories_name'] = config('jam-blog.defaults.categories_name');
                }
                $config['categories_type'] = str_slug("{$config['name']}-{$config['categories_name']}");

                return $config;
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
            new FieldGroup('Posts', [
                new TextField([
                    'label' => 'Name',
                    'name' => 'options_array[posts_name]',
                    'placeholder' => config('jam-blog.defaults.posts_name'),
                ]),
            ]),
            new FieldGroup('Categories', [
                new CheckboxField([
                    'label' => 'Enabled',
                    'name' => 'options_array[categories_enabled]',
                ]),
                new TextField([
                    'label' => 'Name',
                    'name' => 'options_array[categories_name]',
                    'placeholder' => config('jam-blog.defaults.categories_name'),
                ]),
                new TextField([
                    'label' => 'Slug',
                    'name' => 'options_array[categories_slug]',
                    'placeholder' => config('jam-blog.defaults.categories_slug'),
                ]),
            ]),
            new FieldGroup('Archive', [
                new CheckboxField([
                    'label' => 'Enabled',
                    'name' => 'options_array[archive_enabled]',
                ]),
                new TextField([
                    'label' => 'Slug',
                    'name' => 'options_array[archive_slug]',
                    'placeholder' => config('jam-blog.defaults.archive_slug'),
                ]),
            ]),
        ];
    }

    public function getConfig()
    {
        return config('jam-blog.blogs')->where('field_id', $this->id)->first();
    }

    public function getOption($key)
    {
        $config = $this->getConfig();
        return $config[$key] ?: config("jam-blog.defaults.{$key}");
    }

    public function saveValue(Revision $revision, $value)
    {
        parent::saveValue($revision, $value);
        static::clearConfigCache();
    }

    public function injectValue(Entity $entity, Value $value)
    {
        $entity->setValue($value);
        $blog = $this->getValue($value);
        $entity->setAttribute($value->key, $blog);
    }

    public function getValue(Value $value)
    {
        return $this->getConfig();
    }
}
