<?php

namespace Bozboz\JamBlog\Posts;

use Bozboz\JamBlog\Categories\Category;
use Bozboz\JamBlog\Posts\Post;
use Bozboz\Jam\Repositories\EntityRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PostRepository extends EntityRepository
{
    public function getArchive($slugRoot, $postsType)
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

        $archive = (object)[];
        foreach ($results as $row) {
            if (!property_exists($archive, $row->year)) {
                $archive->{$row->year} = (object)[
                    'year' => $row->year,
                    'url' => $slugRoot . '/' . Config::get('jam-blog.archive_slug') . '/' . $row->year,
                    'months' => (object)[]
                ];
            }

            $row->date = new Carbon("{$row->year}-{$row->month}-01");
            $row->url = $archive->{$row->year}->url . '/' . str_pad($row->month, 2, '0', STR_PAD_LEFT);
            $archive->{$row->year}->months->{$row->month} = $row;
        }

        return $archive;
    }

    public function getCategories($postsType, $categoriesType, $parentCategory = null)
    {
        if ($parentCategory) {
            $query = $parentCategory->descendants();
        } else {
            $query = Category::ofType($categoriesType);
        }
        $categories = $query->with('template')
            ->withCanonicalPath()
            ->active()->ordered()->get();

        $categories->each(function($category) {
            $category->injectValues();
        });

        $categoryIds = $categories->pluck('id')->all();

        $relationMethod = Config::get('jam-blog.category_relation_method');
        $relation = Config::get('jam-blog.category_relation_field_name');

        if ($relationMethod === 'whereBelongsTo') {
            $postCounts = Post::ofType($postsType)
                ->{$relationMethod}($relation, $categoryIds)
                ->selectRaw('foreign_key as category_id, count(*) as count')
                ->groupBy('value')->get();
        } else {
            $postCounts = collect();
        }

        return $categories->map(function($category) use ($postCounts) {
            $categoryStats = $postCounts->where('category_id', $category->id)->first();
            $category->post_count = $categoryStats ? $categoryStats->count : 0;
            return $category;
        })->toTree();
    }

    public function whereSlug($slug)
    {
        return Post::whereSlug($slug)->withFields()->active()->first();
    }

    public function getPosts($postsType)
    {
        return Post::ofType($postsType)
            ->withFields()
            ->ordered()->active()->simplePaginate();
    }

    public function postsForCategory($postsType, $category)
    {
        $categories = $this->forType($category->template->type_alias)->descendantsOf($category->id)->pluck('id')->push($category->id);

        $relationMethod = Config::get('jam-blog.category_relation_method');
        $relation = Config::get('jam-blog.category_relation_field_name');

        $posts = Post::ofType($postsType)
                ->select('entities.*')
                ->withCanonicalPath()
                ->with('template', 'currentRevision')
                ->{$relationMethod}($relation, $categories->all())
                ->simplePaginate();

        return $posts;
    }

    public function getBlog($entity)
    {
        $blog = $entity->currentValues->filter(function($value) {
            return $value->type_alias === 'blog';
        })->first();

        if ( ! $blog) {
            return false;
        }

        return (object)$blog->getOptions();
    }
}
