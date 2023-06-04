<?php
	echo $args['before_widget'];

	if ( ! empty( $instance['title'] ) ) {
		echo $args['before_title'] . esc_html( $instance['preped_title'] ) . $args['after_title'];
	}
?>

	<a class="brochure-box" href="<?php echo esc_url( $instance['brochure_url'] ); ?>" target="<?php echo ( ! empty( $instance['new_tab'] ) ) ? '_blank' : '_self'; ?>">
		<span class="brochure-box__icon"><i class="fa  <?php echo esc_attr( $instance['brochure_icon'] ); ?>"></i></span>
		<span class="brochure-box__text"><?php echo wp_kses_post( $instance['brochure_text'] ); ?></span>
	</a>

<?php echo $args['after_widget']; ?>
