<?php

/*
|--------------------------------------------------------------------------
| Admin Package Routes
|--------------------------------------------------------------------------
*/

// Route::group(array('middleware' => ['web'], 'namespace' => 'Bozboz\JamBlog\Http\Controllers\Admin', 'prefix' => 'admin'), function() {

// 	/*=============================
// 	=            Posts            =
// 	=============================*/
// 	Route::resource('posts', 'BlogPostController', ['except' => ['create']]);
// 	Route::get('posts/{type}/create', 'BlogPostController@createOfType');
// 	Route::post('posts/{id}/publish', 'BlogPostController@publish');
// 	Route::post('posts/{id}/unpublish', 'BlogPostController@unpublish');
// 	Route::post('posts/{id}/schedule', 'BlogPostController@schedule');

// 	Route::resource('post-revisions', 'BlogPostRevisionController', ['except' => ['create', 'update', 'destroy']]);
// 	Route::post('post-revisions/{type}/revert', 'BlogPostRevisionController@revert');

// 	Route::resource('post-entity-list', 'BlogPostListController', ['except' => ['create']]);
// 	Route::get('post-entity-list/{type}/{parent_id}/create', [
// 		'uses' => 'BlogPostListController@createForEntityListField',
// 		'as' => 'admin.post-entity-list.create-for-list'
// 	]);

// 	/*==================================
// 	=            Categories            =
// 	==================================*/
// 	Route::resource('post-categories', 'BlogCategoryController', ['except' => ['create']]);
// 	Route::get('post-categories/{type}/create', 'BlogCategoryController@createOfType');
// 	Route::post('post-categories/{id}/publish', 'BlogCategoryController@publish');
// 	Route::post('post-categories/{id}/unpublish', 'BlogCategoryController@unpublish');
// 	Route::post('post-categories/{id}/schedule', 'BlogCategoryController@schedule');

// 	Route::resource('category-revisions', 'BlogCategoryRevisionController', ['except' => ['create', 'update', 'destroy']]);
// 	Route::post('category-revisions/{type}/revert', 'BlogCategoryRevisionController@revert');

// 	Route::resource('category-entity-list', 'BlogCategoryListController', ['except' => ['create']]);
// 	Route::get('category-entity-list/{type}/{parent_id}/create', [
// 		'uses' => 'BlogCategoryListController@createForEntityListField',
// 		'as' => 'admin.category-entity-list.create-for-list'
// 	]);
// });

