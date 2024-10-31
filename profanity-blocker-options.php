<?php

//A bit of security..
if(!defined('ABSPATH')) { exit; }

$options = array
(
	'userkey'				=>	'',
	'connecting'			=>	'use_rest_post',
	'filter_wp_posts'		=>	1,
	'filter_wp_comments'	=>	1,
	'filter_wp_userinfo'	=>	1,
	'filter_wp_categories'	=>	1,
	'filter_bp_activity'	=>	1,
	'filter_bp_member'		=>	1,
	'filter_bp_group'		=>	1,
	'filter_bp_xprofile'	=>	1,
	'filter_bp_messages'	=>	1,
	'filter_check_emails'	=>	1,
	'filter_check_phone'	=>	1,
	'filter_check_links'	=>	1
);

?>