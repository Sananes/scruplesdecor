<?php
/**
 *	Oxygen WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */


# GET/POST getter
function lab_get($var)
{
	return isset($_GET[$var]) ? $_GET[$var] : (isset($_REQUEST[$var]) ? $_REQUEST[$var] : '');
}

function get($var)
{
	return lab_get( $var );
}

function post($var)
{
	return isset($_POST[$var]) ? $_POST[$var] : null;
}

function cookie($var)
{
	return isset($_COOKIE[$var]) ? $_COOKIE[$var] : null;
}




# Generate From-To numbers borders
function generate_from_to($from, $to, $current_page, $max_num_pages, $numbers_to_show = 5)
{
	if($numbers_to_show > $max_num_pages)
		$numbers_to_show = $max_num_pages;


	$add_sub_1 = round($numbers_to_show/2);
	$add_sub_2 = round($numbers_to_show - $add_sub_1);

	$from = $current_page - $add_sub_1;
	$to = $current_page + $add_sub_2;

	$limits_exceeded_l = FALSE;
	$limits_exceeded_r = FALSE;

	if($from < 1)
	{
		$from = 1;
		$limits_exceeded_l = TRUE;
	}

	if($to > $max_num_pages)
	{
		$to = $max_num_pages;
		$limits_exceeded_r = TRUE;
	}


	if($limits_exceeded_l)
	{
		$from = 1;
		$to = $numbers_to_show;
	}
	else
	if($limits_exceeded_r)
	{
		$from = $max_num_pages - $numbers_to_show + 1;
		$to = $max_num_pages;
	}
	else
	{
		$from += 1;
	}

	if($from < 1)
		$from = 1;

	if($to > $max_num_pages)
	{
		$to = $max_num_pages;
	}

	return array($from, $to);
}

# Laborator Pagination
function laborator_show_pagination($current_page, $max_num_pages, $from, $to, $pagination_position = 'full', $numbers_to_show = 5)
{
	$current_page = $current_page ? $current_page : 1;

	?>
	<div class="clear"></div>

	<!-- pagination -->
	<ul class="pagination<?php echo $pagination_position ? " pagination-{$pagination_position}" : ''; ?>">

	<?php if($current_page > 1): ?>
		<li class="first_page"><a href="<?php echo get_pagenum_link(1); ?>"><?php _e('&laquo; First', 'oxygen'); ?></a></li>
	<?php endif; ?>

	<?php if($current_page > 2): ?>
		<li class="first_page"><a href="<?php echo get_pagenum_link($current_page - 1); ?>"><?php _e('Previous', 'oxygen'); ?></a></li>
	<?php endif; ?>

	<?php

	if($from > floor($numbers_to_show / 2))
	{
		?>
		<li><a href="<?php echo get_pagenum_link(1); ?>"><?php echo 1; ?></a></li>
		<li class="dots"><span>...</span></li>
		<?php
	}

	for($i=$from; $i<=$to; $i++):

		$link_to_page = get_pagenum_link($i);
		$is_active = $current_page == $i;

	?>
		<li<?php echo $is_active ? ' class="active"' : ''; ?>><a href="<?php echo $link_to_page; ?>"><?php echo $i; ?></a></li>
	<?php
	endfor;


	if($max_num_pages > $to)
	{
		if($max_num_pages != $i):
		?>
			<li class="dots"><span>...</span></li>
		<?php
		endif;

		?>
		<li><a href="<?php echo get_pagenum_link($max_num_pages); ?>"><?php echo $max_num_pages; ?></a></li>
		<?php
	}
	?>

	<?php if($current_page + 1 <= $max_num_pages): ?>
		<li class="last_page"><a href="<?php echo get_pagenum_link($current_page + 1); ?>"><?php _e('Next', 'oxygen'); ?></a></li>
	<?php endif; ?>

	<?php if($current_page < $max_num_pages): ?>
		<li class="last_page"><a href="<?php echo get_pagenum_link($max_num_pages); ?>"><?php _e('Last &raquo;', 'oxygen'); ?></a></li>
	<?php endif; ?>
	</ul>
	<!-- end: pagination -->
	<?php

	# Deprecated (the above function displays pagination)
	if(false):

		posts_nav_link();

	endif;
}



# Get SMOF data
$data_cached            = array();
$smof_filters           = array();
$data                   = function_exists('of_get_options') ? of_get_options() : array();
$data_iteration_count   = 0;

function get_data($var = '')
{
	global $data, $data_cached, $data_iteration_count;

	$data_iteration_count++;

	if( ! function_exists('of_get_options'))
		return null;

	if(isset($data_cached[$var]))
	{
		return apply_filters("get_data_{$var}", $data_cached[$var]);
	}

	if( ! empty($var) && isset($data[$var]))
	{
		$data_cached[$var] = $data[$var];

		return apply_filters("get_data_{$var}", $data[$var]);
	}

	return null;
}


# Compress Text Function
function compress_text($buffer)
{
	/* remove comments */
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	/* remove tabs, spaces, newlines, etc. */
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '	', '	', '	'), '', $buffer);
	return $buffer;
}


# Breadcrumb
function dimox_breadcrumbs($echo = true, $force_use = false)
{
	#if( ! get_data('blog_breadcrumb') && ! $force_use)
	#	return;

	/* === OPTIONS === */
	$text['home']      = __( 'Home', 'oxygen' );
	$text['category']  = __( 'Category: "%s"', 'oxygen' );
	$text['search']    = __( 'Search Results for "%s" Query', 'oxygen' );
	$text['tag']       = __( 'Tag: "%s"', 'oxygen' );
	$text['author']    = __( 'Author: %s', 'oxygen' );
	$text['404']       = __( 'Error 404', 'oxygen' );

	$show_current      = 1;
	$show_on_home      = 1;
	$show_home_link    = 1;
	$show_title        = 1;
	$delimiter         = ' ';
	$before            = '<a class="active">';
	$after             = '</a>';
	/* === END OF OPTIONS === */

	global $post;

	$home_link		= home_url('/');
	$link_before	= '<span>';
	$link_after	 = '</span>';
	$link_attr		= '';
	$link				 = $link_before . '<a' . $link_attr . ' href="%1$s">%2$s</a>' . $link_after;
	$parent_id		= $parent_id_2 = (isset($post->post_parent) ? $post->post_parent : 0);
	$frontpage_id = get_option('page_on_front');

	$output = '';

	if (is_home() || is_front_page()) {

		if ($show_on_home == 1) $output .= '<div class="breadcrumbs"><a href="' . $home_link . '">' . $text['home'] . '</a></div>';

	} else {

		$output .= '<div class="breadcrumbs">';
		if ($show_home_link == 1) {
			$output .= '<a href="' . $home_link . '">' . $text['home'] . '</a>';
			if ($frontpage_id == 0 || $parent_id != $frontpage_id) $output .= $delimiter;
		}

		if ( is_category() ) {
			$this_cat = get_category(get_query_var('cat'), false);
			if ($this_cat->parent != 0) {
				$cats = get_category_parents($this_cat->parent, TRUE, $delimiter);
				if ($show_current == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
				$cats = str_replace('<a', $link_before . '<a' . $link_attr, $cats);
				$cats = str_replace('</a>', '</a>' . $link_after, $cats);
				if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
				$output .= $cats;
			}
			if ($show_current == 1) $output .= $before . sprintf($text['category'], single_cat_title('', false)) . $after;

		} elseif ( is_search() ) {
			$output .= $before . sprintf($text['search'], get_search_query()) . $after;

		} elseif ( is_day() ) {
			$output .= sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
			$output .= sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
			$output .= $before . get_the_time('d') . $after;

		} elseif ( is_month() ) {
			$output .= sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
			$output .= $before . get_the_time('F') . $after;

		} elseif ( is_year() ) {
			$output .= $before . get_the_time('Y') . $after;

		} elseif ( is_single() && !is_attachment() ) {
			if ( get_post_type() != 'post' ) {
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				printf($link, $home_link . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);
				if ($show_current == 1) $output .= $delimiter . $before . get_the_title() . $after;
			} else {
				$cat = get_the_category(); $cat = $cat[0];
				$cats = get_category_parents($cat, TRUE, $delimiter);
				if ($show_current == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
				$cats = str_replace('<a', $link_before . '<a' . $link_attr, $cats);
				$cats = str_replace('</a>', '</a>' . $link_after, $cats);
				if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
				$output .= $cats;
				if ($show_current == 1) $output .= $before . get_the_title() . $after;
			}

		} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
			$post_type = get_post_type_object(get_post_type());
			$output .= $before . $post_type->labels->singular_name . $after;

		} elseif ( is_attachment() ) {
			$parent = get_post($parent_id);
			$cat = get_the_category($parent->ID); $cat = $cat[0];
			if ($cat) {
				$cats = get_category_parents($cat, TRUE, $delimiter);
				$cats = str_replace('<a', $link_before . '<a' . $link_attr, $cats);
				$cats = str_replace('</a>', '</a>' . $link_after, $cats);
				if ($show_title == 0) $cats = preg_replace('/ title="(.*?)"/', '', $cats);
				$output .= $cats;
			}
			printf($link, get_permalink($parent), $parent->post_title);
			if ($show_current == 1) $output .= $delimiter . $before . get_the_title() . $after;

		} elseif ( is_page() && !$parent_id ) {
			if ($show_current == 1) $output .= $before . get_the_title() . $after;

		} elseif ( is_page() && $parent_id ) {
			if ($parent_id != $frontpage_id) {
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					if ($parent_id != $frontpage_id) {
						$breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
					}
					$parent_id = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					$output .= $breadcrumbs[$i];
					if ($i != count($breadcrumbs)-1) $output .= $delimiter;
				}
			}
			if ($show_current == 1) {
				if ($show_home_link == 1 || ($parent_id_2 != 0 && $parent_id_2 != $frontpage_id)) $output .= $delimiter;
				$output .= $before . get_the_title() . $after;
			}

		} elseif ( is_tag() ) {
			$output .= $before . sprintf($text['tag'], single_tag_title('', false)) . $after;

		} elseif ( is_author() ) {
	 		global $author;
			$userdata = get_userdata($author);
			$output .= $before . sprintf($text['author'], $userdata->display_name) . $after;

		} elseif ( is_404() ) {
			$output .= $before . $text['404'] . $after;

		} elseif ( has_post_format() && !is_singular() ) {
			$output .= get_post_format_string( get_post_format() );
		}

		if ( get_query_var('paged') ) {
			#if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $output .= ' (';
			$output .= ' <a class="paged">(' . __('Page', 'oxygen') . ' ' . get_query_var('paged') . ')</a>';
			#if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $output .= ')';
		}

		$output .= '</div><!-- .breadcrumbs -->';

	}

	if($echo)
		echo $output;
	else
		return $output;
} // end dimox_breadcrumbs()



# List Comments
function laborator_list_comments_open($comment, $args, $depth)
{
	global $post, $wpdb, $comment_index;

	$comment_ID 			= $comment->comment_ID;
	$comment_author 		= $comment->comment_author;
	$comment_author_url		= $comment->comment_author_url;
	$comment_author_email	= $comment->comment_author_email;
	$comment_date 			= $comment->comment_date;

	$avatar					= preg_replace("/\s?(height='[0-9]+'|width='[0-9]+')/", "", get_avatar($comment));

	$comment_time 			= strtotime($comment_date);
	$comment_timespan 		= human_time_diff($comment_time, time());

	$link 					= '<a href="' . $comment_author_url . '" target="_blank">';

	if($comment_author_url)
	{
		$avatar = $link . $avatar . '</a>';
	}

	?>
	<div <?php comment_class("author_post"); ?> id="comment-<?php echo $comment_ID; ?>">
		<div class="comment-entry">

			<div class="comment-inner-body">

				<div class="comment-thumb">
					<div class="author_img"><?php echo $avatar; ?></div>
				</div>

				<div class="comment-details">
					<div class="author_about_part">

						<div class="author_name">
							<?php echo $comment_author_url ? ($link . $comment_author . '</a>') : $comment_author; ?>
							<span><?php _e('says:', 'oxygen'); ?></span>
						</div>
						<div class="date_time_reply_text">
							<?php echo date_i18n("F d, Y - H:i", $comment_time); ?>
							-
							<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __('<span>Reply</span>', 'oxygen'), 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => '' ) ), $comment, $post ); ?>
						</div>

						<div class="clearfix"></div>

						<div class="comment-content">
							<?php comment_text(); ?>
						</div>
					</div>
				</div>

			</div>

		</div>
	</div>
	<?php
}

function laborator_list_comments_close()
{
}


# Share Network Story
function share_story_network_link($network, $id, $simptips = true)
{
	global $post;


	$networks = array(
		'fb' => array(
			'url'		=> 'http://www.facebook.com/sharer.php?m2w&s=100&p&#91;url&#93;=' . get_permalink() . '&p&#91;title&#93;=' . urlencode( get_the_title() ),
			'tooltip'	=> __('Share on Facebook', 'oxygen'),
			'icon'		=> 'facebook'
		),

		'tw' => array(
			'url'		=> 'http://twitter.com/home?status=' . urlencode( get_the_title() ) . ' ' . get_permalink(),
			'tooltip'	=> __('Share on Twitter', 'oxygen'),
			'icon'		 => 'twitter'
		),

		'gp' => array(
			'url'		=> 'https://plus.google.com/share?url=' . get_permalink(),
			'tooltip'	=> __('Share on Google+', 'oxygen'),
			'icon'		 => 'gplus'
		),

		'tlr' => array(
			'url'		=> 'http://www.tumblr.com/share/link?url=' . get_permalink() . '&name=' . urlencode( get_the_title() ) . '&description=' . urlencode( get_the_excerpt() ),
			'tooltip'	=> __('Share on Tumblr', 'oxygen'),
			'icon'		 => 'tumblr'
		),

		'lin' => array(
			'url'		=> 'http://linkedin.com/shareArticle?mini=true&amp;url=' . get_permalink() . '&amp;title=' . urlencode( get_the_title() ),
			'tooltip'	=> __('Share on LinkedIn', 'oxygen'),
			'icon'		 => 'linkedin'
		),

		'pi' => array(
			'url'		=> 'http://pinterest.com/pin/create/button/?url=' . get_permalink() . '&amp;description=' . urlencode( get_the_title() ) . '&amp;' . ($id ? ('media=' . wp_get_attachment_url( get_post_thumbnail_id($id) )) : ''),
			'tooltip'	=> __('Share on Pinterest', 'oxygen'),
			'icon'	 	 => 'pinterest'
		),

		'vk' => array(
			'url'		=> 'http://vkontakte.ru/share.php?url=' . get_permalink(),
			'tooltip'	=> __('Share on VKontakte', 'oxygen'),
			'icon'	 	 => 'vkontakte'
		),

		'em' => array(
			'url'		=> 'mailto:?subject=' . urlencode( get_the_title() ) . '&amp;body=' . get_permalink(),
			'tooltip'	=> __('Share via Email', 'oxygen'),
			'icon'		 => 'mail'
		),
	);

	$network_entry = $networks[ $network ];

	?>
	<a class="<?php echo $network_entry['icon'] . ' '; echo $simptips ? 'simptip-position-top simptip-fade' : ''; ?>" data-tooltip="<?php echo $network_entry['tooltip']; ?>" href="<?php echo $network_entry['url']; ?>" target="_blank">
		<i class="entypo-<?php echo $network_entry['icon']; ?>"></i>
	</a>
	<?php
}



# Fontello Icon List
function fontello_icon_list()
{
	return array('plus', 'minus', 'info', 'left-thin', 'up-thin', 'right-thin', 'down-thin', 'level-up', 'level-down', 'switch', 'infinity', 'plus-squared', 'minus-squared', 'home', 'keyboard', 'erase', 'pause', 'fast-forward', 'fast-backward', 'to-end', 'to-start', 'hourglass', 'stop', 'up-dir', 'play', 'right-dir', 'down-dir', 'left-dir', 'adjust', 'cloud', 'star', 'star-empty', 'cup', 'menu', 'moon', 'heart-empty', 'heart', 'note', 'note-beamed', 'layout', 'flag', 'tools', 'cog', 'attention', 'flash', 'record', 'cloud-thunder', 'tape', 'flight', 'mail', 'pencil', 'feather', 'check', 'cancel', 'cancel-circled', 'cancel-squared', 'help', 'quote', 'plus-circled', 'minus-circled', 'right', 'direction', 'forward', 'ccw', 'cw', 'left', 'up', 'down', 'list-add', 'list', 'left-bold', 'right-bold', 'up-bold', 'down-bold', 'user-add', 'help-circled', 'info-circled', 'eye', 'tag', 'upload-cloud', 'reply', 'reply-all', 'code', 'export', 'print', 'retweet', 'comment', 'chat', 'vcard', 'address', 'location', 'map', 'compass', 'trash', 'doc', 'doc-text-inv', 'docs', 'doc-landscape', 'archive', 'rss', 'share', 'basket', 'shareable', 'login', 'logout', 'volume', 'resize-full', 'resize-small', 'popup', 'publish', 'window', 'arrow-combo', 'chart-pie', 'language', 'air', 'database', 'drive', 'bucket', 'thermometer', 'down-circled', 'left-circled', 'right-circled', 'up-circled', 'down-open', 'left-open', 'right-open', 'up-open', 'down-open-mini', 'left-open-mini', 'right-open-mini', 'up-open-mini', 'down-open-big', 'left-open-big', 'right-open-big', 'up-open-big', 'progress-0', 'progress-1', 'progress-2', 'progress-3', 'back-in-time', 'network', 'inbox', 'install', 'lifebuoy', 'mouse', 'dot', 'dot-2', 'dot-3', 'suitcase', 'flow-cascade', 'flow-branch', 'flow-tree', 'flow-line', 'flow-parallel', 'brush', 'paper-plane', 'magnet', 'gauge', 'traffic-cone', 'cc', 'cc-by', 'cc-nc', 'cc-nc-eu', 'cc-nc-jp', 'cc-sa', 'cc-nd', 'cc-pd', 'cc-zero', 'cc-share', 'cc-remix', 'github', 'github-circled', 'flickr', 'flickr-circled', 'vimeo', 'vimeo-circled', 'twitter', 'twitter-circled', 'facebook', 'facebook-circled', 'facebook-squared', 'gplus', 'gplus-circled', 'pinterest', 'pinterest-circled', 'tumblr', 'tumblr-circled', 'linkedin', 'linkedin-circled', 'dribbble', 'dribbble-circled', 'stumbleupon', 'stumbleupon-circled', 'lastfm', 'lastfm-circled', 'rdio', 'rdio-circled', 'spotify', 'spotify-circled', 'qq', 'instagram', 'dropbox', 'evernote', 'flattr', 'skype', 'skype-circled', 'renren', 'sina-weibo', 'paypal', 'picasa', 'soundcloud', 'mixi', 'behance', 'google-circles', 'vkontakte', 'smashing', 'db-shape', 'sweden', 'logo-db', 'picture', 'globe', 'leaf', 'graduation-cap', 'mic', 'palette', 'ticket', 'video', 'target', 'music', 'trophy', 'thumbs-up', 'thumbs-down', 'bag', 'user', 'users', 'lamp', 'alert', 'water', 'droplet', 'credit-card', 'monitor', 'briefcase', 'floppy', 'cd', 'folder', 'doc-text', 'calendar', 'chart-line', 'chart-bar', 'clipboard', 'attach', 'bookmarks', 'book', 'book-open', 'phone', 'megaphone', 'upload', 'download', 'box', 'newspaper', 'mobile', 'signal', 'camera', 'shuffle', 'loop', 'arrows-ccw', 'light-down', 'light-up', 'mute', 'sound', 'battery', 'search', 'key', 'lock', 'lock-open', 'bell', 'bookmark', 'link', 'back', 'flashlight', 'chart-area', 'clock', 'rocket', 'block');
}


# Execution Time
function et()
{
	return microtime(true) - STIME;
}



# Load Font Style
function laborator_load_font_style()
{
	$api_url           = '//fonts.googleapis.com/css?family=';

	$font_variants 	   = '300italic,400italic,700italic,300,400,700';

	$primary_font      = 'Roboto:400,400italic,500,900,900italic,700italic,700,500italic,300italic,300,100italic,100';
	$secondary_font    = 'Roboto+Condensed:300italic,400italic,700italic,300,400,700';


	# Custom Font
	$_font_primary      = get_data('font_primary');
	$_font_secondary    = get_data('font_secondary');

	if($_font_primary != 'none' && $_font_primary != 'Use default')
	{
		$primary_font_replaced = true;
		$primary_font = $_font_primary . ':' . $font_variants;
	}

	if($_font_secondary != 'none' && $_font_secondary != 'Use default')
	{
		$secondary_font_replaced = true;
		$secondary_font = $_font_secondary . ':' . $font_variants;
	}

	$to_lowercase = get_data('font_to_lowercase') == 'Default Case' ? true : false;

	$base_font_size = get_data('font_size_base');
	$base_font_size = is_numeric($base_font_size) && $base_font_size >= 10 ? $base_font_size : null;

	# Start: Added in v1.8
	$custom_primary_font_url   = get_data('custom_primary_font_url');
	$custom_primary_font_name  = get_data('custom_primary_font_name');

	$custom_heading_font_url   = get_data('custom_heading_font_url');
	$custom_heading_font_name  = get_data('custom_heading_font_name');

	if($custom_primary_font_url && $custom_primary_font_name)
	{
		$primary_font_replaced    = 2;
		$primary_font             = $custom_primary_font_url;
		$_font_primary            = $custom_primary_font_name;
	}

	if($custom_heading_font_url && $custom_heading_font_name)
	{
		$secondary_font_replaced    = 2;
		$secondary_font             = $custom_heading_font_url;
		$_font_secondary            = $custom_heading_font_name;
	}

	# End: Added in v1.8

	wp_enqueue_style('primary-font', strstr($primary_font, "://") ? $primary_font : ($api_url . $primary_font));
	wp_enqueue_style('heading-font', strstr($secondary_font, "://") ? $secondary_font : ($api_url . $secondary_font));

	ob_start();
?>

<style>
<?php if(isset($primary_font_replaced)): ?>
.primary-font,
body,
p {
  <?php if($primary_font_replaced == 2): ?>
  font-family: <?php echo $_font_primary; ?>;
  <?php else: ?>
  font-family: "<?php echo $_font_primary; ?>", Helvetica, Arial, sans-serif;
  <?php endif; ?>
}
<?php endif; ?>

<?php if(isset($secondary_font_replaced)): ?>
.heading-font,
.contact-store .address-content p,
.nav,
.navbar-blue,
body h1,
body h2,
body h3,
body h4,
body h5,
body h6,
h1,
h2,
h3,
h4,
h5,
h6,
h7,
a,
label,
th,
.oswald,
.banner .button_outer .button_inner .banner-content strong,
.laborator-woocommerce .myaccount-env .my_account_orders th,
.laborator-woocommerce .myaccount-env .my_account_orders td,
.shop .items .item-wrapper .item .sale_tag .ribbon,
.shop .items .item-wrapper .item .description .price,
.shop .results,
.shop .shop-grid .quickview-list .quickview-entry .quickview-wrapper .product-gallery-env .ribbon .ribbon-content,
footer.footer_widgets .widget_laborator_subscribe #subscribe_now,
footer.footer_widgets .widget_search #searchsubmit,
footer .footer_main .copyright_text,
footer .footer_main .footer-nav ul li a,
.header-cart .cart-items .no-items,
.header-cart .cart-items .cart-item .details .price-quantity,
.shop_sidebar .sidebar h3,
.widget_search div #searchsubmit,
.widget_product_search div #searchsubmit,
.price_slider_wrapper .price_slider_amount .button,
.widget_shopping_cart_content .buttons .button,
.cart-env .cart-totals > li .name,
.cart-env .cart-header-row .up,
.cart-env .cart-item-row .col .quantity input,
.cart-env .cart-item-row .col .quantity input[type="button"],
.blog .blog-post .blog_content h1,
.blog .blog-post .blog_content .post-meta .blog_date,
.blog .single_post .post_img .loading,
.blog .single_post .post_details .author_text,
.blog .single_post .post-content h1,
.blog .single_post .post-content h2,
.blog .single_post .post-content h3,
.blog .single_post .post-content h4,
.blog .single_post .post-content h5,
.blog .single_post .post-content blockquote,
.blog .single_post .post-content blockquote p,
.blog .single_post .post-content blockquote cite,
.comments .author_post .author_about_part .meta,
.comments .author_post .author_about_part .author_name,
.comments .author_post .author_about_part .date_time_reply_text,
.comments .author_post .author_about_part .comment-content h1,
.comments .author_post .author_about_part .comment-content h2,
.comments .author_post .author_about_part .comment-content h3,
.comments .author_post .author_about_part .comment-content h4,
.comments .author_post .author_about_part .comment-content h5,
.comments .author_post .author_about_part .comment-content blockquote,
.comments .author_post .author_about_part .comment-content blockquote p,
.comments .author_post .author_about_part .comment-content blockquote cite,
.comments .form-submit #submit,
.comment-respond input#submit,
.laborator-woocommerce .product-single .product-left-info .ribbon .ribbon-content,
.laborator-woocommerce .product-single .entry-summary .price,
.laborator-woocommerce .product-single .entry-summary .quantity input[type="button"],
.laborator-woocommerce .product-single .entry-summary .quantity input.qty,
.laborator-woocommerce .product-single .entry-summary .stock,
.laborator-woocommerce .product-single .woocommerce-tabs .tabs > li a,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab h1,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab h2,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab h3,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab h4,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab h5,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab blockquote,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab blockquote p,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab blockquote cite,
.laborator-woocommerce .product-single .woocommerce-tabs #reviews .comments .comment-entry time,
.laborator-woocommerce .product-single .woocommerce-tabs #reviews .comments .comment-entry .meta,
.laborator-woocommerce .product-single .woocommerce-tabs #reviews #review_form input[type="submit"],
.laborator-woocommerce .product-single .woocommerce-tabs #reviews #review_form input[type="submit"],
.btn,
.tooltip,
.price,
.amount,
.cart-sub-total,
.page-container .wpb_content_element blockquote strong,
.page-container .lab_wpb_blog_posts .blog-posts .blog-post .post .date,
.page-container .vc_separator.double-bordered-thick h4,
.page-container .vc_separator.double-bordered-thin h4,
.page-container .vc_separator.double-bordered h4,
.page-container .vc_separator.one-line-border h4,
.lab_wpb_banner_2 .title,
.lab_wpb_testimonials .testimonials-inner .testimonial-entry .testimonial-blockquote,
.woocommerce .woocommerce-success .button,
.laborator-woocommerce .items .product .loading-disabled .loader strong,
.laborator-woocommerce .select-wrapper .select-placeholder,
.laborator-woocommerce #wl-wrapper .shop_table tbody .quantity input[type="button"],
.laborator-woocommerce #wl-wrapper .wishlist_table .button,
.laborator-woocommerce .wishlist-empty,
.woocommerce .price,
.laborator-woocommerce .order_details.header li,
.lab_wpb_lookbook_carousel .lookbook-carousel .product-item .lookbook-hover-info .lookbook-inner-content .title,
.lab_wpb_lookbook_carousel .lookbook-carousel .product-item .lookbook-hover-info .lookbook-inner-content .price-and-add-to-cart .price > .amount,
.lab_wpb_lookbook_carousel .lookbook-carousel .product-item .lookbook-hover-info .lookbook-inner-content .price-and-add-to-cart .price ins {
<?php if($secondary_font_replaced == 2): ?>
  font-family: <?php echo $_font_secondary; ?>;
<?php else: ?>
  font-family: "<?php echo $_font_secondary; ?>", Helvetica, Arial, sans-serif;
<?php endif; ?>
}
<?php endif; ?>

<?php if($to_lowercase): ?>
.to-uppercase,
.main-sidebar ul.nav a,
.top-first,
.oxygen-top-menu > .wrapper > .top-menu > .main .tl-header .sec-nav .sec-nav-menu > li a,
.oxygen-top-menu > .wrapper > .main-menu-top > .main .main-menu-env .nav > li > a,
.oxygen-top-menu > .wrapper > .main-menu-top > .main .main-menu-env .nav > li .sub-menu > li > a,
.oxygen-top-menu > .wrapper > .top-menu-centered > .main .navs .main-menu-env .nav > li > a,
.oxygen-top-menu > .wrapper > .top-menu-centered > .main .navs .main-menu-env .nav > li .sub-menu > li > a,
.oxygen-top-menu > .wrapper > .top-menu-centered > .main .navs .sec-nav-menu > li a,
.capital,
.block-pad h1,
.block-pad h2,
.block-pad h3,
.block-pad h4,
.block-pad h5,
.block-pad h6,
.block-pad h1,
.twleve,
.banner .button_outer .button_inner .banner-content strong,
.btn.btn-mini,
.btn-group.open .btn-grey li a,
.fluid-dark-button,
.alert h2,
.alert h3,
.alert h4,
.alert h5,
.alert a.alert-link,
label,
.form-elements .contact-form-submit .contact-send,
.feature-tab.feature-tab-type-1 .title,
.feature-tab.feature-tab-type-2 .title,
.slider_wrapper h5,
.laborator-woocommerce .myaccount-env .myaccount-tabs > li a,
.laborator-woocommerce .myaccount-env .my_account_orders th,
.laborator-woocommerce .myaccount-env .my_account_orders td,
.laborator-woocommerce .myaccount-env .addresses .address .title .btn,
.shop .items .item-wrapper .item .sale_tag .ribbon span,
.shop .items .item-wrapper .item .btn,
.shop .items .item-wrapper .item .quick-view a,
.shop .items .item-wrapper .item .description .title,
.shop .items .item-wrapper .item .description .type,
.shop .results,
.shop .shop-grid .quickview-list .quickview-entry .quickview-wrapper .product-gallery-env .ribbon .ribbon-content,
.shop .shop-grid .quickview-list .quickview-entry .quickview-wrapper .entry-summary .view-more,
.widget .widget-title h1,
.widget .widget-item .cart_top_detail h4,
ul.pagination li a,
ul.page-numbers li a,
ul.pagination li span,
ul.page-numbers li span,
footer.footer_widgets .col h1,
footer.footer_widgets .tagcloud a,
footer.footer_widgets ul,
footer.footer_widgets h3,
footer.footer_widgets h4,
footer.footer_widgets .col h2,
footer.footer_widgets .widget_laborator_subscribe #subscribe_now.btn-mini,
footer.footer_widgets .widget_search #searchsubmit.btn-mini,
footer .footer_main .copyright_text,
footer .footer_main .footer-nav ul li a,
.header-cart .cart-items .no-items,
.header-cart .cart-items .cart-item .details .title,
.header-cart .btn-block,
.header-cart .cart-sub-total,
.shop_add_cart .shop_add_cart_part .col_2 h1,
.shop_add_cart .shop_add_cart_part .col_2 .add_total_cart .btn,
.search-results-header .row .results-text,
.search-results-header .row .search-box input,
body .search-results .search-entry .title,
.shop_sidebar .sidebar h3,
.shop_sidebar .sidebar ul li,
.widget_tag_cloud .tagcloud a,
.widget_product_tag_cloud .tagcloud a,
.widget_search div #searchsubmit,
.widget_product_search div #searchsubmit,
.widget_search div #searchsubmit.btn-mini,
.widget_product_search div #searchsubmit.btn-mini,
.price_slider_wrapper .price_slider_amount .button,
.price_slider_wrapper .price_slider_amount .button.btn-mini,
.product_list_widget li a,
.widget_shopping_cart_content .total,
.widget_shopping_cart_content .buttons .button,
.widget_shopping_cart_content .buttons .button.btn-mini,
.widget_rss ul li .rss-date,
.widget_calendar #wp-calendar caption,
.widget_calendar #wp-calendar #prev,
.widget_calendar #wp-calendar #next,
.cart-env .cart-totals > li .cross-sells h4,
.cart-env .cart-totals > li .cross-sells .product-entry .product-info h3,
.cart-env .cart-main-buttons .button,
.cart-env .cart-item-row .col .item-name .item-name-span,
.cart-env .cart-item-row .col .quantity input[type="button"].btn-mini,
.cart-env .shipping_calculator button[name="calc_shipping"],
.blog .blog-post .blog-img.hover-effect a .hover em,
.blog .blog-post .blog_content h1,
.blog .blog-post .blog_content h2,
.blog .blog-post .blog_content .post-meta .blog_date,
.blog .blog-post .blog_content .post-meta .comment_text,
.blog .single_post .post_img .loading,
.blog .single_post .post_details > h1,
.blog .single_post .post_details > h2,
.blog .single_post .post_details .author_text,
.blog .single_post .post-content h1,
.blog .single_post .post-content h2,
.blog .single_post .post-content h3,
.blog .single_post .post-content h4,
.blog .single_post .post-content h5,
.share-post h1,
.comments h1,
.comments .author_post .author_about_part .author_name,
.comments .author_post .author_about_part .date_time_reply_text,
.comments .author_post .author_about_part .comment-content h1,
.comments .author_post .author_about_part .comment-content h2,
.comments .author_post .author_about_part .comment-content h3,
.comments .author_post .author_about_part .comment-content h4,
.comments .author_post .author_about_part .comment-content h5,
.comments .form-submit #submit,
.comments .form-submit #submit.btn-mini,
.comment-respond input#submit.btn-mini,
.laborator-woocommerce .product-single .product-left-info .ribbon .ribbon-content,
.laborator-woocommerce .product-single .entry-summary .entry-title,
.laborator-woocommerce .product-single .entry-summary .posted_in,
.laborator-woocommerce .product-single .entry-summary .single_add_to_cart_button,
.laborator-woocommerce .product-single .entry-summary .quantity input[type="button"].btn-mini,
.laborator-woocommerce .product-single .entry-summary .stock,
.laborator-woocommerce .product-single .entry-summary .variations_form .variations .reset_variations,
.laborator-woocommerce .product-single .woocommerce-tabs .tabs > li a,
.laborator-woocommerce .product-single .woocommerce-tabs .tab-title,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab h1,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab h2,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab h3,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab h4,
.laborator-woocommerce .product-single .woocommerce-tabs .description-tab h5,
.laborator-woocommerce .product-single .woocommerce-tabs #reviews .comments .comment-entry time,
.laborator-woocommerce .product-single .woocommerce-tabs #reviews .comments .comment-entry .meta,
.laborator-woocommerce .product-single .woocommerce-tabs #reviews .comments .comment-entry .meta .verified,
.laborator-woocommerce .product-single .woocommerce-tabs #reviews #review_form h3,
.laborator-woocommerce .product-single .woocommerce-tabs #reviews #review_form input[type="submit"],
.laborator-woocommerce .product-single .woocommerce-tabs #reviews #review_form input[type="submit"].btn-mini,
.laborator-woocommerce .product-single .wl-button-wrap .wl-already-in,
.laborator-woocommerce .product-single .yith-wcwl-add-to-wishlist.laborator .yith-btn,
.tooltip,
.not-found .center div h2,
.not-found .center div a,
.toggle-info-blocks,
.page-container .wpb_text_column h1,
.page-container .wpb_text_column h2,
.page-container .wpb_text_column h3,
.page-container .wpb_text_column h4,
.page-container .wpb_text_column h5,
.page-container .wpb_text_column h6,
.page-container .wpb_tabs.wpb_content_element .wpb_tour_tabs_wrapper .wpb_tabs_nav li a,
.page-container .wpb_accordion.wpb_content_element .wpb_accordion_wrapper .wpb_accordion_section .wpb_accordion_header,
.page-container .wpb_content_element blockquote strong,
.page-container .lab_wpb_banner .banner-call-button a,
.page-container .lab_wpb_blog_posts .blog-posts .blog-post .image a .hover-readmore,
.page-container .lab_wpb_blog_posts .blog-posts .blog-post .post h3,
.page-container .lab_wpb_blog_posts .blog-posts .blog-post .post .date,
.page-container .lab_wpb_blog_posts .more-link .btn,
.page-container .vc_separator.double-bordered-thick h4,
.page-container .vc_separator.double-bordered-thin h4,
.page-container .vc_separator.double-bordered h4,
.page-container .vc_separator.one-line-border h4,
.lab_wpb_banner_2 .title,
.woocommerce .woocommerce-error h2,
.woocommerce .woocommerce-error h3,
.woocommerce .woocommerce-error h4,
.woocommerce .woocommerce-error h5,
.woocommerce .woocommerce-error a.alert-link,
.woocommerce .woocommerce-success h2,
.woocommerce .woocommerce-success h3,
.woocommerce .woocommerce-success h4,
.woocommerce .woocommerce-success h5,
.woocommerce .woocommerce-success a.alert-link,
.woocommerce .woocommerce-success .button.btn-mini,
.woocommerce .woocommerce-info h2,
.woocommerce .woocommerce-info h3,
.woocommerce .woocommerce-info h4,
.woocommerce .woocommerce-info h5,
.woocommerce .woocommerce-info a.alert-link,
.laborator-woocommerce .up,
.laborator-woocommerce .with-divider,
.laborator-woocommerce .select-wrapper .select-placeholder,
.laborator-woocommerce .form-label,
.laborator-woocommerce #wl-wrapper .wl-intro .wl-share-url strong,
.laborator-woocommerce #wl-wrapper .wl-intro .wlbuttons-list .btn,
.laborator-woocommerce #wl-wrapper .wl-tab-wrap .wl-tabs li a,
.laborator-woocommerce #wl-wrapper .shop_table thead td,
.laborator-woocommerce #wl-wrapper .shop_table thead th,
.laborator-woocommerce #wl-wrapper .shop_table tbody .product-name a,
.laborator-woocommerce #wl-wrapper .shop_table tbody .quantity input[type="button"].btn-mini,
.laborator-woocommerce #wl-wrapper .my-lists-table .row-actions .edit a,
.laborator-woocommerce #wl-wrapper .my-lists-table .row-actions .trash a,
.laborator-woocommerce #wl-wrapper .my-lists-table .row-actions .view a,
.laborator-woocommerce #wl-wrapper .wishlist_table .button,
.laborator-woocommerce #wl-wrapper .wishlist_table .button.btn-mini,
.laborator-woocommerce .yith-wcwl-share h4,
.woocommerce .wl-list-pop,
.loader strong,
.lab_wpb_lookbook_carousel .lookbook-header h2,
.lab_wpb_lookbook_carousel .lookbook-carousel .product-item .lookbook-hover-info .lookbook-inner-content .posted_in,
.lab_wpb_lookbook_carousel .lookbook-carousel .product-item .lookbook-hover-info .lookbook-inner-content .title,
.lab_wpb_lookbook_carousel .lookbook-carousel .product-item .lookbook-hover-info .lookbook-inner-content .price-and-add-to-cart .add-to-cart-btn,
.mobile-menu .nav > li > a,
.mobile-menu .nav ul > li > a,
.mobile-menu .cart-items {
	text-transform: none;
}
<?php endif; ?>

<?php if($base_font_size): ?>
.main-font-size,
.main-sidebar ul.nav .sub-menu li > a,
.oxygen-top-menu > .wrapper > .top-menu > .main .tl-header .sec-nav .sec-nav-menu > li a,
.oxygen-top-menu > .wrapper > .main-menu-top > .main .main-menu-env .nav > li .sub-menu > li > a,
.oxygen-top-menu > .wrapper > .main-menu-top > .main .main-menu-env .nav > li.has-sub > a:after,
.oxygen-top-menu > .wrapper > .top-menu-centered > .main .navs .main-menu-env .nav > li .sub-menu > li > a,
.oxygen-top-menu > .wrapper > .top-menu-centered > .main .navs .main-menu-env .nav > li.has-sub > a:after,
.oxygen-top-menu > .wrapper > .top-menu-centered > .main .navs .sec-nav-menu > li a,
.order ul li i,
.accordion .accordion-body,
.drop-down .form-dropdown li a,
.shop .items .item-wrapper .item .description .price .real_price,
footer.footer_widgets p,
.header-cart .cart-items .cart-item .details .price-quantity .price del,
.shop_add_cart .shop_add_cart_part .col_2 .pro_category_detail_text,
.widget_recent_comments .recentcomments a,
.widget_text .textwidget,
.product_list_widget li del,
.widget_rss ul li .rssSummary,
.blog .blog-post .blog_content p,
.blog .blog-post .blog_content .post-meta .blog_date,
.blog .blog-post .blog_content .post-meta .comment_text,
.blog .single_post .post_details,
.blog .single_post .post_details > h2,
.blog .single_post .post_details .author_about,
.comments .author_post .author_about_part .comment-content,
.laborator-woocommerce .product-single .entry-summary .quantity input.qty,
.laborator-woocommerce .product-single .entry-summary .stock,
.laborator-woocommerce .product-single .entry-summary .group_table .price del,
.laborator-woocommerce .product-single .entry-summary .group_table .price del .amount,
.tooltip,
.page-container .wpb_content_element blockquote,
.page-container .lab_wpb_blog_posts .blog-posts .blog-post .post .content p,
.laborator-woocommerce .items .product .white-block .price del,
.laborator-woocommerce .select-wrapper .select-placeholder,
.laborator-woocommerce #wl-wrapper .wl-intro .wl-share-url,
.laborator-woocommerce #wl-wrapper .shop_table tbody .quantity input.qty,
.loader strong,
.lab_wpb_lookbook_carousel .lookbook-carousel .product-item .lookbook-hover-info .lookbook-inner-content .posted_in,
.lab_wpb_lookbook_carousel .lookbook-carousel .product-item.cols-6 .lookbook-hover-info .lookbook-inner-content .price del,
.lab_wpb_lookbook_carousel .lookbook-carousel .product-item.cols-5 .lookbook-hover-info .lookbook-inner-content .price del,
.lab_wpb_lookbook_carousel .lookbook-carousel .product-item.cols-6 .lookbook-hover-info .lookbook-inner-content .price del .amount,
.lab_wpb_lookbook_carousel .lookbook-carousel .product-item.cols-5 .lookbook-hover-info .lookbook-inner-content .price del .amount {
	font-size: <?php echo $base_font_size; ?>px;
}
<?php endif; ?>
</style>

<?php

	global $custom_styles;

	$custom_styles = ob_get_clean();

	add_action('wp_print_scripts', create_function('', 'global $custom_styles; echo compress_text($custom_styles);'));
}




# Compile Custom Skin
function custom_skin_compile($vars = array(), $file = 'css/custom-skin.less')
{
	$result = false;

	include_once THEMEDIR . 'inc/lib/lessc.inc.php';

	$file = THEMEDIR . 'assets/' . $file;

	$file_contents = file_get_contents($file) . PHP_EOL;
	$file_contents .= file_get_contents(THEMEDIR . 'assets/css/skin-structure.less');

	foreach($vars as $var => $value)
	{
		if( ! preg_match("/#[a-f0-9]{3}([a-f0-9]{3})?/i", $value))
			$value = '#000';

		$file_contents = preg_replace("/(@{$var})\s*:\s*\{value\}/i", "$1: $value", $file_contents);
	}

	$less = new lessc;
	$css = $less->compile($file_contents);

	if($fp = fopen(str_replace(".less", ".css", $file), "w"))
	{
		fwrite($fp, $css);
		fclose($fp);

		$result = true;
	}

	return $result;
}


# Catalog mode check
function is_catalog_mode()
{
	return get_data('shop_catalog_mode') == true;
}

function catalog_mode_hide_prices()
{
	return get_data('shop_catalog_mode_hide_prices') == true;
}


# Remove Width and Height attribute
function remove_wh($html)
{
	$html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
	return $html;
}



# Show Thumbnail
function laborator_show_thumbnail($post_id, $thumb_size = '')
{
	return remove_wh( wp_get_attachment_image(get_post_thumbnail_id($post_id), $thumb_size) );
}



# Logout URL
function laborator_logout_url($url)
{
	$logout_nonce = wp_create_nonce('lab-logout');

	return esc_url( add_query_arg(array("logout-nonce" => $logout_nonce, "to" => urlencode($url)), home_url("/")) );
}

if(isset($_REQUEST['logout-nonce']) && wp_verify_nonce($_REQUEST['logout-nonce'], 'lab-logout'))
{
	$to = isset($_REQUEST['to']) ? $_REQUEST['to'] : home_url();

	wp_clear_auth_cookie();
	wp_redirect($to);
	exit;
}


# Translate Post ID
function translate_id($base_id, $type = 'page')
{
	global $wpdb, $sitepress;

	if( ! defined("ICL_LANGUAGE_CODE") || ! method_exists($sitepress, 'get_default_language'))
		return $base_id;

	$default_language = $sitepress->get_default_language();
	$current_language = ICL_LANGUAGE_CODE;

	if($current_language != $default_language)
	{
		return icl_object_id($base_id, $type);
	}

	return $base_id;
}