<?php
/**
 * The Header for our theme.
 *
 * @package WordPress
 * @subpackage Politician
 */
?>
<!DOCTYPE html>
<!--[if IE 7]>					<html class="ie7 no-js" lang="en">     <![endif]-->
<!--[if lte IE 8]>              <html class="ie8 no-js" lang="en">     <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="not-ie no-js" <?php language_attributes(); ?>>  <!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="shortcut icon" href="<?php theme_favico(); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', TEMPLATENAME ), max( $paged, $page ) );

	?></title>

	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<?php wp_enqueue_style('css_custom'); ?>

	<!-- HTML5 Shiv -->
	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/modernizr.custom.js"></script>
	<script>window.jQuery || document.write('<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-1.7.1.min.js"><\/script>')</script>

<?php

	wp_enqueue_script('js_easing');
	wp_enqueue_script('js_cycle');
	wp_enqueue_script('js_respond');
	wp_enqueue_script('js_fancybox');
	wp_enqueue_script('js_autoAlign');

	if (is_page('gallery')) {
		wp_enqueue_script('js_isotope');
	}
 ?>

<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>

	<?php if (get_option('ga_use')) echo get_option('ga_code'); ?>
</head>
<body <?php body_class(); ?>>
<div class="wrap-header"></div><!--/ .wrap-header-->

<div class="wrap">
	
	<!-- - - - - - - - - - - - - - Header - - - - - - - - - - - - - - - - -->	
	
	<header id="header" class="clearfix">
		
		<a href="<?php echo home_url('/'); ?>" id="logo"><img src="<?php theme_logo(); ?>" alt="IACC USA, Indo American Chamber of Commerce" title="IACC USA" /></a>

		<div class="header-sitename">

			<h1>Indo American Chamber of Commerce USA</h1>

		</div><!--/ .sitename -->

		<?php get_social_links(); ?>
		<!--/ .social-links-->
			
		<nav id="navigation" class="navigation">
			
			<?php if ( has_nav_menu( 'primary' ) ) { ?>
				<?php wp_nav_menu(
					array(
						'container'		=> false,
						'menu_class'	=> false,
						'theme_location'=> 'primary',
						'items_wrap'	=> '<ul>%3$s</ul>'
					)
				); ?>
			<?php } else { ?>
				<?php wp_page_menu(
					array(
						'sort_column'	=> 'menu_order',
						'menu_class'	=> 'navigation',
						'menu_id'		=> false,
						'include'		=> '',
						'exclude'		=> '',
						'echo'			=> true,
						'link_before'	=> '',
						'link_after'	=> ''
					)
				); 
			} ?>
			
			<?php if (get_option('use_donate')) { ?>
			<a href="<?php echo get_option('donate_url'); ?>" class="donate"><?php echo get_option('donate_text'); ?></a>
			<?php } ?>
			
		</nav><!--/ #navigation-->
		
	</header><!--/ #header-->
	
	<!-- - - - - - - - - - - - - - end Header - - - - - - - - - - - - - - - - -->