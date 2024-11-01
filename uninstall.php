<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
delete_option('stockpulse_api_key');
delete_option('stockpulse_api_token');
delete_option('stockpulse_shortcodes');
delete_option('stockpulse_error');

?>