<?php
/**
 * Icon List Widget
 *
 * @package medicpress-pt
 */

if ( ! class_exists( 'PW_Icon_List' ) ) {
	class PW_Icon_List extends WP_Widget {
		private $widget_id_base, $widget_class, $widget_name, $widget_description;
		private $current_widget_id;
		private $font_awesome_icons_list;

		public function __construct() {
			// Basic widget settings.
			$this->widget_id_base     = 'icon_list';
			$this->widget_class       = 'widget-icon-list';
			$this->widget_name        = esc_html__( 'Icon List', 'medicpress-pt' );
			$this->widget_description = esc_html__( 'Display a list with icons and text.', 'medicpress-pt' );

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
				'pw/icon_list_widget_fa_icons_list',
				array(
					'fa-home',
					'fa-phone',
					'fa-inbox',
					'fa-angle-right',
					'fa-question-circle',
					'fa-envelope-o',
					'fa-envelope',
					'fa-map-marker',
					'fa-users',
					'fa-female',
					'fa-male',
					'fa-compass',
					'fa-laptop',
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
			<div class="icon-list">
				<?php foreach ( $items as $item ) : ?>

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
				$instance['items'][ $key ]['id']          = sanitize_key( $item['id'] );
				$instance['items'][ $key ]['text']        = sanitize_text_field( $item['text'] );
				$instance['items'][ $key ]['icon']        = sanitize_text_field( $item['icon'] );
				$instance['items'][ $key ]['link']        = esc_url_raw( $item['link'] );
				$instance['items'][ $key ]['is-featured'] = ! empty( $item['is-featured'] ) ? sanitize_key( $item['is-featured'] ) : '';
				$instance['items'][ $key ]['description'] = wp_kses_post( $item['description'] );
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
						'id'          => 1,
						'text'        => '',
						'icon'        => '',
						'link'        => '',
						'is-featured' => '',
						'description' => '',
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

			<h4><?php esc_html_e( 'Icon list items', 'medicpress-pt' ); ?></h4>

			<script type="text/template" id="js-pt-icon-list-item-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="pt-sortable-setting  ui-widget  ui-widget-content  ui-helper-clearfix  ui-corner-all">
					<div class="pt-sortable-setting__header  ui-widget-header  ui-corner-all">
						<span class="dashicons  dashicons-sort"></span>
						<span><?php esc_html_e( 'Icon list item', 'medicpress-pt' ); ?> - </span>
						<span class="pt-sortable-setting__header-title">{{text}}</span>
						<span class="pt-sortable-setting__toggle  dashicons  dashicons-minus"></span>
					</div>
					<div class="pt-sortable-setting__content">

						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-text"><?php esc_html_e( 'Text:', 'medicpress-pt' ); ?></label>
							<input class="widefat  js-pt-sortable-setting-title" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-text" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][text]" type="text" value="{{text}}" />
						</p>

						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-icon"><?php esc_html_e( 'Icon:', 'medicpress-pt' ); ?></label> <br />
							<small><?php echo wp_kses_post( apply_filters( 'pw/icons_input_field_notice', sprintf( esc_html__( 'Click on the icon below or manually select from the %s website.', 'medicpress-pt' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>' ) ) ); ?></small>
							<input id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-icon" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][icon]" type="text" value="{{icon}}" class="widefat  js-icon-input" /> <br><br>
							<?php foreach ( $this->font_awesome_icons_list as $icon ) : ?>
								<a class="js-selectable-icon  icon-widget" href="#" data-iconname="<?php echo esc_attr( $icon ); ?>"><i class="fa fa-lg <?php echo esc_html( $icon ) ?>"></i></a>
							<?php endforeach; ?>
						</p>

						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-link"><?php esc_html_e( 'URL:', 'medicpress-pt' ); ?></label>
							<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-link" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][link]" type="link" value="{{link}}" />
						</p>

						<p>
							<input class="checkbox  js-is-featured-checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-is-featured" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][is-featured]" value="on"/>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-is-featured"><?php esc_html_e( 'Set this item as featured!', 'medicpress-pt' ); ?></label>
							<br>
							<small><?php esc_html_e( 'It will make this item bigger and display a description as well.', 'medicpress-pt' ); ?></small>
						</p>

						<p class="js-icon-list-description-container">
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-description"><?php esc_html_e( 'Description:', 'medicpress-pt' ); ?></label>
							<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-description" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][description]" type="description" value="{{description}}" />
						</p>

						<p>
							<input name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][id]" class="js-pt-icon-list-id" type="hidden" value="{{id}}" />
							<a href="#" class="pt-remove-icon-list-item  js-pt-remove-icon-list-item"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove item', 'medicpress-pt' ); ?></a>
						</p>

					</div>
				</div>
			</script>

			<div class="pt-widget-icon-list-items" id="icon-list-items-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="icon-list-items  js-pt-sortable-icon-list-items"></div>
				<p>
					<a href="#" class="button  js-pt-add-icon-list-item"><?php esc_html_e( 'Add new item', 'medicpress-pt' ); ?></a>
				</p>
			</div>

			<script type="text/javascript">
				(function( $ ) {
					// Repopulate the form.
					var itemsJSON = <?php echo wp_json_encode( $instance['items'] ) ?>;

					// Get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					if ( _.isFunction( ProteusWidgets.Utils.repopulateIconListItems ) ) {
						ProteusWidgets.Utils.repopulateIconListItems( itemsJSON, widgetId );
					}

					// Hide/display description.
					$( document ).on( 'change', '.js-is-featured-checkbox', function() {
						$( this )
							.parent()
							.siblings( '.js-icon-list-description-container' )
								.toggle( $( this ).prop( 'checked' ) );
					});

					// Make settings sortable.
					$( '.js-pt-sortable-icon-list-items' ).sortable({
						items: '.pt-widget-single-icon-list-item',
						handle: '.pt-sortable-setting__header',
						cancel: '.pt-sortable-setting__toggle',
						placeholder: 'pt-sortable-setting__placeholder',
						stop: function( event, ui ) {
							$( this ).find( '.js-pt-icon-list-id' ).each( function( index ) {
								$( this ).val( index );
							});
						}
					});
				})( jQuery );
			</script>
		<?php
		}
	}
	register_widget( 'PW_Icon_List' );
}
