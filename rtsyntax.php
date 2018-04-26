<?php
/**
 * Plugin Name: rtSyntax
 * Plugin URI: https://wordpress.org/plugins/rtsyntax/
 * Author: rtCamp
 * Author URI: https://rtcamp.com
 * Tested up to: 4.9.5
 * Version: 1.1.0
 * Description: A no-fuss, lightweight, fast and optimised syntax highlighter for WordPress.
 * Contributors: rtcamp, rahul286, JoshuaAbenazer, sid177, montu3366
 * Tags: code highlighter, highlighter, highlighting, syntax, syntax highlighter, source, jquery, javascript, nginx, php, code, CSS, html, php, sourcecode, xhtml, languages, TinyMCE
 * Text Domain: rtsyntax
 *
 * @package rtsyntax
 */

/**
 * Constants
 */
define( 'RTSYNTAX_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Class rtSyntax
 */
class rtSyntax {

	/**
	 * RtSyntax constructor.
	 */
	public function __construct() {

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
	 * Add option if doesn't exist
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

		$themes = array(
			'default'                 => __( 'Default', 'rtsyntax' ),
			'arta'                    => __( 'Arta', 'rtsyntax' ),
			'ascetic'                 => __( 'Ascetic', 'rtsyntax' ),
			'brown-paper'             => __( 'Brown Paper', 'rtsyntax' ),
			'dark'                    => __( 'Dark', 'rtsyntax' ),
			'far'                     => __( 'FAR', 'rtsyntax' ),
			'github'                  => __( 'GitHub', 'rtsyntax' ),
			'googlecode'              => __( 'Google Code', 'rtsyntax' ),
			'idea'                    => __( 'IDEA', 'rtsyntax' ),
			'ir-black'                => __( 'IR Black', 'rtsyntax' ),
			'magula'                  => __( 'Magula', 'rtsyntax' ),
			'monokai'                 => __( 'Monokai', 'rtsyntax' ),
			'pojoaque'                => __( 'Pojoaque', 'rtsyntax' ),
			'rainbow'                 => __( 'Rainbow', 'rtsyntax' ),
			'school-book'             => __( 'School Book', 'rtsyntax' ),
			'solarized-dark'          => __( 'Solarized Dark', 'rtsyntax' ),
			'solarized-light'         => __( 'Solarized Light', 'rtsyntax' ),
			'sunburst'                => __( 'Sunburst', 'rtsyntax' ),
			'tomorrow-night-blue'     => __( 'Tomorrow Night Blue', 'rtsyntax' ),
			'tomorrow-night-bright'   => __( 'Tomorrow Night Bright', 'rtsyntax' ),
			'tomorrow-night-eighties' => __( 'Tomorrow Night Eighties', 'rtsyntax' ),
			'tomorrow-night'          => __( 'Tomorrow Night', 'rtsyntax' ),
			'tomorrow'                => __( 'Tomorrow', 'rtsyntax' ),
			'vs'                      => __( 'Visual Studio', 'rtsyntax' ),
			'xcode'                   => __( 'XCode', 'rtsyntax' ),
			'zenburn'                 => __( 'Zenburn', 'rtsyntax' ),
		);

		?>
		<select title="Select theme" id="rtsyntax-theme" name="rtsyntax_options[theme]">
			<?php
			foreach ( $themes as $value => $label ) {
				?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $options['theme'], $value ); ?>><?php echo esc_html( $value ); ?></option>
				<?php
			}
			?>
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
		wp_enqueue_style( 'rtsyntax-common-style', RTSYNTAX_DIR_URL . '/css/style.css' );
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

		return preg_replace( '/<pre(.*)>(.*)<\/pre>/isU', '<pre class="rtsyntax-pre"><code $1>$2</code></pre>', $content );
	}

}

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
