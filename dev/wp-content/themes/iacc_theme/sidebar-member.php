<?php
/**
 * @package WordPress
 * @subpackage Politician
 */
global $_theme_side_sidebar;
?>
		<!-- - - - - - - - - - - - - - - Sidebar - - - - - - - - - - - - - - - - -->	
		
		<aside id="sidebar">

			<?php if (is_user_logged_in()) { get_users_upcoming_events(); } ?>

		<!--<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
		<script type="IN/FollowCompany" data-id="2712759" data-counter="none"></script> -->

		</aside><!--/ #sidebar-->
		
		<!-- - - - - - - - - - - - - end Sidebar - - - - - - - - - - - - - - - - -->