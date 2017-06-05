# JAM Blog


## 1. Installation

1. Require the package in Composer, by running `composer require bozboz/jam-blog`
2. Add `Bozboz\JamBlog\Providers\JamBlogServiceProvider::class,` to the providers array config/app.php


## 2. Setup


### 2.1. Posts

You'll need an entity type to represent your posts.

eg.
```php
<?php
'blog_posts' => new Type([
    'name' => 'Posts',
    'menu_title' => 'Blog',
    'entity' => Post::class,
    'link_builder' => LinkBuilder::class,
    'menu_builder' => Standalone::class,
]),
```

You should give your post templates a BelongsToEntity field with make parent enabled and set to your blog listing page. This will ensure that your posts are nested inside the blog.


### 2.2. Categories

Categories are optional so if you don't need them don't bother. 

Add a type.

eg.
```php
<?php
'blog_categories' => new Type([
    'name' => 'Categories',
    'menu_title' => 'Blog',
    'link_builder' => \Bozboz\JamBlog\Categories\LinkBuilder::class,
    'menu_builder' => Standalone::class,
]),
```

__Note:__ The example above shows the category link builder from this package, that is the link builder that should be used to ensure the correct routes are generated for your categories.

You should also give the category tempaltes a parent field set to your main blog listing page, as with posts. 


### 2.3. Listing page

This can be a template created in any area of JAM, just make sure that you add the "Blog" field to its template and assign the posts and categories types.

### 2.4. Config

There is a config file in the package that will get merged with any config set in the app and everything in it is explained in there.