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

			<?php endif; ?>


		<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
		<script type="IN/FollowCompany" data-id="2712759" data-counter="none"></script>

		</aside><!--/ #sidebar-->
		
		<!-- - - - - - - - - - - - - end Sidebar - - - - - - - - - - - - - - - - -->