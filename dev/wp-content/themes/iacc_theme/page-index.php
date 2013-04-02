<?php
/*
Template Name: IACC Index
*/
?>

<?php get_header(); ?>

<?php 	get_template_part('part', 'layouts');		
		global $_theme_layout;
?>

	<section class="container sbr clearfix">

		<?php

			if (get_option('enable_slider') == true):

			get_template_part('functions/home-slider');

			endif;

			if (get_option('use_feature_home_box')):
		
				echo do_shortcode(get_option('feature_home_box'));

			endif;

		?>

		<section id="content">
			
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<?php the_content(); ?>

			<?php endwhile; endif; ?>

		</section><!--/ #content -->

		<?php get_sidebar('side'); ?>

	</section> <!--/ .container -->

<?php get_footer(); ?>