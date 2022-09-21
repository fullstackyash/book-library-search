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

require BOOK_LIBRARY_SEARCH_DIR . '/includes/constants.php';
require BOOK_LIBRARY_SEARCH_DIR . '/includes/admin/class-loader.php';
require BOOK_LIBRARY_SEARCH_DIR . '/includes/admin/class-bls-book-post-type.php';
require BOOK_LIBRARY_SEARCH_DIR . '/includes/admin/class-bls-book-taxonomies.php';
require BOOK_LIBRARY_SEARCH_DIR . '/includes/admin/class-bls-book-meta-fields.php';
require BOOK_LIBRARY_SEARCH_DIR . '/includes/shortcodes/class-bls-book-search-shortcode.php';
