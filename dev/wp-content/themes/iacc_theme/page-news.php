<?php
/*
Template Name: IACC in the News
*/
?>

<?php get_header(); ?>

<?php 	get_template_part('part', 'layouts');		
		global $_theme_layout;
?>

	<section class="container sbr clearfix">

		<?php get_template_part('part', 'title'); ?>

		<section id="content">

			<?php 

				query_posts("cat=3&posts_per_page=20");

				if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

						<?php $image_id = get_post_thumbnail_id(); ?>
						<?php $full_thumbnail = wp_get_attachment_image_src($image_id, 'full'); ?>

						<a href="<?php the_permalink(); ?>">
							<h3 class="title"><?php the_title(); ?></h3><!--/ .title -->
						</a>

						<?php if(has_post_thumbnail()): ?>
							<a class="single-image" href="<?php echo $full_thumbnail[0]; ?>">
							<?php the_post_thumbnail('blog', array('title' => false, 'class' => 'custom-frame')); ?>
							</a>
						<?php endif; ?>
				
						<?php the_excerpt(); ?>
				
						<?php if (!empty($_readmore_text)): ?>
							<a href="<?php the_permalink(); ?>" class="button gray" title="<?php echo $_readmore_text; ?>">
								<?php echo $_readmore_text; ?></a>
						<?php endif; ?>
						<div class="clear"></div>

				<?php endwhile; endif; ?>

				<?php if ( !have_posts() )
				{
					echo "<h2>No News</h2>";
					echo "<p>There are currently no news articles to display</p>";
				}
				?>

				<?php /* Display navigation to next/previous pages when applicable */
					if ($wp_query->max_num_pages > 1): ?>
						<!-- Start Paging -->
							<?php	if (function_exists('wp_pagenavi')):
								echo wp_pagenavi();
					else: ?>
						<div class="navigation" id="nav-below">
						<div class="nav-previous"><?php next_posts_link(__('<span class="meta-nav">&larr;</span> Older posts', TEMPLATENAME)); ?></div>
						<div class="nav-next"><?php previous_posts_link(__('Newer posts <span class="meta-nav">&rarr;</span>', TEMPLATENAME)); ?></div>
						</div><!-- #nav-below -->
					<?php endif; ?>
					<div class="clear"></div>
						<!-- End Paging -->
					<?php endif; ?>

		</section><!--/ #content -->

		<?php get_sidebar('side'); ?>

	</section><!--/ .container -->

<?php get_footer(); ?>
