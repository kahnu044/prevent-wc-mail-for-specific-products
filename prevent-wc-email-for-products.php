<?php

/**
 * Plugin Name: Prevent WooCommerce Email for Specific Products
 * Description: Prevent WooCommerce email notifications for specific products or categories.
 * Version: 1.0.0
 * Author: kahnu044
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

// Add an admin menu to configure the plugin settings
add_action('admin_menu', 'pwcemail_admin_menu');
function pwcemail_admin_menu()
{
    add_menu_page('Prevent WC Email Settings', 'Prevent WC Email', 'manage_options', 'prevent-wc-email-settings', 'pwcemail_settings_page', 'dashicons-email-alt');
}


function pwcemail_settings_page()
{
    echo "Prevent WC Email Settings Page";
}