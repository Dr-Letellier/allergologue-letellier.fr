<?php echo $args['before_widget']; ?>

	<div class="person-profile  h-card">
		<?php if ( ! empty( $instance['image'] ) ) : ?>
		<img class="person-profile__image  wp-post-image" src="<?php echo esc_url( $instance['image'] ); ?>" alt="<?php echo esc_attr( $text['picture_of'] ) . ' ' . esc_attr( $instance['name'] ); ?>">
		<?php endif; ?>
		<?php if ( ! empty( $instance['carousel'] ) ) : ?>
			<?php
				// Parameters for the slick carousel slider.
				$slick_data = array(
					'adaptiveHeight' => true,
					'prevArrow'      => '<button type="button" class="slick-prev  slick-arrow"><span class="screen-reader-text">' . esc_html__( 'Previous', 'medicpress-pt' ) . '</span><i class="fa fa-arrow-left" aria-hidden="true"></i></button>',
					'nextArrow'      => '<button type="button" class="slick-next  slick-arrow"><span class="screen-reader-text">' . esc_html__( 'Next', 'medicpress-pt' ) . '</span><i class="fa fa-arrow-right" aria-hidden="true"></i></button>',
				);
			?>
			<div class="person-profile__carousel<?php echo 1 < count( $instance['carousel'] ) ? '  js-person-profile-initialize-carousel' : ''; ?>" <?php echo 1 < count( $instance['carousel'] ) ? "data-slick='" . wp_json_encode( $slick_data ) . "'" : ""; ?>>
				<?php foreach ( $instance['carousel'] as $carousel_item ) : ?>
					<div class="person-profile__carousel-item">
						<?php if ( 'image' === $carousel_item['type'] && ! empty( $carousel_item['url'] ) ) : ?>
							<img class="person-profile__image  wp-post-image  u-photo" src="<?php echo esc_url( $carousel_item['url'] ); ?>" alt="<?php echo esc_attr( $text['picture_of'] ) . ' ' . esc_attr( $instance['name'] ); ?>">
						<?php elseif ( 'video' === $carousel_item['type'] && ! empty( $carousel_item['url'] ) ) : ?>
							<?php
								$video_class = '';
								if ( strstr( $carousel_item['url'], 'youtube.com/' ) ) {
									$video_class = '  js-carousel-item-yt-video';
								}
								elseif ( strstr( $carousel_item['url'], 'vimeo.com/' ) ) {
									$video_class = '  js-carousel-item-vimeo-video';
								}
							?>
							<div class="person-profile__carousel-item--video<?php echo esc_attr( $video_class ); ?>">
								<?php
									echo wp_oembed_get(
										esc_url( $carousel_item['url'] ),
										array(
											'api' => '1',
											'player_id' => uniqid( 'pt-sc-pp-video-' ),
										)
									);
								?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="person-profile__container">
			<div class="person-profile__basic-info">
				<?php if ( ! empty( $instance['name'] ) ) : ?>
					<h4 class="person-profile__name  p-name">
					<?php if ( ! empty( $instance['name_link'] ) ) : ?>
						<a href="<?php echo esc_url( $instance['name_link'] ); ?>">
					<?php endif; ?>
					<?php echo esc_html( $instance['name'] ); ?>
					<?php if ( ! empty( $instance['name_link'] ) ) : ?>
						</a>
					<?php endif; ?>
					</h4>
				<?php endif; ?>

				<?php if ( ! empty( $instance['label'] ) ) : ?>
					<div class="person-profile__label">
						<?php echo esc_html( $instance['label'] ); ?>
					</div>
				<?php endif; ?>

			</div>

			<?php if ( ! empty( $instance['location'] ) ) : ?>
				<div class="h4 person-profile__location">
					<?php echo esc_html( $instance['location'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $instance['specific_location'] ) ) : ?>
				<div class="person-profile__specific-location">
					<?php echo esc_html( $instance['specific_location'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $instance['icon_list_items'] ) ) : ?>
				<div class="person-profile__icon-list  icon-list">
					<?php foreach ( $instance['icon_list_items'] as $item ) : ?>
						<<?php echo empty( $item['link'] ) ? 'div' : 'a href="' . esc_url( $item['link'] ) . '"'; ?> class="icon-list__item  icon-list__item--<?php echo empty( $item['is-featured'] ) ? 'default' : 'featured'; ?>">
							<i class="fa  <?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></i>
							<?php if ( ! empty( $item['is-featured'] ) ) : ?>
							<div class="icon-list__content">
								<div class="icon-list__description">
									<?php echo esc_html( $item['description'] ); ?>
								</div>
							<?php endif; ?>
								<div class="icon-list__text">
									<?php echo esc_html( $item['text'] ); ?>
								</div>
							<?php if ( ! empty( $item['is-featured'] ) ) : ?>
							</div>
							<?php endif; ?>
						</<?php echo empty( $item['link'] ) ? 'div' : 'a'; ?>>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $instance['cta_text'] ) ) : ?>
				<a class="btn  btn-secondary  btn-block  person-profile__button" href="<?php echo esc_url( $instance['cta_link'] ); ?>" target="<?php echo empty( $instance['cta_new_tab'] ) ? '_self' : '_blank'; ?>"><?php echo esc_html( $instance['cta_text'] ); ?></a>
			<?php endif; ?>

		</div>
	</div>

<?php echo $args['after_widget']; ?>
