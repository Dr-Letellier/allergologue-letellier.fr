<?php
/**
 * The template for displaying Comments.
 *
 * @package medicpress-pt
 */

?>
<?php if ( ! post_password_required() ) : ?>
	<div id="comments" class="comments  comments-post-<?php the_ID(); ?>">
		<?php if ( have_comments() || comments_open() || pings_open() ) : ?>

			<h2 class="comments__heading"><?php esc_html_e( 'Leave a Comment' , 'medicpress-pt' ); ?></h2>
			<p class="comments__counter">(<?php MedicPressHelpers::pretty_comments_number(); ?>)</p>

			<?php if ( have_comments() ) : ?>

				<div class="comments__container">
					<?php wp_list_comments( array( 'callback' => 'MedicPressHelpers::custom_comment', 'avatar_size' => '75' ) ); ?>
				</div>

				<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through. ?>
					<nav id="comment-nav-below" class="text-xs-center  text-uppercase" role="navigation">
						<h3 class="assistive-text"><?php esc_html_e( 'Comment Navigation' , 'medicpress-pt' ); ?></h3>
						<div class="nav-previous  pull-left"><?php previous_comments_link( esc_html__( '&larr; Older Comments' , 'medicpress-pt' ) ); ?></div>
						<div class="nav-next  pull-right"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;' , 'medicpress-pt' ) ); ?></div>
					</nav>
				<?php endif; ?>

				<?php
				// If there are no comments and comments are closed, let's leave a note.
				// But we only want the note on posts and pages that had comments in the first place.
				if ( ! comments_open() && get_comments_number() ) :
				?>
					<p class="nocomments"><?php esc_html_e( 'Comments for this post are closed.', 'medicpress-pt' ); ?></p>
				<?php
				endif;
				?>

			<?php endif; // 'Have comments' check end. ?>

			<?php
				$medicpress_commenter = wp_get_current_commenter();
				$medicpress_req       = get_option( 'require_name_email' );
				$medicpress_req_html  = $medicpress_req ? '<span class="required theme-clr">*</span>' : '';
				$medicpress_req_aria  = $medicpress_req ? ' aria-required="true" required' : '';

				// The author field has a opening row div and the url field has the closing one, so that all three fields are in the same row.
				$medicpress_fields    = array(
					'author' => sprintf( '<div class="row"><div class="col-xs-12  col-lg-4  form-group"><label class="screen-reader-text" for="author">%1$s%2$s</label><input id="author" name="author" type="text" value="%3$s" placeholder="' . esc_html__( 'Name' , 'medicpress-pt' ) . '" class="form-control" %4$s /></div>',
						esc_html__( 'First and Last name', 'medicpress-pt' ),
						$medicpress_req_html,
						esc_attr( $medicpress_commenter['comment_author'] ),
						$medicpress_req_aria
					),
					'email'  => sprintf( '<div class="col-xs-12  col-lg-4  form-group"><label class="screen-reader-text" for="email">%1$s%2$s</label><input id="email" name="email" type="email" value="%3$s" placeholder="' . esc_html__( 'E-mail' , 'medicpress-pt' ) . '" class="form-control" %4$s /></div>',
						esc_html__( 'E-mail Address', 'medicpress-pt' ),
						$medicpress_req_html,
						esc_attr( $medicpress_commenter['comment_author_email'] ),
						$medicpress_req_aria
					),
					'url'    => sprintf( '<div class="col-xs-12  col-lg-4  form-group"><label class="screen-reader-text" for="url">%1$s</label><input id="url" name="url" type="url" value="%2$s" placeholder="' . esc_html__( 'Website' , 'medicpress-pt' ) . '" class="form-control" /></div></div>',
						esc_html__( 'Website', 'medicpress-pt' ),
						esc_attr( $medicpress_commenter['comment_author_url'] )
					),
				);

				$medicpress_comments_args = array(
					'fields'        => $medicpress_fields,
					'id_submit'     => 'comments-submit-button',
					'class_submit'  => 'submit  btn  btn-secondary',
					'comment_field' => sprintf( '<div class="row"><div class="col-xs-12  form-group"><label for="comment" class="screen-reader-text">%1$s%2$s</label><textarea id="comment" name="comment" class="form-control" placeholder="' . esc_html__( 'New Comment' , 'medicpress-pt' ) . '" rows="5" aria-required="true"></textarea></div></div>',
						esc_html_x( 'Your comment', 'noun', 'medicpress-pt' ),
						$medicpress_req_html
					),
					'title_reply'   => '',
				);

				// See: https://developer.wordpress.org/reference/functions/comment_form/.
				comment_form( $medicpress_comments_args );

		else : ?>
		<div class="comments__closed">
			<?php esc_html_e( 'Comments for this post are closed.' , 'medicpress-pt' ); ?>
		</div>
	<?php
		endif;
	?>

	</div>
<?php endif; ?>
