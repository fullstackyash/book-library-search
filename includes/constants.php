<?php
/**
 * Book Library Search Constants.
 *
 * @package book-library-search
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'BOOK_LIBRARY_SEARCH_DIR' ) ) {
	define( 'BOOK_LIBRARY_SEARCH_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
}

if ( ! defined( 'BOOK_LIBRARY_SEARCH__FILE__' ) ) {
	define( 'BOOK_LIBRARY_SEARCH__FILE__', __FILE__ );
}

if ( ! defined( 'BOOK_POST_TYPE' ) ) {
	define( 'BOOK_POST_TYPE', 'book' );
}

if ( ! defined( 'AUTHOR_TAXONOMY' ) ) {
	define( 'AUTHOR_TAXONOMY', 'author' );
}

if ( ! defined( 'PUBLISHER_TAXONOMY' ) ) {
	define( 'PUBLISHER_TAXONOMY', 'publisher' );
}
