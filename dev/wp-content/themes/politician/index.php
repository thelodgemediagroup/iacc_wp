<?php
/**
 * The main template file.
 *
 * @package WordPress
 * @subpackage Politician
 */
	get_header();

	if ( have_posts() ): the_post();
	get_template_part('part', 'layouts');
	global $_theme_layout;
?>

	<!-- - - - - - - - - - - - - - - Container - - - - - - - - - - - - - - - - -->	
	
	<?php  if ($_theme_layout == 1): ?>
	<section class="container sbr clearfix">
	<?php elseif ($_theme_layout == 2): ?>
	<section class="container sbl clearfix">
	<?php else: ?>
	<section class="container clearfix">
	<?php endif; ?>
		
		<?php if (is_front_page()) {

			if (get_option('enable_slider') == true):

			get_template_part('functions/home-slider');

			endif;

			if (get_option('use_feature_home_box')):
		
				echo do_shortcode(get_option('feature_home_box'));

			endif;

		} ?>

		<!-- - - - - - - - - - - - - - - Content - - - - - - - - - - - - - - - - -->		
		
		<?php get_template_part('part', 'title'); ?>
		<?php $listing_layout = get_option('default_listing_layout'); ?>

		<section id="content" class="<?php echo $listing_layout; ?>">

<?php
	if (is_page())
		get_template_part('loop', 'page');
	elseif (is_singular())
		get_template_part('loop', 'single');
	else
		get_template_part('loop', 'posts');
?>

		</section><!--/ #content-->
		
		<!-- - - - - - - - - - - - - - end Content - - - - - - - - - - - - - - - - -->	

		<?php if ($_theme_layout != 3): ?>
			
			<?php get_sidebar('side'); ?>

		<?php endif; ?>

	</section><!--/.container -->
		
	<!-- - - - - - - - - - - - - end Container - - - - - - - - - - - - - - - - -->	

<?php else: ?>

	<section class="container clearfix">

		<?php get_template_part('part', 'no_results'); ?>

	</section><!--/.container -->
		
	<!-- - - - - - - - - - - - - end Container - - - - - - - - - - - - - - - - -->	
<?php
	endif;
	get_footer();
?>