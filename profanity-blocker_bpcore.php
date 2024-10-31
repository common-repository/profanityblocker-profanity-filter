<?php
/*
	Note: Having a nice and clean code is what we are after. Do mind that this is beta 1 release of the plugin and service and that there will be future updates to all aspects to what we offer. While that is true, we will try to bring you the best possible right from the start.
	
	About file:
	This file holds the functions specific to the BuddyPress ie - WordPress installation with BuddyPress addon for social networking.
*/

//A bit of security..
if(!defined('ABSPATH')) { exit; }

//If there is no BuddyPress, no need to run this section of the code
add_action('bp_include', 'astrizstudios_pro_block_run_buddy_init');


//add hooks to all options/hooks available in BuddyPress
function astrizstudios_pro_block_run_buddy_init()
{
	$opt = astrizstudios_pro_block_get_options();

	$opt['bp_runs'] = true;

	update_option( 'astrizstudios_pro_block_options', $opt);

	$aspbh = new AstrizStudiosProBlockHooks();

	$sections = $aspbh->getHooks_section(null, true, 'bp');
	$sectionsA = $aspbh->getHooks_section(null, false, 'bp');

	foreach($sections as $section => $areas)
	{
		foreach($areas as $area)
		{
			//Some areas have multiple parameters that should be checked..
			add_filter($area, "astrizstudios_pro_block_filter_bp_".$section);
		}
	}

	foreach($sectionsA as $section => $areas)
	{
		foreach($areas as $area)
		{
			add_filter($area, 'astrizstudios_pro_block_filter_array_bp');
		}
	}
}

function astrizstudios_pro_block_filter_array_bp($data, $t1 = null, $t2 = null, $t3 = null, $t4 = null, $t5 = null)
{
	//This helps us to speed things up..if $t1 is null, all others are as well.. ;) 
	if($t1 === null)
	{
		return astrizstudios_pro_block_filter_array($data, null, 'bp');
	}

	$data2 = array();

	$data2['data'] = $data;

	for($i = 1; $i < 6; $i++)
	{
		$f = 't'.$i;
		if($f !== null)			{ $data2[$i] = $f; }
		else					{ break; } //no need to go any further..
	}

	$rez = astrizstudios_pro_block_filter_array($data2, null, 'bp');

	//@HERE - Further testing of BP array functions needs to be done. Documentation is not very clear..
	return;
}

function astrizstudios_pro_block_filter_bp_activity($data, $id = false)
{
	//Get options, we will need them..
	$options = astrizstudios_pro_block_get_options();

	if($options['filter_bp_activity'] === 1)
	{
		$data = astrizstudios_pro_block_textcheck($data, $options);
	}

	return $data;
}

function astrizstudios_pro_block_filter_bp_member($data)
{
	//Get options, we will need them..
	$options = astrizstudios_pro_block_get_options();

	if($options['filter_bp_member'] === 1)
	{
		$data = astrizstudios_pro_block_textcheck($data, $options);
	}

	return $data;
}

function astrizstudios_pro_block_filter_bp_group($data, $t1 = false, $t2 = false, $t3 = false, $t4 = false, $t5 = false)
{
	//Get options, we will need them..
	$options = astrizstudios_pro_block_get_options();

	if($options['filter_bp_group'] === 1)
	{
		$data = astrizstudios_pro_block_textcheck($data, $options);
	}

	return $data;
}

function astrizstudios_pro_block_filter_bp_xprofile($data)
{
	//Get options, we will need them..
	$options = astrizstudios_pro_block_get_options();

	if($options['filter_bp_xprofile'] === 1)
	{
		$data = astrizstudios_pro_block_textcheck($data, $options);
	}

	return $data;
}

function astrizstudios_pro_block_filter_bp_messages($data)
{
	//Get options, we will need them..
	$options = astrizstudios_pro_block_get_options();

	if($options['filter_bp_messages'] === 1)
	{
		$data = astrizstudios_pro_block_textcheck($data, $options);
	}

	return $data;
}

?>