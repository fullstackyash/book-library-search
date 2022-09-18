<?php
/**
 * Register book Taxonomies.
 *
 * @package book-library-search
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Class for registering the "book" custom post type taxonomies.
 */
class  BLS_BOOK_TAXONOMIES {


	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'bls_setup_taxonomies' ) );
	}


	/**
	 * Register taxonomies for "book" post type.
	 *
	 * @return void
	 */
	public function bls_setup_taxonomies() : void {
		register_taxonomy(
			AUTHOR_TAXONOMY,
			BOOK_POST_TYPE,
			array(
				'labels'            => array(
					'name'          => __( 'Authors', 'library-book-search-tool' ),
					'singular_name' => __( 'Author', 'library-book-search-tool' ),
					'search_items'  => __( 'Search Author', 'library-book-search-tool' ),
					'all_items'     => __( 'All Authors', 'library-book-search-tool' ),
					'parent_item'   => __( 'Parent Author', 'library-book-search-tool' ),
					'edit_item'     => __( 'Edit Author', 'library-book-search-tool' ),
					'update_item'   => __( 'Update Author', 'library-book-search-tool' ),
					'add_new_item'  => __( 'Add New Author', 'library-book-search-tool' ),
					'new_item_name' => __( 'New Author', 'library-book-search-tool' ),
					'menu_name'     => __( 'Authors', 'library-book-search-tool' ),
				),
				'hierarchical'      => false,
				'query_var'         => true,
				'rewrite'           => true,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => true,
				'show_admin_column' => true,
				'public'            => true,
				'show_in_menu'      => true,
				'show_in_rest'      => true,
			)
		);

		register_taxonomy(
			PUBLISHER_TAXONOMY,
			BOOK_POST_TYPE,
			array(
				'labels'            => array(
					'name'          => __( 'Publishers', 'library-book-search-tool' ),
					'singular_name' => __( 'Publisher', 'library-book-search-tool' ),
					'search_items'  => __( 'Search Publisher', 'library-book-search-tool' ),
					'all_items'     => __( 'All Publishers', 'library-book-search-tool' ),
					'parent_item'   => __( 'Parent Publisher', 'library-book-search-tool' ),
					'edit_item'     => __( 'Edit Publisher', 'library-book-search-tool' ),
					'update_item'   => __( 'Update Publisher', 'library-book-search-tool' ),
					'add_new_item'  => __( 'Add New Publisher', 'library-book-search-tool' ),
					'new_item_name' => __( 'New Publisher', 'library-book-search-tool' ),
					'menu_name'     => __( 'Publishers', 'library-book-search-tool' ),
				),
				'hierarchical'      => false,
				'query_var'         => true,
				'rewrite'           => true,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => true,
				'show_admin_column' => true,
				'public'            => true,
				'show_in_menu'      => true,
				'show_in_rest'      => true,
			)
		);
	}
}

$bls_book_taxonomies = new BLS_BOOK_TAXONOMIES();
