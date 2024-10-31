<?php
/*
	Note: Having a nice and clean code is what we are after. Do mind that this is beta 1 release of the plugin and service and that there will be future updates to all aspects to what we offer. While that is true, we will try to bring you the best possible right from the start.
	
	About file:
	This file is in charge of the AJAX calls. For now it is only used when you get the plugin activated for the first time to make your experience using our plugin a nice one. Later on we will most likely use this some more..
*/

add_action( 'wp_ajax_astrizstudios_pro_block_ajax_new_acc', 'astrizstudios_pro_block_ajax_new_acc_callback' );

function astrizstudios_pro_block_ajax_new_acc_callback()
{
	//Lets make sure there are no issues with output..
	$oldER = error_reporting();
	error_reporting(0);
	ob_clean();

	//Lets check if this was already done. If it was, no need for it so lets just 'loose' the call..
	$options = astrizstudios_pro_block_get_options();

	//Sorry but we will not trust any input except the email..
	$data = array
	(
		'module'		=> 'free',
		'action'		=> 'package_implementation',
		'siteURL'		=> get_bloginfo('wpurl'),
		'siteName'		=> get_bloginfo('name'),
		'wpVersion'		=> get_bloginfo('version'),
		'adminEmail'	=> trim($_POST['email'])
	);

	//Lets send this to the API to check it out..
	$result = astrizstudios_pro_block_curl_caller($data, ASTRIZSTUDIOS_PROBLOCK_LINK_API,true,false);

	echo $result;

	error_reporting($oldER); //We are setting it back as it was..
	die(); // this is required to return a proper result
}

?>