<?php
/**
 * This file must be required in main plugin file
 *
 * @package rtsyntax
 */

defined( 'ABSPATH' ) || exit;


/**
 * Load required css and js files for gutenberg editor
 */
function rtsyntax_block_enqueue_editor_assets() {
	$options = get_option( 'rtsyntax_options' );

	/**
	 * File in which all the blocks are created and converted to normal js
	 */
	wp_enqueue_script( 'rtsyntax-gutenberg-block', plugins_url( '/js/block.build.js', __FILE__ ), array( 'wp-blocks', 'wp-i18n', 'wp-element', 'moment' ) );

	/**
	 * Theme file for code editor
	 */
	wp_enqueue_style( 'highlight', plugins_url( '/css/' . $options['theme'] . '.css', __FILE__ ) );

	/**
	 * Theme file for block view
	 */
	wp_enqueue_style( 'editor', plugins_url( '/css/editor.css', __FILE__ ) );

	/**
	 * Common style for both, admin and front-end
	 */
	wp_enqueue_style( 'rtsyntax-common-style', RTSYNTAX_DIR_URL . '/css/style.css' );

	/**
	 * List of themes available for user to select
	 * Object will be available inside js at front-end
	 */
	wp_localize_script(
		'rtsyntax-gutenberg-block', 'rtSyntax',
		array(
			'path'  => RTSYNTAX_DIR_URL,
			'debug' => WP_DEBUG,
		)
	);
}

add_action( 'enqueue_block_editor_assets', 'rtsyntax_block_enqueue_editor_assets' );
