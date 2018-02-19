<?php
/**
 * Entry File For Our Plugin.
 *
 * @package rtsyntax
 */

/**
 * Plugin Name: rtSyntax
 * Plugin URI: http://rtcamp.com
 * Author: rtCamp
 * Author URI: http://rtcamp.com
 * Version: 1.0.5
 * Description: A no-fuss, lightweight, fast and optimised syntax highlighter for WordPress. Tested upto 4.9.4
 * Contributors: rtcamp, rahul286, JoshuaAbenazer
 * Tags: code highlighter, highlighter, highlighting, syntax, syntax highlighter, source, jquery, javascript, nginx, php, code, CSS, html, php, sourcecode, xhtml, languages, TinyMCE
 */

/**
 * Class rtSyntax
 */
class RtSyntax {

	/**
	 * RtSyntax constructor.
	 */
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

	/**
	 * Initialize plugin option
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
		add_settings_section( 'rtsyntax-options', __( 'Code Theme', 'rtSyntax' ), array( $this, 'settings_section' ), 'rtsyntax' );
		add_settings_field( 'rtsyntax-theme', __( 'Theme', 'rtSyntax' ), array( $this, 'settings_field' ), 'rtsyntax', 'rtsyntax-options' );
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
		$options    = get_option( 'rtsyntax_options' );
		$pluginpath = plugin_dir_path( __FILE__ ) . 'css/themes/';
		$themes     = null;
		if ( is_dir( $pluginpath ) ) {
			$opendir = opendir( $pluginpath );
			if ( $opendir ) {
				$file = readdir( $opendir );
				while ( false !== $file ) {
					if ( '.' !== $file && '..' !== $file && ' ' !== $file ) {
						$filename = explode( '.', $file );
						$themes[] = $filename[0];
					}
					$file = readdir( $opendir );
				}
			}
		}
		?>
		<select title="Select theme" id="rtsyntax-theme" name="rtsyntax_options[theme]">
		<?php
		foreach ( $themes as $theme ) {
		?>
			<option value="<?php echo esc_attr( $theme ); ?>"<?php selected( $options['theme'], $theme, true ); ?>><?php echo esc_html( $theme, 'rtSyntax' ); ?></option>
		<?php
		}
	}

	/**
	 * Add settings page on admin side
	 */
	public function admin() {
		add_options_page( __( 'rtSyntax', 'rtSyntax' ), __( 'rtSyntax', 'rtSyntax' ), 'manage_options', 'rtsyntax', array( $this, 'admin_page' ) );
	}

	/**
	 * Add section for registered settings
	 */
	public function admin_page() {
	?>
		<div class="wrap">
		<h2><?php esc_html_e( 'rtSyntax', 'rtSyntax' ); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'rtsyntax' ); ?>
			<?php do_settings_sections( 'rtsyntax' ); ?>
			<?php do_settings_fields( 'rtsyntax', 'rtsyntax-theme' ); ?>
			<?php submit_button(); ?>
		</form>
		</div>
		<?php
	}

	/**
	 * Register buttons to display on tinyMCE editor
	 *
	 * @param Array $plugin_array Pulugin Related Array.
	 *
	 * @return mixed
	 */
	public function rtsyntax_buttons( $plugin_array ) {
		$plugin_array['rtsyntax'] = plugin_dir_url( __FILE__ ) . 'js/rtsyntax.js';
		$plugin_array['rtcode']   = plugin_dir_url( __FILE__ ) . 'js/rtsyntax.js';
		$plugin_array['rtkey']    = plugin_dir_url( __FILE__ ) . 'js/rtsyntax.js';
		return $plugin_array;
	}

	/**
	 * Add registered buttons on tinyMCE editor
	 *
	 * @param Array $buttons Buttons Array.
	 *
	 * @return mixed
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
		wp_enqueue_style( 'rtsyntax-' . $options['theme'], plugin_dir_url( __FILE__ ) . '/css/' . $options['theme'] . '.css' );
		wp_enqueue_script( 'rtsyntax', plugin_dir_url( __FILE__ ) . '/js/highlight.js', array(), null, true );
	}

	/**
	 * Initialize highlight library
	 */
	public function onload() {
	?>
		<script>
			jQuery(function () {
				var $ = jQuery;
				$('pre code').each(function(i, block) {
					if(block.classList.contains('highlight-block')){
						return;
					}
					hljs.highlightBlock(block);
				});
			} );
		</script>
		<?php
	}

	/**
	 * Get Post Content and convert string to html tags.
	 *
	 * @param String $content Post Content.
	 *
	 * @return null|string|string[] Content With Some HTML Content.
	 */
	public function convert_pres( $content ) {
		$content = str_replace( '<pre>', '<pre class="no-highlight">', $content );
		return preg_replace( '/<pre(.*)>(.*)<\/pre>/isU', '<pre><code $1>$2</code></pre>', $content );
	}

}
$rtsyntax = new RtSyntax();

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
