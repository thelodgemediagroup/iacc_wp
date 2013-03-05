<?php

function theme_admin_notice() {
	echo "<div class='error'>" . sprintf( __( 'Your hosting currently use version of PHP %s. To get this theme worked properly you should ask your hosting provider to turn on at least version of PHP5 on your hosting.' ), phpversion() ) . "</div>";
}

if (version_compare(phpversion(), "5.0.0", "<"))
	add_action( 'admin_notices', 'theme_admin_notice' );
else {
	require_once(TEMPLATEPATH . '/functions/core.php');
	require_once(TEMPLATEPATH . '/functions/custom_types/post_types.php');
	require_once(TEMPLATEPATH . '/functions/custom_types/portfolio.php');
	require_once(TEMPLATEPATH . '/functions/sidebars.php');
	require_once(TEMPLATEPATH . '/functions/thumbnails.php');
	require_once(TEMPLATEPATH . '/functions/short_codes.php');
	require_once(TEMPLATEPATH . '/functions/breadcrumbs.php');
	require_once(TEMPLATEPATH . '/functions/social_links.php');
	require_once(TEMPLATEPATH . '/functions/options.php');
	require_once(TEMPLATEPATH . '/functions/comments.php');
}

function media_upload_html_bypass_test() {
	?>
	<p class="upload-html-bypass hide-if-no-js">
       
	</p>
	<script type="text/javascript">
		
		
		jQuery(".upload-html-bypass").each(function() {
			jQuery(this).remove(); // this points to the current element
			
		});
		jQuery("#drag-drop-area").each(function() {
			jQuery(this).remove(); // this points to the current element
			
		});
		
		
		
	</script>
	<?php
}

add_action('post-upload-ui', 'media_upload_html_bypass_test');
add_filter( 'flash_uploader', create_function( '$a','return false;' ), 5 );

?>