<?php
/**
 * Book Search Shotcode.
 *
 * @package book-library-search
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for adding the "book search" shortcode.
 */
class  BLS_BOOK_SEARCH_SHORTCODE {


	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'bls_book_search_enqueue_assets' ) );
		add_shortcode( 'book_search', array( $this, 'bls_book_search_shortcode' ) );
		add_action( 'wp_ajax_bls_filter_books', array( $this, 'bls_book_search_render_results' ) );
		add_action( 'wp_ajax_nopriv_bls_filter_books', array( $this, 'bls_book_search_render_results' ) );
		add_filter( 'posts_where', array( $this, 'bls_search_book_by_title' ), 10, 2 );
	}

	/**
	 * Book Search Shortcode.
	 *
	 * @return mixed
	 */
	public function bls_book_search_shortcode() {
		ob_start();
		$this->bls_book_search_render_filters();
		?>
		<div class="book_results_wrapper">
		<?php
		$this->bls_book_search_render_results();
		?>
		</div>
		<?php
		wp_enqueue_script( 'book-search-script' );
		return ob_get_clean();
	}


	/**
	 * Book Search results.
	 *
	 * @return string
	 */
	public function bls_book_search_render_results() {

		if ( ! empty( $_POST ) && ! isset( $_POST['_ajaxnonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['_ajaxnonce'] ) ) ) {
			return false;
		}

		$page     = ! empty( $_POST['page'] ) ? filter_input( INPUT_POST, 'page', FILTER_SANITIZE_NUMBER_INT ) : 1;
		$cur_page = $page;
		$page     = --$page;
		// Set the number of results to display.
		$per_page = 10;
		$start    = $page * $per_page;

		$args = array(
			'post_type'      => BOOK_POST_TYPE,
			'post_status'    => 'publish',
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'posts_per_page' => $per_page,
			'offset'         => $start,
		);

		if ( ! empty( $_POST ) ) {
			$book_name      = filter_input( INPUT_POST, 'book_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$author_name    = filter_input( INPUT_POST, 'author_name', FILTER_SANITIZE_NUMBER_INT );
			$publisher_name = filter_input( INPUT_POST, 'publisher_name', FILTER_SANITIZE_NUMBER_INT );
			$book_rating    = filter_input( INPUT_POST, 'book_rating', FILTER_SANITIZE_NUMBER_INT );
			$book_price_min = (int) filter_input( INPUT_POST, 'book_price_min', FILTER_SANITIZE_NUMBER_INT );
			$book_price_max = (int) filter_input( INPUT_POST, 'book_price_max', FILTER_SANITIZE_NUMBER_INT );

			if ( ! empty( $author_name ) || ! empty( $publisher_name ) ) {
				$args['tax_query'] = array();
				if ( ! empty( $author_name ) ) {
					array_push(
						$args['tax_query'],
						array(
							'taxonomy' => AUTHOR_TAXONOMY,
							'terms'    => array( (int) $author_name ),
							'field'    => 'term_id',
							'operator' => 'IN',
						)
					);
				}
				if ( ! empty( $publisher_name ) ) {
					array_push(
						$args['tax_query'],
						array(
							'taxonomy' => PUBLISHER_TAXONOMY,
							'terms'    => array( (int) $publisher_name ),
							'field'    => 'term_id',
							'operator' => 'IN',
						)
					);
				}
				if ( ! empty( $author_name ) && ! empty( $publisher_name ) ) {
					array_push( $args['tax_query'], array( 'relation' => 'AND' ) );
				}
			}

			if ( ! empty( $book_rating ) || ( '' !== $book_price_min || '' !== $book_price_max ) ) {
				$args['meta_query'] = array();
				if ( ! empty( $book_rating ) ) {
					array_push(
						$args['meta_query'],
						array(
							'key'     => 'book_rating',
							'value'   => (int) $book_rating,
							'type'    => 'numeric',
							'compare' => '==',
						)
					);
				}

				if ( '' !== $book_price_min || '' !== $book_price_max ) {
					array_push(
						$args['meta_query'],
						array(
							'key'     => 'book_price',
							'value'   => array( $book_price_min, $book_price_max ),
							'type'    => 'numeric',
							'compare' => 'BETWEEN',
						)
					);
				}
				if ( ! empty( $book_rating ) && ( '' !== $book_price_min || '' !== $book_price_max ) ) {
					array_push( $args['meta_query'], array( 'relation' => 'AND' ) );
				}
			}

			if ( ! empty( $book_name ) ) {
				$args['search_book'] = $book_name;
			}

			ob_start();
		}

		$books_query = new WP_Query( $args );
		$books       = $books_query->posts;
		$count       = $books_query->found_posts;
		?>
		<div class="book_results">
			<table>               
				<tr>
					<th><?php esc_html_e( 'No', 'book-library-search' ); ?></th>
					<th><?php esc_html_e( 'Book Name', 'book-library-search' ); ?></th>
					<th><?php esc_html_e( 'Price', 'book-library-search' ); ?></th>
					<th><?php esc_html_e( 'Author', 'book-library-search' ); ?></th>
					<th><?php esc_html_e( 'Publisher', 'book-library-search' ); ?></th>
					<th><?php esc_html_e( 'Rating', 'book-library-search' ); ?></th>
				</tr>
				
				<?php
				$book_count = 0 === $page ? $page + 1 : 1 + ( $per_page * $page );
				if ( ! empty( $books ) ) :
					foreach ( $books as $book ) {
						$price           = get_post_meta( $book->ID, 'book_price', true );
						$rating          = get_post_meta( $book->ID, 'book_rating', true );
						$book_authors    = wp_get_post_terms( $book->ID, AUTHOR_TAXONOMY, array( 'fields' => 'names' ) );
						$author          = implode( ', ', $book_authors );
						$book_publishers = wp_get_post_terms( $book->ID, PUBLISHER_TAXONOMY, array( 'fields' => 'names' ) );
						$publisher       = implode( ', ', $book_publishers );
						?>
						<tr>
							<td><?php echo esc_html( $book_count ); ?></td>
							<td><a target="_blank" href="<?php the_permalink( $book->ID ); ?>"><?php echo esc_html( $book->post_title ); ?></a></td>
							<td><?php echo esc_html( '$' . $price ); ?></td>
							<td><?php echo esc_html( $author ); ?></td>
							<td><?php echo esc_html( $publisher ); ?></td>
							<td><?php $this->bls_display_star_rating( $rating ); ?></td>
						</tr>
						<?php
						$book_count ++;
					}
				else :
					?>
					<tr>
						<td>
						<?php esc_html_e( 'No Records Found', 'book-library-search' ); ?>
						</td>
						<td></td><td></td><td></td><td></td><td></td>                       
					</tr>
					<?php
				endif;
				?>
						   
			</table>
		</div>
		<?php
		$this->bls_books_pagination( $count, $per_page, $cur_page );
		if ( ! empty( $_POST ) ) {
			$html = ob_get_clean();
			wp_send_json_success( $html );
		}

	}

	/**
	 * Book Search filters.
	 *
	 * @param int $count total post count.
	 * @param int $per_page total post count in a page.
	 * @param int $cur_page current page.
	 *
	 * @return void
	 */
	public function bls_books_pagination( $count, $per_page, $cur_page ) : void {
		$previous_btn = true;
		$next_btn     = true;
		$first_btn    = false;
		$last_btn     = false;

		$no_of_paginations = ceil( $count / $per_page );

		if ( $cur_page >= 7 ) {
			$start_loop = $cur_page - 3;
			if ( $no_of_paginations > $cur_page + 3 ) {
				$end_loop = $cur_page + 3;
			} elseif ( $cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6 ) {
				$start_loop = $no_of_paginations - 6;
				$end_loop   = $no_of_paginations;
			} else {
				$end_loop = $no_of_paginations;
			}
		} else {
			$start_loop = 1;
			if ( $no_of_paginations > 7 ) {
				$end_loop = 7;
			} else {
				$end_loop = $no_of_paginations;
			}
		}
		// Pagination Buttons logic.
		$pag_container  = '';
		$pag_container .= "
        <div class='bls-universal-pagination'>
            <ul>";

		if ( $first_btn && $cur_page > 1 ) {
			$pag_container .= "<li p='1' class='active'>First</li>";
		} elseif ( $first_btn ) {
			$pag_container .= "<li p='1' class='inactive'>First</li>";
		}

		if ( $previous_btn && $cur_page > 1 ) {
			$pre            = $cur_page - 1;
			$pag_container .= "<li p='$pre' class='active'>Previous</li>";
		} elseif ( $previous_btn ) {
			$pag_container .= "<li class='inactive'>Previous</li>";
		}
		for ( $i = $start_loop; $i <= $end_loop; $i++ ) {

			if ( $cur_page == $i ) {
				$pag_container .= "<li p='$i' class = 'selected' >{$i}</li>";
			} else {
				$pag_container .= "<li p='$i' class='active'>{$i}</li>";
			}
		}

		if ( $next_btn && $cur_page < $no_of_paginations ) {
			$nex            = $cur_page + 1;
			$pag_container .= "<li p='$nex' class='active'>Next</li>";
		} elseif ( $next_btn ) {
			$pag_container .= "<li class='inactive'>Next</li>";
		}

		if ( $last_btn && $cur_page < $no_of_paginations ) {
			$pag_container .= "<li p='$no_of_paginations' class='active'>Last</li>";
		} elseif ( $last_btn ) {
			$pag_container .= "<li p='$no_of_paginations' class='inactive'>Last</li>";
		}

		$pag_container = $pag_container . '
            </ul>
        </div>';

		// We echo the final output.
		echo '<div class = "bls-pagination-nav">' . wp_kses_post( $pag_container ) . '</div>';

	}

	/**
	 * Book Search filters.
	 *
	 * @return void
	 */
	public function bls_book_search_render_filters() : void {

		$authors    = get_terms(
			array(
				'taxonomy'   => AUTHOR_TAXONOMY,
				'hide_empty' => true,
			)
		);
		$publishers = get_terms(
			array(
				'taxonomy'   => PUBLISHER_TAXONOMY,
				'hide_empty' => true,
			)
		);

		?>
		<form method="post" class="book-search book_search_filters">
			<div class="book_search_filters">
				<h2><?php esc_html_e( 'Book Search', 'book-library-search' ); ?></h2>
				<div class="filters" >
					<div class="filter_row">
						<div class="filter-item">
							<label for="book_name"><?php esc_html_e( 'Book Name:', 'book-library-search' ); ?></label>
							<input type="text" id="book_name" name="book_name">
						</div>
						<div class="filter-item">
							<label for="author_name"><?php esc_html_e( 'Author:', 'book-library-search' ); ?></label>
							<?php if ( ! empty( $authors ) ) : ?>                        
							<select name="author_name" id="author_name">
								<option value="" > <?php esc_html_e( 'Select', 'book-library-search' ); ?> </option>
								<?php
								foreach ( $authors as $author ) {
									?>
									<option value="<?php echo esc_attr( $author->term_id ); ?>" > <?php echo esc_html( $author->name ); ?> </option>
									<?php
								}
								?>
							   
							</select>
							<?php endif; ?>
						</div>
					</div>
					<div class="filter_row">
						<div class="filter-item">
							<label for="publisher_name"><?php esc_html_e( 'Publisher:', 'book-library-search' ); ?></label>
							<?php if ( ! empty( $publishers ) ) : ?>                        
							<select name="publisher_name" id="publisher_name">
								<option value="" > <?php esc_html_e( 'Select', 'book-library-search' ); ?> </option>
								<?php
								foreach ( $publishers as $publisher ) {
									?>
									<option value="<?php echo esc_attr( $publisher->term_id ); ?>" > <?php echo esc_html( $publisher->name ); ?> </option>
									<?php
								}
								?>
							   
							</select>
							<?php endif; ?>
						</div>
						<div class="filter-item">
							<label for="book_rating"><?php esc_html_e( 'Rating:', 'book-library-search' ); ?></label>                       
							<select name="book_rating" id="book_rating">
								<option value=""  ><?php esc_html_e( 'Select', 'book-library-search' ); ?></option>
								<option value="1" ><?php esc_html_e( '1 star', 'book-library-search' ); ?></option>
								<option value="2" ><?php esc_html_e( '2 star', 'book-library-search' ); ?></option>
								<option value="3" ><?php esc_html_e( '3 star', 'book-library-search' ); ?></option>
								<option value="4" ><?php esc_html_e( '4 star', 'book-library-search' ); ?></option>
								<option value="5" ><?php esc_html_e( '5 star', 'book-library-search' ); ?></option>
							</select>
						</div>
					</div>
					<div class="filter_row range">
						<div class="filter-item">
														
							<label for="book_price">
								<?php esc_html_e( 'Book Price($): ', 'book-library-search' ); ?>                           
							</label>
							<div class="range-slider flat" data-ticks-position='top'>
								<input id="book_price_min" type="range" min="0" max="999" value="100">
								<output></output>
								<input  id="book_price_max" type="range" min="0" max="1000" value="500" >
								<output></output>
								<div class='range-slider__progress'></div>
							</div>
												
							
						</div>
					</div>
					<div class="filter_row">
						<button type="submit" class="search_book" id="search_book" p="1" >   <?php esc_html_e( 'Search ', 'book-library-search' ); ?> </button>
					</div>   
				</div>
			</div>
			
		</form>
		<?php
	}

	/**
	 * Book Search enqueue scripts.
	 *
	 * @return void
	 */
	public function bls_book_search_enqueue_assets() : void {

		$plugin_name = basename( BOOK_LIBRARY_SEARCH_DIR );
		// Enqueue style book search.
		wp_register_style(
			'book-search-style',
			plugins_url( $plugin_name . '/src/styles/book-search.css' ),
			array(),
			filemtime( BOOK_LIBRARY_SEARCH_DIR . '/src/styles/book-search.css' )
		);
		wp_enqueue_style( 'book-search-style' );

		// Enqueue script for book search.
		wp_register_script( 'book-search-script', plugins_url( $plugin_name . '/src/scripts/book-search.js' ), array(), filemtime( BOOK_LIBRARY_SEARCH_DIR . '/src/scripts/book-search.js' ), true );
		wp_localize_script(
			'book-search-script',
			'ajaxload_params',
			array(
				'ajax_url' => site_url() . '/wp-admin/admin-ajax.php',
				'nonce'    => wp_create_nonce( 'ajax-nonce' ),
			)
		);

	}

	/**
	 * Fetch books by title.
	 *
	 * @param string   $where where condition.
	 * @param WP_Query $wp_query WP_Query object.
	 * @return string.
	 */
	public function bls_search_book_by_title( $where, $wp_query ) {
		global $wpdb;
		if ( ! empty( $wp_query ) && $wp_query->get( 'search_book' ) ) {
			$search_book = $wp_query->get( 'search_book' );
			$where      .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $search_book ) ) . '%\'';
		}
		return $where;
	}

	/**
	 * Display book start rating.
	 *
	 * @param int $rating star rating count.
	 *
	 * @return void.
	 */
	public function bls_display_star_rating( $rating ) : void {
		if ( ! empty( $rating ) ) {
			for ( $i = 1; $i <= 5; $i++ ) {
				if ( $i <= (int) $rating ) {
					echo '<span class="dashicons dashicons-star-filled"></span>';
				} else {
					echo '<span class="dashicons dashicons-star-empty"></span>';
				}
			}
		} else {
			for ( $i = 1; $i <= 5; $i++ ) {
				echo '<span class="dashicons dashicons-star-empty"></span>';
			}
		}
	}
}

$bls_book_search_shortcode = new BLS_BOOK_SEARCH_SHORTCODE();
