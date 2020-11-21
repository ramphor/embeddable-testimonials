<?php
/**
 * Plugin Name: Ramphor Testimonials
 * Plugin URI: https://github.com/ramphor/embeddable-testimonials
 * Author: Ramphor Premium
 * Author URI: https://puleeno.com
 * Version: 1.0.0.13
 * Description: Create a testimonials for your site as WordPress plugin or embed to other plugins, themes
 */

use Ramphor\Testimonials\Testimonials;

define( 'EMBEDDABLE_TESTIMONIALS_PLUGIN_FILE', __FILE__ );


if ( ! class_exists( Testimonials::class ) ) {
	$composerAutoloader = sprintf( '%s/vendor/autoload.php', dirname( EMBEDDABLE_TESTIMONIALS_PLUGIN_FILE ) );
	if ( file_exists( $composerAutoloader ) ) {
		require_once $composerAutoloader;
	}
}

if ( ! function_exists( 'embeddable_testimonials' ) ) {
	function embeddable_testimonials() {
		return Testimonials::getInstance();
	}
}

$GLOBALS['embeddable_testimonials'] = embeddable_testimonials();
