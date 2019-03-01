<?php

/**
 * Plugin Name: WLI API
 * Description: Light weight api for wli theme
 * Author: Abolfazl Sabagh
 * Version: 0.1
 */
add_action('rest_api_init', 'wli_register_routes');

if (!function_exists('wli_register_routes')) {

    function wli_register_routes() {
        register_rest_route(
                'wli/v1', '/posts', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'wli_api_return_latest_post',
            'args' => [
                'per_page' => [
                    'required' => false,
                    'type' => 'integer',
                    'default' => 5,
                    'description' => 'posts per page'
                ],
                'page' => [
                    'required' => false,
                    'type' => 'integer',
                    'default' => 1,
                    'description' => 'paged argument',
                    'validate_callback' => function($param) {
                        if ($param > 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ]
                ]
        );
        register_rest_route(
                'wli/v1', '/search', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'wli_api_return_search_posts',
            'args' => [
                's' => [
                    'required' => true,
                    'default' => 5,
                    'description' => 'search expression'
                ]
            ]
                ]
        );
        register_rest_route(
                'wli/v1', '/posts/cat', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'wli_api_return_posts_cat',
            'args' => [
                'count' => [
                    'required' => false,
                    'type' => 'integer',
                    'default' => 5,
                    'description' => 'posts per page'
                ],
                'id' => [
                    'required' => true,
                    'type' => 'integer',
                    'description' => 'category id'
                ],
                'page' => [
                    'required' => false,
                    'type' => 'integer',
                    'description' => 'paged argument',
                    'validate_callback' => function($param) {
                        if ($param > 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ]
                ]
        );
        register_rest_route(
                'wli/v1', '/full-posts', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'wli_api_return_full_posts',
            'args' => [
                'count' => [
                    'required' => false,
                    'type' => 'integer',
                    'default' => 5,
                    'description' => 'posts per page'
                ],
                'page' => [
                    'required' => false,
                    'type' => 'integer',
                    'default' => 1,
                    'description' => 'paged argument',
                    'validate_callback' => function($param) {
                        if ($param > 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                ]
            ]
                ]
        );
    }

}

/*
|--------------------------------------------------------------------------
| wli_api_return_latest_post
|--------------------------------------------------------------------------
|
| return latest 5 posts
| sample route : EXAMPLE/wli/v1/posts?per_page=%id%&page=%page%
|
*/

if (!function_exists('wli_api_return_latest_post')) {

    function wli_api_return_latest_post($request) {
        $posts_args = array(
            'post_type' => 'post',
            'posts_per_page' => $request->get_param('per_page'),
            'paged' => $request->get_param('page')
        );

        $posts = get_posts($posts_args);

        if (empty($posts)) {
            return null;
        }
        $post_array = array();
        foreach ($posts as $post) {
            if (has_post_thumbnail($post->ID)) {
                $post_thumbnail_id = get_post_thumbnail_id($post->ID);
                $thumbnail_url = current(wp_get_attachment_image_src($post_thumbnail_id, 'medium', FALSE));
            } else {
                $thumbnail_url = "#";
            }
            $post_array[] = array(
                'ID' => $post->ID,
                'title' => $post->post_title,
                'thumbnail' => $thumbnail_url
            );
        }
        return $post_array;
    }

}

/*
|--------------------------------------------------------------------------
| wli_api_return_search_posts
|--------------------------------------------------------------------------
|
| return searched posts
| sample route : EXAMPLE/wli/v1/search?s=%string%
|
*/


if (!function_exists('wli_api_return_search_posts')) {

    function wli_api_return_search_posts($request) {
        $posts = get_posts(array(
            'post_type' => 'post',
            's' => $request->get_param('s')
        ));

        if (empty($posts)) {
            return null;
        }
        $post_array = array();
        foreach ($posts as $post) {
            if (has_post_thumbnail($post->ID)) {
                $post_thumbnail_id = get_post_thumbnail_id($post->ID);
                $thumbnail_url = current(wp_get_attachment_image_src($post_thumbnail_id, 'medium', FALSE));
            } else {
                $thumbnail_url = "#";
            }
            $post_array[] = array(
                'ID' => $post->ID,
                'title' => $post->post_title,
                'thumbnail' => $thumbnail_url
            );
        }
        return $post_array;
    }

}


/*
|--------------------------------------------------------------------------
| wli_api_return_posts_cat
|--------------------------------------------------------------------------
|
| return posts base on category
| sample route : EXAMPLE/wli/v1/posts/cat?id=%id%&page=%page%&count=%count%
|
*/


if (!function_exists('wli_api_return_posts_cat')) {

    function wli_api_return_posts_cat($request) {
        $cat_id = $request->get_param('id');
        $paged = $request->get_param('page');
        $per_page = $request->get_param('count');
        $posts = get_posts(array(
            'post_type' => 'post',
            'cat' => $cat_id,
            'posts_per_page' => $per_page,
            'paged' => $paged
        ));

        if (empty($posts)) {
            return null;
        }
        $post_array = array();
        foreach ($posts as $post) {
            if (has_post_thumbnail($post->ID)) {
                $post_thumbnail_id = get_post_thumbnail_id($post->ID);
                $thumbnail_url = current(wp_get_attachment_image_src($post_thumbnail_id, 'medium', FALSE));
            } else {
                $thumbnail_url = "#";
            }
            $post_array[] = array(
                'ID' => $post->ID,
                'title' => $post->post_title,
                'thumbnail' => $thumbnail_url
            );
        }
        return $post_array;
    }

}


/*
|--------------------------------------------------------------------------
| wli_api_return_full_posts
|--------------------------------------------------------------------------
|
| return posts base on category
| sample route : EXAMPLE/wli/v1/full-posts?page=%page%&count=%count%
|
*/

if (!function_exists('wli_api_return_full_posts')) {

    function wli_api_return_full_posts($request) {
        $paged = $request->get_param('page');
        $per_page = $request->get_param('count');
        $posts = get_posts(array(
            'post_type' => 'post',
            'posts_per_page' => $per_page,
            'paged' => $paged
        ));

        if (empty($posts)) {
            return array('message' => 'no post');
        }

        $post_array = array();
        foreach ($posts as $post) {
            if (has_post_thumbnail($post->ID)) {
                $post_thumbnail_id = get_post_thumbnail_id($post->ID);
                $thumbnail_url = current(wp_get_attachment_image_src($post_thumbnail_id, 'medium', FALSE));
            } else {
                $thumbnail_url = "#";
            }
            $categories = get_the_category($post->ID);
            foreach ($categories as $category) {
                $categories_array[] = array(
                    'id' => $category->term_id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'parent' => $category->parent,
                    'posts_count' => $category->count
                );
            }
            $post_array[] = array(
                'ID' => $post->ID,
                'type' => $post->post_type,
                'title' => $post->post_title,
                'title_plain' => $post->post_title,
                'slug' => $post->post_name,
                'url' => get_the_permalink($post->ID),
                'status' => $post->status,
                'date' => get_the_date('', $post->ID),
                'modified' => get_the_modified_date('', $post->ID),
                'author' => get_user_by("id", $post->post_author)->data,
                'thumbnail' => $thumbnail_url,
                'categories' => $categories_array,
                'comment_number' => get_comments_number($post->ID),
                'comment_status' => (comments_open($post->ID)) ? "open" : "close",
                'content' => '#',
                'excerpt' => '#',
                'comments' => array(),
                'attachments' => array()
            );
        }

        return $post_array;
    }

}
