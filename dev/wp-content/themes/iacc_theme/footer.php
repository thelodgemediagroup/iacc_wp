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
					<script src="http://localhost/wp-content/logos/sponsors.js"></script>
		<script src="http://localhost/wp-content/logos/jcarousellite_1.0.1.js"></script>
		<link href="http://localhost/wp-content/logos/style.css" rel="stylesheet">
		<div class="wpic_container">
			<div class="wpic_navigation" style="float: left;width: 60px;">
				<button style="float:right; background: url(http://localhost/wp-content/logos/next.png) no-repeat;" class="wpic_next"></button>
				<button style="float:right; background: url(http://localhost/wp-content/logos/prev.png) no-repeat;" class="wpic_prev"></button>
			</div>
			<div style="clear: both;"></div>
			<div class="wpic_content">
				<ul class="wpic_gallery">
					<li style="height: 210px; margin: 0px;padding: 0px;top: 0px; bottom: 0px;"><a href="http://www.google.com"><img src="http://localhost/wp-content/logos/lakeshore_toltest.png"></a></li>
					<li style="height: 210px; margin: 0px;padding: 0px;top: 0px; bottom: 0px;"><img src="http://localhost/wp-content/logos/comerica.png"></li>
					<li style="height: 210px; margin: 0px;padding: 0px;top: 0px; bottom: 0px;"><img src="http://localhost/wp-content/logos/jack_doheny.png"></li>
					<li style="height: 210px; margin: 0px;padding: 0px;top: 0px; bottom: 0px;"><img src="http://localhost/wp-content/logos/ug_software.png"></li>
					<li style="height: 210px; margin: 0px;padding: 0px;top: 0px; bottom: 0px;"><img src="http://localhost/wp-content/logos/drc_iacc.png"></li>
				</ul>
			</div>	
		</div>	
			<li>Copyright &copy; <?php echo date('Y'); ?> IACC USA.</li>
			<li>A Non-Profit, Tax-exempt Organization</li>
			<li>Tax ID #38-3687119</li>
			
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