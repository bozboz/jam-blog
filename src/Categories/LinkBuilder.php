<?php

namespace Bozboz\JamBlog\Categories;

use Bozboz\Jam\Entities\Entity;
use Bozboz\Jam\Entities\LinkBuilder as Base;
use Illuminate\Support\Facades\Config;

class LinkBuilder extends Base
{
	protected function calculatePathsForInstance(Entity $instance)
	{
        $path = $instance->getAncestors()->pluck('slug');
        $path->splice(1, 0, Config::get('jam-blog.categories_slug'));
        $path->push($instance->slug);
        return collect(
            str_pad(trim($path->implode('/'), '/'), 1, '/')
        );
	}
}
