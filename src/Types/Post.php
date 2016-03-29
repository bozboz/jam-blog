<?php

namespace Bozboz\JamBlog\Types;

use Bozboz\Jam\Types\Type;

class Post extends Type
{
    protected $attributes = [
        'sorter' => \Bozboz\Jam\Types\Sorting\PublishedAt::class,
        'report' => \Bozboz\Admin\Reports\Report::class,
        'link_builder' => \Bozboz\JamBlog\Posts\LinkBuilder::class,
        'menu_builder' => \Bozboz\Jam\Types\Menu\Standalone::class,
        'entity' => \Bozboz\JamBlog\Posts\Post::class
    ];
}
