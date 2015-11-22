<?php
/**
 *	Laborator Twitter Widget
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
 

class Laborator_Twitter extends WP_Widget
{
	public function __construct()
	{
		$title = 'Twitter';
		$desc = 'Show latest tweets from your twitter account.';
		
		parent::__construct(false, '[Laborator] ' . $title, array('description' => $desc), array('width' => 340));
	}
	
	
	public function widget($args, $instance)
	{
		extract($instance);
		
		wp_enqueue_script(array('laborator_tweetroller'));
		
		if( ! isset($instance['title']))
			$instance['title'] = '';
			
		$count 				= $instance['count'];
		$per_page	 		= $instance['per_page'];
		$autoswitch 		= $instance['autoswitch'];
		$height 			= $instance['height'];
		$tweets_effect 		= $instance['tweets_effect'];
		$twitter_username 	= $instance['twitter_username'];
		
		$tweets_per_page	= $per_page;
		
		if( ! $twitter_username)
		{
			return;
		}
		
		// Widget Start
		echo $args['before_widget'];
		
		// Display Title
		$title = apply_filters('widget_title', empty($instance['title']) ? 'Tweets' : $instance['title'], $instance, $this->id_base);
		
		if($title)
			echo PHP_EOL . $args['before_title'] . $title . $args['after_title'] . PHP_EOL;
		
		if( ! $twitter_username)
		{
			_e('Please configure Twitter account at admin panel.', TD);
			return;
		}
		
		$api_credentials = array();
		
		if($twitter_consumerkey)
		{
			$api_credentials = array(
				'consumerkey' 		=> $twitter_consumerkey,
				'consumersecret' 	=> $twitter_consumersecret,
				'accesstoken' 		=> $twitter_accesstoken,
				'accesstokensecret'	=> $twitter_accesstokensecret
			);
		}
		
		$tweets = get_latest_tweets($twitter_username, $count, $api_credentials);
			
		?>
		
		<!-- tweets -->
		<ul class="tweets"<?php echo $autoswitch > 0 ? (' data-autoswitch="' . ($autoswitch * 1000) . '"') : ''; ?> data-user="<?php echo $twitter_username; ?>" data-count="<?php echo $count; ?>" data-num="<?php echo $tweets_per_page; ?>">
		<?php

			foreach($tweets as $tweet)
			{
				$text = $tweet['text'];
				$time = $tweet['time'];
			?>			
			<li>
				<!-- tweet item -->
				<p>
					<?php echo $text; ?>
				</p>
				<span class="date"><?php echo date_i18n("d M Y", $time); ?></span>
				<!-- end: tweet item -->
			</li>
			<?php
			}
		?>
		</ul>
		<?php
		
		// Widget End
		echo $args['after_widget'];
	}
	
	
	public function update($new_instance, $old_instance)
	{
		$defaults = array(
			'twitter_username' => ''
		);
		
		$old_instance = array_merge($defaults, $old_instance);
		
		// Title
		$old_instance['title'] = post('title');
		$old_instance['twitter_username'] = post('twitter_username');
		$old_instance['count'] = post('count');
		$old_instance['per_page'] = post('per_page');
		$old_instance['autoswitch'] = post('autoswitch');
		$old_instance['tweets_effect'] = post('tweets_effect');
		$old_instance['height'] = post('height');
		
		$old_instance['twitter_consumerkey'] = post('twitter_consumerkey');
		$old_instance['twitter_consumersecret'] = post('twitter_consumersecret');
		$old_instance['twitter_accesstoken'] = post('twitter_accesstoken');
		$old_instance['twitter_accesstokensecret'] = post('twitter_accesstokensecret');
		
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
			'twitter_username' => '',
			'tweets_effect' => '',
			'autoswitch' => '',
			'height' => '',
			'twitter_consumerkey' => '',
			'twitter_consumersecret' => '',
			'twitter_accesstoken' => '',
			'twitter_accesstokensecret' => ''
		);
		
		$instance = array_merge($defaults, $instance);
		
		extract($instance);
		
		?>
		<p>
			<label for="title">Display Title:</label>
			<input type="text" id="title" name="title" class="nl" value="<?php echo $instance['title']; ?>" />
		</p>
		
		<p>
			<label for="twitter_username">Twitter Account:</label>
			<input type="text" id="twitter_username" name="twitter_username" class="nl" value="<?php echo $instance['twitter_username']; ?>" />
			
			<?php if( ! ($twitter_consumerkey && $twitter_consumersecret && $twitter_accesstoken && $twitter_accesstokensecret)): ?>
			<span style="font-size: 10px;" class="description">
				This widget requires <strong>Twitter API Configuration</strong>, make sure have already Access Token for your Twitter account. 
				<br />
				Then apply the Twitter App consumer key, secret and access to this widget (click <strong>Show API Options</strong>).
				<br />
				For more information about how to create twitter app, click <a href="http://iag.me/socialmedia/how-to-create-a-twitter-app-in-8-easy-steps/" target="_blank">here</a>.
			</span>
			<?php endif; ?>
		</p>
		
		<p>
			<label for="count">Count:</label>
			<input type="number" min="0" id="count" name="count" class="nl" placeholder="5 latest tweets by default" value="<?php echo $instance['count']; ?>" />
		</p>
		
		<p>
			<label for="per_page">Paginate Tweets:</label>
			<input type="number" min="0" id="per_page" name="per_page" class="nl" placeholder="2 tweets per row by default" value="<?php echo $instance['per_page']; ?>" />
		</p>
		<?php /*
		<p>
			<label for="autoswitch">Auto-Switch:</label>
			<input type="number" min="0" id="autoswitch" name="autoswitch" class="nl" placeholder="10 (seconds, 0 - disabled)" value="<?php echo $instance['autoswitch']; ?>" />
		</p><p>
			<label for="tweets_effect">Effect:</label>
			<select name="tweets_effect" class="nl">
				<option value="fade">Fade</option>
				<option value="puff"<?php echo $instance['tweets_effect'] == 'puff' ? ' selected' : ''; ?>>Puff</option>
			</select>
		</p>*/ ?>
		
		<div class="clear"></div>
		<a href="#" data-less="Hide Options" data-more="Show API Options"><?php echo defined('OG_VISIBLE') ? 'Hide Options' : 'Show API Options'; ?></a>
		
		<div class="options_group<?php echo ! defined('OG_VISIBLE') ? ' hidden_option' : ''; ?>">
			
			<input type="hidden" name="og_visible" value="<?php echo defined('OG_VISIBLE') ? '1' : '0'; ?>"></input>
			
			<p>
				<label for="twitter_consumerkey">Twitter Consumer Key:</label>
				<input type="text" id="twitter_consumerkey" name="twitter_consumerkey" class="nl" value="<?php echo $instance['twitter_consumerkey']; ?>" />
			</p>
			
			<p>
				<label for="twitter_consumersecret">Twitter Consumer Secret:</label>
				<input type="text" id="twitter_consumersecret" name="twitter_consumersecret" class="nl" value="<?php echo $instance['twitter_consumersecret']; ?>" />
			</p>
			
			<p>
				<label for="twitter_accesstoken">Twitter Access Token:</label>
				<input type="text" id="twitter_accesstoken" name="twitter_accesstoken" class="nl" value="<?php echo $instance['twitter_accesstoken']; ?>" />
			</p>
			
			<p>
				<label for="twitter_accesstokensecret">Twitter Access Token Secret:</label>
				<input type="text" id="twitter_accesstokensecret" name="twitter_accesstokensecret" class="nl" value="<?php echo $instance['twitter_accesstokensecret']; ?>" />
			</p>
			
		</div>
		<?php
	}
}



// Register widget
add_action('widgets_init', 'init_laborator_twitter_widget_v2');

function init_laborator_twitter_widget_v2(){
	
	register_widget('Laborator_Twitter');

}