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

// Disable WooCommerce customer order emails based on product IDs and Category ids
add_filter('woocommerce_email_recipient_customer_processing_order', 'pwcemail_disable_customer_email_for_specific_products', 10, 2);

function pwcemail_disable_customer_email_for_specific_products($recipient, $order)
{

    $disabled_product_ids = array_map('trim', explode(',', get_option('pwcemail_product_ids')));
    $disabled_category_ids = array_map('trim', explode(',', get_option('pwcemail_category_ids')));

    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();

        // Disabled product ID
        if (in_array($product_id, $disabled_product_ids)) {
            $recipient = '';
            break;
        }

        // Disabled category ID
        $product = wc_get_product($product_id);
        $categories = $product->get_category_ids();
        if (array_intersect($categories, $disabled_category_ids)) {
            $recipient = '';
            break;
        }
    }

    return $recipient;
}

// Tabs for email notification setting
add_filter('woocommerce_product_data_tabs', 'pwcemail_email_notification_product_tab');
function pwcemail_email_notification_product_tab($tabs)
{
    $tabs['pwcemail_email_notification_tab'] = array(
        'label'    => __('Email Notification', 'pwcemail'),
        'target'   => 'email_notification_product_data',
        'class'    => array('show_if_simple', 'show_if_variable')
    );
    return $tabs;
}

add_action('woocommerce_product_data_panels', 'pwcemail_email_notification_product_data_fields');
function pwcemail_email_notification_product_data_fields()
{
?>
    <div id="email_notification_product_data" class="panel woocommerce_options_panel">
        <div class="options_group">
            <p><?php _e('Disable Default Email Notification Settings', 'pwcemail'); ?></p>
            <?php

            woocommerce_wp_checkbox(array(
                'id'          => 'pwcemail_disable_processing_order',
                'label'       => __('Processing Order', 'pwcemail'),
                'description' => __('Disable the default processing order email.', 'pwcemail'),
            ));

            woocommerce_wp_checkbox(array(
                'id'          => 'pwcemail_disable_completed_order',
                'label'       => __('Completed Order', 'pwcemail'),
                'description' => __('Disable the default completed order email.', 'pwcemail'),
            ));

            woocommerce_wp_checkbox(array(
                'id'          => 'pwcemail_disable_on_hold_order',
                'label'       => __('On Hold Order', 'pwcemail'),
                'description' => __('Disable the default on hold order email.', 'pwcemail'),
            ));

            woocommerce_wp_checkbox(array(
                'id'          => 'pwcemail_disable_cancelled_order',
                'label'       => __('Cancelled Order', 'pwcemail'),
                'description' => __('Disable the default cancelled order email.', 'pwcemail'),
            ));

            ?>
        </div>
    </div>
<?php
}

add_action('woocommerce_process_product_meta', 'pwcemail_save_email_notification');
function pwcemail_save_email_notification($post_id)
{
    $settings = array(
        'pwcemail_disable_processing_order',
        'pwcemail_disable_completed_order',
        'pwcemail_disable_on_hold_order',
        'pwcemail_disable_cancelled_order'
    );

    foreach ($settings as $setting) {
        $value = isset($_POST[$setting]) ? 'yes' : 'no';
        update_post_meta($post_id, $setting, $value);
    }
}
