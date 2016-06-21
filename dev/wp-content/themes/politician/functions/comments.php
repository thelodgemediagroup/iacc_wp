<?php
if ( ! function_exists( 'custom_theme_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own custom_theme_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 */
function custom_theme_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
				<li <?php comment_class(); ?>>
					<article>
						<?php echo get_avatar($comment, 40); ?>
						<div class="comment-entry">
							<div class="comment-meta">
								<h6 class="author"><?php echo get_comment_author_link(); ?></h6>
								<p class="date"><?php echo get_comment_date('M d, Y'); ?>&nbsp;<?php edit_comment_link(__('(Edit)', TEMPLATENAME)); ?> <?php _e( 'at', TEMPLATENAME ); ?> <?php echo get_the_time(); ?></p>
							</div><!--/ .comment-meta -->
							<div class="comment-body">
								<?php if ( $comment->comment_approved == '0' ) : ?>
									<em><?php _e( 'Your comment is awaiting moderation.' ); ?></em>
									<br />
								<?php endif; ?>
								<?php comment_text(); ?>
								<p><?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?></p>
							</div><!--/ .comment-body -->
						</div><!--/ .comment-entry-->
					</article>
	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', TEMPLATENAME ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', TEMPLATENAME), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

function comment_form_custom_fields($fields) {
	$commenter = wp_get_current_commenter();
	$req = get_option('require_name_email');
	$aria_req = ($req ? " aria-required='true'" : '');
	$fields['author'] = '<fieldset class="input-block"><label for="name"><strong>' . __( 'Your Name', TEMPLATENAME ) . ': ' . ( $req ? '</strong><span>*</span><i>(' . __('required', TEMPLATENAME) . ')</i>' : '' ) . '</label>' . '<input id="name" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" required' . $aria_req . ' /></fieldset>';
	$fields['email']  = '<fieldset class="input-block"><label for="email"><strong>' . __( 'E-mail', TEMPLATENAME ) . ': ' . ( $req ? '</strong><span>*</span><i>(' . __('required', TEMPLATENAME) . ')</i>' : '' ) . '</label>' .	'<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" required' . $aria_req . ' /></fieldset>';
	$fields['url'] = '<fieldset class="input-block"><label for="website"><strong>' . __( 'Website', TEMPLATENAME ) . '</strong></label>' . '<input id="website" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></fieldset>';
	return $fields;
}
add_filter('comment_form_default_fields', 'comment_form_custom_fields');

?>
