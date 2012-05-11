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
   return jQuerybuttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_rtprettifier_tinymce_plugin($plugin_array) {
   $plugin_array['rtprettifier'] = plugin_dir_url(__FILE__).'js/rtprettify-admin.js';
   return jQueryplugin_array;
}
 
// init process for button control
add_action('init', 'rtprettifier_addbuttons');

function rtprettifier_scripts_and_styles() {
    if ( is_singular() ) {
        wp_enqueue_style( 'rtprettify', plugin_dir_url(__FILE__) . 'css/rtprettify.css');
        wp_enqueue_script( 'rtprettify', plugin_dir_url(__FILE__) . 'js/rtprettify.js', array( 'jquery' ) );
        wp_enqueue_script( 'zclip', plugin_dir_url(__FILE__) . 'js/ZeroClipboard.js' );
    }
}
add_action( 'wp_enqueue_scripts', 'rtprettifier_scripts_and_styles' );

function rtprettifier_onload() { ?>
    <script type="text/javascript">
        ZeroClipboard.setMoviePath( '<?php echo plugin_dir_url(__FILE__); ?>js/ZeroClipboard.swf' );
        window.onload = prettyPrint;

    jQuery(document).ready(function() {
        jQuery('pre').each(function(){
            jQuery(this).before('<a href="#" class="copy">Copy</a>')
            jQuery(this).prev().click(function(e) {
            e.preventDefault();    
            clip = new ZeroClipboard.Client();
            var txt = jQuery(this).next().text();
            txt = txt.replace('\n', '\r\n');
            clip.setHandCursor(true);
            clip.setText(txt);
            clip.glue(this);
            return false;

        }); 
        jQuery('.copy').each(function(){
           jQuery(this).trigger('click'); 
        });
        });

    });
    function nl2br (str, is_xhtml) {   
var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}
    </script><?php
}
add_action('wp_head','rtprettifier_onload');
?>