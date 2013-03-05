<?php
global $_theme_social_links;
$_theme_social_links = array(
	'twitter' => __('twitter', TEMPLATENAME),
	'facebook' => __('facebook', TEMPLATENAME),
	'dribbble' => __('dribbble', TEMPLATENAME),
	'vimeo' => __('vimeo', TEMPLATENAME),
	'youtube' => __('youtube', TEMPLATENAME),
	'rss' => __('rss', TEMPLATENAME),
	'linkedin' => __('linkedin', TEMPLATENAME),
);

function get_social_links() {
?>
		<ul class="social-links clearfix">
			<?php
			global $_theme_social_links;
			foreach ($_theme_social_links as $key => $title):
				$link = get_option($key.'_social_link');
				if (!empty($link)):
			?>
			<li class="<?php echo $key; ?>"><a href="<?php echo $link; ?>" title="<?php echo ucfirst($title); ?>"><?php echo ucfirst($title); ?><span></span></a></li>
			<?php
				endif;
			endforeach;
			?>
		</ul>
		<!--/ .social-links-->
<?php
}
?>