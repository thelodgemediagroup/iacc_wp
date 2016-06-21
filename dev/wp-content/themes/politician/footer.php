<?php
/**
 * The template for displaying the footer.
 *
 * @package WordPress
 * @subpackage Politician
 */
?>
	<!-- - - - - - - - - - - - - - - Footer - - - - - - - - - - - - - - - - -->	
	
	<footer id="footer" class="container clearfix">
		
		<?php get_sidebar('footer'); ?>
		
		<ul class="copyright">
			
			<li><a href="http://webtemplatemasters.com"><?php _e('ThemeMakers', TEMPLATENAME); ?></a></li>
			<li><?php echo get_option('copyright'); ?></li>
			
		</ul><!--/ .copyright-->
	
	</footer><!--/ #footer-->
	
	<!-- - - - - - - - - - - - - - - end Footer - - - - - - - - - - - - - - - - -->		
	
</div><!--/ .wrap-->

<!--[if lt IE 9]>
	<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/js/ie.js"></script>
<![endif]-->
<!--[if IE 8]>
	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/selectivizr-and-extra-selectors.min.js"></script>
<![endif]-->

<?php wp_enqueue_script('js_custom'); ?>
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
<script type="text/javascript">
	if(jQuery('#portfolio-items').length) {<?php wp_enqueue_script('js_isotope'); ?>}
	if(jQuery('.single-image').length) {<?php wp_enqueue_script('js_fancybox'); ?>}
	jQuery('body').addClass('<?php echo get_option('theme_color'); ?>');
</script>
</body>
</html>