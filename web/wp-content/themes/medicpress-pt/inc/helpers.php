<?php
/**
 * Helper functions
 *
 * @package medicpress-pt
 */

/**
 * MedicPressHelpers class with static methods
 */
class MedicPressHelpers {
	/**
	 * Get logo dimensions from the db
	 *
	 * @param  string $theme_mod theme mod where the array with width and height is saved.
	 * @return mixed             string or FALSE
	 */
	static function get_logo_dimensions( $theme_mod = 'logo_dimensions_array' ) {
		$width_height_array = get_theme_mod( $theme_mod );

		if ( is_array( $width_height_array ) && 2 === count( $width_height_array ) ) {
			return sprintf( ' width="%d" height="%d" ', absint( $width_height_array['width'] ), absint( $width_height_array['height'] ) );
		}

		return '';
	}


	/**
	 * The comments_number() does not use _n function, here we are to fix that
	 */
	static function pretty_comments_number() {
		global $post;
		printf(
			/* translators: %s represents a number */
			_n( '%s Comment', '%s Comments', get_comments_number(), 'medicpress-pt' ), number_format_i18n( get_comments_number() )
		);
	}


	/**
	 * Check if WooCommerce is active
	 *
	 * @return boolean
	 */
	static function is_woocommerce_active() {
		return class_exists( 'Woocommerce' );
	}


	/**
	 * Return array of the number which represent the layout of the footer.
	 *
	 * @return array
	 */
	static function footer_widgets_layout_array() {
		$layout = get_theme_mod( 'footer_widgets_layout', '[4,6,8]' );
		$layout = json_decode( $layout );

		if ( is_array( $layout ) && ! empty( $layout ) ) {
			$spans = array( (int) $layout[0] );

			for ( $i = 0; $i < ( count( $layout ) - 1 ); $i++ ) {
				$spans[] = $layout[ $i + 1 ] - $layout[ $i ];
			}

			$spans[] = 12 - $layout[ $i ];

			return $spans;
		}
		elseif ( 1 === $layout ) { // Single column.
			return array( '12' );
		}

		// Default: disable footer.
		return array();
	}

	/**
	 * Return url with Google Fonts.
	 *
	 * @see https://github.com/grappler/wp-standard-handles/blob/master/functions.php
	 * @return string Google fonts URL for the theme.
	 */
	static function google_web_fonts_url() {
		$fonts_url = '';
		$fonts     = array();
		$subsets   = array( 'latin' );

		$fonts = apply_filters( 'medicpress_pre_google_web_fonts', $fonts );

		foreach ( $fonts as $key => $value ) {
			$fonts[ $key ] = $key . ':' . implode( ',', $value );
		}

		/* translators: To add an additional character subset specific to your language, translate this to 'greek', 'cyrillic', 'devanagari' or 'vietnamese'. Do not translate into your own language. */
		$subset = esc_html_x( 'no-subset', 'Add new subset (greek, cyrillic, devanagari, vietnamese)', 'medicpress-pt' );
		if ( 'cyrillic' == $subset ) {
			array_push( $subsets, 'cyrillic', 'cyrillic-ext' );
		} elseif ( 'greek' == $subset ) {
			array_push( $subsets, 'greek', 'greek-ext' );
		} elseif ( 'devanagari' == $subset ) {
			array_push( $subsets, 'devanagari' );
		} elseif ( 'vietnamese' == $subset ) {
			array_push( $subsets, 'vietnamese' );
		}

		$subsets = apply_filters( 'medicpress_subsets_google_web_fonts', $subsets );

		if ( $fonts ) {
			$fonts_url = add_query_arg(
				array(
					'family' => urlencode( implode( '|', $fonts ) ),
					'subset' => urlencode( implode( ',', array_unique( $subsets ) ) ),
				),
				'//fonts.googleapis.com/css'
			);
		}

		return apply_filters( 'medicpress_google_web_fonts_url', $fonts_url );
	}


	/**
	 * Prepare the srcset attribute value.
	 *
	 * @param int   $img_id ID of the image.
	 * @param array $sizes array of the image sizes. Example: $sizes = array( 'jumbotron-slider-s', 'jumbotron-slider-l' );.
	 * @uses http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src
	 * @return string
	 */
	static function get_image_srcset( $img_id, $sizes ) {
		$srcset = array();

		foreach ( $sizes as $size ) {
			$img = wp_get_attachment_image_src( absint( $img_id ), $size );
			$srcset[] = sprintf( '%s %sw', $img[0], $img[1] );
		}

		return implode( ', ' , $srcset );
	}


	/**
	 * Create a style for the HTML attribute from the array of the CSS properties
	 *
	 * @param array $attrs array with CSS settings.
	 * @return string of the background style (CSS)
	 */
	static function create_background_style_attr( $attrs ) {
		$bg_style = array();

		if ( ! empty( $attrs ) ) {
			foreach ( $attrs as $key => $value ) {
				$trimmed_val = trim( $value );
				if ( ! empty( $trimmed_val ) ) {
					if ( 'background-image' === $key ) {
						$bg_style[] = $key . ': url(' . esc_url( $trimmed_val ) . ')';
					}
					elseif ( 'background-color' === $key && ! array_key_exists( 'background-image', $attrs ) ) {
						// To overwrite the pattern set in customizer.
						$bg_style[] = 'background: ' . $trimmed_val;
					}
					else {
						$bg_style[] = $key . ': ' . $trimmed_val;
					}
				}
			}
		}

		if ( empty( $bg_style ) ) {
			return '';
		}
		else {
			return join( '; ', $bg_style );
		}

	}


	/**
	 * Custom wp_list_comments callback (template)
	 *
	 * @param obj   $comment WP comment object.
	 * @param array $args comment arguments.
	 * @param int   $depth WP comment depth.
	 */
	static function custom_comment( $comment, $args, $depth ) {
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>

		<<?php echo tag_escape( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( array( 'clearfix', empty( $args['has_children'] ) ? '' : 'parent' ) ); ?>>
			<div class="comment__content">
				<div class="comment__inner">
					<div class="comment__avatar">
						<?php if ( 0 != $args['avatar_size'] ) { echo get_avatar( $comment, $args['avatar_size'] ); } ?>
					</div>
					<div class="comment__metadata">
						<?php comment_reply_link( array_merge( $args, array(
							'depth' => $depth,
							'before' => '',
						) ) ); ?>
						<?php edit_comment_link( esc_html__( 'Edit', 'medicpress-pt' ), '' ); ?>
					</div>
					<cite class="comment__author  vcard">
						<?php echo get_comment_author_link(); ?>
					</cite>
					<time class="comment__date" datetime="<?php comment_time( 'c' ); ?>">
						<i class="fa fa-calendar-o" aria-hidden="true"></i>  <?php comment_date( 'F j, Y' ); ?>
					</time>
					<div class="comment__text">
						<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment__awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.' , 'medicpress-pt' ); ?></p>
						<?php endif; ?>

						<?php comment_text(); ?>
					</div>
				</div>

		<?php
	}


	/**
	 * Helper function to get terms (categories) for custom post types
	 *
	 * @param  int    $post_id ID of the post.
	 * @param  string $taxonomy taxonomy name.
	 * @return array
	 */
	public static function get_custom_categories( $post_id, $taxonomy ) {
		$out   = array();
		$terms = get_the_terms( $post_id, $taxonomy );

		if ( ! is_array( $terms ) ) {
			return array();
		}

		foreach ( $terms as $term ) {
			$out[ $term->slug ] = $term->name;
		}

		return $out;
	}


	/**
	 * Get the post excerpt. If post_excerpt data is defined use that, otherwise
	 * trim down the content to the proper size.
	 *
	 * @param string $post_excerpt post excerpt text.
	 * @param string $post_content post content text.
	 * @param int    $excerpt_length length of the excerpt.
	 * @return string
	 */
	public static function get_post_excerpt( $post_excerpt, $post_content, $excerpt_length = 55 ) {
		$excerpt      = empty( $post_excerpt ) ? $post_content : $post_excerpt;
		$excerpt      = strip_shortcodes( $excerpt );
		$excerpt      = wp_strip_all_tags( $excerpt );
		$excerpt      = str_replace( ']]>', ']]&gt;', $excerpt );
		$excerpt_more = apply_filters( 'excerpt_more', '[&hellip;]' );
		$excerpt      = wp_trim_words( $excerpt, $excerpt_length, $excerpt_more );

		return $excerpt;
	}


	/**
	 * Return correct path to the file (check child theme first)
	 *
	 * @param string $relative_file_path relative path to the file.
	 * @return string, absolute path of the correct file.
	 */
	public static function get_correct_file_path( $relative_file_path ) {
		if ( file_exists( get_stylesheet_directory() . $relative_file_path ) ) {
			return get_stylesheet_directory() . $relative_file_path;
		}
		elseif ( file_exists( get_template_directory() . $relative_file_path ) ) {
			return get_template_directory() . $relative_file_path;
		}
		else {
			return false;
		}
	}


	/**
	 * Require the correct file with require_once (checks child theme first)
	 *
	 * @param string $relative_file_path relative path to the file.
	 */
	public static function load_file( $relative_file_path ) {
		require_once self::get_correct_file_path( $relative_file_path );
	}


	/**
	 * Bound an integer between two numbers
	 *
	 * @param int $input number to evaluate.
	 * @param int $min minimal allowed number.
	 * @param int $max maximal allowed number.
	 */
	public static function bound( $input, $min, $max ) {
		return min( max( $input, $min ), $max );
	}


	/**
	 * Create appropriate page header background style.
	 *
	 * @param string $bg_id page ID for which the header style should be build for.
	 */
	public static function page_header_background_style( $bg_id ) {

		$style_array = array();

		if ( get_field( 'background_image', $bg_id ) ) {
			$style_array = array(
				'background-image'      => get_field( 'background_image', $bg_id ),
				'background-position'   => get_field( 'background_image_horizontal_position', $bg_id ) . ' ' . get_field( 'background_image_vertical_position', $bg_id ),
				'background-repeat'     => get_field( 'background_image_repeat', $bg_id ),
				'background-attachment' => get_field( 'background_image_attachment', $bg_id ),
			);
		}

		$style_array['background-color'] = get_field( 'background_color', $bg_id );

		// Create the style tag to use in the .page-header element.
		return self::create_background_style_attr( $style_array );
	}


	/**
	 * Return the correct page ID.
	 * WPML support.
	 *
	 * @param mixed [string|int] $original_id Original ID.
	 */
	public static function get_page_id( $original_id ) {
		if ( function_exists( 'icl_object_id' ) ) {
			return icl_object_id( $original_id, 'page', true );
		}

		return $original_id;
	}


	/**
	 * Return true if the template used is a slider template:
	 * 'template-front-page-slider.php' or 'template-front-page-slider-alt.php'.
	 */
	public static function is_slider_template() {
		if ( is_page_template( array( 'template-front-page-slider.php', 'template-front-page-slider-alt.php' ) ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Get the author posts link with author display name (also works outside the WP loop).
	 *
	 * @param string $class String of classes that will be added to the link.
	 */
	public static function get_author_posts_link( $class = '' ) {
		global $post;

		$author_id   = $post->post_author;
		$author_url  = get_author_posts_url( $author_id );
		$author_name = get_the_author_meta( 'display_name', $author_id );

		ob_start();
		?>
			<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $author_url ); ?>" title="<?php echo esc_html__( 'Posted by', 'medicpress-pt' ) . ' ' . esc_html( $author_name ); ?>" rel="author"><?php echo esc_html( $author_name ); ?></a>
		<?php
		$output = ob_get_clean();

		return $output;
	}


	/**
	 * Get the featured page customizer settings data or return false if it's set to 'none'.
	 *
	 * @return mixed Boolean if set to 'none' or array with the data.
	 */
	public static function get_featured_page_data() {
		$data = array(
			'url'    => '',
			'target' => '',
			'title'  => '',
		);

		$selected_page = get_theme_mod( 'featured_page_select', 'none' );

		// If the featured page is not set in customizer return false.
		if ( 'none' === $selected_page ) {
			return false;
		}

		// Get the target data.
		$data['target'] = get_theme_mod( 'featured_page_open_in_new_window', '' ) ? '_blank' : '_self';

		// Get the url and the title, depending on what settings are selected.
		if ( 'custom-url' === $selected_page ) {
			$data['title'] = get_theme_mod( 'featured_page_custom_text', 'Featured Page' );
			$data['url']   = get_theme_mod( 'featured_page_custom_url', '#' );
		}
		else {
			$selected_page = self::get_page_id( $selected_page );

			$data['title'] = get_the_title( absint( $selected_page ) );
			$data['url']   = get_permalink( absint( $selected_page ) );
		}

		return $data;
	}
}
