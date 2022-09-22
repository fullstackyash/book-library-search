<?php
/**
 * The template for displaying all book posts.
 *
 * @package book-library-search
 */

get_header(); ?>
 
	<div id="primary" class="book-details">
		<main id="main" class="site-main" role="main">
 
		<?php
		// Start the loop.
		while ( have_posts() ) :
			the_post();
			?>
			<header class="entry-header alignwide">
				<?php
				the_post_thumbnail( 'medium' );
				the_title( '<h1 class="entry-title">', '</h1>' );
				?>
											
			</header><!-- .entry-header -->
			<hr />
			
			<div class="entry-content">
			<?php

				the_content();
				$book_authors    = wp_get_object_terms( get_the_ID(), AUTHOR_TAXONOMY );
				$book_publishers = wp_get_object_terms( get_the_ID(), PUBLISHER_TAXONOMY );
				$book_price      = get_post_meta( get_the_ID(), 'book_price', true );
				$book_rating     = get_post_meta( get_the_ID(), 'book_rating', true );
			if ( ! empty( $book_authors ) ) :
				?>
					<div class="authors">
						<h4><?php esc_html_e( 'Authors' ); ?></h4>
						<hr />
						<ul>
						<?php
						foreach ( $book_authors as $book_author ) {
							?>
							<li><a href="<?php echo esc_url( get_term_link( $book_author->slug, AUTHOR_TAXONOMY ) ); ?>" ><?php echo esc_html( $book_author->name ); ?></a></li>
						<?php } ?>
						</ul>          
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $book_publishers ) ) : ?>
					<div class="publishers">
						<h4><?php esc_html_e( 'Publishers' ); ?></h4>
						<hr />
						<ul>
						<?php
						foreach ( $book_publishers as $book_publisher ) {
							?>
							<li><a href="<?php echo esc_url( get_term_link( $book_publisher->slug, PUBLISHER_TAXONOMY ) ); ?>" ><?php echo esc_html( $book_publisher->name ); ?></a></li>
						<?php } ?>
						</ul>          
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $book_rating ) ) : ?>
					<div class="book_rating">
						<h4><?php esc_html_e( 'Book Rating' ); ?></h4>
						<hr />
						<?php
						$bls_book_search_shortcode = new BLS_BOOK_SEARCH_SHORTCODE();
						$bls_book_search_shortcode->bls_display_star_rating( $book_rating );
						?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $book_price ) ) : ?>
					<div class="book_price">
						<h4><?php esc_html_e( 'Book Price' ); ?></h4>
						<hr />
						<?php echo esc_html( '$' . $book_price ); ?>
					</div>
				<?php endif; ?>
			
			</div>    
			<?php

			// End the loop.
		endwhile;
		?>
 
		</main><!-- .Site-main. -->
	</div><!-- .Content-area -->
 
<?php
get_footer();
