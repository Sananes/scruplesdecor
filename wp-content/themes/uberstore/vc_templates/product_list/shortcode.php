<?php function thb_product_list( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'product_sort' => 'best-sellers',
       	'title' => '',
       	'item_count' => '4',
       	'product_ids' => ''
    ), $atts));
	
	global $woocommerce, $woocommerce_loop;

	$args = array();
	
	if ($product_sort == "latest-products") {
		$args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'   => 1,
				'posts_per_page' => $item_count
			);	    
	} else if ($product_sort == "top-rated") {
		add_filter( 'posts_clauses',  array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
				
		$args = array(
		    'post_type' => 'product',
		    'post_status' => 'publish',
				'ignore_sticky_posts'   => 1,
		    'posts_per_page' => $item_count
		);
		$args['meta_query'] = $woocommerce->query->get_meta_query();
	
	} else if ($product_sort == "sale-products") {
		$args = array(
			    'post_type' => 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'   => 1,
				'posts_per_page' => $item_count,
				'meta_query' => array(
					array(
						'key' => '_sale_price',
						'value' =>  0,
						'compare'   => '>',
						'type'      => 'NUMERIC'
					),
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					),
				)
			);
	} else if ($product_sort == "by-id"){
		$product_id_array = explode(',', $product_ids);
		$args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'   => 1,
				'post__in'		=> $product_id_array
			);	    
	} else {
		$args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'   => 1,
				'posts_per_page' => $item_count,
				'meta_key' 		=> 'total_sales',
				'orderby' 		=> 'meta_value'
			);	    
	}
	$products = new WP_Query( $args );
 	
	 $out = '';
 	
	 if ( $products->have_posts() ) {

		$out .= '<ul class="list">';
		
		if ($title) { $out .= '<strong>'.$title.'</strong>'; }
		       
	   while ( $products->have_posts() ) : $products->the_post();
	
	       $rating = $output = "";
	        
	        global $product, $post, $wpdb, $woocommerce_loop;
	
	        if ( ! $product->is_visible() ) { return; }
	       	
	       	if ( comments_open() ) {
	       	
	       		$count = $wpdb->get_var("
	       		    SELECT COUNT(meta_value) FROM $wpdb->commentmeta
	       		    LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
	       		    WHERE meta_key = 'rating'
	       		    AND comment_post_ID = $post->ID
	       		    AND comment_approved = '1'
	       		    AND meta_value > 0
	       		");
	       	
	       		$rating = $wpdb->get_var("
	     	        SELECT SUM(meta_value) FROM $wpdb->commentmeta
	     	        LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
	     	        WHERE meta_key = 'rating'
	     	        AND comment_post_ID = $post->ID
	     	        AND comment_approved = '1'
	     	    ");
	       	
	       	    if ( $count > 0 ) {
	       	
	       	        $average = number_format($rating / $count, 2);	           			
	       	        $rating_output = '
	       	        <div class="star-rating" title="'.sprintf(__('Rated %s out of 5', THB_THEME_NAME), $average).'" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
	       	        	<span style="width:'.($average*10).'px"><strong itemprop="ratingValue" class="rating">'.$average.'</strong>
	       	        </div>';
	       	
	       	    }
	       	}
	        
	        $output .= '<li class="post" itemscope itemtype="http://schema.org/Product">';
	        
	        $output .= '<figure>';
	        $output .= '<a href="'.get_permalink($post->ID).'">';
	        $output .= get_the_post_thumbnail();	
	        $output .= '</a>';
	        $output .= '</figure>';
	        $output .= '<div class="post-title">';
	        $output .= '<h5 itemprop="name"><a href="'.get_permalink($post->ID).'">'.get_the_title().'</a></h5>';
	   		
	   		if ($product_sort == "top-rated") {
	   			
	   			$output .= $rating_output;
	   		
	   		} else {
	        
	    		$size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
	    		$output .= $product->get_categories( ', ', '<span class="post_categories">' . _n( '', '', $size, THB_THEME_NAME ) . ' ', '</span>' );
	    	
	    	}
	      	
	        $output .= '<span class="price" itemprop="price">'.$product->get_price_html().'</span>';
	        $output .= '</div>';
	        $output .= '</li>';
	        
	        $out .= $output;
	
	   endwhile;
		
		$out .= '</ul>';
	   
	 }
   
   wp_reset_query();
   wp_reset_postdata();
   remove_filter( 'posts_clauses',  array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
	   
  return $out;
}
add_shortcode('thb_product_list', 'thb_product_list');
