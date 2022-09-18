<?php
/**
 * Plugin Name:     Book Library Search
 * Plugin URI:      https://github.com/fullstackyash/book-library-search
 * Description:     Book library search plugin to keep book records and search books based on book title, publisher, author etc.
 * Author:          Yash Chopra
 * Author URI:      https://github.com/fullstackyash
 * Text Domain:     book-library-search
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         book-library-search
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


require BOOK_LIBRARY_SEARCH_DIR . '/includes/admin/class-loader.php';
require BOOK_LIBRARY_SEARCH_DIR . '/includes/admin/class-bls-book-post-type.php';
require BOOK_LIBRARY_SEARCH_DIR . '/includes/admin/class-bls-book-taxonomies.php';
