<?php

namespace Bozboz\JamBlog\Posts;

use Bozboz\Jam\Entities\Entity;

class Post extends Entity
{
	public function scopeOrdered($query)
	{
		$query->select('entities.*')
			->join('entity_revisions as order_join', 'entities.revision_id', '=', 'order_join.id')
			->orderBy('order_join.published_at', 'desc')
			->orderBy('order_join.created_at', 'desc');
	}
}