<?php function thb_progressbar( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'values'     => '',
       	'bgcolor' 		 => ''
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
	
	foreach($graph_lines_data as $line) {

	    $output .= '<div class="progress_bar">';
	    $output .= '<small>'. $line['label'].'</small>';
	    if($line['percentage_value']!==false) {
	        $percentage_value = $line['percentage_value'];
	    } elseif($max_value > 100.00) {
	        $percentage_value = (float)$line['value'] > 0 && $max_value > 100.00 ? round((float)$line['value']/$max_value*100, 4) : 0;
	    } else {
	        $percentage_value = $line['value'];
	    }
	    $output .= '<span class="bar '.$bgcolor.'" data-value="'.$line['value'].'"></span>';
	    $output .= '</div>';
	}
	return $output;

}
add_shortcode('thb_progressbar', 'thb_progressbar');
