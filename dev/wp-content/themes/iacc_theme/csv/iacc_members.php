<?php

	$filename = 'IACC_members.csv';
	header( 'Content-Description: File Transfer' );
	header( 'Content-Disposition: attachment; filename=' . $filename );
	header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );

	global $wpdb;

	$attendee_args = array('key' => 'membership_type', 'value' => 'attendee');
	$member_args = array('key' => 'membership_type', 'value' => 'member');
	$corp_member_args = array('key' => 'membership_type', 'value' => 'corporate_member');
	$user_args = array('relation' => 'OR', $attendee_args, $member_args, $corp_member_args);
	$iacc_users = get_users($user_args);
	

	foreach ($iacc_users as $user)
	{
		$iacc_members = array();
		$member_format = '"'.$user->user_email.'"';
		$iacc_members[] = $member_format;
		echo implode(',', $iacc_members)."\n";
	}
	exit;

?>