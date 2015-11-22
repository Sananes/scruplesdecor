<?php
/**
 *	Likes System
 *
 *	AJAX Gateway
 *	admin-ajax.php?action={laborator_likes}&post_id={REQ}&type={unlike|like}&{LAB_LIKE_AJAX_NONCE_VAR}={NONCE_KEY}
 *	
 *	Laborator.co
 *	www.laborator.co
 *
 *	Coded by: Arlind Nushi
 */

global $laborator_supported_like_post_types;

define("LAB_LIKE_VAR", "laborator_likes"); # AJAX Action Name
define("LAB_LIKE_AJAX_NONCE_VAR", "like_verify_key"); # AJAX Request VAR
define("LAB_LIKE_NONCE_ACTION", "laborator_likes_nonce");

$laborator_supported_like_post_types = array();


# Add Support for the post type
function laborator_add_like_support($post_types, $args = array())
{
	global $laborator_supported_like_post_types;
	
	if( ! is_array($post_types))
	{
		$post_types = array($post_types);
	}
	
	foreach($post_types as $post_type)
	{
		if( ! isset($laborator_supported_like_post_types[ $post_type ]))
		{
			if( ! isset($args['name']))
			{
				$args['name'] = ucwords($post_type . " likes");
			}
			
			$laborator_supported_like_post_types[$post_type] = $args;
		}
	}
}

# Get Supported Post types for Likes
function laborator_get_like_post_types()
{
	global $laborator_supported_like_post_types;
	
	return $laborator_supported_like_post_types;
}


# Get Number of likes (1 - number of likes, 2 - array with details)
function laborator_get_likes($post_id = null, $type = 1) 
{
	global $post;
	
	if($post_id)
	{
		$post = get_post($post_id);
	}
	
	$likes_arr = get_post_meta($post->ID, LAB_LIKE_VAR, true);
	
	if( ! is_array($likes_arr))
	{
		$likes_arr = array();
	}
	
	switch($type)
	{
		case 2:
		case 'array':
			return array_reverse($likes_arr);
			break;
			
		# Get Number Of Likes
		case 1:
		default:
			
			$likes = count($likes_arr);
			return $likes;
	}
}


# Like an item
function laborator_like_item($post_id = null, $do_unlike = false)
{
	global $post, $current_user;
	
	if($post_id)
	{
		$post = get_post($post_id);
	}
	
	if($post)
	{
		$likes_arr = $post->laborator_likes;
		
		if( ! is_array($likes_arr))
		{
			$likes_arr = array();
		}
		
		$like_entry = laborator_gen_like_entry();
		
		# Unlike Item
		if($do_unlike)
		{	
			$likes_arr = laborator_remove_like($like_entry, $likes_arr);
			update_post_meta($post->ID, LAB_LIKE_VAR, $likes_arr);
		}
		# Like Item
		else
		{		
			if(laborator_verify_like($like_entry, $likes_arr))
			{
				$likes_arr[] = $like_entry;
				
				# Write to the post meta
				update_post_meta($post->ID, LAB_LIKE_VAR, $likes_arr);
				
				return true;
			}
		}
	}
	
	return false;
}


# Get Client ID (for current user session)
function laborator_likes_get_cid()
{
	$sess_id = session_id();
	
	return $sess_id;
}


# Verify if like is valid
function laborator_verify_like($like_entry, $likes = array())
{
	global $current_user;
	
	$user_can_like = true;
	
	$is_logged = is_user_logged_in();
	$time_diff = 1800; # time between two or more same ip's (annonymous mode) can like one item (seconds unit)
	
	$phase_2_verify_intervals = array();
	
	foreach($likes as $like)
	{
		extract($like);
		
		if($is_logged)
		{
			if($user_id == $current_user->ID)
			{
				$user_can_like = false;
				break;
			}
		}
		else
		{
			if($ip == $_SERVER['REMOTE_ADDR'])
			{
				$phase_2_verify_intervals[] = time() - $like['time'];
			}
		}
	}
	
	if(count($phase_2_verify_intervals))
	{
		$min_time = min($phase_2_verify_intervals);
		
		if($time_diff > $min_time)
		{
			$user_can_like = false;
		}
	}
	
	return $user_can_like;
}


# Generate Like Entry
function laborator_gen_like_entry()
{
	global $current_user;
	
	$cid = laborator_likes_get_cid();
	
	$like_entry = array(
		'anonymous' => is_user_logged_in() ? false : true,
		'time' 		=> time(),
		'cid' 		=> $cid,
		'ip' 		=> $_SERVER['REMOTE_ADDR'],
		'user_id' 	=> $current_user->ID
	);
	
	return $like_entry;
}


# Check if item is liked
function laborator_item_is_liked($post_id = null)
{
	global $post, $current_user;
	
	if($post_id)
	{
		$post = get_post($post_id);
	}
	
	if($post)
	{
		$like_entry = laborator_gen_like_entry();
		$likes_arr = laborator_get_likes($post_id, 2);
		
		
		return laborator_verify_like($like_entry, $likes_arr) ? false : true;
	}
	
	return false;
}


# Remove Like
function laborator_remove_like($like_entry, $likes)
{
	global $current_user;
	
	$is_logged = is_user_logged_in();
	$time_diff = 1800; # time between two or more same ip's (annonymous mode) can like one item (seconds unit)
	
	$new_likes_arr = array();
	$phase_2_verify_intervals = array();
	
	$i = 0;
	foreach($likes as $like)
	{
		extract($like);
		
		if($is_logged)
		{
			if($user_id != $current_user->ID)
			{
				$new_likes_arr[] = $like;
			}
		}
		else
		{
			if($time_diff > time() - $time)
			{
				if($like['ip'] != $_SERVER['REMOTE_ADDR'])
				{
					$new_likes_arr[] = $like;
				}
			}
			else
			{
				$new_likes_arr[] = $like;
			}
		}
		
		$i++;
	}
	
	return $new_likes_arr;
}



/* Admin Interface for Likes */
add_action('admin_init', 'laborator_likes_admin_init');

add_action('wp_ajax_laborator_likes', 'laborator_likes_process_ajax');
add_action('wp_ajax_nopriv_laborator_likes', 'laborator_likes_process_ajax');


function laborator_likes_admin_init()
{
	$likes_post_types = laborator_get_like_post_types();
	$post_types = get_post_types();
	
	foreach($likes_post_types as $post_type => $args)
	{
		if(isset($post_types[ $post_type ]))
		{
			add_meta_box($post_type . '-laborator-likes', $args['name'], "laborator_likes_add_meta_box", $post_type);
			
			add_action("manage_{$post_type}_posts_columns", 'laborator_likes_posts_columns');
			add_action("manage_{$post_type}_posts_custom_column", 'laborator_likes_posts_custom_column');
		}
	}
	
}

# WP_List_Table Columns for Showing Item Likes Title
function laborator_likes_posts_columns($posts_columns)
{	
	$post_id = count($posts_columns) - 1; # Position to move the number of likes
	
	$p1 = array_slice($posts_columns, 0, $post_id);
	$p1["laborator_likes"] = "Likes";
	
	$p2 = array_slice($posts_columns, $post_id);
	
	return array_merge($p1, $p2);
}


# WP_List_Table Columns for Showing Item Likes Number
function laborator_likes_posts_custom_column($column_name)
{
	global $post;
	
	if($column_name == "laborator_likes")
	{
		$likes_count = laborator_get_likes();
		
		if($likes_count)
			echo "<strong>";
			
		echo sprintf(_n('%d like', '%d likes', $likes_count, 'oxygen'), number_format($likes_count));
		
		if($likes_count)
			echo "</strong>";
	}
}


# Render Metabox
function laborator_likes_add_meta_box()
{
	global $post;
	
	$likes_arr = laborator_get_likes(null, 2);
	
	?>
	<style>
		#<?php echo $post->post_type; ?>-laborator-likes .inside {
			max-height: 250px;
			overflow: auto;
			margin: 0;
		}
		
		.laborator_likes_env {
			background: #FFF;
			border: 1px solid #EEE;
			width: 100%;
			padding: 0;
			margin: 10px 0;
			border-spacing: 0;
			border-collapse: collapse;
		}
		
		.laborator_likes_env th {
			text-align: left;
			padding: 5px;
			padding-bottom: 2px;
		}
		
		.laborator_likes_env .laborator_like_entry td {
			border-top: 1px solid #EEE;
			padding: 5px;
			color: #777;
			white-space: nowrap;
		}
		
		.laborator_likes_env thead {
			background: #FAFAFA;
		}
		
		.laborator_likes_env tbody tr:nth-child(even) {
			background: #FAFAFA;
		}
		
		.laborator_likes_env tfoot th {
			border: 1px solid #E0E0E0;
			border-top: 1px solid #E0E0E0;
			background: #f2f2f2;
		}
		
		.laborator_copyright {
			border-top: 1px solid #EEE;
			padding: 10px 0;
			font-size: 11px;
		}
	</style>
	
	<table class="laborator_likes_env">
		<thead>
			<tr>
				<th>User Type</th>
				<th>Name</th>
				<th>Time</th>
				<th>Address</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if( ! count($likes_arr)):

			?>
			<tr class="laborator_like_entry">
				<td colspan="4" align="center">
					<em>There are currently no likes for this item!</em>
				</td>
			</tr>
			<?php

		else:
		
			foreach($likes_arr as $like):
			
				$user = $like['user_id'] ? get_user_by('id', $like['user_id']) : null;
				
				?>
				<tr class="laborator_like_entry">
					<td width="20%"><?php echo $like['anonymous'] ? 'Anonymous' : 'Registered User'; ?></td>
					<td width="25%">
						<?php if($user): ?>
							<a href="<?php echo admin_url("user-edit.php?user_id={$like['user_id']}"); ?>" target="_blank"><?php echo $user->data->display_name; ?><a>
						<?php else: ?>
							<em>Unknown</em>
						<?php endif; ?>
					</td>
					<td><?php echo date_i18n(get_option('date_format') . ' - ' . get_option('time_format'), $like['time']); ?> (<?php echo human_time_diff(time(), $like['time']); ?> ago)</td>
					<td width="20%"><?php echo $like['ip']; ?></td>
				</tr>
				<?php
			
			endforeach;
			
		endif;
		?>
		</tbody>
		<?php
		if(count($likes_arr)):
		?>
		<tfoot>
			<tr>
				<th style="border-right: 0">Total Likes</th>
				<th style="border-left: 0" colspan="3"><?php echo number_format(count($likes_arr)); ?></th>
			</tr>
		</tfoot>
		<?php endif; ?>
	</table>
	
	<div class="laborator_copyright">
		&copy; <strong>Post Likes</strong> Plugin by <a href="http://www.laborator.co">Laborator.co</a>
	</div>
	<?php
}


# Process Like Requests (mainly from JavaScript)
function laborator_likes_process_ajax()
{
	global $laborator_supported_like_post_types;
	
	$resp = array(
		'errcode' => '',
		'errmsg' => '',
		'status' => ''
	);
	
	# Vars
	$post_id = get('post_id');
	$type = get('type') == 'unlike' ? 2 : 1;
	
	# Post
	$post = get_post($post_id);
	
	# Verify if the request is valid
	$nonce = isset($_REQUEST[LAB_LIKE_AJAX_NONCE_VAR]) ? $_REQUEST[LAB_LIKE_AJAX_NONCE_VAR] : '';
	
	if($post && isset($laborator_supported_like_post_types[$post->post_type]) && wp_verify_nonce($nonce, LAB_LIKE_NONCE_ACTION))
	{
		$err_code = 0;
	
		# Like Item
		if($type == 1)
		{
			$status_code = laborator_like_item($post_id) ? 1 : 2; # item liked or not
			
			if($status_code == 2)
			{
				$err_code = 1;
				$err_msg = __('You have already liked this item.', 'oxygen');
			}
			else
			{
				$err_msg = '';
			}
		}
		# Unlike Item
		if($type == 2)
		{
			laborator_like_item($post_id, true);
			
			$status_code = 3;
			$err_msg = '';
		}
		
		$resp['errcode']	= $err_code;
		$resp['errmsg']		= $err_msg;
		$resp['status']		= $status_code;
		$resp['likes'] 		= laborator_get_likes($post_id);
	}
	else
	{
		$resp['errcode'] 	= 1;
		$resp['errmsg'] 	= __('You are not allowed to like this item!', 'oxygen');
		$resp['status']		= 0;
	}
	
	/*
		error codes:
		0 - contains no errors
		1 - contains errors
		
		status codes:
		0 - item not liked (error)
		1 - item liked
		2 - item already liked
		3 - item unliked
	*/
	
	header( "Content-Type: application/json" );
	echo json_encode($resp);
	
	die();
}


# Generate Likes General Nonce
function laborator_likes_nonce()
{
	return wp_create_nonce(LAB_LIKE_NONCE_ACTION);
}



/*
JavaScript Code for Implementation of the Like functionality. Copy and paste to your script.

<script type="text/javascript">
function laborator_like_item(id, nonce, type, callback)
{
	if(typeof window.ajaxurl == 'string')
	{
		var like_data = {
			action: '@{LAB_LIKE_VAR}',
			{@LAB_LIKE_AJAX_NONCE_VAR}: nonce,
			post_id: id,
			type: type
		};
		
		jQuery.getJSON(window.ajaxurl, like_data, function(resp)
		{
			if(typeof callback == 'function')
			{
				callback(resp);
			}
		});
	}
	else
	{
		alert("Ajax URL is not defined!")
	}
}
</script>
*/