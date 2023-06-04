<?php echo $args['before_widget']; ?>

	<div class="page-box  page-box--<?php echo esc_attr( $instance['layout'] ); ?>">
		<?php
		if ( $page['thumbnail'] || ( function_exists( 'has_post_video' ) && has_post_video( $page['ID'] ) ) ) :
			if ( 'block' === $instance['layout'] ) :
				if ( function_exists( 'has_post_video' ) && has_post_video( $page['ID'] ) ) :
					echo get_the_post_thumbnail( $page['ID'] );
				else :
		?>
				<a class="page-box__picture" href="<?php echo esc_url( $page['link'] ); ?>">
					<img src="<?php echo esc_url( $page['image_url'] ); ?>" width="<?php echo esc_attr( $page['image_width'] ); ?>" height="<?php echo esc_attr( $page['image_height'] ); ?>" srcset="<?php echo esc_attr( $page['srcset'] ); ?>" sizes="(min-width: 1200px) 350px, (min-width: 992px) 290px, (min-width: 768px) 690px, (min-width: 576px) 510px, calc(100vw - 30px)" class="wp-post-image" alt="<?php echo esc_attr( $page['post_title'] ); ?>">
					<?php if ( ! empty( $instance['tag'] ) ) : ?>
						<div class="page-box__tag">
							<?php echo esc_html( $instance['tag'] ); ?>
						</div>
					<?php endif; ?>
				</a>
				<?php endif; ?>
		<?php else : ?>
			<a class="page-box__picture" href="<?php echo esc_url( $page['link'] ); ?>">
				<?php
				// Fix for Featured Video Plus plugin (where the video is not working when inline layout is set).
				// Inline layout will always have small pw-inline image.
				$attachment_image_id   = get_post_thumbnail_id( $page['ID'] );
				$attachment_image_data = wp_get_attachment_image_src( $attachment_image_id, $thumbnail_size );
				?>
					<img src="<?php echo esc_url( $attachment_image_data[0] ); ?>" width="<?php echo esc_attr( $attachment_image_data[1] ); ?>" height="<?php echo esc_attr( $attachment_image_data[2] ); ?>" alt="<?php echo esc_attr( $page['post_title'] ); ?>">
				<?php
				?>
			</a>
		<?php
			endif;
		endif;
		?>
		<div class="page-box__content">
			<h2 class="page-box__title"><a href="<?php echo esc_url( $page['link'] ); ?>"><?php echo wp_kses_post( $page['post_title'] ); ?></a></h2>
			<p class="page-box__text"><?php echo wp_kses_post( $page['post_excerpt'] ); ?></p>
			<?php if ( 'block' === $instance['layout'] && ! empty( $instance['show_read_more_link'] ) ) : ?>
				<a href="<?php echo esc_url( $page['link'] ); ?>" class="page-box__more-link"><?php echo esc_html( $instance['read_more_text'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>

<?php echo $args['after_widget']; ?>
