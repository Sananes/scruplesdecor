<?php
	
	// Shortcode: nm_social_profiles
	function nm_shortcode_social_profiles( $atts, $content = NULL ) {
		extract( shortcode_atts( array(
			'social_profile_facebook'	=> '',
			'social_profile_instagram'	=> '',
			'social_profile_twitter'	=> '',
			'social_profile_googleplus'	=> '',
			'social_profile_linkedin'	=> '',
			'social_profile_pinterest'	=> '',
			'social_profile_rss'		=> '',
			'social_profile_tumblr'		=> '',
			'social_profile_vimeo'		=> '',
			'social_profile_youtube'	=> '',
			'icon_size'					=> 'medium',
			'alignment'					=> 'center'
		), $atts ) );
		
		$social_profiles = array(
			'facebook'		=> array( 'title' => 'Facebook', 'url' => $social_profile_facebook ),
			'instagram'		=> array( 'title' => 'Instagram', 'url' => $social_profile_instagram ),
			'twitter'		=> array( 'title' => 'Twitter', 'url' => $social_profile_twitter ),
			'google-plus'	=> array( 'title' => 'Google+', 'url' => $social_profile_googleplus ),
			'linkedin'		=> array( 'title' => 'LinkedIn', 'url' => $social_profile_linkedin ),
			'pinterest'		=> array( 'title' => 'Pinterest', 'url' => $social_profile_pinterest ),
			'rss-square'	=> array( 'title' => 'RSS', 'url' => $social_profile_rss ),
			'tumblr'		=> array( 'title' => 'Tunblr', 'url' => $social_profile_tumblr ),
			'vimeo-square'	=> array( 'title' => 'Vimeo', 'url' => $social_profile_vimeo ),
			'youtube'		=> array( 'title' => 'YouTube', 'url' => $social_profile_youtube )
		);
		
		$output = '';
		foreach ( $social_profiles as $service => $details ) {
			if ( $details['url'] !== '' ) {
				$output .= '<li><a href="' . esc_url( $details['url'] ) . '" target="_blank" title="' . $details['title'] . '" class="dark"><i class="nm-font nm-font-' . $service . '"></i></a></li>';
			}
		}
		
		return '<ul class="nm-social-profiles icon-size-' . esc_attr( $icon_size ) . ' align-' . esc_attr( $alignment ) . '">' . $output . '</ul>';
	}
	
	add_shortcode( 'nm_social_profiles', 'nm_shortcode_social_profiles' );
	