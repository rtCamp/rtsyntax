<?php
/*
Plugin Name: rtPrettifier
Plugin URI: http://rtcamp.com
Description: Google Code Prettifier Plugin for WordPress.
Version: 1.0
Author: rtCamp
Author URI: http://rtcamp.com
Contributors: Joshua Abenazer
*/

function rtprettifier_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_rtprettifier_tinymce_plugin");
     add_filter('mce_buttons', 'register_rtprettifier_button');
   }
}
 
function register_rtprettifier_button($buttons) {
   array_push($buttons, "separator", "rtprettifier");
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_rtprettifier_tinymce_plugin($plugin_array) {
   $plugin_array['rtprettifier'] = plugin_dir_url(__FILE__).'js/rtprettify-admin.js';
   return $plugin_array;
}
 
// init process for button control
add_action('init', 'rtprettifier_addbuttons');

function rtprettifier_scripts_and_styles() {
    if ( is_singular() ) {
        wp_enqueue_style( 'rtprettify', plugin_dir_url(__FILE__) . 'css/rtprettify.css');
        wp_enqueue_script( 'rtprettify', plugin_dir_url(__FILE__) . 'js/rtprettify.js', array( 'jquery' ) );
    }
}
add_action( 'wp_enqueue_scripts', 'rtprettifier_scripts_and_styles' );

function rtprettifier_onload() { ?>
    <script type="text/javascript">
        window.onload = prettyPrint;
        jQuery('pre').each( function(){
            alert('hi');
            $regexp = '/lang-(.*)( |")/'; // Change the regex here suiting your phone number format

//if( preg_match( $regexp, jQuery(this).attr('class') ) ) {
    console.log(preg_match( $regexp, jQuery(this).attr('class') ));
//}
            jQuery(this).append('<span class="lang-type"></span>');
        } );
    </script><?php
}
add_action('wp_head','rtprettifier_onload');
?>