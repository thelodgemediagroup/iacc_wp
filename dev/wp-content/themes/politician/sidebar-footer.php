<?php
/**
 * @package WordPress
 * @subpackage Politician
 */
?>

<?php
	/* The footer widget area is triggered if any of the areas
	 * have widgets. So let's check that first.
	 *
	 * If none of the sidebars have widgets, then let's bail early.
	 */
	global $_theme_bottom_sidebar;
	if (!isset($_theme_bottom_sidebar) || empty($_theme_bottom_sidebar) || $_theme_bottom_sidebar == 'disable' || !is_active_sidebar($_theme_bottom_sidebar))
		return;
	// If we get this far, we have widgets. Let do this.
?>

<!-- Start Footer Sidebar -->

	<div class="footer_row">
		<?php dynamic_sidebar($_theme_bottom_sidebar); ?>
		<div class="clear"></div>
	</div>

<!-- End Footer Sidebar -->