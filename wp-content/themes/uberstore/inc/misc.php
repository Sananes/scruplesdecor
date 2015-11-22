<?php

/* Custom Background Support */
$args = array(
	'default-color' => 'ffffff'
);
add_theme_support( 'custom-background', $args );


/* Add SoundCloud oEmbed */
function add_oembed_soundcloud(){
	wp_oembed_add_provider( 'http://soundcloud.com/*', 'http://soundcloud.com/oembed' );
}
add_action('init','add_oembed_soundcloud');

/* Get Portfolio Page Link */
function get_portfolio_page_link($post_id) {
    global $wpdb;
	
    $results = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta
    WHERE meta_key='_wp_page_template' AND meta_value='template-portfolio.php' OR meta_value='template-portfolio-shapes.php' OR meta_value='template-portfolio-paginated.php'");

    foreach ($results as $result) 
    {
        $page_id = $result->post_id;
    }
	
    return get_page_link($page_id);
} 

/* Required Settings */
if(!isset($content_width)) $content_width = 1170;
add_theme_support( 'automatic-feed-links' );

/* Read More class */
add_filter( 'the_content_more_link', 'add_morelink_classes' );
function add_morelink_classes( $more_link_html ) {
	// Example - else this var has no scope inside the function
	global $var_declared_outside_function;

	$new_classes = array( 'btn small' );
	$more_link_html = str_replace( 'class="more-link', 'class="' . implode( ' ', $new_classes ) . ' more-link', $more_link_html );

	return $more_link_html;
}

/* Remove WP default inline CSS for ".recentcomments a" from header */
add_action('widgets_init', 'my_remove_recent_comments_style');
function my_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
}

/* Remove Unwanted Tags */
function remove_invalid_tags($str, $tags) 
{
    foreach($tags as $tag)
    {
    	$str = preg_replace('#^<\/'.$tag.'>|<'.$tag.'>$#', '', $str);
    }

    return $str;
}

/* Category Rel Fix */
function remove_category_list_rel( $output ) {
    return str_replace( ' rel="category tag"', '', $output );
}
 
add_filter( 'wp_list_categories', 'remove_category_list_rel' );
add_filter( 'the_category', 'remove_category_list_rel' );

/* Editor Styling */
add_editor_style();

/* Add Twitter oEmbed */
add_filter('oembed_result','twitter_no_width',10,3);
function twitter_no_width($html, $url, $args) {
    if (false !== strpos($url, 'twitter.com')) {
        $html = str_replace('width="550"','',$html);
    }
    return $html;
}

/* Remove default styling for Gallery Shortcode */
add_filter('gallery_style',
	create_function(
		'$css',
		'return preg_replace("#<style type=\'text/css\'>(.*?)</style>#s", "", $css);'
	)
);

/* Fix Image Margins */
class fixImageMargins{
    public $xs = 0; //change this to change the amount of extra spacing

    public function __construct(){
        add_filter('img_caption_shortcode', array(&$this, 'fixme'), 10, 3);
    }
    public function fixme($x=null, $attr, $content){

        extract(shortcode_atts(array(
                'id'    => '',
                'align'    => 'alignnone',
                'width'    => '',
                'caption' => ''
            ), $attr));

        if ( 1 > (int) $width || empty($caption) ) {
            return $content;
        }

        if ( $id ) $id = 'id="' . $id . '" ';

    return '<div ' . $id . 'class="wp-caption ' . $align . '" style="width: ' . ((int) $width + $this->xs) . 'px">'
    . $content . '<p class="wp-caption-text"><i class="fa fa-picture"></i> ' . $caption . '</p></div>';
    }
}
$fixImageMargins = new fixImageMargins();


/* Author FB, TW & G+ Links */
function my_new_contactmethods( $contactmethods ) {
// Add Twitter
$contactmethods['twitter'] = 'Twitter URL';
// Add Facebook
$contactmethods['facebook'] = 'Facebook URL';
// Add Google+
$contactmethods['googleplus'] = 'Google Plus URL';

return $contactmethods;
}
add_filter('user_contactmethods','my_new_contactmethods',10,1);

/* Font Awesome Array */
if (!function_exists('getFontAwesomeIconArray')){
	function getFontAwesomeIconArray(){
		$icons = array(
				'' => '',
				'fa-adjust' => 'fa-adjust',
				'fa-anchor' => 'fa-anchor',
				'fa-archive' => 'fa-archive',
				'fa-arrows' => 'fa-arrows',
				'fa-arrows-h' => 'fa-arrows-h',
				'fa-arrows-v' => 'fa-arrows-v',
				'fa-asterisk' => 'fa-asterisk',
				'fa-automobile' => 'fa-automobile',
				'fa-ban' => 'fa-ban',
				'fa-bank' => 'fa-bank',
				'fa-bar-chart-o' => 'fa-bar-chart-o',
				'fa-barcode' => 'fa-barcode',
				'fa-bars' => 'fa-bars',
				'fa-beer' => 'fa-beer',
				'fa-bell' => 'fa-bell',
				'fa-bell-o' => 'fa-bell-o',
				'fa-bolt' => 'fa-bolt',
				'fa-bomb' => 'fa-bomb',
				'fa-book' => 'fa-book',
				'fa-bookmark' => 'fa-bookmark',
				'fa-bookmark-o' => 'fa-bookmark-o',
				'fa-briefcase' => 'fa-briefcase',
				'fa-bug' => 'fa-bug',
				'fa-building' => 'fa-building',
				'fa-building-o' => 'fa-building-o',
				'fa-bullhorn' => 'fa-bullhorn',
				'fa-bullseye' => 'fa-bullseye',
				'fa-cab' => 'fa-cab',
				'fa-calendar' => 'fa-calendar',
				'fa-calendar-o' => 'fa-calendar-o',
				'fa-camera' => 'fa-camera',
				'fa-camera-retro' => 'fa-camera-retro',
				'fa-car' => 'fa-car',
				'fa-caret-square-o-down' => 'fa-caret-square-o-down',
				'fa-caret-square-o-left' => 'fa-caret-square-o-left',
				'fa-caret-square-o-right' => 'fa-caret-square-o-right',
				'fa-caret-square-o-up' => 'fa-caret-square-o-up',
				'fa-certificate' => 'fa-certificate',
				'fa-check' => 'fa-check',
				'fa-check-circle' => 'fa-check-circle',
				'fa-check-circle-o' => 'fa-check-circle-o',
				'fa-check-square' => 'fa-check-square',
				'fa-check-square-o' => 'fa-check-square-o',
				'fa-child' => 'fa-child',
				'fa-circle' => 'fa-circle',
				'fa-circle-o' => 'fa-circle-o',
				'fa-circle-o-notch' => 'fa-circle-o-notch',
				'fa-circle-thin' => 'fa-circle-thin',
				'fa-clock-o' => 'fa-clock-o',
				'fa-cloud' => 'fa-cloud',
				'fa-cloud-download' => 'fa-cloud-download',
				'fa-cloud-upload' => 'fa-cloud-upload',
				'fa-code' => 'fa-code',
				'fa-code-fork' => 'fa-code-fork',
				'fa-coffee' => 'fa-coffee',
				'fa-cog' => 'fa-cog',
				'fa-cogs' => 'fa-cogs',
				'fa-comment' => 'fa-comment',
				'fa-comment-o' => 'fa-comment-o',
				'fa-comments' => 'fa-comments',
				'fa-comments-o' => 'fa-comments-o',
				'fa-compass' => 'fa-compass',
				'fa-credit-card' => 'fa-credit-card',
				'fa-crop' => 'fa-crop',
				'fa-crosshairs' => 'fa-crosshairs',
				'fa-cube' => 'fa-cube',
				'fa-cubes' => 'fa-cubes',
				'fa-cutlery' => 'fa-cutlery',
				'fa-dashboard' => 'fa-dashboard',
				'fa-database' => 'fa-database',
				'fa-desktop' => 'fa-desktop',
				'fa-dot-circle-o' => 'fa-dot-circle-o',
				'fa-download' => 'fa-download',
				'fa-edit' => 'fa-edit',
				'fa-ellipsis-h' => 'fa-ellipsis-h',
				'fa-ellipsis-v' => 'fa-ellipsis-v',
				'fa-envelope' => 'fa-envelope',
				'fa-envelope-o' => 'fa-envelope-o',
				'fa-envelope-square' => 'fa-envelope-square',
				'fa-eraser' => 'fa-eraser',
				'fa-exchange' => 'fa-exchange',
				'fa-exclamation' => 'fa-exclamation',
				'fa-exclamation-circle' => 'fa-exclamation-circle',
				'fa-exclamation-triangle' => 'fa-exclamation-triangle',
				'fa-external-link' => 'fa-external-link',
				'fa-external-link-square' => 'fa-external-link-square',
				'fa-eye' => 'fa-eye',
				'fa-eye-slash' => 'fa-eye-slash',
				'fa-fax' => 'fa-fax',
				'fa-female' => 'fa-female',
				'fa-fighter-jet' => 'fa-fighter-jet',
				'fa-file-archive-o' => 'fa-file-archive-o',
				'fa-file-audio-o' => 'fa-file-audio-o',
				'fa-file-code-o' => 'fa-file-code-o',
				'fa-file-excel-o' => 'fa-file-excel-o',
				'fa-file-image-o' => 'fa-file-image-o',
				'fa-file-movie-o' => 'fa-file-movie-o',
				'fa-file-pdf-o' => 'fa-file-pdf-o',
				'fa-file-photo-o' => 'fa-file-photo-o',
				'fa-file-picture-o' => 'fa-file-picture-o',
				'fa-file-powerpoint-o' => 'fa-file-powerpoint-o',
				'fa-file-sound-o' => 'fa-file-sound-o',
				'fa-file-video-o' => 'fa-file-video-o',
				'fa-file-word-o' => 'fa-file-word-o',
				'fa-file-zip-o' => 'fa-file-zip-o',
				'fa-film' => 'fa-film',
				'fa-filter' => 'fa-filter',
				'fa-fire' => 'fa-fire',
				'fa-fire-extinguisher' => 'fa-fire-extinguisher',
				'fa-flag' => 'fa-flag',
				'fa-flag-checkered' => 'fa-flag-checkered',
				'fa-flag-o' => 'fa-flag-o',
				'fa-flash' => 'fa-flash',
				'fa-flask' => 'fa-flask',
				'fa-folder' => 'fa-folder',
				'fa-folder-o' => 'fa-folder-o',
				'fa-folder-open' => 'fa-folder-open',
				'fa-folder-open-o' => 'fa-folder-open-o',
				'fa-frown-o' => 'fa-frown-o',
				'fa-gamepad' => 'fa-gamepad',
				'fa-gavel' => 'fa-gavel',
				'fa-gear' => 'fa-gear',
				'fa-gears' => 'fa-gears',
				'fa-gift' => 'fa-gift',
				'fa-glass' => 'fa-glass',
				'fa-globe' => 'fa-globe',
				'fa-graduation-cap' => 'fa-graduation-cap',
				'fa-group' => 'fa-group',
				'fa-hdd-o' => 'fa-hdd-o',
				'fa-headphones' => 'fa-headphones',
				'fa-heart' => 'fa-heart',
				'fa-heart-o' => 'fa-heart-o',
				'fa-history' => 'fa-history',
				'fa-home' => 'fa-home',
				'fa-image' => 'fa-image',
				'fa-inbox' => 'fa-inbox',
				'fa-info' => 'fa-info',
				'fa-info-circle' => 'fa-info-circle',
				'fa-institution' => 'fa-institution',
				'fa-key' => 'fa-key',
				'fa-keyboard-o' => 'fa-keyboard-o',
				'fa-language' => 'fa-language',
				'fa-laptop' => 'fa-laptop',
				'fa-leaf' => 'fa-leaf',
				'fa-legal' => 'fa-legal',
				'fa-lemon-o' => 'fa-lemon-o',
				'fa-level-down' => 'fa-level-down',
				'fa-level-up' => 'fa-level-up',
				'fa-life-bouy' => 'fa-life-bouy',
				'fa-life-ring' => 'fa-life-ring',
				'fa-life-saver' => 'fa-life-saver',
				'fa-lightbulb-o' => 'fa-lightbulb-o',
				'fa-location-arrow' => 'fa-location-arrow',
				'fa-lock' => 'fa-lock',
				'fa-magic' => 'fa-magic',
				'fa-magnet' => 'fa-magnet',
				'fa-mail-forward' => 'fa-mail-forward',
				'fa-mail-reply' => 'fa-mail-reply',
				'fa-mail-reply-all' => 'fa-mail-reply-all',
				'fa-male' => 'fa-male',
				'fa-map-marker' => 'fa-map-marker',
				'fa-meh-o' => 'fa-meh-o',
				'fa-microphone' => 'fa-microphone',
				'fa-microphone-slash' => 'fa-microphone-slash',
				'fa-minus' => 'fa-minus',
				'fa-minus-circle' => 'fa-minus-circle',
				'fa-minus-square' => 'fa-minus-square',
				'fa-minus-square-o' => 'fa-minus-square-o',
				'fa-mobile' => 'fa-mobile',
				'fa-mobile-phone' => 'fa-mobile-phone',
				'fa-money' => 'fa-money',
				'fa-moon-o' => 'fa-moon-o',
				'fa-mortar-board' => 'fa-mortar-board',
				'fa-music' => 'fa-music',
				'fa-navicon' => 'fa-navicon',
				'fa-paper-plane' => 'fa-paper-plane',
				'fa-paper-plane-o' => 'fa-paper-plane-o',
				'fa-paw' => 'fa-paw',
				'fa-pencil' => 'fa-pencil',
				'fa-pencil-square' => 'fa-pencil-square',
				'fa-pencil-square-o' => 'fa-pencil-square-o',
				'fa-phone' => 'fa-phone',
				'fa-phone-square' => 'fa-phone-square',
				'fa-photo' => 'fa-photo',
				'fa-picture-o' => 'fa-picture-o',
				'fa-plane' => 'fa-plane',
				'fa-plus' => 'fa-plus',
				'fa-plus-circle' => 'fa-plus-circle',
				'fa-plus-square' => 'fa-plus-square',
				'fa-plus-square-o' => 'fa-plus-square-o',
				'fa-power-off' => 'fa-power-off',
				'fa-print' => 'fa-print',
				'fa-puzzle-piece' => 'fa-puzzle-piece',
				'fa-qrcode' => 'fa-qrcode',
				'fa-question' => 'fa-question',
				'fa-question-circle' => 'fa-question-circle',
				'fa-quote-left' => 'fa-quote-left',
				'fa-quote-right' => 'fa-quote-right',
				'fa-random' => 'fa-random',
				'fa-recycle' => 'fa-recycle',
				'fa-refresh' => 'fa-refresh',
				'fa-reorder' => 'fa-reorder',
				'fa-reply' => 'fa-reply',
				'fa-reply-all' => 'fa-reply-all',
				'fa-retweet' => 'fa-retweet',
				'fa-road' => 'fa-road',
				'fa-rocket' => 'fa-rocket',
				'fa-rss' => 'fa-rss',
				'fa-rss-square' => 'fa-rss-square',
				'fa-search' => 'fa-search',
				'fa-search-minus' => 'fa-search-minus',
				'fa-search-plus' => 'fa-search-plus',
				'fa-send' => 'fa-send',
				'fa-send-o' => 'fa-send-o',
				'fa-share' => 'fa-share',
				'fa-share-alt' => 'fa-share-alt',
				'fa-share-alt-square' => 'fa-share-alt-square',
				'fa-share-square' => 'fa-share-square',
				'fa-share-square-o' => 'fa-share-square-o',
				'fa-shield' => 'fa-shield',
				'fa-shopping-cart' => 'fa-shopping-cart',
				'fa-sign-in' => 'fa-sign-in',
				'fa-sign-out' => 'fa-sign-out',
				'fa-signal' => 'fa-signal',
				'fa-sitemap' => 'fa-sitemap',
				'fa-sliders' => 'fa-sliders',
				'fa-smile-o' => 'fa-smile-o',
				'fa-sort' => 'fa-sort',
				'fa-sort-alpha-asc' => 'fa-sort-alpha-asc',
				'fa-sort-alpha-desc' => 'fa-sort-alpha-desc',
				'fa-sort-amount-asc' => 'fa-sort-amount-asc',
				'fa-sort-amount-desc' => 'fa-sort-amount-desc',
				'fa-sort-asc' => 'fa-sort-asc',
				'fa-sort-desc' => 'fa-sort-desc',
				'fa-sort-down' => 'fa-sort-down',
				'fa-sort-numeric-asc' => 'fa-sort-numeric-asc',
				'fa-sort-numeric-desc' => 'fa-sort-numeric-desc',
				'fa-sort-up' => 'fa-sort-up',
				'fa-space-shuttle' => 'fa-space-shuttle',
				'fa-spinner' => 'fa-spinner',
				'fa-spoon' => 'fa-spoon',
				'fa-square' => 'fa-square',
				'fa-square-o' => 'fa-square-o',
				'fa-star' => 'fa-star',
				'fa-star-half' => 'fa-star-half',
				'fa-star-half-empty' => 'fa-star-half-empty',
				'fa-star-half-full' => 'fa-star-half-full',
				'fa-star-half-o' => 'fa-star-half-o',
				'fa-star-o' => 'fa-star-o',
				'fa-suitcase' => 'fa-suitcase',
				'fa-sun-o' => 'fa-sun-o',
				'fa-support' => 'fa-support',
				'fa-tablet' => 'fa-tablet',
				'fa-tachometer' => 'fa-tachometer',
				'fa-tag' => 'fa-tag',
				'fa-tags' => 'fa-tags',
				'fa-tasks' => 'fa-tasks',
				'fa-taxi' => 'fa-taxi',
				'fa-terminal' => 'fa-terminal',
				'fa-thumb-tack' => 'fa-thumb-tack',
				'fa-thumbs-down' => 'fa-thumbs-down',
				'fa-thumbs-o-down' => 'fa-thumbs-o-down',
				'fa-thumbs-o-up' => 'fa-thumbs-o-up',
				'fa-thumbs-up' => 'fa-thumbs-up',
				'fa-ticket' => 'fa-ticket',
				'fa-times' => 'fa-times',
				'fa-times-circle' => 'fa-times-circle',
				'fa-times-circle-o' => 'fa-times-circle-o',
				'fa-tint' => 'fa-tint',
				'fa-toggle-down' => 'fa-toggle-down',
				'fa-toggle-left' => 'fa-toggle-left',
				'fa-toggle-right' => 'fa-toggle-right',
				'fa-toggle-up' => 'fa-toggle-up',
				'fa-trash-o' => 'fa-trash-o',
				'fa-tree' => 'fa-tree',
				'fa-trophy' => 'fa-trophy',
				'fa-truck' => 'fa-truck',
				'fa-umbrella' => 'fa-umbrella',
				'fa-university' => 'fa-university',
				'fa-unlock' => 'fa-unlock',
				'fa-unlock-alt' => 'fa-unlock-alt',
				'fa-unsorted' => 'fa-unsorted',
				'fa-upload' => 'fa-upload',
				'fa-user' => 'fa-user',
				'fa-users' => 'fa-users',
				'fa-video-camera' => 'fa-video-camera',
				'fa-volume-down' => 'fa-volume-down',
				'fa-volume-off' => 'fa-volume-off',
				'fa-volume-up' => 'fa-volume-up',
				'fa-warning' => 'fa-warning',
				'fa-wheelchair' => 'fa-wheelchair',
				'fa-wrench' => 'fa-wrench',
				'fa-file' => 'fa-file',
				'fa-file-archive-o' => 'fa-file-archive-o',
				'fa-file-audio-o' => 'fa-file-audio-o',
				'fa-file-code-o' => 'fa-file-code-o',
				'fa-file-excel-o' => 'fa-file-excel-o',
				'fa-file-image-o' => 'fa-file-image-o',
				'fa-file-movie-o' => 'fa-file-movie-o',
				'fa-file-o' => 'fa-file-o',
				'fa-file-pdf-o' => 'fa-file-pdf-o',
				'fa-file-photo-o' => 'fa-file-photo-o',
				'fa-file-picture-o' => 'fa-file-picture-o',
				'fa-file-powerpoint-o' => 'fa-file-powerpoint-o',
				'fa-file-sound-o' => 'fa-file-sound-o',
				'fa-file-text' => 'fa-file-text',
				'fa-file-text-o' => 'fa-file-text-o',
				'fa-file-video-o' => 'fa-file-video-o',
				'fa-file-word-o' => 'fa-file-word-o',
				'fa-file-zip-o' => 'fa-file-zip-o',
				'fa-circle-o-notch' => 'fa-circle-o-notch',
				'fa-cog' => 'fa-cog',
				'fa-gear' => 'fa-gear',
				'fa-refresh' => 'fa-refresh',
				'fa-spinner' => 'fa-spinner',
				'fa-check-square' => 'fa-check-square',
				'fa-check-square-o' => 'fa-check-square-o',
				'fa-circle' => 'fa-circle',
				'fa-circle-o' => 'fa-circle-o',
				'fa-dot-circle-o' => 'fa-dot-circle-o',
				'fa-minus-square' => 'fa-minus-square',
				'fa-minus-square-o' => 'fa-minus-square-o',
				'fa-plus-square' => 'fa-plus-square',
				'fa-plus-square-o' => 'fa-plus-square-o',
				'fa-square' => 'fa-square',
				'fa-square-o' => 'fa-square-o',
				'fa-bitcoin' => 'fa-bitcoin',
				'fa-btc' => 'fa-btc',
				'fa-cny' => 'fa-cny',
				'fa-dollar' => 'fa-dollar',
				'fa-eur' => 'fa-eur',
				'fa-euro' => 'fa-euro',
				'fa-gbp' => 'fa-gbp',
				'fa-inr' => 'fa-inr',
				'fa-jpy' => 'fa-jpy',
				'fa-krw' => 'fa-krw',
				'fa-money' => 'fa-money',
				'fa-rmb' => 'fa-rmb',
				'fa-rouble' => 'fa-rouble',
				'fa-rub' => 'fa-rub',
				'fa-ruble' => 'fa-ruble',
				'fa-rupee' => 'fa-rupee',
				'fa-try' => 'fa-try',
				'fa-turkish-lira' => 'fa-turkish-lira',
				'fa-usd' => 'fa-usd',
				'fa-won' => 'fa-won',
				'fa-yen' => 'fa-yen',
				'fa-align-center' => 'fa-align-center',
				'fa-align-justify' => 'fa-align-justify',
				'fa-align-left' => 'fa-align-left',
				'fa-align-right' => 'fa-align-right',
				'fa-bold' => 'fa-bold',
				'fa-chain' => 'fa-chain',
				'fa-chain-broken' => 'fa-chain-broken',
				'fa-clipboard' => 'fa-clipboard',
				'fa-columns' => 'fa-columns',
				'fa-copy' => 'fa-copy',
				'fa-cut' => 'fa-cut',
				'fa-dedent' => 'fa-dedent',
				'fa-eraser' => 'fa-eraser',
				'fa-file' => 'fa-file',
				'fa-file-o' => 'fa-file-o',
				'fa-file-text' => 'fa-file-text',
				'fa-file-text-o' => 'fa-file-text-o',
				'fa-files-o' => 'fa-files-o',
				'fa-floppy-o' => 'fa-floppy-o',
				'fa-font' => 'fa-font',
				'fa-header' => 'fa-header',
				'fa-indent' => 'fa-indent',
				'fa-italic' => 'fa-italic',
				'fa-link' => 'fa-link',
				'fa-list' => 'fa-list',
				'fa-list-alt' => 'fa-list-alt',
				'fa-list-ol' => 'fa-list-ol',
				'fa-list-ul' => 'fa-list-ul',
				'fa-outdent' => 'fa-outdent',
				'fa-paperclip' => 'fa-paperclip',
				'fa-paragraph' => 'fa-paragraph',
				'fa-paste' => 'fa-paste',
				'fa-repeat' => 'fa-repeat',
				'fa-rotate-left' => 'fa-rotate-left',
				'fa-rotate-right' => 'fa-rotate-right',
				'fa-save' => 'fa-save',
				'fa-scissors' => 'fa-scissors',
				'fa-strikethrough' => 'fa-strikethrough',
				'fa-subscript' => 'fa-subscript',
				'fa-superscript' => 'fa-superscript',
				'fa-table' => 'fa-table',
				'fa-text-height' => 'fa-text-height',
				'fa-text-width' => 'fa-text-width',
				'fa-th' => 'fa-th',
				'fa-th-large' => 'fa-th-large',
				'fa-th-list' => 'fa-th-list',
				'fa-underline' => 'fa-underline',
				'fa-undo' => 'fa-undo',
				'fa-unlink' => 'fa-unlink',
				'fa-angle-double-down' => 'fa-angle-double-down',
				'fa-angle-double-left' => 'fa-angle-double-left',
				'fa-angle-double-right' => 'fa-angle-double-right',
				'fa-angle-double-up' => 'fa-angle-double-up',
				'fa-angle-down' => 'fa-angle-down',
				'fa-angle-left' => 'fa-angle-left',
				'fa-angle-right' => 'fa-angle-right',
				'fa-angle-up' => 'fa-angle-up',
				'fa-arrow-circle-down' => 'fa-arrow-circle-down',
				'fa-arrow-circle-left' => 'fa-arrow-circle-left',
				'fa-arrow-circle-o-down' => 'fa-arrow-circle-o-down',
				'fa-arrow-circle-o-left' => 'fa-arrow-circle-o-left',
				'fa-arrow-circle-o-right' => 'fa-arrow-circle-o-right',
				'fa-arrow-circle-o-up' => 'fa-arrow-circle-o-up',
				'fa-arrow-circle-right' => 'fa-arrow-circle-right',
				'fa-arrow-circle-up' => 'fa-arrow-circle-up',
				'fa-arrow-down' => 'fa-arrow-down',
				'fa-arrow-left' => 'fa-arrow-left',
				'fa-arrow-right' => 'fa-arrow-right',
				'fa-arrow-up' => 'fa-arrow-up',
				'fa-arrows' => 'fa-arrows',
				'fa-arrows-alt' => 'fa-arrows-alt',
				'fa-arrows-h' => 'fa-arrows-h',
				'fa-arrows-v' => 'fa-arrows-v',
				'fa-caret-down' => 'fa-caret-down',
				'fa-caret-left' => 'fa-caret-left',
				'fa-caret-right' => 'fa-caret-right',
				'fa-caret-square-o-down' => 'fa-caret-square-o-down',
				'fa-caret-square-o-left' => 'fa-caret-square-o-left',
				'fa-caret-square-o-right' => 'fa-caret-square-o-right',
				'fa-caret-square-o-up' => 'fa-caret-square-o-up',
				'fa-caret-up' => 'fa-caret-up',
				'fa-chevron-circle-down' => 'fa-chevron-circle-down',
				'fa-chevron-circle-left' => 'fa-chevron-circle-left',
				'fa-chevron-circle-right' => 'fa-chevron-circle-right',
				'fa-chevron-circle-up' => 'fa-chevron-circle-up',
				'fa-chevron-down' => 'fa-chevron-down',
				'fa-chevron-left' => 'fa-chevron-left',
				'fa-chevron-right' => 'fa-chevron-right',
				'fa-chevron-up' => 'fa-chevron-up',
				'fa-hand-o-down' => 'fa-hand-o-down',
				'fa-hand-o-left' => 'fa-hand-o-left',
				'fa-hand-o-right' => 'fa-hand-o-right',
				'fa-hand-o-up' => 'fa-hand-o-up',
				'fa-long-arrow-down' => 'fa-long-arrow-down',
				'fa-long-arrow-left' => 'fa-long-arrow-left',
				'fa-long-arrow-right' => 'fa-long-arrow-right',
				'fa-long-arrow-up' => 'fa-long-arrow-up',
				'fa-toggle-down' => 'fa-toggle-down',
				'fa-toggle-left' => 'fa-toggle-left',
				'fa-toggle-right' => 'fa-toggle-right',
				'fa-toggle-up' => 'fa-toggle-up',
				'fa-arrows-alt' => 'fa-arrows-alt',
				'fa-backward' => 'fa-backward',
				'fa-compress' => 'fa-compress',
				'fa-eject' => 'fa-eject',
				'fa-expand' => 'fa-expand',
				'fa-fast-backward' => 'fa-fast-backward',
				'fa-fast-forward' => 'fa-fast-forward',
				'fa-forward' => 'fa-forward',
				'fa-pause' => 'fa-pause',
				'fa-play' => 'fa-play',
				'fa-play-circle' => 'fa-play-circle',
				'fa-play-circle-o' => 'fa-play-circle-o',
				'fa-step-backward' => 'fa-step-backward',
				'fa-step-forward' => 'fa-step-forward',
				'fa-stop' => 'fa-stop',
				'fa-youtube-play' => 'fa-youtube-play',
				'fa-adn' => 'fa-adn',
				'fa-android' => 'fa-android',
				'fa-apple' => 'fa-apple',
				'fa-behance' => 'fa-behance',
				'fa-behance-square' => 'fa-behance-square',
				'fa-bitbucket' => 'fa-bitbucket',
				'fa-bitbucket-square' => 'fa-bitbucket-square',
				'fa-bitcoin' => 'fa-bitcoin',
				'fa-btc' => 'fa-btc',
				'fa-codepen' => 'fa-codepen',
				'fa-css3' => 'fa-css3',
				'fa-delicious' => 'fa-delicious',
				'fa-deviantart' => 'fa-deviantart',
				'fa-digg' => 'fa-digg',
				'fa-dribbble' => 'fa-dribbble',
				'fa-dropbox' => 'fa-dropbox',
				'fa-drupal' => 'fa-drupal',
				'fa-empire' => 'fa-empire',
				'fa-facebook' => 'fa-facebook',
				'fa-facebook-square' => 'fa-facebook-square',
				'fa-flickr' => 'fa-flickr',
				'fa-foursquare' => 'fa-foursquare',
				'fa-ge' => 'fa-ge',
				'fa-git' => 'fa-git',
				'fa-git-square' => 'fa-git-square',
				'fa-github' => 'fa-github',
				'fa-github-alt' => 'fa-github-alt',
				'fa-github-square' => 'fa-github-square',
				'fa-gittip' => 'fa-gittip',
				'fa-google' => 'fa-google',
				'fa-google-plus' => 'fa-google-plus',
				'fa-google-plus-square' => 'fa-google-plus-square',
				'fa-hacker-news' => 'fa-hacker-news',
				'fa-html5' => 'fa-html5',
				'fa-instagram' => 'fa-instagram',
				'fa-joomla' => 'fa-joomla',
				'fa-jsfiddle' => 'fa-jsfiddle',
				'fa-linkedin' => 'fa-linkedin',
				'fa-linkedin-square' => 'fa-linkedin-square',
				'fa-linux' => 'fa-linux',
				'fa-maxcdn' => 'fa-maxcdn',
				'fa-openid' => 'fa-openid',
				'fa-pagelines' => 'fa-pagelines',
				'fa-pied-piper' => 'fa-pied-piper',
				'fa-pied-piper-alt' => 'fa-pied-piper-alt',
				'fa-pied-piper-square' => 'fa-pied-piper-square',
				'fa-pinterest' => 'fa-pinterest',
				'fa-pinterest-square' => 'fa-pinterest-square',
				'fa-qq' => 'fa-qq',
				'fa-ra' => 'fa-ra',
				'fa-rebel' => 'fa-rebel',
				'fa-reddit' => 'fa-reddit',
				'fa-reddit-square' => 'fa-reddit-square',
				'fa-renren' => 'fa-renren',
				'fa-share-alt' => 'fa-share-alt',
				'fa-share-alt-square' => 'fa-share-alt-square',
				'fa-skype' => 'fa-skype',
				'fa-slack' => 'fa-slack',
				'fa-soundcloud' => 'fa-soundcloud',
				'fa-spotify' => 'fa-spotify',
				'fa-stack-exchange' => 'fa-stack-exchange',
				'fa-stack-overflow' => 'fa-stack-overflow',
				'fa-steam' => 'fa-steam',
				'fa-steam-square' => 'fa-steam-square',
				'fa-stumbleupon' => 'fa-stumbleupon',
				'fa-stumbleupon-circle' => 'fa-stumbleupon-circle',
				'fa-tencent-weibo' => 'fa-tencent-weibo',
				'fa-trello' => 'fa-trello',
				'fa-tumblr' => 'fa-tumblr',
				'fa-tumblr-square' => 'fa-tumblr-square',
				'fa-twitter' => 'fa-twitter',
				'fa-twitter-square' => 'fa-twitter-square',
				'fa-vimeo-square' => 'fa-vimeo-square',
				'fa-vine' => 'fa-vine',
				'fa-vk' => 'fa-vk',
				'fa-wechat' => 'fa-wechat',
				'fa-weibo' => 'fa-weibo',
				'fa-weixin' => 'fa-weixin',
				'fa-windows' => 'fa-windows',
				'fa-wordpress' => 'fa-wordpress',
				'fa-xing' => 'fa-xing',
				'fa-xing-square' => 'fa-xing-square',
				'fa-yahoo' => 'fa-yahoo',
				'fa-youtube' => 'fa-youtube',
				'fa-youtube-play' => 'fa-youtube-play',
				'fa-youtube-square' => 'fa-youtube-square',
				'fa-ambulance' => 'fa-ambulance',
				'fa-h-square' => 'fa-h-square',
				'fa-hospital-o' => 'fa-hospital-o',
				'fa-medkit' => 'fa-medkit',
				'fa-plus-square' => 'fa-plus-square',
				'fa-stethoscope' => 'fa-stethoscope',
				'fa-user-md' => 'fa-user-md',
				'fa-wheelchair' => 'fa-wheelchair',
			);
	  return $icons;
	}
}
/* Product Categories Array */
function thb_productCategories(){
	if(in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	$product_categories = get_terms('product_cat', array('hide_empty' => 0));
	
	$out = array();
	if ($product_categories) {
		foreach($product_categories as $product_category) {
			$out[$product_category->name] = $product_category->slug;
		}
	}
	return $out;
	}
}

/* Portfolio Categories Array */
function thb_portfolioCategories(){
	$portfolio_categories = get_categories(array('taxonomy'=>'project-category'));
	$out = array();
	foreach($portfolio_categories as $portfolio_category) {
		$out[$portfolio_category->cat_name] = $portfolio_category->term_id;
	}
	return $out;
}

/* Out of Stock Check */
function thb_out_of_stock() {
  global $post;
  $id = $post->ID;
  $status = get_post_meta($id, '_stock_status',true);
  
  if ($status == 'outofstock') {
  	return true;
  } else {
  	return false;
  }
}

/* Get WishList Count */
function thb_wishlist_count() {
	if ( is_user_logged_in() ) {
	    $user_id = get_current_user_id();
	}
	
	$count = array();
	if ( class_exists( 'YITH_WCWL_UI' ) )  {
		if( is_user_logged_in() ) {
		    $count = $wpdb->get_results( $wpdb->prepare( 'SELECT COUNT(*) as `cnt` FROM `' . YITH_WCWL_TABLE . '` WHERE `user_id` = %d', $user_id  ), ARRAY_A );
		    $count = $count[0]['cnt'];
		} elseif( yith_usecookies() ) {
		    $count[0]['cnt'] = count( yith_getcookie( 'yith_wcwl_products' ) );
		} else {
		    $count[0]['cnt'] = count( $_SESSION['yith_wcwl_products'] );
		}
		
		if (is_array($count)) {
			$count = 0;
		}
	}
	return $count;
}

/* WishList Button*/
function thb_wishlist_button() {

	global $product, $yith_wcwl; 
	
	if ( class_exists( 'YITH_WCWL_UI' ) )  {
		$url = $yith_wcwl->get_wishlist_url();
		$product_type = $product->product_type;
		$exists = $yith_wcwl->is_product_in_wishlist( $product->id );
		
		$classes = get_option( 'yith_wcwl_use_button' ) == 'yes' ? 'class="add_to_wishlist single_add_to_wishlist button alt"' : 'class="add_to_wishlist"';
		
		$html  = '<div class="yith-wcwl-add-to-wishlist">'; 
		    $html .= '<div class="yith-wcwl-add-button';  // the class attribute is closed in the next row
		    
		    $html .= $exists ? ' hide" style="display:none;"' : ' show"';
		    
		    $html .= '><a href="' . htmlspecialchars($yith_wcwl->get_addtowishlist_url()) . '" data-product-id="' . $product->id . '" data-product-type="' . $product_type . '" ' . $classes . ' ><i class="fa fa-heart-o"></i></a>';
		    $html .= '</div>';
		
		$html .= '<div class="yith-wcwl-wishlistaddedbrowse hide" style="display:none;"><span class="feedback">' . __( 'Product added to wishlist.', THB_THEME_NAME ) . '</span> <a href="' . $url . '" class="add_to_wishlist"><i class="fa fa-heart"></i></a></div>';
		$html .= '<div class="yith-wcwl-wishlistexistsbrowse ' . ( $exists ? 'show' : 'hide' ) . '" style="display:' . ( $exists ? 'block' : 'none' ) . '"><a href="' . $url . '" class="add_to_wishlist"><i class="fa fa-heart"></i></a></div>';
		$html .= '<div style="clear:both"></div><div class="yith-wcwl-wishlistaddresponse"></div>';
		
		$html .= '</div>';
		
		return $html;
		
	}

}

/* Prev/Next Post Links - http://wordpress.org/plugins/previous-and-next-post-in-same-taxonomy/ */
function be_previous_post_link($format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '', $taxonomy = 'category') {
	be_adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, true, $taxonomy);
}
function be_next_post_link($format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = '', $taxonomy = 'category') {
	be_adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, false, $taxonomy);
}
function be_adjacent_post_link($format, $link, $in_same_cat = false, $excluded_categories = '', $previous = true, $taxonomy = 'category') {
	if ( $previous && is_attachment() )
		$post = & get_post($GLOBALS['post']->post_parent);
	else
		$post = be_get_adjacent_post($in_same_cat, $excluded_categories, $previous, $taxonomy);

	if ( !$post )
		return;

	$title = $post->post_title;

	if ( empty($post->post_title) )
		$title = $previous ? __('Previous Post') : __('Next Post');

	$title = apply_filters('the_title', $title, $post->ID);
	$date = mysql2date(get_option('date_format'), $post->post_date);
	$rel = $previous ? 'prev' : 'next';

	$string = '<a href="'.get_permalink($post).'" rel="'.$rel.'" data-id="'.$post->ID.'">';
	$link = str_replace('%title', $title, $link);
	$link = str_replace('%date', $date, $link);
	$link = $string . $link . '</a>';

	$format = str_replace('%link', $link, $format);

	$adjacent = $previous ? 'previous' : 'next';
	echo apply_filters( "{$adjacent}_post_link", $format, $link );
}
function be_get_adjacent_post( $in_same_cat = false, $excluded_categories = '', $previous = true, $taxonomy = 'category' ) {
	global $post, $wpdb;

	if ( empty( $post ) )
		return null;

	$current_post_date = $post->post_date;

	$join = '';
	$posts_in_ex_cats_sql = '';
	if ( $in_same_cat || ! empty( $excluded_categories ) ) {
		$join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

		if ( $in_same_cat ) {
			$cat_array = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'ids'));
			$join .= " AND tt.taxonomy = '$taxonomy' AND tt.term_id IN (" . implode(',', $cat_array) . ")";
		}

		$posts_in_ex_cats_sql = "AND tt.taxonomy = '$taxonomy'";
		if ( ! empty( $excluded_categories ) ) {
			if ( ! is_array( $excluded_categories ) ) {
				// back-compat, $excluded_categories used to be IDs separated by " and "
				if ( strpos( $excluded_categories, ' and ' ) !== false ) {
					_deprecated_argument( __FUNCTION__, '3.3', sprintf( __( 'Use commas instead of %s to separate excluded categories.' ), "'and'" ) );
					$excluded_categories = explode( ' and ', $excluded_categories );
				} else {
					$excluded_categories = explode( ',', $excluded_categories );
				}
			}

			$excluded_categories = array_map( 'intval', $excluded_categories );
				
			if ( ! empty( $cat_array ) ) {
				$excluded_categories = array_diff($excluded_categories, $cat_array);
				$posts_in_ex_cats_sql = '';
			}

			if ( !empty($excluded_categories) ) {
				$posts_in_ex_cats_sql = " AND tt.taxonomy = '$taxonomy' AND tt.term_id NOT IN (" . implode($excluded_categories, ',') . ')';
			}
		}
	}

	$adjacent = $previous ? 'previous' : 'next';
	$op = $previous ? '<' : '>';
	$order = $previous ? 'DESC' : 'ASC';

	$join  = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories );
	$where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' $posts_in_ex_cats_sql", $current_post_date, $post->post_type), $in_same_cat, $excluded_categories );
	$sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );

	$query = "SELECT p.* FROM $wpdb->posts AS p $join $where $sort";
	$query_key = 'adjacent_post_' . md5($query);
	$result = wp_cache_get($query_key, 'counts');
	if ( false !== $result )
		return $result;

	$result = $wpdb->get_row("SELECT p.* FROM $wpdb->posts AS p $join $where $sort");
	if ( null === $result )
		$result = '';

	wp_cache_set($query_key, $result, 'counts');
	return $result;
}
?>