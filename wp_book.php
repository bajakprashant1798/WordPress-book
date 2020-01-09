<?php
/**
 * Plugin Name: WordPress book plugin
 * Plugin URI: https://dailylearnconcept.wordpress.com 
 * Description: This is my first WordPress Book Plugin
 * Author: Prashant Bajak
 * Author URI: https://dailylearnconcept.wordpress.com 
 * Version: 1.0.0
 * License: GPLv2 or later
 * Text Domain: WPbook
 */

// defined('ABSPATH') or die('you cannot access this file.');

include('functions.php');
include('meta_box.php');
include('widget.php');
include('book_setting_page.php');
include('sort-code.php');
include('dashboard_widget.php');
include('wpbook_texonomies.php');

/**
 * Register wpbook1 Widget for book category.. 
 */
function WPbook_Register_widget()
{
    register_widget('Category_Posts');
}
//hook function
add_action('widgets_init', 'WPbook_Register_Widget');

/**
 * Dashboard widget
 */
function WPbook_Custom_Dashboard_widget()
{
    wp_add_dashboard_widget('custom_dashboard', 'Show categories of book post', 'WPbook_Custom_Dashboard_Widget_callback');
}
add_action('wp_dashboard_setup', 'WPbook_Custom_Dashboard_widget');

