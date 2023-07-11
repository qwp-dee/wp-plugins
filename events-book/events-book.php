<?php
/*
Plugin Name: Events book
Description: In this plugin having functionlity event post type and performing CRUD opration via REST API.
Version: 1.0
Author: Shukla Deepak
Text Domain: events-book
*/

/*Exit if accessed directly*/
if (!defined("ABSPATH")) {
    exit();
}

/*Adding some user define constrain for plugin directory path and url.*/
if (!defined("DIR_PATH")) {
    define("DIR_PATH", plugin_dir_path(__FILE__));
}
if (!defined("DIR_URL")) {
    define("DIR_URL", plugins_url() . "/events-book");
}

/*Register events post type and adding fileds Event title, Events descriptions and Events date and time.*/

if (!function_exists("event_post_type_fn")) {
    function event_post_type_fn(){
        $args = [
            "public" => true,
            "label" => __("Event", "events-crud-api"),
            'taxonomies'  => array( 'category' ),
            "supports" => ["custom-fields", "title", "editor", "thumbnail"],
        ];
        register_post_type("event", $args);
    }
    add_action("init", "event_post_type_fn");
}


/**
 *  GET routes API endpoint: ‘/events/show’ 
 *  http://localhost/deepak_Vlog/wp-json/v1/events/show
 */
add_action("init", function () {
    register_rest_route("v1", "/events/show", [
        "methods" => "GET",
        "callback" => "event_api_get_callback_fn",
    ]);
});

/**
 * GET routes API endpoint: Show by event id ‘/events/show?id=ID’.
 * http://localhost/deepak_Vlog/wp-json/v1/events/45
 */
add_action("init", function () {
    register_rest_route("v1", "/events/(?P<event_id>\d+)", [
        "methods" => "GET",
        "callback" => "event_api_get_callback_fn",
    ]);
});

/**
 * Get all events from our WordPress plugins Installation
 */
function event_api_get_callback_fn($request){

    $event_id = $request->get_param("event_id");
    if (empty($event_id)) {
        $posts = get_posts([
            "post_type" => "event",
            "post_status" => "publish",
        ]);

        if (count($posts) > 0) {
            $response["status"] = 200;
            $response["success"] = true;
            $response["data"] = $posts;
        } else {
            $response["status"] = 200;
            $response["success"] = false;
            $response["message"] = "No events..!";
        }
    } else {
        if ($event_id > 0) {
            $post = get_post($event_id);
            if (!empty($post)) {
                $response["status"] = 200;
                $response["success"] = true;
                $response["data"] = $post;
            } else {
                $response["status"] = 200;
                $response["success"] = false;
                $response["message"] = "No event found!";
            }
        }
    }

    wp_reset_postdata();
    return new WP_REST_Response($response);
}


/**
 *  POST API endpoint: ‘/events/create’. 
 * http://localhost/deepak_Vlog/wp-json/v1/events/create?title=frinedshipday&content=Lorem Ipsum is.&meta_start_date_time=24-12-23 at 04:00pm&meta_end_date_time=24-12-23 at 08:00pm
 */
add_action("init", function () {
    register_rest_route("v1", "/events/create/", [
        "methods" => "POST",
        "callback" => "event_api_post_callback_fn",
    ]);
});


/**
 * Create a event post by rest-api
 */
function event_api_post_callback_fn($request){

    $post["post_title"] = sanitize_text_field($request->get_param("title"));
    $post["post_content"] = sanitize_text_field($request->get_param("content"));
    $post["meta_input"] = [
        "start date-time" => sanitize_text_field(
            $request->get_param("meta_start_date_time")
        ),
        "end date-time" => sanitize_text_field(
            $request->get_param("meta_end_date_time")
        ),
    ];

    $post["post_status"] = "publish";
    $post["post_type"] = "event";
    $new_post_id = wp_insert_post($post);

    if (!is_wp_error($new_post_id)) {
        $response["status"] = 200;
        $response["success"] = true;
        $response["data"] = get_post($new_post_id);
    } else {
        $response["status"] = 200;
        $response["success"] = false;
        $response["message"] = "No event found!";
    }

    return new WP_REST_Response($response);
}

/**
 *  PUT API endpoint: ‘/events/update’. 
 * http://localhost/deepak_Vlog/wp-json/v1/events/update/43?title=Baal krishan&content=sunday this event&meta_start_date_time=24-07-2023 at 6:00pm&meta_end_date_time=24-07-2023 at 9:00pm
 */
add_action("init", function () {
    register_rest_route("v1", "/events/update/(?P<event_id>\d+)", [
        "methods" => "PUT",
        "callback" => "event_api_put_update_callback_fn",
    ]);
});

/**
 * Update a event post by rest-api
 */
function event_api_put_update_callback_fn($request){

    $event_id = $request->get_param("event_id");
    if ($event_id > 0) {
        $post["ID"] = $event_id;
        $post["post_title"] = sanitize_text_field($request->get_param("title"));
        $post["post_content"] = sanitize_text_field(
            $request->get_param("content")
        );
        $post["meta_input"] = [
            "start date-time" => sanitize_text_field(
                $request->get_param("meta_start_date_time")
            ),
            "end date-time" => sanitize_text_field(
                $request->get_param("meta_end_date_time")
            ),
        ];
        $post["post_status"] = "publish";
        $post["post_type"] = "event";
        $new_post_id = wp_update_post($post, true);

        if (!is_wp_error($new_post_id)) {
            $response["status"] = 200;
            $response["success"] = true;
            $response["data"] = $new_post_id;
        } else {
            $response["status"] = 200;
            $response["success"] = false;
            $response["message"] = "No events found!";
        }
    } else {
        $response["status"] = 200;
        $response["success"] = false;
        $response["message"] = "Event id is no set!";
    }
    return new WP_REST_Response($response);
}

/**
 *  DELETE API endpoint: ‘/events/delete’ 
 */
add_action("init", function () {
    register_rest_route("v1", "/events/delete/(?P<event_id>\d+)", [
        "methods" => "DELETE",
        "callback" => "event_api_delete_callback_fn",
    ]);
});


/**
 * Delete a event post by Rest-api
 */
function event_api_delete_callback_fn($request){

    $event_id = $request->get_param("event_id");
    if ($event_id > 0) {
        $deleted_post = wp_delete_post($event_id);
        if (!empty($deleted_post)) {
            $response["status"] = 200;
            $response["success"] = true;
            $response["data"] = $deleted_post;
        } else {
            $response["status"] = 200;
            $response["success"] = false;
            $response["message"] = "No event found!";
        }
    } else {
        $response["status"] = 200;
        $response["success"] = false;
        $response["message"] = "Event id is no set!";
    }
    return new WP_REST_Response($response);
}

/*Search event post by title and category*/

add_action( 'rest_api_init', function() {
  register_rest_route( 'v1', '/events_search', [
    'methods' => 'POST',
    'callback' => 'post_events_search_fn',
    'permission_callback' => '__return_true',
  ] );
} );

// Search Events by title and category name
function post_events_search_fn( $request ) {
  // Get sent data and set default value
      $params = wp_parse_args( $request->get_params(), [
        'title' => '',
        'category' => null
      ] );

      $args = [
        'post_type' => 'event',
        's' => $params['title'],
      ];
/*http://localhost/deepak_Vlog/wp-json/v1/events_search?title=frinedship&category=4*/
  if( $params['category'] ) {
    $args['tax_query'] = [[
      'taxonomy' => 'category',
      // 'field' => 'id',    It is take category_id peramiter like [1,4 ]
      /*This peramiter should be http://localhost/deepak_Vlog/wp-json/v1/events_search?title=frinedship&category=official  */
      'field'    => 'slug',    
      'terms' => $params['category']
    ]];

    if (!empty(get_posts( $args ))) {
            $response["status"] = 200;
            $response["success"] = true;
            $response["data"] = get_posts( $args );
        
    }else{
           $response["status"] = 200;
           $response["success"] = false;
           $response["message"] = "No event found!";
    }
  
  }else{
        $response["status"] = 200;
        $response["success"] = true;
        $response["message"] = get_posts( $args );
  }
    return new WP_REST_Response($response);

}
