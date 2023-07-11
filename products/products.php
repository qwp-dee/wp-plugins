<?php
/**
 * Plugin Name: Products
 * Description: Products module crud opratoion.
 * Version: 1.0.0
 * Author: Shukla Deepak
 */


/*
 * Define plugin directory and plugin path.
 */

/* Define plugin absolute path.*/
if ( !defined( 'ABSPATH' ) ) exit; 

/* Define plugin directory path.*/
if (!defined("PLUGIN_DIR"))
    define("PLUGIN_DIR", plugin_dir_path(__FILE__));

/* Define plugin URL.*/
if (!defined("PLUGIN_URL"))
    define("PLUGIN_URL", plugins_url() . "/products");

/*
 * Define plugin assets scripts and stylesheet.
 */
if(!function_exists('products_assets_fn')) :

    function products_assets_fn() {

        $slug = '';
        $pages_includes = array("frontendpage","products","product-list","add-product", "edit-product", "add-category");
        $currentPage = isset($_GET['page']) ? $_GET['page'] : null; 
        if(empty($currentPage)){
            $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                if (preg_match("/product_page/", $actual_link)) {
                    $currentPage = "frontendpage";
                }
        }

        if(in_array($currentPage,$pages_includes)){
            wp_enqueue_style("bootstrap", "https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css");
            wp_enqueue_style("font-awesome", "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css");
            wp_enqueue_style("datatable", "//cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css");
            
            //scripts
            wp_enqueue_script('jquery');
            wp_enqueue_script('bootstrap.min', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js', '', true);
            wp_enqueue_script('datatable.min', '//cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js', '', true);
            wp_enqueue_script('plugin-script', PLUGIN_URL . '/assets/script.js', array('jquery'), true);
        }  
    }
    add_action("init", "products_assets_fn");
endif;

/*
 * Define wp_enqueue_media() admin script.
 */
if(!function_exists('enqueue_media_fn')) :
    function enqueue_media_fn() { 
        if ( ! did_action( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            }
        }
    add_action('admin_enqueue_scripts', 'enqueue_media_fn');
endif;

/*
 * Define plugin activation hooks for creating product, category tables and product-page for frontend site.
 */

register_activation_hook( __FILE__, "activate_plugin_fn" );

/*
 * Define plugin de-activation hooks for drop product, category tables and product-page for frontend site.
 */
register_deactivation_hook( __FILE__, "deactivate_plugin_fn" );

/*Activate Plugin*/
if(!function_exists('activate_plugin_fn')) :
    function activate_plugin_fn() {
    	init_db_plugin();
        add_option('plugin_do_activation_redirect', true);
    }
    add_action( 'admin_init', 'plugin_activate_redirect_fn' );
endif;

/*After activate plugin's page redirect to products page. */

if(!function_exists('plugin_activate_redirect_fn')) :
    function plugin_activate_redirect_fn(){
        if (get_option('plugin_do_activation_redirect', false)) {
            delete_option('plugin_do_activation_redirect');
            if(!isset($_GET['activate-multi'])){
                wp_redirect("admin.php?page=products");
            }
        }
    }
endif;

/*De-activate Plugin*/
if(!function_exists('deactivate_plugin_fn')) :

    function deactivate_plugin_fn() {
        // Delete product and category tables.
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS " . product_table());
        $wpdb->query("DROP TABLE IF EXISTS " . product_category_table());

        //Delete product page.
        if(!empty(get_option("product_page_id"))){
            $page_id = get_option("product_page_id");
            wp_delete_post($page_id, true); //wp_posts
            delete_option("product_page_id"); // wp_options
        }
    }
endif;

/* wordpress product table */
function product_table() {
    global $wpdb;
    return $wpdb->prefix . "products"; //wp_products
}

/* wordpress category table */
function product_category_table() {
    global $wpdb;
    return $wpdb->prefix . "products_category"; //wp_products_category
}

/* Initialize DB Products and Category Tables */
if(!function_exists('init_db_plugin')):

    function init_db_plugin() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Creating products table.
        $product_sql = "  CREATE TABLE `" . product_table() . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `product_title` varchar(255) DEFAULT NULL,
                `product_description` varchar(500) DEFAULT NULL,
                `product_price` int(10) DEFAULT NULL,
                `product_category` varchar(255) DEFAULT NULL,
                `product_quantity` int(10) DEFAULT NULL,
                `product_sku` varchar(15) DEFAULT NULL,
                `product_image`varchar(500) DEFAULT NULL,
                PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
        dbDelta($product_sql);

        // Creating category table.    
        $category_sql = "  CREATE TABLE `" . product_category_table() . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `category_name` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=latin1";

        dbDelta($category_sql);    

        /*Dynamic page creation products-listing of created product page for frontend.*/
        $product_page = array(
            'post_title'    => "Product Page",
            'post_content'  => "[product_page]", //shortcode
            'post_status'   => 'publish',
            "post_type" =>"page",
            "post_name" => "product_page"
        );
            
        // Insert the post into the database
        $product_page = wp_insert_post( $product_page );
        add_option("product_page_id",$product_page);

    }
endif;

/*Products admin menu.*/
if(!function_exists('products_admin_menu_options_fn')):
    function products_admin_menu_options_fn(){
      add_menu_page('Products', 'Products', 'manage_options', 'products', 'product_manage_fn');
      add_submenu_page( 'products', 'Add product', 'Add product', 'manage_options', 'add-product', 'add_new_product_fn');
      add_submenu_page( 'products', 'Edit product', 'Edit product', 'manage_options', 'edit-product', 'edite_product_fn');
      add_submenu_page( 'products', 'Add category', 'Add category', 'manage_options', 'add-category', 'add_new_category_fn');
    }
    add_action('admin_menu', 'products_admin_menu_options_fn');
endif;

/*Product Listing.*/
if (!function_exists('product_manage_fn')) :
    // Product listing and product-delete modules
    function product_manage_fn(){
        include_once PLUGIN_DIR. '/INC/products-list.php';
    }
endif;

/*Add New Product.*/
if (!function_exists('add_new_product_fn')) :
    function add_new_product_fn(){
        include_once PLUGIN_DIR. '/INC/add-product.php';
    }
endif;

/*Edit/Update Product.*/
if (!function_exists('edite_product_fn')) :
    function edite_product_fn(){
        include_once PLUGIN_DIR. '/INC/edit-product.php';
    }
endif;

/*Add New Category.*/
if (!function_exists('add_new_category_fn')) :
    function add_new_category_fn(){
        include_once PLUGIN_DIR. '/INC/add-category.php';
    }
endif;

/* shortcode for product page frontend side*/
function product_page_fn(){
   ob_start();
   include_once PLUGIN_DIR. '/INC/template-products.php';
   return ob_get_clean();
}
add_shortcode("product_page","product_page_fn");

