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
	 * Array of all the admin screens and the tag for appropriate videos
	 * @var array
	 */
	public $places = array(
		array(
			'screen' => 'tools',
			'tag'    => 'import',
		),
		array(
			'screen' => 'plugins',
			'tag'    => 'plugins'
		),
		array(
			'screen' => 'dashboard',
			'tag'    => 'dashboard'
		),
		array(
			'screen' => 'update-core',
			'tag'    => 'update'
		),
		array(
			'screen' => 'widgets',
			'tag'    => 'widgets'
		),
	);

	/**
	 * Initialize all the things
	 */
	public function __construct() {
		add_action( 'current_screen', array( $this, 'add_help_tab' ) );
		add_action( 'admin_init', array( $this, 'style' ) );
	}

	public function style() {
		wp_register_style( 'dzs_help', plugins_url( 'assets/style.css', __FILE__ ), array(), '0.1' );
	}

	/**
	 * @param $tag
	 *
	 * @return array|mixed|string|null
	 */
	public function request( $tag ) {
		$url     = 'http://wordpress.tv/?dzs_helpapi=videos.json&posts_per_page=3&tag=';
		$request = wp_remote_get( $url . $tag );
		$request = wp_remote_retrieve_body( $request );
		if( is_wp_error( $request ) ) {
			error_log( $request->get_error_message() );
			$request = null;
		}
		return json_decode( $request );
	}

	/**
	 * Creates a Videos tab in the help menu for screens included in $places
	 */
	public function add_help_tab() {
		$screen = get_current_screen();
		foreach ( $this->places as $place ) {
			if ( $place['screen'] == $screen->base ) {
				wp_enqueue_style( 'dzs_help' );
				$screen->add_help_tab( array(
						'id'       => 'videos',
						'title'    => 'Videos',
						'content'  => '',
						'callback' => array( $this, 'display' ),
					)
				);
			}
		}
	}

	/**
	 * Displays the videos inside the help menu
	 */
	public function display() {
		$screen = get_current_screen();
		foreach ( $this->places as $place ) {
			if ( $place['screen'] == $screen->base ) {
				$tag = $place['tag'];
				$videos = $this->request( $tag );
				if ( !( $videos === null ) ) {
					echo '<ul class="dzs-help">';
					foreach ( $videos->videos as $video ) {
						echo '<li>';
						echo '<a href="' . $video->permalink . '" target="_BLANK">';
						echo '<img src="' . $video->thumbnail . '" />';
						echo '<span>' . $video->title . '</span>';
						echo '</a>';
						echo '</li>';
					}
					echo '</ul>';
				}
			}
		}
		echo '<a class="dzs-help-link" href="http://wordpress.tv/tag/' . $tag . '/">See more videos</a>';
	}

}

$dzs_help = new dzs_help();
