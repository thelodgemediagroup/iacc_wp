<?php
	$_readmore_text = get_option('blog_more_text');
	do {
	$image_id = get_post_thumbnail_id();
	$full_thumbnail = wp_get_attachment_image_src($image_id, 'full');
?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				
				<a href="<?php the_permalink(); ?>">
					<h3 class="title"><?php the_title(); ?></h3><!--/ .title -->
				</a>
				
				<section class="post-meta clearfix">
					
					<div class="post-date"><?php echo get_the_date(); ?></div><!--/ .post-date-->
					<div class="post-tags"><?php echo get_the_category_list(', '); ?></div><!--/ .post-tags-->
					<?php if ( comments_open() ) : ?>
					<div class="post-comments"><a href="<?php echo get_comments_link(); ?>" title="<?php comments_number(); ?>"><?php comments_number(); ?></a></div><!--/ .post-comments-->
					<?php endif; // check for comments ?>
					<div class="post-tags"><?php the_tags();  ?></div>
					
				</section><!--/ .post-meta-->
				
				<?php if(has_post_thumbnail()): ?>
				<a class="single-image" href="<?php echo $full_thumbnail[0]; ?>">
					<?php the_post_thumbnail('blog', array('title' => false, 'class' => 'custom-frame')); ?>
				</a>
				<?php endif; ?>
				
				<?php the_excerpt(); ?>
				
				<?php if (!empty($_readmore_text)): ?>
				<a href="<?php the_permalink(); ?>" class="button gray" title="<?php echo $_readmore_text; ?>"><?php echo $_readmore_text; ?></a>
				<?php endif; ?>
				<div class="clear"></div>
			</article><!--/ .post-->

<?php
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
				<div class="clear"></div>
				<!-- End Paging -->
				<?php endif; ?>
