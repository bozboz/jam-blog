<?php

namespace Bozboz\JamBlog\Http\Controllers\Admin;

use Bozboz\Jam\Http\Controllers\Admin\EntityListController;

class BlogCategoryListController extends EntityListController
{
	protected function getEntityController()
	{
		return BlogCategoryController::class;
	}
}