<?php

namespace Bozboz\JamBlog\Http\Controllers\Admin;

use Bozboz\Jam\Http\Controllers\Admin\EntityListController;

class BlogPostListController extends EntityListController
{
	protected function getEntityController()
	{
		return BlogPostController::class;
	}
}