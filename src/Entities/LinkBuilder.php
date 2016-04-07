<?php

namespace Bozboz\JamBlog\Entities;

use Bozboz\Jam\Entities\LinkBuilder as Base;
use Bozboz\Jam\Entities\Entity;
use Bozboz\Jam\Entities\EntityPath;

class LinkBuilder extends Base
{
    public function archiveLink($config)
    {
        return url("{$config['slug_root']}/{$config['archive_slug']}");
    }
}
