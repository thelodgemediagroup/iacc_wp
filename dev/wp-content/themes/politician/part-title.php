<?php

if (is_front_page()) {
	$_title = '';
} elseif (is_singular()) {
	$_title = the_title(null, null, false);
} elseif (is_category()) {
	$_title = single_cat_title(null, false);
} elseif (is_tax()) {
	$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
	$_title = $term->name;
} elseif (is_search()) {
	$_title = sprintf(__('Search result for: <span>%s</span>', TEMPLATENAME), esc_attr(apply_filters('the_search_query', get_search_query(false))));
} elseif (is_author()) {
	$_title = sprintf(__( 'Posts by: <span>%s</span>', TEMPLATENAME ), get_the_author());
} elseif (is_archive()) {
	if (is_day())
		$_title = sprintf(__('Daily Archives: <span>%s</span>', TEMPLATENAME), get_the_date());
	elseif (is_month())
		$_title = sprintf(__('Monthly Archives: <span>%s</span>', TEMPLATENAME), get_the_date('F Y'));
	elseif (is_year())
		$_title = sprintf(__('Yearly Archives: <span>%s</span>', TEMPLATENAME), get_the_date('Y'));
	else
		$_title = __('All Archives', TEMPLATENAME);
}

if (!empty($_title)) :
 ?>
		<!-- - - - - - - - - - Page Header - - - - - - - - - - -->	
		
		<div class="page-header">
			
			<h1 class="page-title"><?php echo $_title; ?></h1>
			
		</div><!--/ .page-header-->
		
		<!-- - - - - - - - - end Page Header - - - - - - - - - -->	
<?php endif; ?>