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
	}

	public function style() {

		wp_register_style( 'dzs_help', plugins_url( 'assets/style.css', __FILE__ ), array(), '0.1' );
	}

	public function do_add() {

		$screen = get_current_screen()->id;

		$files = glob( plugin_dir_path( __FILE__ ) . "docs/{$screen}*.html" );

		if ( ! empty( $files ) ) {
			return $files;
		}

		return false;
	}

	/**
	 * Creates a Videos tab in the help menu for screens included in $places
	 */
	public function add_help_tab() {

		$screen = get_current_screen();

		if ( $files = $this->do_add() ) {

			wp_enqueue_style( 'dzs_help' );

			foreach ( $files as $filename ) {

				$filename = basename( $filename );

				$file_parts = explode( $screen->id, $filename );
				$tab        = $file_parts[1];

				if ( $tab != '.html' ) {

					$tab = substr( $tab, 1, strlen( $tab ) - 6 );

					$screen->add_help_tab( array(
							'id'       => "dzs_$tab",
							'title'    => ucwords( str_replace( '-', ' ', $tab ) ),
							'content'  => '',
							'callback' => array( $this, 'display' ),
							'filename'     => $filename,
						)
					);
				} else {

					$screen->add_help_tab( array(
							'id'       => "dzs_help",
							'title'    => 'Help',
							'content'  => '',
							'callback' => array( $this, 'display' ),
							'filename'     => $filename,
						)
					);
				}
			}
		}
	}

	/**
	 * Displays the videos inside the help menu
	 */
	public function display( $screen, $args ) {

		$filename = $args['filename'];

		$text = file_get_contents( __DIR__ . "/docs/{$filename}" );
		$text = apply_filters( 'the_content', $text );
		echo $text;
	}

}

$dzs_help = new dzs_help();
