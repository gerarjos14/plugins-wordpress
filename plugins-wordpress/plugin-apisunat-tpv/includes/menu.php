<?php 

function lars_pos_add_option_admin_menu(){
    add_menu_page(
        'LARS POS', 
        'LARS POS', 
        'manage_options', 
        'lars-pos', 
        'lars_pos_admin_menu_content', 
        'dashicons-rest-api', 
        15
    );
}
add_action('admin_menu', 'lars_pos_add_option_admin_menu');

function lars_pos_admin_menu_content(){
    require_once( plugin_dir_path(__FILE__) . 'main-view.php' );
}
