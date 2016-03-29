<?php

namespace Bozboz\JamBlog\Posts;

use Bozboz\Jam\Entities\Entity;
use Bozboz\Jam\Entities\EntityPath;
use Bozboz\Jam\Entities\LinkBuilder as BaseLinkBuilder;
use Illuminate\Support\Facades\Config;

class LinkBuilder extends BaseLinkBuilder
{
	protected function calculatePathForInstance(Entity $instance)
	{
		$path = parent::calculatePathForInstance($instance);
		return $instance->template->type()->slug_root . ($path ? '/'.$path : null);
	}

	public function categoryLink($category, $post)
	{
		// return
	}
}
