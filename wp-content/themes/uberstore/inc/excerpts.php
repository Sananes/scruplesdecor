<?php

function thb_excerpt($excerpt_length, $added)
{
        $text = get_the_content('');
        $text = strip_shortcodes( $text );
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = strip_tags($text, '<em><strong><i><b>');
        
        $excerpt_more = apply_filters('excerpt_more', ' ' . $added);
        $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
        if ( count($words) > $excerpt_length ) {
            array_pop($words);
            $text = implode(' ', $words);
            $text = $text . $excerpt_more;
        } else {
            $text = implode(' ', $words);
        }
    echo $text;
}
function ShortenText($text, $chars_limit)
	{
	$text = strip_tags($text);
	$text = strip_shortcodes( $text );
	
	$chars_text = strlen($text);
	$text = $text." ";
	$text = substr($text,0,$chars_limit);
	$text = substr($text,0,strrpos($text,' '));
	
	if ($chars_text > $chars_limit) {
		$text = $text."...";
	}
	$text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text);
	return $text;
}
function parse_video($link) {
	$video_link = '';
	if (strpos($link, 'youtube') > 0) {
	
		$regexstr = '#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#';
		preg_match($regexstr, $link, $matches);
		$video_link = 'http://youtube.com/watch?v='.$matches[2];
		
	} elseif (strpos($link, 'vimeo') > 0) {
	
    $regexstr = '~
    		# Match Vimeo link and embed code
    		(?:<iframe [^>]*src=")?         # If iframe match up to first quote of src
    		(?:                             # Group vimeo url
    				https?:\/\/             		# Either http or https
    				(?:[\w]+\.)*            		# Optional subdomains
    				vimeo\.com              		# Match vimeo.com
    				(?:[\/\w]*\/videos?)?   		# Optional video sub directory this handles groups links also
    				\/                      		# Slash before Id
    				([0-9]+)                		# $1: VIDEO_ID is numeric
    				[^\s]*                  		# Not a space
    		)                               # End group
    		"?                              # Match end quote if part of src
    		(?:[^>]*></iframe>)?            # Match the end of the iframe
    		(?:<p>.*</p>)?                  # Match any title information stuff
    		~ix';
    preg_match_all($regexstr, $link, $matches);
    $video_link = 'http://vimeo.com/'.$matches[1][0];
	}
	
	return $video_link;
	
}
?>