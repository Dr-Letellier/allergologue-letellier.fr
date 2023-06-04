<?php echo $args['before_widget']; ?>

	<?php if ( $instance['block'] ) : ?>
		<?php foreach ( $posts as $post ) : ?>
			<div class="latest-news  latest-news--<?php echo esc_attr( $instance['type'] ); ?>">
				<?php if ( isset( $post['image_url'] ) ) : ?>
					<a href="<?php echo esc_url( $post['link'] ); ?>" class="latest-news__image-link">
						<img src="<?php echo esc_url( $post['image_url'] ); ?>" width="<?php echo esc_attr( $post['image_width'] ); ?>" height="<?php echo esc_attr( $post['image_height'] ); ?>" srcset="<?php echo esc_attr( $post['srcset'] ); ?>" sizes="(min-width: 1200px) 350px, (min-width: 992px) 290px, (min-width: 768px) 690px, (min-width: 576px) 510px, calc(100vw - 30px)" class="latest-news__image  wp-post-image" alt="<?php echo esc_attr( $post['title'] ); ?>">
					</a>
				<?php endif; ?>

				<div class="latest-news__content">
					<div class="latest-news__category-container">
						<?php echo get_the_category_list( ', ', '', $post['id'] ); ?>
					</div>
					<h2 class="latest-news__title"><a href="<?php echo esc_attr( $post['link'] ); ?>"><?php echo wp_kses_post( $post['title'] ); ?></a></h2>
					<p class="latest-news__excerpt">
						<?php echo wp_kses_post( wp_trim_words( $post['excerpt'], 20 ) ); ?>
					</p>
					<?php
						$tags = wp_get_post_tags( $post['id'] );
						if (! empty( $tags ) ) :
					?>
						<div class="latest-news__tags-container">
							<?php foreach ( $tags as $wp_term ) : ?>
								<a class="latest-news__tag" href="<?php echo get_tag_link( $wp_term->term_id ); ?>"><?php echo esc_html( $wp_term->name ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $instance['more_news'] ) ) : ?>
						<a href="<?php echo esc_url( $instance['link_to_more_news'] ); ?>" class="latest-news__more-news">
							<?php echo wp_kses_post( $text['more_news'] ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	<?php else : ?>
		<div class="latest-news__container">
			<?php foreach ( $posts as $post ) : ?>
				<a href="<?php echo esc_url( $post['link'] ); ?>" class="latest-news  latest-news--<?php echo esc_attr( $instance['type'] ); ?>">
					<div class="latest-news__content">
						<time class="latest-news__date" datetime="<?php echo esc_attr( $post['full_date_time'] ); ?>">
							<?php echo esc_html( $post['full_date'] ); ?>
						</time>
						<h2 class="latest-news__title"><?php echo wp_kses_post( $post['title'] ); ?></h2>
					</div>
				</a>
			<?php endforeach; ?>

			<?php if ( ! empty( $instance['more_news'] ) ) : ?>
				<a href="<?php echo esc_url( $instance['link_to_more_news'] ); ?>" class="latest-news  latest-news--more-news">
					<?php echo wp_kses_post( $text['more_news'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php echo $args['after_widget']; ?>
