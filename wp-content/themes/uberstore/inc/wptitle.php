<?php
function thb_filter_wp_title( $title, $separator ) {

	if ( is_feed() ) return $title;
		
	global $paged, $page;

	if ( is_search() ) {
	
		//If we're a search, let's start over:
		$title = sprintf( __( 'Search results for %s', THB_THEME_NAME ), '"' . get_search_query() . '"' );
		//Add a page number if we're on page 2 or more:
		if ( $paged >= 2 )
			$title .= " $separator " . sprintf( __( 'Page %s', THB_THEME_NAME ), $paged );
		//Add the site name to the end:
		$title .= " $separator " . get_bloginfo( 'name', 'Display' );
		//We're done. Let's send the new title back to wp_title():
		return $title;
	}

	//Otherwise, let's start by adding the site name to the end:
	$title .= get_bloginfo( 'name', 'Display' );

	//If we have a site description and we're on the home/front page, add the description:
	$site_description = get_bloginfo( 'description', 'Display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $separator " . $site_description;

	//Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $separator " . sprintf( __( 'Page %s', THB_THEME_NAME ), max( $paged, $page ) );

	//Return the new title to wp_title():
	return $title;
}
add_filter( 'wp_title', 'thb_filter_wp_title', 10, 2 );
?>