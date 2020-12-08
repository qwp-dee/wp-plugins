<?php
/*
Plugin Name: Sd Metabox-Repetor Fields
Plugin URI: https://profiles.wordpress.org/iamshukla/
Description: Sd Metabox-Repetor fields is a simple plugin which is help to you adding a jQuery add-more fileds concepts using wordpress pages.
Version: 1.0
Text Domain: sd
Author: Shukla Deepak
Author URI: https://www.linkedin.com/in/shukladeepak1
*/

define( 'SD_PLUGIN_URL', plugin_dir_url( __FILE__) );
define( 'SD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SD_PLUGIN_VERSION', '1.0' );


if ( ! defined( 'ABSPATH' ) ) { return; }
// Adding Custom meta box
if (!function_exists('sd_codes_init_fn')) :
	function sd_codes_init_fn() {
	add_meta_box('dynamically_add_remove', 'Dynamically Add/Remove', 'sd_dynamically_metabox_fn', 'page', 'normal', 'low');
	}
endif;
add_action( 'admin_init', 'sd_codes_init_fn' );

// Metabox callback function.
if (!function_exists('sd_dynamically_metabox_fn')) :
	function sd_dynamically_metabox_fn (){
	 global $post;
	 $metatext =   get_post_meta($post->ID, 'metatext', true); ?>
		<div class="input_fields_wrap">
 		   <a class="add_field_button button-secondary">Add Field</a>
  	  <?php
   		 if(isset($metatext) && is_array($metatext)) {
     	   $i = 1;
      		  $output = '';
        	foreach($metatext as $text){
            //echo $text;
            $output = '<div><input type="text" name="metatext[]" value="' . $text . '">';
            if( $i !== 1 && $i > 1 ) $output .= '<a href="#" class="remove_field">Remove</a>';
            else $output .= '</div>';
   
            echo $output;
            $i++;
        }
      } else {
        echo '<div><input type="text" name="metatext[]"></div>';
    }
    ?>
</div>
    <?php
}
endif;
// Save and update metabox value
if (!function_exists('save_post_metabox')) :	
		function save_post_metabox($post_id) {
	// Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;
    // now we can actually save the data
    $allowed = array(
        'a' => array( // on allow a tags
            'href' => array() // and those anchors can only have href attribute
        )
    );
    // If any value present in input field, then update the post meta
    if(isset($_POST['metatext'])) {
        // $post_id, $meta_key, $meta_value
        update_post_meta( $post_id, 'metatext', $_POST['metatext'] );
    }
}
endif;
add_action('save_post', 'save_post_metabox');

// Adding filed dynemiclly
if (!function_exists('admin_footer_script')) :
		function admin_footer_script() {
   		 global $post;
   		 $metatext =   get_post_meta($post->ID, 'metatext', true);
  	     $x = 1;
  		  if(is_array($metatext)) {
   		     $x = 0;
          		foreach($metatext as $text){
            	 $x++;
         		}
   		 }
    if('page' == $post->post_type ) {
       echo ' <script type="text/javascript">
					jQuery(document).ready(function($) {
					    // Dynamic input fields ( Add / Remove input fields )
					    var max_fields      = 5; //maximum input boxes allowed
					    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
					    var add_button      = $(".add_field_button"); //Add button ID
					    
						    var x = '.$x.'; //initlal text box count
						    $(add_button).click(function(e){ //on add input button click
						        e.preventDefault();
						        if(x < max_fields){ //max input box allowed
						            x++; //text box increment
						            $(wrapper).append(\'<div><input type="text" name="metatext[]"/><a href="#" class="remove_field">Remove</a></div>\');
						        }
						    });
			    
			    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
			        e.preventDefault(); $(this).parent(\'div\').remove(); x--;
			    })
			});

			</script>
         ';
    }
}
endif;
add_action('admin_footer', 'admin_footer_script');




