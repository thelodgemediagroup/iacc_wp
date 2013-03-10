<?php

if ( ! current_user_can('manage_options') )
{
	add_filter( 'show_admin_bar', '__return_false' );
}

add_action( 'init', 'blockusers_init' );
function blockusers_init() {
    if ( is_admin() && ! current_user_can( 'administrator' ) &&
       ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_redirect( home_url() );
        exit;
    }
}

?>