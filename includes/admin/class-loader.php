<?php
/**
 * Loader file.
 *
 * @package book-library-search
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Loader class for plugin activation and deactivation hooks.
 */
class Loader {

	/**
	 * Constructor.
	 */
	public function __construct() {
		register_activation_hook( BOOK_LIBRARY_SEARCH__FILE__, array( $this, 'bls_activate' ) );
		register_deactivation_hook( BOOK_LIBRARY_SEARCH__FILE__, array( $this, 'bls_deactivate' ) );
	}

	 /**
	  * Activate the plugin.
	  *
	  * @return void
	  */
	public function bls_activate() : void {
		$bls_book_post_type = new BLS_BOOK_POST_TYPE();
		// Trigger our function that registers the book custom post type plugin.
		$bls_book_post_type->bls_setup_post_type();
		// Clear the permalinks after the post type has been registered.
		flush_rewrite_rules();
	}

	/**
	 * Deactivation hook.
	 *
	 * @return void
	 */
	public function bls_deactivate() : void {
		// Unregister the post type, so the rules are no longer in memory.
		unregister_post_type( 'book' );
		// Clear the permalinks to remove our post type's rules from the database.
		flush_rewrite_rules();
	}
}

$loader = new Loader();
