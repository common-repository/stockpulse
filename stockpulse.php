<?php

/*
Plugin Name: StockPulse
Plugin URI: https://www.stockpulse.com/services/stockpulse-platform
Description: StockPulse WordPress Plugin for Stock Quotes, Charts and and Tickers
Version: 1.0.7
Author: StockPulse
Author URI: https://www.stockpulse.com
License: GPL2
 
StockPulse is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version.
 
StockPulse is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License along with StockPulse. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

// Block If Accessed Directly
if(!defined('ABSPATH')){
	exit;
}

// Define Plugin Variables
define("STOCKPULSE_VERSION", '1.0.7');
define("STOCKPULSE_DOMAIN", $_SERVER['HTTP_HOST']);

// create custom plugin settings menu
add_action('admin_menu', 'stockpulse');

// Register Bootstrap Styles
//wp_register_style('bootstrap-styles', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
wp_register_style('bootstrap-styles', plugins_url('/assets/css/bootstrap.v4.3.1.min.css', __FILE__));

// Register Font Awesome Styles
//wp_register_style('font-awesome-styles', 'https://use.fontawesome.com/releases/v5.8.1/css/all.css');
wp_register_style('font-awesome-styles', plugins_url('/assets/fontawesome-5.8.1/css/all.min.css', __FILE__));

// Register stockpulse Styles
wp_register_style('stockpulse-styles', plugins_url('/assets/css/stockpulse-styles.css', __FILE__));

// Register stockpulse widget Styles
wp_register_style('stockpulse-widgetstyles', plugins_url('/assets/css/stockpulse-widgetstyles.css', __FILE__));

// Register stockpulse Script
wp_register_script('stockpulse-script', plugins_url('/assets/js/stockpulse-script.js', __FILE__));

// Load stockpulse widget styles
wp_enqueue_style('stockpulse-widgetstyles');

// List Styles and Scripts to be loaded on Admin Page
function stockpulse_load_admin_assets($hook){
	if($hook === 'toplevel_page_stockpulse' || strpos($hook, 'stockpulse_page') !== false){
		// Load Bootstrap styles
		wp_enqueue_style('bootstrap-styles');

		// Load Font Awesome styles
		wp_enqueue_style('font-awesome-styles');

		// Load stockpulse styles
		wp_enqueue_style('stockpulse-styles');

		// Load jQuery
		wp_enqueue_script('jquery');

		// Load stockpulse script
		wp_enqueue_script('stockpulse-script');
	}
}
// Load Styles and Scripts on Admin Page
add_action('admin_enqueue_scripts', 'stockpulse_load_admin_assets');

function stockpulse(){
	//create new top-level menu
	add_menu_page('StockPulse Dashboard', 'StockPulse', 'administrator', '/stockpulse', 'stockpulse_dashboard', plugins_url('/assets/images/icon.png', __FILE__));
	
	//add submenu
	add_submenu_page('stockpulse', 'StockPulse Dashboard', 'Dashboard', 'administrator', '/stockpulse', 'stockpulse_dashboard');
	add_submenu_page('stockpulse', 'StockPulse Settings', 'Settings', 'administrator', '/stockpulsesettings', 'stockpulse_settings');
	add_submenu_page('stockpulse', 'StockPulse Help', 'Help', 'administrator', '/stockpulsehelp', 'stockpulse_help');
	add_submenu_page('stockpulse', 'StockPulse Upgrade', 'Upgrade', 'administrator', 'https://www.stockpulse.com/services');

	//call register settings function
	add_action('admin_init', 'init_stockpulse');
}

//register our settings and initiate plugin
function init_stockpulse(){
	register_setting('stockpulse-widget-settings', 'stockpulse_api_token');
	register_setting('stockpulse-widget-settings', 'stockpulse_api_key');
	register_setting('stockpulse-widget-settings', 'stockpulse_shortcodes');
	register_setting('stockpulse-widget-settings', 'stockpulse_error');
	
	check_stockpulse_registration();
}

// Include Widget Functions
include_once('stockpulse-widgets.php');

// Include Pages
include_once('stockpulse-pages.php');

// Activation Function
function stockpulse_activate(){
	// Cannot Redirect User Right after plugin is activated.
	//exit(wp_redirect(admin_url('/admin.php?page=stockpulse')));
}

// Plugin Activated
register_activation_hook( __FILE__, 'stockpulse_activate');

// Error Function
/*function my_error_notice(){
    ?>
    <div class="error notice is-dismissible">
        <p><?php _e("We were unable to apply your API Token and Key!", 'my_plugin_textdomain'); ?></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'my_error_notice' );*/

// Notification Function
/*function my_update_notice() {
    ?>
    <div class="updated notice is-dismissible">
        <p><?php _e("Your plugin is active!", 'my_plugin_textdomain'); ?></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'my_update_notice' );*/

function check_stockpulse_registration(){
	$stockpulse_api_token = esc_attr(get_option('stockpulse_api_token'));
	$stockpulse_api_key = esc_attr(get_option('stockpulse_api_key'));
	
	if(empty($stockpulse_api_token) || empty($stockpulse_api_key)){
		register_stockpulse_api(STOCKPULSE_DOMAIN);
	}
}

function register_stockpulse_api($utm_domain){
	$API_Body = array(
		'utm_domain' => STOCKPULSE_DOMAIN,
		'token' => esc_attr(get_option('stockpulse_api_token')),
		'key' => esc_attr(get_option('stockpulse_api_key')),
		'symbol' => $symbol
	);
	$API_Args = array(
		'body' => $API_Body,
		'timeout' => '5',
		'redirection' => '5',
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'cookies' => array()
	);

	$API_Response = wp_remote_retrieve_body(wp_remote_post('https://api.stockpulse.com/v1/register', $API_Args));

	// Decode json Responce
	$API_Response = json_decode($API_Response, true);

	if($API_Response['status'] === 'success'){
		if(isset($API_Response['newToken']) && !empty($API_Response['newToken']) && isset($API_Response['newKey']) && !empty($API_Response['newKey'])){
			update_option('stockpulse_api_token', $API_Response['newToken']);
			update_option('stockpulse_api_key', $API_Response['newKey']);
		}
	}
}

// Function to fire after API Key & Token Changes
function stockpulse_api_auth_update($old_value, $new_value){
	if($old_value != $new_value){
		update_option('stockpulse_error', '');
	}
}
add_action('update_option_stockpulse_api_token', 'stockpulse_api_auth_update', 10, 2);
add_action('update_option_stockpulse_api_key', 'stockpulse_api_auth_update', 10, 2);

?>