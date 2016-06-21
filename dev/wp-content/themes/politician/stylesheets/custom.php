<?php
	header('Content-Type: text/css');
	//*****
	$css_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
	if (!is_file($css_path)) {
		$css_path = '../../../../wp-load.php';
	}
	include_once $css_path;
	//**

	echo stripslashes(get_option('custom_css'));
?>