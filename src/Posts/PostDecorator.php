<?php

namespace Bozboz\JamBlog\Posts;

use Bozboz\JamBlog\Posts\Post;
use Bozboz\Jam\Entities\EntityDecorator;
use Bozboz\Jam\Fields\FieldMapper;
use Illuminate\Database\Eloquent\Builder;

class PostDecorator extends EntityDecorator
{
	public function __construct(Post $entity, FieldMapper $fieldMapper)
	{
		parent::__construct($entity, $fieldMapper);
	}

	public function isSortable()
	{
		return false;
	}

	public function modifyListingQuery(Builder $query)
	{
		$query->ordered();
		parent::modifyListingQuery($query);
	}
}