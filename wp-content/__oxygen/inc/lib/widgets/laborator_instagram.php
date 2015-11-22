<?php
/**
 *	Instagram Slideshow
 *	
 *	Laborator.co
 *	www.laborator.co 
 */


class Laborator_InstaSlideshow extends WP_Widget
{
	public static $update_timeout = 43200; // Seconds - 43200=12hrs
	
	public function __construct()
	{
		$title = 'Instagram Slideshow';
		$desc = 'Photostream of Instagram user/hashtag.';
		
		parent::__construct(false, '[Laborator] ' . $title, array('description' => $desc));
	}
	
	
	public function widget($args, $instance)
	{
		extract($instance);
		
		wp_enqueue_script('laborator_posts_slider');
		
		if( ! isset($instance['title']))
			$instance['title'] = '';
			
		
		$use_standard_resolution = true;
		
		// Widget Start
		echo $args['before_widget'];
		
		// Display Title
		$title = apply_filters('widget_title', empty($instance['title']) ? 'Instagram' : $instance['title'], $instance, $this->id_base);
		
		if($title)
			echo PHP_EOL . $args['before_title'] . $title . $args['after_title'] . PHP_EOL;
			
		if( ! $per_page)
			$per_page = 8;
		
		if($valid_req)
		{
			$details_images = unserialize(base64_decode($details_images));
			
			$user = $details_images['user'];
			$images = $details_images['images'];
			
			if(count($images)):
			
				if($shuffle_images)
					shuffle($images);
			?>
			<div class="instagram-gallery" data-num="<?php echo intval($per_page); ?>">
				
				<?php
				foreach($images as $i => $image):
					
					$is_video = $image->type == 'video';
					
					$link = $image->link;
					$caption = $image->caption;
					$likes = $image->likes;
					$created_time = $image->created_time;
					$tags = implode(', ', $image->tags);
					
					$low_resolution = $image->images->low_resolution->url;
					$standard_resolution = $image->images->standard_resolution->url;
					
					$classes = array('image_entry');
					
					if($i > 0)
					{
						//$classes[] = 'hidden';
					}
				?>
				<a href="<?php echo $link; ?>" target="_blank" class="<?php echo implode(' ', $classes); ?>">
					<img src="<?php echo $use_standard_resolution ? $standard_resolution : $low_resolution; ?>" />
					
					<?php if(false&&$caption): ?>
					<span class="caption" title="<?php echo esc_attr($caption); ?>"><?php echo removeEmoji($caption); ?></span>
					<?php endif; ?>
					
					<?php if($is_video): ?>
					<i class="entypo-play"></i>
					<?php endif; ?>
				</a>
				<?php
				endforeach;
				?>
			</div>
			
			<i class="instagram" title="Instagram"></i>
			<?php
			endif;
		}
		
		// Widget End
		echo $args['after_widget'];
	}
	
	
	public function update($new_instance, $old_instance)
	{
		$defaults = array(
			'count' => '',
			'browse_mode' => '',
			'query' => ''
		);
		
		$old_instance = array_merge($defaults, $old_instance);
		
		$old_count = $old_instance['count'];
		$old_browse_mode = $old_instance['browse_mode'];
		$old_query = $old_instance['query'];
		
		$old_instance['title'] = post('title');
		$old_instance['count'] = post('count');
		$old_instance['per_page'] = post('per_page');
		$old_instance['browse_mode'] = post('browse_mode');
		$old_instance['query'] = post('query');
		$old_instance['thumb_height'] = post('thumb_height');
		$old_instance['thumb_crop_method'] = post('thumb_crop_method');
		$old_instance['client_id'] = post('client_id');
		$old_instance['shuffle_images'] = post('shuffle_images');
		
		$old_instance['valid_req']	= false;
		$old_instance['valid_api_credentials']	= null;
		
		if( ! isset($old_instance['last_update']))
		{
			$old_instance['last_update'] = 0;
		}
		
		if($old_instance['client_id'])
		{
			if(
				true ||
				//$old_instance['last_update'] < time() - 60 || 
				$old_count != post('count') || 
				$old_browse_mode != post('browse_mode') || 
				$old_query != post('query')
			)
			{	
				$insta_resp = self::fetch_images(array(
					'query' => post('query'), 
					'browse_mode' => post('browse_mode'), 
					'count' => post('count'), 
					'client_id' => post('client_id')
				));
				
				if($insta_resp['errors'] == false)
				{
					$old_instance['valid_req'] = true;
					$old_instance['details_images'] = base64_encode(serialize($insta_resp));
					$old_instance['valid_api_credentials'] = true;
					$old_instance['last_update'] = time();
				}
				else
				{
					define("ME_IS_ERRORS", implode('<br />', $insta_resp['errors_msg']));
				}
			}
			else
			{
				$old_instance['valid_req'] = $old_instance['valid_api_credentials'] = true;
			}
		}
		
		if(post('og_visible') == 1)
		{
			define("OG_VISIBLE", true);
		}
		
		return $old_instance;
	}
	
	
	public function form($instance)
	{
		$defaults = array(
			'title' => '',
			'per_page' => '',
			'query' => '',
			'browse_mode' => '',
			'count' => '',
			'thumb_height' => '',
			'thumb_crop_method' => '',
			'client_id' => '',
			'valid_req' => '',
			'details_images' => '',
			'shuffle_images' => ''
		);
		
		$instance = array_merge($defaults, $instance);
		
		if(defined("ME_IS_ERRORS"))
		{
			?>
			<div class="error">
				<?php echo ME_IS_ERRORS; ?>
			</div>
			<?php
		}
		
		?>
		<p>
			<label for="title">Display Title:</label>
			<input type="text" id="title" name="title" class="nl" value="<?php echo $instance['title']; ?>" />
		</p>
			
		<p>
			<label for="count">Count:</label>
			<input type="number" min="0" id="count" name="count" class="nl" placeholder="Images Count" value="<?php echo $instance['count']; ?>" />
		</p>
		
		<p>
			<label for="per_page">Paginate Images:</label>
			<input type="number" min="0" id="per_page" name="per_page" class="nl" placeholder="Default: 8 images" value="<?php echo $instance['per_page']; ?>" />
		</p>
		
		<p>
			<label for="browse_mode">Browse Mode:</label>
			
			<select name="browse_mode" class="nl">
				<option value="user">User</option>
				<option value="tagged"<?php echo $instance['browse_mode'] == 'tagged' ? ' selected' : ''; ?>>Hashtag</option>
				<option value="popular"<?php echo $instance['browse_mode'] == 'popular' ? ' selected' : ''; ?>>Popular</option>
			</select>
		</p>
			
		<p>
			<label for="query">Query:</label>
			<input type="text" id="query" name="query" class="nl" placeholder="Username/Hashtag" value="<?php echo $instance['query']; ?>" />
			<span class="description" style="font-size: 10px;">
				If browsing mode is set to <strong>Hashtag</strong> put the hashtags to search, if <strong>User</strong> is selected, enter the Instagram username (without any symbol @ or #). <br />
				Note: Only public profiles photos will be fetched.
			</span>
		</p>
			
		<p>
			<label for="client_id">Client ID:</label>
			<input type="text" id="client_id" name="client_id" class="nl" placeholder="Client ID Hash" value="<?php echo $instance['client_id'] ? $instance['client_id'] : '59a113fa0bd04a4ba05d478c0e99de47'; ?>" />
			<span class="description" style="font-size: 10px;">
				Before proceeding, you must register an Application on <a href="http://instagram.com/developer/clients/manage/" target="_blank">Instagram Developer Centre</a>. After you create a <strong>Client</strong> you'll get the Client ID and paste it here in order for this widget to work.
			</span>
		</p>
		
		
		<?php
		if($instance['valid_req']): 
			
			$details_images = unserialize(base64_decode($instance['details_images']));
			
			if(is_array($details_images))
			{
				$user = $details_images['user'];
				$images = $details_images['images'];
				
				?>
				<p>
					<span class="nl description">
						Total images fetched: <strong><?php echo count($images); ?></strong>
					</span>
					
					<div class="nl">
						<?php foreach($images as $image): ?>
						<a href="<?php echo $image->link; ?>" target="_blank"><img src="<?php echo $image->images->thumbnail->url; ?>" width="18" height="18" /></a>
						<?php endforeach; ?>
					</div>
					
					<?php if($instance['browse_mode'] == 'user' && is_object($user)): ?>
					<strong style="display: block; padding-top: 8px;">Profile</strong>
					
					<style>
						#<?php echo $this->id; ?>_profile a {
							text-decoration: none;
							margin-top: 5px;
							display: block;
						}
						
						#<?php echo $this->id; ?>_profile a img {
							margin-right: 5px;
							border: 1px solid #CCC;
							padding: 2px;
							background: #FFF;
						}
						
						#<?php echo $this->id; ?>_profile a span.username {
							font-size: 10px;
							font-weight: bold;
							color: #000;
						}
					</style>
					
					<div class="nl" id="<?php echo $this->id; ?>_profile">
						<a href="http://instagram.com/<?php echo $user->username; ?>" target="_blank">
							<img src="<?php echo $user->profile_picture; ?>" align="left" width="32" />
							<?php if($user->full_name): ?><span><?php echo removeEmoji($user->full_name); ?></span><br /><?php endif; ?>
							<span class="username"><?php echo $user->username; ?></span>
						</a>
					</div>
					<?php endif; ?>
				</p>
				<?php
			}
			
		endif; ?>
		
		<div class="clear"></div>
		<a href="#" data-less="Less Options" data-more="More Options"><?php echo defined('OG_VISIBLE') ? 'Less' : 'More'; ?> Options</a>
		
		<div class="options_group<?php echo ! defined('OG_VISIBLE') ? ' hidden_option' : ''; ?>">
			
			<input type="hidden" name="og_visible" value="<?php echo defined('OG_VISIBLE') ? '1' : '0'; ?>"></input>
				
			<p>
				<label for="thumb_height">Thumbnail Height:</label>
				<input type="number" min="0" step="10" id="thumb_height" name="thumb_height" class="nl" placeholder="Image Thumbnail Height" value="<?php echo $instance['thumb_height']; ?>" />
			</p>
			
			<p>
				<label for="thumb_crop_method">Thumbnail Cropping Method:</label>
				
				<select name="thumb_crop_method" class="nl">
					<option value="center">Crop Center</option>
					<option value="top"<?php echo $instance['thumb_crop_method'] == 'top' ? ' selected' : ''; ?>>Top</option>
					<option value="boxed"<?php echo $instance['thumb_crop_method'] == 'boxed' ? ' selected' : ''; ?>>Fit to Box</option>
				</select>
			</p>	
			
			<p>
				<label>
					<input type="checkbox" id="shuffle_images" name="shuffle_images" value="1" <?php echo $instance['shuffle_images'] ? ' checked' : ''; ?> />
					Shuffle Images
				</label>
			</p>
			
		</div>
		<?php
	}
	
	
	public static function fetch_images($args = array())
	{
		extract($args);
		
		$access_token = '22169740.59a113f.ffb2ffd32b064ea3974da070b8f6a0f9';
		
		$base 		= "https://api.instagram.com/v1";
		$endpoint 	= '';
		$query 		= urlencode($query);
		
		$resp = array(
			'errors' 		=> 0,
			'errors_msg' 	=> array(),
			'user' 			=> null,
			'images' 		=> array()
		);
		
		switch($browse_mode)
		{
			case 'popular':
				$endpoint = "media/popular";
				break;
				
			case 'tagged':
				$endpoint = "tags/{$query}/media/recent";
				$resp['errors'] = 0;
				break;
				
			case 'user':
			default:
				
				$search_user_url = $base . '/users/search?client_id=' . $client_id . '&q=' . $query;
				
				$user_info_req = laborator_get_url($search_user_url);
				
				$user_id = 0;
				
				if(is_wp_error($user_info_req))
				{
					$resp['errors'] = 1;
					$resp['errors_msg'] = $user_info_req->get_error_message();
				}
				else
				{				
					$user_info = json_decode($user_info_req['body']);
					
					if($user_info->meta->code == 200)
					{
						$results = $user_info->data;
						$user = reset($results);
										
						if($user)
						{
							$resp['user'] = $user;
							$user_id = $user->id;
						}
						else
						{	
							$resp['errors'] = 1;
							$resp['errors_msg'][] = "Couldn't fetch the user <strong>{$query}</strong>.";
						}
					}
				}
				
				$endpoint = "users/{$user_id}/media/recent";
		}
		
		$query_string = array(
			'client_id' => $client_id,
			'access_token' => $access_token
		);
		
		if(isset($count))
		{
			$query_string['count'] = $count;
		}
		
		$req_url = $base . '/' . $endpoint . '/?' . http_build_query($query_string);
		
		
		if( ! $resp['errors'])
		{
			$req = wp_remote_get($req_url);
			
			if( ! is_wp_error($req))
			{
				$req_json = json_decode($req['body']);
				
				if($req_json->meta->code == 200)
				{
					$data = $req_json->data;
					$images = self::data_to_images_arr($data);
					
					$resp['images'] = $images;
				}
			}
			else
			{
				$resp['errors'] = true;
				$resp['errors_msg'][] = $req->get_error_message();
			}
		}
		
		return $resp;
	}
	
	
	public static function data_to_images_arr($data)
	{
		$images_list = array();
		
		if(is_array($data))
		{
			foreach($data as $image)
			{	
				$id 			= $image->id;
				$type 			= $image->type; // image|video
				
				$images 		= $image->images;
				$link 			= $image->link;
				$caption 		= isset($image->caption) ? $image->caption->text : '';
				
				$likes 			= $image->likes->count;
				$comments 		= $image->comments->count;
				
				$created_time 	= $image->created_time;
				
				$tags 			= $image->tags;
				
				# Image Object
				$image_obj		= (object) compact('id', 'type', 'images', 'link', 'caption', 'likes', 'comments', 'created_time', 'tags');
				
				$images_list[]	= $image_obj;
			}
		}
		
		return  $images_list;
	}
}



// Register widget
add_action('widgets_init', 'init_laborator_instagram_widget');

function init_laborator_instagram_widget(){
	
	register_widget('Laborator_InstaSlideshow');

}

function laborator_instagram_update()
{
	$widget_laboratorme_instaslideshow = get_option('widget_laboratorme_instaslideshow');
	$check_timeout = Laborator_InstaSlideshow::$update_timeout; // seconds (every 60 minutes)
	
	$save_changes = false;
	
	if( ! is_array($widget_laboratorme_instaslideshow))
		return;
	
	$requests_sent = 0;
	$max_requests = 2;
	
	foreach($widget_laboratorme_instaslideshow as $wid => $widget)
	{
		if(is_numeric($wid))
		{	
			$last_update = $widget['last_update'];
				
			if($widget['valid_req'] || $last_update)
			{	
				// Requests Counter	Check
				if($requests_sent >= $max_requests)
				{
					continue;
				}
				
				if($last_update < time() - $check_timeout)
				{
					// Update Instagram Feed
					$save_changes = true;
					
					$query = $widget['query'];
					$browse_mode = $widget['browse_mode'];
					$count = $widget['count'];
					$client_id = $widget['client_id'];
					
					$details_images = Laborator_InstaSlideshow::fetch_images(array(
						'query' => $query, 
						'browse_mode' => $browse_mode, 
						'count' => $count,
						'client_id' => $client_id
					));
					
					if( ! is_wp_error($details_images))
					{
						$widget_laboratorme_instaslideshow[$wid]['last_update'] = time();
					
						$widget_laboratorme_instaslideshow[$wid]['valid_req'] = true;
						$widget_laboratorme_instaslideshow[$wid]['details_images'] = base64_encode(serialize($details_images));
						$widget_laboratorme_instaslideshow[$wid]['valid_api_credentials'] = true;
						$widget_laboratorme_instaslideshow[$wid]['last_update'] = time();
					}
				}
			}
		}
	}
	
	if($save_changes)
	{
		update_option('widget_laboratorme_instaslideshow', $widget_laboratorme_instaslideshow);
	}
}

add_action('init', 'laborator_instagram_update');