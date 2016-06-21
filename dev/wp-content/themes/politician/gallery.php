<?php
/**
 * Template Name: Gallery Template
 *
 * @package WordPress
 * @subpackage Politician
 */
	get_header();
	if ( have_posts() ): the_post();
?>

<?php $terms = get_terms('gallery-tags', 'orderby=name'); ?>
	<!-- - - - - - - - - - - - - - - Container - - - - - - - - - - - - - - - - -->	
	
	<section class="container clearfix">

		<?php
		
		get_template_part('part', 'title');
		get_template_part('part', 'layouts');

		?>

		<!-- - - - - - - - - - Portfolio Filter - - - - - - - - - - -->	
		
		<ul id="portfolio-filter">

			<li><a data-categories="*" title="<?php _e('View all', TEMPLATENAME); ?>"><?php _e('View all', TEMPLATENAME); ?></a></li>
			<?php foreach($terms as $term): ?>
			<li><a data-categories="<?php echo $term->slug; ?>"><?php echo $term->name; ?></a></li>
			<?php endforeach; ?>

		</ul><!--/ end #portfolio-filter -->
		
		<!-- - - - - - - - - end Portfolio Filter - - - - - - - - - -->	

		<!-- - - - - - - - - - Portfolio Items - - - - - - - - - - -->	

		<section id="portfolio-items">

<?php
	do {
		query_posts(array(
			'post_type' =>'politic-gallery',
			'showposts' => -1
		));
		if (have_posts()) : while ( have_posts() ) : the_post();
		$terms = wp_get_post_terms($post->ID,'gallery-tags');
		$term_class = array();
		foreach($terms as $term) {
			$term_class[] = $term->slug;
		}
		$term_class = implode(' ', $term_class);

		$image_id = get_post_thumbnail_id();
		$full_thumbnail = wp_get_attachment_image_src($image_id, 'full');
?>
				<article class="one-fourth <?php echo $term_class; ?>" id="id<?php echo the_ID(); ?>" data-categories="<?php echo $term_class; ?>">
					<?php if (has_post_thumbnail()): ?>
					<a href="<?php echo $full_thumbnail[0]; ?>" rel="gallery_group" class="single-image picture-icon">
						<?php the_post_thumbnail('gallery', array('title' => false, 'class' => false)); ?>
					</a>
					<?php else: ?>
					<img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo get_template_directory_uri().'/images/no_image.gif&w=300&h=300'; ?>" class="pic" title="" alt="" />
					<?php endif; ?>
					<a href="#" class="project-meta">
						<h6 class="title-item"><?php the_title(); ?></h6>
					</a>
				</article><!--/ .one-fourth -->
<?php
		endwhile; endif; wp_reset_query();
		if (!have_posts())
			break;
		the_post();
	} while (1);
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
			<!-- End Paging -->
			<?php endif; ?>

		</section><!--/ #portfolio-items-->
		
		<!-- - - - - - - - - end Portfolio Items - - - - - - - - - - -->

	</section><!--/.container -->
		
	<!-- - - - - - - - - - - - - end Container - - - - - - - - - - - - - - - - -->
<?php
	endif;
	get_footer();
 ?>