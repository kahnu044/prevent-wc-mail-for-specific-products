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

add_action('admin_init', 'pwcemail_settings_init');
function pwcemail_settings_init()
{
    register_setting('pwcemail', 'pwcemail_product_ids');
    register_setting('pwcemail', 'pwcemail_category_ids');

    add_settings_section('pwcemail_section', 'Product/Category Settings', null, 'pwcemail');

    add_settings_field('pwcemail_product_ids', 'Product IDs (Comma Separated)', 'pwcemail_product_ids_render', 'pwcemail', 'pwcemail_section');
    add_settings_field('pwcemail_category_ids', 'Category IDs (Comma Separated)', 'pwcemail_category_ids_render', 'pwcemail', 'pwcemail_section');
}

function pwcemail_product_ids_render()
{
    $value = get_option('pwcemail_product_ids');
    echo '<input type="text" name="pwcemail_product_ids" value="' . esc_attr($value) . '" class="regular-text" />';
}

function pwcemail_category_ids_render()
{
    $value = get_option('pwcemail_category_ids');
    echo '<input type="text" name="pwcemail_category_ids" value="' . esc_attr($value) . '" class="regular-text" />';
}


function pwcemail_settings_page()
{
?>
    <form action="options.php" method="post">
        <h2>Prevent WooCommerce Email for Specific Products</h2>
        <?php
        settings_fields('pwcemail');
        do_settings_sections('pwcemail');
        submit_button();
        ?>
    </form>
<?php
}
