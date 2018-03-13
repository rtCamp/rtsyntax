<?php
/**
 * Plugin Name: rtSyntax
 * Plugin URI: https://wordpress.org/plugins/rtsyntax/
 * Author: rtCamp
 * Author URI: https://rtcamp.com
 * Version: 1.1.0
 * Description: A no-fuss, lightweight, fast and optimised syntax highlighter for WordPress.
 * Contributors: rtcamp, rahul286, JoshuaAbenazer, sid177, montu3366
 * Tags: code highlighter, highlighter, highlighting, syntax, syntax highlighter, source, jquery, javascript, nginx, php, code, CSS, html, php, sourcecode, xhtml, languages, TinyMCE
 * Text Domain: rtsyntax
 *
 * @package rtsyntax
 *
 */

/**
 * Class rtSyntax
 */
class rtSyntax {

	/**
	 * rtSyntax constructor.
	 */
	public function __construct() {

		/**
		 * Constants
		 */
		define( 'RTSYNTAX_DIR_URL', plugin_dir_url( __FILE__ ) );

		register_activation_hook( __FILE__, array( $this, 'initialize_option' ) );
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

			add_filter( 'mce_external_plugins', array( $this, 'rtsyntax_buttons' ) );
			add_filter( 'mce_buttons', array( $this, 'register_rtsyntax_buttons' ) );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'wp_head', array( $this, 'onload' ) );
			add_action( 'the_content', array( $this, 'convert_pres' ) );
		}
	}

	/**
	 * add option if doesn't exist
	 */
	public function initialize_option() {
		if ( ! get_option( 'rtsyntax_options' ) ) {
			$options = array( 'theme' => 'default' );
			add_option( 'rtsyntax_options', $options );
		}
	}

	/**
	 * Register settings in settings section
	 */
	public function register_settings() {
		add_settings_section( 'rtsyntax-options', __( 'Code Theme', 'rtsyntax' ), array( $this, 'settings_section' ), 'rtsyntax' );
		add_settings_field( 'rtsyntax-theme', __( 'Theme', 'rtsyntax' ), array( $this, 'settings_field' ), 'rtsyntax', 'rtsyntax-options' );
		register_setting( 'rtsyntax', 'rtsyntax_options' );
	}

	/**
	 * Null.
	 */
	public function settings_section() {
	}

	/**
	 * List of available themes
	 */
	public function settings_field() {
		$options = get_option( 'rtsyntax_options' );
		?>
		<select title="Select theme" id="rtsyntax-theme" name="rtsyntax_options[theme]">
			<option value="default"<?php selected( $options['theme'], 'default', true ); ?>><?php esc_html_e( 'Default', 'rtsyntax' ); ?></option>
			<option value="arta"<?php selected( $options['theme'], 'arta', true ); ?>><?php esc_html_e( 'Arta', 'rtsyntax' ); ?></option>
			<option value="ascetic"<?php selected( $options['theme'], 'ascetic', true ); ?>><?php esc_html_e( 'Ascetic', 'rtsyntax' ); ?></option>
			<option value="brown_paper"<?php selected( $options['theme'], 'brown-paper', true ); ?>><?php esc_html_e( 'Brown Paper', 'rtsyntax' ); ?></option>
			<option value="dark"<?php selected( $options['theme'], 'dark', true ); ?>><?php esc_html_e( 'Dark', 'rtsyntax' ); ?></option>
			<option value="far"<?php selected( $options['theme'], 'far', true ); ?>><?php esc_html_e( 'FAR', 'rtsyntax' ); ?></option>
			<option value="github"<?php selected( $options['theme'], 'github', true ); ?>><?php esc_html_e( 'GitHub', 'rtsyntax' ); ?></option>
			<option value="googlecode"<?php selected( $options['theme'], 'googlecode', true ); ?>><?php esc_html_e( 'Google Code', 'rtsyntax' ); ?></option>
			<option value="idea"<?php selected( $options['theme'], 'idea', true ); ?>><?php esc_html_e( 'IDEA', 'rtsyntax' ); ?></option>
			<option value="ir_black"<?php selected( $options['theme'], 'ir-black', true ); ?>><?php esc_html_e( 'IR Black', 'rtsyntax' ); ?></option>
			<option value="magula"<?php selected( $options['theme'], 'magula', true ); ?>><?php esc_html_e( 'Magula', 'rtsyntax' ); ?></option>
			<option value="monokai"<?php selected( $options['theme'], 'monokai', true ); ?>><?php esc_html_e( 'Monokai', 'rtsyntax' ); ?></option>
			<option value="pojoaque"<?php selected( $options['theme'], 'pojoaque', true ); ?>><?php esc_html_e( 'Pojoaque', 'rtsyntax' ); ?></option>
			<option value="rainbow"<?php selected( $options['theme'], 'rainbow', true ); ?>><?php esc_html_e( 'Rainbow', 'rtsyntax' ); ?></option>
			<option value="school_book"<?php selected( $options['theme'], 'school-book', true ); ?>><?php esc_html_e( 'School Book', 'rtsyntax' ); ?></option>
			<option value="solarized_dark"<?php selected( $options['theme'], 'solarized-dark', true ); ?>><?php esc_html_e( 'Solarized Dark', 'rtsyntax' ); ?></option>
			<option value="solarized_light"<?php selected( $options['theme'], 'solarized-light', true ); ?>><?php esc_html_e( 'Solarized Light', 'rtsyntax' ); ?></option>
			<option value="sunburst"<?php selected( $options['theme'], 'sunburst', true ); ?>><?php esc_html_e( 'Sunburst', 'rtsyntax' ); ?></option>
			<option value="tomorrow-night-blue"<?php selected( $options['theme'], 'tomorrow-night-blue', true ); ?>><?php esc_html_e( 'Tomorrow Night Blue', 'rtsyntax' ); ?></option>
			<option value="tomorrow-night-bright"<?php selected( $options['theme'], 'tomorrow-night-bright', true ); ?>><?php esc_html_e( 'Tomorrow Night Bright', 'rtsyntax' ); ?></option>
			<option value="tomorrow-night-eighties"<?php selected( $options['theme'], 'tomorrow-night-eighties', true ); ?>><?php esc_html_e( 'Tomorrow Night Eighties', 'rtsyntax' ); ?></option>
			<option value="tomorrow-night"<?php selected( $options['theme'], 'tomorrow-night', true ); ?>><?php esc_html_e( 'Tomorrow Night', 'rtsyntax' ); ?></option>
			<option value="tomorrow"<?php selected( $options['theme'], 'tomorrow', true ); ?>><?php esc_html_e( 'Tomorrow', 'rtsyntax' ); ?></option>
			<option value="vs"<?php selected( $options['theme'], 'vs', true ); ?>><?php esc_html_e( 'Visual Studio', 'rtsyntax' ); ?></option>
			<option value="xcode"<?php selected( $options['theme'], 'xcode', true ); ?>><?php esc_html_e( 'XCode', 'rtsyntax' ); ?></option>
			<option value="zenburn"<?php selected( $options['theme'], 'zenburn', true ); ?>><?php esc_html_e( 'Zenburn', 'rtsyntax' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Add settings page on admin side
	 */
	public function add_admin_menu() {
		add_options_page( __( 'rtSyntax', 'rtsyntax' ), __( 'rtSyntax', 'rtsyntax' ), 'manage_options', 'rtsyntax', array( $this, 'admin_page' ) );
	}

	/**
	 * Add section for registered settings
	 */
	public function admin_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'rtSyntax', 'rtsyntax' ); ?></h2>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'rtsyntax' );
				do_settings_sections( 'rtsyntax' );
				do_settings_fields( 'rtsyntax', 'rtsyntax-theme' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register buttons to display on tinyMCE editor
	 *
	 * @param array $plugin_array Plugin Related Array.
	 *
	 * @return array
	 */
	public function rtsyntax_buttons( $plugin_array ) {
		$plugin_array['rtsyntax'] = RTSYNTAX_DIR_URL . 'js/rtsyntax.js';
		$plugin_array['rtcode']   = RTSYNTAX_DIR_URL . 'js/rtsyntax.js';
		$plugin_array['rtkey']    = RTSYNTAX_DIR_URL . 'js/rtsyntax.js';
		return $plugin_array;
	}

	/**
	 * Add registered buttons on tinyMCE editor
	 *
	 * @param array $buttons Buttons Array.
	 *
	 * @return array
	 */
	public function register_rtsyntax_buttons( $buttons ) {
		array_push( $buttons, 'separator', 'rtsyntax', 'rtcode', 'rtkey', 'code' );

		return $buttons;
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue() {
		$options = get_option( 'rtsyntax_options' );
		wp_enqueue_style( 'rtsyntax-' . $options['theme'], RTSYNTAX_DIR_URL . '/css/' . $options['theme'] . '.css' );
		wp_enqueue_script( 'rtsyntax', RTSYNTAX_DIR_URL . '/js/highlight.js', array(), null, true );
	}

	/**
	 * Initialize highlight library
	 */
	public function onload() {
		?>
		<script>
			jQuery(function () {
				hljs.initHighlightingOnLoad();
			} );
		</script>
		<?php
	}

	/**
	 * Get Post Content and convert string to html tags.
	 *
	 * @param String $content Post Content.
	 *
	 * @return null|string Content With Some HTML Content.
	 */
	public function convert_pres( $content ) {
		$content = str_replace( '<pre>', '<pre class="no-highlight">', $content );

		return preg_replace( '/<pre(.*)>(.*)<\/pre>/isU', '<pre style="max-height: 60em;"><code $1>$2</code></pre>', $content );
	}

}

/** @var rtSyntax $rtsyntax */
$rtsyntax = new rtSyntax();

/**
 * Check if this is_plugin_active function is already avaiable or not
 */
if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * If gutenberg plugin is activate then register the blocks
 */
if ( is_plugin_active( 'gutenberg/gutenberg.php' ) ) {
	require 'rtsyntax-block.php';
}
