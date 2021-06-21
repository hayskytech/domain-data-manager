<?php
/*
Plugin Name: #Domain Data Manager
Plugin URI: https://www.haysky.com/
Description: This plugin code is generated by Haysky Code Generator.
Version: 0.2
Author: Sufyan
Author URI: https://www.sufyan.in/
License: GPLv2 or later
Text Domain: sufyan
*/
// "<footer" is searched in website to find whether it is designed or not
// https://www.enclout.com/api/v1/whois
// $wpdb->show_errors(); $wpdb->print_error();
function num_1596819241_admin_menu(){
    add_menu_page('Domain List','Domain List','manage_options','num_admin','num_wir','dashicons-admin-users','2');
    add_submenu_page('num_admin', 'Filter','Filter','manage_options','filter_wir','filter_wir');
    add_submenu_page('num_admin', 'Message','Message','manage_options','message_wal','message_wal');
}
add_action('admin_menu' , 'num_1596819241_admin_menu');

function message_wal(){ include 'message.php'; }
function num_wir(){ include 'domain_list.php'; }
function filter_wir(){ include 'filter.php'; }


add_action( "init", function(){
    if ( is_admin() ) {
        if( !class_exists( "Smashing_Updater" ) ){
            $updater_file = dirname( __FILE__ ) . "/github_updater.php";
            if (!file_exists($updater_file)) {
                $src = file_get_contents( "https://raw.githubusercontent.com/rayman813/smashing-updater-plugin/master/updater.php" );
                file_put_contents($updater_file, $src);
            }
            include $updater_file;
        }
        $updater = new Smashing_Updater( __FILE__ );
        $updater->set_username( "hayskytech" );
        $updater->set_repository( "domain-data-manager" );
        // $updater->authorize( "abcdefghijk1234567890" ); // Your auth code goes here for private repos
        $updater->initialize();
    }
});
?>