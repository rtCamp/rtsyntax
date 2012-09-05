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
        jQuery(document).ready( function(){
            jQuery('pre').each( function(){
                jQuery(this).wrap('<div class="prettyprint-code" />')
                jQuery(this).before('<a href="#" class="copy-source">Raw</a>')
            });
            jQuery('.copy-source').each(function(){
                jQuery(this).after('<pre class="plain-code" style="display:none;">'+jQuery(this).parent().find('pre').html()+'</pre>'); 
            });
            jQuery('.copy-source').live( 'click', function(){
                jQuery(window.open().document.body).html('<pre>'+jQuery(this).parent().find('pre').html()+'</pre>').selectText('pre');
                return false;
            });
        });
        window.onload = prettyPrint
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
add_action('wp_head','rtprettifier_onload');
?>