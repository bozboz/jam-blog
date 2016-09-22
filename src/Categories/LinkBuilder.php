<?php

namespace Bozboz\JamBlog\Categories;

use Bozboz\Jam\Entities\Entity;
use Bozboz\Jam\Entities\EntityPath;
use Bozboz\Jam\Entities\LinkBuilder as BaseLinkBuilder;
use Illuminate\Support\Facades\Config;

class LinkBuilder extends BaseLinkBuilder
{
	protected function calculatePathsForInstance(Entity $instance)
	{
		$path = parent::calculatePathForInstance($instance);
		$config = config('jam-blog.blogs')->where('categories_type', $instance->template->type_alias)->first();
		return collect($config['slug_root'] . '/' . $config['categories_slug'] . '/' . $path);
	}
}
