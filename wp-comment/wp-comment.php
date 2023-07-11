<?php
/**
 * Plugin Name: WP Comment
 * Description: Wordpress comment section display title, taxonomy fields. user can fill thoses fields and admin can manage title and category fields from admin side. In this plugin having functionlity to additional comment metadata fields and saved admin side.
 * Version: 1.0.0
 * Author: Shukla Deepak
 */

if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Removed defaults fields from comments form.
 */
if(!function_exists('unset_url_field_fn')) : 
    function unset_url_field_fn($fields){

        $defaults["comment_notes_before"] = "";
        if (isset($fields["url"])) unset($fields["url"]);
        if (isset($fields["cookies"])) unset($fields["cookies"]);
        return $fields;

    }
    add_filter("comment_form_default_fields", "unset_url_field_fn");
endif;

/**
 * Adding additional ( title and category/taxonomy ) fields inside comment form.
 */
add_action( 'comment_form_logged_in_after', 'comment_form_additional_fields_fn' );
add_action( 'comment_form_after_fields', 'comment_form_additional_fields_fn' );
if(!function_exists('comment_form_additional_fields_fn')) : 
    function comment_form_additional_fields_fn(){
        ?>
        <p> <label for="comment_category"><?php _e( 'Category' ); ?>  </label>
            <?php  $categories = get_categories( array('orderby' => 'name', 'order'   => 'ASC') );
            foreach( $categories as $category ) {   ?>
            <span><?php echo $category->name ; ?></span>
            <input type="radio"  name="comment_category" value="<?php echo esc_attr( $category->name ); ?>" class="widefat" />
        <?php } ?>
        </p>
        <p class="comment-form-title">
            <label for="comment_title"><?php _e( 'Title' ); ?></label>
            <input type="text" name="comment_title" id="comment_title" />
        </p>
      
        <?php
    }
endif;

/**
 * Save comments metadata.
 */
if(!function_exists('save_comment_meta_data_fn')) :
    function save_comment_meta_data_fn($comment_id){
        add_comment_meta($comment_id, "comment_title", $_POST["comment_title"]);
        add_comment_meta( $comment_id,"comment_category", $_POST["comment_category"] );
    }
    add_action("comment_post", "save_comment_meta_data_fn");
endif; 

/**
 * comment/mesaage fileds moved bottom of form.
 */
if(!function_exists('move_comment_field_to_bottom_fn')) :
    function move_comment_field_to_bottom_fn( $fields ) {
        $comment_field = $fields['comment'];
        unset( $fields['comment'] );
        $fields['comment'] = $comment_field;
        return $fields;
    }
    add_filter( 'comment_form_fields', 'move_comment_field_to_bottom_fn');
endif;

/**
 * Add the title and category to our admin area, for editing, etc
 */
if(!function_exists('comment_add_meta_box_fn')) :
    function comment_add_meta_box_fn(){
        add_meta_box( 'additional', __( 'Additional fields' ), 'comment_meta_box_fn', 'comment', 'normal', 'high' );
    }
    add_action( 'add_meta_boxes_comment', 'comment_add_meta_box_fn' );
endif;

/**
 * Comment metabox callback function.
 */
if(!function_exists('comment_meta_box_fn')) :
    function comment_meta_box_fn( $comment ){
        $title = get_comment_meta( $comment->comment_ID, 'comment_title', true );
        $cate = get_comment_meta( $comment->comment_ID, 'comment_category', true );

        wp_nonce_field( 'comment_update', 'comment_update', false );
        ?>
       
         <p>
            <label for="comment_category"><?php _e( ' Category: ' ); ?> </label>&nbsp;&nbsp;
            <?php  
            $categories = get_categories( array('orderby' => 'name', 'order'   => 'ASC') );
                foreach( $categories as $category ) :  
                    if ( $cate == $category->name ) : ?>
                 <span><?php echo $cate; ?>:</span>&nbsp;<input type="radio" checked="checked" name="comment_category" value="<?php echo esc_attr( $category->name ); ?>" />
                    <?php else: ?>
                    <span><?php echo esc_attr( $category->name ); ?>:</span>&nbsp;<input type="radio" name="comment_category" value="<?php echo esc_attr( $category->name ); ?>"/>
                    <?php endif; 
                endforeach; ?>
        </p>
         <p>
            <label for="comment_title"><?php _e( 'Title' ); ?></label>
            <input type="text" name="comment_title" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
        </p>
        <?php
    }
endif;

/**
 * Save our comment (from the admin area)
 */
add_action( 'edit_comment', 'comment_edit_comment_fn' );
function comment_edit_comment_fn( $comment_id ){
    if( ! isset( $_POST['comment_update'] ) || ! wp_verify_nonce( $_POST['comment_update'], 'comment_update' ) ) return;

    if( isset( $_POST['comment_title'] ) )
        update_comment_meta( $comment_id, 'comment_title', esc_attr( $_POST['comment_title'] ) );

    if( isset( $_POST['comment_category'] ) )
        update_comment_meta( $comment_id, 'comment_category', esc_attr( $_POST['comment_category'] ) );
}

/**
 * Save our title (from the front end)
 */
add_action( 'comment_post', 'comment_insert_comment_fn', 10, 1 );
function comment_insert_comment_fn( $comment_id ){
    if( isset( $_POST['comment_title'] ) )
        update_comment_meta( $comment_id, 'comment_title', esc_attr( $_POST['comment_title'] ) );

    if( isset( $_POST['comment_category'] ) )
        update_comment_meta( $comment_id, 'comment_category', esc_attr( $_POST['comment_category'] ) );
}

/**
 * Display title and category front-end site
 */
add_filter( 'comment_text', 'comment_add_title_to_text_fn', 99, 2 );
function comment_add_title_to_text_fn( $text, $comment ){
    if( is_admin() ) return $text;
    if( $title = get_comment_meta( $comment->comment_ID, 'comment_title', true ) ) {
        $title = '<h4 class="comment_title">' . esc_attr( $title ) . '</h3>';
        $text = $title . $text;
    }
     if( $category = get_comment_meta( $comment->comment_ID, 'comment_category', true ) ){
        $category = '<strong>Category : <i>' . esc_attr( $category ) . '</i></strong>';
        $text = $category . $text;

    }
    return $text;
}