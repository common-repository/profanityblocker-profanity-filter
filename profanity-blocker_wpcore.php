<?php
/*
	Note: Having a nice and clean code is what we are after. Do mind that this is beta 1 release of the plugin and service and that there will be future updates to all aspects to what we offer. While that is true, we will try to bring you the best possible right from the start.
	
	About file:
	This file holds the functions specific to the WordPress Core ie - without any additional plugins/addons.
*/


//A bit of security..
if(!defined('ABSPATH')) { exit; }

//run this when wp starts..
add_action( 'plugins_loaded', 'astrizstudios_pro_block_run_wp_init' );

//add hooks to all options/hooks available in WordPress
function astrizstudios_pro_block_run_wp_init()
{
	$aspbh = new AstrizStudiosProBlockHooks();

	$sections = $aspbh->getHooks_section(null, true, 'wp');
	$sectionsA = $aspbh->getHooks_section(null, false, 'wp');

	foreach($sections as $section => $areas)
	{
		foreach($areas as $area)
		{
			add_filter($area, "astrizstudios_pro_block_filter_wp_".$section);
		}
	}

	foreach($sectionsA as $section => $areas)
	{
		foreach($areas as $area)
		{
			if($area === 'wp_insert_post_data')
			{
				//We need this for bypass
				add_filter( $area, 'astrizstudios_pro_block_filter_array', '99', 2 );
			}
			else
			{
				add_filter($area, 'astrizstudios_pro_block_filter_array');
			}
		}
	}
}

//Calling a function for each section, to make sure that the site owners want this section to be filtered..
function astrizstudios_pro_block_filter_wp_userinfo($data)
{
	//Get options, we will need them..
	$options = astrizstudios_pro_block_get_options();

	//Check if we should filter this..
	if($options['filter_wp_userinfo'] === 1)
	{
		$data = astrizstudios_pro_block_textcheck($data, $options);
	}

	return $data;
}

function astrizstudios_pro_block_filter_wp_comments($data)
{
	//Get options, we will need them..
	$options = astrizstudios_pro_block_get_options();

	//Check if we should filter this..
	if($options['filter_wp_comments'] === 1)
	{
		$data = astrizstudios_pro_block_textcheck($data, $options);
	}

	return $data;
}

function astrizstudios_pro_block_filter_wp_categories($data)
{
	//Get options, we will need them..
	$options = astrizstudios_pro_block_get_options();

	//Check if we should filter this..
	if($options['filter_wp_categories'] === 1)
	{
		$data = astrizstudios_pro_block_textcheck($data, $options);
	}

	return $data;
}

function astrizstudios_pro_block_filter_wp_posts($data)
{
	//Get options, we will need them..
	$options = astrizstudios_pro_block_get_options();

	//Check if we should filter this..
	if($options['filter_wp_posts'] === 1)
	{
		$data = astrizstudios_pro_block_textcheck($data, $options);
	}

	return $data;
}

?>