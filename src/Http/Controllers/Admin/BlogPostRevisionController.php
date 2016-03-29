<?php

namespace Bozboz\JamBlog\Http\Controllers\Admin;

use Bozboz\Jam\Http\Controllers\Admin\EntityRevisionController;

class BlogPostRevisionController extends EntityRevisionController
{
	protected function getEntityController()
	{
		return BlogPostController::class;
	}
}
