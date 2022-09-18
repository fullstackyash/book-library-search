<?php
/**
 * Register book post type.
 *
 * @package book-library-search
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for registering the "book" custom post type.
 */
class  BLS_BOOK_POST_TYPE {


	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'bls_setup_post_type' ) );
	}

	/**
	 * Register the "book" custom post type.
	 *
	 * @return void
	 */
	public function bls_setup_post_type() : void {
		register_post_type(
			BOOK_POST_TYPE,
			array(
				'labels'             => array(
					'name'          => __( 'Books', 'book-library-search' ),
					'singular_name' => __( 'Book', 'book-library-search' ),
					'search_items'  => __( 'Search Books', 'book-library-search' ),
					'menu_name'     => __( 'Books', 'book-library-search' ),
				),
				'public'             => true,
				'has_archive'        => true,
				'show_in_menu'       => true,
				'show_ui'            => true,
				'menu_icon'          => 'dashicons-book-alt',
				'supports'           => array( 'title', 'thumbnail', 'editor', 'featured-image', 'custom-fields' ),
				'show_in_rest'       => true,
				'publicly_queryable' => true,
				'rewrite'            => true,

			)
		);
	}
}

$bls_book_post_type = new BLS_BOOK_POST_TYPE();
