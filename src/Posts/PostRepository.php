<?php

namespace Bozboz\JamBlog\Posts;

use Bozboz\JamBlog\Categories\Category;
use Bozboz\JamBlog\Posts\Post;
use Bozboz\Jam\Repositories\EntityRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PostRepository extends EntityRepository
{
    public function getArchive($postsType)
    {
        $results = DB::table('entities')->selectRaw("
                COUNT(entities.id) as post_count,
                EXTRACT(YEAR FROM published_at) as year,
                EXTRACT(MONTH FROM published_at) as month
            ")
            ->join('entity_templates', 'entity_templates.id', '=', 'entities.template_id')
            ->join('entity_revisions', 'entity_revisions.id', '=', 'entities.revision_id')
            ->whereTypeAlias($postsType)
            ->where('entity_revisions.published_at', '<', new Carbon)
            ->whereNull('entities.deleted_at')
            ->groupBy(DB::raw("DATE_FORMAT(published_at, '%c%Y')"))
            ->orderBy('published_at', 'desc')
            ->get();

        $type = app('EntityMapper')->get($postsType);
        $linkBuilder = $type->getLinkBuilder();

        $config = config('jam-blog.blogs')->where('posts_type', $postsType)->first();

        $archive = [];
        foreach ($results as $row) {
            $archive[$row->year]['year'] = $row->year;
            $archive[$row->year]['url'] = $linkBuilder->archiveLink($postsType, $row->year);

            $row->date = new Carbon("{$row->year}-{$row->month}-01");
            $row->url = $linkBuilder->archiveLink($postsType, $row->year, str_pad($row->month, 2, '0', STR_PAD_LEFT));
            $archive[$row->year]['months'][$row->month] = $row;
        }

        return $archive;
    }

    public function getCategories($categoriesType, $parentCategory = null)
    {
        if ($parentCategory) {
            $query = $parentCategory->descendants();
        } else {
            $query = Category::ofType($categoriesType);
        }
        return $this->loadCurrentListingValues($query->active()->ordered()->get())->toTree();
    }

    public function postsForCategory($postsType, $categoryId)
    {
        return $this->loadCurrentListingValues(Post::ofType($postsType)->whereBelongsToEntity('category', $categoryId)->with('template')->get());
    }
}