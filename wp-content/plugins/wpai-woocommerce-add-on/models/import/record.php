<?php

class PMWI_Import_Record extends PMWI_Model_Record {		

	/**
	 * Associative array of data which will be automatically available as variables when template is rendered
	 * @var array
	 */
	public $data = array();

	public $options = array();

	public $previousID;

	public $post_meta_to_update;
	public $post_meta_to_insert;
	public $existing_meta_keys;
	public $articleData;

	public $reserved_terms = array(
				'attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category', 'category__and',
				'category__in', 'category__not_in', 'category_name', 'comments_per_page', 'comments_popup', 'cpage', 'day',
				'debug', 'error', 'exact', 'feed', 'hour', 'link_category', 'm', 'minute', 'monthnum', 'more', 'name',
				'nav_menu', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page', 'page_id', 'paged', 'pagename', 'pb', 'perm',
				'post', 'post__in', 'post__not_in', 'post_format', 'post_mime_type', 'post_status', 'post_tag', 'post_type',
				'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots', 's', 'search', 'second', 'sentence',
				'showposts', 'static', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in', 'tag__not_in', 'tag_id',
				'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'type', 'w', 'withcomments', 'withoutcomments', 'year',
			);
	
	/**
	 * Initialize model instance
	 * @param array[optional] $data Array of record data to initialize object with
	 */
	public function __construct($data = array()) {
		parent::__construct($data);
		$this->setTable(PMXI_Plugin::getInstance()->getTablePrefix() . 'imports');
	}	
	
	/**
	 * Perform import operation
	 * @param string $xml XML string to import
	 * @param callback[optional] $logger Method where progress messages are submmitted
	 * @return PMWI_Import_Record
	 * @chainable
	 */
	public function parse($parsing_data = array()) { //$import, $count, $xml, $logger = NULL, $chunk = false, $xpath_prefix = ""

		extract($parsing_data);

		if ($import->options['custom_type'] != 'product') return;

		add_filter('user_has_cap', array($this, '_filter_has_cap_unfiltered_html')); kses_init(); // do not perform special filtering for imported content
		
		$this->options = $import->options;		

		$cxpath = $xpath_prefix . $import->xpath;

		$this->data = array();
		$records = array();
		$tmp_files = array();

		$chunk == 1 and $logger and call_user_func($logger, __('Composing product data...', 'pmxi_plugin'));

		// Composing product types
		if ($import->options['is_multiple_product_type'] != 'yes' and "" != $import->options['single_product_type']){
			$this->data['product_types'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_type'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_types'] = array_fill(0, $count, $import->options['multiple_product_type']);
		}

		// Composing product is Virtual									
		if ($import->options['is_product_virtual'] == 'xpath' and "" != $import->options['single_product_virtual']){
			$this->data['product_virtual'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_virtual'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_virtual'] = array_fill(0, $count, $import->options['is_product_virtual']);
		}

		// Composing product is Downloadable									
		if ($import->options['is_product_downloadable'] == 'xpath' and "" != $import->options['single_product_downloadable']){
			$this->data['product_downloadable'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_downloadable'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_downloadable'] = array_fill(0, $count, $import->options['is_product_downloadable']);
		}

		// Composing product is Variable Enabled									
		if ($import->options['is_product_enabled'] == 'xpath' and "" != $import->options['single_product_enabled']){
			$this->data['product_enabled'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_enabled'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_enabled'] = array_fill(0, $count, $import->options['is_product_enabled']);
		}

		// Composing product is Featured									
		if ($import->options['is_product_featured'] == 'xpath' and "" != $import->options['single_product_featured']){
			$this->data['product_featured'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_featured'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_featured'] = array_fill(0, $count, $import->options['is_product_featured']);
		}

		// Composing product is Visibility									
		if ($import->options['is_product_visibility'] == 'xpath' and "" != $import->options['single_product_visibility']){
			$this->data['product_visibility'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_visibility'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_visibility'] = array_fill(0, $count, $import->options['is_product_visibility']);
		}

		if ("" != $import->options['single_product_sku']){
			$this->data['product_sku'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_sku'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sku'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_url']){
			$this->data['product_url'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_url'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_url'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_button_text']){
			$this->data['product_button_text'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_button_text'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_button_text'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_regular_price']){
			$this->data['product_regular_price'] = array_map(array($this, 'adjust_price'), array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['single_product_regular_price'], $file)->parse($records)),  array_fill(0, $count, "regular_price")); $tmp_files[] = $file;			
		}
		else{
			$count and $this->data['product_regular_price'] = array_fill(0, $count, "");
		}

		if ($import->options['is_regular_price_shedule'] and "" != $import->options['single_sale_price_dates_from']){
			$this->data['product_sale_price_dates_from'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_sale_price_dates_from'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sale_price_dates_from'] = array_fill(0, $count, "");
		}

		if ($import->options['is_regular_price_shedule'] and "" != $import->options['single_sale_price_dates_to']){
			$this->data['product_sale_price_dates_to'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_sale_price_dates_to'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sale_price_dates_to'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_sale_price']){
			$this->data['product_sale_price'] = array_map(array($this, 'adjust_price'), array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['single_product_sale_price'], $file)->parse($records)), array_fill(0, $count, "sale_price")); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sale_price'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_whosale_price']){
			$this->data['product_whosale_price'] = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['single_product_whosale_price'], $file)->parse($records)); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_whosale_price'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_files']){
			$this->data['product_files'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_files'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_files'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_files_names']){
			$this->data['product_files_names'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_files_names'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_files_names'] = array_fill(0, $count, "");
		}		

		if ("" != $import->options['single_product_download_limit']){
			$this->data['product_download_limit'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_download_limit'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_download_limit'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_download_expiry']){
			$this->data['product_download_expiry'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_download_expiry'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_download_expiry'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_download_type']){
			$this->data['product_download_type'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_download_type'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_download_type'] = array_fill(0, $count, "");
		}
		
		// Composing product Tax Status									
		if ($import->options['is_multiple_product_tax_status'] != 'yes' and "" != $import->options['single_product_tax_status']){
			$this->data['product_tax_status'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_tax_status'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_tax_status'] = array_fill(0, $count, $import->options['multiple_product_tax_status']);
		}

		// Composing product Tax Class									
		if ($import->options['is_multiple_product_tax_class'] != 'yes' and "" != $import->options['single_product_tax_class']){
			$this->data['product_tax_class'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_tax_class'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_tax_class'] = array_fill(0, $count, $import->options['multiple_product_tax_class']);
		}

		// Composing product Manage stock?								
		if ($import->options['is_product_manage_stock'] == 'xpath' and "" != $import->options['single_product_manage_stock']){
			$this->data['product_manage_stock'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_manage_stock'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_manage_stock'] = array_fill(0, $count, $import->options['is_product_manage_stock']);
		}

		if ("" != $import->options['single_product_stock_qty']){
			$this->data['product_stock_qty'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_stock_qty'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_stock_qty'] = array_fill(0, $count, "");
		}					

		// Composing product Stock status							
		if ($import->options['product_stock_status'] == 'xpath' and "" != $import->options['single_product_stock_status']){
			$this->data['product_stock_status'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_stock_status'], $file)->parse($records); $tmp_files[] = $file;						
		}
		elseif($import->options['product_stock_status'] == 'auto'){
			$count and $this->data['product_stock_status'] = array_fill(0, $count, $import->options['product_stock_status']);
			foreach ($this->data['product_stock_qty'] as $key => $value) {
				$this->data['product_stock_status'][$key] = ((int) $value === 0) ? 'outofstock' : 'instock';
			}
		}
		else{
			$count and $this->data['product_stock_status'] = array_fill(0, $count, $import->options['product_stock_status']);
		}

		// Composing product Allow Backorders?						
		if ($import->options['product_allow_backorders'] == 'xpath' and "" != $import->options['single_product_allow_backorders']){
			$this->data['product_allow_backorders'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_allow_backorders'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_allow_backorders'] = array_fill(0, $count, $import->options['product_allow_backorders']);
		}

		// Composing product Sold Individually?					
		if ($import->options['product_sold_individually'] == 'xpath' and "" != $import->options['single_product_sold_individually']){
			$this->data['product_sold_individually'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_sold_individually'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_sold_individually'] = array_fill(0, $count, $import->options['product_sold_individually']);
		}

		if ("" != $import->options['single_product_weight']){
			$this->data['product_weight'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_weight'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_weight'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_length']){
			$this->data['product_length'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_length'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_length'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_width']){
			$this->data['product_width'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_width'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_width'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_height']){
			$this->data['product_height'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_height'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_height'] = array_fill(0, $count, "");
		}

		// Composing product Shipping Class				
		if ($import->options['is_multiple_product_shipping_class'] != 'yes' and "" != $import->options['single_product_shipping_class']){
			$this->data['product_shipping_class'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_shipping_class'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_shipping_class'] = array_fill(0, $count, $import->options['multiple_product_shipping_class']);
		}

		if ("" != $import->options['single_product_up_sells']){
			$this->data['product_up_sells'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_up_sells'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_up_sells'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_cross_sells']){
			$this->data['product_cross_sells'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_cross_sells'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_cross_sells'] = array_fill(0, $count, "");
		}

		if ($import->options['is_multiple_grouping_product'] != 'yes'){
			
			if ($import->options['grouping_indicator'] == 'xpath'){
				
				if ("" != $import->options['single_grouping_product']){
					$this->data['product_grouping_parent'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_grouping_product'], $file)->parse($records); $tmp_files[] = $file;						
				}
				else{
					$count and $this->data['product_grouping_parent'] = array_fill(0, $count, $import->options['multiple_grouping_product']);
				}

			}
			else{
				if ("" != $import->options['custom_grouping_indicator_name'] and "" != $import->options['custom_grouping_indicator_value'] ){
					$this->data['custom_grouping_indicator_name'] = XmlImportParser::factory($xml, $cxpath, $import->options['custom_grouping_indicator_name'], $file)->parse($records); $tmp_files[] = $file;	
					$this->data['custom_grouping_indicator_value'] = XmlImportParser::factory($xml, $cxpath, $import->options['custom_grouping_indicator_value'], $file)->parse($records); $tmp_files[] = $file;	
				}
				else{
					$count and $this->data['custom_grouping_indicator_name'] = array_fill(0, $count, "");
					$count and $this->data['custom_grouping_indicator_value'] = array_fill(0, $count, "");
				}
			}		
		}
		else{
			$count and $this->data['product_grouping_parent'] = array_fill(0, $count, $import->options['multiple_grouping_product']);
		}

		if ("" != $import->options['single_product_purchase_note']){
			$this->data['product_purchase_note'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_purchase_note'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_purchase_note'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_menu_order']){
			$this->data['product_menu_order'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_menu_order'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_menu_order'] = array_fill(0, $count, "");
		}
		
		// Composing product Enable reviews		
		if ($import->options['is_product_enable_reviews'] == 'xpath' and "" != $import->options['single_product_enable_reviews']){
			$this->data['product_enable_reviews'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_enable_reviews'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_enable_reviews'] = array_fill(0, $count, $import->options['is_product_enable_reviews']);
		}

		if ("" != $import->options['single_product_id']){
			$this->data['single_product_ID'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_id'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['single_product_ID'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_parent_id']){
			$this->data['single_product_parent_ID'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_parent_id'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['single_product_parent_ID'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_id_first_is_parent_id']){
			$this->data['single_product_id_first_is_parent_ID'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_id_first_is_parent_id'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['single_product_id_first_is_parent_ID'] = array_fill(0, $count, "");
		}		
		if ("" != $import->options['single_product_id_first_is_parent_title']){
			$this->data['single_product_id_first_is_parent_title'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_id_first_is_parent_title'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['single_product_id_first_is_parent_title'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_id_first_is_variation']){
			$this->data['single_product_id_first_is_variation'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_id_first_is_variation'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['single_product_id_first_is_variation'] = array_fill(0, $count, "");
		}

		// Composing product is Manage stock									
		if ($import->options['is_variation_product_manage_stock'] == 'xpath' and "" != $import->options['single_variation_product_manage_stock']){
			
			$this->data['v_product_manage_stock'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_variation_product_manage_stock'], $file)->parse($records); $tmp_files[] = $file;						
			
		}
		else{
			$count and $this->data['v_product_manage_stock'] = array_fill(0, $count, $import->options['is_variation_product_manage_stock']);
		}

		// Stock Qty
		if ($import->options['variation_stock'] != ""){
			
			$this->data['v_stock'] = XmlImportParser::factory($xml, $cxpath, $import->options['variation_stock'], $file)->parse($records); $tmp_files[] = $file;
			
		}
		else{
			$count and $this->data['v_stock'] = array_fill(0, $count, '');
		}

		// Stock Status
		if ($import->options['variation_stock_status'] == 'xpath' and "" != $import->options['single_variation_stock_status']){
			$this->data['v_stock_status'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_variation_stock_status'], $file)->parse($records); $tmp_files[] = $file;						
		}
		elseif($import->options['variation_stock_status'] == 'auto'){
			$count and $this->data['v_stock_status'] = array_fill(0, $count, $import->options['variation_stock_status']);
			foreach ($this->data['v_stock'] as $key => $value) {
				$this->data['v_stock_status'][$key] = ( (int) $value <= 0) ? 'outofstock' : 'instock';
			}
		}
		else{
			$count and $this->data['v_stock_status'] = array_fill(0, $count, $import->options['variation_stock_status']);
		}

		if ($import->options['matching_parent'] != "auto") {					
			switch ($import->options['matching_parent']) {
				case 'first_is_parent_id':
					$this->data['single_product_parent_ID'] = $this->data['single_product_ID'] = $this->data['single_product_id_first_is_parent_ID'];
					break;
				case 'first_is_parent_title':
					$this->data['single_product_parent_ID'] = $this->data['single_product_ID'] = $this->data['single_product_id_first_is_parent_title'];
					break;
				case 'first_is_variation':
					$this->data['single_product_parent_ID'] = $this->data['single_product_ID'] = $this->data['single_product_id_first_is_variation'];
					break;						
			}					
		}
		
		if ($import->options['matching_parent'] == 'manual' and $import->options['parent_indicator'] == "custom field"){
			if ("" != $import->options['custom_parent_indicator_name']){
				$this->data['custom_parent_indicator_name'] = XmlImportParser::factory($xml, $cxpath, $import->options['custom_parent_indicator_name'], $file)->parse($records); $tmp_files[] = $file;
			}
			else{
				$count and $this->data['custom_parent_indicator_name'] = array_fill(0, $count, "");
			}
			if ("" != $import->options['custom_parent_indicator_value']){
				$this->data['custom_parent_indicator_value'] = XmlImportParser::factory($xml, $cxpath, $import->options['custom_parent_indicator_value'], $file)->parse($records); $tmp_files[] = $file;
			}
			else{
				$count and $this->data['custom_parent_indicator_value'] = array_fill(0, $count, "");
			}			
		}
		
		// Composing variations attributes					
		$chunk == 1 and $logger and call_user_func($logger, __('Composing variations attributes...', 'pmxi_plugin'));
		$attribute_keys = array(); 
		$attribute_values = array();	
		$attribute_in_variation = array(); 
		$attribute_is_visible = array();			
		$attribute_is_taxonomy = array();	
		$attribute_create_taxonomy_terms = array();		
				
		if (!empty($import->options['attribute_name'][0])){			
			foreach ($import->options['attribute_name'] as $j => $attribute_name) { if ($attribute_name == "") continue;	
				$attribute_keys[$j]   = XmlImportParser::factory($xml, $cxpath, $attribute_name, $file)->parse($records); $tmp_files[] = $file;								
				$attribute_values[$j] = XmlImportParser::factory($xml, $cxpath, $import->options['attribute_value'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_in_variation[$j] = XmlImportParser::factory($xml, $cxpath, $import->options['in_variations'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_is_visible[$j] = XmlImportParser::factory($xml, $cxpath, $import->options['is_visible'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_is_taxonomy[$j] = XmlImportParser::factory($xml, $cxpath, $import->options['is_taxonomy'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_create_taxonomy_terms[$j] = XmlImportParser::factory($xml, $cxpath, $import->options['create_taxonomy_in_not_exists'][$j], $file)->parse($records); $tmp_files[] = $file;				
			}			
		}					
		
		// serialized attributes for product variations
		$this->data['serialized_attributes'] = array();
		if (!empty($attribute_keys)){
			foreach ($attribute_keys as $j => $attribute_name) {
							
				$this->data['serialized_attributes'][] = array(
					'names' => $attribute_name,
					'value' => $attribute_values[$j],
					'is_visible' => $attribute_is_visible[$j],
					'in_variation' => $attribute_in_variation[$j],
					'in_taxonomy' => $attribute_is_taxonomy[$j],
					'is_create_taxonomy_terms' => $attribute_create_taxonomy_terms[$j]
				);						

			}
		} 						

		remove_filter('user_has_cap', array($this, '_filter_has_cap_unfiltered_html')); kses_init(); // return any filtering rules back if they has been disabled for import procedure
		
		foreach ($tmp_files as $file) { // remove all temporary files created
			unlink($file);
		}

		if ($import->options['put_variation_image_to_gallery']){
			add_action('pmxi_gallery_image', array($this, 'wpai_gallery_image'), 10, 3);
		}

		return $this->data;
	}		

	public function filtering($var){
		return ("" == $var) ? false : true;
	}

	public function import( $importData = array() ){

		extract($importData); 

		if ( empty($this->options['custom_type']) or $this->options['custom_type'] != 'product') return;

		$cxpath = $xpath_prefix . $import->xpath;

		global $woocommerce;		

		extract($this->data);

		$is_new_product = empty($articleData['ID']);

		// Get types
		$product_type 	= empty( $product_types[$i] ) ? 'simple' : sanitize_title( stripslashes( $product_types[$i] ) );

		if ($this->options['update_all_data'] == 'no' and ! $this->options['is_update_product_type'] and ! $is_new_product ){			
			$product 	  = get_product($pid);
			$product_type = $product->product_type;			
		}

		$this->existing_meta_keys = array();
		foreach (get_post_meta($pid, '') as $cur_meta_key => $cur_meta_val) $this->existing_meta_keys[] = $cur_meta_key;

		$this->post_meta_to_update = array(); // for bulk UPDATE SQL query
		$this->post_meta_to_insert = array(); // for bulk INSERT SQL query
		$this->articleData = $articleData;
		$this->pushmeta($pid, 'total_sales', '0');

		$is_downloadable 	= $product_downloadable[$i];
		$is_virtual 		= $product_virtual[$i];
		$is_featured 		= $product_featured[$i];

		// Product type + Downloadable/Virtual
		if ($is_new_product or $this->options['update_all_data'] == 'no' and $this->options['is_update_product_type']) 			
			wp_set_object_terms( $pid, $product_type, 'product_type' );

		$this->pushmeta($pid, '_downloadable', ($is_downloadable == "yes") ? 'yes' : 'no' );
		$this->pushmeta($pid, '_virtual', ($is_virtual == "yes") ? 'yes' : 'no' );

		// Update post meta
		$this->pushmeta($pid, '_regular_price', (empty($product_regular_price[$i])) ? '' : stripslashes( $product_regular_price[$i] ) );
		$this->pushmeta($pid, '_sale_price', (empty($product_sale_price[$i])) ? '' : stripslashes( $product_sale_price[$i] ) );
		$this->pushmeta($pid, '_tax_status', stripslashes( $product_tax_status[$i] ) );
		$this->pushmeta($pid, '_tax_class', stripslashes( $product_tax_class[$i] ) );
		$this->pushmeta($pid, '_visibility', stripslashes( $product_visibility[$i] ) );
		$this->pushmeta($pid, '_purchase_note', stripslashes( $product_purchase_note[$i] ) );
		$this->pushmeta($pid, '_featured', ($is_featured == "yes") ? 'yes' : 'no' );

		// Dimensions
		if ( $is_virtual == 'no' ) {
			$this->pushmeta($pid, '_weight', stripslashes( $product_weight[$i] ) );
			$this->pushmeta($pid, '_length', stripslashes( $product_length[$i] ) );
			$this->pushmeta($pid, '_width', stripslashes( $product_width[$i] ) );
			$this->pushmeta($pid, '_height', stripslashes( $product_height[$i] ) );			
		} else {
			$this->pushmeta($pid, '_weight', '' );
			$this->pushmeta($pid, '_length', '' );
			$this->pushmeta($pid, '_width', '' );
			$this->pushmeta($pid, '_height', '' );			
		}

		$this->wpdb->update( $this->wpdb->posts, array('comment_status' => ($product_enable_reviews[$i] == 'yes') ? 'open' : 'closed' ), array('ID' => $pid));

		if ($this->options['update_all_data'] == 'yes' or $this->options['is_update_menu_order']) $this->wpdb->update( $this->wpdb->posts, array('menu_order' => ($product_menu_order[$i] != '') ? (int) $product_menu_order[$i] : 0 ), array('ID' => $pid));

		// Save shipping class
		if ( pmwi_is_update_taxonomy($articleData, $this->options, 'product_shipping_class') ){

			if (ctype_digit($product_shipping_class[$i])){

				$p_shipping_class = $product_shipping_class[$i] > 0 && $product_type != 'external' ? absint( $product_shipping_class[$i] ) : '';			

			}
			else{

				$t_shipping_class = term_exists($product_shipping_class[$i], 'product_shipping_class', 0);	
				if ( empty($t_shipping_class) and !is_wp_error($t_shipping_class) ){																																
					$t_shipping_class = term_exists(htmlspecialchars($product_shipping_class[$i]), 'product_shipping_class', 0);						
				}
				if ( ! is_wp_error($t_shipping_class) )												
					$p_shipping_class = (int) $t_shipping_class['term_id']; 				
			}
			
			wp_set_object_terms( $pid, $p_shipping_class, 'product_shipping_class');

		}

		// Unique SKU
		$sku				= ($is_new_product) ? '' : get_post_meta($pid, '_sku', true);
		$new_sku 			= wc_clean( trim( stripslashes( $product_sku[$i] ) ) );
		
		if ( $new_sku == '' and $this->options['disable_auto_sku_generation'] ) {
			$this->pushmeta($pid, '_sku', '' );				
		}
		elseif ( $new_sku == '' and ! $this->options['disable_auto_sku_generation'] ) {
			if ($is_new_product or $this->is_update_cf('_sku')){
				$unique_keys = XmlImportParser::factory($xml, $cxpath, $this->options['unique_key'], $file)->parse(); $tmp_files[] = $file;
				foreach ($tmp_files as $file) { // remove all temporary files created
					@unlink($file);
				}
				$new_sku = substr(md5($unique_keys[$i]), 0, 12);
			}
		}
		if ( $new_sku != '' and $new_sku !== $sku ) {
			if ( ! empty( $new_sku ) ) {
				if ( ! $this->options['disable_sku_matching'] and 
					$this->wpdb->get_var( $this->wpdb->prepare("
						SELECT ".$this->wpdb->posts.".ID
					    FROM ".$this->wpdb->posts."
					    LEFT JOIN ".$this->wpdb->postmeta." ON (".$this->wpdb->posts.".ID = ".$this->wpdb->postmeta.".post_id)
					    WHERE ".$this->wpdb->posts.".post_type = 'product'
					    AND ".$this->wpdb->posts.".post_status = 'publish'
					    AND ".$this->wpdb->postmeta.".meta_key = '_sku' AND ".$this->wpdb->postmeta.".meta_value = '%s'
					 ", $new_sku ) )
					) {
					$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: Product SKU must be unique.', 'pmxi_plugin')));
									
				} else {					
					$this->pushmeta($pid, '_sku', $new_sku );							
				}
			} else {
				$this->pushmeta($pid, '_sku', '' );
			}
		}

		// Save Attributes
		$attributes = array();

		$is_variation_attributes_defined = false;

		if ( $this->options['update_all_data'] == "yes" or ( $this->options['update_all_data'] == "no" and $this->options['is_update_attributes']) or $is_new_product){ // Update Product Attributes		

			$is_update_attributes = true;

			if ( !empty($serialized_attributes) ) {
				
				$attribute_position = 0;

				$attr_names = array();

				foreach ($serialized_attributes as $anum => $attr_data) {	$attr_name = $attr_data['names'][$i];

					if ( in_array( $attr_name, $this->reserved_terms ) ) {
						$attr_name .= 's';
					}

					if (empty($attr_name) or in_array($attr_name, $attr_names)) continue;

					$attr_names[] = $attr_name;					

					$is_visible 	= intval( $attr_data['is_visible'][$i] );
					$is_variation 	= intval( $attr_data['in_variation'][$i] );
					$is_taxonomy 	= intval( $attr_data['in_taxonomy'][$i] );

					if ( $is_variation and $attr_data['value'][$i] != "" ) {
				 		$is_variation_attributes_defined = true;
				 	}

					// Update only these Attributes, leave the rest alone
					if ($this->options['update_all_data'] == "no" and $this->options['is_update_attributes'] and $this->options['update_attributes_logic'] == 'only'){
						if ( ! empty($this->options['attributes_list']) and is_array($this->options['attributes_list'])) {
							if ( ! in_array( ( ($is_taxonomy) ? wc_attribute_taxonomy_name( $attr_name ) : $attr_name ) , array_filter($this->options['attributes_list'], 'trim'))){ 
								$attribute_position++;
								continue;
							}
						}
						else {
							$is_update_attributes = false;
							break;
						}
					}

					// Leave these attributes alone, update all other Attributes
					if ($this->options['update_all_data'] == "no" and $this->options['is_update_attributes'] and $this->options['update_attributes_logic'] == 'all_except'){
						if ( ! empty($this->options['attributes_list']) and is_array($this->options['attributes_list'])) {
							if ( in_array( ( ($is_taxonomy) ? wc_attribute_taxonomy_name( $attr_name ) : $attr_name ) , array_filter($this->options['attributes_list'], 'trim'))){ 
								$attribute_position++;
								continue;
							}
						}
					}

					if ( $is_taxonomy ) {										

						if ( isset( $attr_data['value'][$i] ) ) {
					 		
					 		$values = array_map( 'stripslashes', array_map( 'strip_tags', explode( '|', $attr_data['value'][$i] ) ) );

						 	// Remove empty items in the array
						 	$values = array_filter( $values, array($this, "filtering") );			

						 	if (intval($attr_data['is_create_taxonomy_terms'][$i])) $this->create_taxonomy($attr_name, $logger);			 						 							

						 	if ( ! empty($values) and taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) )){

						 		$attr_values = array();						 								 		
						 			
						 		foreach ($values as $key => $value) {

						 			$term = term_exists($value, wc_attribute_taxonomy_name( $attr_name ), 0);	

						 			if ( empty($term) and !is_wp_error($term) ){																																
										$term = term_exists(htmlspecialchars($value), wc_attribute_taxonomy_name( $attr_name ), 0);	
										if ( empty($term) and !is_wp_error($term) and intval($attr_data['is_create_taxonomy_terms'][$i])){		
											
											$term = wp_insert_term(
												$value, // the term 
											  	wc_attribute_taxonomy_name( $attr_name ) // the taxonomy										  	
											);													
										}
									}
									if ( ! is_wp_error($term) )												
										$attr_values[] = (int) $term['term_taxonomy_id']; 

						 		}

						 		$values = $attr_values;
						 		$values = array_map( 'intval', $values );
								$values = array_unique( $values );
						 	} 
						 	else $values = array(); 					 							 	

					 	} 				 				 						 	
					 	
				 		// Update post terms
				 		if ( taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ))			 			
				 			$this->associate_terms( $pid, $values, wc_attribute_taxonomy_name( $attr_name ) );				 					 	
				 		
				 		if ( !empty($values) ) {									 			
					 		// Add attribute to array, but don't set values
					 		$attributes[ sanitize_title(wc_attribute_taxonomy_name( $attr_name )) ] = array(
						 		'name' 			=> wc_attribute_taxonomy_name( $attr_name ),
						 		'value' 		=> '',
						 		'position' 		=> $attribute_position,
						 		'is_visible' 	=> $is_visible,
						 		'is_variation' 	=> $is_variation,
						 		'is_taxonomy' 	=> 1,
						 		'is_create_taxonomy_terms' => (!empty($attr_data['is_create_taxonomy_terms'][$i])) ? 1 : 0
						 	);

					 	}

				 	} else {

				 		if ( taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ))
				 			wp_set_object_terms( $pid, NULL, wc_attribute_taxonomy_name( $attr_name ) );			 		

				 		if (!empty($attr_data['value'][$i])){

					 		// Custom attribute - Add attribute to array and set the values
						 	$attributes[ sanitize_title( $attr_name ) ] = array(
						 		'name' 			=> sanitize_text_field( $attr_name ),
						 		'value' 		=> $attr_data['value'][$i],
						 		'position' 		=> $attribute_position,
						 		'is_visible' 	=> $is_visible,
						 		'is_variation' 	=> $is_variation,
						 		'is_taxonomy' 	=> 0
						 	);
						}

				 	}				 	

				 	$attribute_position++;
				}							
			}						
			
			if ($is_new_product or $is_update_attributes) {
				
				$current_product_attributes = get_post_meta($pid, '_product_attributes', true);

				update_post_meta($pid, '_product_attributes', ( ! empty($current_product_attributes)) ? array_merge($current_product_attributes, $attributes) : $attributes );					
			}

		}else{

			$is_variation_attributes_defined = true;

		}	// is update attributes

		// Sales and prices
		if ( ! in_array( $product_type, array( 'grouped' ) ) ) {

			$date_from = isset( $product_sale_price_dates_from[$i] ) ? $product_sale_price_dates_from[$i] : '';
			$date_to   = isset( $product_sale_price_dates_to[$i] ) ? $product_sale_price_dates_to[$i] : '';

			// Dates
			if ( $date_from ){
				$this->pushmeta($pid, '_sale_price_dates_from', strtotime( $date_from ));				
			}
			else{
				$this->pushmeta($pid, '_sale_price_dates_from', '');				
			}

			if ( $date_to ){
				$this->pushmeta($pid, '_sale_price_dates_to', strtotime( $date_to ));								
			}
			else{
				$this->pushmeta($pid, '_sale_price_dates_to', '');												
			}

			if ( $date_to && ! $date_from ){
				$this->pushmeta($pid, '_sale_price_dates_from', strtotime( 'NOW', current_time( 'timestamp' ) ) );	
			}

			// Update price if on sale
			if ( $product_sale_price[$i] != '' && $date_to == '' && $date_from == '' ){
				$this->pushmeta($pid, '_price', (empty($product_sale_price[$i])) ? '' : stripslashes( $product_sale_price[$i] ));						
			}
			else{
				$this->pushmeta($pid, '_price', (empty($product_regular_price[$i])) ? '' : stripslashes( $product_regular_price[$i] ));						
			}

			if ( $product_sale_price[$i] != '' && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ){				
				$this->pushmeta($pid, '_price', (empty($product_sale_price[$i])) ? '' : stripslashes( $product_sale_price[$i] ));				
			}

			if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
				$this->pushmeta($pid, '_price', (empty($product_regular_price[$i])) ? '' : stripslashes( $product_regular_price[$i] ));				
				$this->pushmeta($pid, '_sale_price_dates_from', '');				
				$this->pushmeta($pid, '_sale_price_dates_to', '');													
			}
		}

		if (in_array( $product_type, array( 'simple', 'external' ) )) { 

			if ($this->options['is_multiple_grouping_product'] != 'yes'){
				if ($this->options['grouping_indicator'] == 'xpath' and ! is_numeric($product_grouping_parent[$i])){
					$dpost = pmxi_findDuplicates(array(
						'post_type' => 'product',
						'ID' => $pid,
						'post_parent' => $articleData['post_parent'],
						'post_title' => $product_grouping_parent[$i]
					));				
					if (!empty($dpost))
						$product_grouping_parent[$i] = $dpost[0];	
					else				
						$product_grouping_parent[$i] = 0;
				}
				elseif ($this->options['grouping_indicator'] != 'xpath'){
					$dpost = pmxi_findDuplicates($articleData, $custom_grouping_indicator_name[$i], $custom_grouping_indicator_value[$i], 'custom field');
					if (!empty($dpost))
						$product_grouping_parent[$i] = array_shift($dpost);
					else				
						$product_grouping_parent[$i] = 0;
				}
			}

			if ( "" != $product_grouping_parent[$i] and absint($product_grouping_parent[$i]) > 0){

				$this->wpdb->update( $this->wpdb->posts, array('post_parent' => absint( $product_grouping_parent[$i] ) ), array('ID' => $pid));
				
			}
		}	

		// Update parent if grouped so price sorting works and stays in sync with the cheapest child
		if ( $product_type == 'grouped' || ( "" != $product_grouping_parent[$i] and absint($product_grouping_parent[$i]) > 0)) {

			$clear_parent_ids = array();													

			if ( $product_type == 'grouped' )
				$clear_parent_ids[] = $pid;		

			if ( "" != $product_grouping_parent[$i] and absint($product_grouping_parent[$i]) > 0 )
				$clear_parent_ids[] = absint( $product_grouping_parent[$i] );					

			if ( $clear_parent_ids ) {
				foreach( $clear_parent_ids as $clear_id ) {

					$children_by_price = get_posts( array(
						'post_parent' 	=> $clear_id,
						'orderby' 		=> 'meta_value_num',
						'order'			=> 'asc',
						'meta_key'		=> '_price',
						'posts_per_page'=> 1,
						'post_type' 	=> 'product',
						'fields' 		=> 'ids'
					) );
					if ( $children_by_price ) {
						foreach ( $children_by_price as $child ) {
							$child_price = get_post_meta( $child, '_price', true );							
							update_post_meta( $clear_id, '_price', $child_price );
						}
					}

					// Clear cache/transients
					//wc_delete_product_transients( $clear_id );
				}
			}
		}	

		// Sold Individuall
		if ( "yes" == $product_sold_individually[$i] ) {
			$this->pushmeta($pid, '_sold_individually', 'yes');			
		} else {
			$this->pushmeta($pid, '_sold_individually', '');			
		}

		// Stock Data
		if ( strtolower($product_manage_stock[$i]) == 'yes' ) {

			if ( $product_type == 'grouped' ) {

				$this->pushmeta($pid, '_stock_status', stripslashes( $product_stock_status[$i] ));	
				$this->pushmeta($pid, '_stock', '');	
				$this->pushmeta($pid, '_manage_stock', 'no');	
				$this->pushmeta($pid, '_backorders', 'no');	
				
			} elseif ( $product_type == 'external' ) {

				$this->pushmeta($pid, '_stock_status', 'instock');	
				$this->pushmeta($pid, '_stock', '');	
				$this->pushmeta($pid, '_manage_stock', 'no');	
				$this->pushmeta($pid, '_backorders', 'no');	
				
			} elseif ( ! empty( $product_manage_stock[$i] ) ) {

				// Manage stock
				$this->pushmeta($pid, '_stock_status', stripslashes( $product_stock_status[$i] ));	
				$this->pushmeta($pid, '_stock', (int) $product_stock_qty[$i]);	
				$this->pushmeta($pid, '_manage_stock', 'yes');	
				$this->pushmeta($pid, '_backorders', stripslashes( $product_allow_backorders[$i] ));				

				// Check stock level
				if ( $product_type !== 'variable' && $product_allow_backorders[$i] == 'no' && (int) $product_stock_qty[$i] < 1 ){
					$this->pushmeta($pid, '_stock_status', 'outofstock');						
				}

			} else {

				// Don't manage stock
				$this->pushmeta($pid, '_stock_status', stripslashes( $product_stock_status[$i] ));	
				$this->pushmeta($pid, '_stock', '');	
				$this->pushmeta($pid, '_manage_stock', 'no');	
				$this->pushmeta($pid, '_backorders', stripslashes( $product_allow_backorders[$i] ));				

			}

		} else {

			if ( $product_type == 'external' ) {

				$this->pushmeta($pid, '_stock_status', 'instock');	
				$this->pushmeta($pid, '_stock', '');	
				$this->pushmeta($pid, '_manage_stock', 'no');	
				$this->pushmeta($pid, '_backorders', 'no');	
				
			}
			else{

				$this->pushmeta($pid, '_stock_status', stripslashes( $product_stock_status[$i] ));				

			}

		}

		// Upsells
		if ( !empty( $product_up_sells[$i] ) ) {
			$upsells = array();
			$ids = array_filter(explode(',', $product_up_sells[$i]), 'trim');
			foreach ( $ids as $id ){								
				$args = array(
					'post_type' => 'product',
					'meta_query' => array(
						array(
							'key' => '_sku',
							'value' => $id,						
						)
					)
				);			
				$query = new WP_Query( $args );
				
				if ( $query->have_posts() ) $upsells[] = $query->post->ID;

				wp_reset_postdata();
			}								

			$this->pushmeta($pid, '_upsell_ids', $upsells);	
			
		} else {
			if ($is_new_product or $this->is_update_cf('_upsell_ids')) delete_post_meta( $pid, '_upsell_ids' );
		}

		// Cross sells
		if ( !empty( $product_cross_sells[$i] ) ) {
			$crosssells = array();
			$ids = array_filter(explode(',', $product_cross_sells[$i]), 'trim');
			foreach ( $ids as $id ){
				$args = array(
					'post_type' => 'product',
					'meta_query' => array(
						array(
							'key' => '_sku',
							'value' => $id,						
						)
					)
				);			
				$query = new WP_Query( $args );
				
				if ( $query->have_posts() ) $crosssells[] = $query->post->ID;

				wp_reset_postdata();
			}								
			
			$this->pushmeta($pid, '_crosssell_ids', $crosssells);	

		} else {
			if ($is_new_product or $this->is_update_cf('_crosssell_ids')) delete_post_meta( $pid, '_crosssell_ids' );
		}

		// Downloadable options
		if ( $is_downloadable == 'yes' ) {

			$_download_limit = absint( $product_download_limit[$i] );
			if ( ! $_download_limit )
				$_download_limit = ''; // 0 or blank = unlimited

			$_download_expiry = absint( $product_download_expiry[$i] );
			if ( ! $_download_expiry )
				$_download_expiry = ''; // 0 or blank = unlimited
			
			// file paths will be stored in an array keyed off md5(file path)
			if ( !empty( $product_files[$i] ) ) {
				$_file_paths = array();
				
				$file_paths = explode( $this->options['product_files_delim'] , $product_files[$i] );
				$file_names = explode( $this->options['product_files_names_delim'] , $product_files_names[$i] );

				foreach ( $file_paths as $fn => $file_path ) {
					$file_path = trim( $file_path );					
					$_file_paths[ md5( $file_path ) ] = array('name' => ((!empty($file_names[$fn])) ? $file_names[$fn] : basename($file_path)), 'file' => $file_path);
				}								

				$this->pushmeta($pid, '_downloadable_files', $_file_paths);	

			}
			if ( isset( $product_download_limit[$i] ) )
				$this->pushmeta($pid, '_download_limit', esc_attr( $_download_limit ));	

			if ( isset( $product_download_expiry[$i] ) )
				$this->pushmeta($pid, '_download_expiry', esc_attr( $_download_expiry ));	
				
			if ( isset( $product_download_type[$i] ) )
				$this->pushmeta($pid, '_download_type', esc_attr( $product_download_type ));	
				
		}

		// Product url
		if ( $product_type == 'external' ) {
			if ( isset( $product_url[$i] ) && $product_url[$i] ){							
				$this->auto_cloak_links($import, $product_url[$i]);										
				$this->pushmeta($pid, '_product_url', esc_attr( $product_url[$i] ));					
			}
			if ( isset( $product_button_text[$i] ) && $product_button_text[$i] ){
				$this->pushmeta($pid, '_button_text', esc_attr( $product_button_text[$i] ));						
			}
		}			

		// prepare bulk SQL query
		//$this->executeSQL();

		wc_delete_product_transients($pid);
		
		// VARIATIONS
		if ( in_array($product_type, array('variation', 'variable')) and ! $this->options['link_all_variations'] and "xml" != $import->options['matching_parent'] ){												

			$set_defaults = false;

			$product_parent_post_id = false;			
				
			//[search parent product]
			$first_is_parent = "yes";												
										
			if ("manual" != $this->options['duplicate_matching']){					
				
				// find corresponding article among previously imported
				$postRecord = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM " . $this->wpdb->prefix . "pmxi_posts WHERE `import_id` = %d AND `product_key` = %s ORDER BY post_id ASC", $import->id, $single_product_parent_ID[$i]));

				$product_parent_post = ( ! empty($postRecord) ) ? get_post($product_parent_post_id = $postRecord->post_id) : false;
			
			}
			else{											

				if ($articleData['post_type'] == 'product'){

					$args = array(
						'post_type' => 'product_variation',
						'meta_query' => array(
							array(
								'key' => '_sku',
								'value' => get_post_meta($pid, '_sku', true),						
							)
						)
					);			
					$query = new WP_Query( $args );													

					if ( $query->have_posts() ){ 

						$duplicate_id = $query->post->ID;

						if ($duplicate_id) {				

							$product_parent_post = get_post($product_parent_post_id = $pid);															

							$pid = $duplicate_id;

							$this->duplicate_post_meta($pid, $product_parent_post_id);																
							
							$tmp = get_post_meta( $product_parent_post_id, '_stock', true);										
							$this->pushmeta($product_parent_post_id, '_stock_tmp', $tmp);	
							if ( empty($import->options['set_parent_stock']) ) 
								$this->pushmeta($product_parent_post_id, '_stock', '');	
								
							$tmp = get_post_meta( $product_parent_post_id, '_regular_price', true);										
							$this->pushmeta($product_parent_post_id, '_regular_price_tmp', $tmp);	
							$this->pushmeta($product_parent_post_id, '_regular_price', '');	

							$tmp = get_post_meta( $product_parent_post_id, '_price', true);										
							$this->pushmeta($product_parent_post_id, '_price_tmp', $tmp);	
							$this->pushmeta($product_parent_post_id, '_price', '');																											

						}

					}
					else{							

						$tmp = get_post_meta( $pid, '_stock', true);										
						$this->pushmeta($pid, '_stock_tmp', $tmp);	
						if ( empty($import->options['set_parent_stock']) ) 
							$this->pushmeta($pid, '_stock', '');									
						
						$tmp = get_post_meta( $pid, '_regular_price', true);										
						$this->pushmeta($pid, '_regular_price_tmp', $tmp);	
						$this->pushmeta($pid, '_regular_price', '');	

						$tmp = get_post_meta( $pid, '_price', true);										
						$this->pushmeta($pid, '_price_tmp', $tmp);	
						$this->pushmeta($pid, '_price', '');	

					}

					wp_reset_postdata();
					
				}
				elseif ($articleData['post_type'] == 'product_variation'){
					$variation_post = get_post($pid);
					$product_parent_post = get_post($product_parent_post_id = $variation_post->post_parent);							
				}
				
			}												

			$first_is_parent = ( in_array($import->options['matching_parent'], array("auto", "first_is_parent_title")) ) ? "yes" : "no";																
			//[\search parent product]

			if ( ! empty($product_parent_post_id) and ($product_parent_post_id != $pid or ($product_parent_post_id == $pid and $first_is_parent == "no")) ) {		

				$create_new_variation = ($product_parent_post_id == $pid and $first_is_parent == "no") ? true : false;							

				if ( $create_new_variation ){

					$postRecord = new PMXI_Post_Record();
					
					$postRecord->clear();
					
					// find corresponding article among previously imported
					$postRecord->getBy(array(
						'unique_key' => 'Variation ' . $new_sku,
						'import_id' => $import->id,
					));
					
					$pid = ( ! $postRecord->isEmpty() ) ? $postRecord->post_id : false;
						
				}

				$variable_enabled = ($product_enabled[$i] == "yes") ? 'yes' : 'no'; 

				$attributes = array(); 

				// Enabled or disabled
				$post_status = ( $variable_enabled == 'yes' ) ? 'publish' : 'private';

				// Generate a useful post title
				if ("manual" != $this->options['duplicate_matching']){
					$variation_post_title = sprintf( __( 'Variation #%s of %s', 'woocommerce' ), absint( $pid ), $articleData['post_title'] );
				}
				else{
					$variation_post_title = $articleData['post_title'];
				}				

				// Update or Add post							
				$variation = array(
					'post_title' 	=> $variation_post_title,
					'post_content' 	=> '',
					'post_status' 	=> $post_status,
					'post_parent' 	=> $product_parent_post_id,
					'post_type' 	=> 'product_variation'									
				);

				if ( ! $pid ) {

					if ($import->options['create_new_records']){
						
						$pid = wp_insert_post( $variation );	

						if ($create_new_variation){															
							
							$this->duplicate_post_meta($pid, $product_parent_post_id);

							// associate variation with import
							$postRecord->isEmpty() and $postRecord->set(array(
								'post_id' => $pid,
								'import_id' => $import->id,
								'unique_key' => 'Variation ' . $new_sku,
								'product_key' => ''
							))->insert();

							$postRecord->set(array('iteration' => $import->iteration))->update();

						}
						
					}				

				} else {

					if ($create_new_variation) {							
						
						$this->duplicate_post_meta($pid, $product_parent_post_id);										

						$postRecord->set(array('iteration' => $import->iteration))->update();

					}

					$this->wpdb->update( $this->wpdb->posts, $variation, array( 'ID' => $pid ) );				

				}								

				if ($pid){

					if ( $first_is_parent == "no" ){

						// Stock Data
						if ( strtolower($v_product_manage_stock[$i]) == 'yes' ) {

							// Manage stock
							$this->pushmeta($pid, '_manage_stock', 'yes');	
							$this->pushmeta($pid, '_stock_status', stripslashes( $v_stock_status[$i] ));
							$this->pushmeta($pid, '_stock', (int) $v_stock[$i]);															
							
						} else {

							$this->pushmeta($pid, '_manage_stock', 'no');
							$this->pushmeta($pid, '_stock_status', stripslashes( $v_stock_status[$i] ));
							$this->is_update_cf('_backorders') and delete_post_meta( $pid, '_backorders' );
							$this->is_update_cf('_stock') and delete_post_meta( $pid, '_stock' );
													
						}

					}

					if (empty($articleData['ID']) or $this->is_update_cf('_tax_class'))
					{
						if ( $product_tax_class[ $i ] !== 'parent' )
							$this->pushmeta($pid, '_tax_class', sanitize_text_field( $product_tax_class[ $i ] ));										
						else
							delete_post_meta( $pid, '_tax_class' );
					}

					if ( $is_downloadable == 'yes' ) {
						$this->pushmeta($pid, '_download_limit', sanitize_text_field( $product_download_limit[ $i ] ));	
						$this->pushmeta($pid, '_download_expiry', sanitize_text_field( $product_download_expiry[ $i ] ));	
						$this->pushmeta($pid, '_download_type', sanitize_text_field( $product_download_type[ $i ] ));									

						$_file_paths = array();
						
						if ( !empty($product_files[$i]) ) {
							$file_paths = explode( $import->options['product_files_delim'] , $product_files[$i] );
							$file_names = explode( $import->options['product_files_names_delim'] , $product_files_names[$i] );

							foreach ( $file_paths as $fn => $file_path ) {
								$file_path = sanitize_text_field( $file_path );							
								$_file_paths[ md5( $file_path ) ] = array('name' => ((!empty($file_names[$fn])) ? $file_names[$fn] : basename($file_path)), 'file' => $file_path);
							}
						}

						$this->pushmeta($pid, '_downloadable_files', $_file_paths);									

					} else {
						$this->pushmeta($pid, '_download_limit', '');	
						$this->pushmeta($pid, '_download_expiry', '');	
						$this->pushmeta($pid, '_download_type', '');	
						$this->pushmeta($pid, '_downloadable_files', '');									
					}

					wp_set_object_terms( $pid, NULL, 'product_type' );

					// Remove old taxonomies attributes so data is kept up to date
					if ( $pid and ($import->options['update_all_data'] == "yes" or ( $import->options['update_all_data'] == "no" and $import->options['is_update_attributes']))) {
						// Update all Attributes
						if ( $import->options['update_all_data'] == "yes" or $import->options['update_attributes_logic'] == 'full_update' ) 
							$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->wpdb->postmeta} WHERE meta_key LIKE 'attribute_%%' AND post_id = %d;", $pid ) );					

						wp_cache_delete( $pid, 'post_meta');
					}										

				}

				// Update taxonomies
				if ( $import->options['update_all_data'] == "yes" or ( $import->options['update_all_data'] == "no" and $import->options['is_update_attributes']) or $is_new_product){
					
					$attr_names = array();

					foreach ($serialized_attributes as $anum => $attr_data) {

						$attr_name = strtolower($attr_data['names'][$i]);

						if (empty($attr_name) or in_array($attr_name, $attr_names)) continue;
								
						$attr_names[] = $attr_name;

						// Update only these Attributes, leave the rest alone
						if ( $import->options['update_all_data'] == "no" and $import->options['is_update_attributes'] and $import->options['update_attributes_logic'] == 'only'){
							if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])){
								if ( ! in_array( ( (intval($attr_data['in_taxonomy'][$i])) ? wc_attribute_taxonomy_name( $attr_name ) : $attr_name ) , array_filter($import->options['attributes_list'], 'trim'))) continue;
							}
							else break;								
						}	

						// Leave these attributes alone, update all other Attributes
						if ( $import->options['update_all_data'] == "no" and $import->options['is_update_attributes'] and $import->options['update_attributes_logic'] == 'all_except'){
							if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) {
								if ( in_array( ( ($is_taxonomy) ? wc_attribute_taxonomy_name( $attr_name ) : $attr_name ) , array_filter($import->options['attributes_list'], 'trim'))) continue;									
							}
						}						

						if ( intval($attr_data['in_taxonomy'][$i]) and ( strpos($attr_name, "pa_") === false or strpos($attr_name, "pa_") !== 0 ) ) $attr_name = "pa_" . $attr_name;	

						$is_variation 	= intval( $attr_data['in_variation'][$i]);													

						if ($is_variation){
							
							// Don't use woocommerce_clean as it destroys sanitized characters																								
							$values = (intval($attr_data['in_taxonomy'][$i])) ? $attr_data['value'][$i] : $attr_data['value'][$i];	
							
							if (intval($attr_data['in_taxonomy'][$i])){
								
								$cname = wc_attribute_taxonomy_name( preg_replace("%^pa_%", "", $attr_name) );

								$term = term_exists($values, $cname, 0);

								if ( empty($term) and !is_wp_error($term) ){																																
									$term = term_exists(htmlspecialchars($values), $cname, 0);																
								}															
								if ( ! empty($term) and ! is_wp_error($term) ){	
									$term = get_term_by('id', $term['term_id'], $cname);									
									if ( ! empty($term) and ! is_wp_error($term) )
										$this->pushmeta($pid, 'attribute_' . sanitize_title( $attr_name ), $term->slug);																					
								}
								else{
									$this->pushmeta($pid, 'attribute_' . sanitize_title( $attr_name ), '');																					
								}

							} else {
								$this->pushmeta($pid, 'attribute_' . sanitize_title( $attr_name ), $values);																											
							}	
								
						}						
						else{
							delete_post_meta($pid, 'attribute_' . sanitize_title( $attr_name ));
						}
					}							
				}					

				$this->pmwi_buf_prices($product_parent_post_id);						
											
				if ($product_parent_post_id) wc_delete_product_transients($product_parent_post_id);									
								
			}						

			$previousID = get_option('wp_all_import_' . $import->id . '_parent_product');						

			// [execute only for parent products]

			if ( ! empty($previousID) and ( empty($product_parent_post_id) or $product_parent_post_id != $previousID or ! isset($product_types[$i + 1])) ){				

				$parent_product_ids = array($previousID);
				
				if ( ! isset($product_types[$i + 1]) and ! in_array($product_parent_post_id, $parent_product_ids)) $parent_product_ids[] = $product_parent_post_id;

				foreach ($parent_product_ids as $post_parent) {													

					$children = get_posts( array(
						'post_parent' 	=> $post_parent,
						'posts_per_page'=> -1,
						'post_type' 	=> 'product_variation',
						'fields' 		=> 'ids',
						'orderby'		=> 'ID',
						'order'			=> 'ASC',
						'post_status'	=> array('draft', 'publish', 'trash', 'pending', 'future', 'private')
					) );			
					
					if (count($children)){

						wp_set_object_terms( $post_parent, 'variable', 'product_type' );

						$lowest_price = $lowest_regular_price = $lowest_sale_price = $highest_price = $highest_regular_price = $highest_sale_price = '';
						$lowest_price_id = $lowest_regular_price_id = $lowest_sale_price_id = $highest_price_id = $highest_regular_price_id = $highest_sale_price_id = '';						

						$total_stock = 0;

						if ( $children ) {
							foreach ( $children as $n => $child ) {

								if ($first_is_parent == "no" and !$n ){
									$parent_thumbnail_id = get_post_thumbnail_id( $post_parent );																														
									if ($parent_thumbnail_id) 
										update_post_meta($child, '_thumbnail_id', $parent_thumbnail_id);
								}
								$_variation_stock = get_post_meta($child, '_stock', true);

								$total_stock += (empty($_variation_stock)) ? 0 : $_variation_stock;

								$child_price 			= get_post_meta( $child, '_price', true );
								$child_regular_price 	= get_post_meta( $child, '_regular_price', true );
								$child_sale_price 		= get_post_meta( $child, '_sale_price', true );

								// Regular prices
								if ( empty( $lowest_regular_price ) ||  (float) $child_regular_price < (float) $lowest_regular_price ){
									$lowest_regular_price = $child_regular_price;
									$lowest_regular_price_id = $child;
								}

								if ( empty( $highest_regular_price ) || (float) $child_regular_price > (float) $highest_regular_price ){
									$highest_regular_price = $child_regular_price;
									$highest_regular_price_id = $child;
								}
								
								// Sale prices
								if ( $child_price == $child_sale_price ) {
									if ( $child_sale_price !== '' && ( ! is_numeric( $lowest_sale_price ) || (float) $child_sale_price < (float) $lowest_sale_price ) ){
										$lowest_sale_price = $child_sale_price;
										$lowest_sale_price_id = $child;
									}

									if ( $child_sale_price !== '' && ( ! is_numeric( $highest_sale_price ) || (float) $child_sale_price > (float) $highest_sale_price ) ){
										$highest_sale_price = $child_sale_price;
										$highest_sale_price_id = $child;
									}
								}
							}

					    	$lowest_price 	= $lowest_sale_price === '' || (float) $lowest_regular_price < (float) $lowest_sale_price ? $lowest_regular_price : $lowest_sale_price;
							$highest_price 	= $highest_sale_price === '' || (float) $highest_regular_price > (float) $highest_sale_price ? $highest_regular_price : $highest_sale_price;

							$lowest_price_id 	= $lowest_sale_price === '' || (float) $lowest_regular_price < (float) $lowest_sale_price ? $lowest_regular_price_id : $lowest_sale_price_id;
							$highest_price_id 	= $highest_sale_price === '' || (float) $highest_regular_price > (float) $highest_sale_price ? $highest_regular_price_id : $highest_sale_price_id;

						}


						$this->pushmeta($post_parent, '_stock_status', $total_stock ? 'instock' : 'outofstock');
						$this->pushmeta($post_parent, '_price', $lowest_price);		
						$this->pushmeta($post_parent, '_min_variation_price', $lowest_price);		
						$this->pushmeta($post_parent, '_max_variation_price', $highest_price);		
						$this->pushmeta($post_parent, '_min_variation_regular_price', $lowest_regular_price);		
						$this->pushmeta($post_parent, '_max_variation_regular_price', $highest_regular_price);		
						$this->pushmeta($post_parent, '_min_variation_sale_price', $lowest_sale_price);		
						$this->pushmeta($post_parent, '_max_variation_sale_price', $highest_sale_price);									

						$this->pushmeta($post_parent, '_min_price_variation_id', $lowest_price_id);
						$this->pushmeta($post_parent, '_max_price_variation_id', $highest_price_id);
						$this->pushmeta($post_parent, '_min_regular_price_variation_id', $lowest_regular_price_id);
						$this->pushmeta($post_parent, '_max_regular_price_variation_id', $highest_regular_price_id);
						$this->pushmeta($post_parent, '_min_sale_price_variation_id', $lowest_sale_price_id);
						$this->pushmeta($post_parent, '_max_sale_price_variation_id', $highest_sale_price_id);

						// Update default attribute options setting
						if ( $import->options['update_all_data'] == "yes" or ( $import->options['update_all_data'] == "no" and $import->options['is_update_attributes'] ) or $is_new_product ){
							
							$default_attributes = array();
							$parent_attributes  = array();
							$unique_attributes  = array();
							$attribute_position = 0;
							$is_update_attributes = true;

							foreach ( $children as $child ) {

								$child_attributes = (array) maybe_unserialize( get_post_meta( $child, '_product_attributes', true ) );

								foreach ($child_attributes as $attr) 
									if ( ! in_array($attr['name'], $unique_attributes) and $attr['is_variation']) {
										$attributes[] = $attr;
										$unique_attributes[] = $attr['name'];
									}
							}				

							foreach ( $attributes as $attribute ) {															

								$default_attributes[ sanitize_title($attribute['name']) ] = array();

								$values = array();

								foreach ( $children as $child ) {
									
									$value = array_map( 'stripslashes', array_map( 'strip_tags',  explode("|", trim( get_post_meta($child, 'attribute_'.sanitize_title($attribute['name']), true)))));
									
									if ( ! empty($value) ){
										//$this->pushmeta($child, 'attribute_' . sanitize_title( $attribute['name'] ), $value[0]);
										foreach ($value as $val) {
										 	if ( ! in_array($val, $values, true) )  $values[] = $val;
										} 
									}

									if ( $attribute['is_variation'] ) {							

										if (!empty($value) and empty($default_attributes[ $attribute['name'] ])){
											switch ($import->options['default_attributes_type']) {
												case 'instock':
													$is_instock = get_post_meta($child, '_stock_status', true);
													if ($is_instock == 'instock'){
														$default_attributes[ sanitize_title($attribute['name']) ] = sanitize_title((is_array($value)) ? $value[0] : $value);	
													}
													break;
												case 'first':
													$default_attributes[ sanitize_title($attribute['name']) ] = sanitize_title((is_array($value)) ? $value[0] : $value);
													break;
												
												default:
													# code...
													break;
											}
																							
										}
										
									}
								}								

								// Update only these Attributes, leave the rest alone
								if ($import->options['update_all_data'] == "no" and $import->options['is_update_attributes'] and $import->options['update_attributes_logic'] == 'only'){
									if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])){
										if ( ! in_array( $attribute['name'] , array_filter($import->options['attributes_list'], 'trim'))){ 
											$attribute_position++;		
											continue;
										}
									}
									else {
										$is_update_attributes = false;
										break;
									}
								}

								// Leave these attributes alone, update all other Attributes
								if ($import->options['update_all_data'] == "no" and $import->options['is_update_attributes'] and $import->options['update_attributes_logic'] == 'all_except'){
									if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) {
										if ( in_array( $attribute['name'] , array_filter($import->options['attributes_list'], 'trim'))){ 
											$attribute_position++;
											continue;
										}
									}
								}

								if ( $attribute['is_taxonomy'] ){
									
									if ( ! empty($values) ) {				 												

									 	// Remove empty items in the array
									 	$values = array_filter( $values, array($this, "filtering") );						 	

								 		$attr_values = array();						 		

								 		foreach ($values as $key => $value) {
								 			
								 			$term = term_exists($value, $attribute['name'], 0);	

								 			if ( empty($term) and !is_wp_error($term) ){																																
												$term = term_exists(htmlspecialchars($value), $attribute['name'], 0);	
												if ( empty($term) and !is_wp_error($term) and $attribute['is_create_taxonomy_terms']){													
													$term = wp_insert_term(
														$value, // the term 
													  	$attribute['name'] // the taxonomy										  	
													);													
												}
											}
											if ( ! is_wp_error($term) )												
												$attr_values[] = (int) $term['term_taxonomy_id']; 
								 			
								 		}

								 		$values = $attr_values;
								 		$values = array_map( 'intval', $values );
										$values = array_unique( $values );

								 	} else {
								 		$values = array();
								 	}

							 		// Update post terms
							 		if ( $values and taxonomy_exists( $attribute['name'] ) )
							 			$this->associate_terms( $post_parent, $values, $attribute['name'] );									 			

							 		//do_action('wpai_parent_set_object_terms', $post_parent, $attribute['name']);

							 		if ( $values ) {
							 			
								 		// Add attribute to array, but don't set values
								 		$parent_attributes[ sanitize_title( $attribute['name'] ) ] = array(
									 		'name' 			=> $attribute['name'],
									 		'value' 		=> '',
									 		'position' 		=> $attribute_position,
									 		'is_visible' 	=> $attribute['is_visible'],
									 		'is_variation' 	=> $attribute['is_variation'],
									 		'is_taxonomy' 	=> 1,
									 		'is_create_taxonomy_terms' => $attribute['is_create_taxonomy_terms'],
									 	);
								 	
								 	}						 	

								}
								else
								{
									if (!empty($values)){
										$parent_attributes[ sanitize_title( $attribute['name'] ) ] = array(
									 		'name' 			=> sanitize_text_field( $attribute['name'] ),
									 		'value' 		=> implode('|', $values),
									 		'position' 		=> $attribute_position,
									 		'is_visible' 	=> $attribute['is_visible'],
									 		'is_variation' 	=> $attribute['is_variation'],
									 		'is_taxonomy' 	=> 0
									 	);
									}
								}

							 	$attribute_position++;		
							}				
							
							if ($import->options['is_default_attributes'] and $is_update_attributes) $this->pushmeta($post_parent, '_default_attributes', $default_attributes);

							if (empty($articleData['ID']) or $is_update_attributes){ 
								
								$current_product_attributes = get_post_meta($post_parent, '_product_attributes', true);						
								
								update_post_meta($post_parent, '_product_attributes', (( ! empty($current_product_attributes)) ? array_merge($current_product_attributes, $parent_attributes) : $parent_attributes));
								
							}			

							if ( $this->options['make_simple_product'] ) {
								$product_attributes = get_post_meta($post_parent, '_product_attributes', true);		
								if ( empty($product_attributes) ){
									wp_set_object_terms( $post_parent, 'simple', 'product_type' );
									$this->pmwi_update_prices( $post_parent );												
								}
							}

						}

						if (count($children) == 1 and $this->options['make_simple_product']){
							wp_set_object_terms( $post_parent, 'simple', 'product_type' );
							$this->pmwi_update_prices( $post_parent );							
						}

						if ( ! isset($product_types[$i + 1]) )
							delete_option('wp_all_import_' . $import->id . '_parent_product');

					} 
					elseif ( $this->options['make_simple_product'] ) {
						wp_set_object_terms( $post_parent, 'simple', 'product_type' );
						$this->pmwi_update_prices( $post_parent );							
					}

					wc_delete_product_transients($post_parent);		

					do_action('wp_all_import_variable_product_imported', $post_parent);

				}
			}
			// \[execute only for parent products]

			update_option('wp_all_import_' . $import->id . '_parent_product', $product_parent_post_id ? $product_parent_post_id : $pid);

		} elseif ( in_array( $product_type, array( 'variable' ) ) ){

			// Link All Variations
			if ( "variable" == $product_type and $this->options['link_all_variations'] and $this->options['update_all_data'] == "yes" or ($this->options['update_all_data'] == "no" and $this->options['is_update_attributes']) or $is_new_product){

				$added_variations = $this->pmwi_link_all_variations($pid, $this->options);

				$logger and call_user_func($logger, sprintf(__('<b>CREATED</b>: %s variations for parent product %s.', 'pmxi_plugin'), $added_variations, $articleData['post_title']));	

			}

			// Variable products have no prices		
			$this->pmwi_buf_prices($pid);

		}

		if ( in_array( $product_type, array( 'grouped' ) ) ){
			$this->pushmeta($pid, '_regular_price', '');
			$this->pushmeta($pid, '_sale_price', '');
			$this->pushmeta($pid, '_sale_price_dates_from', '');
			$this->pushmeta($pid, '_sale_price_dates_to', '');
			$this->pushmeta($pid, '_price', '');	
		}

		//$this->executeSQL();			

		// Find children elements by XPath and create variations
		if ( "variable" == $product_type and "xml" == $import->options['matching_parent'] and "" != $import->options['variations_xpath'] and "" != $import->options['variable_sku'] and ! $import->options['link_all_variations']) {
			
			$logger and call_user_func($logger, __('- Importing Variations', 'pmxi_plugin'));

			$variation_xpath = $cxpath . '[' . ( $i + 1 ) . ']/'.  ltrim(trim(str_replace("[*]", "", $import->options['variations_xpath']),'{}'), '/');
			
			$records = array();

			$variation_sku = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_sku'], $file)->parse($records); $tmp_files[] = $file;
			$count_variations = count($variation_sku);			

			if ( $count_variations > 0 ){				

				// Composing product is Manage stock									
				if ($import->options['is_variable_product_manage_stock'] == 'xpath' and "" != $import->options['single_variable_product_manage_stock']){
					if ($import->options['single_variable_product_manage_stock_use_parent']){
						$parent_variable_product_manage_stock = XmlImportParser::factory($xml, $cxpath, $import->options['single_variable_product_manage_stock'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_product_manage_stock = array_fill(0, count($variation_sku), $parent_variable_product_manage_stock[$i]);						
					}
					else {
						$variation_product_manage_stock = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_product_manage_stock'], $file)->parse($records); $tmp_files[] = $file;						
					}
				}
				else{
					count($variation_sku) and $variation_product_manage_stock = array_fill(0, count($variation_sku), $import->options['is_variable_product_manage_stock']);
				}

				// Stock Qty
				if ($import->options['variable_stock'] != ""){
					if ($import->options['variable_stock_use_parent']){
						$parent_variation_stock = XmlImportParser::factory($xml, $cxpath, $import->options['variable_stock'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_stock = array_fill(0, count($variation_sku), $parent_variation_stock[$i]);						
					}
					else {
						$variation_stock = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_stock'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_stock = array_fill(0, count($variation_sku), '');
				}

				// Stock Status
				if ($import->options['variable_stock_status'] == 'xpath' and "" != $import->options['single_variable_stock_status']){
					$variable_stock_status = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_stock_status'], $file)->parse($records); $tmp_files[] = $file;						
				}
				elseif($import->options['variable_stock_status'] == 'auto'){
					count($variation_sku) and $variable_stock_status = array_fill(0, count($variation_sku), $import->options['variable_stock_status']);
					foreach ($variation_stock as $key => $value) {
						$variable_stock_status[$key] = ( (int) $value <= 0) ? 'outofstock' : 'instock';
					}
				}
				else{
					count($variation_sku) and $variable_stock_status = array_fill(0, count($variation_sku), $import->options['variable_stock_status']);
				}

				// Image			
				$variation_image = array();				
				if ($import->options['variable_image']) {
					
					if ($import->options['variable_image_use_parent']){
						$parent_image = XmlImportParser::factory($xml, $cxpath, $import->options['variable_image'], $file)->parse($records); $tmp_files[] = $file;						
						count($variation_sku) and $variation_image = array_fill(0, count($variation_sku), $parent_image[$i]);						
					}
					else {
						$variation_image = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_image'], $file)->parse($records); $tmp_files[] = $file;	
					}					
					
				} else {
					count($variation_sku) and $variation_image = array_fill(0, count($variation_sku), '');
				}

				// Regular Price
				if (!empty($import->options['variable_regular_price'])){
					if ($import->options['variable_regular_price_use_parent']){
						$parent_regular_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['variable_regular_price'], $file)->parse($records)); $tmp_files[] = $file;
						count($variation_sku) and $variation_regular_price = array_fill(0, count($variation_sku), $parent_regular_price[$i]);						
					}
					else {
						$variation_regular_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_regular_price'], $file)->parse($records)); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_regular_price = array_fill(0, count($variation_sku), '');
				}

				// Sale Price
				if (!empty($import->options['variable_sale_price'])){
					if ($import->options['variable_sale_price_use_parent']){
						$parent_sale_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['variable_sale_price'], $file)->parse($records)); $tmp_files[] = $file;
						count($variation_sku) and $variation_sale_price = array_fill(0, count($variation_sku), $parent_sale_price[$i]);						
					}
					else {
						$variation_sale_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_sale_price'], $file)->parse($records)); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_sale_price = array_fill(0, count($variation_sku), '');
				}	

				// Who Sale Price
				if (!empty($import->options['variable_whosale_price'])){
					if ($import->options['variable_whosale_price_use_parent']){
						$parent_whosale_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['variable_whosale_price'], $file)->parse($records)); $tmp_files[] = $file;
						count($variation_sku) and $variation_whosale_price = array_fill(0, count($variation_sku), $parent_whosale_price[$i]);						
					}
					else {
						$variation_whosale_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_whosale_price'], $file)->parse($records)); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_whosale_price = array_fill(0, count($variation_sku), '');
				}	

				if ( $import->options['is_variable_sale_price_shedule']){
					// Sale price dates from
					if (!empty($import->options['variable_sale_price_dates_from'])){

						if ($import->options['variable_sale_dates_use_parent']){
							$parent_sale_date_start = XmlImportParser::factory($xml, $cxpath, $import->options['variable_sale_price_dates_from'], $file)->parse($records); $tmp_files[] = $file;
							count($variation_sku) and $variation_sale_price_dates_from = array_fill(0, count($variation_sku), $parent_sale_date_start[$i]);							
						}
						else {
							$variation_sale_price_dates_from = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_sale_price_dates_from'], $file)->parse($records); $tmp_files[] = $file;
						}
					}
					else{
						count($variation_sku) and $variation_sale_price_dates_from = array_fill(0, count($variation_sku), '');
					}

					// Sale price dates to
					if (!empty($import->options['variable_sale_price_dates_to'])){
						
						if ($import->options['variable_sale_dates_use_parent']){
							$parent_sale_date_end = XmlImportParser::factory($xml, $cxpath, $import->options['variable_sale_price_dates_to'], $file)->parse($records); $tmp_files[] = $file;
							count($variation_sku) and $variation_sale_price_dates_to = array_fill(0, count($variation_sku), $parent_sale_date_end[$i]);							
						}
						else {
							$variation_sale_price_dates_to = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_sale_price_dates_to'], $file)->parse($records); $tmp_files[] = $file;
						}						
					}
					else{
						count($variation_sku) and $variation_sale_price_dates_to = array_fill(0, count($variation_sku), '');
					}
				}			

				// Composing product is Virtual									
				if ($import->options['is_variable_product_virtual'] == 'xpath' and "" != $import->options['single_variable_product_virtual']){
					if ($import->options['single_variable_product_virtual_use_parent']){
						$parent_variable_product_virtual = XmlImportParser::factory($xml, $cxpath, $import->options['single_variable_product_virtual'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_product_virtual = array_fill(0, count($variation_sku), $parent_variable_product_virtual[$i]);						
					}
					else {
						$variation_product_virtual = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_product_virtual'], $file)->parse($records); $tmp_files[] = $file;						
					}
				}
				else{
					count($variation_sku) and $variation_product_virtual = array_fill(0, count($variation_sku), $import->options['is_variable_product_virtual']);
				}				

				// Composing product is Downloadable									
				if ($import->options['is_variable_product_downloadable'] == 'xpath' and "" != $import->options['single_variable_product_downloadable']){
					if ($import->options['single_variable_product_downloadable_use_parent']){
						$parent_variable_product_downloadable = XmlImportParser::factory($xml, $cxpath, $import->options['single_variable_product_downloadable'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_product_downloadable = array_fill(0, count($variation_sku), $parent_variable_product_downloadable[$i]);						
					}
					else {
						$variation_product_downloadable = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_product_downloadable'], $file)->parse($records); $tmp_files[] = $file;						
					}
				}
				else{
					count($variation_sku) and $variation_product_downloadable = array_fill(0, count($variation_sku), $import->options['is_variable_product_downloadable']);
				}

				// Weigth										
				if (!empty($import->options['variable_weight'])){
					if ($import->options['variable_weight_use_parent']){
						$parent_weight = XmlImportParser::factory($xml, $cxpath, $import->options['variable_weight'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_weight = array_fill(0, count($variation_sku), $parent_weight[$i]);						
					}
					else {
						$variation_weight = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_weight'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_weight = array_fill(0, count($variation_sku), '');
				}

				// Length										
				if (!empty($import->options['variable_length'])){
					if ($import->options['variable_dimensions_use_parent']){
						$parent_length = XmlImportParser::factory($xml, $cxpath, $import->options['variable_length'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_length = array_fill(0, count($variation_sku), $parent_length[$i]);						
					}
					else {
						$variation_length = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_length'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_length = array_fill(0, count($variation_sku), '');
				}

				// Width
				if (!empty($import->options['variable_width'])){
					if ($import->options['variable_dimensions_use_parent']){
						$parent_width = XmlImportParser::factory($xml, $cxpath, $import->options['variable_width'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_width = array_fill(0, count($variation_sku), $parent_width[$i]);						
					}
					else {
						$variation_width = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_width'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_width = array_fill(0, count($variation_sku), '');
				}

				// Heigth										
				if (!empty($import->options['variable_height'])){
					if ($import->options['variable_dimensions_use_parent']){
						$parent_heigth = XmlImportParser::factory($xml, $cxpath, $import->options['variable_height'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_height = array_fill(0, count($variation_sku), $parent_heigth[$i]);						
					}
					else {
						$variation_height = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_height'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_height = array_fill(0, count($variation_sku), '');
				}
				
				// Composing product Shipping Class				
				if ($import->options['is_multiple_variable_product_shipping_class'] != 'yes' and "" != $import->options['single_variable_product_shipping_class']){
					if ($import->options['single_variable_product_shipping_class_use_parent']){
						$parent_shipping_class = XmlImportParser::factory($xml, $cxpath, $import->options['single_variable_product_shipping_class'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_product_shipping_class = array_fill(0, count($variation_sku), $parent_shipping_class[$i]);						
					}
					else {
						$variation_product_shipping_class = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_product_shipping_class'], $file)->parse($records); $tmp_files[] = $file;						
					}
				}
				else{
					count($variation_sku) and $variation_product_shipping_class = array_fill(0, count($variation_sku), $import->options['multiple_variable_product_shipping_class']);
				}

				// Composing product Tax Class				
				if ($import->options['is_multiple_variable_product_tax_class'] != 'yes' and "" != $import->options['single_variable_product_tax_class']){
					if ($import->options['single_variable_product_tax_class_use_parent']){
						$parent_tax_class = XmlImportParser::factory($xml, $cxpath, $import->options['single_variable_product_tax_class'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_product_tax_class = array_fill(0, count($variation_sku), $parent_tax_class[$i]);						
					}
					else {
						$variation_product_tax_class = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_product_tax_class'], $file)->parse($records); $tmp_files[] = $file;						
					}
				}
				else{
					count($variation_sku) and $variation_product_tax_class = array_fill(0, count($variation_sku), $import->options['multiple_variable_product_tax_class']);
				}

				// Download limit										
				if (!empty($import->options['variable_download_limit'])){
					if ($import->options['variable_download_limit_use_parent']){
						$parent_download_limit = XmlImportParser::factory($xml, $cxpath, $import->options['variable_download_limit'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_download_limit = array_fill(0, count($variation_sku), $parent_download_limit[$i]);						
					}
					else {
						$variation_download_limit = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_download_limit'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_download_limit = array_fill(0, count($variation_sku), '');
				}

				// Download expiry										
				if (!empty($import->options['variable_download_expiry'])){
					if ($import->options['variable_download_expiry_use_parent']){
						$parent_download_expiry = XmlImportParser::factory($xml, $cxpath, $import->options['variable_download_expiry'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_download_expiry = array_fill(0, count($variation_sku), $parent_download_expiry[$i]);						
					}
					else {
						$variation_download_expiry = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_download_expiry'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_download_expiry = array_fill(0, count($variation_sku), '');
				}

				// File paths								
				if (!empty($import->options['variable_file_paths'])){
					$variation_file_paths = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_file_paths'], $file)->parse($records); $tmp_files[] = $file;
				}
				else{
					count($variation_sku) and $variation_file_paths = array_fill(0, count($variation_sku), '');
				}

				// File names								
				if (!empty($import->options['variable_file_names'])){
					$variation_file_names = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_file_names'], $file)->parse($records); $tmp_files[] = $file;
				}
				else{
					count($variation_sku) and $variation_file_names = array_fill(0, count($variation_sku), '');
				}

				// Variation enabled								
				if ($import->options['is_variable_product_enabled'] == 'xpath' and "" != $import->options['single_variable_product_enabled']){
					$variation_product_enabled = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_product_enabled'], $file)->parse($records); $tmp_files[] = $file;						
				}
				else{
					count($variation_sku) and $variation_product_enabled = array_fill(0, count($variation_sku), $import->options['is_variable_product_enabled']);
				}

				$variation_attribute_keys = array(); 
				$variation_attribute_values = array();	
				$variation_attribute_in_variation = array(); 
				$variation_attribute_is_visible = array();
				$variation_attribute_in_taxonomy = array();			
				$variable_create_terms_in_not_exists = array();
									
				if (!empty($import->options['variable_attribute_name'][0])){
					foreach ($import->options['variable_attribute_name'] as $j => $attribute_name) { if ($attribute_name == "") continue;						
						$variation_attribute_keys[$j]   = XmlImportParser::factory($xml, $variation_xpath, $attribute_name, $file)->parse($records); $tmp_files[] = $file;
						$variation_attribute_values[$j] = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_attribute_value'][$j], $file)->parse($records); $tmp_files[] = $file;
						$variation_attribute_in_variation[$j] = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_in_variations'][$j], $file)->parse($records); $tmp_files[] = $file;
						$variation_attribute_is_visible[$j] = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_is_visible'][$j], $file)->parse($records); $tmp_files[] = $file;						
						$variation_attribute_in_taxonomy[$j] = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_is_taxonomy'][$j], $file)->parse($records); $tmp_files[] = $file;						
						$variable_create_terms_in_not_exists[$j] = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_create_taxonomy_in_not_exists'][$j], $file)->parse($records); $tmp_files[] = $file;
					}
				}					

				// serialized attributes for product variations
				$variation_serialized_attributes = array();
				if (!empty($variation_attribute_keys)){
					foreach ($variation_attribute_keys as $j => $attribute_name) {											
						if (!in_array($attribute_name[0], array_keys($variation_serialized_attributes))){
							$variation_serialized_attributes[$attribute_name[0]] = array(
								'value' => $variation_attribute_values[$j],
								'is_visible' => $variation_attribute_is_visible[$j],
								'in_variation' => $variation_attribute_in_variation[$j],
								'in_taxonomy' => $variation_attribute_in_taxonomy[$j],
								'is_create_taxonomy_terms' => $variable_create_terms_in_not_exists[$j]
							);						
						}							
					}
				} 

				// Create Variations
				foreach ($variation_sku as $j => $void) {	if ("" == $variation_sku[$j]) continue;

					if ($import->options['variable_sku_add_parent']) $variation_sku[$j] = $product_sku[$i] . '-' . $variation_sku[$j];

					$variable_enabled = ($variation_product_enabled[$j] == "yes") ? 'yes' : 'no'; 					

					// Enabled or disabled
					$post_status = ( $variable_enabled == 'yes' ) ? 'publish' : 'private';
					$variation_to_update_id = false;					
					$postRecord = new PMXI_Post_Record();
					$postRecord->clear();																					
						
					// Generate a useful post title
					$variation_post_title = sprintf( __( 'Variation #%s of %s', 'woocommerce' ), $variation_sku[$j], $articleData['post_title'] );

					// handle duplicates according to import settings
					/*if ($duplicates = pmxi_findDuplicates(array('post_title' => $variation_post_title, 'post_type' => 'product_variation', 'post_parent' => $pid),'','','parent')) {															
						$duplicate_id = array_shift($duplicates);							
						if ($duplicate_id) {														
							$variation_to_update = get_post($variation_to_update_id = $duplicate_id);
						}						
					}	*/					

					// Update or Add post							

					$variation = array(
						'post_title' 	=> $variation_post_title,
						'post_content' 	=> '',
						'post_status' 	=> $post_status,									
						'post_parent' 	=> $pid,
						'post_type' 	=> 'product_variation'									
					);

					$variation_just_created = false;

					$postRecord->getBy(array(
						'unique_key' => 'Variation ' . $variation_sku[$j] . ' of ' . $pid,
						'import_id' => $import->id
					));
					if ( ! $postRecord->isEmpty() ){
						$variation_to_update_id = $postRecord->post_id;
						$postRecord->set(array('iteration' => $import->iteration))->update();											
					}

					if ( ! $variation_to_update_id ) {

						$variation_to_update_id = wp_insert_post( $variation );		

						// associate variation with import
						$postRecord->isEmpty() and $postRecord->set(array(
							'post_id' => $variation_to_update_id,
							'import_id' => $import->id,
							'unique_key' => 'Variation ' . $variation_sku[$j] . ' of ' . $pid,
							'product_key' => ''
						))->insert();	

						$postRecord->set(array('iteration' => $import->iteration))->update();

						$variation_just_created = true;		

						$logger and call_user_func($logger, sprintf(__('- `%s`: variation created successfully', 'pmxi_plugin'), sprintf( __( 'Variation #%s of %s', 'woocommerce' ), absint( $variation_to_update_id ), esc_html( get_the_title( $pid ) ) )));

					} else {						
							
						$this->wpdb->update( $this->wpdb->posts, $variation, array( 'ID' => $variation_to_update_id ) );
						//do_action( 'woocommerce_update_product_variation', $variation_to_update_id );
						$logger and call_user_func($logger, sprintf(__('- `%s`: variation updated successfully', 'pmxi_plugin'), $variation_post_title));
						
					}		

					do_action( 'pmxi_update_product_variation', $variation_to_update_id );								

					$existing_variation_meta_keys = array();
					foreach (get_post_meta($variation_to_update_id, '') as $cur_meta_key => $cur_meta_val) $existing_variation_meta_keys[] = $cur_meta_key;

					// delete keys which are no longer correspond to import settings																
					if ( !empty($existing_variation_meta_keys) ) 

						foreach ($existing_variation_meta_keys as $cur_meta_key) { 
						
							// Do not delete post meta for features image 
							if ( in_array($cur_meta_key, array('_thumbnail_id','_product_image_gallery')) ) continue;

							// Update all data
							if ($import->options['update_all_data'] == 'yes') {
								delete_post_meta($variation_to_update_id, $cur_meta_key);
								continue;
							}
							
							// Do not update attributes
							if ( ! $import->options['is_update_attributes'] and (in_array($cur_meta_key, array('_default_attributes', '_product_attributes')) or strpos($cur_meta_key, "attribute_") === 0)) continue;
							
							// Update only these Attributes, leave the rest alone
							if ($import->options['is_update_attributes'] and $import->options['update_attributes_logic'] == 'only'){
								
								if ($cur_meta_key == '_product_attributes'){
									$current_product_attributes = get_post_meta($variation_to_update_id, '_product_attributes', true);
									if ( ! empty($current_product_attributes) and ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) 
										foreach ($current_product_attributes as $attr_name => $attr_value) {
											if ( in_array($attr_name, array_filter($import->options['attributes_list'], 'trim'))) unset($current_product_attributes[$attr_name]);
										}
										
									update_post_meta($variation_to_update_id, '_product_attributes', $current_product_attributes);
									continue;
								}

								if ( strpos($cur_meta_key, "attribute_") === 0 and ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list']) and ! in_array(str_replace("attribute_", "", $cur_meta_key), array_filter($import->options['attributes_list'], 'trim'))) continue;

								if (in_array($cur_meta_key, array('_default_attributes'))) continue;
							}

							// Leave these attributes alone, update all other Attributes
							if ($import->options['is_update_attributes'] and $import->options['update_attributes_logic'] == 'all_except'){
								
								if ($cur_meta_key == '_product_attributes'){
									
									if (empty($import->options['attributes_list'])) { delete_post_meta($variation_to_update_id, $cur_meta_key); continue; }

									$current_product_attributes = get_post_meta($variation_to_update_id, '_product_attributes', true);
									if ( ! empty($current_product_attributes) and ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) 
										foreach ($current_product_attributes as $attr_name => $attr_value) {
											if ( ! in_array($attr_name, array_filter($import->options['attributes_list'], 'trim'))) unset($current_product_attributes[$attr_name]);
										}
										
									update_post_meta($variation_to_update_id, '_product_attributes', $current_product_attributes);
									continue;
								}

								if ( strpos($cur_meta_key, "attribute_") === 0 and ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list']) and in_array(str_replace("attribute_", "", $cur_meta_key), array_filter($import->options['attributes_list'], 'trim'))) continue;

								if (in_array($cur_meta_key, array('_default_attributes'))) continue;
							}

							// Update all Custom Fields is defined
							if ($import->options['update_custom_fields_logic'] == "full_update"){
								delete_post_meta($variation_to_update_id, $cur_meta_key);								
							}
							// Update only these Custom Fields, leave the rest alone
							elseif ($import->options['update_custom_fields_logic'] == "only"){
								if ( ! empty($import->options['custom_fields_list']) and is_array($import->options['custom_fields_list']) and in_array($cur_meta_key, $import->options['custom_fields_list'])) delete_post_meta($variation_to_update_id, $cur_meta_key);
							}
							// Leave these fields alone, update all other Custom Fields
							elseif ($import->options['update_custom_fields_logic'] == "all_except"){
								if ( empty($import->options['custom_fields_list']) or ! in_array($cur_meta_key, $import->options['custom_fields_list'])) delete_post_meta($variation_to_update_id, $cur_meta_key);
							}
						}

					// Add any default post meta
					add_post_meta( $variation_to_update_id, 'total_sales', '0', true );
					
					// Product type + Downloadable/Virtual
					wp_set_object_terms( $variation_to_update_id, NULL, 'product_type' );
					update_post_meta( $variation_to_update_id, '_downloadable', ($variation_product_downloadable[$j] == "yes") ? 'yes' : 'no' );
					update_post_meta( $variation_to_update_id, '_virtual', ($variation_product_virtual[$j] == "yes") ? 'yes' : 'no' );						

					// Update post meta
					if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_regular_price')) update_post_meta( $variation_to_update_id, '_regular_price', stripslashes( $variation_regular_price[$j] ) );
					if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_sale_price')) update_post_meta( $variation_to_update_id, '_sale_price', stripslashes( $variation_sale_price[$j] ) );
					if ( class_exists('woocommerce_wholesale_pricing') ) update_post_meta( $variation_to_update_id, 'pmxi_wholesale_price', stripslashes( $variation_whosale_price[$j] ) );

					// Dimensions
					if ( $variation_product_virtual[$j] == 'no' ) {
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_weight')) update_post_meta( $variation_to_update_id, '_weight', stripslashes( $variation_weight[$j] ) );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_length')) update_post_meta( $variation_to_update_id, '_length', stripslashes( $variation_length[$j] ) );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_width')) update_post_meta( $variation_to_update_id, '_width', stripslashes( $variation_width[$j] ) );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_height')) update_post_meta( $variation_to_update_id, '_height', stripslashes( $variation_height[$j] ) );
					} else {
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_weight')) update_post_meta( $variation_to_update_id, '_weight', '' );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_length')) update_post_meta( $variation_to_update_id, '_length', '' );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_width')) update_post_meta( $variation_to_update_id, '_width', '' );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_height')) update_post_meta( $variation_to_update_id, '_height', '' );
					}															
					
					// Save shipping class		
					if (ctype_digit($variation_product_shipping_class[ $j ])){

						$v_shipping_class = $variation_product_shipping_class[ $j ] > 0 ? absint( $variation_product_shipping_class[ $j ] ) : '';			

					}
					else{

						$vt_shipping_class = term_exists($variation_product_shipping_class[ $j ], 'product_shipping_class', 0);	
						if ( empty($vt_shipping_class) and !is_wp_error($vt_shipping_class) ){																																
							$vt_shipping_class = term_exists(htmlspecialchars($variation_product_shipping_class[ $j ]), 'product_shipping_class', 0);						
						}
						if ( ! is_wp_error($vt_shipping_class) )												
							$v_shipping_class = (int) $vt_shipping_class['term_id']; 				
					}
					
					wp_set_object_terms( $variation_to_update_id, $v_shipping_class, 'product_shipping_class');					

					// Unique SKU
					$sku				= get_post_meta($variation_to_update_id, '_sku', true);
					$new_sku 			= esc_html( trim( stripslashes( $variation_sku[$j] ) ) );
					
					if ( $new_sku == '' and $import->options['disable_auto_sku_generation'] ) {
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_sku')) 				
								update_post_meta( $variation_to_update_id, '_sku', '' );
					}
					elseif ( $new_sku == '' and ! $import->options['disable_auto_sku_generation'] ) {
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_sku')){				
							
							$new_sku = substr(md5($variation_post_title), 0, 12);
						}
					}

					if ( $new_sku == '' ) {
						update_post_meta( $variation_to_update_id, '_sku', '' );
					} elseif ( $new_sku !== $sku ) {
						if ( ! empty( $new_sku ) ) {
							if ( ! $import->options['disable_sku_matching']  and 
								$this->wpdb->get_var( $this->wpdb->prepare("
									SELECT ".$this->wpdb->posts.".ID
								    FROM ".$this->wpdb->posts."
								    LEFT JOIN ".$this->wpdb->postmeta." ON (".$this->wpdb->posts.".ID = ".$this->wpdb->postmeta.".post_id)
								    WHERE ".$this->wpdb->posts.".post_type = 'product'
								    AND ".$this->wpdb->posts.".post_status = 'publish'
								    AND ".$this->wpdb->postmeta.".meta_key = '_sku' AND ".$this->wpdb->postmeta.".meta_value = '%s'
								 ", $new_sku ) )
								) {
								$logger and call_user_func($logger, sprintf(__('- <b>WARNING</b>: Product SKU must be unique.', 'pmxi_plugin')));							
								
							} else {
								update_post_meta( $variation_to_update_id, '_sku', $new_sku );
							}
						} else {
							update_post_meta( $variation_to_update_id, '_sku', '' );
						}
					}

					$date_from = isset( $variation_sale_price_dates_from[$j] ) ? $variation_sale_price_dates_from[$j] : '';
					$date_to = isset( $variation_sale_price_dates_to[$i] ) ? $variation_sale_price_dates_to[$i] : '';

					// Dates
					if ( $date_from )
						update_post_meta( $variation_to_update_id, '_sale_price_dates_from', strtotime( $date_from ) );
					else
						update_post_meta( $variation_to_update_id, '_sale_price_dates_from', '' );

					if ( $date_to )
						update_post_meta( $variation_to_update_id, '_sale_price_dates_to', strtotime( $date_to ) );
					else
						update_post_meta( $variation_to_update_id, '_sale_price_dates_to', '' );

					if ( $date_to && ! $date_from )
						update_post_meta( $variation_to_update_id, '_sale_price_dates_from', strtotime( 'NOW', current_time( 'timestamp' ) ) );

					// Update price if on sale
					if ( $variation_sale_price[$j] != '' && $date_to == '' && $date_from == '' ){
						if (empty($articleData['ID']) or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_price')) update_post_meta( $variation_to_update_id, '_price', stripslashes( $variation_sale_price[$j] ) );
					}
					else{
						if (empty($articleData['ID']) or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_price')) update_post_meta( $variation_to_update_id, '_price', stripslashes( $variation_regular_price[$j] ) );
					}

					if ( $variation_sale_price[$j] != '' && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) )
						update_post_meta( $variation_to_update_id, '_price', stripslashes($variation_sale_price[$j]) );

					if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
						if (empty($articleData['ID']) or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_price')) update_post_meta( $variation_to_update_id, '_price', stripslashes($variation_regular_price[$j]) );
						update_post_meta( $variation_to_update_id, '_sale_price_dates_from', '');
						update_post_meta( $variation_to_update_id, '_sale_price_dates_to', '');
					}				

					// Stock Data
					if ( strtolower($variation_product_manage_stock[$j]) == 'yes' ) {

						// Manage stock
						if (empty($articleData['ID']) or $this->is_update_cf('_manage_stock')) {
							update_post_meta( $variation_to_update_id, '_manage_stock', 'yes' );	
						}
						if (empty($articleData['ID']) or $this->is_update_cf('_stock_status')) {
							update_post_meta( $variation_to_update_id, '_stock_status', stripslashes( $variable_stock_status[$j] ) );	
						}
						if (empty($articleData['ID']) or $this->is_update_cf('_stock')) {
							update_post_meta( $variation_to_update_id, '_stock', (int) $variation_stock[$j] );
						}						
						
					} else {

						if (empty($articleData['ID']) or $this->is_update_cf('_manage_stock')) {
							update_post_meta( $variation_to_update_id, '_manage_stock', 'no' );	
						}
						if (empty($articleData['ID']) or $this->is_update_cf('_stock_status')) {
							update_post_meta( $variation_to_update_id, '_stock_status', stripslashes( $variable_stock_status[$j] ) );	
						}
						delete_post_meta( $variation_to_update_id, '_backorders' );
						delete_post_meta( $variation_to_update_id, '_stock' );
												
					}

					if ( $variation_product_tax_class[ $j ] !== 'parent' )
						update_post_meta( $variation_to_update_id, '_tax_class', sanitize_text_field( $variation_product_tax_class[ $j ] ) );
					else
						delete_post_meta( $variation_to_update_id, '_tax_class' );

					if ( $variation_product_downloadable[$j] == 'yes' ) {
						update_post_meta( $variation_to_update_id, '_download_limit', sanitize_text_field( $variation_download_limit[ $j ] ) );
						update_post_meta( $variation_to_update_id, '_download_expiry', sanitize_text_field( $variation_download_expiry[ $j ] ) );

						$_file_paths = array();
						
						if ( !empty($variation_file_paths[$j]) ) {
							$file_paths = explode( $import->options['variable_product_files_delim'] , $variation_file_paths[$j] );
							$file_names = explode( $import->options['variable_product_files_names_delim'] , $variation_file_names[$j] );

							foreach ( $file_paths as $fn => $file_path ) {
								$file_path = sanitize_text_field( $file_path );								
								$_file_paths[ md5( $file_path ) ] = array('name' => ((!empty($file_names[$fn])) ? $file_names[$fn] : basename($file_path)), 'file' => $file_path);
							}
						}

						// grant permission to any newly added files on any existing orders for this product						
						update_post_meta( $variation_to_update_id, '_downloadable_files', $_file_paths );
					} else {
						update_post_meta( $variation_to_update_id, '_download_limit', '' );
						update_post_meta( $variation_to_update_id, '_download_expiry', '' );
						update_post_meta( $variation_to_update_id, '_downloadable_files', '' );
						update_post_meta( $variation_to_update_id, '_download_type', '' );
					}

					// Remove old taxonomies attributes so data is kept up to date
					if ( $variation_to_update_id and ( $import->options['update_all_data'] == 'yes' or ($import->options['update_all_data'] == 'no' and $import->options['is_update_attributes']) or $variation_just_created) ) {
						if ($import->options['update_all_data'] == 'yes' or $import->options['update_attributes_logic'] == 'full_update' ) $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->wpdb->postmeta} WHERE meta_key LIKE 'attribute_%%' AND post_id = %d;", $variation_to_update_id ) );
						wp_cache_delete( $variation_to_update_id, 'post_meta');
					}

					// Update taxonomies
					if ( $import->options['update_all_data'] == 'yes' or ($import->options['update_all_data'] == 'no' and $import->options['is_update_attributes']) or $variation_just_created ){

						foreach ($variation_serialized_attributes as $a_name => $attr_data) {																										

							$attr_name = $a_name;

							if ( in_array( $attr_name, $this->reserved_terms ) ) {
								$attr_name .= 's';
							}

							// Update only these Attributes, leave the rest alone
							if ($import->options['update_all_data'] == 'no' and $import->options['update_attributes_logic'] == 'only'){
								if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])){
									if ( ! in_array( ( (intval($attr_data['in_taxonomy'][$j])) ? "pa_" . $attr_name : $attr_name ) , array_filter($import->options['attributes_list'], 'trim'))) continue;
								}
								else break;								
							}	

							// Leave these attributes alone, update all other Attributes
							if ($import->options['update_all_data'] == 'no' and $import->options['update_attributes_logic'] == 'all_except'){
								if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) {
									if ( in_array( ( (intval($attr_data['in_taxonomy'][$j])) ? wc_attribute_taxonomy_name( $attr_name ) : $attr_name ) , array_filter($import->options['attributes_list'], 'trim'))) continue;								
								}
							}	
															
							$is_variation 	= intval( $attr_data['in_variation'][$j]);													
								
							// Don't use woocommerce_clean as it destroys sanitized characters																								
							$values = (intval($attr_data['in_taxonomy'][$j])) ? $attr_data['value'][$j] : $attr_data['value'][$j];	
							
							if (intval($attr_data['in_taxonomy'][$j])){

								if (intval($attr_data['is_create_taxonomy_terms'][0])) $this->create_taxonomy($attr_name, $logger);

								$terms = get_terms( wc_attribute_taxonomy_name( preg_replace("%^pa_%", "", $attr_name) ), array('hide_empty' => false));		

								if ( ! is_wp_error($terms) ){
							 		
						 			$term_founded = false;	
									if ( count($terms) > 0 ){	
								    	foreach ( $terms as $term ) {									    										    										    	
									    	if ( strtolower($term->name) == trim(strtolower($values)) or $term->slug == sanitize_title(trim(strtolower($values))) ) {										    		
									    		update_post_meta( $variation_to_update_id, 'attribute_' . wc_attribute_taxonomy_name( $attr_name ), $term->slug );
									    		$term_founded = true;	
									    		break;
									    	}
									    }
									}	
								    if ( ! $term_founded and intval($attr_data['is_create_taxonomy_terms'][0]) ){
								    	$term = wp_insert_term(
											$values, // the term 
										  	wc_attribute_taxonomy_name( $attr_name ) // the taxonomy										  	
										);		
										if ( ! is_wp_error($term) ){
											$term = get_term_by( 'id', $term['term_id'], wc_attribute_taxonomy_name( $attr_name ));
											update_post_meta( $variation_to_update_id, 'attribute_' . wc_attribute_taxonomy_name( $attr_name ), $term->slug );
										}
								    }
							 		
							 	}
							 	else{
							 		$logger and call_user_func($logger, sprintf(__('- <b>WARNING</b>: %s.', 'pmxi_plugin'), $terms->get_error_message()));
							 	}

							} else {
								update_post_meta( $variation_to_update_id, 'attribute_' . sanitize_title( $attr_name ), $values );		
							}							
							
						}
					}					

					if ( ! is_array($variation_image[$j]) ) $variation_image[$j] = array($variation_image[$j]);

					$uploads = wp_upload_dir();

					if ( ! empty($uploads) and false === $uploads['error'] and !empty($variation_image[$j]) and (empty($articleData['ID']) or $import->options['update_all_data'] == "yes" or ( $import->options['update_all_data'] == "no" and $import->options['is_update_images']))) {

						$gallery_attachment_ids = array();	

						foreach ($variation_image[$j] as $featured_image)
						{							
							$imgs = explode(',', $featured_image);

							if (!empty($imgs)) {	

								foreach ($imgs as $img_url) { if (empty($img_url)) continue;	

									$url = str_replace(" ", "%20", trim($img_url));
									$bn = preg_replace('/[\\?|&].*/', '', basename($url));
									
									$img_ext = pmxi_getExtensionFromStr($url);									
									$default_extension = pmxi_getExtension($bn);																									

									if ($img_ext == "") 										
										$img_ext = pmxi_get_remote_image_ext($url);																			

									// generate local file name
									$image_name = urldecode(sanitize_file_name((($img_ext) ? str_replace("." . $default_extension, "", $bn) : $bn))) . (("" != $img_ext) ? '.' . $img_ext : '');																	
									
									// if wizard store image data to custom field									
									$create_image = false;
									$download_image = true;

									$image_filename = wp_unique_filename($uploads['path'], $image_name);
									$image_filepath = $uploads['path'] . '/' . $image_filename;
									
									// keep existing and add newest images
									if ( ! empty($articleData['ID']) and $import->options['is_update_images'] and $import->options['update_images_logic'] == "add_new" and $import->options['update_all_data'] == "no"){ 																																											
										
										$attachment_imgs = get_posts( array(
											'post_type' => 'attachment',
											'posts_per_page' => -1,
											'post_parent' => $variation_to_update_id,												
										) );

										if ( $attachment_imgs ) {
											foreach ( $attachment_imgs as $attachment_img ) {													
												if ($attachment_img->guid == $uploads['url'] . '/' . $image_name){
													$download_image = false;
													$success_images = true;
													
													set_post_thumbnail($variation_to_update_id, $attachment_img->ID);													
													$gallery_attachment_ids[] = $attachment_img->ID;	

													$logger and call_user_func($logger, sprintf(__('- <b>Image SKIPPED</b>: The image %s is always exists for the %s', 'pmxi_plugin'), basename($attachment_img->guid), $variation_post_title));							
												}
											}												
										}

									}

									if ($download_image){											

										// do not download images
										if ( ! $import->options['download_images'] or $import->options['variable_image_use_parent']){ 		

											$image_filename = $image_name;
											$image_filepath = $uploads['path'] . '/' . $image_filename;																							
											
											$existing_attachment = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM " . $this->wpdb->prefix ."posts WHERE guid = '%s'", $uploads['url'] . '/' . $image_filename ) );
											
											if ( ! empty($existing_attachment->ID) ){

												$download_image = false;	
												$create_image = false;	
												
												set_post_thumbnail($variation_to_update_id, $existing_attachment->ID); 																							
												$gallery_attachment_ids[] = $existing_attachment->ID;	

												do_action( 'pmxi_gallery_image', $variation_to_update_id, $existing_attachment->ID, $image_filepath); 

											}
											else{													
												
												if ( @file_exists($image_filepath) ){
													$download_image = false;																				
													if( ! ($image_info = @getimagesize($image_filepath)) or ! in_array($image_info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
														$logger and call_user_func($logger, sprintf(__('- <b>WARNING</b>: File %s is not a valid image and cannot be set as featured one', 'pmxi_plugin'), $image_filepath));														
														@unlink($image_filepath);
													} else {
														$create_image = true;											
													}
												}
											}											
										}	

										if ($download_image){
											
											$request = get_file_curl($url, $image_filepath);

											if ( (is_wp_error($request) or $request === false) and ! @file_put_contents($image_filepath, @file_get_contents($url))) {
												@unlink($image_filepath); // delete file since failed upload may result in empty file created
											} elseif( ($image_info = @getimagesize($image_filepath)) and in_array($image_info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
												$create_image = true;											
											}												
											
											if ( ! $create_image ){

												$url = str_replace(" ", "%20", trim(pmxi_convert_encoding($img_url)));
												
												$request = get_file_curl($url, $image_filepath);

												if ( (is_wp_error($request) or $request === false) and ! @file_put_contents($image_filepath, @file_get_contents($url))) {
													$logger and call_user_func($logger, sprintf(__('- <b>WARNING</b>: File %s cannot be saved locally as %s', 'pmxi_plugin'), $url, $image_filepath));													
													@unlink($image_filepath); // delete file since failed upload may result in empty file created										
												} elseif( ! ($image_info = @getimagesize($image_filepath)) or ! in_array($image_info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
													$logger and call_user_func($logger, sprintf(__('- <b>WARNING</b>: File %s is not a valid image and cannot be set as featured one', 'pmxi_plugin'), $url));													
													@unlink($image_filepath);
												} else {
													$create_image = true;											
												}
											}
										}
									}

									if ($create_image){

										// you must first include the image.php file
										// for the function wp_generate_attachment_metadata() to work
										require_once(ABSPATH . 'wp-admin/includes/image.php');	

										$attachment = array(
											'post_mime_type' => image_type_to_mime_type($image_info[2]),
											'guid' => $uploads['url'] . '/' . $image_filename,
											'post_title' => $image_filename,
											'post_content' => ''										
										);
										if (($image_meta = wp_read_image_metadata($image_filepath))) {
											if (trim($image_meta['title']) && ! is_numeric(sanitize_title($image_meta['title'])))
												$attachment['post_title'] = $image_meta['title'];
											if (trim($image_meta['caption']))
												$attachment['post_content'] = $image_meta['caption'];
										}

										$attid = wp_insert_attachment($attachment, $image_filepath, $variation_to_update_id);

										if (is_wp_error($attid)) {
											$logger and call_user_func($logger, __('- <b>WARNING</b>', 'pmxi_plugin') . ': ' . $attid->get_error_message());											
										} else {
																						
											wp_update_attachment_metadata($attid, wp_generate_attachment_metadata($attid, $image_filepath));																																										

											do_action( 'pmxi_gallery_image', $variation_to_update_id, $attid, $image_filepath); 

											$success_images = true;											
											set_post_thumbnail($variation_to_update_id, $attid); 																						
											$gallery_attachment_ids[] = $attid;												

										}										
									}																									
								}							
							}						
						}
						// Set product gallery images
						if ( ! empty($gallery_attachment_ids) )
							update_post_meta($variation_to_update_id, '_product_image_gallery', implode(',', $gallery_attachment_ids));		
					}							

					wc_delete_product_transients( $variation_to_update_id );	
				}

				foreach ($tmp_files as $file) { // remove all temporary files created
					if (file_exists($file)) @unlink($file);
				}

				// Update parent if variable so price sorting works and stays in sync with the cheapest child				

				$children = get_posts( array(
					'post_parent' 	=> $pid,
					'posts_per_page'=> -1,
					'post_type' 	=> 'product_variation',
					'fields' 		=> 'ids',
					'post_status'	=> array('draft', 'publish', 'trash', 'pending', 'future', 'private')
				) );

				$lowest_price = $lowest_regular_price = $lowest_sale_price = $highest_price = $highest_regular_price = $highest_sale_price = '';

				if ( $children ) {
					foreach ( $children as $child ) {

						$child_price 			= get_post_meta( $child, '_price', true );
						$child_regular_price 	= get_post_meta( $child, '_regular_price', true );
						$child_sale_price 		= get_post_meta( $child, '_sale_price', true );

						// Regular prices
						if ( ! is_numeric( $lowest_regular_price ) || $child_regular_price < $lowest_regular_price )
							$lowest_regular_price = $child_regular_price;

						if ( ! is_numeric( $highest_regular_price ) || $child_regular_price > $highest_regular_price )
							$highest_regular_price = $child_regular_price;

						// Sale prices
						if ( $child_price == $child_sale_price ) {
							if ( $child_sale_price !== '' && ( ! is_numeric( $lowest_sale_price ) || $child_sale_price < $lowest_sale_price ) )
								$lowest_sale_price = $child_sale_price;

							if ( $child_sale_price !== '' && ( ! is_numeric( $highest_sale_price ) || $child_sale_price > $highest_sale_price ) )
								$highest_sale_price = $child_sale_price;
						}
					}

			    	$lowest_price 	= $lowest_sale_price === '' || $lowest_regular_price < $lowest_sale_price ? $lowest_regular_price : $lowest_sale_price;
					$highest_price 	= $highest_sale_price === '' || $highest_regular_price > $highest_sale_price ? $highest_regular_price : $highest_sale_price;

					update_post_meta( $pid, '_price', $lowest_price );
					update_post_meta( $pid, '_min_variation_price', $lowest_price );
					update_post_meta( $pid, '_max_variation_price', $highest_price );
					update_post_meta( $pid, '_min_variation_regular_price', $lowest_regular_price );
					update_post_meta( $pid, '_max_variation_regular_price', $highest_regular_price );
					update_post_meta( $pid, '_min_variation_sale_price', $lowest_sale_price );
					update_post_meta( $pid, '_max_variation_sale_price', $highest_sale_price );

					// Update default attribute options setting
					if ( $import->options['update_all_data'] == 'yes' or ($import->options['update_all_data'] == 'no' and $import->options['is_update_attributes']) or $variation_just_created ){
						
						$default_attributes = array();
						$parent_attributes  = array();
						$attribute_position = 0;
						$is_update_attributes = true;

						foreach ($variation_serialized_attributes as $a_name => $attr_data) {

							$attr_name = $a_name;

							if ( in_array( $attr_name, $this->reserved_terms ) ) {
								$attr_name .= 's';
							}
							
							$values = array();

							// Update only these Attributes, leave the rest alone
							if ($import->options['update_all_data'] == 'no' and $import->options['update_attributes_logic'] == 'only'){
								if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])){
									if ( ! in_array( (( intval($attr_data['in_taxonomy'][$j]) ) ? "pa_" . $attr_name : $attr_name), array_filter($import->options['attributes_list'], 'trim'))){ 
										$attribute_position++;		
										continue;
									}
								}
								else {
									$is_update_attributes = false;
									break;
								}
							}

							// Leave these attributes alone, update all other Attributes
							if ($import->options['update_all_data'] == 'no' and $import->options['update_attributes_logic'] == 'all_except'){
								if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) {
									if ( in_array( (( intval($attr_data['in_taxonomy'][$j]) ) ? "pa_" . $attr_name : $attr_name) , array_filter($import->options['attributes_list'], 'trim'))){ 
										$attribute_position++;
										continue;
									}
								}
							}

							foreach ($variation_sku as $j => $void) {							

								$is_variation 	= ( intval($attr_data['in_variation'][$j]) ) ? 1 : 0;								

								if ($is_variation){

									$value = esc_attr(trim( $attr_data['value'][$j] ));

									if ( ! in_array($value, $values, true))  $values[] = $value;

									if ( ! empty($value) and empty($default_attributes[ (( intval($attr_data['in_taxonomy'][$j])) ? wc_attribute_taxonomy_name( $attr_name ) : sanitize_title($attr_name)) ])){

										switch ($import->options['default_attributes_type']) {
											case 'instock':												
												if ($variable_stock_status[$j] == 'instock'){
													$default_attributes[ (( intval($attr_data['in_taxonomy'][$j]) ) ? wc_attribute_taxonomy_name( $attr_name ) : sanitize_title($attr_name)) ] = sanitize_title($value);
												}
												break;
											case 'first':
												$default_attributes[ (( intval($attr_data['in_taxonomy'][$j]) ) ? wc_attribute_taxonomy_name( $attr_name ) : sanitize_title($attr_name)) ] = sanitize_title($value);
												break;
											
											default:
												# code...
												break;
										}										

									}
																			
								}
							}												

							if ( intval($attr_data['in_taxonomy'][0]) ){						

								if (intval($attr_data['is_create_taxonomy_terms'][0])) $this->create_taxonomy($attr_name, $logger);
																			 	
								if ( isset($values) and taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ) ) {				 							 		

								 	// Remove empty items in the array
								 	$values = array_filter( $values, array($this, "filtering") );

								 	if ( ! empty($values) ){

								 		$attr_values = array();
								 		
								 		$terms = get_terms( wc_attribute_taxonomy_name( $attr_name ), array('hide_empty' => false));								

								 		if ( ! is_wp_error($terms) ){

									 		foreach ($values as $key => $value) {
									 			$term_founded = false;	
												if ( count($terms) > 0 ){	
												    foreach ( $terms as $term ) {											    	
												    	if ( strtolower($term->name) == trim(strtolower($value)) or $term->slug == sanitize_title(trim(strtolower($value)))) {
												    		$attr_values[] = $term->slug;
												    		$term_founded = true;
												    		break;
												    	}
												    }
												}
											    if ( ! $term_founded and intval($attr_data['is_create_taxonomy_terms'][0]) ){
											    	$term = wp_insert_term(
														$value, // the term 
													  	wc_attribute_taxonomy_name( $attr_name ) // the taxonomy										  	
													);		
													if ( ! is_wp_error($term) )													
														$attr_values[] = (int) $term['term_id'];												
											    }
									 		}
									 	}
									 	else{
									 		$logger and call_user_func($logger, sprintf(__('- <b>WARNING</b>: %s.', 'pmxi_plugin'), $terms->get_error_message()));
									 	}

								 		$values = $attr_values;
								 	}

							 	} else {
							 		$values = array();
							 	}					 						 	
						 		// Update post terms
						 		if ( taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ))
						 			wp_set_object_terms( $pid, $values, wc_attribute_taxonomy_name( $attr_name ));

						 		if ( $values ) {
							 		// Add attribute to array, but don't set values
							 		$parent_attributes[ wc_attribute_taxonomy_name( $attr_name ) ] = array(
								 		'name' 			=> wc_attribute_taxonomy_name( $attr_name ),
								 		'value' 		=> '',
								 		'position' 		=> $attribute_position,
								 		'is_visible' 	=> (!empty($attr_data['is_visible'][0])) ? 1 : 0,
								 		'is_variation' 	=> (!empty($attr_data['in_variation'][0])) ? 1 : 0,
								 		'is_taxonomy' 	=> 1,
								 		'is_create_taxonomy_terms' => (!empty( $attr_data['is_create_taxonomy_terms'][0] )) ? 1 : 0
								 	);
							 	}

							}
							else{

								if ( taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ))
									wp_set_object_terms( $pid, NULL, wc_attribute_taxonomy_name( $attr_name ));

								$parent_attributes[ sanitize_title( $attr_name ) ] = array(
							 		'name' 			=> sanitize_text_field( $attr_name ),
							 		'value' 		=> implode('|', $values),
							 		'position' 		=> $attribute_position,
							 		'is_visible' 	=> (!empty($attr_data['is_visible'][0])) ? 1 : 0,
								 	'is_variation' 	=> (!empty($attr_data['in_variation'][0])) ? 1 : 0,
							 		'is_taxonomy' 	=> 0
							 	);
							}

						 	$attribute_position++;	
							
						}			

						if ($import->options['is_default_attributes'] and $is_update_attributes) {

							$current_default_attributes = get_post_meta($pid, '_default_attributes', true);		

							update_post_meta( $pid, '_default_attributes', (( ! empty($current_default_attributes)) ? array_merge($current_default_attributes, $default_attributes) : $default_attributes) );

						}
				
						if ($is_update_attributes) {
							
							$current_product_attributes = get_post_meta($pid, '_product_attributes', true);						
							
							update_post_meta( $pid, '_product_attributes', (( ! empty($current_product_attributes)) ? array_merge($current_product_attributes, $parent_attributes) : $parent_attributes) );	

						}
					}
				}
				elseif ( $import->options['make_simple_product']){
					wp_set_object_terms( $pid, 'simple', 'product_type' );
					pmwi_update_prices($pid);
				}								
			}	
			elseif ( $import->options['make_simple_product']){
				wp_set_object_terms( $pid, 'simple', 'product_type' );
				pmwi_update_prices($pid);
			}
		}
	}

	public function wpai_gallery_image($pid, $attid, $image_filepath){			

		$table = $this->wpdb->posts;

		$p = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $table WHERE ID = %d;", $pid));		

		if ($p and $p->post_parent){

			$gallery = explode(",", get_post_meta($p->post_parent, '_product_image_gallery', true));
			if (is_array($gallery)){
				if ( ! in_array($attid, $gallery) ) $gallery[] = $attid;
			}
			else{
				$gallery = array($attid);
			}

			update_post_meta($p->post_parent, '_product_image_gallery', implode(',', $gallery));
		
		}

	}

	protected function executeSQL(){
		// prepare bulk SQL query
		$table = _get_meta_table('post');
		
		if ( $this->post_meta_to_insert ){			
			$values = array();
			$already_added = array();
			
			foreach (array_reverse($this->post_meta_to_insert) as $key => $value) {
				if ( ! empty($value['meta_key']) and ! in_array($value['pid'] . '-' . $value['meta_key'], $already_added) ){
					$already_added[] = $value['pid'] . '-' . $value['meta_key'];						
					$values[] = '(' . $value['pid'] . ',"' . $value['meta_key'] . '",\'' . maybe_serialize($value['meta_value']) .'\')';						
				}
			}
			
			$this->wpdb->query("INSERT INTO $table (`post_id`, `meta_key`, `meta_value`) VALUES " . implode(',', $values));
			$this->post_meta_to_insert = array();
		}	
	}

	protected function pushmeta($pid, $meta_key, $meta_value){

		if (empty($meta_key)) return;		

		//$table = _get_meta_table( 'post' );
		
		if ( empty($this->articleData['ID']) or $this->is_update_cf($meta_key)){
			
			update_post_meta($pid, $meta_key, $meta_value);

			/*$this->wpdb->query($this->wpdb->prepare("DELETE FROM $table WHERE `post_id` = $pid AND `meta_key` = %s", $meta_key));

			$this->post_meta_to_insert[] = array(
				'meta_key' => $meta_key,
				'meta_value' => $meta_value,
				'pid' => $pid
			);*/
		}
		/*elseif ($this->is_update_cf($meta_key)){						
	
	        $this->wpdb->query($this->wpdb->prepare("DELETE FROM $table WHERE `post_id` = $pid AND `meta_key` = %s", $meta_key));			
				
			// previous meta field is not found
			$this->post_meta_to_insert[] = array(
				'meta_key' => $meta_key,
				'meta_value' => $meta_value,
				'pid' => $pid
			);
			
		}*/

	}

	/**
	* 
	* Is update allowed according to import record matching setting
	*
	*/
	protected function is_update_cf( $meta_key ){

		if ($this->options['update_all_data'] == 'yes') return true;

		if ( ! $this->options['is_update_custom_fields'] ) return false;			

		if ( $this->options['update_custom_fields_logic'] == "full_update" ) return true;
		if ( $this->options['update_custom_fields_logic'] == "only" and ! empty($this->options['custom_fields_list']) and is_array($this->options['custom_fields_list']) and in_array($meta_key, $this->options['custom_fields_list']) ) return true;
		if ( $this->options['update_custom_fields_logic'] == "all_except" and ( empty($this->options['custom_fields_list']) or ! in_array($meta_key, $this->options['custom_fields_list']) )) return true;
		
		return false;

	}	

	protected function associate_terms($pid, $assign_taxes, $tx_name, $logger = false){			

		$terms = wp_get_object_terms( $pid, $tx_name );
		$term_ids = array();        

		if ( ! empty($terms) ){
			if ( ! is_wp_error( $terms ) ) {				
				foreach ($terms as $term_info) {
					$term_ids[] = $term_info->term_taxonomy_id;
					$this->wpdb->query(  $this->wpdb->prepare("UPDATE {$this->wpdb->term_taxonomy} SET count = count - 1 WHERE term_taxonomy_id = %d", $term_info->term_taxonomy_id) );
				}				
				$in_tt_ids = "'" . implode( "', '", $term_ids ) . "'";
				$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->wpdb->term_relationships} WHERE object_id = %d AND term_taxonomy_id IN ($in_tt_ids)", $pid ) );
			}
		}

		if (empty($assign_taxes)){ 
			//_wc_term_recount($terms, $tx_name, true, false);
			return;
		}

		foreach ($assign_taxes as $tt) {
			$this->wpdb->insert( $this->wpdb->term_relationships, array( 'object_id' => $pid, 'term_taxonomy_id' => $tt ) );
			$this->wpdb->query( "UPDATE {$this->wpdb->term_taxonomy} SET count = count + 1 WHERE term_taxonomy_id = $tt" );
			delete_transient( 'wc_ln_count_' . md5( sanitize_key( $tx_name ) . sanitize_key( $tt ) ) );
		}

		$values = array();
        $term_order = 0;
		foreach ( $assign_taxes as $tt )			                        	
    		$values[] = $this->wpdb->prepare( "(%d, %d, %d)", $pid, $tt, ++$term_order);
		                					

		if ( $values ){
			if ( false === $this->wpdb->query( "INSERT INTO {$this->wpdb->term_relationships} (object_id, term_taxonomy_id, term_order) VALUES " . join( ',', $values ) . " ON DUPLICATE KEY UPDATE term_order = VALUES(term_order)" ) ){
				$logger and call_user_func($logger, __('<b>ERROR</b> Could not insert term relationship into the database', 'pmxi_plugin') . ': '. $this->wpdb->last_error);				
			}
		}       		                 		

		wp_cache_delete( $pid, $tx_name . '_relationships' ); 

		//_wc_term_recount( $assign_taxes, $tx_name );
	}

	protected function duplicate_post_meta( $new_id, $id ) {

		$table = _get_meta_table('post');
		
		$post_meta_infos = $this->wpdb->get_results("SELECT meta_key, meta_value FROM $table WHERE post_id=$id");

		if (count($post_meta_infos)!=0) {
			$sql_query_sel = array();
			$sql_query = "INSERT INTO $table (post_id, meta_key, meta_value) ";
			foreach ($post_meta_infos as $meta_info) {
				if ($this->is_update_cf($meta_info->meta_key)){					
					$meta_key = $meta_info->meta_key;
					$this->wpdb->query($this->wpdb->prepare("DELETE FROM $table WHERE `post_id` = $new_id AND `meta_key` = %s", $meta_key));
					$meta_value = addslashes($meta_info->meta_value);
					$sql_query_sel[]= "SELECT $new_id, '$meta_key', '$meta_value'";
				}
			}
			if ( ! empty($sql_query_sel) ){
				$sql_query.= implode(" UNION ALL ", $sql_query_sel);
				$this->wpdb->query($sql_query);
			}
		}

	}
	
	function pmwi_buf_prices($pid){

		$table = _get_meta_table('post');
		
		$post_meta_infos = $this->wpdb->get_results("SELECT meta_key, meta_value FROM $table WHERE post_id=$pid");

		foreach ($post_meta_infos as $meta_info) {
			if (in_array($meta_info->meta_key, array('_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_from', '_sale_price_dates_to', '_price'))){
				$this->pushmeta($pid, $meta_info->meta_key . '_tmp', $meta_info->meta_value);				
			}
		}

		//$this->executeSQL();

	}

	function pmwi_update_prices($pid){

		$table = _get_meta_table('post');
		
		$post_meta_infos = $this->wpdb->get_results("SELECT meta_key, meta_value FROM $table WHERE post_id=$pid");

		foreach ($post_meta_infos as $meta_info) {
			if (in_array($meta_info->meta_key, array('_regular_price_tmp', '_sale_price_tmp', '_sale_price_dates_from_tmp', '_sale_price_dates_from_tmp', '_sale_price_dates_to_tmp', '_price_tmp'))){
				$this->pushmeta($pid, str_replace('_tmp', '', $meta_info->meta_key), $meta_info->meta_value);
				delete_post_meta( $pid, $meta_info->meta_key );
			}
		}

		//$this->executeSQL();

	}
	
	function create_taxonomy($attr_name, $logger){
		
		global $woocommerce;

		if ( ! taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ) ) {

	 		// Grab the submitted data							
			$attribute_name    = ( isset( $attr_name ) ) ? wc_sanitize_taxonomy_name( stripslashes( (string) $attr_name ) ) : '';
			$attribute_label   = ucwords( stripslashes( (string) $attr_name ));
			$attribute_type    = 'select';
			$attribute_orderby = 'menu_order';			

			if ( in_array( $attribute_name, $this->reserved_terms ) ) {
				$attribute_name .= 's';
			}

			if ( in_array( $attribute_name, $this->reserved_terms ) ) {
				$logger and call_user_func($logger, sprintf(__('- <b>WARNING</b>: Slug “%s” is not allowed because it is a reserved term. Change it, please.', 'pmxi_plugin'), wc_attribute_taxonomy_name( $attribute_name )));
			}			
			else{
				// Register the taxonomy now so that the import works!
				$domain = wc_attribute_taxonomy_name( $attr_name );
				if (strlen($domain) <= 32){

					$this->wpdb->insert(
						$this->wpdb->prefix . 'woocommerce_attribute_taxonomies',
						array(
							'attribute_label'   => $attribute_label,
							'attribute_name'    => $attribute_name,
							'attribute_type'    => $attribute_type,
							'attribute_orderby' => $attribute_orderby,
						)
					);												
								
					register_taxonomy( $domain,
				        apply_filters( 'woocommerce_taxonomy_objects_' . $domain, array('product') ),
				        apply_filters( 'woocommerce_taxonomy_args_' . $domain, array(
				            'hierarchical' => true,
				            'show_ui' => false,
				            'query_var' => true,
				            'rewrite' => false,
				        ) )
				    );

					delete_transient( 'wc_attribute_taxonomies' );
					$attribute_taxonomies = $this->wpdb->get_results( "SELECT * FROM " . $this->wpdb->prefix . "woocommerce_attribute_taxonomies" );
					set_transient( 'wc_attribute_taxonomies', $attribute_taxonomies );
					apply_filters( 'woocommerce_attribute_taxonomies', $attribute_taxonomies );

					$logger and call_user_func($logger, sprintf(__('- <b>CREATED</b>: Taxonomy attribute “%s” have been successfully created.', 'pmxi_plugin'), wc_attribute_taxonomy_name( $attribute_name )));	

				}
				else{
					$logger and call_user_func($logger, sprintf(__('- <b>WARNING</b>: Taxonomy “%s” name is more than 32 characters. Change it, please.', 'pmxi_plugin'), $attr_name));
				}

			}

	 	}
	}

	function pmwi_link_all_variations($product_id, $options = array()) {

		global $woocommerce;

		@set_time_limit(0);

		$post_id = intval( $product_id );

		if ( ! $post_id ) return 0;

		$variations = array();

		$_product = get_product( $post_id, array( 'product_type' => 'variable' ) );

		$v = $_product->get_attributes();		

		// Put variation attributes into an array
		foreach ( $_product->get_attributes() as $attribute ) {

			if ( ! $attribute['is_variation'] ) continue;

			$attribute_field_name = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( $attribute['is_taxonomy'] ) {
				$post_terms = wp_get_post_terms( $post_id, $attribute['name'] );
				$options = array();
				foreach ( $post_terms as $term ) {
					$options[] = $term->slug;
				}
			} else {
				$options = explode( '|', $attribute['value'] );
			}

			$options = array_map( 'sanitize_title', array_map( 'trim', $options ) );

			$variations[ $attribute_field_name ] = $options;
		}

		// Quit out if none were found
		if ( sizeof( $variations ) == 0 ) return 0;

		// Get existing variations so we don't create duplicates
	    $available_variations = array();

	    foreach( $_product->get_children() as $child_id ) {
	    	$child = $_product->get_child( $child_id );

	        if ( ! empty( $child->variation_id ) ) {
	            $available_variations[] = $child->get_variation_attributes();

	            update_post_meta( $child->variation_id, '_regular_price', get_post_meta( $post_id, '_regular_price', true ) );
				update_post_meta( $child->variation_id, '_sale_price', get_post_meta( $post_id, '_sale_price', true ) );
				if ( class_exists('woocommerce_wholesale_pricing') ) update_post_meta( $child->variation_id, 'pmxi_wholesale_price', get_post_meta( $post_id, 'pmxi_wholesale_price', true ) );
				update_post_meta( $child->variation_id, '_sale_price_dates_from', get_post_meta( $post_id, '_sale_price_dates_from', true ) );
				update_post_meta( $child->variation_id, '_sale_price_dates_to', get_post_meta( $post_id, '_sale_price_dates_to', true ) );
				update_post_meta( $child->variation_id, '_price', get_post_meta( $post_id, '_price', true ) );
				update_post_meta( $child->variation_id, '_stock', get_post_meta( $post_id, '_stock', true ) );
				update_post_meta( $child->variation_id, '_stock_status', get_post_meta( $post_id, '_stock_status', true ) );			
				update_post_meta( $child->variation_id, '_manage_stock', get_post_meta( $post_id, '_manage_stock', true ) );			
				update_post_meta( $child->variation_id, '_backorders', get_post_meta( $post_id, '_backorders', true ) );	
	        }
	    }	  

		// Created posts will all have the following data
		$variation_post_data = array(
			'post_title' => 'Product #' . $post_id . ' Variation',
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => get_current_user_id(),
			'post_parent' => $post_id,
			'post_type' => 'product_variation'
		);
		
		$variation_ids = array();
		$added = 0;
		$possible_variations = $this->array_cartesian( $variations );		

		foreach ( $possible_variations as $variation ) {

			// Check if variation already exists
			if ( in_array( $variation, $available_variations ) )
				continue;

			$variation_id = wp_insert_post( $variation_post_data );			
			
			update_post_meta( $variation_id, '_regular_price', get_post_meta( $post_id, '_regular_price', true ) );
			update_post_meta( $variation_id, '_sale_price', get_post_meta( $post_id, '_sale_price', true ) );
			if ( class_exists('woocommerce_wholesale_pricing') ) update_post_meta( $variation_id, 'pmxi_wholesale_price', get_post_meta( $post_id, 'pmxi_wholesale_price', true ) );
			update_post_meta( $variation_id, '_sale_price_dates_from', get_post_meta( $post_id, '_sale_price_dates_from', true ) );
			update_post_meta( $variation_id, '_sale_price_dates_to', get_post_meta( $post_id, '_sale_price_dates_to', true ) );
			update_post_meta( $variation_id, '_price', get_post_meta( $post_id, '_price', true ) );
			update_post_meta( $variation_id, '_stock', get_post_meta( $post_id, '_stock', true ) );
			update_post_meta( $variation_id, '_stock_status', get_post_meta( $post_id, '_stock_status', true ) );			
			update_post_meta( $variation_id, '_manage_stock', get_post_meta( $post_id, '_manage_stock', true ) );			
			update_post_meta( $variation_id, '_backorders', get_post_meta( $post_id, '_backorders', true ) );			
			

			$variation_ids[] = $variation_id;

			foreach ( $variation as $key => $value ) {
				update_post_meta( $variation_id, $key, $value );
			}

			$added++;

			//do_action( 'product_variation_linked', $variation_id );
			
		}		

		wc_delete_product_transients( $post_id );

		return $added;
	}


	function array_cartesian( $input ) {

	    $result = array();

	    while ( list( $key, $values ) = each( $input ) ) {
	        // If a sub-array is empty, it doesn't affect the cartesian product
	        if ( empty( $values ) ) {
	            continue;
	        }

	        // Special case: seeding the product array with the values from the first sub-array
	        if ( empty( $result ) ) {
	            foreach ( $values as $value ) {
	                $result[] = array( $key => $value );
	            }
	        }
	        else {
	            // Second and subsequent input sub-arrays work like this:
	            //   1. In each existing array inside $product, add an item with
	            //      key == $key and value == first item in input sub-array
	            //   2. Then, for each remaining item in current input sub-array,
	            //      add a copy of each existing array inside $product with
	            //      key == $key and value == first item in current input sub-array

	            // Store all items to be added to $product here; adding them on the spot
	            // inside the foreach will result in an infinite loop
	            $append = array();
	            foreach( $result as &$product ) {
	                // Do step 1 above. array_shift is not the most efficient, but it
	                // allows us to iterate over the rest of the items with a simple
	                // foreach, making the code short and familiar.
	                $product[ $key ] = array_shift( $values );

	                // $product is by reference (that's why the key we added above
	                // will appear in the end result), so make a copy of it here
	                $copy = $product;

	                // Do step 2 above.
	                foreach( $values as $item ) {
	                    $copy[ $key ] = $item;
	                    $append[] = $copy;
	                }

	                // Undo the side effecst of array_shift
	                array_unshift( $values, $product[ $key ] );
	            }

	            // Out of the foreach, we can add to $results now
	            $result = array_merge( $result, $append );
	        }
	    }

	    return $result;
	}

	public function _filter_has_cap_unfiltered_html($caps)
	{
		$caps['unfiltered_html'] = true;
		return $caps;
	}		

	function auto_cloak_links($import, &$url){
		
		$url = apply_filters('pmwi_cloak_affiliate_url', trim($url), $import->id);
		
		// cloak urls with `WP Wizard Cloak` if corresponding option is set
		if ( ! empty($import->options['is_cloak']) and class_exists('PMLC_Plugin')) {														
			if (preg_match('%^\w+://%i', $url)) { // mask only links having protocol
				// try to find matching cloaked link among already registered ones
				$list = new PMLC_Link_List(); $linkTable = $list->getTable();
				$rule = new PMLC_Rule_Record(); $ruleTable = $rule->getTable();
				$dest = new PMLC_Destination_Record(); $destTable = $dest->getTable();
				$list->join($ruleTable, "$ruleTable.link_id = $linkTable.id")
					->join($destTable, "$destTable.rule_id = $ruleTable.id")
					->setColumns("$linkTable.*")
					->getBy(array(
						"$linkTable.destination_type =" => 'ONE_SET',
						"$linkTable.is_trashed =" => 0,
						"$linkTable.preset =" => '',
						"$linkTable.expire_on =" => '0000-00-00',
						"$ruleTable.type =" => 'ONE_SET',
						"$destTable.weight =" => 100,
						"$destTable.url LIKE" => $url,
					), NULL, 1, 1)->convertRecords();
				if ($list->count()) { // matching link found
					$link = $list[0];
				} else { // register new cloaked link
					global $wpdb;
					$slug = max(
						intval($wpdb->get_var("SELECT MAX(CONVERT(name, SIGNED)) FROM $linkTable")),
						intval($wpdb->get_var("SELECT MAX(CONVERT(slug, SIGNED)) FROM $linkTable")),
						0
					);
					$i = 0; do {
						is_int(++$slug) and $slug > 0 or $slug = 1;
						$is_slug_found = ! intval($wpdb->get_var("SELECT COUNT(*) FROM $linkTable WHERE name = '$slug' OR slug = '$slug'"));
					} while( ! $is_slug_found and $i++ < 100000);
					if ($is_slug_found) {
						$link = new PMLC_Link_Record(array(
							'name' => strval($slug),
							'slug' => strval($slug),
							'header_tracking_code' => '',
							'footer_tracking_code' => '',
							'redirect_type' => '301',
							'destination_type' => 'ONE_SET',
							'preset' => '',
							'forward_url_params' => 1,
							'no_global_tracking_code' => 0,
							'expire_on' => '0000-00-00',
							'created_on' => date('Y-m-d H:i:s'),
							'is_trashed' => 0,
						));
						$link->insert();
						$rule = new PMLC_Rule_Record(array(
							'link_id' => $link->id,
							'type' => 'ONE_SET',
							'rule' => '',
						));
						$rule->insert();
						$dest = new PMLC_Destination_Record(array(
							'rule_id' => $rule->id,
							'url' => $url,
							'weight' => 100,
						));
						$dest->insert();
					} else {
						$logger and call_user_func($logger, sprintf(__('- <b>WARNING</b>: Unable to create cloaked link for %s', 'pmxi_plugin'), $url));						
						$link = NULL;
					}
				}
				if ($link) { // cloaked link is found or created for url
					$url = preg_replace('%' . preg_quote($url, '%') . '(?=([\s\'"]|$))%i', $link->getUrl(), $url);								
				}									
			}
		}
	}

	function is_update_custom_field($existing_meta_keys, $options, $meta_key){

		if ($options['update_all_data'] == 'yes') return true;

		if ( ! $options['is_update_custom_fields'] ) return false;			

		if ($options['update_custom_fields_logic'] == "full_update") return true;
		if ($options['update_custom_fields_logic'] == "only" and ! empty($options['custom_fields_list']) and is_array($options['custom_fields_list']) and in_array($meta_key, $options['custom_fields_list']) ) return true;
		if ($options['update_custom_fields_logic'] == "all_except" and ( empty($options['custom_fields_list']) or ! in_array($meta_key, $options['custom_fields_list']) )) return true;
		
		return false;
	}	
	
	function prepare_price( $price ){   

		return pmwi_prepare_price( $price, $this->options['disable_prepare_price'], $this->options['prepare_price_to_woo_format'] );
		
	}

	function adjust_price( $price, $field ){

		return pmwi_adjust_price( $price, $field, $this->options);
		
	}
}
