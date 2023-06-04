<?php
/**
 * New Opening Time Widget
 *
 * @package medicpress-pt
 */

if ( ! class_exists( 'PW_New_Opening_Time' ) ) {
	class PW_New_Opening_Time extends WP_Widget {
		private $widget_id_base, $widget_class, $widget_name, $widget_description;
		private $current_widget_id;

		public function __construct() {
			// Basic widget settings.
			$this->widget_id_base     = 'opening_time';
			$this->widget_class       = 'widget-opening-time';
			$this->widget_name        = esc_html__( 'Opening Time', 'medicpress-pt' );
			$this->widget_description = esc_html__( 'Displays a table of your opening hours.', 'medicpress-pt' );

			parent::__construct(
				'pw_' . $this->widget_id_base,
				sprintf( 'ProteusThemes: %s', $this->widget_name ),
				array(
					'description' => $this->widget_description,
					'classname'   => $this->widget_class,
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
			$items = isset( $instance['items'] ) ? array_values( $instance['items'] ) : array();

			echo $args['before_widget'];
		?>
			<div class="opening-time">
			<?php foreach ( $items as $item ) : ?>
				<div class="opening-time__item<?php echo ! empty( $item['less-important'] ) ? '  opening-time__item--less-important' : ''; ?>">
					<div class="opening-time__day">
						<?php echo wp_kses_post( $item['day'] ); ?>
					</div>
					<div class="opening-time__time">
						<?php echo wp_kses_post( $item['time'] ); ?>
					</div>
				</div>
			<?php endforeach; ?>
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

			foreach ( $new_instance['items'] as $key => $item ) {
				$instance['items'][ $key ]['id']             = sanitize_key( $item['id'] );
				$instance['items'][ $key ]['day']            = sanitize_text_field( $item['day'] );
				$instance['items'][ $key ]['time']           = sanitize_text_field( $item['time'] );
				$instance['items'][ $key ]['less-important'] = ! empty( $item['less-important'] ) ? sanitize_key( $item['less-important'] ) : '';
			}

			// Sort items by ids, because order might have changed.
			usort( $instance['items'], array( $this, 'sort_by_id' ) );

			return $instance;
		}

		/**
		 * Helper function to order items by ids.
		 * Used for sorting widget setting items.
		 *
		 * @param int $a first comparable parameter.
		 * @param int $b second comparable parameter.
		 */
		function sort_by_id( $a, $b ) {
			return $a['id'] - $b['id'];
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options.
		 */
		public function form( $instance ) {
			if ( ! isset( $instance['items'] ) ) {
				$instance['items'] = array(
					array(
						'id'             => 1,
						'day'            => '',
						'time'           => '',
						'less-important' => '',
					),
				);
			}

			// Page Builder fix when using repeating fields.
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}
		?>

			<h4><?php esc_html_e( 'Opening time rows', 'medicpress-pt' ); ?></h4>

			<script type="text/template" id="js-pt-opening-time-item-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="pt-sortable-setting  ui-widget  ui-widget-content  ui-helper-clearfix  ui-corner-all">
					<div class="pt-sortable-setting__header  ui-widget-header  ui-corner-all">
						<span class="dashicons  dashicons-sort"></span>
						<span><?php esc_html_e( 'Opening time row', 'medicpress-pt' ); ?> - </span>
						<span class="pt-sortable-setting__header-title">{{day}}</span>
						<span class="pt-sortable-setting__toggle  dashicons  dashicons-minus"></span>
					</div>
					<div class="pt-sortable-setting__content">

						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-day"><?php esc_html_e( 'Day:', 'medicpress-pt' ); ?></label>
							<input class="widefat  js-pt-sortable-setting-title" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-day" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][day]" type="text" value="{{day}}" />
						</p>

						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-time"><?php esc_html_e( 'Time:', 'medicpress-pt' ); ?></label>
							<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-time" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][time]" type="text" value="{{time}}" />
						</p>

						<p>
							<input class="checkbox  js-less-important-checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-less-important" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][less-important]" value="on"/>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-less-important"><?php esc_html_e( 'Is this item less important?', 'medicpress-pt' ); ?></label>
							<br>
							<small><?php esc_html_e( 'Useful for lunch breaks or other less important notifications.', 'medicpress-pt' ); ?></small>
						</p>

						<p>
							<input name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][id]" class="js-pt-opening-time-id" type="hidden" value="{{id}}" />
							<a href="#" class="pt-remove-opening-time-item  js-pt-remove-opening-time-item"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove item', 'medicpress-pt' ); ?></a>
						</p>

					</div>
				</div>
			</script>

			<div class="pt-widget-opening-time-items" id="opening-time-items-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="opening-time-items  js-pt-sortable-opening-time-items"></div>
				<p>
					<a href="#" class="button  js-pt-add-opening-time-item"><?php esc_html_e( 'Add new item', 'medicpress-pt' ); ?></a>
				</p>
			</div>

			<script type="text/javascript">
				(function( $ ) {
					// Repopulate the form.
					var itemsJSON = <?php echo wp_json_encode( $instance['items'] ) ?>;

					// Get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					if ( _.isFunction( MedicPress.Utils.repopulateOpeningTimeItems ) ) {
						MedicPress.Utils.repopulateOpeningTimeItems( itemsJSON, widgetId );
					}

					// Make settings sortable.
					$( '.js-pt-sortable-opening-time-items' ).sortable({
						items: '.pt-widget-single-opening-time-item',
						handle: '.pt-sortable-setting__header',
						cancel: '.pt-sortable-setting__toggle',
						placeholder: 'pt-sortable-setting__placeholder',
						stop: function( event, ui ) {
							$( this ).find( '.js-pt-opening-time-id' ).each( function( index ) {
								$( this ).val( index );
							});
						}
					});
				})( jQuery );
			</script>
		<?php
		}
	}
	register_widget( 'PW_New_Opening_Time' );
}
