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
		$config = config('jam-blog.blogs')->where('posts_type', $instance->template->type_alias)->first();
		return $config['slug_root'] . '/'.$path;
	}

	public function categoryLink($category, $post)
	{
		// return
	}

	public function archiveLink($typeAlias, $year=null, $month=null, $post=null)
	{
		$config = config('jam-blog.blogs')->where('posts_type', $typeAlias)->first();
		return url(
			$config['slug_root'] . '/' .
			$config['archive_slug'] . '/' .
			implode('/', array_filter([$year, $month, $post]))
		);
	}
}
