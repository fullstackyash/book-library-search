<?php
/**
 * Register meta fields for book post type.
 *
 * @package book-library-search
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Class for registering the "book" custom post type meta fields.
 */
class  BLS_BOOK_META_FIELDS {


	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'bls_add_meta_box' ) );
		add_action( 'save_post', array( $this, 'bls_save_meta_data' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'bls_enqueue_assets' ) );
	}

	/**
	 * Adds the meta box container.
	 *
	 * @return void
	 */
	public function bls_add_meta_box() : void {
		add_meta_box(
			'book_settings',
			__( 'Book Settings', 'book-library-search' ),
			array( $this, 'bls_render_meta_box' ),
			BOOK_POST_TYPE,
			'side',
			'high'
		);
	}

	/**
	 * Render custom meta box.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function bls_render_meta_box( $post ) : void {

		// Add an nonce field for security.
		wp_nonce_field( 'bls_render_meta_box_action', 'bls_render_meta_box_nonce_field' );

		// Reterive current meta vales.
		$book_price  = get_post_meta( $post->ID, 'book_price', true );
		$book_rating = get_post_meta( $post->ID, 'book_rating', true );
		?>

		<div class="book_settings">
			<div class="price_slider">
				<p>
					<label for="book_price">
						<?php esc_html_e( 'Book Price: ', 'book-library-search' ); ?>
						<span>$</span><span id="price_value"></span>
					</label>
				</p>
				<p>    
					<input type="range" min="0" max="1000" value="<?php echo esc_attr( $book_price ); ?>" step="1" class="slider" id="book_price" name="book_price" />
				</p>    			
			</div>
			<div class="book_raiting">
				<p>
					<label for="book_rating">
							<?php esc_html_e( 'Book Rating: ', 'book-library-search' ); ?>
					</label>
				</p>
				<p>
					<select name="book_rating" id="book_rating">
						<option value=""  <?php selected( $book_rating, '' ); ?>><?php esc_html_e( 'None', 'book-library-search' ); ?></option>
						<option value="1" <?php selected( $book_rating, '1' ); ?>><?php esc_html_e( '1 star', 'book-library-search' ); ?></option>
						<option value="2" <?php selected( $book_rating, '2' ); ?>><?php esc_html_e( '2 star', 'book-library-search' ); ?></option>
						<option value="3" <?php selected( $book_rating, '3' ); ?>><?php esc_html_e( '3 star', 'book-library-search' ); ?></option>
						<option value="4" <?php selected( $book_rating, '4' ); ?>><?php esc_html_e( '4 star', 'book-library-search' ); ?></option>
						<option value="5" <?php selected( $book_rating, '5' ); ?>><?php esc_html_e( '5 star', 'book-library-search' ); ?></option>
					</select>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Save book meta field values when the post is saved.
	 *
	 * @param int     $post_id The ID of the post being saved.
	 * @param WP_Post $post The post object.
	 */
	public function bls_save_meta_data( $post_id, $post ) {

		/* bail out if this is an autosave. */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		/* Verify the nonce before proceeding. */
		if ( ! isset( $_POST['bls_render_meta_box_nonce_field'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bls_render_meta_box_nonce_field'] ), 'bls_render_meta_box_action' ) ) {
			return $post_id;
		}

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		if ( isset( $_POST['book_price'] ) ) {
			/* Get the posted data and sanitize it. */
			$price = sanitize_text_field( wp_unslash( $_POST['book_price'] ) );
			/* Add, Update, Delete Meta Values. */
			$this->bls_crud_post_meta( $post_id, 'book_price', $price );
		}
		if ( isset( $_POST['book_rating'] ) ) {
			/* Get the posted data and sanitize it. */
			$rating = sanitize_text_field( wp_unslash( $_POST['book_rating'] ) );
			/* Add, Update, Delete Meta Values. */
			$this->bls_crud_post_meta( $post_id, 'book_rating', $rating );
		}
	}


	/**
	 * Perform crud options for book meta.
	 *
	 * @param int    $post_id The ID of the post being saved.
	 * @param string $meta_key The meta key of field.
	 * @param string $new_meta_value The new meta value of field.
	 */
	public function bls_crud_post_meta( $post_id, $meta_key, $new_meta_value ) {

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' === $meta_value ) {
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );
		} elseif ( $new_meta_value && $new_meta_value !== $meta_value ) {  /* If the new meta value does not match the old value, update it. */
			update_post_meta( $post_id, $meta_key, $new_meta_value );
		} elseif ( '' === $new_meta_value && $meta_value ) { /* If there is no new meta value but an old value exists, delete it. */
			delete_post_meta( $post_id, $meta_key, $meta_value );
		}

	}

	/**
	 * Include plugin assets (js & css).
	 *
	 * @return void
	 */
	public function bls_enqueue_assets() {
		global $post_type;

		if ( BOOK_POST_TYPE !== $post_type ) {
			return;
		}

		$plugin_name = basename( BOOK_LIBRARY_SEARCH_DIR );
		wp_enqueue_script( 'book-lib-script', plugins_url( $plugin_name . '/src/scripts/book.js' ), array(), 1, true );
		wp_enqueue_style(
			'book-lib-style',
			plugins_url( $plugin_name . '/src/styles/book.css' ),
			array(),
			filemtime( BOOK_LIBRARY_SEARCH_DIR . '/src/styles/book.css' )
		);
	}

}

$bls_book_meta_fields = new BLS_BOOK_META_FIELDS();
