<?php
// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }
?>

<?php get_header(); ?>

<?php 	get_template_part('part', 'layouts');		
		global $_theme_layout;
?>
	<section class="container sbr clearfix">

		<?php get_template_part('part', 'title'); ?>

		<section id="content">

			<?php tribe_events_before_html(); ?>

			<?php include(tribe_get_current_template()); ?>

			<?php tribe_events_after_html(); ?>

		</section><!--/ #content -->

		<?php get_sidebar('side'); ?>

	</section><!--/ .container -->
	
<?php get_footer(); ?>