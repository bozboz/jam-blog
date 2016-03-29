<?php

namespace Bozboz\JamBlog\Http\Controllers\Admin;

use Bozboz\Admin\Reports\Report;
use Bozboz\JamBlog\Posts\PostDecorator;
use Bozboz\Jam\Contracts\EntityRepository;
use Bozboz\Jam\Http\Controllers\Admin\EntityController;

class BlogPostController extends EntityController
{
	public function __construct(PostDecorator $decorator, EntityRepository $repository)
	{
		parent::__construct($decorator, $repository);
	}

	protected function getListingReport()
	{
		return new Report($this->decorator);
	}
}
