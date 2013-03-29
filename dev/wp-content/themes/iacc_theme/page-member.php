<?php
/*
Template Name: IACC Member
*/
?>

<?php get_header(); ?>

<?php 	get_template_part('part', 'layouts');		
		global $_theme_layout;
?>

	<section class="container sbr clearfix">

		<?php
		$user_id = get_current_user_id();
		$user = get_user_meta($user_id);

		?>
		<div class="page-header">
			<h1 class="page-title"><?php echo $user['nickname'][0]; ?></h1>
		</div><!--/ .page-header-->

		<section id="content">

			<?php

			$member_prettyprint = $user['member_prettyprint'][0];
			$member_permissions = $user['member_permissions'][0];

			if ($member_permissions < 2)
			{
				echo '<p>You are currently registered as an Attendee. <a href="'. site_url().'"/upgrade" title="Upgrade your membership">Upgrade</a>  your membership to get access to all areas of the IACC!</p>';
			}
			else 
			{
				echo '<p>You are currently registered as a '.$member_prettyprint.'.</p>';	
			}
			
			?>

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<?php the_content(); ?>

			<?php endwhile; endif; ?>

		</section><!--/ #content -->

		<?php get_sidebar('side'); ?>

	</section><!--/ .container -->

<?php get_footer(); ?>