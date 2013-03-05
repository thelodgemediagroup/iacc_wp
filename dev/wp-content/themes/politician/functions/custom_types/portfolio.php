<?php
sd_register_post_type('politic-gallery', array(
	'labels' => array(
		'name' => _x("Politic's Gallery", TEMPLATENAME, 'post type general name'),
		'singular_name' => _x('Politic Gallery', TEMPLATENAME, 'post type singular name'),
		'add_new' => _x('Add New', TEMPLATENAME, 'gallery'),
		'add_new_item' => __('Add New Item', TEMPLATENAME),
		'edit_item' => __('Edit Item', TEMPLATENAME),
		'new_item' => __('New Item', TEMPLATENAME),
		'view_item' => __('View Item', TEMPLATENAME),
		'search_items' => __('Search Items', TEMPLATENAME),
		'not_found' =>  __('No items found', TEMPLATENAME),
		'not_found_in_trash' => __('No items found in Trash', TEMPLATENAME),
		'parent_item_colon' => ''
	),
	'public' => true,
	'show_ui' => true,
	'hierarchical' => false,
	'capability_type' => 'post',
	'exclude_from_search' => false,
	'show_in_nav_menus' => false,
	'supports' => array(
		'title',
		'thumbnail',
	),
	'taxonomies' => array('gallery-tags'),
), 'politic-gallery');

function register_gallery_taxonomy() {

	$labels = array(
		'name' => _x( 'Gallery Tags', TEMPLATENAME, 'post type general name'),
		'singular_name' => _x( 'Tag', TEMPLATENAME, 'post type singular name'),
		'search_items' =>  __( 'Search Tags', TEMPLATENAME),
		'all_items' => __( 'All Tags', TEMPLATENAME),
		'parent_item' => __( 'Parent Tag', TEMPLATENAME),
		'parent_item_colon' => __( 'Parent Tag:', TEMPLATENAME),
		'edit_item' => __( 'Edit Tag', TEMPLATENAME),
		'update_item' => __( 'Update Tag', TEMPLATENAME),
		'add_new_item' => __( 'Add New Tag', TEMPLATENAME),
		'new_item_name' => __( 'New Tag Name', TEMPLATENAME),
	);
	register_taxonomy(
		'gallery-tags',
		'politic-gallery',
		array(
			'hierarchical' => false,
			'labels' => $labels,
			'query_var' => true,
			'rewrite' => true,
			'show_in_nav_menus' => false,
		)
	);
}
add_action('init', 'register_gallery_taxonomy');

function set_gallery_columns($columns) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __('Items', TEMPLATENAME),
		'gallery-tags' => __('Gallery Tags', TEMPLATENAME),
		'thumbnail' => __('Thumbnail', TEMPLATENAME),
	);
	return $columns;
}
add_filter('manage_politic-gallery_posts_columns', 'set_gallery_columns');

function display_gallery_columns($column_name, $post_id) {
	global $post;
	if ($post->post_type == 'politic-gallery') {
		if ($column_name == 'gallery-tags') {
			echo get_gallery_taxs($column_name);
		} elseif ($column_name == 'thumbnail') {
			if ( has_post_thumbnail() ) {
				the_post_thumbnail('small_thumb');
			} else { ?>
				<img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo get_template_directory_uri()."/images/no_image.gif&w=60&h=60"; ?>" title="" alt="" />
			<?php }
		}
	}
}
add_action('manage_posts_custom_column',  'display_gallery_columns', 10, 2);

function get_gallery_taxs($cat_name) {
	global $post;
	$terms = get_the_terms($post->ID, $cat_name);
	
	if ( !empty( $terms ) ) {
		$out = array();
		foreach ( $terms as $term )
			$out[] = "<a href='edit.php?post_type={$post->post_type}&amp;{$cat_name}={$term->slug}'> " . esc_html(sanitize_term_field('name', $term->name, $term->term_id, $cat_name, 'display')) . "</a>";
			return join( ', ', $out );
		
	} else {
		return __('No Tags', TEMPLATENAME);
	}
}


function is_gallery() {
	$post_type = get_query_var('post_type');
	$gallery_tags = get_query_var('tags');
	return ($post_type == 'politic-gallery' || !empty($gallery_tags)) ? true : false;
}

function portfolio_post_limits( $limit )
{
	if (is_tax('gallery')) {
		$limit = get_option('gallery_limit');
	}
	if ( !$limit )
		$limit = $old_limit;
	elseif ( $limit == '-1' )
		$limit = '18446744073709551615';
	return $limit;
}

?>