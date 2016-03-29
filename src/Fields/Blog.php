<?php

namespace Bozboz\JamBlog\Fields;

use Bozboz\Admin\Fields\FieldGroup;
use Bozboz\Admin\Fields\HiddenField;
use Bozboz\JamBlog\Categories\Category;
use Bozboz\JamBlog\Posts\Post;
use Bozboz\Jam\Entities\Entity;
use Bozboz\Jam\Entities\EntityDecorator;
use Bozboz\Jam\Entities\Value;
use Bozboz\Jam\Fields\Field;
use Bozboz\Jam\Fields\TypeSelectField;

class Blog extends Field
{
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
        return array_merge([
            new TypeSelectField('Posts'),
            new TypeSelectField('Categories'),
        ]);
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
        return collect([
            'posts' => Post::whereHas('template', function($query) {
                $query->whereTypeAlias($this->getOption('posts_type'));
            })->ordered()->active()->simplePaginate(),
            'categories' => ($this->getOption('categories_type')
                ? Category::whereHas('template', function($query) {
                    $query->whereTypeAlias($this->getOption('categories_type'));
                })->ordered()->active()->get()
                : []
            )
        ]);
    }
}
