<?php

namespace Bozboz\JamBlog\Http\Controllers;

use Bozboz\JamBlog\Categories\Category;
use Bozboz\JamBlog\Posts\Post;
use Bozboz\JamBlog\Posts\PostRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends Controller
{
    public function show(PostRepository $repo, Request $request, $blogSlug, $category = null)
    {
        $blogEntity = $repo->getForPath($blogSlug);

        if ( ! $blogEntity) {
            throw new NotFoundHttpException;
        }

        $repo->hydrate($blogEntity);

        $blogConfig = $repo->getBlog($blogEntity);

        if ( ! $blogConfig || ! property_exists($blogConfig, 'category_type')) {
            throw new NotFoundHttpException;
        }

        $categories = explode('/', $category);
        $currentCategory = Category::ofType($blogConfig->category_type)->whereSlug(end($categories))->first();

        if ($category) {
            $categoryEntity = $repo->getForPath($request->path());

            if ( ! $categoryEntity) {
                throw new NotFoundHttpException;
            }

            $repo->hydrate($categoryEntity);

            $categories = $repo->getCategories($blogConfig->post_type, $blogConfig->category_type, $categoryEntity);
            $posts = $repo->postsForCategory($blogConfig->post_type, $categoryEntity);

            $entity = $categoryEntity;
        } else {
            $categories = $repo->getCategories($blogConfig->post_type, $blogConfig->category_type);
            $posts = $repo->forType($blogConfig->post_type)->simplePaginate();

            $entity = $blogEntity;
        }

        return view('jam-blog::categories')->with([
            'layout' => $entity->template->listing_view ?: $entity->template->view,
            'entity' => $entity,
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }
}
