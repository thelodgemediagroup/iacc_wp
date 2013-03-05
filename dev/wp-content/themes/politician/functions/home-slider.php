	<?php 

		$slider = get_option('enable_slider');
		
		$image1 = get_option('slider_image_1');
		$image2 = get_option('slider_image_2');
		$image3 = get_option('slider_image_3');
		$image4 = get_option('slider_image_4');
		$image5 = get_option('slider_image_5');
		
	
		$desc1 = get_option('slider_image_1_desc');
		$desc2 = get_option('slider_image_2_desc');
		$desc3 = get_option('slider_image_3_desc');
		$desc4 = get_option('slider_image_4_desc');
		$desc5 = get_option('slider_image_5_desc');
		
		if($slider == true) :

		wp_enqueue_style('css_flexslider');
		wp_enqueue_script('js_flexslider');
		
	?>


		<!-- - - - - - - - - - - Slider - - - - - - - - - - - - - -->	

		<div id="slider" class="flexslider">

		<?php if($image1 != '') : ?>
		<ul class="slides">
					
			<?php $image_count = 0; ?>
			
			<?php if($image1 != '') : ?>
			
			<li>
				<img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo $image1; ?>&w=924&h=384" alt="" />
				<?php if($desc1 != '') :?>
				<div class="caption">
					<div class="caption-entry">
						<?php echo stripslashes(htmlspecialchars_decode(nl2br($desc1))); ?>
					</div><!--/ .caption-entry-->
				</div><!--/ .caption-->
				<?php endif; ?>
			</li>
			
			<?php $image_count++; endif;?>
			
			<?php if($image2 != '') : ?>
			
			<li>
				<img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo $image2; ?>&w=924&h=384" alt="" />
				<?php if($desc2 != '') :?>
				<div class="caption">
					<div class="caption-entry">
						<?php echo stripslashes(htmlspecialchars_decode(nl2br($desc2))); ?>
					</div><!--/ .caption-entry-->
				</div><!--/ .caption-->
				<?php endif; ?>
			</li>
			
			<?php $image_count++; endif;?>
			
			<?php if($image3 != '') : ?>
			
			<li>
				<img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo $image3; ?>&w=924&h=384" alt="" />
				<?php if($desc3 != '') :?>
				<div class="caption">
					<div class="caption-entry">
						<?php echo stripslashes(htmlspecialchars_decode(nl2br($desc3))); ?>
					</div><!--/ .caption-entry-->
				</div><!--/ .caption-->
				<?php endif; ?>
			</li>
			
			<?php $image_count++; endif;?>
			
			<?php if($image4 != '') : ?>
			
			<li>
				<img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo $image4; ?>&w=924&h=384" alt="" />
				<?php if($desc4 != '') :?>
				<div class="caption">
					<div class="caption-entry">
						<?php echo stripslashes(htmlspecialchars_decode(nl2br($desc4))); ?>
					</div><!--/ .caption-entry-->
				</div><!--/ .caption-->
				<?php endif; ?>
			</li>
			
			<?php $image_count++; endif;?>
			
			<?php if($image5 != '') : ?>
			
			<li>
				<img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo $image5; ?>&w=924&h=384" alt="" />
				<?php if($desc5 != '') :?>
				<div class="caption">
					<div class="caption-entry">
						<?php echo stripslashes(htmlspecialchars_decode(nl2br($desc5))); ?>
					</div><!--/ .caption-entry-->
				</div><!--/ .caption-->
				<?php endif; ?>
			</li>
			
			<?php $image_count++; endif;?>

		</ul>

		<?php else : ?>

		<img src="<?php echo get_template_directory_uri(); ?>/timthumb.php?src=<?php echo get_template_directory_uri()."/images/no_image.gif&w=940&h=400"; ?>" title="" alt="" />

		<?php endif; ?>

		</div><!--/ #slider-->

		<!-- - - - - - - - - - - end Slider - - - - - - - - - - - - - -->

		<?php endif; ?>