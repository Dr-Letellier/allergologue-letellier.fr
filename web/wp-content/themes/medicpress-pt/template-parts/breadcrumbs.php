<?php
/**
 * Breadcrumbs template used in the theme
 *
 * @package medicpress-pt
 */

if ( function_exists( 'bcn_display' ) ) : ?>
	<div class="breadcrumbs">
		<?php bcn_display(); ?>
	</div>
<?php endif; ?>
