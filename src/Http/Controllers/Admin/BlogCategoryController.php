<?php

namespace Bozboz\JamBlog\Http\Controllers\Admin;

use Bozboz\JamBlog\Categories\CategoryDecorator;
use Bozboz\Jam\Contracts\EntityRepository;
use Bozboz\Jam\Http\Controllers\Admin\EntityController;

class BlogCategoryController extends EntityController
{
	public function __construct(CategoryDecorator $decorator, EntityRepository $repository)
	{
		parent::__construct($decorator, $repository);
	}

	public function getEntityRevisionController()
	{
		return BlogCategoryRevisionController::class;
	}
}
