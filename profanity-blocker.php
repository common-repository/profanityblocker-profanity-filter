<?php
/*
Plugin Name: ProfanityBlocker - Profanity Filter
Plugin URI: http://profanity-blocker.co.uk/plugins/wordpress/
Description: ProfanityBlocker is a cloud based service that scans for profane (swearing/abusive) words - giving you greater control of allowed text on your site.
Version: 1.1.1
Author: AstrizStudios
Author URI: http://astrizstudios.com
License: GPL2
*/

/*  Copyright 2013  AstrizStudios  (email : support@profanity-blocker.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//A bit of security..
if(!defined('ABSPATH')) { exit; }

/* -------------------------------------------------------- Global section -------------------------------------------------------- */

include_once('profanity-blocker_hclass.php');

define('ASTRIZSTUDIOS_PROBLOCK_LINK_API', 'http://data.profanity-blocker.co.uk/api.php');
define('ASTRIZSTUDIOS_PROBLOCK_LINK_SERVICE', 'http://service.profanity-blocker.co.uk/restServer.php');

//Make it active as soon as it is activated
register_activation_hook( __FILE__, 
							function()
							{
								include('profanity-blocker-options.php');

								add_option("astrizstudios_pro_block_options", $options);
							}
						);

//Even if they do not like the plugin it should have clean uninstall...
register_uninstall_hook(__FILE__, 'astrizstudios_pro_block_options_uninstal');

function astrizstudios_pro_block_options_uninstal()
{
	//Security checks before we actually do anything..
	if(!current_user_can('activate_plugins'))	{ return; }

	check_admin_referer('bulk-plugins');

	if(!defined('WP_UNINSTALL_PLUGIN'))			{ return; }

	if(__FILE__ != WP_UNINSTALL_PLUGIN)			{ return; }

	include('profanity-blocker-options.php');
	
	foreach($options as $option)				{ delete_option($option); }

	return 'DEACTIVATED!';
}

//Show in plugins managing page
add_filter('plugin_action_links', 'astrizstudios_pro_block_plugin_action_links', 10, 2);

function astrizstudios_pro_block_plugin_action_links($links, $file)
{
	static $this_plugin;

	if(!$this_plugin)
	{
		$this_plugin = plugin_basename(__FILE__);
	}

	if($file == $this_plugin)
	{
		if($x = astrizstudios_pro_block_get_options())
		{
			if(empty($x['userkey']))
			{
				//It is run for the first time lets make color of the bold text in red, so that we make it more noticeable..
				$style = ' style=\'color:red;\'';
				$name = ' >> ';
			}
			else	{ $style = ''; $name = ''; }
		}

		$new_link = '<a href="options-general.php?page=profanity-blocker-manager"><b'. $style .'>'. $name .'Plugin Settings</b></a>';
		array_unshift($links, $new_link);
	}

	return $links;
}

/* -------------------------------------------------------- WordPress section -------------------------------------------------------- */

include_once('profanity-blocker_wpcore.php');


/* ----------------------------------------------------- Administration  section ----------------------------------------------------- */

include_once('profanity-blocker_admin.php');

/* ----------------------------------------------------------- AJAX section ---------------------------------------------------------- */

include_once('profanity-blocker_ajax.php');

/* -------------------------------------------------------- Global  functions -------------------------------------------------------- */


function astrizstudios_pro_block_get_options()
{
	///load all options using get_option() -- ony one call needed.
	$options = get_option('astrizstudios_pro_block_options', false);

	if($options === false || empty($options))
	{
		include('profanity-blocker-options.php');
	}

	return $options;
}

//function to check the options if they are set properly
//returns default value that is passed to the function or the saved result..
function astrizstudios_pro_block_isset($v, $val, $default)
{
	//the value exists, lets return it, but lets make sure that it is what it is supposed to be..
	if(isset($v[$val]))	return (int)$v[$val];

	//the value is not set, lets return the default value, again, making sure that it is what it should be..
	return (int)$default;
}

/* array functions */

//Function to handle all arrays, since they should be fairly similar.
function astrizstudios_pro_block_filter_array($v1, $v2 = null, $sec = 'wp')
{
	$currentFilter = current_filter(); //v 2.5

	if($currentFilter === 'wp_insert_post_data')
	{
		//publish..
		if($v1['post_status'] === 'draft' || $v1['post_status'] === 'auto-draft')
		{
			//'auto-draft','draft'..
			return $v1;
		}
	}

	$aspbh = new AstrizStudiosProBlockHooks();

	if(!$section = $aspbh->getHooks_section($currentFilter, false, $sec))
	{
		//We were not able to find the section..we should just return back..
		return;
	}

	//Some hooks can be arrays and can be strings..lets speed things up and not cause code mixture by eliminating them right here..
	if(!is_array($v1) && $v2 === null)
	{
		//we are being sent a string..lets send it to the string functions instead..
		$f = 'astrizstudios_pro_block_filter_'.$sec.'_'.$section;

		return $f($v1);
	}

	$section = 'filter_'.$sec.'_'.$section;

	//Get options, we will need them..
	$options = astrizstudios_pro_block_get_options();

	//Check what needs to be filtered
	if(isset($options[$section]) && $options[$section] === 1)
	{
		//Lets prep the data that should be sent based on the current filter..
		$data = astrizstudios_pro_block_prepArray($v1, $v2, $currentFilter);

		//Send the data to filter service
		$data = astrizstudios_pro_block_textcheck($data, $options);

		//Get the filtered data into right layout and send it back
		return astrizstudios_pro_block_useArray($data, $v1);
	}

	return $v1;
}

//o1 and is the original values sent to the function..
function astrizstudios_pro_block_useArray($data,$o1)
{
	$d = null;

	//Lets grab the data that we need.. We do not need to know which filter was it..
	if(isset($data['array_original']))
	{
		$keys = $data['array_original'];
	}
	else
	{
		//Something is off..Send back the original values..
		return $o1;
	}

	foreach($keys as $key => $v)
	{
		//Usually this would be the best way to deal with filtered output, by placing it in a new field there is original content and the filtered out one.
		//since WP is not using this to display the content, we can either make it be displayed by our own plugin, which would affect any other plugin that is set for displaying data or save it as it comes back..
		$d[$key] = $data['array_parsed'][$key]['text_parsed'];
	}

	//Lets make sure that we do not remove other data, which was not sent to the filter..
	foreach($d as $k => $v)
	{
		$o1[$k] = $v;
	}

	return $o1;
}

function astrizstudios_pro_block_prepArray($a, $b = null, $f)
{
	$data = null;

	switch($f)
	{
		case 'preprocess_comment':
		{
			if(empty($a['user_ID']))
			{
				//If user is logged in their name, email and url have already been filtered..f they are not, we need to filter these as well.
				$data = array
				(
					'comment_author'		=> $a['comment_author'],
					'comment_author_email'	=> $a['comment_author_email'],
					'comment_author_url'	=> $a['comment_author_url'],
					'comment_content'		=> $a['comment_content']
				);
			}
			else
			{
				$data = array
				(
					'comment_content'		=> $a['comment_content']
				);
			}

			break;
		}
		case 'comment_save_pre':
		{
			//We must do this..The administration pages send the data from the comment update / edit as content string only..if not done over administration side, it should send more data...
			if(!is_array($a))
			{
				$data = $a;
			}
			elseif(isset($a['comment_content']))
			{
				$data = array
				(
					'comment_content'	=> $a['comment_content']
				);
			}
			break;
		}
		case 'wp_insert_post_data':
		{
			$data = array
			(
				//'post_content_filtered'	=> $b['post_content_filtered'],
				'post_content'				=> $b['post_content'],
				'post_excerpt'				=> $b['post_excerpt'],
				'post_title'				=> $b['post_title'],
				'post_name'					=> $b['post_name'],
				'tags_input'				=> $b['tags_input']
			);
			break;
		}
	}

	if(is_array($data))
	{
		//Lets remove empty fields..no need to filter them out..
		foreach($data as $d => $v)
		{
			if(empty($v))					{ unset($data[$d]); }
		}
	}

	return $data;
}

/* Calls related */

function astrizstudios_pro_block_textcheck($txt, $options)
{
	$arraycall = is_array($txt);

	if($arraycall || !empty($txt))
	{
		$data = array
		(
			'key'	=>	$options['userkey'],
			'text'	=>	$txt,
			'email'	=>	$options['filter_check_emails'],
			'phone'	=>	$options['filter_check_phone'],
			'link'	=>	$options['filter_check_links']
		);

		if($arraycall)					{ $data['type'] = 'array'; $data = http_build_query($data); }

		$ready = astrizstudios_pro_block_curl_caller($data, ASTRIZSTUDIOS_PROBLOCK_LINK_SERVICE);

		//We must have this if the server sends bad encoding of data..we will not need it in php6
		if(substr($ready, 0, 3) === "\xef\xbb\xbf")
		{
			$ready = substr($ready, 3);
		}

		if($arraycall)
		{
			$response = json_decode($ready,true);
			$text = $response; //We send everything..not only the parsed data..must for 'original'..
		}
		elseif($response = simplexml_load_string($ready,'SimpleXMLElement', LIBXML_NOCDATA))
		{
			$text = $response->text_parsed;
		}
		else 							{ $text = $txt; }

	}

	//If the string returned is blank, return the original text. This might happen with rest (soap should not have these problems).
	return (empty($text)) ? ( is_array($text) ? $text : $txt ) : $text;
}

function astrizstudios_pro_block_prepare_for_api($data)
{
	$usr_det = astrizstudios_pro_block_get_options();

	$usr_lic = $usr_det['userkey'];
	$usr_key = $usr_det['user_pub_key'];

	//@HERE - We must add the public key field..
	if($usr_key === '' || $usr_key === null)
	{
		return false;
	}

	$em = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $usr_lic, json_encode($data), MCRYPT_MODE_ECB));

	$params = array();
	$params['m'] = $em;
	$params['k'] = $usr_key;

	return $params;
}

//Contacting function.
//$data is always the array that is sent to the server over cURL.
//Url is the url to sent the request to.
//$close is useful if we do not want to close the connection.
//$report is used to determine if an error is found using cURL should the error be reported back to the service or not. This helps with debug and the only info sent is the info related to that errored call. 
function astrizstudios_pro_block_curl_caller($data, $url, $close = true, $report = true)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	//Lets get the results.
	$result = curl_exec($ch);

	//Is there an error? What should we do (if there is)
	if(curl_error($ch) !== '' && $report)
	{

		$data = array
		(
			'module'		=> 'error',
			'action'		=> 'add',
			'error'			=> array('original_data' => $data,'error_state' => curl_error($ch), 'error_number' => curl_errno($ch),'url' => $url)
		);

		//We must close this handle to be sure that this is not what causes the issue
		curl_close($ch);

		//prep the data
		$data = astrizstudios_pro_block_prepare_for_api($data);

		//Lets send the error..
		astrizstudios_pro_block_curl_caller($data, ASTRIZSTUDIOS_PROBLOCK_LINK_API, true);
		
		//we must take into account that the $close might still be true and we do not want any errors..since each function might result something else on error (false, null, '', etc) we should let it return $result..
		return $result;
	}

	//Check if we should close the cURL connection
	if($close)				{ curl_close($ch); return $result; }

	return array($ch, $result);
}

?>