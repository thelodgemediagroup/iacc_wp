<?php

global $_theme_layout;
global $_theme_side_sidebar;
global $_theme_bottom_sidebar;
$_theme_layout = $_theme_side_sidebar = $_theme_bottom_sidebar = '';
if (is_singular()) {
	$_theme_layout = get_post_meta(get_the_ID(), 'layout', true);
	$_theme_side_sidebar = get_post_meta(get_the_ID(), 'side_bar', true);
	$_theme_bottom_sidebar = get_post_meta(get_the_ID(), 'bottom_bar', true);
}
if (empty($_theme_layout)) {
	if (is_page())
		$_theme_layout = get_option('default_pages_layout', 3);
	else
		$_theme_layout = get_option('default_blog_layout', 1);
}
if (empty($_theme_side_sidebar))
	if (is_category() || (is_single() && is_post_type('post')))
		$_theme_side_sidebar = get_option('blog_side_sidebar', 'disable');
	else
		$_theme_side_sidebar = get_option('default_side_sidebar', 'disable');
if (empty($_theme_bottom_sidebar))
	if (is_category())
		$_theme_bottom_sidebar = get_option('blog_bottom_sidebar', 'disable');
	elseif (is_page('gallery'))
		$_theme_bottom_sidebar = get_option('gallery_bottom_sidebar', 'disable');
	else
		$_theme_bottom_sidebar = get_option('default_bottom_sidebar', 'disable');

?>