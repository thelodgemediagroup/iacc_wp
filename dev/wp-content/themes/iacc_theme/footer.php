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
		<script src="<?php echo site_url(); ?>/wp-content/logos/sponsors.js"></script>
		<script src="<?php echo site_url(); ?>/wp-content/logos/jcarousellite_1.0.1.js"></script>
		<link href="<?php echo site_url(); ?>/wp-content/logos/style.css" rel="stylesheet">
		<div class="wpic_container">
			<div class="wpic_navigation">
				<button style="float:right; background: url('<?php echo site_url(); ?>/wp-content/logos/next.png') no-repeat;" class="wpic_next"></button>
				<button style="float:left; background: url('<?php echo site_url(); ?>/wp-content/logos/prev.png') no-repeat;" class="wpic_prev"></button>
			</div>
			
			<div class="wpic_content">
				<ul class="wpic_gallery">
					<li style="height: 100px; width: 210px;"><a href="http://www.lakeshoreeng.com/"><img src="<?php echo site_url(); ?>/wp-content/logos/lakeshore_toltest.jpg"></a></li>
					<li style="height: 100px; width: 210px;"><a href="http://comerica.com/vgn-ext-templating/v/index.jsp?vgnextoid=8888577d17a31010VgnVCM1000004302a8c0RCRD"><img src="<?php echo site_url(); ?>/wp-content/logos/comerica.jpg"></a></li>
					<li style="height: 100px; width: 210px;"><a href="http://www.dohenysupplies.com/"><img src="<?php echo site_url(); ?>/wp-content/logos/jack_doheny.jpg"></a></li>
					<li style="height: 100px; width: 210px;"><a href="http://www.ugsoftware.com/"><img src="<?php echo site_url(); ?>/wp-content/logos/ug_software.jpg"></a></li>
					<li style="height: 100px; width: 210px;"><a href="http://www.detroitchamber.com/"><img src="<?php echo site_url(); ?>/wp-content/logos/drc_iacc.jpg"></a></li>
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