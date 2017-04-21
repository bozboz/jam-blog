<?php

return [
    // This will suffix the blog page url to form archive urls
    // eg. /blog/archive/2017/04
    'archive_slug' => 'archive',

    // This will site between the blog url and the category slug
    // ie. /blog/categories/teacups
    'categories_slug' => 'categories',

    // The method used to inspect the relation between posts and categories
    // One to many: whereBelongsTo
    // Many to many: whereBelongsToManyEntity
    'category_relation_method' => 'whereBelongsTo',

    // The field name used for the post->category relationship in the JAM template
    'category_relation_field_name' => 'category',
];