<?php

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );


class W3ExAdvBulkEditView{
	
	private static $ins = null;
	private $attributes      = array();
	private $attributes_asoc = array();
	private $variations_fields = array();
	private $categories = array();
	private $cat_asoc = array();
	
	
    public static function init()
    {
       self::instance()->_main();
    }

    public static function instance()
    {
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }
	
	public function mb_ucfirst($p_str)
	{
		if (function_exists('mb_substr') && function_exists('mb_strtoupper') && function_exists('mb_strlen')) 
		{
			$string = $p_str;
			if(mb_strlen($p_str) > 0)
			{
			    $string = mb_strtoupper(mb_substr($p_str, 0, 1)) . mb_substr($p_str, 1);
			}
		    return $string;
		}else
		{
			return ucfirst($p_str);
		}
	}
	
	public function loadAttributes()
	{
		//categories
		$args = array(
		    'number'     => 99999,
		    'orderby'    => 'slug',
		    'order'      => 'ASC',
		    'hide_empty' => false,
		    'include'    => '',
			'fields'     => 'all'
		);

		$woo_categories = get_terms( 'product_cat', $args );

		foreach($woo_categories as $category){
		   if(!is_object($category)) continue;
		   if(!property_exists($category,'term_taxonomy_id')) continue;
		    if(!property_exists($category,'term_id')) continue;
		   $cat = new stdClass();
		   $cat->category_id     = $category->term_taxonomy_id;
		   $cat->term_id         = $category->term_id;
		   $cat->category_name   = $category->name;
		   $cat->category_slug   = urldecode($category->slug);
		   $cat->category_parent = $category->parent;
		   $this->categories[] = $cat;   
		   $this->cat_asoc[$cat->category_id] = $cat;
		};
		
		$curr_settings = get_option('w3exabe_settings');
		if(is_array($curr_settings))
		{
			if(isset($curr_settings['disattributes']))
			{
				if($curr_settings['disattributes'] == 1)
					return;
			}
		}
	    global $wpdb;
		
		$woo_attrs = $wpdb->get_results("select * from " . $wpdb->prefix . "woocommerce_attribute_taxonomies",ARRAY_A);
		$counter = 0;
//		foreach($woo_attrs as $attr){
			
		foreach($woo_attrs as $attr){
//			if($counter > 15)
//				return;
			$counter++;
			$att         = new stdClass();
			$att->id     = $attr['attribute_id'];
			$att->name   = $attr['attribute_name'];  
			$att->label  = $attr['attribute_label']; 
			if(!$att->label)
				$att->label = ucfirst($att->name);
			$att->type   = $attr['attribute_type'];

		  
			$att->values = array();
			$values     = get_terms( 'pa_' . $att->name, array('hide_empty' => false));
			foreach($values as $val){
				if(!is_object($val)) continue;
				if(!property_exists($val,'term_taxonomy_id')) continue;
				$value          = new stdClass();
				$value->id      = $val->term_taxonomy_id;
				$value->term_id      = $val->term_id;
				$value->slug    = $val->slug;
				$value->name    = $val->name;
				$value->parent  = $val->parent;
				$att->values[]  = $value;
			}
			
		 	if(count($att->values) > 0)
			{
				$this->attributes[]                = $att;
				$this->attributes_asoc[$att->name] = $att;
				$this->variations_fields[] = 'pattribute_'.$att->id;
			}
		}
	}

	public function showMainPage()
	{
		global $wpdb;
		$this->loadAttributes();
		$sel_fields = array();
		$sel_fields = get_option('w3exabe_columns');
		$purl = plugin_dir_url(__FILE__);
		echo "<script>
		var W3Ex = W3Ex || {};
		W3Ex.attributes =  {};
		W3Ex.attr_cols =  {};
		W3Ex.categories =  [];
		W3Ex.imagepath = '".plugin_dir_url(__FILE__)."';";
		echo PHP_EOL;
		$upload_dir = wp_upload_dir();
		if(is_array($upload_dir) && isset($upload_dir['baseurl']))
		{
			$upload_dir = $upload_dir['baseurl'];
			echo 'W3Ex.uploaddir = "'. $upload_dir .'";';
		}
		echo PHP_EOL;
		foreach($this->attributes as $attr)
		{
	 		foreach($attr->values as $value)
			{
				$attrname = str_replace('"','\"',$attr->name);
				$attrname = trim(preg_replace('/\s+/', ' ', $attrname));
				$attrslug = str_replace('"','\"',$value->slug);
				$attrslug = trim(preg_replace('/\s+/', ' ', $attrslug));
				$attrvalname = str_replace('"','\"',$value->name);
				$attrvalname = trim(preg_replace('/\s+/', ' ', $attrvalname));
				echo 'W3Ex.attributes['.$value->id.'] = {id:'.$value->id.',term_id:'.$value->term_id.',name:"'.$attrvalname.'",attr:"'.$attrname.'",value:"'.$attrslug.'"};';
				echo PHP_EOL;
			}
		}
		foreach($this->attributes as $attr)
		{
			$attrname = str_replace('"','\"',$attr->name);
			$attrname = trim(preg_replace('/\s+/', ' ', $attrname));
			$attrlabel = str_replace('"','\"',$attr->label);
			$attrlabel = trim(preg_replace('/\s+/', ' ', $attrlabel));
			echo 'W3Ex.attr_cols['.$attr->id.'] = {id:'.$attr->id.',attr:"'.$attrlabel.'",value:"'.$attrname.'"};';
			echo PHP_EOL;
		}
		
		foreach($this->categories as $category)
		{
			$catname = str_replace('"','\"',$category->category_name);
			echo 'W3Ex.categories['.$category->term_id.'] = "'.$catname.'";';
			echo PHP_EOL;
		}
		
		if(is_array($sel_fields) && !empty($sel_fields))
		{
			echo 'W3Ex.colsettings = '. json_encode($sel_fields). ';';
		    echo PHP_EOL;
		}
		
		$sel_fields = get_option('w3exabe_customsel');
		if(is_array($sel_fields) && !empty($sel_fields))
		{
			echo 'W3Ex.customfieldssel = '. json_encode($sel_fields). ';';
		    echo PHP_EOL;
		}
		
		$sel_fields = get_option('w3exabe_custom');
		if(is_array($sel_fields) && !empty($sel_fields))
		{
			echo 'W3Ex.customfields = '. json_encode($sel_fields). ';';
		    echo PHP_EOL;
		}
		
			$settings = get_option('w3exabe_settings');
			/*if(is_array($settings))
			{
				if(isset($settings['usecomma']))
				{	
					if($settings['usecomma'] == 1)
						echo 'W3Ex.sett_usecomma = 1;'; echo PHP_EOL;
				}
			}*/
			echo 'W3Ex.post_excerpt = "'. str_replace('"','\"',__( 'Product Short Description', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.post_content = "'.str_replace('"','\"',__( 'Description', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._thumbnail_id = "'.str_replace('"','\"',__( 'Image', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._product_image_gallery = "'.str_replace('"','\"',__( 'Product Gallery', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._sku = "'.str_replace('"','\"',__( 'SKU', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.post_name = "'.str_replace('"','\"',__( 'Slug', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.product_tag = "'.str_replace('"','\"',__( 'Tags', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._virtual = "'.str_replace('"','\"',__( 'Virtual', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._downloadable = "'.str_replace('"','\"',__( 'Downloadable', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.instock = "'.str_replace('"','\"',__( 'In stock', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.outofstock = "'.str_replace('"','\"',__( 'Out of stock', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.post_title = "'.str_replace('"','\"',__( 'Title', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.product_cat = "'.str_replace('"','\"',__( 'Categories', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._regular_price = "'.str_replace('"','\"',__( 'Regular Price', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._sale_price = "'.str_replace('"','\"',__( 'Sale Price', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._sale_price_dates_from = "'.str_replace('"','\"',__( 'Sale start date:', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._sale_price_dates_to = "'.str_replace('"','\"',__( 'Sale end date:', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._featured = "'.str_replace('"','\"',__( 'Featured', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._tax_status = "'.str_replace('"','\"',__( 'Tax Status', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._tax_class = "'.str_replace('"','\"',__( 'Tax class', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._weight = "'.str_replace('"','\"',__( 'Weight', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._height = "'.str_replace('"','\"',__( 'Height', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._width = "'.str_replace('"','\"',__( 'Width', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._length = "'.str_replace('"','\"',__( 'Length', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._stock = "'.str_replace('"','\"',__( 'Stock Qty', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._stock_status  = "'.str_replace('"','\"',__( 'Stock status', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._manage_stock = "'.str_replace('"','\"',__( 'Manage Stock', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._backorders = "'.str_replace('"','\"',__( 'Allow Backorders?', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._sold_individually = "'.str_replace('"','\"',__( 'Sold Individually', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.product_shipping_class = "'.str_replace('"','\"',__( 'Shipping class', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._purchase_note = "'.str_replace('"','\"',__( 'Purchase Note', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.post_status = "'.str_replace('"','\"',__( 'Status', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._visibility = "'.str_replace('"','\"',__( 'Catalog visibility:', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._upsell_ids = "'.str_replace('"','\"',__( 'Up-Sells', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._crosssell_ids = "'.str_replace('"','\"',__( 'Cross-Sells', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._downloadable = "'.str_replace('"','\"',__( 'Downloadable', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._virtual = "'.str_replace('"','\"',__( 'Virtual', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._download_expiry = "'.str_replace('"','\"',__( 'Download Expiry', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._download_limit = "'.str_replace('"','\"',__( 'Download Limit', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._downloadable_files = "'.str_replace('"','\"',__( 'Downloadable Files', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._download_type = "'.str_replace('"','\"',__( 'Download Type', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._product_url = "'.str_replace('"','\"',__( 'Product URL', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._button_text = "'.str_replace('"','\"',__( 'Button text', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.comment_status = "'.str_replace('"','\"',__( 'Enable reviews', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.menu_order = "'.str_replace('"','\"',__( 'Menu order', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex.product_type = "'.str_replace('"','\"',__( 'Product Type', 'woocommerce')).'";'; echo PHP_EOL;
			echo 'W3Ex._default_attributes = "'.str_replace('"','\"',__( 'Default', 'woocommerce')).' '.__( 'Attributes', 'woocommerce').'";'; echo PHP_EOL;
			echo 'W3Ex.grouped_items = "'.str_replace('"','\"',__( 'Grouping', 'woocommerce')).'";'; echo PHP_EOL;
		echo "</script>";
		?>
		<div class="wrap w3exabe">
		<!--<div id="w3exibaparent">-->
		<h2>Advanced Bulk Edit</h2>
		<br/>
		<?php
//		$sel_fields = get_option('w3exabe_custom');
//		if(is_array($sel_fields) && !empty($sel_fields))
//		{
//			foreach($sel_fields as $customfield)
//			{
////				$catname = str_replace('"','\"',$category->category_name);
//				echo '<div class="customfield" data-id="'.$customfield['name'].'" data-type="'.$customfield['type'].'">'.$customfield['name'].':';
//				if($customfield['type'] === "text" || $customfield['type'] === "multitext")
//				{
//					echo '<select>
//				<option value="con">'. __( 'contains', 'woocommerce-advbulkedit').'</option>
//				<option value="notcon">'.__( 'does not contain', 'woocommerce-advbulkedit').'</option>
//				<option value="start">'.__( 'starts with', 'woocommerce-advbulkedit').'</option>
//				<option value="end">'.__( 'ends with', 'woocommerce-advbulkedit').'</option>
//			</select>
//			<input type="text"/>';
//				}elseif($customfield['type'] === "customh" || $customfield['type'] === "custom")
//				{
//					echo '<select class="makechosen catselset" style="width:250px;" data-placeholder="select" multiple ><option value=""></option>';
//									   $argsb = array(
//									    'number'     => 99999,
//									    'orderby'    => 'slug',
//									    'order'      => 'ASC',
//									    'hide_empty' => false,
//									    'include'    => '',
//										'fields'     => 'all'
//									);
//
//									$woo_categoriesb = get_terms($customfield['name'], $argsb );
//
//									foreach($woo_categoriesb as $category)
//									{
//									    if(!is_object($category)) continue;
//									    if(!property_exists($category,'name')) continue;
//									    if(!property_exists($category,'term_id')) continue;
//									   	echo '<option value="'.$category->term_id.'" >'.$category->name.'</option>';
//									}
//									echo '</select>';
//				}else{
//					echo '<select>
//					<option value="more">></option>
//					<option value="less"><</option>
//					<option value="equal">==</option>
//					<option value="moree">>=</option>
//					<option value="lesse"><=</option>
//				</select>
//				<input type="text"/>';
//				}
//				echo "</div><br><br>";
//				echo PHP_EOL;
//			}
//		}
		?>
			<!--<input id="showhidecustom" class="button" type="button" value="<?php _e("Save Changes","woocommerce-advbulkedit"); ?>" />-->
			<br />
			<table cellpadding="5" cellspacing="0" id="tablesearchfilters">
			<tbody>
			<tr>
			<td>
			<?php _e( 'Title', 'woocommerce');?>: </td>
			<td>
			<select id="titleparams">
				<option value="con"><?php _e( 'contains', 'woocommerce-advbulkedit');?></option>
				<option value="notcon"><?php _e( 'does not contain', 'woocommerce-advbulkedit');?></option>
				<option value="start"><?php _e( 'starts with', 'woocommerce-advbulkedit');?></option>
				<option value="end"><?php _e( 'ends with', 'woocommerce-advbulkedit');?></option>
			</select>
			<input id="titlevalue" type="text"/>
			</td>
			<td>
			<?php _e( 'Categories', 'woocommerce');?>: </td><td><select id="selcategory" class="makechosen catsel" data-placeholder="choose\search" multiple style="width:250px;">
			 <option value=""></option>
			<?php
				$cats = $this->categories;
				$newcats = array();
				$cats_asoc = $this->cat_asoc;
				$depth = array();

			    foreach($cats as $cat)
				{
					if($cat->category_parent == 0)
					{
						$depth[$cat->term_id] = 0;
						$newcats[] = $cat;
					}
				}
				foreach($cats as $cat)
				{
					if($cat->category_parent == 0) continue;
					{
//						if(!isset($options[$cat->category_id]))
						{
							if(!isset($depth[$cat->term_id]))
							{
								$loop = true;
								$counter = 0;
								while($loop && ($counter < 1000))
								{
									foreach($cats as $catin)
									{
										if($catin->category_parent == 0)
										   continue;
										if(isset($depth[$catin->category_parent]))
										{
											$newdepth = $depth[$catin->category_parent];
											$newdepth++;
											if(!isset($depth[$catin->term_id]))
											{
												$depth[$catin->term_id] = $newdepth;
												for($i = 0; $i < count($newcats); $i++)
												{
													$catins = $newcats[$i];
													if($catins->term_id == $catin->category_parent)
													{
														array_splice($newcats, $i+1, 0,array($catin));
														break;
													}
												}
											}

											if($catin->term_id == $cat->term_id)
											{
												$loop = false;
												break;
											}
										}
									}
									$counter++;
								}
							}
						}
					}
					
				}
				if(count($newcats) == count($cats))
				{
					foreach($newcats as $catin)
					{
						$depthstring = '';
						if(isset($depth[$catin->term_id]))
						{
							$depthn = (int)$depth[$catin->term_id];
							if($depthn < 15)
							{
								while($depthn > 0)
								{
									$depthstring = $depthstring.'&nbsp;&nbsp;&nbsp;';
//									$depthstring = $depthstring.'&#09; ';
									$depthn--;
								}
								
							}
						}
						echo '<option value="'.$catin->category_id.'" >'.$depthstring.$catin->category_name.'</option>';
					}
				}else
				{
					foreach($cats as $catin)
					{
						echo '<option value="'.$catin->category_id.'" >'.$catin->category_name.'</option>';
					}
				}
				
		
			?>
			</select>&nbsp;<label><input type="checkbox" id="categoryor">AND</input></label>
			</td></tr>
			<?php
				$endrow = false;
				$counter = 0;
				$settings = get_option('w3exabe_settings');
				$showattrs = "";
				if(is_array($settings))
				{
					if(isset($settings['showattributes']))
					{
						if($settings['showattributes'] == 0)
						{
							$showattrs = 'style="display: none"';
						}
					}
				}
				if(count($this->attributes) > 0)
				{
					foreach($this->attributes as $attr)
					{
						if($counter % 2 == 0)
						{
							echo '<tr class="showattributes" '.$showattrs.'><td>';
						}else
						{
							echo '<td>';
						}
						echo $attr->label.': </td><td><select class="makechosen custattributes" data-placeholder="choose\search" multiple style="width:250px;"> <option value=""></option>';
						
						foreach($attr->values as $value)
						{
							echo '<option value="'.$value->id.'">'.$value->name.'</option>';
						}
						echo '</select>';
						if($counter % 2 == 0)
						{
							$endrow = false;
							echo '</td>';
						}else
						{
							$endrow = true;
							echo '</td></tr>';
						}
						$counter++;					
				    }
					if(!$endrow)
					{
						echo '</tr>';
					}
				}
//				_e( 'Sale Price', 'wooadvbulkedit');
			?>
			<tr class="showprices"
			<?php
				if(is_array($settings))
				{
					if(isset($settings['showprices']))
					{
						if($settings['showprices'] == 0)
						{
							echo 'style="display: none"';
						}
					}
				}
			?>
			>
				<td><?php _e( 'Regular Price', 'woocommerce');?>: </td>
				<td>
				<select id="price">
					<option value="more">></option>
					<option value="less"><</option>
					<option value="equal">==</option>
					<option value="moree">>=</option>
					<option value="lesse"><=</option>
				</select>
				<input id="pricevalue" type="text"/>
			</td>
				<td><?php _e( 'Sale Price', 'woocommerce');?>: </td>
				<td>
				<select id="saleprice">
					<option value="more">></option>
					<option value="less"><</option>
					<option value="equal">==</option>
					<option value="moree">>=</option>
					<option value="lesse"><=</option>
				</select>
				<input id="salepricevalue" type="text"/>
			</td>
			</tr>
			<tr class="showskutags"
			<?php
				if(is_array($settings))
				{
					if(isset($settings['showskutags']))
					{
						if($settings['showskutags'] == 0)
						{
							echo 'style="display: none"';
						}
					}
				}
			?>
			>
				<td><?php _e( 'SKU', 'woocommerce');?>: </td>
				<td>
				<select id="skuparams">
				<option value="con"><?php _e( 'contains', 'woocommerce-advbulkedit');?></option>
				<option value="notcon"><?php _e( 'does not contain', 'woocommerce-advbulkedit');?></option>
				<option value="start"><?php _e( 'starts with', 'woocommerce-advbulkedit');?></option>
				<option value="end"><?php _e( 'ends with', 'woocommerce-advbulkedit');?></option>
			</select>
			<input id="skuvalue" type="text"/>
			</td>
				<td><?php _e( 'Tags', 'woocommerce');?>: </td>
				<td>
					<select id='tagsparams' class="makechosen paramsvalues" data-placeholder="choose\search" multiple style="width:250px;"> <option value=""></option>';
						<?php
						$args = array(
							    'number'     => 99999,
							    'orderby'    => 'slug',
							    'order'      => 'ASC',
							    'hide_empty' => false,
							    'include'    => '',
								'fields'     => 'all'
							);

							$woo_tags = get_terms( 'product_tag', $args );

							foreach($woo_tags as $tag){
							   if(!is_object($tag)) continue;
							   if(!property_exists($tag,'term_taxonomy_id')) continue;
							   if(!property_exists($tag,'name')) continue;
							   echo '<option value="'.$tag->term_taxonomy_id.'" >'.$tag->name.'</option>';
							   /*$cat = new stdClass();
							   $cat->category_id     = $category->term_taxonomy_id;
							   $cat->term_id         = $category->term_id;
							   $cat->category_name   = $category->name;
							   $cat->category_slug   = urldecode($category->slug);
							   $cat->category_parent = $category->parent;
							   $this->categories[] = $cat;   
							   $this->cat_asoc[$cat->category_id] = $cat;*/
							};
							/*foreach($attr->values as $value)
							{
								echo '<option value="'.$value->id.'">'.$value->name.'</option>';
							}*/
						?>
					</select>
				</td>
			</tr>
			<tr class="showdescriptions"
			<?php
				$echovar = 'style="display: none"';
				if(is_array($settings))
				{
					if(isset($settings['showdescriptions']))
					{
						if($settings['showdescriptions'] == 1)
						{
							$echovar = "";
						}
					}
				}
				echo $echovar;
			?>
			>
				<td><?php _e( 'Description', 'woocommerce');?>: </td>
				<td>
				<select id="descparams">
				<option value="con"><?php _e( 'contains', 'woocommerce-advbulkedit');?></option>
				<option value="notcon"><?php _e( 'does not contain', 'woocommerce-advbulkedit');?></option>
				<option value="start"><?php _e( 'starts with', 'woocommerce-advbulkedit');?></option>
				<option value="end"><?php _e( 'ends with', 'woocommerce-advbulkedit');?></option>
			</select>
			<input id="descvalue" type="text"/>
			</td>
				<td><?php _e( 'Product Short Description', 'woocommerce');?>: </td>
				<td>
				<select id="shortdescparams">
				<option value="con"><?php _e( 'contains', 'woocommerce-advbulkedit');?></option>
				<option value="notcon"><?php _e( 'does not contain', 'woocommerce-advbulkedit');?></option>
				<option value="start"><?php _e( 'starts with', 'woocommerce-advbulkedit');?></option>
				<option value="end"><?php _e( 'ends with', 'woocommerce-advbulkedit');?></option>
			</select>
			<input id="shortdescvalue" type="text"/>
			</td>
			</tr>
			<tr class="showidstock"
			<?php
				$echovar = 'style="display: none"';
//				if(is_array($settings))
//				{
//					if(isset($settings['showidstock']))
//					{
//						if($settings['showidstock'] == 1)
//						{
//							$echovar = "";
//						}
//					}
//				}
				echo $echovar;
			?>
			>
				<td><?php _e( 'ID', 'woocommerce');?>: </td>
				<td>
				<!--<select id="descparams">
				<option value="con"><?php _e( 'contains', 'woocommerce-advbulkedit');?></option>
				<option value="notcon"><?php _e( 'does not contain', 'woocommerce-advbulkedit');?></option>
				<option value="start"><?php _e( 'starts with', 'woocommerce-advbulkedit');?></option>
				<option value="end"><?php _e( 'ends with', 'woocommerce-advbulkedit');?></option>
			</select>-->
			<input id="idvalue" type="text"/>
			</td>
				<td><?php _e( 'Stock status', 'woocommerce');?>: </td>
				<td>
				<select id="stockstatusparams">
				<option value="instock"><?php _e( 'In Stock', 'woocommerce');?></option>
				<option value="outofstock"><?php _e( 'Out of stock', 'woocommerce');?></option>
			</select>
			<!--<input id="shortdescvalue" type="text"/>-->
			</td>
			</tr>
			</tbody>
			</table><br/>
			<div style="position: relative;">
			 <input id="getproducts" class="button" type="button" value="<?php _e("Get Products","woocommerce-advbulkedit"); ?>" />&nbsp;&nbsp;
			  <label><input id="getvariations" type="checkbox" <?php 
			  	$settings = get_option('w3exabe_settings');
				if(is_array($settings))
				{
					if(isset($settings['isvariations']))
					{
						if($settings['isvariations'] == 1)
						{
							echo 'checked=checked';
						}
					}else
					{
						echo 'checked=checked';
					}
				}else
				{
					echo 'checked=checked';
				}
			  ?>/><?php _e( 'Variations', 'woocommerce');?></label>
			  
			   <input id="savechanges" class="button" type="button" value="<?php _e("Save Changes","woocommerce-advbulkedit"); ?>" />
			   <div style="display: inline-block;position: relative;width:320px;">
			  <img id="showsavetool" src="<?php echo plugin_dir_url(__FILE__);?>images/help18x18.png"/>
			<div id="savenote"> <?php _e("Changes are saved on going to a different page of products, adding products/variations or via the 'Save Changes' button","woocommerce-advbulkedit"); ?></div>
			</div>
			</div>
			<br /><br />
			<div style="position: relative;">
			 <!--<button id="bulkedit">Bulk Edit</button>-->
			 <div id="addprodarea">
				<input id="addprodbut" class="button" type="button" value="<?php
echo $this->mb_ucfirst(__( "add", "woocommerce-advbulkedit"));
?>" />
			</div>
			<div id="duplicateprodarea">
				<input id="duplicateprodbut" class="button" type="button" value="<?php
 					   _e( "Duplicate", "woocommerce-advbulkedit");
?>" />
			</div>
			<div id="deletearea">
				<input id="deletebut" class="button" type="button" value="<?php
echo $this->mb_ucfirst(__( "delete", "woocommerce-advbulkedit"));
?>" />
			</div>
			<input id="selectedit" class="button" type="button" value="<?php
_e( "Selection Manager", "woocommerce-advbulkedit");
?>" />
			<input id="bulkedit" class="button" type="button" value="<?php
echo _e( "Bulk Edit", "woocommerce-advbulkedit");
?>" />
			<div style="display: inline-block;"><?php _e( "Selected rows for bulk editing", "woocommerce-advbulkedit"); ?>:<div id="bulkeditinfo"> 0 of 0</div><!--<input id="showselectedbut" class="button" type="button" value="Show Selected" />--></div>
			</div>
			<div style="position:relative">
				<div style="width:100%;">
				    <div id="myGrid" style="width:100%;height:80vh;"></div>
				</div>
			</div>
			<div id="pagingholder" style="position:relative;">
			<input id="gotopage" class="button" type="button" value="<?php _e( "First", "woocommerce-advbulkedit"); ?>" /><input id="butprevious" class="button" type="button" value="<?php _e( "Previous", "woocommerce-advbulkedit"); ?>" /> <?php _e( "Page", "woocommerce-advbulkedit"); ?>:<input id="gotopagenumber" type="text" value="1" style="width:15px;" readonly/> 	<input id="butnext" class="button" type="button" value="<?php _e( "Next", "woocommerce-advbulkedit"); ?>" /> <?php _e( "Total records", "woocommerce-advbulkedit"); ?>: <div id="totalrecords" style="display:inline-block;padding:0px 6px;"></div><div id="totalpages" style="display:inline-block;"></div><div id="viewingwhich" style="display:inline-block;padding:0px 6px;"></div></div> <br /><br />
			<div id="revertinfo"><?php _e( "Revert to original vaue", "woocommerce-advbulkedit"); ?></div> <input id="revertcell" class="button" type="button" value="<?php _e( "Active Cell", "woocommerce-advbulkedit"); ?>" />
			<input id="revertrow" class="button" type="button" value="<?php _e( "Active Row", "woocommerce-advbulkedit"); ?>" />
			<input id="revertall" class="button" type="button" value="<?php _e( "Selected Rows", "woocommerce-advbulkedit"); ?>" />
			<br /><br /><br />
			
			<input id="settings" class="button button-primary" type="button" value="<?php _e( "Show/Hide Fields", "woocommerce-advbulkedit"); ?>" />
			<input id="customfieldsbut" class="button" type="button" value="<?php _e( "Custom Fields", "woocommerce-advbulkedit"); ?>" />
			<input id="findcustomfieldsbut" class="button" type="button" value="<?php _e( "Find Custom Fields", "woocommerce-advbulkedit"); ?>" />
			<input id="pluginsettingsbut" class="button" type="button" value="<?php _e( "Plugin Settings", "woocommerce-advbulkedit"); ?>" />
			<input id="exportproducts" class="button" type="button" value="<?php _e( "Export to CSV", "woocommerce-advbulkedit"); ?>" />
			<div id="exportinfo"></div>
			<br/><br/><br/>
			<div style="position: relative;">
			  <label><input id="linkededit" type="checkbox"/><?php _e( 'Linked editing', 'woocommerce-advbulkedit'); ?></label>
			  <div style="display: inline-block;">
			  <img id="showlinked" src="<?php echo plugin_dir_url(__FILE__);?>images/help18x18.png"/></div>
			<div id="linkednote"> <?php _e( 'Manual changes on any selected product will affect all of them', 'woocommerce-advbulkedit'); ?></div>
			</div>
			<div id="exportdialog">
			<div>
				<table cellpadding="10" cellspacing="0">
					<tr>
						<td>
							<input id="exportall" type="radio" value="0" name="exportwhat">
							<label for="exportall"><?php _e( 'All products in table', 'woocommerce-advbulkedit'); ?></label>
						</td>
					</tr>
					<tr>
						<td>
							<input id="exportsel" type="radio" value="1" name="exportwhat">
							<label for="exportsel"><?php _e( 'Selected products only', 'woocommerce-advbulkedit'); ?></label>
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Delimiter', 'woocommerce-advbulkedit'); ?>: 
							<select id="exportdelimiter">
								<option value=",">,</option>
								<option value=";">;</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<label><?php _e( 'Use real meta values', 'woocommerce-advbulkedit'); ?>: 
							<input id="userealmeta" type="checkbox"></input></label>
						</td>
					</tr>
				</table>
			</div>
			</div>
			<div id="confirmdialog">
				<div>
					<?php _e( 'Are you sure you want to continue ?', 'woocommerce-advbulkedit'); ?>
				</div>
			</div>
			<div id="addproddialog">
			</div>
			<div id="pluginsettings">
			<div style="width:100%;height:100%;">
			<br/>
			<div id="pluginsettingstab">
					<ul>
					<li><a href="#pluginsettingstab-1">Main Settings</a></li>
					<li><a href="#pluginsettingstab-2">Search Fields</a></li>
					</ul>
					
					<div id="pluginsettingstab-1">
				
				<table cellpadding="10" cellspacing="0" style="margin: 0 auto;">
					<tr>
						<td>
							<?php _e( 'Limit on product retrieval', 'woocommerce-advbulkedit'); ?>
						</td>
						<td>
							<input id="productlimit" type="text" style="width:50px;" 
							<?php
								$settings = get_option('w3exabe_settings');
								if(is_array($settings))
								{
									if(isset($settings['settlimit']))
									{		
										echo 'value="'.$settings['settlimit'].'"';
									}else
									{
										echo ' value="1000"';
									}
								}else
								{
									echo ' value="1000"';
								}
							?>
							>
						</td>
					</tr>
					<tr>
						<td width="50%" style="padding-top: 20px;">
							<label><input id="gettotalnumber" type="checkbox"
							<?php 
//						  	$settings = get_option('w3exabe_settings');
							if(is_array($settings))
							{
								if(isset($settings['settgetall']))
								{
									if($settings['settgetall'] == 1)
									{
										echo 'checked=checked';
									}
								}
							}						  ?>
							><?php _e( 'Do not retrieve total number', 'woocommerce-advbulkedit'); ?></label>
						</td>
						<td  style="padding-top: 20px;">
							/<?php _e( 'check if you have a large number of products and want to speed up the query', 'woocommerce-advbulkedit'); ?>/
						</td>
					</tr>
					<tr>
						<td width="50%" style="padding-top: 20px;">
							<label><input id="retrievevariations" type="checkbox"
							<?php 
//						  	$settings = get_option('w3exabe_settings');
							if(is_array($settings))
							{
								if(isset($settings['settgetvars']))
								{
									if($settings['settgetvars'] == 1)
									{
										echo 'checked=checked';
									}
								}
							}						  ?>
							><?php _e( 'Retrieve all variations on attribute search', 'woocommerce-advbulkedit'); ?></label>
						</td>
						<td  style="padding-top: 20px;">
							/<?php _e( 'if the parent has it', 'woocommerce-advbulkedit'); ?>/
						</td>
					</tr>
					<tr>
						<td width="50%" style="padding-top: 20px;">
							<label><input id="includechildren" type="checkbox"
							<?php 
//						  	$settings = get_option('w3exabe_settings');
							if(is_array($settings))
							{
								if(isset($settings['incchildren']))
								{
									if($settings['incchildren'] == 1)
									{
										echo 'checked=checked';
									}
								}
							}						  ?>
							><?php _e( 'Get all children of selected category on search', 'woocommerce-advbulkedit'); ?></label>
						</td>
						<td  style="padding-top: 20px;">
							
						</td>
					</tr>
					<tr>
						<td width="50%" style="padding-top: 20px;">
							<label><input id="disattributes" type="checkbox"
							<?php 
//						  	$settings = get_option('w3exabe_settings');
							if(is_array($settings))
							{
								if(isset($settings['disattributes']))
								{
									if($settings['disattributes'] == 1)
									{
										echo 'checked=checked';
									}
								}
							}						  ?>
							><?php _e( 'Disable attribute support', 'woocommerce-advbulkedit'); ?></label>
						</td>
						<td  style="padding-top: 20px;">
							
						</td>
					</tr>
					<tr>
						<td width="50%" style="padding-top: 20px;">
							<label><input id="converttoutf8" type="checkbox"
							<?php 
							$echotext = "checked=checked";
							if(is_array($settings))
							{
								if(isset($settings['converttoutf8']))
								{
									if($settings['converttoutf8'] == 0)
									{
										$echotext = "";
									}
								}
							}	
							echo $echotext;					  ?>
							><?php _e( 'Convert manually to UTF-8', 'woocommerce-advbulkedit'); ?></label>
						</td>
						<td  style="padding-top: 20px;">
							
						</td>
					</tr>
					<tr>
						<td width="50%" style="padding-top: 20px;">
							<label><input id="dontcheckusedfor" type="checkbox"
							<?php 
							$echotext = "checked=checked";
							if(is_array($settings))
							{
								if(isset($settings['dontcheckusedfor']))
								{
									if($settings['dontcheckusedfor'] == 0)
									{
										$echotext = "";
									}
								}
							}	
							echo $echotext;	 ?>
							><?php _e( 'Do not check "Used for variations" automatically', 'woocommerce-advbulkedit'); ?></label>
						</td>
						<td  style="padding-top: 20px;">
							
						</td>
					</tr>
					<tr>
						<td width="50%" style="padding-top: 20px;">
							<label><input id="calldoaction" type="checkbox"
							<?php 
							if(is_array($settings))
							{
								if(isset($settings['calldoaction']))
								{
									if($settings['calldoaction'] == 1)
									{
										echo 'checked=checked';
									}
								}
							}						  ?>
							><?php _e( 'Call woocommerce action on save', 'woocommerce-advbulkedit'); ?></label>
						</td>
						<td  style="padding-top: 20px;">
							/<?php _e( 'for better compatibility with third-party cache plugins', 'woocommerce-advbulkedit'); ?>/
						</td>
					</tr>
					<tr>
						<td width="50%" style="padding-top: 20px;">
							<label><input id="confirmsave" type="checkbox"
							<?php 
							if(is_array($settings))
							{
								if(isset($settings['confirmsave']))
								{
									if($settings['confirmsave'] == 1)
									{
										echo 'checked=checked';
									}
								}
							}						  ?>
							><?php _e( 'Require confirmation on save', 'woocommerce-advbulkedit'); ?></label>
						</td>
						<td  style="padding-top: 20px;">
						</td>
					</tr>
				</table>
				</div>
				<div id="pluginsettingstab-2">
					<table cellpadding="25" cellspacing="0" style="margin: 0 auto;width:100%;">
					<tr>
						<td width="45%" style="padding-top: 20px;">
							<label><input id="showattributes" type="checkbox"
							<?php 
							$echotext = "checked=checked";
							if(is_array($settings))
							{
								if(isset($settings['showattributes']))
								{
									if($settings['showattributes'] == 0)
									{
										$echotext = "";
									}
								}
							}	
							echo $echotext;	 ?>
							><?php _e( 'Attributes', 'woocommerce-advbulkedit'); ?></label>
						</td>
						<td width="55%" style="padding-top: 20px;">
							<label><input id="showprices" type="checkbox"
							<?php 
							$echotext = "checked=checked";
							if(is_array($settings))
							{
								if(isset($settings['showprices']))
								{
									if($settings['showprices'] == 0)
									{
										$echotext = "";
									}
								}
							}	
							echo $echotext;	 ?>
							><?php _e( 'Regular/Sale Price', 'woocommerce-advbulkedit'); ?></label>
						</td>
					</tr>
					<tr>
						<td width="45%" style="padding-top: 20px;">
							<label><input id="showskutags" type="checkbox"
							<?php 
							$echotext = "checked=checked";
							if(is_array($settings))
							{
								if(isset($settings['showskutags']))
								{
									if($settings['showskutags'] == 0)
									{
										$echotext = "";
									}
								}
							}	
							echo $echotext;	 ?>
							><?php _e( 'SKU/Tags', 'woocommerce-advbulkedit'); ?></label>
						</td>
						<td width="55%" style="padding-top: 20px;">
							<label><input id="showdescriptions" type="checkbox"
							<?php 
							$echotext = "";
							if(is_array($settings))
							{
								if(isset($settings['showdescriptions']))
								{
									if($settings['showdescriptions'] == 1)
									{
										$echotext = "checked=checked";
									}
								}
							}	
							echo $echotext;	 ?>
							><?php _e( 'Long/Short Descriptions', 'woocommerce-advbulkedit'); ?></label>
						</td>
					</tr>
					</table>
					</div>
				</div>
				</div>
			</div>
			<?php 
				$setnew = __( 'set new', 'woocommerce-advbulkedit');
				$prepend = __( 'prepend', 'woocommerce-advbulkedit');
				$append = __( 'append', 'woocommerce-advbulkedit');
				$replacetext = __( 'replace text', 'woocommerce-advbulkedit');
				$ignorecase = __( 'Ignore case', 'woocommerce-advbulkedit');
				$withtext = __( 'with text', 'woocommerce-advbulkedit');
				$delete = __( 'delete', 'woocommerce-advbulkedit');
			    echo '<script>';echo PHP_EOL;
				echo 'W3Ex.trans_setnew = "'.$setnew.'";'; echo PHP_EOL;
				echo 'W3Ex.trans_prepend = "'.$prepend.'";'; echo PHP_EOL;
				echo 'W3Ex.trans_append = "'.$append.'";'; echo PHP_EOL;
				echo 'W3Ex.trans_replacetext = "'.$replacetext.'";'; echo PHP_EOL;
				echo 'W3Ex.trans_ignorecase = "'.$ignorecase.'";'; echo PHP_EOL;
				echo 'W3Ex.trans_withtext = "'.$withtext.'";'; echo PHP_EOL;						echo 'W3Ex.trans_delete = "'.$delete.'";'; echo PHP_EOL;	
				echo 'W3Ex.trans_incbyvalue = "'.__( "increase by value", "woocommerce-advbulkedit").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_decbyvalue = "'.__( "decrease by value", "woocommerce-advbulkedit").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_incbyper = "'.__( "increase by %", "woocommerce-advbulkedit").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_decbyper = "'.__( "decrease by %", "woocommerce-advbulkedit").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_movetrash = "'.__( "Move to Trash", "woocommerce").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_delperm = "'.__( "Delete Permanently", "woocommerce").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_products = "'.__( "Products", "woocommerce").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_variations = "'.__( "Variations", "woocommerce").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_duplicate = "'.__( "Duplicate", "woocommerce-advbulkedit").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_times = "'.__( "Time(s)", "woocommerce-advbulkedit").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_add = "'.__( "add", "woocommerce-advbulkedit").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_linkednote = "'.__( "Note ! - Linked editing is turned on, all new variations will be added to all of the selected products. A large number of products * variations can cause a php timeout", "woocommerce-advbulkedit").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_attributes = "'.__( "Attributes", "woocommerce").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_select = "'.__( "Select", "woocommerce").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_bulkadd = "'.__( "Bulk Add", "woocommerce-advbulkedit").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_addsingle = "'.__( "Add Single Variation", "woocommerce-advbulkedit").'";'; echo PHP_EOL;
				echo 'W3Ex.trans_seldoesnot = "'.__( "Selected product does not have any attributes", "woocommerce-advbulkedit").'";'; echo PHP_EOL;
				echo "</script>";
			 ?>
			<div id="bulkdialog">
			<table class="custstyle-table">
				<tr data-id="post_title" style="display: table-row;">
					<td style="max-width:16% !important;">
						<?php _e( 'Title', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulkpost_title" class="bulkselect">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td>
						<input id="bulkpost_titlevalue" type="text" placeholder="Skipped (empty)" data-id="post_title" class="bulkvalue"/>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <input class="inputwithvalue" type="text"></div>
					</td>
				</tr>
				<tr data-id="post_content">
					<td>
						<?php _e( 'Description', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulkpost_content" class="bulkselect">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td>
						<textarea id="bulkpost_contentvalue" rows="1" cols="15" data-id="post_content" class="bulkvalue" placeholder="Skipped (empty)"></textarea>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <textarea class="inputwithvalue" rows="1" cols="15"></textarea></div>
					</td>
				</tr>
				<tr data-id="post_excerpt">
					<td>
						<?php _e( 'Product Short Description', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulkpost_excerpt" class="bulkselect">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td>
						<textarea id="bulkpost_excerptvalue" rows="1" cols="15" data-id="post_excerpt" class="bulkvalue" placeholder="Skipped (empty)"></textarea>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <textarea class="inputwithvalue" rows="1" cols="15"></textarea></div>
					</td>
				</tr>
				<tr data-id="post_name">
					<td>
						<?php _e( 'Slug', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulkpost_name" class="bulkselect">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td>
						<textarea id="bulkpost_namevalue" rows="1" cols="15" data-id="post_name" class="bulkvalue" placeholder="Skipped (empty)"></textarea>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <textarea class="inputwithvalue" rows="1" cols="15"></textarea></div>
					</td>
				</tr>
				<tr data-id="_sku">
					<td>
						<?php _e( 'SKU', 'woocommerce');?>
					</td>
					<td>
						 <select id="bulk_sku" class="bulkselect">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td>
						<input id="bulk_skuvalue" type="text" data-id="_sku" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <input class="inputwithvalue" type="text"></div>
					</td>
				</tr>
				<tr data-id="product_cat">
					<td>
						<input id="setproduct_cat" type="checkbox" class="bulkset" data-id="product_cat" data-type="customtaxh"><label for="setproduct_cat"><?php _e( 'Categories', 'woocommerce'); ?></label>
					</td>
					<td>
						 <select id="bulkaddproduct_cat" class="bulkselect">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="add"><?php _e( "add", "woocommerce-advbulkedit"); ?></option>
							<option value="remove"><?php _e( "remove", "woocommerce-advbulkedit"); ?></option>
						</select>
					</td>
					<td>
						 <select id="bulkproduct_cat" class="makechosen catselset" style="width:250px;" data-placeholder="select category" multiple >
						 <option value=""></option>
						<?php
							$cats = $this->categories;
							$newcats = array();
							$cats_asoc = $this->cat_asoc;
							$depth = array();

						    foreach($cats as $cat)
							{
								if($cat->category_parent == 0)
								{
									$depth[$cat->term_id] = 0;
									$newcats[] = $cat;
								}
							}
							foreach($cats as $cat)
							{
								if($cat->category_parent == 0) continue;
								{
			//						if(!isset($options[$cat->category_id]))
									{
										if(!isset($depth[$cat->term_id]))
										{
											$loop = true;
											$counter = 0;
											while($loop && ($counter < 1000))
											{
												foreach($cats as $catin)
												{
													if($catin->category_parent == 0)
													   continue;
													if(isset($depth[$catin->category_parent]))
													{
														$newdepth = $depth[$catin->category_parent];
														$newdepth++;
														if(!isset($depth[$catin->term_id]))
														{
															$depth[$catin->term_id] = $newdepth;
															for($i = 0; $i < count($newcats); $i++)
															{
																$catins = $newcats[$i];
																if($catins->term_id == $catin->category_parent)
																{
																	array_splice($newcats, $i+1, 0,array($catin));
																	break;
																}
															}
														}

														if($catin->term_id == $cat->term_id)
														{
															$loop = false;
															break;
														}
													}
												}
												$counter++;
											}
										}
									}
								}
								
							}
							if(count($newcats) == count($cats))
							{
								foreach($newcats as $catin)
								{
									$depthstring = '';
									if(isset($depth[$catin->term_id]))
									{
										$depthn = (int)$depth[$catin->term_id];
										if($depthn < 15)
										{
											while($depthn > 0)
											{
												$depthstring = $depthstring.'&nbsp;&nbsp;&nbsp;';
												$depthn--;
											}
											
										}
									}
									echo '<option value="'.$catin->term_id.'" >'.$depthstring.$catin->category_name.'</option>';
								}
							}else
							{
								foreach($cats as $catin)
								{
									echo '<option value="'.$catin->term_id.'" >'.$catin->category_name.'</option>';
								}
							}
//						    foreach($this->categories as $category)
//							{
//									echo '<option value="'.$category->term_id.'" >'.$category->category_name.'</option>';
//								
//							}
					
						?>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="product_tag">
					<td>
						<?php _e( 'Tags', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulkproduct_tag" class="bulkselect" data-id="product_tag">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
							<option value="delete"><?php echo $delete; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td>
						<input id="bulkproduct_tagvalue" type="text" placeholder="Skipped (empty)" data-id="product_tag" class="bulkvalue"/>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <input class="inputwithvalue" type="text"></div>
					</td>
				</tr>
				<tr data-id="_regular_price">
					<td>
						<?php _e( 'Regular Price', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_regular_price">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="incvalue"><?php _e( "increase by value", "woocommerce-advbulkedit"); ?></option>
							<option value="decvalue"><?php _e( "decrease by value", "woocommerce-advbulkedit"); ?></option>
							<option value="incpercent"><?php _e( "increase by %", "woocommerce-advbulkedit"); ?></option>
							<option value="decpercent"><?php _e( "decrease by %", "woocommerce-advbulkedit"); ?></option>
						</select>
					</td>
					<td>
						<input id="bulk_regular_pricevalue" type="text" data-id="_regular_price" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_sale_price">
					<td>
						<?php _e( 'Sale Price', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_sale_price" data-id="_sale_price">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="incvalue"><?php _e( "increase by value", "woocommerce-advbulkedit"); ?></option>
							<option value="decvalue"><?php _e( "decrease by value", "woocommerce-advbulkedit"); ?></option>
							<option value="incpercent"><?php _e( "increase by %", "woocommerce-advbulkedit"); ?></option>
							<option value="decpercent"><?php _e( "decrease by %", "woocommerce-advbulkedit"); ?></option>
							<option value="decvaluereg"><?php _e( "decrease by value", "woocommerce-advbulkedit"); ?> (from reg.)</option>
							<option value="decpercentreg"><?php _e( "decrease by %", "woocommerce-advbulkedit"); ?> (from reg.)</option>
							<option value="delete"><?php echo $delete; ?></option>
						</select>
					</td>
					<td>
						<input id="bulk_sale_pricevalue" type="text" data-id="_sale_price" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						 <input type="checkbox" id="saleskip"><label id="saleskiplabel" for="saleskip"> Skip products that have a sale price</label>
					</td>
				</tr>
				<tr data-id="_tax_status">
					<td>
						<input id="set_tax_status" type="checkbox" class="bulkset" data-id="_tax_status"><label for="set_tax_status"><?php _e( 'Tax Status', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulk_tax_status">
							<option value="Taxable">Taxable</option>
							<option value="Shipping only">Shipping only</option>
							<option value="None">None</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_tax_class">
					<td>
						<input id="set_tax_class" type="checkbox" class="bulkset" data-id="_tax_class"><label for="set_tax_class"><?php _e( 'Tax class', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulk_tax_class">
							<option value="Standard">Standard</option>
							<option value="Reduced Rate">Reduced Rate</option>
							<option value="Zero Rate">Zero Rate</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_weight">
					<td>
						<?php _e( 'Weight', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_weight" data-id="_weight">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="incvalue"><?php _e( "increase by value", "woocommerce-advbulkedit"); ?></option>
							<option value="decvalue"><?php _e( "decrease by value", "woocommerce-advbulkedit"); ?></option>
							<option value="incpercent"><?php _e( "increase by %", "woocommerce-advbulkedit"); ?></option>
							<option value="decpercent"><?php _e( "decrease by %", "woocommerce-advbulkedit"); ?></option>
							<option value="delete"><?php echo $delete; ?></option>
						</select>
					</td>
					<td>
						<input id="bulk_weightvalue" type="text" data-id="_weight" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_height">
					<td>
						<?php _e( 'Height', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_height" data-id="_height">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="incvalue"><?php _e( "increase by value", "woocommerce-advbulkedit"); ?></option>
							<option value="decvalue"><?php _e( "decrease by value", "woocommerce-advbulkedit"); ?></option>
							<option value="incpercent"><?php _e( "increase by %", "woocommerce-advbulkedit"); ?></option>
							<option value="decpercent"><?php _e( "decrease by %", "woocommerce-advbulkedit"); ?></option>
							<option value="delete"><?php echo $delete; ?></option>
						</select>
					</td>
					<td>
						<input id="bulk_heightvalue" type="text" data-id="_height" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_width">
					<td>
						<?php _e( 'Width', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_width" data-id="_width">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="incvalue"><?php _e( "increase by value", "woocommerce-advbulkedit"); ?></option>
							<option value="decvalue"><?php _e( "decrease by value", "woocommerce-advbulkedit"); ?></option>
							<option value="incpercent"><?php _e( "increase by %", "woocommerce-advbulkedit"); ?></option>
							<option value="decpercent"><?php _e( "decrease by %", "woocommerce-advbulkedit"); ?></option>
							<option value="delete"><?php echo $delete; ?></option>
						</select>
					</td>
					<td>
						<input id="bulk_widthvalue" type="text" data-id="_width" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_length">
					<td>
						<?php _e( 'Length', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_length" data-id="_length">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="incvalue"><?php _e( "increase by value", "woocommerce-advbulkedit"); ?></option>
							<option value="decvalue"><?php _e( "decrease by value", "woocommerce-advbulkedit"); ?></option>
							<option value="incpercent"><?php _e( "increase by %", "woocommerce-advbulkedit"); ?></option>
							<option value="decpercent"><?php _e( "decrease by %", "woocommerce-advbulkedit"); ?></option>
							<option value="delete"><?php echo $delete; ?></option>
						</select>
					</td>
					<td>
						<input id="bulk_lengthvalue" type="text" data-id="_length" class="bulkvalue" placeholder="Skipped (empty)" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_stock">
					<td>
						<?php _e( 'Stock Qty', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_stock" data-id="_stock">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="incvalue"><?php _e( "increase by value", "woocommerce-advbulkedit"); ?></option>
							<option value="decvalue"><?php _e( "decrease by value", "woocommerce-advbulkedit"); ?></option>
							<option value="delete"><?php echo $delete; ?></option>
						</select>
					</td>
					<td>
						<input id="bulk_stockvalue" type="text" data-id="_stock" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_stock_status">
					<td>
						<input id="set_stock_status" type="checkbox" class="bulkset" data-id="_stock_status"><label for="set_stock_status"><?php _e( 'Stock status', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulk_stock_status">
							<option value="instock">In stock</option>
							<option value="outofstock">Out of stock</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_manage_stock">
					<td>
						<input id="set_manage_stock" type="checkbox" class="bulkset" data-id="_manage_stock"><label for="set_manage_stock"><?php _e( 'Manage Stock', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulk_manage_stock">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_backorders">
					<td>
						<input id="set_backorders" type="checkbox" class="bulkset" data-id="_backorders"><label for="set_backorders"><?php _e( 'Allow Backorders?', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulk_backorders">
							<option value="Do not allow">Do not allow</option>
							<option value="Allow but notify">Allow but notify</option>
							<option value="Allow">Allow</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_sold_individually">
					<td>
						<input id="set_sold_individually" type="checkbox" class="bulkset" data-id="_sold_individually"><label for="set_sold_individually"><?php _e( 'Sold Individually', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulk_sold_individually">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="product_shipping_class">
					<td>
						<input id="setproduct_shipping_class" type="checkbox" class="bulkset" data-id="product_shipping_class" data-type="customtaxh"><label for="setproduct_shipping_class"><?php _e( 'Shipping class', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulkproduct_shipping_class" class="makechosen catselset" style="width:250px;" data-placeholder="select">
						 <option value="">none</option>
						<?php
							//categories
						$args = array(
						    'number'     => 99999,
						    'orderby'    => 'slug',
						    'order'      => 'ASC',
						    'hide_empty' => false,
						    'include'    => '',
							'fields'     => 'all'
						);

						$woo_categories = get_terms( 'product_shipping_class', $args );
						foreach($woo_categories as $category){
						    if(!is_object($category)) continue;
						    if(!property_exists($category,'name')) continue;
						    if(!property_exists($category,'term_id')) continue;
						   	echo '<option value="'.$category->term_id.'" >'.$category->name.'</option>';
						};
						?>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_purchase_note">
					<td>
						<?php _e( 'Purchase Note', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_purchase_note" class="bulkselect" data-id="_purchase_note">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td>
						<textarea id="bulk_purchase_notevalue" rows="1" cols="15" data-id="_purchase_note" class="bulkvalue" placeholder="Skipped (empty)"></textarea>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <textarea class="inputwithvalue" rows="1" cols="15"></textarea></div>
					</td>
				</tr>
				<tr data-id="post_status">
					<td>
						<input id="setpost_status" type="checkbox" class="bulkset" data-id="post_status"><label for="setpost_status"><?php _e( 'Status', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulkpost_status">
							<option value="publish">Publish</option>
							<option value="draft">Draft</option>
							<option value="private">Private</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_visibility">
					<td>
						<input id="set_visibility" type="checkbox" class="bulkset" data-id="_visibility"><label for="set_visibility"><?php _e( 'Catalog visibility:', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulk_visibility">
							<option value="Catalog/search">Catalog/search</option>
							<option value="Catalog">Catalog</option>
							<option value="Search">Search</option>
							<option value="Hidden">Hidden</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_upsell_ids">
					<td>
						<?php _e( 'Up-Sells', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_upsell_ids" class="bulkselect">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td>
						<input id="bulk_upsell_idsvalue" type="text" data-id="_upsell_ids" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <input class="inputwithvalue" type="text"></div>
					</td>
				</tr>
				<tr data-id="_crosssell_ids">
					<td>
						<?php _e( 'Cross-Sells', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_crosssell_ids" class="bulkselect">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td>
						<input id="bulk_crosssell_idsvalue" type="text" data-id="_crosssell_ids" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <input class="inputwithvalue" type="text"></div>
					</td>
				</tr>
				<tr data-id="_downloadable">
					<td>
						<input id="set_downloadable" type="checkbox" class="bulkset" data-id="_downloadable"><label for="set_downloadable"><?php _e( 'Downloadable', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulk_downloadable">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_virtual">
					<td>
						<input id="set_virtual" type="checkbox" class="bulkset" data-id="_virtual"><label for="set_virtual"><?php _e( 'Virtual', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulk_virtual">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_download_limit">
					<td>
						<?php _e( 'Download Limit', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_download_limit" data-id="_download_limit">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="incvalue"><?php _e( "increase by value", "woocommerce-advbulkedit"); ?></option>
							<option value="decvalue"><?php _e( "decrease by value", "woocommerce-advbulkedit"); ?></option>
							<option value="incpercent"><?php _e( "increase by %", "woocommerce-advbulkedit"); ?></option>
							<option value="decpercent"><?php _e( "decrease by %", "woocommerce-advbulkedit"); ?></option>
							<option value="delete">set unlimited (<?php echo $delete; ?>)</option>
						</select>
					</td>
					<td>
						<input id="bulk_download_limitvalue" type="text" data-id="_download_limit" class="bulkvalue" placeholder="Skipped (empty)" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_download_expiry">
					<td>
						<?php _e( 'Download Expiry', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_download_expiry" data-id="_download_expiry">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="incvalue"><?php _e( "increase by value", "woocommerce-advbulkedit"); ?></option>
							<option value="decvalue"><?php _e( "decrease by value", "woocommerce-advbulkedit"); ?></option>
							<option value="incpercent"><?php _e( "increase by %", "woocommerce-advbulkedit"); ?></option>
							<option value="decpercent"><?php _e( "decrease by %", "woocommerce-advbulkedit"); ?></option>
							<option value="delete">set unlimited (<?php echo $delete; ?>)</option>
						</select>
					</td>
					<td>
						<input id="bulk_download_expiryvalue" type="text" data-id="_download_expiry" class="bulkvalue" placeholder="Skipped (empty)" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_download_type">
					<td>
						<input id="set_download_type" type="checkbox" class="bulkset" data-id="_download_type"><label for="set_download_type"><?php _e( 'Download Type', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulk_download_type">
							<option value="Standard">Standard</option>
							<option value="Application">Application</option>
							<option value="Music">Music</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_featured">
					<td>
						<input id="set_featured" type="checkbox" class="bulkset" data-id="_featured"><label for="set_featured"><?php _e( 'Featured', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulk_featured">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_product_url">
					<td>
						<?php _e( 'Product URL', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_product_url" class="bulkselect" data-id="_product_url">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
							<option value="delete"><?php echo $delete; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td>
						<input id="bulk_product_urlvalue" type="text" data-id="_product_url" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <input class="inputwithvalue" type="text"></div>
					</td>
				</tr>
				<tr data-id="_button_text">
					<td>
						<?php _e( 'Button text', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulk_button_text" class="bulkselect" data-id="_button_text">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="prepend"><?php echo $prepend; ?></option>
							<option value="append"><?php echo $append; ?></option>
							<option value="replace"><?php echo $replacetext; ?></option>
							<option value="delete"><?php echo $delete; ?></option>
						</select>
						<label class="labelignorecase" style="display:none;">
						<input class="inputignorecase" type="checkbox">
						<?php echo $ignorecase; ?></label>
					</td>
					<td>
						<input id="bulk_button_textvalue" type="text" data-id="_button_text" class="bulkvalue" placeholder="Skipped (empty)"/>
					</td>
					<td>
						<div class="divwithvalue" style="display:none;"><?php echo $withtext; ?> <input class="inputwithvalue" type="text"></div>
					</td>
				</tr>
				<tr data-id="menu_order">
					<td>
						<?php _e( 'Menu order', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="bulkmenu_order" data-id="menu_order">
							<option value="new"><?php echo $setnew; ?></option>
							<option value="incvalue"><?php _e( "increase by value", "woocommerce-advbulkedit"); ?></option>
							<option value="decvalue"><?php _e( "decrease by value", "woocommerce-advbulkedit"); ?></option>
						</select>
					</td>
					<td>
						<input id="bulkmenu_ordervalue" type="text" data-id="menu_order" class="bulkvalue" placeholder="Skipped (empty)" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="product_type">
					<td>
						<input id="setproduct_type" type="checkbox" class="bulkset" data-id="product_type" data-type="customtaxh"><label for="setproduct_type"><?php _e( 'Product Type', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulkproduct_type" class="makechosen catselset" style="width:250px;" data-placeholder="select">
						<?php
							//categories
						$args = array(
						    'number'     => 99999,
						    'orderby'    => 'slug',
						    'order'      => 'ASC',
						    'hide_empty' => false,
						    'include'    => '',
							'fields'     => 'all'
						);

						$woo_categories = get_terms( 'product_type', $args );
						foreach($woo_categories as $category){
						    if(!is_object($category)) continue;
						    if(!property_exists($category,'name')) continue;
						    if(!property_exists($category,'term_id')) continue;
						   	echo '<option value="'.$category->term_id.'" >'.$category->name.'</option>';
						};
						?>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="comment_status">
					<td>
						<input id="setcomment_status" type="checkbox" class="bulkset" data-id="comment_status"><label for="setcomment_status"><?php _e( 'Enable reviews', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulkcomment_status">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="grouped_items">
					<td>
						<input id="setgrouped_items" type="checkbox" class="bulkset" data-id="grouped_items" data-type="customtaxh"><label for="setgrouped_items"><?php _e( 'Grouping', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="bulkgrouped_items" class="makechosen catselset" style="width:250px;" data-placeholder="select">
						 <option value="0"> Choose a grouped product...</option>
						<?php
						$argsgr = array(
							'posts_per_page'   => 500,
							'post_type' => 'product',
							'product_type' => 'grouped'
						);
						$query = new WP_Query( $argsgr );

						// The Loop
						while ( $query->have_posts() ) {
							$query->the_post();
							echo '<option value="'.$query->post->ID.'" >'.get_the_title().'</option>';
						}
						wp_reset_postdata();
						?>
						<?php
							//categories
					/*	$args = array(
						    'number'     => 99999,
						    'orderby'    => 'slug',
						    'order'      => 'ASC',
						    'hide_empty' => false,
						    'include'    => '',
							'fields'     => 'all'
						);

						$woo_categories = get_terms( 'product_type', $args );
						foreach($woo_categories as $category){
						    if(!is_object($category)) continue;
						    if(!property_exists($category,'name')) continue;
						    if(!property_exists($category,'term_id')) continue;
						   	echo '<option value="'.$category->term_id.'" >'.$category->name.'</option>';
						};*/
						?>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
			</table>
			<br/>
			</div>
			
			<div id="selectdialog">
			<div id="selectdiv">
			<select id="selectselect">
				<option value="select"><?php _e('select','woocommerce-advbulkedit'); ?></option>
				<option value="deselect"><?php _e('deselect','woocommerce-advbulkedit'); ?></option>
			</select>
			<select id="selectproduct">
				<option value="prodvar"><?php _e('products and variations','woocommerce-advbulkedit'); ?></option>
				<option value="prod"><?php _e('products only','woocommerce-advbulkedit'); ?></option>
				<option value="var"><?php _e('variations only','woocommerce-advbulkedit'); ?></option>
			</select>
			<?php _e('which meet','woocommerce-advbulkedit'); ?>
			<select id="selectany">
				<option value="any"><?php _e('any of the search criteria','woocommerce-advbulkedit'); ?></option>
				<option value="all"><?php _e('all of the search criteria','woocommerce-advbulkedit'); ?></option>
			</select>
			</div>
			<hr />
			<?php 
				$t_contains = __( 'contains', 'woocommerce-advbulkedit');
				$t_doesnot = __( 'does not contain', 'woocommerce-advbulkedit');
				$t_starts = __( 'starts with', 'woocommerce-advbulkedit');
				$t_ends = __( 'ends with', 'woocommerce-advbulkedit');
				$t_isempty = __( 'field is empty', 'woocommerce-advbulkedit');
				 echo '<script>'; echo PHP_EOL;
				echo 'W3Ex.trans_contains = "'.$t_contains.'";'; echo PHP_EOL;
				echo 'W3Ex.trans_doesnot = "'.$t_doesnot.'";'; echo PHP_EOL;
				echo 'W3Ex.trans_starts = "'.$t_starts.'";'; echo PHP_EOL;
				echo 'W3Ex.trans_ends = "'.$t_ends.'";'; echo PHP_EOL;
				echo 'W3Ex.trans_isempty = "'.$t_isempty.'";'; echo PHP_EOL;
//				echo 'W3Ex.trans_withtext = "'.$withtext.'";'; echo PHP_EOL;			
//				echo 'W3Ex.trans_delete = "'.$delete.'";'; echo PHP_EOL;			
				echo "</script>";
			 ?>
			<table class="custstyle-table">
				<tr data-id="post_title" style="display: table-row;">
					<td style="width:30% !important;">
						<?php _e( 'Title', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="selectpost_title" class="selectselect" data-id="post_title">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
						</select>
					</td>
					<td>
						<input id="selectpost_titlevalue" type="text" placeholder="Skipped (empty)" data-id="post_title" class="selectvalue"/>
					</td>
					<td>
						<label><input data-id="post_title" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="post_content">
					<td>
						<?php _e( 'Description', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="selectpost_content" class="selectselect" data-id="post_content">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<textarea cols="15" rows="1" id="selectpost_contentvalue" placeholder="Skipped (empty)" data-id="post_content" class="selectvalue"></textarea >
					</td>
					<td>
						<label><input data-id="post_content" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="post_excerpt">
					<td>
						<?php _e( 'Product Short Description', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="selectpost_excerpt" class="selectselect" data-id="post_excerpt">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<textarea cols="15" rows="1" id="selectpost_excerptvalue" placeholder="Skipped (empty)" data-id="post_excerpt" class="selectvalue"></textarea >
					</td>
					<td>
						<label><input data-id="post_excerpt" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="post_name">
					<td>
						<?php _e( 'Slug', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="selectpost_name" class="selectselect" data-id="post_name">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="iscon">is contained in</option>
						</select>
					</td>
					<td>
						<textarea cols="15" rows="1" id="selectpost_namevalue" placeholder="Skipped (empty)" data-id="post_name" class="selectvalue"></textarea >
					</td>
					<td>
						<label><input data-id="post_name" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="_sku">
					<td>
						<?php _e( 'SKU', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_sku" class="selectselect" data-id="_sku">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_skuvalue" type="text" placeholder="Skipped (empty)" data-id="_sku" class="selectvalue"/>
					</td>
					<td>
						<label><input data-id="_sku" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="product_cat">
					<td>
						<?php _e( 'Categories', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="selectproduct_cat" class="selectselect" data-id="product_cat">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="selectproduct_catvalue" type="text" placeholder="Skipped (empty)" data-id="product_cat" class="selectvalue"/>
					</td>
					<td>
						<label><input data-id="product_cat" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="product_tag">
					<td>
						<?php _e( 'Tags', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="selectproduct_tag" class="selectselect" data-id="product_tag">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="selectproduct_tagvalue" type="text" placeholder="Skipped (empty)" data-id="product_tag" class="selectvalue"/>
					</td>
					<td>
						<label><input data-id="product_tag" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="_regular_price">
					<td>
						<?php _e( 'Regular Price', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_regular_price" class="selectselect" data-id="_regular_price">
							<option value="more">></option>
							<option value="less"><</option>
							<option value="equal">==</option>
							<option value="moree">>=</option>
							<option value="lesse"><=</option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_regular_pricevalue" type="text" placeholder="Skipped (empty)"  data-id="_regular_price" class="selectvalue" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_sale_price">
					<td>
						<?php _e( 'Sale Price', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_sale_price" class="selectselect" data-id="_sale_price">
							<option value="more">></option>
							<option value="less"><</option>
							<option value="equal">==</option>
							<option value="moree">>=</option>
							<option value="lesse"><=</option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_sale_pricevalue" type="text" placeholder="Skipped (empty)" data-id="_sale_price" class="selectvalue" />
					</td>
					<td>
						 <!--<input type="checkbox" id="selectsaleskip"><label id="selectsaleskiplabel" for="selectsaleskip"> Skip products that have a sale price</label>-->
					</td>
				</tr>
				<tr data-id="_tax_status">
					<td>
						<input id="setsel_tax_status" type="checkbox" class="selectset" data-id="_tax_status"><label for="setsel_tax_status"><?php _e( 'Tax Status', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="select_tax_status">
							<option value="Taxable">Taxable</option>
							<option value="Shipping only">Shipping only</option>
							<option value="None">None</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_tax_class">
					<td>
						<input id="setsel_tax_class" type="checkbox" class="selectset" data-id="_tax_class"><label for="setsel_tax_class"><?php _e( 'Tax class', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="select_tax_class">
							<option value="Standard">Standard</option>
							<option value="Reduced Rate">Reduced Rate</option>
							<option value="Zero Rate">Zero Rate</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_weight">
					<td>
						<?php _e( 'Weight', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_weight" class="selectselect" data-id="_weight">
							<option value="more">></option>
							<option value="less"><</option>
							<option value="equal">==</option>
							<option value="moree">>=</option>
							<option value="lesse"><=</option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_weightvalue" type="text" placeholder="Skipped (empty)" data-id="_weight" class="selectvalue" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_height" class="selectselect">
					<td>
						<?php _e( 'Height', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_height" class="selectselect" data-id="_height">
							<option value="more">></option>
							<option value="less"><</option>
							<option value="equal">==</option>
							<option value="moree">>=</option>
							<option value="lesse"><=</option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_heightvalue" type="text" placeholder="Skipped (empty)" data-id="_height" class="selectvalue" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_width">
					<td>
						<?php _e( 'Width', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_width" class="selectselect" data-id="_width">
							<option value="more">></option>
							<option value="less"><</option>
							<option value="equal">==</option>
							<option value="moree">>=</option>
							<option value="lesse"><=</option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_widthvalue" type="text" placeholder="Skipped (empty)" data-id="_width" class="selectvalue" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_length">
					<td>
						<?php _e( 'Length', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_length" class="selectselect" data-id="_length">
							<option value="more">></option>
							<option value="less"><</option>
							<option value="equal">==</option>
							<option value="moree">>=</option>
							<option value="lesse"><=</option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_lengthvalue" type="text" placeholder="Skipped (empty)" data-id="_length" class="selectvalue" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_stock">
					<td>
						<?php _e( 'Stock Qty', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_stock" class="selectselect" data-id="_stock">
							<option value="more">></option>
							<option value="less"><</option>
							<option value="equal">==</option>
							<option value="moree">>=</option>
							<option value="lesse"><=</option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_stockvalue" type="text" placeholder="Skipped (empty)" data-id="_stock" class="selectvalue" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_stock_status">
					<td>
						<input id="setsel_stock_status" type="checkbox" class="selectset" data-id="_stock_status"><label for="setsel_stock_status"><?php _e( 'Stock status', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="select_stock_status">
							<option value="instock">In stock</option>
							<option value="outofstock">Out of stock</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_manage_stock">
					<td>
						<input id="setsel_manage_stock" type="checkbox" class="selectset" data-id="_manage_stock"><label for="setsel_manage_stock"><?php _e( 'Manage Stock', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="select_manage_stock">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_backorders">
					<td>
						<input id="setsel_backorders" type="checkbox" class="selectset" data-id="_backorders"><label for="setsel_backorders"><?php _e( 'Allow Backorders?', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="select_backorders">
							<option value="Do not allow">Do not allow</option>
							<option value="Allow but notify">Allow but notify</option>
							<option value="Allow">Allow</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_sold_individually">
					<td>
						<input id="setsel_sold_individually" type="checkbox" class="selectset" data-id="_sold_individually"><label for="setsel_sold_individually"><?php _e( 'Sold Individually', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="select_sold_individually">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="product_shipping_class">
					<td>
						<?php _e( 'Shipping class', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="selectproduct_shipping_class" class="selectselect" data-id="product_shipping_class">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="selectproduct_shipping_classvalue" type="text" placeholder="Skipped (empty)" data-id="product_shipping_class" class="selectvalue"/>
					</td>
					<td>
						<label><input data-id="product_shipping_class" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="_purchase_note">
					<td>
						<?php _e( 'Purchase Note', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_purchase_note" class="selectselect" data-id="_purchase_note">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<textarea cols="15" rows="1" id="select_purchase_notevalue" placeholder="Skipped (empty)" data-id="_purchase_note" class="selectvalue"></textarea >
					</td>
					<td>
						<label><input data-id="_purchase_note" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="post_status">
					<td>
						<input id="setselpost_status" type="checkbox" class="selectset" data-id="post_status"><label for="setselpost_status"><?php _e( 'Status', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="selectpost_status">
							<option value="publish">Publish</option>
							<option value="draft">Draft</option>
							<option value="private">Private</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_visibility">
					<td>
						<input id="setsel_visibility" type="checkbox" class="selectset" data-id="_visibility"><label for="setsel_visibility"><?php _e( 'Catalog visibility:', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="select_visibility">
							<option value="Catalog/search">Catalog/search</option>
							<option value="Catalog">Catalog</option>
							<option value="Search">Search</option>
							<option value="Hidden">Hidden</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_upsell_ids">
					<td>
						<?php _e( 'Up-Sells', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_upsell_ids" class="selectselect" data-id="_upsell_ids">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_upsell_idsvalue" type="text" placeholder="Skipped (empty)" data-id="_upsell_ids" class="selectvalue"/>
					</td>
					<td>
						<label><input data-id="_upsell_ids" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="_crosssell_ids">
					<td>
						<?php _e( 'Cross-Sells', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_crosssell_ids" class="selectselect" data-id="_crosssell_ids">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_crosssell_idsvalue" type="text" placeholder="Skipped (empty)" data-id="_crosssell_ids" class="selectvalue"/>
					</td>
					<td>
						<label><input data-id="_crosssell_ids" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="_downloadable">
					<td>
						<input id="setsel_downloadable" type="checkbox" class="selectset" data-id="_downloadable"><label for="setsel_downloadable"><?php _e( 'Downloadable', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="select_downloadable">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_virtual">
					<td>
						<input id="setsel_virtual" type="checkbox" class="selectset" data-id="_virtual"><label for="setsel_virtual"><?php _e( 'Virtual', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="select_virtual">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_download_limit">
					<td>
						<?php _e( 'Download Limit', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_download_limit" class="selectselect" data-id="_download_limit">
							<option value="more">></option>
							<option value="less"><</option>
							<option value="equal">==</option>
							<option value="moree">>=</option>
							<option value="lesse"><=</option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_download_limitvalue" type="text" placeholder="Skipped (empty)" data-id="_download_limit" class="selectvalue" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_download_expiry">
					<td>
						<?php _e( 'Download Expiry', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_download_expiry" class="selectselect" data-id="_download_expiry">
						<option value="more">></option>
							<option value="less"><</option>
							<option value="equal">==</option>
							<option value="moree">>=</option>
							<option value="lesse"><=</option>
							<option value="empty"><?php echo $t_isempty; ?> (unlimited)</option>
						</select>
					</td>
					<td>
						<input id="select_download_expiryvalue" type="text" placeholder="Skipped (empty)" data-id="_download_expiry" class="selectvalue" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_download_type">
					<td>
						<input id="setsel_download_type" type="checkbox" class="selectset" data-id="_download_type"><label for="setsel_download_type"><?php _e( 'Download Type', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="select_download_type">
							<option value="Standard">Standard</option>
							<option value="Application">Application</option>
							<option value="Music">Music</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_featured">
					<td>
						<input id="setsel_featured" type="checkbox" class="selectset" data-id="_featured"><label for="setsel_featured"><?php _e( 'Featured', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="select_featured">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="_product_url">
					<td>
						<?php _e( 'Product URL', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_product_url" class="selectselect" data-id="_product_url">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_product_urlvalue" type="text" placeholder="Skipped (empty)" data-id="_product_url" class="selectvalue"/>
					</td>
					<td>
						<label><input data-id="_product_url" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="_button_text">
					<td>
						<?php _e( 'Button text', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="select_button_text" class="selectselect" data-id="_button_text">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_starts; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="select_button_textvalue" type="text" placeholder="Skipped (empty)" data-id="_button_text" class="selectvalue"/>
					</td>
					<td>
						<label><input data-id="_button_text" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="menu_order">
					<td>
						<?php _e( 'Menu order', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="selectmenu_order" class="selectselect" data-id="menu_order">
							<option value="more">></option>
							<option value="less"><</option>
							<option value="equal">==</option>
							<option value="moree">>=</option>
							<option value="lesse"><=</option>
							<option value="empty"><?php echo $t_isempty; ?> (unlimited)</option>
						</select>
					</td>
					<td>
						<input id="selectmenu_ordervalue" type="text" placeholder="Skipped (empty)" data-id="menu_order" class="selectvalue" />
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="product_type">
					<td>
						<?php _e( 'Product Type', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="selectproduct_type" class="selectselect" data-id="product_type">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_ends; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="selectproduct_typevalue" type="text" placeholder="Skipped (empty)" data-id="product_type" class="selectvalue"/>
					</td>
					<td>
						<label><input data-id="product_type" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
				<tr data-id="comment_status">
					<td>
						<input id="setselcomment_status" type="checkbox" class="selectset" data-id="comment_status"><label for="setselcomment_status"><?php _e( 'Enable reviews', 'woocommerce'); ?></label>
					</td>
					<td>
						
					</td>
					<td>
						 <select id="selectcomment_status">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
					<td>
						
					</td>
				</tr>
				<tr data-id="grouped_items">
					<td>
						<?php _e( 'Grouping', 'woocommerce'); ?>
					</td>
					<td>
						 <select id="selectgrouped_items" class="selectselect" data-id="grouped_items">
							<option value="con"><?php echo $t_contains; ?></option>
							<option value="notcon"><?php echo $t_doesnot; ?></option>
							<option value="start"><?php echo $t_ends; ?></option>
							<option value="end"><?php echo $t_ends; ?></option>
							<option value="empty"><?php echo $t_isempty; ?></option>
						</select>
					</td>
					<td>
						<input id="selectgrouped_itemsvalue" type="text" placeholder="Skipped (empty)" data-id="grouped_items" class="selectvalue"/>
					</td>
					<td>
						<label><input data-id="grouped_items" class="selectifignorecase" type="checkbox"> <?php echo $ignorecase; ?></label>
					</td>
				</tr>
			</table>
			<br/>
			</div>
			
		<!--	
		settings dialog
		-->
		
			<div id="settingsdialog">
			<table class="settings-table" >
				<tr>
					
					<td>
						<input id="dimage" class="dsettings" data-id="_thumbnail_id" type="checkbox"><label for="dimage"> <?php _e( 'Image', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dimage_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="d_product_image_gallery" class="dsettings" data-id="_product_image_gallery" type="checkbox"><label for="d_product_image_gallery"> <?php _e( 'Product Gallery', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="d_product_image_gallery_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					
					<td>
						<input id="dmenu_order" class="dsettings" data-id="menu_order" type="checkbox"><label for="dmenu_order"> <?php _e( 'Menu order', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dmenu_order_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dfeatured" class="dsettings" data-id="_featured" type="checkbox"><label for="dfeatured"> <?php _e( 'Featured', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dfeatured_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dprodcutdescription" class="dsettings" data-id="post_content" type="checkbox"><label for="dprodcutdescription"> <?php _e( 'Description', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dprodcutdescription_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dprodcutexcerpt" class="dsettings" data-id="post_excerpt" type="checkbox"><label for="dprodcutexcerpt"> <?php _e( 'Product Short Description', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dprodcutexcerpt_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dpost_name" class="dsettings" data-id="post_name" type="checkbox"><label for="dpost_name"> <?php _e( 'Slug', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dpost_name_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dpost_date" class="dsettings" data-id="post_date" type="checkbox"><label for="dpost_date"> <?php _e( 'Publish Date', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dpost_date_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dsku" class="dsettings" data-id="_sku" type="checkbox"><label for="dsku"> <?php _e( 'SKU', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dsku_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dproduct_cat" class="dsettings" data-id="product_cat" type="checkbox"><label for="dproduct_cat"> <?php _e( 'Categories', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dproduct_cat_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dproduct_tag" class="dsettings" data-id="product_tag" type="checkbox"><label for="dproduct_tag"> <?php _e( 'Tags', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dproduct_tag_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dproduct_shipping_class" class="dsettings" data-id="product_shipping_class" type="checkbox"><label for="dproduct_shipping_class"> <?php _e( 'Shipping class', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dproduct_shipping_class_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dregprice" class="dsettings" data-id="_regular_price" type="checkbox"><label for="dregprice"> <?php _e( 'Regular Price', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dregprice_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dsaleprice" class="dsettings" data-id="_sale_price" type="checkbox"><label for="dsaleprice"> <?php _e( 'Sale Price', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dsaleprice_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dsalefrom" class="dsettings" data-id="_sale_price_dates_from" type="checkbox"><label for="dsalefrom"> <?php _e( 'Sale start date:', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dsalefrom_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dsaleto" class="dsettings" data-id="_sale_price_dates_to" type="checkbox"><label for="dsaleto"> <?php _e( 'Sale end date:', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dsaleto_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dtaxstatus" class="dsettings" data-id="_tax_status" type="checkbox"><label for="dtaxstatus"> <?php _e( 'Tax Status', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dtaxstatus_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dtaxclass" class="dsettings" data-id="_tax_class" type="checkbox"><label for="dtaxclass"> <?php _e( 'Tax class', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dtaxclass_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dweight" class="dsettings" data-id="_weight" type="checkbox"><label for="dweight"> <?php _e( 'Weight', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dweight_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dheight" class="dsettings" data-id="_height" type="checkbox"><label for="dheight"> <?php _e( 'Height', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dheight_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dwidth" class="dsettings" data-id="_width" type="checkbox"><label for="dwidth"> <?php _e( 'Width', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dwidth_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dlength" class="dsettings" data-id="_length" type="checkbox"><label for="dlength"> <?php _e( 'Length', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dlength_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dstockquantity" class="dsettings" data-id="_stock" type="checkbox"><label for="dstockquantity"> <?php _e( 'Stock Qty', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dstockquantity_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dstockstatus" class="dsettings" data-id="_stock_status" type="checkbox"><label for="dstockstatus"> <?php _e( 'Stock status', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dstockstatus_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dmanagestock" class="dsettings" data-id="_manage_stock" type="checkbox"><label for="dmanagestock"> <?php _e( 'Manage Stock', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dmanagestock_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dbackorders" class="dsettings" data-id="_backorders" type="checkbox"><label for="dbackorders"> <?php _e( 'Allow Backorders?', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dbackorders_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dsoldind" class="dsettings" data-id="_sold_individually" type="checkbox"><label for="dsoldind"> <?php _e( 'Sold Individually', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dsoldind_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dpurchasenote" class="dsettings" data-id="_purchase_note" type="checkbox"><label for="dpurchasenote"> <?php _e( 'Purchase Note', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dpurchasenote_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="d_grouped_items" class="dsettings" data-id="grouped_items" type="checkbox"><label for="d_grouped_items"> <?php _e( 'Grouping', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="d_grouped_items_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="d_product_adminlink" class="dsettings" data-id="_product_adminlink" type="checkbox"><label for="d_product_adminlink"> Edit in admin</label>
					</td>
					<td>
						<div>
						 <img id="d_product_adminlink_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dproductstatus" class="dsettings" data-id="post_status" type="checkbox"><label for="dproductstatus"> <?php _e( 'Status', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dproductstatus_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dcatalog" class="dsettings" data-id="_visibility" type="checkbox"><label for="dcatalog"> <?php _e( 'Catalog visibility:', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dcatalog_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="d_upsell_ids" class="dsettings" data-id="_upsell_ids" type="checkbox"><label for="d_upsell_ids"> <?php _e( 'Up-Sells', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="d_upsell_ids_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="d_crosssell_ids" class="dsettings" data-id="_crosssell_ids" type="checkbox"><label for="d_crosssell_ids"> <?php _e( 'Cross-Sells', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="d_crosssell_ids_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="ddownloadable" class="dsettings" data-id="_downloadable" type="checkbox"><label for="ddownloadable"> <?php _e( 'Downloadable', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="ddownloadable_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dvirtual" class="dsettings" data-id="_virtual" type="checkbox"><label for="dvirtual"> <?php _e( 'Virtual', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dvirtual_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="ddownexpiry" class="dsettings" data-id="_download_expiry" type="checkbox"><label for="ddownexpiry"> <?php _e( 'Download Expiry', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="ddownexpiry_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="ddownlimit" class="dsettings" data-id="_download_limit" type="checkbox"><label for="ddownlimit">  <?php _e( 'Download Limit', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="ddownlimit_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="ddownfiles" class="dsettings" data-id="_downloadable_files" type="checkbox"><label for="ddownfiles"> <?php _e( 'Downloadable Files', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="ddownfiles_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="ddowntype" class="dsettings" data-id="_download_type" type="checkbox"><label for="ddowntype"> <?php _e( 'Download Type', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="ddowntype_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="d_product_url" class="dsettings" data-id="_product_url" type="checkbox"><label for="d_product_url"> <?php _e( 'Product URL', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="d_product_url_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="d_button_text" class="dsettings" data-id="_button_text" type="checkbox"><label for="d_button_text"> <?php _e( 'Button text', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="d_button_text_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="dproduct_type" class="dsettings" data-id="product_type" type="checkbox"><label for="dproduct_type"> <?php _e( 'Product Type', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dproduct_type_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="dcomment_status" class="dsettings" data-id="comment_status" type="checkbox"><label for="dcomment_status"> <?php _e( 'Enable reviews', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="dcomment_status_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<input id="d_product_permalink" class="dsettings" data-id="_product_permalink" type="checkbox"><label for="d_product_permalink"> Product URL (permalink)</label>
					</td>
					<td>
						<div>
						 <img id="d_product_permalink_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
					<td>
						<input id="d_default_attributes" class="dsettings" data-id="_default_attributes" type="checkbox"><label for="d_default_attributes"> <?php _e( 'Default', 'woocommerce'); ?> <?php _e( 'Attributes', 'woocommerce'); ?></label>
					</td>
					<td>
						<div>
						 <img id="d_default_attributes_check" src="<?php echo $purl;?>images/tick.png" style="visibility:hidden;"/>
						</div>
					</td>
				</tr>
				<?php
					$counter = 0;
					foreach($this->attributes as $attr)
					{
						if($counter % 2 == 0)
						{
							echo '<tr><td>';
						}else
						{
							echo '<td>';
						}
						$attr_slug = "attribute_pa_".$attr->name;
						echo '<input id="d'.$attr_slug.'" class="dsettings" data-id="'.$attr_slug.'" type="checkbox"><label for="d'.$attr_slug.'"> (attr) '.$attr->label.'</label>
					</td>
					<td>
						<div>
						 <img id="d'.$attr_slug.'_check" src="'.$purl.'images/tick.png" style="visibility:hidden;"/>
						</div>';
//						echo 'W3Ex.attr_cols['.$attr->id.'] = {id:'.$attr->id.',attr:"'.$attr->label.'",value:"'.$.'"};';
						if($counter % 2 == 0)
						{
							$endrow = false;
							echo '</td>';
						}else
						{
							$endrow = true;
							echo '</td></tr>';
						}
						$counter++;
					}
				?>
			</table>
			<br/>
			</div>
			<div id="categoriesdialog">
				<div class="grouped_items">
					<ul class="categorychecklist form-no-clear clearothers">
						<li><label class="selectit"><input value="0" type="checkbox"  /> Choose a grouped product...</label></li>
					<?php
						$argsgr = array(
							'posts_per_page'   => 1000,
							'post_type' => 'product',
							'product_type' => 'grouped'
						);
						$query = new WP_Query( $argsgr );

						// The Loop
						while ( $query->have_posts() ) {
							$query->the_post();
							echo '<li><label class="selectit"><input value="'.$query->post->ID.'" type="checkbox" data-name="'.addslashes(get_the_title()).'" />'.get_the_title().'</label></li>';
						}
						wp_reset_postdata();
					?>
					</ul>
				</div>
				<div class='product_cat'>
					<?php
							$args = array(
							'descendants_and_self'  => 0,
							'selected_cats'         => false,
							'popular_cats'          => false,
							'walker'                => null,
							'taxonomy'              => 'product_cat',
							'checked_ontop'         => true
						);

						?>
					<ul class="categorychecklist form-no-clear">
							<?php wp_terms_checklist( 0, $args ); ?>
					</ul>
				</div>
				<div class='product_shipping_class'>
					<?php
							$args = array(
							'descendants_and_self'  => 0,
							'selected_cats'         => false,
							'popular_cats'          => false,
							'walker'                => null,
							'taxonomy'              => 'product_shipping_class',
							'checked_ontop'         => true
						);

					?>
					<ul class="categorychecklist form-no-clear clearothers">
							<?php wp_terms_checklist( 0, $args ); ?>
					</ul>
				</div>
				<div class='product_type'>
					<?php
							$args = array(
							'descendants_and_self'  => 0,
							'selected_cats'         => false,
							'popular_cats'          => false,
							'walker'                => null,
							'taxonomy'              => 'product_type',
							'checked_ontop'         => true
						);

					?>
					<ul class="categorychecklist form-no-clear clearothers">
							<?php wp_terms_checklist( 0, $args ); ?>
					</ul>
				</div>
				<?php
					if(is_array($this->attributes) && !empty($this->attributes))
					{
						$allattrs = '<div id="allattributeslist"><ul>';
						foreach($this->attributes as $attr)
						{
							
							echo '<div class="attribute_pa_'.$attr->name.'">';
							$allattrs.= '<li><label><input type="checkbox" data-label="'.$attr->label.'" value="attribute_pa_'.$attr->name.'">'.$attr->label.'</label></li>';
							$args = array(
								'descendants_and_self'  => 0,
								'selected_cats'         => false,
								'popular_cats'          => false,
								'walker'                => null,
								'taxonomy'              => 'pa_'.$attr->name,
								'checked_ontop'         => true
							);
							echo '<ul class="categorychecklist form-no-clear">';
								wp_terms_checklist( 0, $args );
							echo '</ul>';
							echo '</div>';
					    }
						$allattrs.= '</ul></div>';
						echo $allattrs;
					}
				?>
				<?php
					if(is_array($sel_fields) && !empty($sel_fields))
					{
						foreach($sel_fields as $key => $innerarray)
						{
							if(isset($innerarray['type']))
							{
								if($innerarray['type'] === 'customh')
								{
									if(taxonomy_exists($key))
									{
										echo '<div class="'.$key.'">';
										echo PHP_EOL;
										echo '<ul class="categorychecklist form-no-clear">';
										$args = array(
											'descendants_and_self'  => 0,
											'selected_cats'         => false,
											'popular_cats'          => false,
											'walker'                => null,
											'taxonomy'              => $key,
											'checked_ontop'         => true
										);
										wp_terms_checklist( 0, $args );
										echo '</ul></div>';
									}
								}
							}
						}
					}
				?>
			</div>
			<?php
				if(is_array($this->attributes) && !empty($this->attributes))
				{
					echo '<script>';
					foreach($this->attributes as $attr)
					{
						$attr_label = substr($attr->label,0,100);
						$attr_label = preg_replace('/\s+/', ' ', trim($attr_label));
						$key = "attribute_pa_".$attr->name;
						$bulktext = '<tr data-id="'.$key.'"><td>'
						.'<input id="set'.$key.'" type="checkbox" class="bulkset" data-id="'.$key.'" data-type="customtaxh"><label for="set'.$key.'">Set (attr) '.$attr_label.'</label></td><td>'.
						'<select id="bulkadd'.$key.'" class="bulkselect">'.
							'<option value="new">'.__('set new','woocommerce-advbulkedit').'</option>'.
							'<option value="add">'.__('add','woocommerce-advbulkedit').'</option>'.
							'<option value="remove">'.__('remove','woocommerce-advbulkedit').'</option></select></td><td>'
						 .'<select id="bulk'.$key.'" class="makechosen catselset" style="width:250px;" data-placeholder="select" multiple ><option value=""></option>';
						  
						foreach($attr->values as $value)
						{
							$val_name = substr($value->name,0,100);
							$val_name = preg_replace('/\s+/', ' ', trim($val_name));
							$bulktext.= '<option value="'.$value->term_id.'">'.$val_name.'</option>';
						}
//						$bulktext.= '</select></td><td><label><input type="checkbox" disabled class="alsosetvisiblefp" data-id="'.$key.'">Also set:</label>&nbsp;<label>(<input type="checkbox" disabled class="visiblefp" data-id="'.$key.'">Visible on the p. page)</label></td></tr>';
						$bulktext.= '</select></td><td>(<select class="selectvisiblefp" disabled data-id="'.$key.'">'.
						'<option value="skip">skip</option><option value="andset">and set</option><option value="onlyset">only set</option>'.
						'</select>&nbsp;<input type="checkbox" disabled class="visiblefp" data-id="'.$key.'">Visible on p. p.)&nbsp;&nbsp;'.
						'(<select disabled class="selectusedforvars" data-id="'.$key.'"><option value="skip">skip</option><option value="andset">and set</option><option value="onlyset">only set</option>'.
						'</select>&nbsp;<input type="checkbox" disabled class="usedforvars" data-id="'.$key.'">Used for var.)</td></tr>';
						echo "W3Ex['".str_replace("'","\'",$key)."bulk'] = '".str_replace("'","\'",$bulktext)."';";
						echo PHP_EOL;
					}
					echo '</script>';
				}
				if(is_array($sel_fields) && !empty($sel_fields))
				{
					echo '<script>';
					foreach($sel_fields as $key => $innerarray)
					{
						if(isset($innerarray['type']))
						{
							if($innerarray['type'] === 'customh' || $innerarray['type'] === 'custom')
							{
								if(taxonomy_exists($key))
								{
									
									$bulktext = '<tr data-id="'.$key.'"><td>'
									.'<input id="set'.$key.'" type="checkbox" class="bulkset" data-id="'.$key.'" data-type="customtaxh"><label for="set'.$key.'">Set '.$key.'</label></td><td>'.
						'<select id="bulkadd'.$key.'" class="bulkselect">'.
							'<option value="new">'.__('set new','woocommerce-advbulkedit').'</option>'.
							'<option value="add">'.__('add','woocommerce-advbulkedit').'</option>'.
							'<option value="remove">'.__('remove','woocommerce-advbulkedit').'</option></select></td><td>'
									 .'<select id="bulk'.$key.'" class="makechosen catselset" style="width:250px;" data-placeholder="select" multiple ><option value=""></option>';
									 $searchtext = ' class="makechosen catselset" style="width:250px;" data-placeholder="select" multiple ><option value=""></option>';
									   $argsb = array(
									    'number'     => 99999,
									    'orderby'    => 'slug',
									    'order'      => 'ASC',
									    'hide_empty' => false,
									    'include'    => '',
										'fields'     => 'all'
									);

									$woo_categoriesb = get_terms($key, $argsb );

									foreach($woo_categoriesb as $category)
									{
									    if(!is_object($category)) continue;
									    if(!property_exists($category,'name')) continue;
									    if(!property_exists($category,'term_id')) continue;
										$catname = str_replace('"','\"',$category->name);
										$catname = trim(preg_replace('/\s+/', ' ', $catname));
									   	$bulktext.= '<option value="'.$category->term_id.'" >'.$catname.'</option>';
										$searchtext.= '<option value="'.$category->term_id.'" >'.$catname.'</option>';
									}
									$bulktext.= '</select></td><td></td></tr>';
									$searchtext.= '</select>';
									if($innerarray['type'] === 'customh')
									{
										echo "W3Ex['".str_replace("'","\'",$key)."bulk'] = '".str_replace("'","\'",$bulktext)."';";
									}
									echo "W3Ex['taxonomyterms".str_replace("'","\'",$key)."'] = '".str_replace("'","\'",$searchtext)."';";
									echo PHP_EOL;
								}
							}
						}
					}
					echo '</script>';
				}
			?>
			<div id="customfieldsdialog">
			<table cellpadding="10" cellspacing="0" id="customfieldstable">
				<tr class="addcontrols">
					<td>
						Field name:<br />
						<input id="fieldname" type="text"/>
					</td>
					<td>
						Field type:<br />
						<select id="fieldtype">
							<option value="text">Text (single line)</option>
							<option value="multitext">Text (multi line)</option>
							<option value="integer">Number (integer)</option>
							<option value="decimal">Number (decimal .00)</option>
							<option value="decimal3">Number (decimal .000)</option>
							<option value="select">Dropdown Select</option>
							<option value="checkbox">Checkbox</option>
							<option value="custom">Custom taxonomy</option>
							<option value="customh">Custom taxonomy (hierarchical)</option>
						</select>
					</td>
					<td>
						Visible:<br />
						<select id="fieldvisible">
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select>
					</td>
				</tr>
				<tr class="addokcancel">
					<td>
						 <button id="addok">Ok</button>&nbsp;&nbsp;&nbsp;&nbsp;
						 <button id="addcancel">Cancel</button>
					</td>
					<td><div id="extracustominfo"></div>
					</td>
					<td>
					</td>
				</tr>
			</table><br />
			 <button id="addcustomfield">Add Custom Field</button>
		</div>
		<div id="findcustomfieldsdialog">
			 <br /><br />
			 <?php _e('Find custom fields by product ID','woocommerce-advbulkedit'); ?>:<input id="productid" type="text"/><button id="findcustomfield" class="button"><?php _e('Find','woocommerce-advbulkedit'); ?></button> &nbsp;&nbsp;<?php _e('OR','woocommerce-advbulkedit'); ?>&nbsp;&nbsp; 
			 <button id="findcustomtaxonomies" class="button"><?php _e('Find Taxonomies','woocommerce-advbulkedit'); ?></button>
			 <br /><br /><br />
			 <table cellpadding="25" cellspacing="0" class="tablecustomfields">
			</table>
		</div>
		
			<div id="debuginfo"></div>
			<iframe id="exportiframe" width="0" height="0">
  			</iframe>
		</div>
		
		<?php
	}
	
	
    public function _main()
    {
		$this->showMainPage();
    }
}

W3ExAdvBulkEditView::init();
