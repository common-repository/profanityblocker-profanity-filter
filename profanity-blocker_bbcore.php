<?php
/*
	Note: Having a nice and clean code is what we are after. Do mind that this is beta 1 release of the plugin and service and that there will be future updates to all aspects to what we offer. While that is true, we will try to bring you the best possible right from the start.
	
	About file:
	This file holds the functions specific to the bbPress ie - WordPress installation with bbPress forum addon (often installed with BuddyPress).
*/

//A bit of security..
if(!defined('ABSPATH')) { exit; }

if(function_exists('is_bbpress') && is_bbpress())
{
	//This is commented out and should stay like this as we will be updating our plugin quite soon with the bbPress code.
	//At this time it will do some filtering (what we have noticed) even without the bbPress hooks in here, but we will be adding them.

	/*$opt = astrizstudios_pro_block_get_options();

	$opt['bb_runs'] = true;

	update_option( 'astrizstudios_pro_block_options', $opt);

	foreach($sections as $section => $areas)
	{
		foreach($areas as $area)
		{
			//Some areas have multiple parameters that should be checked..
			//add_filter($area, "astrizstudios_pro_block_filter_bb_".$section);
		}
	}*/
}

?>