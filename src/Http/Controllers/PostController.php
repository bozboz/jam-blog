<?php

namespace Bozboz\JamBlog\Http\Controllers;

use App\Http\Controllers\Controller;
use Bozboz\JamBlog\Posts\Post;
use Bozboz\JamBlog\Posts\PostRepository;
use Carbon\Carbon;

class PostController extends Controller
{
    public function listing(PostRepository $repository, $blogSlug)
    {
        $blogEntity = $repository->getForPath($blogSlug);
        $repository->hydrate($blogEntity);

        $blogConfig = config('jam-blog.blogs')->where('slug_root', $blogSlug)->first();

        $posts = Post::ofType($blogConfig['posts_type'])
            ->with(['template', 'currentRevision', 'paths' => function($query) {
                $query->whereNull('canonical_id');
            }])->ordered()->active()->simplePaginate();

        $repository->loadCurrentListingValues($posts);

        return view($blogEntity->template->view)->with([
            'entity' => $blogEntity,
            'posts' => $posts,
            'archive' => $repository->getArchive($blogConfig['posts_type'])
        ]);
    }

    public function post(PostRepository $repository, $blogSlug, $postSlug)
    {
        $post = $repository->whereSlug($postSlug);
        $repository->hydrate($post);
        return view($post->template->view)->withEntity($post);
    }
}