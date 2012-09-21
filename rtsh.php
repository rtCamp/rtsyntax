<?php
/*
Plugin Name: rtSyntaxHighlighter
Plugin URI: http://rtcamp.com
Description: Google Code Prettifier Plugin for WordPress.
Version: 1.0
Author: rtCamp
Author URI: http://rtcamp.com
Contributors: Joshua Abenazer
*/

function rtsh_addbuttons() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_rtsh_tinymce_plugin");
     add_filter('mce_buttons', 'register_rtsh_button');
   }
}
 
function register_rtsh_button($buttons) {
   array_push($buttons, "separator", "rtsh", "rtcode", "rtkey", "code");
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_rtsh_tinymce_plugin($plugin_array) {
   $plugin_array['rtsh'] = plugin_dir_url(__FILE__).'js/rtsh-admin.js';
   $plugin_array['rtcode'] = plugin_dir_url(__FILE__).'js/rtsh-admin.js';
   $plugin_array['rtkey'] = plugin_dir_url(__FILE__).'js/rtsh-admin.js';
   return $plugin_array;
}
 
// init process for button control
add_action('init', 'rtsh_addbuttons');

function rtsh_scripts_and_styles() {
    if ( is_singular() ) {
        wp_enqueue_style( 'rtsh', plugin_dir_url(__FILE__) . 'css/rtsh.css');
        wp_enqueue_script( 'rtsh', plugin_dir_url(__FILE__) . 'js/rtsh.js', array( 'jquery' ) );
    }
}
add_action( 'wp_enqueue_scripts', 'rtsh_scripts_and_styles' );

function rtsh_onload() { ?>
    <script type="text/javascript">
        jQuery(document).ready( function(){
            jQuery('pre').each( function(){
                jQuery(this).wrap('<div class="prettyprint-code" />')
            });
        });
        window.onload = prettyPrint;
        function selectText(element) {
    var doc = document;
    var text = doc.getElementById(element);    

    if (doc.body.createTextRange) { // ms
        var range = doc.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    } else if (window.getSelection) { // moz, opera, webkit
        var selection = window.getSelection();            
        var range = doc.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    }
}
    </script><?php
}
add_action('wp_head','rtsh_onload');
?>