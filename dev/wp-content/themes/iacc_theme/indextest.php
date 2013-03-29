
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


		</section><!--/ #content -->

		<?php get_sidebar('side'); ?>

	</section> <!--/ .container -->

<?php get_footer(); ?>