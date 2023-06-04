<?php
/**
 * Notice Widget
 *
 * @package medicpress-pt
 */

if ( ! class_exists( 'PW_Notice' ) ) {
	class PW_Notice extends WP_Widget {
		private $widget_id_base, $widget_class, $widget_name, $widget_description;
		private $font_awesome_icons_list;

		public function __construct() {
			// Basic widget settings.
			$this->widget_id_base     = 'notice';
			$this->widget_class       = 'widget-notice';
			$this->widget_name        = esc_html__( 'Notice', 'medicpress-pt' );
			$this->widget_description = esc_html__( 'A notice with a tag and title.', 'medicpress-pt' );

			parent::__construct(
				'pw_' . $this->widget_id_base,
				sprintf( 'ProteusThemes: %s', $this->widget_name ),
				array(
					'description' => $this->widget_description,
					'classname'   => $this->widget_class,
				)
			);

			// A list of icons to choose from in the widget backend.
			$this->font_awesome_icons_list = apply_filters(
				'pw/notice_widget_fa_icons_list',
				array(
					'fa-home',
					'fa-phone',
					'fa-warning',
					'fa-info',
					'fa-question',
					'fa-info-circle',
					'fa-question-circle',
					'fa-envelope-o',
					'fa-envelope',
					'fa-map-marker',
					'fa-users',
					'fa-female',
					'fa-male',
					'fa-inbox',
					'fa-compass',
					'fa-laptop',
					'fa-money',
				)
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			$title        = empty( $instance['title'] ) ? '' : $instance['title'];
			$tag_text     = empty( $instance['tag_text'] ) ? '' : $instance['tag_text'];
			$tag_icon     = empty( $instance['tag_icon'] ) ? '' : $instance['tag_icon'];
			$tag_bg_color = empty( $instance['tag_bg_color'] ) ? '' : $instance['tag_bg_color'];
			$align        = empty( $instance['align'] ) ? 'left' : $instance['align'];

			// Set alignment class.
			$alignment_class = 'center' === $align ? '  important-notice--center' : '';

			echo $args['before_widget'];
		?>
			<div class="important-notice<?php echo esc_attr( $alignment_class ); ?>">
				<div class="important-notice__label"<?php echo ! empty( $tag_bg_color ) ? ' style="background-color: ' . esc_attr( $tag_bg_color ) . ';"' : ''; ?>>
					<i class="fa  <?php echo esc_attr( $tag_icon ); ?>" aria-hidden="true"></i>
					<div class="important-notice__label-text">
						<?php echo esc_html( $tag_text ); ?>
					</div>
				</div>
				<div class="important-notice__text">
						<?php echo wp_kses_post( $title ); ?>
				</div>
			</div>
		<?php
			echo $args['after_widget'];
		}


		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options.
		 * @param array $old_instance The previous options.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['title']        = empty( $new_instance['title'] ) ? '' : wp_kses_post( $new_instance['title'] );
			$instance['tag_text']     = empty( $new_instance['tag_text'] ) ? '' : sanitize_text_field( $new_instance['tag_text'] );
			$instance['tag_icon']     = empty( $new_instance['tag_icon'] ) ? '' : sanitize_text_field( $new_instance['tag_icon'] );
			$instance['tag_bg_color'] = empty( $new_instance['tag_bg_color'] ) ? '' : sanitize_text_field( $new_instance['tag_bg_color'] );
			$instance['align']        = empty( $new_instance['align'] ) ? 'left' : sanitize_text_field( $new_instance['align'] );

			return $instance;
		}


		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options.
		 */
		public function form( $instance ) {
			$title        = empty( $instance['title'] ) ? '' : $instance['title'];
			$tag_text     = empty( $instance['tag_text'] ) ? '' : $instance['tag_text'];
			$tag_icon     = empty( $instance['tag_icon'] ) ? '' : $instance['tag_icon'];
			$tag_bg_color = empty( $instance['tag_bg_color'] ) ? '' : $instance['tag_bg_color'];
			$align        = empty( $instance['align'] ) ? 'left' : $instance['align'];

		?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'medicpress-pt' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tag_text' ) ); ?>"><?php esc_html_e( 'Tag text:', 'medicpress-pt' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tag_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tag_text' ) ); ?>" type="text" value="<?php echo esc_attr( $tag_text ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tag_icon' ) ); ?>"><?php esc_html_e( 'Tag icon:', 'medicpress-pt' ); ?></label><br>
				<small><?php echo wp_kses_post( apply_filters( 'pw/icons_input_field_notice', sprintf( esc_html__( 'Click on the icon below or manually select from the %s website.', 'medicpress-pt' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>' ) ) ); ?></small><br>
				<input class="widefat  js-icon-input" id="<?php echo esc_attr( $this->get_field_id( 'tag_icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tag_icon' ) ); ?>" type="text" value="<?php echo esc_attr( $tag_icon ); ?>" /><br>
				<?php foreach ( $this->font_awesome_icons_list as $icon ) : ?>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="<?php echo esc_attr( $icon ); ?>"><i class="fa fa-lg <?php echo esc_attr( $icon ); ?>"></i></a>
				<?php endforeach; ?>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tag_bg_color' ) ); ?>"><?php esc_html_e( 'Tag background color:', 'medicpress-pt' ); ?></label><br>
				<input class="js-pt-color-picker" id="<?php echo esc_attr( $this->get_field_id( 'tag_bg_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tag_bg_color' ) ); ?>" type="text" value="<?php echo esc_attr( $tag_bg_color ); ?>" data-default-color="#079bbb" />
			</p>

			<p>
				<?php esc_html_e( 'Widget alignment: ', 'medicpress-pt' ); ?><br>
				<label><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'align' ) ); ?>" value="left" <?php checked( $align, 'left' ); ?>><?php esc_html_e( 'Left', 'medicpress-pt' ); ?></label>
				<label style="margin-left: 10px;"><input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'align' ) ); ?>" value="center" <?php checked( $align, 'center' ); ?>><?php esc_html_e( 'Center', 'medicpress-pt' ); ?></label>
			</p>

			<script type="text/javascript">
				(function( $ ) {

					// Add Color Picker to all inputs that have 'color-field' class
					$(function() {
						$('.js-pt-color-picker').wpColorPicker();
					});

				})( jQuery );
			</script>

		<?php
		}
	}
	register_widget( 'PW_Notice' );
}
