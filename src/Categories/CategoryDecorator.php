<?php

namespace Bozboz\JamBlog\Categories;

use Bozboz\JamBlog\Categories\Category;
use Bozboz\Jam\Entities\EntityDecorator;
use Bozboz\Jam\Fields\FieldMapper;

class CategoryDecorator extends EntityDecorator
{
	public function __construct(Category $entity, FieldMapper $fieldMapper)
	{
		parent::__construct($entity, $fieldMapper);
	}
}