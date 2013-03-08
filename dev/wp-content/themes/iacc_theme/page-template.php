<?php
/*
Template Name: Links/About/Contact
*/
?>

<?php get_header(); ?>

<?php 	get_template_part('part', 'layouts');		
		global $_theme_layout;
?>

	<section class="container sbr clearfix">

		<?php get_template_part('part', 'title'); ?>

		<section id="content">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php the_content(); ?>

			<?php endwhile; ?>

		</section><!--/ #content -->

		<?php get_sidebar('side'); ?>

	</section><!--/ .container -->

<?php get_footer(); ?>