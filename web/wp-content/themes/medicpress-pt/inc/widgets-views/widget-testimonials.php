<?php
	// Parameters for the slick carousel slider in this widget.
	$slick_data = apply_filters( 'pt-medicpress/slick_carousel_testimonials_data', array(
		'slidesToShow'     => 3,
		'appendArrows'     => '#testimonials-container-' . esc_attr( $args['widget_id'] ) . ' .js-testimonials-navigation',
		'prevArrow'        => '<button type="button" class="slick-prev  slick-arrow"><span class="screen-reader-text">' . esc_html__( 'Previous', 'medicpress-pt' ) . '</span><i class="fa fa-arrow-left" aria-hidden="true"></i></button>',
		'nextArrow'        => '<button type="button" class="slick-next  slick-arrow"><span class="screen-reader-text">' . esc_html__( 'Next', 'medicpress-pt' ) . '</span><i class="fa fa-arrow-right" aria-hidden="true"></i></button>',
		'responsive'       => array(
			array(
				'breakpoint' => 992,
				'settings'   => array(
					'slidesToShow' => 1,
				),
			),
		),
	) );
?>

<?php echo $args['before_widget']; ?>

	<div class="testimonials__container" id="testimonials-container-<?php echo esc_attr( $args['widget_id'] ); ?>">

	<?php echo $args['before_title'] . wp_kses_post( $instance['title'] ) . $args['after_title']; ?>

		<div class="testimonials  js-testimonials-initialize-carousel" data-slick='<?php echo wp_json_encode( $slick_data ); ?>'>
			<?php foreach ( $testimonials as $testimonial ) : ?>
				<div class="testimonial__container">
					<div class="testimonial">
						<blockquote class="testimonial__quote">
							<?php echo wp_kses_post( $testimonial['quote'] ); ?>
						</blockquote>
						<div class="testimonial__author-container">
							<div class="testimonial__author-info">
								<!-- Author Name -->
								<cite class="testimonial__author">
									<?php echo esc_html( $testimonial['author'] ); ?>
								</cite>
								<!-- Author Description -->
								<?php if ( ! empty( $testimonial['author_description'] ) ) : ?>
									<div class="testimonial__author-description">
										<?php echo wp_kses_post( $testimonial['author_description'] ); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="testimonials__navigation  js-testimonials-navigation"></div>

	</div>

<?php echo $args['after_widget']; ?>
