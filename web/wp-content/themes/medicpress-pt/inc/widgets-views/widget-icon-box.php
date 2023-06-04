<?php echo $args['before_widget']; ?>

	<?php if ( ! empty( $instance['btn_link'] ) ) : ?>
		<a class="icon-box<?php echo empty( $instance['featured'] ) ? '' : '  icon-box--featured'; ?>" href="<?php echo esc_url( $instance['btn_link'] ); ?>" target="<?php echo esc_attr( $instance['target'] ); ?>">
	<?php else : ?>
		<div class="icon-box<?php echo empty( $instance['featured'] ) ? '' : '  icon-box--featured'; ?>">
	<?php endif; ?>

		<i class="fa  <?php echo esc_attr( $instance['icon'] ); ?>"></i>
		<p class="icon-box__title"><?php echo wp_kses_post( $instance['title'] ); ?></p>
		<p class="icon-box__subtitle"><?php echo wp_kses_post( $instance['text'] ); ?></p>

	<?php if ( $instance['btn_link'] ) : ?>
		</a>
	<?php else : ?>
		</div>
	<?php endif; ?>

<?php echo $args['after_widget']; ?>
