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

		<?php //get_template_part('part', 'title');
		$user_id = get_current_user_id();
		$header = get_user_meta($user_id);
		?>
		<div class="page-header">
			<h1 class="page-title"><?php echo $header['nickname'][0]; ?></h1>
		</div><!--/ .page-header-->

		<section id="content">

			<?php
			
			$membership_val = get_user_meta($user_id, 'membership_type', true);

			if ($membership_val == 'attendee')
			{
				?>
				<p>You are currently registered as an <?php echo ucfirst($membership_val); ?>. <a href="<?php site_url(); ?>/upgrade" title="Upgrade your membership">Upgrade</a> your membership to get access to all areas of the IACC!</p>
				<?php
			}

			?>

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<?php the_content(); ?>

			<?php endwhile; endif; ?>

		</section><!--/ #content -->

		<?php get_sidebar('side'); ?>

	</section><!--/ .container -->

<?php get_footer(); ?>