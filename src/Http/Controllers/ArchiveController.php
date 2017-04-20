<?php

namespace Bozboz\JamBlog\Http\Controllers;

use Bozboz\JamBlog\Posts\Post;
use Bozboz\JamBlog\Posts\PostRepository;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArchiveController extends Controller
{
    public function show(PostRepository $repo, $blogSlug, $dateString = null)
    {
        $blogEntity = $repo->getForPath($blogSlug);

        if ( ! $blogEntity) {
            throw new NotFoundHttpException;
        }

        $repo->hydrate($blogEntity);

        $blogConfig = $repo->getBlog($blogEntity);

        if ( ! $blogConfig) {
            throw new NotFoundHttpException;
        }

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

            $posts = Post::ofType($blogConfig->post_type)
                ->whereHas('currentRevision', function($query) use ($startDate, $endDate) {
                    $query->whereBetween('published_at', [$startDate, $endDate]);
                })
                ->with('template', 'currentRevision')->withCanonicalPath()
                ->ordered()->active()->simplePaginate();

            $posts->each(function($post) {
                $post->injectValues();
            });

        } else {
            $posts = Post::ofType($blogConfig->post_type)
                ->with('template', 'currentRevision')->withCanonicalPath()
                ->ordered()->active()->simplePaginate();
        }

        return view('jam-blog::archive')->with([
            'layout' => $blogEntity->template->listing_view,
            'entity' => $blogEntity,
            'posts' => $posts,
            'archive' => $repo->getArchive($blogEntity->canonical_path, $blogConfig->post_type),
        ]);
    }
}
