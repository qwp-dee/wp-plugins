<?php
/*
Plugin Name: Html JavaScript Code Editor
Plugin URI: https://www.linkedin.com/in/shukladeepak1
Description: Sd Metabox-Repetor fields is a simple plugin which is help to you adding a jQuery add-more fileds concepts using wordpress pages.
Version: 1.0
Text Domain: sd
Author: Shukla Deepak
Author URI: https://www.linkedin.com/in/shukladeepak1
*/
if ( ! defined( 'ABSPATH' ) ) { return; }

if (!class_exists('CoderEditorHtmlJavaScript')) :
    class CoderEditorHtmlJavaScript {
        public function __construct(){
            // Add Admin enqueue scripts. 
            add_action('admin_enqueue_scripts', array(&$this,'add_page_scripts_enqueue_script'));
            // Register the metabox.
            add_action('add_meta_boxes', array(&$this,'add_page_scripts'));
            // Save meta box content.
            add_action('save_post',array(&$this,'page_scripts_save_meta_box'));
            // Put scripts in the head.
            add_action('wp_head', array(&$this,'page_scripts_add_head'));   
        }
            function add_page_scripts_enqueue_script( $hook ) {
                    global $post;
                    if ( ! $post ) { return; }
                    if ( ! 'page' === $post->post_type ) { return; }
                    if( 'post.php' === $hook || 'post-new.php' === $hook ) {
                        wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
                        wp_enqueue_script( 'js-code-editor', plugin_dir_url( __FILE__ ) . '/code-editor.js', array( 'jquery' ), '', true );
                    }
            } //end admin enqueue sripts.

            // Register the metabox.
            function add_page_scripts() {
            add_meta_box( 'page-scripts', __( 'Page Scripts & Styles', 'textdomain' ), array(&$this,'add_page_metabox_scripts_html'), 'page', 'advanced' );
            } //End Register the metabox

            // Meta box display callback.
            function add_page_metabox_scripts_html( $post ) {
                $post_id = $post->ID;
                $page_scripts = get_post_meta( $post_id, 'page_scripts', true );
                if ( ! $page_scripts ) {
                    $page_scripts = array(
                        'page_head' => '',
                        'js'        => '',
                        'css'       => '',
                    );
                }
                ?>
            <fieldset>
                <h3>Head Scripts</h3>
                <p class="description">Enter scripts and style with the tags such as <code>&lt;script&gt;</code></p>
                <textarea id="code_editor_page_head" rows="5" name="page_scripts[page_head]" class="widefat textarea"><?php echo wp_unslash( $page_scripts['page_head'] ); ?></textarea>   
            </fieldset>
            
            <fieldset>
                <h3>Only JavaScript</h3>
                <p class="description">Just write javascript.</p>
                <textarea id="code_editor_page_js" rows="5" name="page_scripts[js]" class="widefat textarea"><?php echo wp_unslash( $page_scripts['js'] ); ?></textarea>   
            </fieldset>

            <fieldset>
                <h3>Only CSS</h3>
                <p class="description">Do your CSS magic</p>
                <textarea id="code_editor_page_css" rows="5" name="page_scripts[css]" class="widefat textarea"><?php echo wp_unslash( $page_scripts['css'] ); ?></textarea>   
             </fieldset>
             <?php
             } //End Meta box display callback function. 

             // Save meta box content
             function page_scripts_save_meta_box( $post_id ) {
                if( defined( 'DOING_AJAX' ) ) {
                    return;
                }
                if( isset( $_POST['page_scripts'] ) ) {
                    $scripts = $_POST['page_scripts'];
                    update_post_meta( $post_id, 'page_scripts', $scripts );
                }
            } // End Save meta box content.

            // Put scripts in the head.
            function page_scripts_add_head() {
                $post_id = get_the_id();
                $page_scripts = get_post_meta( $post_id, 'page_scripts', true );
                if ( ! $page_scripts ) { return; }
                if ( isset( $page_scripts['page_head'] ) && '' !== $page_scripts['page_head'] ) {
                    echo wp_unslash( $page_scripts['page_head'] );
                }
                if ( isset( $page_scripts['js'] ) && '' !== $page_scripts['js'] ) {
                    echo '<script>' . wp_unslash( $page_scripts['js'] ) . '</script>';
                }
                if ( isset( $page_scripts['css'] ) && '' !== $page_scripts['css'] ) {
                    echo '<style>' . wp_unslash( $page_scripts['css'] ) . '</style>';
                }
            } //End Put scripts in the head.

    } //End CoderEditorHtmlJavaScript class.
    new CoderEditorHtmlJavaScript;
endif; ?>