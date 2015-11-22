<?php function thb_testimonials( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'values'     => ''
    ), $atts));
	$output= $out ='';
	$graph_lines = explode(",", $values);
	$max_value = 0.0;
	$graph_lines_data = array();
	foreach ($graph_lines as $line) {
	    $new_line = array();
	    $color_index = 2;
	    $data = explode("|", $line);
	    $new_line['value'] = isset($data[0]) ? $data[0] : 0;
	    $new_line['percentage_value'] = isset($data[1]) && preg_match('/^\d{1,2}\%$/', $data[1]) ? (float)str_replace('%', '', $data[1]) : false;
	    if($new_line['percentage_value']!=false) {
	        $color_index+=1;
	        $new_line['label'] = isset($data[2]) ? $data[2] : '';
	    } else {
	        $new_line['label'] = isset($data[1]) ? $data[1] : '';
	    }
	
	    if($new_line['percentage_value']===false && $max_value < (float)$new_line['value']) {
	        $max_value = $new_line['value'];
	    }
	
	    $graph_lines_data[] = $new_line;
	}
	$output .= '<div class="owl carousel testimonials" data-columns="1">';
	foreach($graph_lines_data as $line) {
	    $output .= '<div class="testimonial">';
	    $output .= '<p>'. $line['label'].'</p>';
	    $output .= '<small>'.$line['value'].'</small>';
	    $output .= '</div>';
	}
	$output .= '</div>';
	return $output;

}
add_shortcode('thb_testimonials', 'thb_testimonials');
