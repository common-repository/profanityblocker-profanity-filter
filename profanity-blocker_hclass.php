<?php
/*
	Note: Having a nice and clean code is what we are after. Do mind that this is beta 1 release of the plugin and service and that there will be future updates to all aspects to what we offer. While that is true, we will try to bring you the best possible right from the start.
	
	About file:
	This file holds all the hooks in use by the plugin. If you would like to add any hooks we suggest adding them in the proprer category and would like to hear from you which hooks did you add/remove as others might benefit from your changes as well.
*/

//A bit of security..
if(!defined('ABSPATH')) { exit; }

Class AstrizStudiosProBlockHooks
{
	private $wp_harray = array();
	private $bp_harray = array();
	private $bb_harray = array();
	private $wp_hstr = array();
	private $bp_hstr = array();
	private $bb_hstr = array();

	function __construct()
	{
		/* --- Initialize WP hooks --- */
		$this->wp_harray = array
		(
			'comments' => array
			(
				'preprocess_comment',
				'comment_save_pre'
			),
			'posts' => array
			(
				'wp_insert_post_data'
			)
		);

		$this->wp_hstr = array
		(
			'userinfo' => array
			(
				'pre_user_nicename',
				'pre_user_first_name',
				'pre_user_last_name',
				'pre_user_description',
				'pre_user_display_name',
				'pre_user_login',
				'pre_user_nickname'
			),
			'comments' => array
			(
				'comment_text_rss'
			),
			'categories' => array
			(
				'pre_category_nicename',
				'pre_category_name',
				'pre_category_description'
			),
			'posts' => array //here for blogs that allow public posts
			(
				'wp_title_rss'
			)
		);

		/* --- Initialize BP hooks --- */
		$this->bp_hstr = array
		(
			'activity' => array
			(
				'bp_activity_comment_content',
				'bp_activity_new_update_content',
				'bp_activity_post_comment_content',
				'bp_activity_post_update_content',
				'bp_activity_post_update_object',
				'bp_get_activity_content_body',
				'bp_get_activity_feed_item_description',
				'bp_get_activity_latest_update_excerpt',
				'groups_activity_new_update_content'
			),
			'group' => array
			(
				'group_forum_topic_title_before_save',
				'group_forum_topic_tags_before_save',
				'group_forum_topic_text_before_save',
				'group_forum_post_text_before_save',
				'groups_group_status_before_save',
				'groups_group_name_before_save',
				'groups_group_description_before_save',
				'groups_group_slug_before_save',
				'groups_member_user_title_before_save',
				'groups_member_invite_sent_before_save',
				'groups_member_comments_before_save'
			),
			'xprofile' => array
			(
				'xprofile_group_name_before_save',
				'xprofile_group_description_before_save',
				'xprofile_field_name_before_save',
				'xprofile_field_description_before_save',
				'xprofile_data_value_before_save'
			)
		);

		$this->bp_harray = array //BP has many functions that send variables, not the actual arrays..
		(
			'activity' => array
			(
				'bp_create_excerpt',
				'groups_activity_new_forum_post_content',
				'groups_activity_new_forum_topic_content'
			),
			'group' => array
			(
				'groups_at_message_notification_subject',
				'groups_at_message_notification_message',
				'groups_update_group_forum'
			),
			'messages' => array
			(
				'messages_notification_new_message_message'
			)
		);
	}

	public function getHooks_section($filter, $str = true, $sec = 'wp')
	{
		if($sec !== 'wp' && $sec !== 'bp' && $sec !== 'bb')
		{
			//wrong call..
			return false;
		}

		//The hstr should be taken a look at..
		if($str)
		{
			$sec .= '_hstr';
			$sections = $this->$sec;
		}
		//The harray should be taken a look at ..
		else
		{
			$sec .= '_harray';
			$sections = $this->$sec;
		}

		//Is it called with filter or just to get all hooks..
		if($filter === null)
		{
			return $sections;
		}

		foreach($sections as $section => $areas)
		{
			foreach($areas as $area)
			{
				if($area === $filter)	return $section;
			}
		}

		return false;
	}

/* -------------------------------------------------------- WordPress section -------------------------------------------------------- */


/* ------------------------------------------------------- BuddyPress  section ------------------------------------------------------- */


/* --------------------------------------------------------- bbPress section --------------------------------------------------------- */


}


?>