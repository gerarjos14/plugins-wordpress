<?php 

function wab_add_option_admin_menu(){
    add_menu_page('Billing', 'Billing', 'manage_options', 'woocommerce-alegra-billing', 'wab_admin_menu_content', 'dashicons-text-page', 65);
}
add_action('admin_menu', 'wab_add_option_admin_menu');

function wab_admin_menu_content(){
    require_once( plugin_dir_path(__FILE__) . 'main-view.php' );
}
