<?php
require_once(dirname(__FILE__) . '/admin_options/AdminPageFactory.php');

function theme_options_pages() {
	/*-------------------- Appearance Options Subpage --------------------*/
	ap_add_sub_page('Appearance', __('Theme Options', TEMPLATENAME), __('Theme Options', TEMPLATENAME), 'administrator', 'custom_theme_options');
	ap_page_title(__('Theme Options', TEMPLATENAME));
	ap_page_icon('index');

	/*-------------------- General Options --------------------*/
	ap_add_section('general', __('General', TEMPLATENAME));
	ap_add_select(array(
		'name' => 'theme_color',
		'title' => __('Theme Skin', TEMPLATENAME),
		'default' => 'style-1',
		'options' => array(
			'style-1' => __('Blue', TEMPLATENAME),
			'style-2' => __('Red', TEMPLATENAME),
			'style-3' => __('Green', TEMPLATENAME),
			'style-4' => __('Yellow', TEMPLATENAME),
			'style-5' => __('Blue/Bordo', TEMPLATENAME),
			'style-6' => __('Light Blue/Dark Blue', TEMPLATENAME),
		),
		'desc' => __('Choose an option to order slides.', TEMPLATENAME),
	));

	ap_add_upload(array(
		'name' => 'logo',
		'title' => __('Upload logo', TEMPLATENAME),
		'class' => 'large-text',
	));
	ap_add_upload(array(
		'name' => 'favicon',
		'title' => __('Upload favicon', TEMPLATENAME),
		'class' => 'large-text',
		'desc' => __('File type: .ico or .png File dimensions: 16x16, 32x32.', TEMPLATENAME),
	));
	
	
	ap_add_checkbox(array(
		'name' => 'use_donate',
		'title' => __('Display donate button?', TEMPLATENAME),
		'default' => false,
		'desc' => __('Check this to display donate button. (Dont forget to use WP menu nav, so the donate button will display in proper place)', TEMPLATENAME),
	));
	
	
	ap_add_input(array(
		'name' => 'donate_text',
		'title' => __('Donate button text', TEMPLATENAME),
		'default' => 'Donate',
		'desc' => __('Type the text for donate button.', TEMPLATENAME),
		'class' => 'large-text code',
	));
	
	
	ap_add_input(array(
		'name' => 'donate_url',
		'title' => __('Donate button url', TEMPLATENAME),
		'default' => '#',
		'desc' => __('Type an url for donate button.', TEMPLATENAME),
		'class' => 'large-text code',
	));

	ap_add_input(array(
		'name' => 'copyright',
		'title' => __('Footer copyright text', TEMPLATENAME),
		'default' => 'Copyright &copy; 2012 '.get_bloginfo('name').' company. All rights reserved.',
		'desc' => __('Type a copyright text.', TEMPLATENAME),
		'class' => 'large-text code',
	));
	ap_add_textarea(array(
		'name' => 'custom_css',
		'title' => __('Custom CSS', TEMPLATENAME),
		'class' => 'large-text code',
	));

	/*-------------------- Home Page Options --------------------*/

	ap_add_section('home', __('Home Page', TEMPLATENAME));
	ap_add_radio(array(
		'name' => 'show_on_front',
		'title' => __('Home page displays', TEMPLATENAME),
		'options' => array(
			'posts' => __('Your latest posts', TEMPLATENAME),
			'page' => sprintf(__('A %sstatic page%s (select bellow)', TEMPLATENAME), '<a href="edit.php?post_type=page">', '</a>'),
		),
		'default' => 'posts',
	));

	ap_add_select(array(
		'name' => 'page_on_front',
		'title' => __('Static home page', TEMPLATENAME),
		'options' => array(
			0  => __('- Select -', TEMPLATENAME),
		) + get_registered_pages(),
		'default' => '0',
	));

	ap_add_checkbox(array(
		'name' => 'use_feature_home_box',
		'title' => __('Show Special box?', TEMPLATENAME),
		'default' => true,
		'desc' => __('Check this to show special box.', TEMPLATENAME),
	));

	ap_add_textarea(array(
		'name' => 'feature_home_box',
		'title' => __('Home page Special box', TEMPLATENAME),
		'default' => '',
		'desc' => __('Content area for special gray box, which is under slider.', TEMPLATENAME),
		'class' => 'large-text code',
	));

	ap_add_checkbox(array(
		'name' => 'enable_slider',
		'title' => __('Enable Slider', TEMPLATENAME),
		'default' => false,
		'desc' => __('Check this to enable the slider.', TEMPLATENAME),
	));

	ap_add_upload(array(
		'name' => 'slider_image_1',
		'title' => __('Image 1', TEMPLATENAME),
		'class' => 'large-text',
		'desc' => __('Upload an image to show in the slider. Dimensions: 924px x 384px.', TEMPLATENAME),
	));

	ap_add_textarea(array(
		'name' => 'slider_image_1_desc',
		'title' => __('Image 1 Description', TEMPLATENAME),
		'class' => 'large-text code',
		'desc' => __('Enter in an optional description (accepts HTML)', TEMPLATENAME),
	));

	ap_add_upload(array(
		'name' => 'slider_image_2',
		'title' => __('Image 2', TEMPLATENAME),
		'class' => 'large-text',
		'desc' => __('Upload an image to show in the slider. Dimensions: 924px x 384px.', TEMPLATENAME),
	));

	ap_add_textarea(array(
		'name' => 'slider_image_2_desc',
		'title' => __('Image 2 Description', TEMPLATENAME),
		'class' => 'large-text code',
		'desc' => __('Enter in an optional description (accepts HTML)', TEMPLATENAME),
	));

	ap_add_upload(array(
		'name' => 'slider_image_3',
		'title' => __('Image 3', TEMPLATENAME),
		'class' => 'large-text',
		'desc' => __('Upload an image to show in the slider. Dimensions: 924px x 384px.', TEMPLATENAME),
	));

	ap_add_textarea(array(
		'name' => 'slider_image_3_desc',
		'title' => __('Image 3 Description', TEMPLATENAME),
		'class' => 'large-text code',
		'desc' => __('Enter in an optional description (accepts HTML)', TEMPLATENAME),
	));

	ap_add_upload(array(
		'name' => 'slider_image_4',
		'title' => __('Image 4', TEMPLATENAME),
		'class' => 'large-text',
		'desc' => __('Upload an image to show in the slider. Dimensions: 924px x 384px.', TEMPLATENAME),
	));

	ap_add_textarea(array(
		'name' => 'slider_image_4_desc',
		'title' => __('Image 4 Description', TEMPLATENAME),
		'class' => 'large-text code',
		'desc' => __('Enter in an optional description (accepts HTML)', TEMPLATENAME),
	));

	ap_add_upload(array(
		'name' => 'slider_image_5',
		'title' => __('Image 5', TEMPLATENAME),
		'class' => 'large-text',
		'desc' => __('Upload an image to show in the slider. Dimensions: 924px x 384px.', TEMPLATENAME),
	));

	ap_add_textarea(array(
		'name' => 'slider_image_5_desc',
		'title' => __('Image 5 Description', TEMPLATENAME),
		'class' => 'large-text code',
		'desc' => __('Enter in an optional description (accepts HTML)', TEMPLATENAME),
	));

	/*-------------------- Blog Options --------------------*/
	ap_add_section('blog', __('Blog posts & Pages', TEMPLATENAME));

	ap_add_select(array(
		'name' => 'default_listing_layout',
		'title' => __('Blog\'s listing page layout', TEMPLATENAME),
		'default' => 'first',
		'options' => array(
			'first' => __('Default', TEMPLATENAME),
			'second' => __('Alternate', TEMPLATENAME),
		),
		'desc' => __('Choose an option to order slides.', TEMPLATENAME),
	));

	global $_theme_layouts;
	ap_add_select(array(
		'name' => 'default_blog_layout',
		'title' => __('Default layout for blog', TEMPLATENAME),
		'default' => 1,
		'options' => $_theme_layouts,
		'desc' => __('Select default layout for blog.', TEMPLATENAME),
	));

	ap_add_input(array(
		'name' => 'blog_more_text',
		'title' => __('More button text', TEMPLATENAME),
		'default' => __('Read More &rarr;', TEMPLATENAME),
		'desc' => __('Leave it blank if you do not want to display this button.', TEMPLATENAME),
	));

	ap_add_input(array(
		'name' => 'searches_limit',
		'title' => __('Search posts limit', TEMPLATENAME),
		'default' => '10',
		'desc' => __('Type a number of posts to be displayed on search results page', TEMPLATENAME),
		'class' => 'small-text',
	));

	/*-------------------- SEO Options --------------------*/
	ap_add_section('google', __('Google Analytics', TEMPLATENAME));
	ap_add_checkbox(array(
		'name' => 'ga_use',
		'title' => __('Use Google Analytics', TEMPLATENAME),
		'default' => false,
		'desc' => __('Check this if you want to enable Google Analytics Service', TEMPLATENAME),
	));
	ap_add_textarea(array(
		'name' => 'ga_code',
		'title' => __('Google Analytics Code', TEMPLATENAME),
		'default' => "<script type=\"text/javascript\">\n\n  var _gaq = _gaq || [];\n  _gaq.push(['_setAccount', 'XX-XXXXXXXX-X']);\n  _gaq.push(['_trackPageview']);\n\n  (function() {\n	 var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n	 ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n	 var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n  })();\n\n</script>",
		'desc' => sprintf(__('Paste your %sGoogle Analytics%s code here, it will get applied to each page.', TEMPLATENAME), '<a href="http://www.google.com/analytics/" target="_blank">', '</a>'),
		'class' => 'large-text code',
	));

	/*-------------------- Sidebars Options --------------------*/
	ap_add_section('sidebars', __('Sidebars', TEMPLATENAME));
	ap_add_select(array(
		'name' => 'default_pages_layout',
		'title' => __('Default layout for pages', TEMPLATENAME),
		'default' => 3,
		'options' => $_theme_layouts,
		'desc' => __('Select default layout for pages.', TEMPLATENAME),
	));
	ap_add_select(array(
		'name' => 'default_side_sidebar',
		'title' => __('Default side sidebar', TEMPLATENAME),
		'default' => 'disable',
		'options' => array('disable' => __('Disable', TEMPLATENAME)),
		'options_func' => 'get_registered_sidebars',
		'desc' => __('Choose sidebar for side of page.', TEMPLATENAME),
	));

	ap_add_select(array(
		'name' => 'default_bottom_sidebar',
		'title' => __('Default bottom sidebar', TEMPLATENAME),
		'default' => 'disable',
		'options' => array('disable' => __('Disable', TEMPLATENAME)),
		'options_func' => 'get_registered_sidebars',
		'desc' => __('Choose sidebar for bottom of page.', TEMPLATENAME),
	));

	ap_add_select(array(
		'name' => 'gallery_bottom_sidebar',
		'title' => __('Gallery bottom sidebar', TEMPLATENAME),
		'default' => 'disable',
		'options' => array('disable' => __('Disable', TEMPLATENAME)),
		'options_func' => 'get_registered_sidebars',
		'desc' => __('Choose bottom sidebar for gallery pages.', TEMPLATENAME),
	));

	ap_add_select(array(
		'name' => 'blog_side_sidebar',
		'title' => __('Blog side sidebar', TEMPLATENAME),
		'default' => 'disable',
		'options' => array('disable' => __('Disable', TEMPLATENAME)),
		'options_func' => 'get_registered_sidebars',
		'desc' => __('Choose side sidebar for blog pages.', TEMPLATENAME),
	));

	ap_add_select(array(
		'name' => 'blog_bottom_sidebar',
		'title' => __('Blog bottom sidebar', TEMPLATENAME),
		'default' => 'disable',
		'options' => array('disable' => __('Disable', TEMPLATENAME)),
		'options_func' => 'get_registered_sidebars',
		'desc' => __('Choose bottom sidebar for blog pages.', TEMPLATENAME),
	));

	/*-------------------- Social Options --------------------*/
	ap_add_section('social', __('Social', TEMPLATENAME));
	global $_theme_social_links;
	foreach ($_theme_social_links as $key => $link) {
		ap_add_input(array(
			'name' => $key.'_social_link',
			'title' => '<b>'.ucfirst($link).'</b><img src="'.get_template_directory_uri()."/images/social/{$key}.png" .'">',
			'default' => '',
			'class' => 'large-text',
		));
	}
}
add_action('init', 'theme_options_pages');


function get_registered_pages() {
	$pages = get_pages();
	$out = array();
	foreach ($pages as $page)
		$out[$page->ID] = $page->post_title;
	return $out;
}

?>