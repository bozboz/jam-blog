<?php

namespace Bozboz\JamBlog\Http\Controllers;

use Bozboz\JamBlog\Posts\Post;
use Bozboz\JamBlog\Posts\PostRepository;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class ArchiveController extends Controller
{
    public function listing(PostRepository $repository, $blogSlug, $dateString = null)
    {
        $blogEntity = $repository->getForPath($blogSlug);
        $repository->hydrate($blogEntity);

        $blogConfig = config('jam-blog.blogs')->where('slug_root', $blogSlug)->first();

        if ($dateString) {
            $dateLength = count(explode('/', $dateString));

            $date = new Carbon(str_pad($dateString, 10, '/01/01'));

            switch ($dateLength) {
                case 3:
                    $startDate = $date->startOfDay()->copy();
                    $endDate = $date->endOfDay();
                break;

                case 2:
                    $startDate = $date->startOfMonth()->copy();
                    $endDate = $date->endOfMonth();
                break;

                case 1:
                    $startDate = $date->startOfYear()->copy();
                    $endDate = $date->endOfYear();
                break;
            }

            $posts = Post::ofType($blogConfig['posts_type'])
                ->whereHas('currentRevision', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('published_at', [$startDate, $endDate]);
                })->with(['template', 'currentRevision', 'paths' => function($query) {
                    $query->whereNull('canonical_id');
                }])->ordered()->active()->simplePaginate();

            $repository->loadCurrentListingValues($posts);
        } else {
            $posts = [];
        }

        $blogEntity->canonical_path = \Request::path();

        return view($blogEntity->template->view)->with([
            'entity' => $blogEntity,
            'posts' => $posts,
            'archive' => $repository->getArchive($blogConfig['posts_type']),
            'categories' => $repository->getCategories($blogConfig['categories_type'])
        ]);
    }
}