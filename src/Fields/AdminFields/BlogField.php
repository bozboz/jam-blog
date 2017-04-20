<?php

namespace Bozboz\JamBlog\Fields\AdminFields;

use Bozboz\Admin\Fields\Field;
use Collective\Html\FormFacade as Form;

class BlogField extends Field
{
    public function getInput()
    {
        return Form::hidden($this->name) .
            '<a class="btn btn-sm btn-info" href="' .
                action('\\Bozboz\\Jam\\Http\\Controllers\\Admin\\EntityController@show', ['type' => $this['post_type']]) .
            '"><i class="fa fa-files-o"></i> View Posts</a>';
    }

    public function getLabel()
    {
        return '';
    }
}