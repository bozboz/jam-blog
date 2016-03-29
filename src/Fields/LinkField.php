<?php

namespace Bozboz\JamBlog\Fields;

use Bozboz\Admin\Fields\Field;

class LinkField extends Field
{
    public function getInput()
    {
    }

    public function render($errors)
    {
        return '<a href="'.route($this->route, ['type' => $this->entityTypeAlias]).'" class="btn btn-info">View '.$this->name.'</a>';
    }
}