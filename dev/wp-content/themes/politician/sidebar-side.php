<?php
/**
 * @package WordPress
 * @subpackage Politician
 */
global $_theme_side_sidebar;
?>
		<!-- - - - - - - - - - - - - - - Sidebar - - - - - - - - - - - - - - - - -->	
		
		<aside id="sidebar">

			<?php if ($_theme_side_sidebar == 'disable' || !dynamic_sidebar($_theme_side_sidebar)): ?>
				<div class="widget-container widget_search">
					<h3 class="widget-title"><?php _e('Search', TEMPLATENAME); ?></h3>
					<?php get_search_form(); ?>
				</div>

				<div class="widget-container widget_archive">
				<h3 class="widget-title"><?php _e( 'Archives', TEMPLATENAME ); ?></h3>
					<ul>
					<?php wp_get_archives( 'type=monthly' ); ?>
					</ul>
				</div>

				<div class="widget-container widget_meta">
				<h3 class="widget-title"><?php _e( 'Meta', TEMPLATENAME ); ?></h3>
					<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
					</ul>
				</div>
			<?php endif; ?>

		</aside><!--/ #sidebar-->
		
		<!-- - - - - - - - - - - - - end Sidebar - - - - - - - - - - - - - - - - -->