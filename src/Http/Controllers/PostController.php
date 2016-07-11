<?php

namespace Bozboz\JamBlog\Http\Controllers;

use Bozboz\JamBlog\Posts\Post;
use Bozboz\JamBlog\Posts\PostRepository;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class PostController extends Controller
{
    public function listing(PostRepository $repository, $blogSlug)
    {
        $blogEntity = $repository->getForPath($blogSlug);
        $repository->hydrate($blogEntity);

        $blogConfig = config('jam-blog.blogs')->where('slug_root', $blogSlug)->first();

        $posts = $repository->getPosts($blogConfig['posts_type']);

        $posts->each(function($post) {
            $post->injectValues();
        });

        return view($blogEntity->template->view)->with([
            'entity' => $blogEntity,
            'metaTitle' => $blogEntity->name,
            'posts' => $posts,
            'archive' => [],
            'categories' => []
        ]);
    }

    public function post(PostRepository $repository, $blogSlug, $postSlug)
    {
        $post = $repository->whereSlug($postSlug);

        $repository->hydrate($post);

        return view($post->template->view)->with([
            'entity' => $post,
            'metaTitle' => $post->name,
        ]);
    }
}
