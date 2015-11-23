<?php
	
	// Shortcode: nm_post_slider
	function nm_shortcode_post_slider( $atts, $content = NULL ) {
		nm_add_page_include( 'post-slider' );
		
		extract( shortcode_atts( array(
			'num_posts'			=> '8',
			'category'			=> '',
			'columns'			=> '4',
			'image_type'		=> 'fluid',
			'bg_image_height'	=> '',
			'post_excerpt'		=> '0'
		), $atts ) );
		
		$args = array(
			'post_status' 		=> 'publish',
			'post_type' 		=> 'post',
			'category_name' 	=> $category,
			'posts_per_page'	=> intval( $num_posts )
		);
		
		$posts = new WP_Query( $args );
		
		$columns = intval( $columns );
		$data_settings = 'data-slides-to-show="' . $columns . '" data-slides-to-scroll="' . $columns . '"';
		
		ob_start();
		
		if ( $posts->have_posts() ) :
		?>
        <div class="nm-post-slider slick-slider slick-controls-gray slick-dots-centered slick-dots-active-small" <?php echo $data_settings; ?>>
			<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
            <div>
                <div class="nm-post-slider-inner">
                    <a href="<?php esc_url( the_permalink() ); ?>" class="nm-post-slider-image">
					<?php 
                    if ( has_post_thumbnail() ) :
                        $image_id = get_post_thumbnail_id();
                        $image = wp_get_attachment_image_src( $image_id, 'nm_large', true );
                    	
						// Image HTML
						if ( $image_type === 'fluid' ) {
                        	echo '<img src="' . esc_url( $image[0] ) . '" />';
						} else {
                        	$image_height_style = ( strlen( $bg_image_height ) > 0 ) ? 'height:' . intval( $bg_image_height ) . 'px; ' : '';
                        	
							printf( '<div class="bg-image" style="%sbackground-image:url(%s);"></div>', $image_height_style, $image[0] );
						}
					?>
						<div class="nm-image-overlay"></div>
					<?php else : ?>
						<span class="nm-post-slider-noimage"></span>
					<?php endif; ?>
                    </a>
                    
                    <div class="nm-post-slider-content">
                        <div class="nm-post-meta"><?php the_time( get_option( 'date_format' ) ); ?></div>
                        <h3><a href="<?php esc_url( the_permalink() ); ?>" class="dark"><?php the_title(); ?></a></h3>
                        <?php if ( $post_excerpt ) : ?>
                        <div class="nm-post-slider-excerpt"><?php esc_html( the_excerpt() ); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php
		endif;
		
		wp_reset_query();
		
		$output = ob_get_clean();
		
		return $output;
	}
	
	add_shortcode( 'nm_post_slider', 'nm_shortcode_post_slider' );
	