<?php

namespace Bozboz\JamBlog\Fields;

use Bozboz\Admin\Fields\HiddenField;
use Bozboz\JamBlog\Fields\AdminFields\BlogField;
use Bozboz\JamBlog\Posts\PostRepository;
use Bozboz\Jam\Entities\Entity;
use Bozboz\Jam\Entities\EntityDecorator;
use Bozboz\Jam\Entities\Value;
use Bozboz\Jam\Fields\Field;
use Bozboz\Jam\Fields\TypeSelectField;
use Illuminate\Support\Facades\App;

class Blog extends Field
{
    public function getOptionFields()
    {
        return [
            new TypeSelectField('post_type', ['required' => 'required']),
            new TypeSelectField('category_type'),
        ];
    }

    public function getAdminField(Entity $instance, EntityDecorator $decorator, Value $value)
    {
        return new BlogField($this->getInputName(), json_decode(json_encode($this->options_array), true));
    }

    public function getValue(Value $value)
    {
        return App::make(PostRepository::class)->forType($this->getOption('post_type'))->simplePaginate();
    }
}
