<?php

namespace Bozboz\JamBlog\Categories;

use Bozboz\Jam\Entities\Entity;

class Category extends Entity
{
    public function scopeOrdered($query)
    {
    	$query->orderBy('name');
    }
}