<?php
	$image_id = get_post_thumbnail_id();
	$full_thumbnail = wp_get_attachment_image_src($image_id, 'full');
	
?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<section class="post-meta clearfix">
					
					<div class="post-date"><?php echo get_the_date(); ?></div><!--/ .post-date-->
					<div class="post-tags"><?php echo get_the_category_list(', '); ?></div><!--/ .post-tags-->
					<?php if ( comments_open() ) : ?>
					<div class="post-comments"><a href="<?php echo get_comments_link(); ?>" title="<?php comments_number(); ?>"><?php comments_number(); ?></a></div><!--/ .post-comments-->
					
					<div class="post-tags"><?php the_tags();  ?></div>
					
					<?php endif; // check for comments ?>
					
				</section><!--/ .post-meta-->

				<?php if(has_post_thumbnail()): ?>
				<a class="single-image" href="<?php echo $full_thumbnail[0]; ?>">
					<?php the_post_thumbnail('blog', array('title' => false, 'class' => 'custom-frame')); ?>
				</a>
				<?php endif; ?>

				<?php the_content(); ?>
				
				<div class="clear"></div>
			</article><!--/ .post-->

			<?php if ( comments_open() ) : ?>
			<?php comments_template(); ?>
			<?php endif; // check for comments ?>