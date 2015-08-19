<?php

/*
Plugin Name: DZS Help
Plugin URI: http://realbigplugins.com
Description: Provides helpful information and resources within the WordPress dashboard.
Version: 0.1
Author: Kyle Maurer
Author URI: http://kyleblog.net
License: GPL2
*/

/**
 * Class dzs_help
 */
class dzs_help {

	/**
	 * Initialize all the things
	 */
	public function __construct() {
		add_action( 'current_screen', array( $this, 'add_help_tab' ) );
//		add_action( 'admin_init', array( $this, 'style' ) );
//		add_action( 'admin_notices', function() {
//			var_dump(get_current_screen());
//		});
	}

	public function style() {
		wp_register_style( 'dzs_help', plugins_url( 'assets/style.css', __FILE__ ), array(), '0.1' );
	}

	public function do_add() {
		$screen = get_current_screen()->id;

		if ( file_exists( __DIR__ . '/docs/' . $screen . '.html' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Creates a Videos tab in the help menu for screens included in $places
	 */
	public function add_help_tab() {
		$screen = get_current_screen();
		if ( $this->do_add() ) {
			wp_enqueue_style( 'dzs_help' );
			$screen->add_help_tab( array(
					'id'       => 'dzs_help',
					'title'    => 'Help',
					'content'  => '',
					'callback' => array( $this, 'display' ),
				)
			);
		}
	}

	/**
	 * Displays the videos inside the help menu
	 */
	public function display() {

		$screen = get_current_screen()->id;
		$text = file_get_contents( __DIR__ . '/docs/' . $screen . '.html' );
		$text = apply_filters( 'the_content', $text );
		echo $text;
	}

}

$dzs_help = new dzs_help();
