<?php
/**
 *	Get Tweets
 *	Version: 2.1
 *	
 *	Laborator.co
 *	www.laborator.co 
 */


if( ! function_exists('get_latest_tweets'))
{
	if( ! class_exists('OAuthRequest'))
		require THEMEDIR . "inc/lib/twitteroauth/twitteroauth.php";

	function get_latest_tweets($userid, $status_count = 5, $api_credentials = array())
	{
		$transient_key 		= '_latest_tweets_' . $userid . '_' . $status_count;
		$transient_expire 	= 60 * 60 * 6; # 6 hours
		$include_rts 		= false;
		
		# API Access
		$consumerkey 		= get_data('twitter_consumerkey');
		$consumersecret 	= get_data('twitter_consumersecret');
		$accesstoken 		= get_data('twitter_accesstoken');
		$accesstokensecret	= get_data('twitter_accesstokensecret');

		
		if(is_array($api_credentials))
		{
			extract($api_credentials);
		}
		
		# check for transient
		if($fetched_tweets = get_transient($transient_key))
		{
			$latest_userid = get_transient("{$transient_key}_id");
			
			if($latest_userid == $userid)
			{
				$fetched_tweets = array_map('_replace_old_hashtag_array', $fetched_tweets); // Fix for older versions
				
				return $fetched_tweets;
			}
		}
	
		if( ! $consumerkey || ! $consumersecret || ! $accesstoken || ! $accesstokensecret)
		{
			return array();
		}
		
		$connection = new TwitterOAuth($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
		$get_tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$userid."&count=".$status_count."&include_rts=".$include_rts);
		
		$tweets = array();
		
		if( ! is_array($get_tweets))
		{
			return $tweets;
		}
		
		foreach($get_tweets as $status)
		{
			$text = (string) $status->text;
			$created_at = strtotime($status->created_at);
			$text = twitterify($text);
			
			$tweet_entry = array('text' => $text, 'time' => $created_at);
			
			array_push($tweets, $tweet_entry);
		}
		
		if(count($tweets))
		{
			set_transient($transient_key, $tweets, $transient_expire);
			set_transient("{$transient_key}_id", $userid, $transient_expire);
		}
		
		return $tweets;
	}
}

if( ! function_exists('twitterify'))
{
	function twitterify($ret) 
	{
	    $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
	    $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
	    $ret = preg_replace("/@(\w+)/", "<a href=\"https://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret);
	    $ret = preg_replace("/#(\w+)/", "<a href=\"https://twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $ret);
	    
	    return $ret;
	}
}

function _replace_old_hashtag_array($entry)
{
	return str_replace('http://search.twitter.com', 'https://twitter.com', $entry);
}