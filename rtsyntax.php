<?php
/*
Plugin Name: rtSyntax
Plugin URI: http://rtcamp.com
Author: rtCamp
Author URI: http://rtcamp.com
Version: 1.0.5
Description: A no-fuss, lightweight, fast and optimised syntax highlighter for WordPress
Contributors: rtcamp, rahul286, JoshuaAbenazer
Tags: code highlighter, highlighter, highlighting, syntax, syntax highlighter, source, jquery, javascript, nginx, php, code, CSS, html, php, sourcecode, xhtml, languages, TinyMCE
*/

class rtSyntax {

    public function __construct() {
        register_activation_hook( __FILE__, array( $this, 'initialize_option' ) );
        if ( is_admin() ) {
            add_action( 'admin_init', array( &$this, 'register_settings' ) );
            add_action( 'admin_menu', array( &$this, 'admin' ) );

            add_filter( 'mce_external_plugins', array( $this, 'rtsyntax_buttons' ) );
            add_filter( 'mce_buttons', array( $this, 'register_rtsyntax_buttons' ) );
        } else {
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
            add_action( 'wp_head', array( $this, 'onload' ) );
            add_action( 'the_content', array( $this, 'convert_pres' ) );
        }
    }

    public function initialize_option() {
        if ( !get_option( 'rtsyntax_options' ) ) {
            $options = array( 'theme' => 'default' );
            add_option( 'rtsyntax_options', $options );
        }
    }

    public function register_settings() {
 	add_settings_section( 'rtsyntax-options', __( 'Code Theme', 'rtSyntax' ), array( $this, 'settings_section' ), 'rtsyntax' );
 	add_settings_field( 'rtsyntax-theme', __( 'Theme', 'rtSyntax' ), array( $this, 'settings_field' ), 'rtsyntax', 'rtsyntax-options' );
        register_setting( 'rtsyntax', 'rtsyntax_options' );
    }

    public function settings_section(){
    }

    public function settings_field() {
        $options = get_option( 'rtsyntax_options' ); ?>
        <select id="rtsyntax-theme" name="rtsyntax_options[theme]">
            <option value="default"<?php selected( $options['theme'], 'default', true ); ?>><?php _e( 'Default', 'rtSyntax' ); ?></option>
            <option value="arta"<?php selected( $options['theme'], 'arta', true ); ?>><?php _e( 'Arta', 'rtSyntax' ); ?></option>
            <option value="ascetic"<?php selected( $options['theme'], 'ascetic', true ); ?>><?php _e( 'Ascetic', 'rtSyntax' ); ?></option>
            <option value="brown_paper"<?php selected( $options['theme'], 'brown_paper', true ); ?>><?php _e( 'Brown Paper', 'rtSyntax' ); ?></option>
            <option value="dark"<?php selected( $options['theme'], 'dark', true ); ?>><?php _e( 'Dark', 'rtSyntax' ); ?></option>
            <option value="far"<?php selected( $options['theme'], 'far', true ); ?>><?php _e( 'FAR', 'rtSyntax' ); ?></option>
            <option value="github"<?php selected( $options['theme'], 'github', true ); ?>><?php _e( 'GitHub', 'rtSyntax' ); ?></option>
            <option value="googlecode"<?php selected( $options['theme'], 'googlecode', true ); ?>><?php _e( 'Google Code', 'rtSyntax' ); ?></option>
            <option value="idea"<?php selected( $options['theme'], 'idea', true ); ?>><?php _e( 'IDEA', 'rtSyntax' ); ?></option>
            <option value="ir_black"<?php selected( $options['theme'], 'ir_black', true ); ?>><?php _e( 'IR Black', 'rtSyntax' ); ?></option>
            <option value="magula"<?php selected( $options['theme'], 'magula', true ); ?>><?php _e( 'Magula', 'rtSyntax' ); ?></option>
            <option value="monokai"<?php selected( $options['theme'], 'monokai', true ); ?>><?php _e( 'Monokai', 'rtSyntax' ); ?></option>
            <option value="pojoaque"<?php selected( $options['theme'], 'pojoaque', true ); ?>><?php _e( 'Pojoaque', 'rtSyntax' ); ?></option>
            <option value="rainbow"<?php selected( $options['theme'], 'rainbow', true ); ?>><?php _e( 'Rainbow', 'rtSyntax' ); ?></option>
            <option value="school_book"<?php selected( $options['theme'], 'school_book', true ); ?>><?php _e( 'School Book', 'rtSyntax' ); ?></option>
            <option value="solarized_dark"<?php selected( $options['theme'], 'solarized_dark', true ); ?>><?php _e( 'Solarized Dark', 'rtSyntax' ); ?></option>
            <option value="solarized_light"<?php selected( $options['theme'], 'solarized_light', true ); ?>><?php _e( 'Solarized Light', 'rtSyntax' ); ?></option>
            <option value="sunburst"<?php selected( $options['theme'], 'sunburst', true ); ?>><?php _e( 'Sunburst', 'rtSyntax' ); ?></option>
            <option value="tomorrow-night-blue"<?php selected( $options['theme'], 'tomorrow-night-blue', true ); ?>><?php _e( 'Tomorrow Night Blue', 'rtSyntax' ); ?></option>
            <option value="tomorrow-night-bright"<?php selected( $options['theme'], 'tomorrow-night-bright', true ); ?>><?php _e( 'Tomorrow Night Bright', 'rtSyntax' ); ?></option>
            <option value="tomorrow-night-eighties"<?php selected( $options['theme'], 'tomorrow-night-eighties', true ); ?>><?php _e( 'Tomorrow Night Eighties', 'rtSyntax' ); ?></option>
            <option value="tomorrow-night"<?php selected( $options['theme'], 'tomorrow-night', true ); ?>><?php _e( 'Tomorrow Night', 'rtSyntax' ); ?></option>
            <option value="tomorrow"<?php selected( $options['theme'], 'tomorrow', true ); ?>><?php _e( 'Tomorrow', 'rtSyntax' ); ?></option>
            <option value="vs"<?php selected( $options['theme'], 'vs', true ); ?>><?php _e( 'Visual Studio', 'rtSyntax' ); ?></option>
            <option value="xcode"<?php selected( $options['theme'], 'xcode', true ); ?>><?php _e( 'XCode', 'rtSyntax' ); ?></option>
            <option value="zenburn"<?php selected( $options['theme'], 'zenburn', true ); ?>><?php _e( 'Zenburn', 'rtSyntax' ); ?></option>
        </select><?php
    }


    public function admin() {
        add_options_page( __( 'rtSyntax', 'rtSyntax' ), __( 'rtSyntax', 'rtSyntax' ), 'manage_options', 'rtsyntax', array( $this, 'admin_page' ) );
    }

    public function admin_page() { ?>
        <div class="wrap">
            <h2><?php _e( 'rtSyntax', 'rtSyntax' ); ?></h2>
            <form method="post" action="options.php">
                <?php settings_fields( 'rtsyntax' ); ?>
                <?php do_settings_sections( 'rtsyntax' ); ?>
                <?php do_settings_fields( 'rtsyntax', 'rtsyntax-theme' ); ?>
                <?php submit_button(); ?>
            </form>
        </div><?php
    }

    public function rtsyntax_buttons( $plugin_array ) {
        $plugin_array['rtsyntax'] = plugin_dir_url(__FILE__).'js/rtsyntax.js';
        $plugin_array['rtcode'] = plugin_dir_url(__FILE__).'js/rtsyntax.js';
        $plugin_array['rtkey'] = plugin_dir_url(__FILE__).'js/rtsyntax.js';
        return $plugin_array;
    }

    public function register_rtsyntax_buttons( $buttons ) {
        array_push( $buttons, "separator", "rtsyntax", "rtcode", "rtkey", "code" );
        return $buttons;
    }

    public function enqueue() {
        $options = get_option( 'rtsyntax_options' );
        wp_enqueue_style( 'rtsyntax-' . $options['theme'], plugin_dir_url(__FILE__) . 'css/' . $options['theme'] . '.css' );
        wp_enqueue_script( 'rtsyntax', plugin_dir_url(__FILE__) . 'js/highlight.js', array(), null, true );
    }

    public function onload() { ?>
        <script>
            jQuery(function ($) {
                if( typeof hljs === 'object' ) {
                    hljs.initHighlightingOnLoad();
                }
            } );
        </script><?php
    }

    public function convert_pres( $content ){
        $content = str_replace( '<pre>', '<pre class="no-highlight">', $content );
        return preg_replace( '/<pre(.*)>(.*)<\/pre>/isU', '<pre><code$1>$2</code></pre>', $content );
    }

}
$rtSyntax = new rtSyntax();
