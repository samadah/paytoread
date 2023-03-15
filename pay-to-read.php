<?php
/*
 
Plugin Name: Pay to Read
Plugin URI: https://loopwi.com/monetization
Description: Create and manage paid content on your website.
Version: 2.0
Author: Loopwi
Author URI: https://loowi.com/
License: GPLv2 or later
Text Domain: loopwi_p2r
}
 
*/
//Declare
define( 'loopwi_p2r', 'Pay to Read' );


// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

//Load jQuery
function loopwi_p2r_load_jquery() {
    if ( ! wp_script_is( 'jquery', 'enqueued' )) {

        //Enqueue
        wp_enqueue_script( 'jquery' );

    }
}
add_action( 'wp_enqueue_scripts', 'loopwi_p2r_load_jquery' );

//Declare global
global $wpdb;
//
$table_name = $wpdb->prefix . "p2rplugins_settings";
$my_products_db_version = '1.0.0';
$charset_collate = $wpdb->get_charset_collate();

//first table for settings
if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name ) {

    $sql = "CREATE TABLE $table_name (
            ID int(11) NOT NULL AUTO_INCREMENT,
            `pid` text NOT NULL,
            `domain` text NOT NULL,
            `site_code` text NOT NULL,
            `button_label` text NOT NULL,
            `first_text` text NOT NULL,
            `second_text` text NOT NULL,
            `date` text NOT NULL,
            PRIMARY KEY  (ID)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    add_option('my_db_version', $my_products_db_version);
}

//Second is for the premium content
$table_name = $wpdb->prefix . "p2rplugins_posts";
$my_products_db_version = '1.0.0';
$charset_collate = $wpdb->get_charset_collate();
if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name ) {

    $sql = "CREATE TABLE $table_name (
            ID int(11) NOT NULL AUTO_INCREMENT,
            `pid` text NOT NULL,
            `domain` text NOT NULL,
            `title` text NOT NULL,
            `content` text NOT NULL,
            `featured_image` text NOT NULL,
            `date` text NOT NULL,
            PRIMARY KEY  (ID)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    add_option('my_db_version', $my_products_db_version);
}


//Install intial data
//Check if it exists first
global $wpdb; 
$results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}p2rplugins_settings WHERE pid = 1" );
if ( null == $results->pid ) { 

$pid = "1";
$domain = $_SERVER['SERVER_NAME'];
$button_label = "Unlock this Article";
$first_text = "You've landed on a premium article";
$second_text = "Our premium posts are our indepth look and quality research into certain topics, capable of providing you with solid information that could support your decision making in the area of concern.";
$date = date("D d, M, Y");

$table_name = $wpdb->prefix . 'p2rplugins_settings';

$wpdb->insert( 
	$table_name, 
	array(   
		'pid' => $pid, 
		'domain' => $domain, 
        'button_label' => $button_label,
        'first_text' => $first_text,
        'second_text' => $second_text,
		'date' => $date, 
	) 
);

}

//

//Enqueue styles
function loopwi_p2r_styles() {
    wp_enqueue_style( 'loopwi_p2r',  plugin_dir_url( __FILE__ ) . 'css/p2r.css');
    wp_enqueue_style( 'loopwi_p2r_fonts',  plugin_dir_url( __FILE__ ) . 'css/opensans-font.css');
    wp_enqueue_style( 'loopwi_p2r_fonts2',  plugin_dir_url( __FILE__ ) . 'fonts/line-awesome/css/line-awesome.min.css');        
    wp_enqueue_style( 'loopwi_p2r_styles',  plugin_dir_url( __FILE__ ) . 'css/style.css');
    wp_enqueue_style( 'loopwi_p2r_bootsrap',  plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css');
    wp_enqueue_style( 'loopwi_p2r_plugins',  plugin_dir_url( __FILE__ ) . 'css/plugins.css'); 
    wp_enqueue_style( 'loopwi_p2r_style_report',  plugin_dir_url( __FILE__ ) . 'css/style_report.css');             
}
add_action( 'wp_enqueue_scripts', 'loopwi_p2r_styles' );

//Enqueue Scripts
function loopwi_p2r_add_theme_scripts() { 
  wp_enqueue_script( 'script', get_template_directory_uri() . '/js/jquery.min.js', array ( 'jquery' ), '3.6.0', 'true');
  wp_enqueue_script( 'script', get_template_directory_uri() . '/js/popper.min.js', array ( 'popper' ), '2.9.3', 'true');
  wp_enqueue_script( 'script', get_template_directory_uri() . '/js/bootstrap.min.js', array ( 'bootstrap' ), '5.1.0', 'true');
}
add_action( 'wp_enqueue_scripts', 'loopwi_p2r_add_theme_scripts' );


//Create menu item  
add_action('admin_menu', 'loopwi_p2r_plugin_setup_menu'); 

function loopwi_p2r_plugin_setup_menu(){
    add_menu_page('Pay to Read Plugin', 'Pay to Read Plugin', 'manage_options', 'loopwi_p2r', 'loopwi_p2r_init', 'dashicons-awards', 27 );

    add_submenu_page('loopwi_p2r', 'Pay to Read Plugin Settings', 'Settings', 'manage_options', 'loopwi_p2r_settings', 'loopwi_p2r_settings_init' ); 
}

//Acton links
function loopwi_p2r_action_links( $links ) {

    $links = array_merge( array(
        '<a href="' . esc_url( admin_url( '/admin.php?page=p2r' ) ) . '">' . __( 'Reports', 'textdomain' ) . '</a>',
         '<a href="' . esc_url( admin_url( '/admin.php?page=p2r_settings' ) ) . '">' . __( 'Settings', 'textdomain' ) . '</a>',
          '<a href="' . esc_url( admin_url( '/admin.php?page=p2r' ) ) . '">' . __( 'Premium Posts', 'textdomain' ) . '</a>'
    ), $links );

    return $links;

}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'loopwi_p2r_action_links' );

//Register my Postype 
function loopwi_p2r_post_type_init() {
    // set up product labels
    $labels = array(
        'name' => 'Premium Posts',
        'singular_name' => 'Premium Posts',
        'add_new' => 'Add New Premium Post',
        'add_new_item' => 'Add New Premium Post',
        'edit_item' => 'Edit Post',
        'new_item' => 'New Premium Post',
        'all_items' => 'All Premium Posts',
        'view_item' => 'View Post',
        'search_items' => 'Search Premium Posts',
        'not_found' =>  'No Premium Posts Found',
        'not_found_in_trash' => 'No Premium Posts found in Trash', 
        'parent_item_colon' => '',
        'menu_name' => 'Premium Posts',
    );
    
    // register post type
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'publicly_queryable' => true,
        'rewrite' => array('slug' => 'p2r_premium_posts'),
        'query_var' => true,
        'menu_icon' => 'dashicons-lock',
        'menu_position' => '26',
        'supports' => array(
            'title',
            'editor', 
            'thumbnail',

        )
    ); 
    register_post_type( 'p2r_premium_posts', $args );
    
    // register taxonomy
    register_taxonomy('premium_posts_category', 'p2r_premium_posts', array('hierarchical' => true, 'label' => 'Premium Posts Category', 'query_var' => true, 'rewrite' => array( 'slug' => 'premium_posts-category' )));
}
add_action( 'init', 'loopwi_p2r_post_type_init');

//Add metaboxes
function loopwi_p2r_add_custom_box() {
    $screens = [ 'p2r_premium_posts', 'p2r_cpt' ];
    foreach ( $screens as $screen ) {
        add_meta_box(
            'p2r_box_id',                 // Unique ID
            'YOUR CHARGE FOR READERS OF READ THIS ARTICLE',      // Box title
            'loopwi_p2r_custom_box_html',  // Content callback, must be of type callable
            $screen                            // Post type
        );
    }
}
add_action( 'add_meta_boxes', 'loopwi_p2r_add_custom_box' );

//Meta boxes
include( plugin_dir_path( __FILE__ ). 'includes/metaboxes.php');

//Print main page 
function loopwi_p2r_init(){
    include( plugin_dir_path( __FILE__ ) . 'includes/dashboard.php');
}

//Print settings page
function loopwi_p2r_settings_init(){
    include( plugin_dir_path( __FILE__ ) . 'includes/settings.php');
}