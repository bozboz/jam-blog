<?php

namespace Bozboz\JamBlog\Http\Controllers\Admin;

use Bozboz\Jam\Http\Controllers\Admin\EntityRevisionController;

class BlogCategoryRevisionController extends EntityRevisionController
{
	protected function getEntityController()
	{
		return BlogCategoryController::class;
	}
}
