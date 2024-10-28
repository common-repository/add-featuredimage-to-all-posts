<?php
/*
Plugin Name: Featured image to All-Posts
Plugin URI: http://yellow-goose.com/?p=1839
Description: Add thumbnails of featured image to a column of admin All Posts page. No complecated settings.


Version: 1.8.5
Author: yuya2since
Author URI: http://yellow-goose.com/?p=1839
License: GPLv2 or later
Text Domain: add-featuredimage-to-all-posts
Domain Path: /languages
*/

/*
Copyright (C) 2020 yuya2since

This plugin is free. This plugin is distributed WITHOUT ANY WARRANTY.

*/


load_plugin_textdomain( 'add-featuredimage-to-all-posts', false, basename( dirname( __FILE__ ) ).'/languages' );



Add_Thumb_To_Admin_Posts::init();

/**
 * Class Add_Thumb_To_Admin_Posts
 */
class Add_Thumb_To_Admin_Posts {

	static $instance;

	// singleton
	public static function init() {
		if ( ! self::$instance )
			self::$instance = new Add_Thumb_To_Admin_Posts;
		return self::$instance;
	}


	// constructor
	function __construct() {
		add_filter( 'admin_init', array( $this, 'actions' ) );
	}


	// action hooks
	function actions() {
		add_filter( 'manage_posts_columns', array( $this, 'add_thumb_columns' ) );
		add_filter( 'manage_posts_custom_column', array( $this, 'add_thumb_column' ), 10, 2 );
	}


	/**
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	function add_thumb_columns( $columns ) {
		echo '<style>.column-thumb{width:80px;}</style>';
		$columns          = array_reverse( $columns, true );
		$columns['thumb'] = '<div class="dashicons dashicons-format-image"></div>';
		$columns          = array_reverse( $columns, true );

		return $columns;
	}

	/**
	 *
	 * @param $column
	 * @param $post_id
	 */
	function add_thumb_column( $column, $post_id ) {
		switch ( $column ) {
			case 'thumb':
				if ( $thumb = get_the_post_thumbnail( $post_id, array( 80, 80 ) ) ) {
					$user_can_edit = current_user_can( 'edit_post', $post_id );
					$is_trash      = isset( $_REQUEST['status'] ) && 'trash' == $_REQUEST['status'];
					if ( ! $is_trash || $user_can_edit ) {
						$thumb = sprintf( '<a href="%s" title="%s">%s</a>',
							get_edit_post_link( $post_id, true ),
							esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'default' ), _draft_or_post_title() ) ),
							$thumb );
					}
					echo $thumb;
				}
				break;
			default:
				break;
		}
	}
}
