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
		add_filter( 'manage_' . BOOK_POST_TYPE . '_posts_columns', array( $this, 'bls_add_custom_columns' ) );
		add_action( 'manage_' . BOOK_POST_TYPE . '_posts_custom_column', array( $this, 'bls_add_custom_columns_content' ), 10, 2 );
		add_filter( 'template_include', array( $this, 'bls_book_detail_template' ) );
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

	/**
	 * Add Custom Columns for book post type.
	 *
	 * @param array $columns columns Array.
	 * @return array
	 */
	public function bls_add_custom_columns( $columns ) : array {

		$columns['price']  = __( 'Book Price', 'book-library-search' );
		$columns['rating'] = __( 'Book Rating', 'book-library-search' );
		return $columns;
	}

	/**
	 * Add content for custom columns for book post type.
	 *
	 * @param array $column Columns Array.
	 * @param int   $post_id book id.
	 * @return void
	 */
	public function bls_add_custom_columns_content( $column, $post_id ) : void {
		$rating = get_post_meta( $post_id, 'book_rating', true );
		$price  = get_post_meta( $post_id, 'book_price', true );

		if ( 'price' === $column ) {
			echo ! empty( $price ) ? '$' . esc_html( $price ) : '';
		}

		if ( 'rating' === $column ) {
			$bls_book_search_shortcode = new BLS_BOOK_SEARCH_SHORTCODE();
			$bls_book_search_shortcode->bls_display_star_rating( $rating );
		}
	}

	/**
	 * Custom book template to render on frontend.
	 *
	 * @param string $template Post template.
	 * @return string.
	 */
	public function bls_book_detail_template( $template ) : string {
		if ( is_singular( BOOK_POST_TYPE ) ) {
			$template = BOOK_LIBRARY_SEARCH_DIR . '/templates/single-book.php';
		}
		return $template;
	}
}

$bls_book_post_type = new BLS_BOOK_POST_TYPE();
