<?php

/**
 * Pagination for pages of replies (when viewing a topic)
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php 

$user_id = get_current_user_id();
$member_permissions = get_user_meta($user_id, 'member_permissions', TRUE);

?>

<?php if ($member_permissions > 1): ?>

<?php do_action( 'bbp_template_before_pagination_loop' ); ?>

<div class="bbp-pagination">
	<div class="bbp-pagination-count">

		<?php bbp_topic_pagination_count(); ?>

	</div>

	<div class="bbp-pagination-links">

		<?php bbp_topic_pagination_links(); ?>

	</div>
</div>

<?php do_action( 'bbp_template_after_pagination_loop' ); ?>

<?php else: ?>

	<br />
	<br />
	<br />
	<p><b>You must be an Member to access the IACC Forums. Upgrade your account to participate!</b></p>

<?php endif; ?>