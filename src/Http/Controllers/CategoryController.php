<?php

namespace Bozboz\JamBlog\Http\Controllers;

use App\Http\Controllers\Controller;
use Bozboz\JamBlog\Categories\Category;
use Bozboz\JamBlog\Posts\Post;
use Bozboz\JamBlog\Posts\PostRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function listing(PostRepository $repository, Request $request, $blogSlug, $category = null)
    {
        $categories = explode('/', $category);
        $currentCategory = Category::whereSlug(end($categories))->first();

        $blogConfig = config('jam-blog.blogs')->where('slug_root', $blogSlug)->first();

        if ($category) {
            $parentCategory = $repository->getForPath($request->path());

            $categories = $repository->getCategories($blogConfig['categories_type'], $parentCategory);
            $posts = $repository->postsForCategory($blogConfig['posts_type'], $parentCategory);

            $entity = $parentCategory;
        } else {
            $categories = $repository->getCategories($blogConfig['categories_type']);
            $posts = [];

            $entity = $repository->getForPath($blogSlug);
        }

        return view($entity->template->view)->with([
            'entity' => $repository->hydrate($entity),
            'posts' => $posts,
            'categories' => $categories
        ]);
    }
}