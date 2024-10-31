/*
Plugin Name: ProfanityBlocker - Profanity Filter
Plugin URI: http://profanity-blocker.co.uk/plugins/wordpress/
Description: ProfanityBlocker is a cloud based service that scans for profane (swearing/abusive) words - giving you greater control of allowed text on your site.
Version: 1.1.1
Author: AstrizStudios
Author URI: http://astrizstudios.com
License: GPL2
*/

function astrizstudios_pro_block_getNew($v)
{
	var data =
	{
		action: 'astrizstudios_pro_block_ajax_new_acc',
		email: document.getElementById('astriz_pro_block_adminEmail').value.trim()
	};

	// we support 2.7 so we are sending the ajax url to the function..we will trust ajaxurl if it is defined though..
	if(!ajaxurl)	{ ajaxurl = $v; }

	//Lets create a div that would show the loading is in the process..
	var $md = jQuery("<div>", {id: "overlay_process_bar"});
	$md.click(function(){ this.remove(); });
	jQuery("#astrizstudios_pro_block_screenShade").append($md);

	jQuery.post(ajaxurl, data, function(response)
	{
		if(response === "") { astrizstudios_pro_block_showError('unknown_ajax', 'null'); }

		response = jQuery.parseJSON(response);

		if(response.data !== null && response.data !== false)
		{
			if(response.data.status === false)
			{
				jQuery('#overlay_process_bar').html(response.data.error_text + ' Error code:'+ response.data.error_num);
			}
			else
			{
				//jQuery('#astriz_pro_block_pubkey').val(response.data.key); //Key will not be available for now for simplicity reasons
				jQuery('#astriz_pro_block_licence').val(response.data.license);
				jQuery('#overlay_process_bar').html('Success! <br/>If the page did not refresh itself please click on this field and click on "Save all Changes" a bit down on the page.');
				jQuery('#submit_hidden').show();
				var form = jQuery('#submit_hidden').parents('form:first');
				form.submit();
				return true;
			}
		}
		else
		{
			astrizstudios_pro_block_showError('unknown_ajax', response.errormsg);
		}
	});

	return false;
}

function astrizstudios_pro_block_showError(e, extra)
{
	switch(e)
	{
		case 'unknown_ajax':
		{
			jQuery('#overlay_process_bar').html('Unfortunatelly there seems to have happened an unknown error. Please contact our support directly and they will create account for you if this is not the first time that you see this message. You can do that <a href="mailto:support@profanity-blocker.co.uk">here</a>. Please include this as the error message that you got ('+extra+')');
			break;
		}
		default:
		{
		}
	}
}