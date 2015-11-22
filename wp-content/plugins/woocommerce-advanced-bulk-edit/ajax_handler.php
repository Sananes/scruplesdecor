<?php

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class W3ExABulkEditAjaxHandler{
	
	public static function mres($value)
	{
//		$search = array("\x00", "\n", "\r", "\\", "'", "\"", "\x1a");
//		$replace = array("\\x00", "\\n", "\\r", "\\\\" ,"\';", "\\\"", "\\\x1a");

//		return str_replace($search, $replace, $value);
		return strtr($value, array(
		  "\x00" => '\x00',
		  "\n" => '\n', 
		  "\r" => '\r', 
		  '\\' => '\\\\',
		  "'" => "\'", 
		  '"' => '\"', 
		  "\x1a" => '\x1a'
		));
	}
	
	public static function loadProducts($titleparam,$catparams,$attrparams,$priceparam,$saleparam,$customparam,&$total,$ispagination,$isnext,&$hasnext,&$isbegin,$categoryor,$skuparam,$tagsparams,$descparam,$shortdescparam,$custsearchparam,&$arrduplicate = null)
	{
	try {
		global $wpdb;
//		$chars = get_bloginfo('charset');
		$posts = $wpdb->posts;
		$meta = $wpdb->postmeta;
		$temptable = $wpdb->prefix."wpmelon_advbedit_temp";
		$term = $wpdb->term_relationships;
		$term_taxonomy = $wpdb->term_taxonomy;
		$attributes = array();
		$attrmapslugtoname = array();
		$LIMIT = 1000;
		$temptotal = 0;
		$idlimitquery = "";
		$bgetvariations = true;
		$bgettotalnumber = true;
		$bgetallvars = false;
		$idquery = "";
		$p1idquery = "";
		$getnumberquery = "";
		$limitquery = "";
		$sortquery = " ASC";
		$info = array();
		
		$curr_settings = get_option('w3exabe_settings');
		if(is_array($curr_settings))
		{
			if(isset($curr_settings['isvariations']))
			{
				if($curr_settings['isvariations'] == 0)
					$bgetvariations = false;
			}
			if(isset($curr_settings['settlimit']))
			{
				$LIMIT = (int)$curr_settings['settlimit'];
			}
			if(isset($curr_settings['settgetall']))
			{
				if($curr_settings['settgetall'] == 1)
					$bgettotalnumber = false;
			}
			if(isset($curr_settings['settgetvars']))
			{
				if($curr_settings['settgetvars'] == 1)
					$bgetallvars = true;
			}
			
		}
		
		
		self::GetAttributes($attributes,$attrmapslugtoname);
		$attributekeys = array();
		if(is_array($attributes) && !empty($attributes))
		{
			foreach($attributes as $attr)
			{
				$attributekeys['pa_'.$attr->name] = 'pa_'.$attr->name;
		    }
		}
		$query = "CREATE TABLE IF NOT EXISTS {$temptable} (
			 ID bigint(20) unsigned NOT NULL DEFAULT '0',
   			 type int(1) NOT NULL DEFAULT '0',
    	     post_parent bigint(20) unsigned NOT NULL DEFAULT '0',
			 useit int(1) NOT NULL DEFAULT '0',
			 PRIMARY KEY(ID))";
if($arrduplicate === null)
{
		$ret = $wpdb->query($query);
		if ( false === $ret) {
				return new WP_Error( 'db_query_error', 
					__( 'Could not execute query' ), $wpdb->last_error );
		} 
		if ( $ispagination) 
		{
			if($isnext)
			{
				$query = "SELECT MAX(ID) FROM {$temptable} WHERE useit=1";
				$ret = $wpdb->get_var($query);
				if($ret)
				{
					$idquery = " AND ID > {$ret}";
					$p1idquery = " AND p1.ID > {$ret}";
				}else
				{
					$ispagination = false;
					$isbegin = true;
				}
					
			}				
			else
			{
				$query = "SELECT MIN(ID) FROM {$temptable} WHERE useit=1";
				$ret = $wpdb->get_var($query);
				if($ret)
				{
					$idquery = " AND ID < {$ret}";
					$p1idquery = " AND p1.ID < {$ret}";
					$sortquery = " DESC";
				}else
				{
					$ispagination = false;
					$isbegin = true;
				}
				
			}
			
			
		}
		$query = "TRUNCATE TABLE {$temptable}";
		$ret = $wpdb->query($query);
		if ( false === $ret) {
			if ( is_wp_error( $ret ) ) {
				return new WP_Error( 'db_query_error', 
					__( 'Could not execute query' ), $wpdb->last_error );
			} else {
				$query = "DELETE FROM {$temptable} WHERE 1";
				$ret = $wpdb->query($query);
				if ( false === $ret) {
					return $wpdb->last_error;
				}
			}
		}
		$catsquery = "";
		$pricequery = "";
		$salequery = "";
//		$titlequery = "";
		$titlelike = "";
		if($catparams == NULL) $catparams = array();
		if($attrparams == NULL) $attrparams = array();
		if($titleparam == NULL) $titleparam = "";
		if($descparam == NULL) $descparam = "";
		if($shortdescparam == NULL) $shortdescparam = "";
		if($customparam == NULL) $customparam = array();
		if($skuparam == NULL) $skuparam = "";
		if($tagsparams == NULL) $tagsparams = array();
		if($custsearchparam == NULL) $custsearchparam = array();
		$hascustomtax = false;
		foreach($custsearchparam as $custitem)
		{
			if(isset($custitem['type']) && ($custitem['type'] === 'custom' || $custitem['type'] === 'customh'))
			{
				if(isset($custitem['array']) && is_array($custitem['array']))
				{
					$hascustomtax = true;
					break;
				}
			}
		}
		if(count($catparams) > 0 || count($attrparams) > 0 || count($tagsparams) > 0 || $hascustomtax)
		{
			if(is_array($curr_settings))
			{
				if(isset($curr_settings['incchildren']))
				{
					if($curr_settings['incchildren'] == 1)
						self::HandleCatParams($catparams);
				}
			}
			$catsquery = "INNER JOIN {$term} rel ON {$posts}.ID=rel.object_id AND rel.term_taxonomy_id IN (";
			$bfirst = true;
			$catcounter = 0;
			foreach($catparams as $catparam)
			{
				if($bfirst)
				{
					$bfirst = false;
					$catsquery.= $catparam;
					if($categoryor)
					{
						foreach($attrparams as $attrparam)
						{
							$catsquery.= ','. $attrparam['id'];
						}
						foreach($tagsparams as $tagparam)
						{
							$catsquery.= ','. $tagparam;
						}
						foreach($custsearchparam as $custitem)
						{
							if(isset($custitem['type']) && ($custitem['type'] === 'custom' || $custitem['type'] === 'customh'))
							{
								if(isset($custitem['array']) && is_array($custitem['array']))
								{
									foreach($custitem['array'] as $custarritem)
									{
										$catsquery.= ','. $custarritem;
									}
								}
							}
						}
						$catsquery.= ')';
					}
				}else
				{
					$catcounter++;
					if($categoryor)
					{
						$catsquery.= " INNER JOIN {$term} rel{$catcounter} ON {$posts}.ID=rel{$catcounter}.object_id AND rel{$catcounter}.term_taxonomy_id IN (".$catparam;
						foreach($attrparams as $attrparam)
						{
							$catsquery.= ','. $attrparam['id'];
						}
						foreach($tagsparams as $tagparam)
						{
							$catsquery.= ','. $tagparam;
						}
						foreach($custsearchparam as $custitem)
						{
							if(isset($custitem['type']) && ($custitem['type'] === 'custom' || $custitem['type'] === 'customh'))
							{
								if(isset($custitem['array']) && is_array($custitem['array']))
								{
									foreach($custitem['array'] as $custarritem)
									{
										$catsquery.= ','. $custarritem;
									}
								}
							}
						}
						$catsquery.= ')';
					}else
					{
						$catsquery.= ','. $catparam;
					}
					
				}
			}
			if(!$categoryor)
			{
				foreach($attrparams as $attrparam)
				{
					if($bfirst)
					{
						$bfirst = false;
						$catsquery.= $attrparam['id'];
					}else
					{
						$catsquery.= ','. $attrparam['id'];
					}
				}
				foreach($tagsparams as $tagparam)
				{
					if($bfirst)
					{
						$bfirst = false;
						$catsquery.= $tagparam;
					}else
					{
						$catsquery.= ','. $tagparam;
					}
				}
				foreach($custsearchparam as $custitem)
				{
					if(isset($custitem['type']) && ($custitem['type'] === 'custom' || $custitem['type'] === 'customh'))
					{
						if(isset($custitem['array']) && is_array($custitem['array']))
						{
							foreach($custitem['array'] as $custarritem)
							{
								if($bfirst)
								{
									$bfirst = false;
									$catsquery.= $custarritem;
								}else
								{
									$catsquery.= ','. $custarritem;
								}
							}
						}
					}
				}
				$catsquery.= ')';
			}else
			{
				if(count($catparams) == 0)	
				{
					foreach($attrparams as $attrparam)
					{
						if($bfirst)
						{
							$bfirst = false;
							$catsquery.= $attrparam['id'];
						}else
						{
							$catsquery.= ','. $attrparam['id'];
						}
					}
					foreach($tagsparams as $tagparam)
					{
						if($bfirst)
						{
							$bfirst = false;
							$catsquery.= $tagparam;
						}else
						{
							$catsquery.= ','. $tagparam;
						}
					}
					foreach($custsearchparam as $custitem)
					{
						if(isset($custitem['type']) && ($custitem['type'] === 'custom' || $custitem['type'] === 'customh'))
						{
							if(isset($custitem['array']) && is_array($custitem['array']))
							{
								foreach($custitem['array'] as $custarritem)
								{
									if($bfirst)
									{
										$bfirst = false;
										$catsquery.= $custarritem;
									}else
									{
										$catsquery.= ','. $custarritem;
									}
								}
							}
						}
					}
					$catsquery.= ')';
				}
			}
			

		}
		if($priceparam != NULL)
		{
			$pricequery = " INNER JOIN {$meta} meta ON {$posts}.ID=meta.post_id
			AND CASE WHEN meta.meta_key='_regular_price' THEN meta.meta_value";
			if($priceparam['value'] == 'more')
			{
				$pricequery.= ' > ';
			}else if($priceparam['value'] == 'less')
			{
				$pricequery.= ' < ';
			}else if($priceparam['value'] == 'equal')
			{
				$pricequery.= ' = ';
			}else if($priceparam['value'] == 'moree')
			{
				$pricequery.= ' >= ';
			}else
			{//lesse
				$pricequery.= ' <= ';
			}
			$pricequery.= $priceparam['price'].' END ';
		}
		if($saleparam != NULL)
		{
			$salequery = " INNER JOIN {$meta} meta2 ON {$posts}.ID=meta2.post_id
			AND CASE WHEN meta2.meta_key='_sale_price' THEN meta2.meta_value";
			if($saleparam['value'] == 'more')
			{
				$salequery.= ' > ';
			}else if($saleparam['value'] == 'less')
			{
				$salequery.= ' < ';
			}else if($saleparam['value'] == 'equal')
			{
				$salequery.= ' = ';
			}else if($saleparam['value'] == 'moree')
			{
				$salequery.= ' >= ';
			}else
			{//lesse
				$salequery.= ' <= ';
			}
			$salequery.= $saleparam['price'].' END ';
		}

		$arrsearchtitle = array();
		if($titleparam != NULL && $titleparam !== "")
		{
			switch($titleparam['value']){
				case "con":
				{
					$searchstring = $titleparam['title'];
						$searchstring = trim($searchstring);
					if($searchstring == "") break;
					$arrstrings = explode(' ',$searchstring);
					
					if(count($arrstrings) > 1)
					{
						$titlelike = " AND (";
						$counter = 0;
						foreach($arrstrings as $arrstring)
						{
							$arrstring = trim($arrstring);
							if($arrstring == "") continue;
							if($titlelike == " AND (")
							{
								$titlelike.= "{$posts}.post_title LIKE '%%%s%%'";
							}
							else
							{
								$titlelike.= " AND {$posts}.post_title LIKE '%%%s%%'";
							}
							$arrsearchtitle[] = $arrstring;
						}
						$titlelike.= ")";
						$counter++;
					}else
					{
						$titlelike = " AND {$posts}.post_title LIKE '%%%s%%' ";
						$arrsearchtitle[] = $searchstring;
					}
						
				}
				break;
				case "notcon":
				{
					$titlelike = " AND {$posts}.post_title NOT LIKE '%".$titleparam['title']."%' ";
				}
				break;
				case "start":
				{
					$titlelike = " AND {$posts}.post_title LIKE '".$titleparam['title']."%' ";
				}
				break;
				case "end":
				{
					$titlelike = " AND {$posts}.post_title LIKE '%".$titleparam['title']."' ";
				}
				break;
				default:
					break;
			}
		}

		$desclike = "";
		if($descparam != NULL && $descparam !== "")
		{
			switch($descparam['value']){
				case "con":
				{
					$searchstring = $descparam['title'];
					$searchstring = trim($searchstring);
					if($searchstring == "") break;
					$arrstrings = explode(' ',$searchstring);
					
					if(count($arrstrings) > 1)
					{
						$desclike = " AND (";
						$counter = 0;
						foreach($arrstrings as $arrstring)
						{
							$arrstring = trim($arrstring);
							if($arrstring == "") continue;
							if($desclike == " AND (")
							{
								$desclike.= "{$posts}.post_content LIKE '%%%s%%'";
							}
							else
							{
								$desclike.= " AND {$posts}.post_content LIKE '%%%s%%'";
							}
							$arrsearchtitle[] = $arrstring;
						}
						$desclike.= ")";
						$counter++;
					}else
					{
						$desclike = " AND {$posts}.post_content LIKE '%%%s%%' ";
						$arrsearchtitle[] = $searchstring;
					}
						
				}
				break;
				case "notcon":
				{
					$desclike = " AND {$posts}.post_content NOT LIKE '%".$descparam['title']."%' ";
				}
				break;
				case "start":
				{
					$desclike = " AND {$posts}.post_content LIKE '".$descparam['title']."%' ";
				}
				break;
				case "end":
				{
					$desclike = " AND {$posts}.post_content LIKE '%".$descparam['title']."' ";
				}
				break;
				default:
					break;
			}
		}

		$shortdesclike = "";
		if($shortdescparam != NULL && $shortdescparam !== "")
		{
			switch($shortdescparam['value']){
				case "con":
				{
					$searchstring = $shortdescparam['title'];
					$searchstring = trim($searchstring);
					if($searchstring == "") break;
					$arrstrings = explode(' ',$searchstring);
					
					if(count($arrstrings) > 1)
					{
						$shortdesclike = " AND (";
						$counter = 0;
						foreach($arrstrings as $arrstring)
						{
							$arrstring = trim($arrstring);
							if($arrstring == "") continue;
							if($shortdesclike == " AND (")
							{
								$shortdesclike.= "{$posts}.post_excerpt LIKE '%%%s%%'";
							}
							else
							{
								$shortdesclike.= " AND {$posts}.post_excerpt LIKE '%%%s%%'";
							}
							$arrsearchtitle[] = $arrstring;
						}
						$shortdesclike.= ")";
						$counter++;
					}else
					{
						$shortdesclike = " AND {$posts}.post_excerpt LIKE '%%%s%%' ";
						$arrsearchtitle[] = $searchstring;
					}
						
				}
				break;
				case "notcon":
				{
					$shortdesclike = " AND {$posts}.post_excerpt NOT LIKE '%".$shortdescparam['title']."%' ";
				}
				break;
				case "start":
				{
					$shortdesclike = " AND {$posts}.post_excerpt LIKE '".$shortdescparam['title']."%' ";
				}
				break;
				case "end":
				{
					$shortdesclike = " AND {$posts}.post_excerpt LIKE '%".$shortdescparam['title']."' ";
				}
				break;
				default:
					break;
			}
		}
		
		$skuquery = "";
		if($skuparam != NULL && $skuparam !== "")
		{
			$skuquery = " INNER JOIN {$meta} meta3 ON {$posts}.ID=meta3.post_id
			AND CASE WHEN meta3.meta_key='_sku' THEN meta3.meta_value";
			switch($skuparam['value']){
				case "con":
				{
					$skuquery.= " LIKE '%".$skuparam['title']."%' ";
				}
				break;
				case "notcon":
				{
					$skuquery.= " NOT LIKE '%".$skuparam['title']."%' ";
				}
				break;
				case "start":
				{
					$skuquery.= " LIKE '".$skuparam['title']."%' ";
				}
				break;
				case "end":
				{
					$skuquery.= " LIKE '%".$skuparam['title']."' ";
				}
				break;
				default:
					break;
			}
			$skuquery.= ' END ';
			
		}

		$custommetasearch = "";
		{
			$innercounter = 5;
			foreach($custsearchparam as $custitem)
			{
				if(isset($custitem['type']) && ($custitem['type'] !== 'custom' || $custitem['type'] !== 'customh'))
				{
					if(isset($custitem['title']) && isset($custitem['value']))
					{
						if( $custitem['type'] === 'integer' ||  $custitem['type'] === 'decimal' ||  $custitem['type'] === 'decimal3')
						{
							if(!is_numeric($custitem['title']))
								continue;
						}
						$custommetasearch.= " INNER JOIN {$meta} meta{$innercounter} ON {$posts}.ID=meta{$innercounter}.post_id
							AND CASE WHEN meta{$innercounter}.meta_key='{$custitem['id']}' THEN meta{$innercounter}.meta_value";
						if($custitem['type'] === 'integer' ||  $custitem['type'] === 'decimal' ||  $custitem['type'] === 'decimal3')
						{
							if($custitem['value'] == 'more')
							{
								$custommetasearch.= ' > ';
							}else if($custitem['value'] == 'less')
							{
								$custommetasearch.= ' < ';
							}else if($custitem['value'] == 'equal')
							{
								$custommetasearch.= ' = ';
							}else if($custitem['value'] == 'moree')
							{
								$custommetasearch.= ' >= ';
							}else
							{//lesse
								$custommetasearch.= ' <= ';
							}
							$custommetasearch.= $custitem['title'].' END ';
							
						}else
						{
							switch($custitem['value'])
							{
								case "con":
								{
									$custommetasearch.= " LIKE '%".$custitem['title']."%' ";
								}
								break;
								case "notcon":
								{
									$custommetasearch.= " NOT LIKE '%".$custitem['title']."%' ";
								}
								break;
								case "start":
								{
									$custommetasearch.= " LIKE '".$custitem['title']."%' ";
								}
								break;
								case "end":
								{
									$custommetasearch.= " LIKE '%".$custitem['title']."' ";
								}
								break;
								default:
									break;
							}
							$custommetasearch.= ' END ';
						}
						$innercounter++;
					}
				}
				
			}
		}
		
		$LIMIT+= 1;
//		if($catsquery !== "")
//		{
//			$catsquery.= "INNER JOIN {$term} rel ON {$posts}.ID=rel.object_id{$catsquery}";
//		}
		if(!$bgettotalnumber)
			$limitquery = " LIMIT {$LIMIT}";
//			INNER JOIN {$term} rel ON {$posts}.ID=rel.object_id{$catsquery}
		if($bgettotalnumber)
		{
			$query = "INSERT INTO {$temptable} (
				SELECT 
				{$posts}.ID, 0 AS type, 0 AS post_parent,0 as useit 
				FROM {$posts}
				{$catsquery}{$pricequery}{$salequery}{$skuquery}{$custommetasearch}
				WHERE {$posts}.post_type='product'{$titlelike}{$desclike}{$shortdesclike} AND {$posts}.post_status IN ('draft','publish','private') GROUP BY {$posts}.ID
				)";
		}else
		{
			$query = "INSERT INTO {$temptable} (
				SELECT 
				{$posts}.ID, 0 AS type, 0 AS post_parent,0 as useit 
				FROM {$posts}
				{$catsquery}{$pricequery}{$salequery}{$skuquery}{$custommetasearch}
				WHERE {$posts}.post_type='product'{$titlelike}{$desclike}{$shortdesclike} AND {$posts}.post_status IN ('draft','publish','private'){$idquery} GROUP BY {$posts}.ID{$limitquery}
				)";
		}
		if($catsquery === '')
		{//let's get products without product_type'
			if($bgettotalnumber)
			{
				$query = "INSERT INTO {$temptable} (
					SELECT 
					{$posts}.ID, 0 AS type, 0 AS post_parent,0 as useit 
					FROM {$posts}{$pricequery}{$salequery}{$skuquery}{$custommetasearch}
					WHERE {$posts}.post_type='product'{$titlelike}{$desclike}{$shortdesclike} AND {$posts}.post_status IN ('draft','publish','private')	)";
			}else
			{
				$query = "INSERT INTO {$temptable} (
					SELECT 
					{$posts}.ID, 0 AS type, 0 AS post_parent,0 as useit 
					FROM {$posts}{$pricequery}{$salequery}{$skuquery}{$custommetasearch}
					WHERE {$posts}.post_type='product'{$titlelike}{$desclike}{$shortdesclike} AND {$posts}.post_status IN ('draft','publish','private'){$idquery}  )";
			}
		}
//		$query = mysql_escape_string($query);
		if(count($arrsearchtitle) > 0)
		{
			$ret = $wpdb->query($wpdb->prepare($query,$arrsearchtitle));
		}else{
			$ret = $wpdb->query($query);
		}
		
		$LIMIT-= 1;
		if ( is_wp_error($ret) ) {
			return new WP_Error( 'db_query_error', 
				__( 'Could not execute query' ), $wpdb->last_error );
		} 

		$query = "SELECT MIN(ID) as minid, MAX(ID) as maxid FROM {$temptable} LIMIT {$LIMIT}";
		$ret = $wpdb->get_results($query);
		if ( is_wp_error($ret) ) {
			return new WP_Error( 'db_query_error', 
				__( 'Could not execute query' ), $wpdb->last_error );
		} 
		$minid = $ret[0]->minid;
		$maxid = $ret[0]->maxid;
		$query = "SELECT COUNT(ID) FROM {$temptable}";
		$ret = $wpdb->get_var($query);
		if ( is_wp_error($ret) ) {
			return new WP_Error( 'db_query_error', 
				__( 'Could not execute query' ), $wpdb->last_error );
		} 
		$total = (int)$ret;
		$bdontcheckforparent = false;
		if((int)$ret > $LIMIT)
		{
			$hasnext = true;
			if($minid !== NULL && $maxid !== NULL)
				$idlimitquery = " AND p1.ID > {$minid} AND p1.ID < {$maxid}";
			if(!$bgettotalnumber)
			{
				$total = -1;
				$bdontcheckforparent = true;
			}
		}
		if(!$bgettotalnumber)
				$total = -1;
		if(!$bgettotalnumber)
		{
			$limitquery = " LIMIT {$LIMIT}";
		}else
		{
			$idlimitquery = "";
		}

		$attrsquery = "";
		if(count($attrparams) > 0)
		{
			$attrsquery = " INNER JOIN {$meta} ON p1.ID={$meta}.post_id AND ";
			$bfirst = true;
			foreach($attrparams as $attrparam)
			{
				if($bfirst)
				{
					$bfirst = false;
					$attrsquery.= "(({$meta}.meta_key='attribute_pa_".$attrparam['attr']."' AND {$meta}.meta_value='".$attrparam['value']."')";
				}else
				{
					$attrsquery.= " OR ({$meta}.meta_key='attribute_pa_".$attrparam['attr']."' AND {$meta}.meta_value='".$attrparam['value']."')";
				}
			}
			$attrsquery.= ")";
		}
		
		$query ="INSERT INTO {$temptable}(
			SELECT p1.ID, 1 AS type,p1.post_parent,0 as useit 
			FROM {$posts} p1{$attrsquery}
			WHERE (p1.post_parent IN (SELECT ID FROM {$temptable}))
			AND (p1.post_type='product_variation'){$idlimitquery} ORDER BY p1.ID ASC {$limitquery})";
		if($bdontcheckforparent)
		{
			$query ="INSERT INTO {$temptable}(
			SELECT p1.ID, 1 AS type,p1.post_parent,0 as useit 
			FROM {$posts} p1{$attrsquery}
			WHERE (p1.post_type='product_variation'){$idlimitquery} ORDER BY p1.ID ASC {$limitquery})";
		}

		if($pricequery != "" && $bgetvariations)
		{
			$pricequery = " INNER JOIN {$meta} meta ON p1.ID=meta.post_id
			AND CASE WHEN meta.meta_key='_regular_price' THEN meta.meta_value";
			if($priceparam['value'] == 'more')
			{
				$pricequery.= ' > ';
			}else if($priceparam['value'] == 'less')
			{
				$pricequery.= ' < ';
			}else if($priceparam['value'] == 'equal')
			{
				$pricequery.= ' = ';
			}else if($priceparam['value'] == 'moree')
			{
				$pricequery.= ' >= ';
			}else
			{//lesse
				$pricequery.= ' <= ';
			}
			$pricequery.= $priceparam['price'].' END ';
			if($attrsquery != "")
			{
				{
					$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 1 AS type,p1.post_parent,0 AS useit
					FROM {$posts} p1{$attrsquery}{$pricequery}
					WHERE (p1.post_type='product_variation'){$idlimitquery}  AND p1.ID NOT IN (SELECT ID FROM {$temptable}) ORDER BY p1.ID ASC {$limitquery})";
					$ret = $wpdb->query($query);
					if ( is_wp_error($ret) ) {
						return new WP_Error( 'db_query_error', 
							__( 'Could not execute query' ), $wpdb->last_error );
					} 
					$query ="INSERT INTO {$temptable}(
						SELECT p1.ID, 0 AS type,0 AS post_parent, 0 AS useit
						FROM {$posts} p1
						WHERE p1.ID IN (SELECT post_parent FROM {$temptable} WHERE type=1) AND (p1.post_type='product') AND (p1.post_status IN ('publish','draft','private')){$idlimitquery} ORDER BY p1.ID ASC {$limitquery})";
				}
			}else
			{
				$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 1 AS type,p1.post_parent ,0 AS useit
					FROM {$posts} p1{$attrsquery}{$pricequery}
					WHERE p1.post_type='product_variation'{$idlimitquery}  AND p1.ID NOT IN (SELECT ID FROM {$temptable}) ORDER BY p1.ID ASC {$limitquery})";
				$ret = $wpdb->query($query);
				if ( is_wp_error($ret) ) {
					return new WP_Error( 'db_query_error', 
						__( 'Could not execute query' ), $wpdb->last_error );
				} 
				$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 0 AS type,0 AS post_parent, 0 AS useit
					FROM {$posts} p1
					WHERE p1.ID IN (SELECT post_parent FROM {$temptable} WHERE type=1) AND (p1.post_type='product') AND (p1.post_status IN ('publish','draft','private')){$idlimitquery} ORDER BY p1.ID ASC {$limitquery})";
				
			}
			
		}
		if($salequery != "" && $bgetvariations)
		{
			$salequery = " INNER JOIN {$meta} meta ON p1.ID=meta.post_id
			AND CASE WHEN meta.meta_key='_sale_price' THEN meta.meta_value";
			if($saleparam['value'] == 'more')
			{
				$salequery.= ' > ';
			}else if($saleparam['value'] == 'less')
			{
				$salequery.= ' < ';
			}else if($saleparam['value'] == 'equal')
			{
				$salequery.= ' = ';
			}else if($saleparam['value'] == 'moree')
			{
				$salequery.= ' >= ';
			}else
			{//lesse
				$salequery.= ' <= ';
			}
			$salequery.= $saleparam['price'].' END ';
			if($attrsquery != "")
			{
				{
					$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 1 AS type,p1.post_parent,0 AS useit
					FROM {$posts} p1{$attrsquery}{$salequery}
					WHERE (p1.post_type='product_variation'){$idlimitquery} ORDER BY p1.ID ASC {$limitquery}
					)";
					$ret = $wpdb->query($query);
					if ( is_wp_error($ret) ) {
						return new WP_Error( 'db_query_error', 
						__( 'Could not execute query' ), $wpdb->last_error );
					} 
					$query ="INSERT INTO {$temptable}(
						SELECT p1.ID, 0 AS type,0 AS post_parent, 0 AS useit
						FROM {$posts} p1
						WHERE p1.ID IN (SELECT post_parent FROM {$temptable} WHERE type=1) AND (p1.post_type='product') AND (p1.post_status IN ('publish','draft','private')) {$idlimitquery} AND p1.ID NOT IN (SELECT ID FROM {$temptable}) ORDER BY p1.ID ASC {$limitquery})";
				}
			}else
			{
				$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 1 AS type,p1.post_parent ,0 AS useit
					FROM {$posts} p1{$attrsquery}{$salequery}
					WHERE p1.post_type='product_variation'{$idlimitquery} ORDER BY p1.ID ASC {$limitquery})";
				$ret = $wpdb->query($query);
				if ( is_wp_error($ret) ) {
					return new WP_Error( 'db_query_error', 
					__( 'Could not execute query' ), $wpdb->last_error );
				} 
				$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 0 AS type,0 AS post_parent, 0 AS useit
					FROM {$posts} p1
					WHERE p1.ID IN (SELECT post_parent FROM {$temptable} WHERE type=1) AND (p1.post_type='product') AND (p1.post_status IN ('publish','draft','private')) {$idlimitquery} AND p1.ID NOT IN (SELECT ID FROM {$temptable}) ORDER BY p1.ID ASC {$limitquery})";
				
			}
			
		}
		if($skuquery != "" && $bgetvariations)
		{
			$skuquery = " INNER JOIN {$meta} meta ON p1.ID=meta.post_id
			AND CASE WHEN meta.meta_key='_sku' THEN meta.meta_value";
			switch($skuparam['value']){
				case "con":
				{
					$skuquery.= " LIKE '".$skuparam['title']."%' ";
				}
				break;
				case "notcon":
				{
					$skuquery.= " NOT LIKE '%".$skuparam['title']."%' ";
				}
				break;
				case "start":
				{
					$skuquery.= " LIKE '".$skuparam['title']."%' ";
				}
				break;
				case "end":
				{
					$skuquery.= " LIKE '%".$skuparam['title']."' ";
				}
				break;
				default:
					break;
			}
			$skuquery.= ' END ';
			
			if($attrsquery != "")
			{
				{
					$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 1 AS type,p1.post_parent,0 AS useit
					FROM {$posts} p1{$attrsquery}{$skuquery}
					WHERE (p1.post_type='product_variation'){$idlimitquery} ORDER BY p1.ID ASC {$limitquery}
					)";
					$ret = $wpdb->query($query);
					if ( is_wp_error($ret) ) {
						return new WP_Error( 'db_query_error', 
						__( 'Could not execute query' ), $wpdb->last_error );
					} 
					$query ="INSERT INTO {$temptable}(
						SELECT p1.ID, 0 AS type,0 AS post_parent, 0 AS useit
						FROM {$posts} p1
						WHERE p1.ID IN (SELECT post_parent FROM {$temptable} WHERE type=1) AND (p1.post_type='product') AND (p1.post_status IN ('publish','draft','private')) {$idlimitquery} AND p1.ID NOT IN (SELECT ID FROM {$temptable}) ORDER BY p1.ID ASC {$limitquery})";
				}
			}else
			{
				$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 1 AS type,p1.post_parent ,0 AS useit
					FROM {$posts} p1{$attrsquery}{$skuquery}
					WHERE p1.post_type='product_variation'{$idlimitquery} ORDER BY p1.ID ASC {$limitquery})";
				$ret = $wpdb->query($query);
				if ( is_wp_error($ret) ) {
					return new WP_Error( 'db_query_error', 
					__( 'Could not execute query' ), $wpdb->last_error );
				} 
				$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 0 AS type,0 AS post_parent, 0 AS useit
					FROM {$posts} p1
					WHERE p1.ID IN (SELECT post_parent FROM {$temptable} WHERE type=1) AND (p1.post_type='product') AND (p1.post_status IN ('publish','draft','private')) {$idlimitquery} AND p1.ID NOT IN (SELECT ID FROM {$temptable}) ORDER BY p1.ID ASC {$limitquery})";
				
			}
			
		}
		if($custommetasearch !== "" && $bgetvariations)
		{
			$innercounter = 5;
			$custommetasearch = "";
			foreach($custsearchparam as $custitem)
			{
				if(isset($custitem['type']) && ($custitem['type'] !== 'custom' || $custitem['type'] !== 'customh'))
				{
					if(isset($custitem['title']) && isset($custitem['value']))
					{
						if( $custitem['type'] === 'integer' ||  $custitem['type'] === 'decimal' ||  $custitem['type'] === 'decimal3')
						{
							if(!is_numeric($custitem['title']))
								continue;
						}
						$custommetasearch.= " INNER JOIN {$meta} meta{$innercounter} ON p1.ID=meta{$innercounter}.post_id
							AND CASE WHEN meta{$innercounter}.meta_key='{$custitem['id']}' THEN meta{$innercounter}.meta_value";
						if($custitem['type'] === 'integer' ||  $custitem['type'] === 'decimal' ||  $custitem['type'] === 'decimal3')
						{
							if($custitem['value'] == 'more')
							{
								$custommetasearch.= ' > ';
							}else if($custitem['value'] == 'less')
							{
								$custommetasearch.= ' < ';
							}else if($custitem['value'] == 'equal')
							{
								$custommetasearch.= ' = ';
							}else if($custitem['value'] == 'moree')
							{
								$custommetasearch.= ' >= ';
							}else
							{//lesse
								$custommetasearch.= ' <= ';
							}
							$custommetasearch.= $custitem['title'].' END ';
							
						}else
						{
							switch($custitem['value']){
								case "con":
								{
									$custommetasearch.= " LIKE '".$custitem['title']."%' ";
								}
								break;
								case "notcon":
								{
									$custommetasearch.= " NOT LIKE '%".$custitem['title']."%' ";
								}
								break;
								case "start":
								{
									$custommetasearch.= " LIKE '".$custitem['title']."%' ";
								}
								break;
								case "end":
								{
									$custommetasearch.= " LIKE '%".$custitem['title']."' ";
								}
								break;
								default:
									break;
							}
							$custommetasearch.= ' END ';
						}
						$innercounter++;
					}
					
				}
			}
			
			if($attrsquery != "")
			{
				{
					$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 1 AS type,p1.post_parent,0 AS useit
					FROM {$posts} p1{$attrsquery}{$custommetasearch}
					WHERE (p1.post_type='product_variation'){$idlimitquery}  AND p1.ID NOT IN (SELECT ID FROM {$temptable}) ORDER BY p1.ID ASC {$limitquery})";
					$ret = $wpdb->query($query);
					if ( is_wp_error($ret) ) {
						return new WP_Error( 'db_query_error', 
							__( 'Could not execute query' ), $wpdb->last_error );
					} 
					$query ="INSERT INTO {$temptable}(
						SELECT p1.ID, 0 AS type,0 AS post_parent, 0 AS useit
						FROM {$posts} p1
						WHERE p1.ID IN (SELECT post_parent FROM {$temptable} WHERE type=1) AND (p1.post_type='product') AND (p1.post_status IN ('publish','draft','private')){$idlimitquery} ORDER BY p1.ID ASC {$limitquery})";
				}
			}else
			{
				$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 1 AS type,p1.post_parent ,0 AS useit
					FROM {$posts} p1{$attrsquery}{$custommetasearch}
					WHERE p1.post_type='product_variation'{$idlimitquery}  AND p1.ID NOT IN (SELECT ID FROM {$temptable}) ORDER BY p1.ID ASC {$limitquery})";
				$ret = $wpdb->query($query);
				if ( is_wp_error($ret) ) {
					return new WP_Error( 'db_query_error', 
						__( 'Could not execute query' ), $wpdb->last_error );
				} 
				$query ="INSERT INTO {$temptable}(
					SELECT p1.ID, 0 AS type,0 AS post_parent, 0 AS useit
					FROM {$posts} p1
					WHERE p1.ID IN (SELECT post_parent FROM {$temptable} WHERE type=1) AND (p1.post_type='product') AND (p1.post_status IN ('publish','draft','private')){$idlimitquery} ORDER BY p1.ID ASC {$limitquery})";
				
			}
		}
		if($bgetvariations)
		{
			$ret = $wpdb->query($query);
			if ( is_wp_error($ret) ) {
					return new WP_Error( 'db_query_error', 
						__( 'Could not execute query' ), $wpdb->last_error );
				} 
			if($bgetallvars && $attrsquery !== "")
			{
				$query ="INSERT INTO {$temptable}(
				SELECT p1.ID, 1 AS type,p1.post_parent,0 as useit 
				FROM {$posts} p1
				WHERE (p1.post_parent IN (SELECT ID FROM {$temptable})) AND p1.ID NOT IN (SELECT ID FROM {$temptable})
				AND (p1.post_type='product_variation'){$idlimitquery} ORDER BY p1.ID ASC {$limitquery})";
				$ret = $wpdb->query($query);
				if ( is_wp_error($ret) ) {
						return new WP_Error( 'db_query_error', 
							__( 'Could not execute query' ), $wpdb->last_error );
					} 
			}
		}
		
		if($bgettotalnumber)
		{
			$query ="SELECT count(DISTINCT ID) 
					FROM {$temptable}";
			$total = $wpdb->get_var($query);
			if($total == NULL) $total = -1;
		}
		
		$useit = "";
		$query ="UPDATE {$temptable} SET useit=1 ORDER BY ID ASC LIMIT {$LIMIT}";
		if($ispagination)
		{
			$query ="UPDATE {$temptable} SET useit=1 WHERE 1{$idquery} ORDER BY ID{$sortquery} LIMIT {$LIMIT}";
		}
		$ret = $wpdb->query($query);
		if ( is_wp_error($ret) ) {
			return new WP_Error( 'db_query_error', 
				__( 'Could not execute query' ), $wpdb->last_error );
		}
		$useit =  " WHERE {$temptable}.useit=1"; 
//		if($total < $LIMIT)
		{//check and added variations
			$query ="SELECT MAX(ID) as maxid FROM {$temptable}";
			$ret = $wpdb->get_var($query);
			if ( is_wp_error($ret) ) {
				return new WP_Error( 'db_query_error', 
					__( 'Could not execute query' ), $wpdb->last_error );
			} 
			if($ret === NULL)
			{
				$hasnext = false;
				return;
			}
			$query ="SELECT useit FROM {$temptable} WHERE ID={$ret}";
			$ret = $wpdb->get_var($query);
			if($ret == 0)
			{
				$hasnext = true;
			}else
			{
				$hasnext = false;
			}
		}

				
		$ret = $wpdb->query($query);
		if ( is_wp_error($ret) ) {
				return new WP_Error( 'db_query_error', 
					__( 'Could not execute query' ), $wpdb->last_error );
			} 
		$query = "SELECT CASE WHEN p1.post_parent = 0 THEN p1.ID ELSE p1.post_parent END AS Sort,
			p1.ID,p1.post_title,p1.post_parent,p1.post_status,p1.post_content,p1.post_excerpt,p1.post_name,p1.post_date,p1.comment_status,p1.menu_order,p1.post_type
			FROM {$posts} p1
			WHERE p1.ID IN (SELECT ID FROM {$temptable}{$useit})
			ORDER BY Sort DESC LIMIT {$LIMIT}";
		$info = $wpdb->get_results($query);
}
		if($arrduplicate !== null)
		{
			$info = $arrduplicate;
		}
		
		$ids = array();
		
		for($i = 0; $i < count($info); ++$i) 
		{
			$ids[$info[$i]->ID] =&$info[$i];
		}
		foreach($ids as &$id)
		{
			if($id->post_parent != 0 && $id->post_type == 'product_variation')
			{
				$id->post_title = '(Var. of #'.$id->post_parent.')';
				if(array_key_exists($id->post_parent,$ids))
				{
					$obj = $ids[$id->post_parent];
					$obj->haschildren = true;
					$id->post_title = $obj->post_title.' (Var. of #'.$id->post_parent.')';
				}
				$id->comment_status = 'no';
				$id->post_name = '';
				$id->post_date = '';
			}else
			{
				if($id->comment_status === 'open')
					$id->comment_status = 'yes';
				else
					$id->comment_status = 'no';
				$id->_product_permalink = '';
				$permalink = get_permalink($id->ID);
				if(false !== $permalink)
				{
					$id->_product_permalink = $permalink;
				}
			}
			if(property_exists($id,'post_excerpt'))
			{
				$id->post_excerpt = str_replace("\r\n", "\n", $id->post_excerpt);
//				$id->post_excerpt = str_replace(chr(194),"", $id->post_excerpt);
//				$id->post_excerpt = str_replace(chr(160)," ", $id->post_excerpt);
			}
			if(property_exists($id,'post_content'))
			{
				$id->post_content = str_replace("\r\n", "\n", $id->post_content);
//				$id->post_content = str_replace(chr(194),"", $id->post_content);
//				$id->post_content = str_replace(chr(160)," ", $id->post_content);
			}
		}
		$customfields = "";
		if($customparam !== NULL)
		{
			foreach($customparam as $value)
			{
				$customfields.= ",'" . esc_attr($value) . "'";
			}
			
		}
		$metavals = array();
		
		
			
	if($arrduplicate === null)
	{
		$query ="SELECT p1.ID, p1.post_title,p1.post_parent, {$meta}.meta_key, {$meta}.meta_value
			FROM {$posts} p1
			INNER JOIN {$meta} ON p1.ID={$meta}.post_id 
			AND ({$meta}.meta_key IN ('_regular_price','_sale_price','_sku','_weight','_length','_width','_height','_stock','_stock_status','_visibility','_virtual','_download_type','_download_limit','_download_expiry','_downloadable_files','_downloadable','_sale_price_dates_from','_sale_price_dates_to','_tax_class','_tax_status','_backorders','_manage_stock','_featured','_purchase_note','_sold_individually','_product_url','_button_text','_thumbnail_id','_product_image_gallery','_upsell_ids','_crosssell_ids','_product_attributes','_default_attributes'{$customfields})
			OR {$meta}.meta_key LIKE 'attribute_pa_%')
			WHERE p1.ID IN (SELECT ID FROM {$temptable}{$useit})";
		$metavals =  $wpdb->get_results($query);
		if ( is_wp_error($metavals) ) {
			return new WP_Error( 'db_query_error', 
				__( 'Could not execute query' ), $wpdb->last_error );
		} 
	}else
	{
//		$metavals = $arrduplicate;
		$duplicateids = "";
		foreach($arrduplicate as $key => $value)
		{
			if($duplicateids == "")
				$duplicateids.= $value->ID;
			else
				$duplicateids.= ",".$value->ID;
		}
		
		$query ="SELECT p1.ID, p1.post_title,p1.post_parent, {$meta}.meta_key, {$meta}.meta_value
			FROM {$posts} p1
			INNER JOIN {$meta} ON p1.ID={$meta}.post_id 
			AND ({$meta}.meta_key IN ('_regular_price','_sale_price','_sku','_weight','_length','_width','_height','_stock','_stock_status','_visibility','_virtual','_download_type','_download_limit','_download_expiry','_downloadable_files','_downloadable','_sale_price_dates_from','_sale_price_dates_to','_tax_class','_tax_status','_backorders','_manage_stock','_featured','_purchase_note','_sold_individually','_product_url','_button_text','_thumbnail_id','_product_image_gallery','_upsell_ids','_crosssell_ids','_product_attributes','_default_attributes'{$customfields})
			OR {$meta}.meta_key LIKE 'attribute_pa_%')
			WHERE p1.ID IN ({$duplicateids})";
		$metavals =  $wpdb->get_results($query);
		if ( is_wp_error($metavals) ) {
			return new WP_Error( 'db_query_error', 
				__( 'Could not execute query' ), $wpdb->last_error );
		} 
	}
		
		foreach($metavals as &$val)
		{
			
			if(array_key_exists($val->ID,$ids))
			{
				$obj = $ids[$val->ID];
				
				$metaval = $val->meta_key;
				if(strpos($metaval,'attribute_pa_') !== FALSE && $obj->post_type == 'product_variation')
				{
					$attname = ucfirst($val->meta_value);
					if($attname !== "")
					{
						if(isset($attrmapslugtoname[$val->meta_value]))
						{
							$attname = $attrmapslugtoname[$val->meta_value];
						}
						$attname = "(". $attname . ")";
					}
					if(property_exists($obj,'post_title'))
					{
						$obj->post_title = $obj->post_title." ".$attname;
					}else
					{
						$obj->post_title = "Variation ".$attname;
					}
					if(is_array($attributes) && !empty($attributes))
					{
						foreach($attributes as $attr)
						{
							$attr_col = 'attribute_pa_'.$attr->name;
							if($attr_col == $metaval)
							{
								foreach($attr->values as $value)
								{
									if($val->meta_value == $value->slug)
									{
										$obj->{$val->meta_key} = $value->name;
										$obj->{$val->meta_key . '_ids'} = $value->term_id;
//										$idmap = array((string)$value->name,'attribute_pa_'.$attr->name);
//										$cats_assoc[$value->id] = $idmap;
										break;
									}
								}
								break;
							}
					    }
					}
					continue;
				}
				if($val->meta_key == '_downloadable_files')
				{
					$downloadable_files = maybe_unserialize($val->meta_value);
						
					if ( $downloadable_files ) 
					{
						if(is_array($downloadable_files))
						{
							$obj->_downloadable_files = "";
							$obj->_downloadable_files_val = "";
							foreach ( $downloadable_files as $key => $file ) 
							{
								$filepath = $file["file"];
								$filename = "";
								if(isset($file["name"]))
									$filename = $file["name"];
//									if($filename != "")
								{
									$obj->_downloadable_files = $obj->_downloadable_files . " Name:" . $filename . " URL:" . $filepath;
									if($obj->_downloadable_files_val == "")
										$obj->_downloadable_files_val = $filename . "#####" . $filepath;
									else
										$obj->_downloadable_files_val = $obj->_downloadable_files_val . "*****" . $filename . "#####" . $filepath;
								}
							}
						}
					}
				}else if($val->meta_key == '_download_type'){
					if($val->meta_value == "")
						$obj->_download_type = "Standard";
					if($val->meta_value == "application")
						$obj->_download_type = "Application";
					if($val->meta_value == "music")
						$obj->_download_type = "Music";
				}else if($val->meta_key == '_visibility'){
					$obj->_visibility = "Catalog/search";
					if($val->meta_value == "visible")
						$obj->_visibility = "Catalog/search";
					if($val->meta_value == "catalog")
						$obj->_visibility = "Catalog";
					if($val->meta_value == "search")
						$obj->_visibility = "Search";
					if($val->meta_value == "hidden")
						$obj->_visibility = "Hidden";
				}else if($val->meta_key == '_tax_class'){
					if($val->meta_value == "")
						$obj->_tax_class = "Standard";
					if($val->meta_value == "reduced-rate")
						$obj->_tax_class = "Reduced Rate";
					if($val->meta_value == "zero-rate")
						$obj->_tax_class = "Zero Rate";
				}
				else if($val->meta_key == '_tax_status')
				{
					if($val->meta_value == "" || $val->meta_value == "taxable")
						$obj->{$val->meta_key} = "Taxable";
					else if($val->meta_value == "shipping")
						$obj->{$val->meta_key} ="Shipping only";
					else if($val->meta_value == "none")
						$obj->{$val->meta_key} ="None";
//						$obj->{$val->meta_key} = $val->meta_value;
				}else if($val->meta_key == '_upsell_ids'){
					if($val->meta_value !== "")
					{
						$sellids = maybe_unserialize($val->meta_value);
						if(is_array($sellids) && count($sellids) > 0)
						{
							$insertstr = '';
							foreach ( $sellids as $curid ) 
							{
								if($insertstr === '')
								{
									$insertstr = (string)$curid;
								}else
								{
									$insertstr.= ', '.(string)$curid;
								}
							}
							$obj->{$val->meta_key} = $insertstr;
						}
						
					}
						
				}else if($val->meta_key == '_crosssell_ids'){
					if($val->meta_value !== "")
					{
						$sellids = maybe_unserialize($val->meta_value);
						if(is_array($sellids) && count($sellids) > 0)
						{
							$insertstr = '';
							foreach ( $sellids as $curid ) 
							{
								if($insertstr === '')
								{
									$insertstr = (string)$curid;
								}else
								{
									$insertstr.= ', '.(string)$curid;
								}
							}
							$obj->{$val->meta_key} = $insertstr;
						}
						
					}
				}else if($val->meta_key == '_backorders'){
					if($val->meta_value == "no")
						$obj->_backorders = "Do not allow";
					if($val->meta_value == "notify")
						$obj->_backorders = "Allow but notify";
					if($val->meta_value == "yes")
						$obj->_backorders = "Allow";
				}else if($val->meta_key == '_sale_price_dates_from' || $val->meta_key == '_sale_price_dates_to'){
					if($val->meta_value !== "")
						$obj->{$val->meta_key} = date('Y-m-d', $val->meta_value);
				}else if($val->meta_key == '_regular_price' || $val->meta_key == '_sale_price'){
					$obj->{$val->meta_key} = str_replace(",",".",$val->meta_value);
				}else if($val->meta_key == '_sold_individually'){
					if($val->meta_value == "")
						$obj->_sold_individually = "no";
					else
						$obj->{$val->meta_key} = $val->meta_value;
				}else if($val->meta_key == '_default_attributes'){
					if($val->meta_value !== "")
					{
						$def_attrs = maybe_unserialize($val->meta_value);
						if(is_array($def_attrs) && count($def_attrs) > 0)
						{
							$value = "";
							foreach($def_attrs as $attr => $def_slug)
							{
								if($value === "")
								{
									$value = $attr.','.$def_slug;
								}else
								{
									$value.= ' ;'.$attr.','.$def_slug;
								}
							}
							
							$obj->{$val->meta_key} = $value;
						}
						
					}
				}else if($val->meta_key == '_product_attributes')
				{
					if($obj->post_parent == 0 || $obj->post_type == 'product')
					{
						if(is_array($attributes))
						{
							$attributes_meta = maybe_unserialize($val->meta_value);
							if (is_array($attributes_meta)) 
							{
								foreach($attributes_meta as $keyattr => $valarray)
								{
	//								$taxonomy_slug = str_replace('attribute_','',$attribute);
		if(array_key_exists($keyattr,$attributekeys))
		{
			$taxonomy_slug = 'attribute_' . $keyattr; 
//			if(property_exists($obj,$taxonomy_slug))
			{
				if(isset( $valarray['is_visible']))
				{
					if(!property_exists($obj,$taxonomy_slug.'_visiblefp'))
					{
						
						$isvars = (int)$valarray['is_visible'];
						if($isvars > 0)
							$isvars = 1;
						$obj->{$taxonomy_slug.'_visiblefp'} = $isvars;
					}
					else
					{
						$oldvalue = (int)$obj->{$taxonomy_slug.'_visiblefp'};
						$isvars = (int)$valarray['is_visible'];
						if($isvars > 0)
							$isvars = 1;
						$oldvalue|= $isvars;
						$obj->{$taxonomy_slug.'_visiblefp'} = $oldvalue;
					}
				}
				if(isset( $valarray['is_variation']))
				{
					if(!property_exists($obj,$taxonomy_slug.'_visiblefp'))
					{
						
						$obj->{$taxonomy_slug.'_visiblefp'} = $valarray['is_variation'];
						$isvars = (int)$valarray['is_variation'];
						if($isvars > 0)
							$isvars = 2;
						else 
							$isvars = 0;
						$obj->{$taxonomy_slug.'_visiblefp'} = $isvars;
					}
					else
					{
						$oldvalue = (int)$obj->{$taxonomy_slug.'_visiblefp'};
						$isvars = (int)$valarray['is_variation'];
						if($isvars > 0)
							$isvars = 2;
						$oldvalue|= $isvars;
						$obj->{$taxonomy_slug.'_visiblefp'} = $oldvalue;
					}
				}
			}
		}
								}
															
							}
						}
					}
				}else{
					$obj->{$val->meta_key} = $val->meta_value;
				}
			}
		}
		unset($metavals);
		$thumbids = "";
		$thumbcounter = 0;
		$thumbsidmap = array();
		$gal_thumbids = "";
		$gal_thumbcounter = 0;
		$gal_thumbsidmap = array();
		$upload_dir = wp_upload_dir();
		if(is_array($upload_dir) && isset($upload_dir['baseurl']))
			$upload_dir = $upload_dir['baseurl'];
		else
			$upload_dir = "";
		
		$converttoutf8 = true;
		if(is_array($curr_settings))
		{
			if(isset($curr_settings['converttoutf8']))
			{
				if($curr_settings['converttoutf8'] == 0)
					$converttoutf8 = false;
			}
		}
		foreach($ids as &$id)
		{
			if($converttoutf8 && function_exists('mb_convert_encoding'))
			{
				$id->post_title =  mb_convert_encoding($id->post_title, "UTF-8");
				$id->post_content =	mb_convert_encoding($id->post_content, "UTF-8");
				$id->post_excerpt =	mb_convert_encoding($id->post_excerpt, "UTF-8");
			}
			if($id->post_parent == 0 ||  $id->post_type == 'product')
			{
				if(is_array($attributes) && !empty($attributes))
				{
					foreach($attributes as $attr)
					{
						if(!property_exists($id,'attribute_pa_'.$attr->name.'_visiblefp'))
							$id->{'attribute_pa_'.$attr->name.'_visiblefp'} = 0;
				    }
				}
			}
			if(!property_exists($id,'_tax_class'))
			{
				$id->_tax_class = "Standard";
			}
			if(!property_exists($id,'_tax_status') && $id->post_type == 'product')
			{
				$id->_tax_status = "Taxable";
			}
			if(property_exists($id,'_downloadable'))
			{
				if($id->_downloadable == "yes")
				{
					if(!property_exists($id,'_download_type'))
					{
						$id->_download_type = "Standard";
					}
				}
			}
			if(property_exists($id,'post_parent'))
			{
				if($id->post_parent == 0 || $id->post_type == 'product')
				{
					if(!property_exists($id,'_stock_status'))
					{
						$id->stock_status = "instock";
					}
				}
			}
			if($upload_dir === "") continue;
			if(property_exists($id,'_thumbnail_id'))
			{
				if($id->_thumbnail_id != "")
				{
					if(array_key_exists($id->_thumbnail_id,$thumbsidmap))
					{
						$oldids = $thumbsidmap[$id->_thumbnail_id];
						$oldids.= ';'. (string)$id->ID;
						$thumbsidmap[$id->_thumbnail_id] = $oldids;
					}else
					{
						$thumbsidmap[$id->_thumbnail_id] = (string)$id->ID;
					}
					
					if($thumbids == "")
					{
						$thumbids = $id->_thumbnail_id;
					}else
					{
						$thumbids.= ',' . $id->_thumbnail_id;
					}
					if($thumbcounter > 100)
					{
						$query ="SELECT post_id,meta_value
						FROM  {$meta} WHERE post_id IN ({$thumbids}) AND meta_key='_wp_attachment_metadata'";
						$metathumbs =  $wpdb->get_results($query);
						if ( false === $metathumbs) {
							$thumbcounter = 0;
							$thumbids = "";
							$metathumbs = array();
						}
						foreach($metathumbs as &$thumb)
			{
				if(array_key_exists($thumb->post_id,$thumbsidmap))
				{
					$thumbidsmul = $thumbsidmap[$thumb->post_id];
					$curthumbids = explode(';',$thumbidsmul);
					foreach($curthumbids as $curthumbid)
					{
					if(array_key_exists($curthumbid,$ids))
					{
						$obj = $ids[$curthumbid];
						$allsizes = maybe_unserialize($thumb->meta_value);
						if ( $allsizes ) 
						{
							if(is_array($allsizes))
							{
								$obj->_thumbnail_id_val = "";
								if(isset($allsizes['file']))
								{
									$dirpart = $allsizes['file'];
									$lastSlash = strrpos($dirpart,"/");
									if(FALSE !== $lastSlash)
									{
										$dirpart = substr($dirpart,0,$lastSlash + 1);
									}else
									{
										$dirpart = "";
									}
									
									$obj->_thumbnail_id_val = $upload_dir.'/'.$allsizes['file'];
									$obj->_thumbnail_id_original = $allsizes['file'];
					if(isset($allsizes['sizes']) && $dirpart !== "")
					{
						$sizes = $allsizes['sizes'];
						//check for thumbnail or medium size to save bandwith
						if(isset($sizes["thumbnail"]) && isset($sizes["thumbnail"]["file"]))
						{
							$obj->_thumbnail_id_val = $upload_dir.'/'.$dirpart.$sizes["thumbnail"]["file"];
						}else if(isset($sizes["shop_thumbnail"]) && isset($sizes["shop_thumbnail"]["file"]))
						{
							$obj->_thumbnail_id_val = $upload_dir.'/'.$dirpart.$sizes["shop_thumbnail"]["file"];
						}else if(isset($sizes["medium"]) && isset($sizes["medium"]["file"]))
						{
							$obj->_thumbnail_id_val = $upload_dir.'/'.$dirpart.$sizes["medium"]["file"];
						}else if(isset($sizes["shop_single"]) && isset($sizes["shop_single"]["file"]))
						{
							$obj->_thumbnail_id_val = $upload_dir.'/'.$dirpart.$sizes["shop_single"]["file"];
						}
					}
								}
								
							}
						}
					}
					}
				}
			}
						$thumbcounter = 0;
						$thumbids = "";
						unset($thumbsidmap);
						$thumbsidmap = array();
					}else
					{
						$thumbcounter++;
					}
				}
			}
			
			//gallery
			if(property_exists($id,'_product_image_gallery'))
			{
				if($id->_product_image_gallery != "")
				{
					if(array_key_exists($id->_product_image_gallery,$gal_thumbsidmap))
					{
						$oldids = $gal_thumbsidmap[$id->_product_image_gallery];
						$oldids.= ';'. (string)$id->ID;
						$gal_thumbsidmap[$id->_product_image_gallery] = $oldids;
					}else
					{
						$gal_thumbsidmap[$id->_product_image_gallery] = (string)$id->ID;
					}
					
					if($gal_thumbids == "")
					{
						$gal_thumbids = $id->_product_image_gallery;
					}else
					{
						$gal_thumbids.= ',' . $id->_product_image_gallery;
					}
					if($gal_thumbcounter > 100)
					{
						$query ="SELECT post_id,meta_value
						FROM  {$meta} WHERE post_id IN ({$gal_thumbids}) AND meta_key='_wp_attachment_metadata'";
						$metathumbs =  $wpdb->get_results($query);
						if ( false === $metathumbs) {
							$gal_thumbcounter = 0;
							$gal_thumbids = "";
							continue;
						}
						$metamap = array();
						foreach($metathumbs as &$thumb)
						{
							$metamap[$thumb->post_id] = $thumb;
						}
						foreach($gal_thumbsidmap as $gal_ids => $prod_ids)
						{
							$gal_ids_arr = explode(',',$gal_ids);
							$val_for_pruduct = "";
							$val_for_pruduct_temp = "";
							$orig_val_for_pruduct = "";
							$orig_val_for_pruduct_temp = "";
							foreach($gal_ids_arr as $imgid)
							{
								if(!array_key_exists($imgid,$metamap)) continue;
								$thumb = &$metamap[$imgid];
								$allsizes = maybe_unserialize($thumb->meta_value);
								if ( !$allsizes ) continue;
								if(!is_array($allsizes)) continue;
								if(!isset($allsizes['file'])) continue;
								$val_for_pruduct_temp = "";
								$orig_val_for_pruduct_temp = "";
								$dirpart = $allsizes['file'];
								$lastSlash = strrpos($dirpart,"/");
								if(FALSE !== $lastSlash)
								{
									$dirpart = substr($dirpart,0,$lastSlash + 1);
								}else
								{
									$dirpart = "";
								}
									
									$val_for_pruduct_temp = $upload_dir.'/'.$allsizes['file'];
									$orig_val_for_pruduct_temp =  $upload_dir.'/'.$allsizes['file'];
						if(isset($allsizes['sizes']) && $dirpart !== "")
						{
							$sizes = $allsizes['sizes'];
							//check for thumbnail or medium size to save bandwith
							if(isset($sizes["thumbnail"]) && isset($sizes["thumbnail"]["file"]))
							{
								$val_for_pruduct_temp = $upload_dir.'/'.$dirpart.$sizes["thumbnail"]["file"];
							}else if(isset($sizes["shop_thumbnail"]) && isset($sizes["shop_thumbnail"]["file"]))
							{
								$val_for_pruduct_temp = $upload_dir.'/'.$dirpart.$sizes["shop_thumbnail"]["file"];
							}else if(isset($sizes["medium"]) && isset($sizes["medium"]["file"]))
							{
								$val_for_pruduct_temp = $upload_dir.'/'.$dirpart.$sizes["medium"]["file"];
							}else if(isset($sizes["shop_single"]) && isset($sizes["shop_single"]["file"]))
							{
								$val_for_pruduct_temp = $upload_dir.'/'.$dirpart.$sizes["shop_single"]["file"];
							}
						}//end if set sizes
								
								if($val_for_pruduct == "")
								{
									$val_for_pruduct = $val_for_pruduct_temp;
								}else
								{
									$val_for_pruduct = $val_for_pruduct . "|" . $val_for_pruduct_temp;
								}
								if($orig_val_for_pruduct == "")
								{
									$orig_val_for_pruduct = $orig_val_for_pruduct_temp;
								}else
								{
									$orig_val_for_pruduct = $orig_val_for_pruduct . "|" . $orig_val_for_pruduct_temp;
								}
								
							}
							$prod_ids_arr = explode(';',$prod_ids);
							foreach($prod_ids_arr as $prodid)
							{
								if(!array_key_exists($prodid,$ids)) continue;
								$obj = $ids[$prodid];
								$obj->_product_image_gallery_val = $val_for_pruduct;
								$obj->_product_image_gallery_original = $orig_val_for_pruduct;
							}
							
						}
						$gal_thumbcounter = 0;
						$gal_thumbids = "";
						unset($gal_thumbsidmap);
						$gal_thumbsidmap = array();
					}else
					{
						$gal_thumbcounter++;
					}
				}
			}
		}
//		return new WP_Error( 'db_query_error', 
//					__( 'Could not execute query' ), $wpdb->last_error );
		if($gal_thumbcounter !== 0 && $gal_thumbids !== "")
		{
			$query ="SELECT post_id,meta_value
			FROM  {$meta} WHERE post_id IN ({$gal_thumbids}) AND meta_key='_wp_attachment_metadata'";
			$metathumbs =  $wpdb->get_results($query);
			if ( false === $metathumbs) {
				$gal_thumbcounter = 0;
				$gal_thumbids = "";
				$metathumbs = array();
			}
			$metamap = array();
			foreach($metathumbs as &$thumb)
			{
				$metamap[$thumb->post_id] = $thumb;
			}
			foreach($gal_thumbsidmap as $gal_ids => $prod_ids)
			{
				$gal_ids_arr = explode(',',$gal_ids);
				$val_for_pruduct = "";
				$val_for_pruduct_temp = "";
				$orig_val_for_pruduct = "";
				$orig_val_for_pruduct_temp = "";
				foreach($gal_ids_arr as $imgid)
				{
					if(!array_key_exists($imgid,$metamap)) continue;
					$thumb = &$metamap[$imgid];
					$allsizes = maybe_unserialize($thumb->meta_value);
					if ( !$allsizes ) continue;
					if(!is_array($allsizes)) continue;
					if(!isset($allsizes['file'])) continue;
					$val_for_pruduct_temp = "";
					$orig_val_for_pruduct_temp = "";
					$dirpart = $allsizes['file'];
					$lastSlash = strrpos($dirpart,"/");
					if(FALSE !== $lastSlash)
					{
						$dirpart = substr($dirpart,0,$lastSlash + 1);
					}else
					{
						$dirpart = "";
					}
						
						$val_for_pruduct_temp = $upload_dir.'/'.$allsizes['file'];
						$orig_val_for_pruduct_temp = $upload_dir.'/'.$allsizes['file'];
			if(isset($allsizes['sizes']) && $dirpart !== "")
			{
				$sizes = $allsizes['sizes'];
				//check for thumbnail or medium size to save bandwith
				if(isset($sizes["thumbnail"]) && isset($sizes["thumbnail"]["file"]))
				{
					$val_for_pruduct_temp = $upload_dir.'/'.$dirpart.$sizes["thumbnail"]["file"];
				}else if(isset($sizes["shop_thumbnail"]) && isset($sizes["shop_thumbnail"]["file"]))
				{
					$val_for_pruduct_temp = $upload_dir.'/'.$dirpart.$sizes["shop_thumbnail"]["file"];
				}else if(isset($sizes["medium"]) && isset($sizes["medium"]["file"]))
				{
					$val_for_pruduct_temp = $upload_dir.'/'.$dirpart.$sizes["medium"]["file"];
				}else if(isset($sizes["shop_single"]) && isset($sizes["shop_single"]["file"]))
				{
					$val_for_pruduct_temp = $upload_dir.'/'.$dirpart.$sizes["shop_single"]["file"];
				}
			}//end if set sizes
					
					if($val_for_pruduct == "")
					{
						$val_for_pruduct = $val_for_pruduct_temp;
					}else
					{
						$val_for_pruduct = $val_for_pruduct . "|" . $val_for_pruduct_temp;
					}
					if($orig_val_for_pruduct == "")
					{
						$orig_val_for_pruduct = $orig_val_for_pruduct_temp;
					}else
					{
						$orig_val_for_pruduct = $orig_val_for_pruduct . "|" . $orig_val_for_pruduct_temp;
					}
					
				}
				$prod_ids_arr = explode(';',$prod_ids);
				foreach($prod_ids_arr as $prodid)
				{
					if(!array_key_exists($prodid,$ids)) continue;
					$obj = $ids[$prodid];
					$obj->_product_image_gallery_val = $val_for_pruduct;
					$obj->_product_image_gallery_original = $orig_val_for_pruduct;
				}
				
			}
			$gal_thumbcounter = 0;
			$gal_thumbids = "";
		}
		if($thumbcounter !== 0 && $thumbids !== "")
		{
			$query = "SELECT post_id,meta_value
			FROM  {$meta} WHERE post_id IN ({$thumbids}) AND meta_key='_wp_attachment_metadata'";
			$metathumbs =  $wpdb->get_results($query);
			if ( false === $metathumbs) {
				$thumbcounter = 0;
				$thumbids = "";
				$metathumbs = array();
			}
			foreach($metathumbs as &$thumb)
			{
				if(array_key_exists($thumb->post_id,$thumbsidmap))
				{
					$thumbidsmul = $thumbsidmap[$thumb->post_id];
					$curthumbids = explode(';',$thumbidsmul);
					foreach($curthumbids as $curthumbid)
					{
					if(array_key_exists($curthumbid,$ids))
					{
						$obj = $ids[$curthumbid];
						$allsizes = maybe_unserialize($thumb->meta_value);
						if ( $allsizes ) 
						{
							if(is_array($allsizes))
							{
								$obj->_thumbnail_id_val = "";
								if(isset($allsizes['file']))
								{
									$dirpart = $allsizes['file'];
									$lastSlash = strrpos($dirpart,"/");
									if(FALSE !== $lastSlash)
									{
										$dirpart = substr($dirpart,0,$lastSlash + 1);
									}else
									{
										$dirpart = "";
									}
									
									$obj->_thumbnail_id_val = $upload_dir.'/'.$allsizes['file'];
									$obj->_thumbnail_id_original = $allsizes['file'];
					if(isset($allsizes['sizes']) && $dirpart !== "")
					{
						$sizes = $allsizes['sizes'];
						//check for thumbnail or medium size to save bandwith
						if(isset($sizes["thumbnail"]) && isset($sizes["thumbnail"]["file"]))
						{
							$obj->_thumbnail_id_val = $upload_dir.'/'.$dirpart.$sizes["thumbnail"]["file"];
						}else if(isset($sizes["shop_thumbnail"]) && isset($sizes["shop_thumbnail"]["file"]))
						{
							$obj->_thumbnail_id_val = $upload_dir.'/'.$dirpart.$sizes["shop_thumbnail"]["file"];
						}else if(isset($sizes["medium"]) && isset($sizes["medium"]["file"]))
						{
							$obj->_thumbnail_id_val = $upload_dir.'/'.$dirpart.$sizes["medium"]["file"];
						}else if(isset($sizes["shop_single"]) && isset($sizes["shop_single"]["file"]))
						{
							$obj->_thumbnail_id_val = $upload_dir.'/'.$dirpart.$sizes["shop_single"]["file"];
						}
					}
								}
								
							}
						}
					}
					
					}
				}
			}
		}
		$cats = array();
		if($arrduplicate === null)
		{
			if($useit != "")
			{
				$useit = " AND {$temptable}.useit=1";
			}
			$query = "SELECT 
				{$temptable}.ID, rel.term_taxonomy_id, term.term_id
				FROM {$temptable}
				INNER JOIN {$term} rel ON {$temptable}.ID=rel.object_id
				INNER JOIN {$term_taxonomy} term ON rel.term_taxonomy_id=term.term_taxonomy_id
				{$useit}";
			$cats = $wpdb->get_results($query);
			if ( is_wp_error($cats) ) {
				return new WP_Error( 'db_query_error', 
					__( 'Could not execute query' ), $wpdb->last_error );
			} 
		}else
		{
			$duplicateids = "";
			foreach($arrduplicate as $key => $value)
			{
				if($duplicateids == "")
					$duplicateids.= $value->ID;
				else
					$duplicateids.= ",".$value->ID;
			}
			
			$query ="SELECT p1.ID, p1.post_title,p1.post_parent, {$meta}.meta_key, {$meta}.meta_value
				FROM {$posts} p1
				INNER JOIN {$meta} ON p1.ID={$meta}.post_id 
				AND ({$meta}.meta_key IN ('_regular_price','_sale_price','_sku','_weight','_length','_width','_height','_stock','_stock_status','_visibility','_virtual','_download_type','_download_limit','_download_expiry','_downloadable_files','_downloadable','_sale_price_dates_from','_sale_price_dates_to','_tax_class','_tax_status','_backorders','_manage_stock','_featured','_purchase_note','_sold_individually','_product_url','_button_text','_thumbnail_id','_product_image_gallery','_upsell_ids','_crosssell_ids','_product_attributes','_default_attributes'{$customfields})
				OR {$meta}.meta_key LIKE 'attribute_pa_%')
				WHERE p1.ID IN ({$duplicateids})";
				
			$query = "SELECT 
				{$posts}.ID, rel.term_taxonomy_id, term.term_id
				FROM {$posts}
				INNER JOIN {$term} rel ON {$posts}.ID=rel.object_id
				INNER JOIN {$term_taxonomy} term ON rel.term_taxonomy_id=term.term_taxonomy_id
				WHERE {$posts}.ID IN ({$duplicateids})";
			$cats = $wpdb->get_results($query);
			if ( is_wp_error($cats) ) {
				return new WP_Error( 'db_query_error', 
					__( 'Could not execute query' ), $wpdb->last_error );
			} 
		}
		
		//categories
//		return new WP_Error( 'db_query_error', 
//					__( 'Could not execute query' ), $wpdb->last_error );
		$cats_assoc = array();
		
		$arrtaxonomies = array();
		$arrtaxonomies[] = 'product_cat';
		$arrtaxonomies[] = 'product_tag';
		$arrtaxonomies[] = 'product_shipping_class';
		$arrtaxonomies[] = 'product_type';
		
		$args_cats = array(
		    'number'     => 99999,
		    'orderby'    => 'slug',
		    'order'      => 'ASC',
		    'hide_empty' => false,
		    'include'    => '',
			'fields'     => 'all'
		);
		
		$sel_fields = get_option('w3exabe_custom');
	
		if(is_array($sel_fields) && !empty($sel_fields))
		{
			foreach($sel_fields as $i => $innerarray)
			{
				if(isset($innerarray['type']))
				{
					if($innerarray['type'] === 'customh' || $innerarray['type'] === 'custom')
					{
						if(taxonomy_exists($i))
						{
							$arrtaxonomies[] = $i;
						}
					}
				}
				
			}
		}
		
		foreach($arrtaxonomies as $taxonomy)
		{
			$woo_categories = get_terms( $taxonomy, $args_cats );

			foreach($woo_categories as $category)
			{
			   if(!is_object($category)) continue;
			   if(!property_exists($category,'term_taxonomy_id')) continue;
			   if(!property_exists($category,'name')) continue;
//			   if($taxonomy == 'product_cat')
//			   	  $idmap = array((string)$category->name,'cats');
//			   else
			  	  $idmap = array((string)$category->name,$taxonomy);
			   $cats_assoc[$category->term_taxonomy_id] = $idmap;
			};
		}
		
		if(is_array($attributes) && !empty($attributes))
		{
			foreach($attributes as $attr)
			{
				if(!property_exists($attr,'values'))
					continue;
				foreach($attr->values as $value)
				{
					if(!property_exists($value,'name') || !property_exists($attr,'name'))
						continue;
				    $idmap = array((string)$value->name,'attribute_pa_'.$attr->name);
					$cats_assoc[$value->id] = $idmap;
				}
		    }
		}
//		return new WP_Error( 'db_query_error', 
//					__( 'Could not execute query' ), $wpdb->last_error );
		foreach($cats as &$val)
		{
			if(!property_exists($val,'ID') || !property_exists($val,'term_id') || !property_exists($val,'term_taxonomy_id'))
				continue;
			if(array_key_exists($val->term_taxonomy_id,$cats_assoc))
			{
				if(array_key_exists($val->ID,$ids))
				{
					$idmap = $cats_assoc[$val->term_taxonomy_id];
					$obj = $ids[$val->ID];
					if(!is_object($obj))
						continue;
					if(!isset($idmap[1]) || !isset($idmap[0]))
						continue;
					if(property_exists($obj,$idmap[1]) && property_exists($obj,$idmap[1] . '_ids'))
					{
						if(strpos($idmap[1],'attribute_pa_') !== FALSE)
						{
							if($obj->post_type != 'product')
								continue;
						} 
						$obj->{$idmap[1]} = $obj->{$idmap[1]}. ', '. $idmap[0];
						$obj->{$idmap[1] . '_ids'} = $obj->{$idmap[1] . '_ids'} . ',' .$val->term_id;
					}else
					{
						$obj->{$idmap[1]} = $idmap[0];
						$obj->{$idmap[1] . '_ids'} = $val->term_id;
					}
				}
			}
		}
}
catch(Exception $e) {
  return $e->getMessage();
}
//		return new WP_Error( 'db_query_error', 
//					__( 'Could not execute query' ), $wpdb->last_error );
		return $info;
	}
	
	public static function saveProducts(&$data,&$children)
	{
		global $wpdb;
		$posts = $wpdb->posts;
		$meta = $wpdb->postmeta;
		$temptable = $wpdb->prefix."wpmelon_advbedit_temp";
		$term = $wpdb->term_relationships;
		$handledchildren = array();
		$sel_fields = get_option('w3exabe_custom');
		$handledattrs = array();
		$attributes = array();
		$attrmapslugtoname = array();
		$parentattrs_cache = array();
		$update_parent_attr = array();
		$update_vars_price = array();
		self::GetAttributes($attributes,$attrmapslugtoname);
		$retarray = array();
		foreach($data as $arrrow)
		{
			if(!is_array($arrrow)) continue;
			$ID = 0;
			if(array_key_exists('ID',$arrrow))
			{
				$ID = (int)$arrrow['ID'];
			
				$parentid = 0;
				if(array_key_exists('post_parent',$arrrow))
					$parentid = (int)$arrrow['post_parent'];
				if(array_key_exists('_sale_price',$arrrow))
					$arrrow['_sale_price'] = str_replace(",",".",$arrrow['_sale_price']);
				if(array_key_exists('_regular_price',$arrrow))
					$arrrow['_regular_price'] = str_replace(",",".",$arrrow['_regular_price']);
				if($ID < 0) continue;
				$where = "";
				$fields = "";
				foreach($arrrow as $i => $Row)
				{
					if(is_array($sel_fields) && !empty($sel_fields))
					{
						if(array_key_exists($i,$sel_fields))
						{
							if(isset($sel_fields[$i]['type']))
							{
								if($sel_fields[$i]['type'] === 'customh')
								{
									if(taxonomy_exists($i))
									{
										$cat_ids = explode(',',$Row);
										$cat_ids = array_map( 'intval', $cat_ids );
										$cat_ids = array_unique( $cat_ids );
										wp_set_object_terms($ID,$cat_ids,$i);
									}
									continue;
								}elseif($sel_fields[$i]['type'] === 'custom')
								{
									if(isset($sel_fields[$i]['isnewvals']) && ($sel_fields[$i]['isnewvals'] === 'true') && taxonomy_exists($i))
									{
										$cat_ids = explode(',',$Row);
										$cat_ids = array_map( 'trim', $cat_ids );
										$cat_ids = array_unique( $cat_ids );
										wp_set_object_terms($ID,$cat_ids,$i);
									}else
									{
										$cat_ids = explode(',',$Row);
										$cat_ids = array_map( 'trim', $cat_ids );
										$cat_ids = array_unique( $cat_ids );
										$new_ids = array();
										foreach($cat_ids as $value)
										{
											if(term_exists($value,$i))
											{
												$new_ids[] = $value;
											}
										}
										wp_set_object_terms($ID,$new_ids,$i);
									}
									continue;
								}
							}
							
						}
					}
					
					switch($i){
						case "post_title"://title
						{
							$query = "UPDATE {$posts} SET post_title='".$Row."' WHERE ID={$ID}";
							$wpdb->query($query);
						}break;
						case "post_content"://desct
						{
							$Row = str_replace("\r\n", "\n",$Row);
							$Row = str_replace("\n", "\r\n",$Row);
							$query = "UPDATE {$posts} SET post_content='".$Row."' WHERE ID={$ID}";
							$wpdb->query($query);
						}break;
						case "post_excerpt":
						{
							$Row = str_replace("\r\n", "\n",$Row);
							$Row = str_replace("\n", "\r\n",$Row);
							$query = "UPDATE {$posts} SET post_excerpt='".$Row."' WHERE ID={$ID}";
							$wpdb->query($query);
						}break;
						case "post_name":
						{
							
							$slug = sanitize_title_with_dashes($Row,'','save');
							$slug = wp_unique_post_slug( $slug, $ID, 'publish', 'product', 0);
							$query = "UPDATE {$posts} SET post_name='{$slug}' WHERE ID={$ID}";
							$wpdb->query($query);
//							if($slug != $Row)
							{
								$newvar = new stdClass();
								$newvar->ID = (string)$ID;
								$newvar->post_name = $slug;
								$permalink = get_permalink($ID);
								if(false !== $permalink)
								{
									$newvar->_product_permalink = $permalink;
								}
								$retarray[] = $newvar;
							}
							
								
						}break;
						case "post_date":
						{
							$date = $Row;
							$date1 = new DateTime($date);
							$date = $date1->format('Y-m-d');
//							$datenow = new DateTime();
							$date = $date.' '.date('H:i:s');
							$date_gmt = get_gmt_from_date($date);
							$query = "UPDATE {$posts} SET post_date='{$date}', post_date_gmt='{$date_gmt}' WHERE ID={$ID}";
							$wpdb->query($query);
						}break;
						case "menu_order":
						{
							$query = "UPDATE {$posts} SET menu_order='".intval($Row)."' WHERE ID={$ID}";
							$wpdb->query($query);
						}break;
						case "comment_status":
						{
							if($Row == 'yes')
								$query = "UPDATE {$posts} SET comment_status='open' WHERE ID={$ID}";
							else
								$query = "UPDATE {$posts} SET comment_status='closed' WHERE ID={$ID}";
							$wpdb->query($query);
						}break;
						case "_visibility":
						{
							$visibility = "visible";
							if($Row == "Catalog/search")
								$visibility = "visible";
							if($Row == "Catalog")
								$visibility = "catalog";
							if($Row == "Search")
								$visibility = "search";
							if($Row == "Hidden")
								$visibility = "hidden";
							update_post_meta( $ID , '_visibility', $visibility);
						}break;
						case "grouped_items":
						{
							$cat_ids = explode(',',$Row);
							$cat_ids = array_map( 'intval', $cat_ids );
							$cat_ids = array_unique( $cat_ids );
							if(count($cat_ids) > 0)
							{
								$query = "UPDATE {$posts} SET post_parent='.$cat_ids[0].' WHERE ID={$ID}";
								$wpdb->query($query);
							}
						}break;
						case "product_cat":
						{
							$cat_ids = explode(',',$Row);
							$cat_ids = array_map( 'intval', $cat_ids );
							$cat_ids = array_unique( $cat_ids );
							wp_set_object_terms($ID,$cat_ids,'product_cat');
						}break;
						case "product_tag":
						{
							$cat_ids = explode(',',$Row);
							$cat_ids = array_map( 'trim', $cat_ids );
							$cat_ids = array_unique( $cat_ids );
							wp_set_object_terms($ID,$cat_ids,'product_tag');
						}break;
						case "product_shipping_class":
						{
							$cat_ids = explode(',',$Row);
							$cat_ids = array_map( 'intval', $cat_ids );
							$cat_ids = array_unique( $cat_ids );
							wp_set_object_terms($ID,$cat_ids,'product_shipping_class');
						}break;
						case "product_type":
						{
							$cat_ids = explode(',',$Row);
							$cat_ids = array_map( 'intval', $cat_ids );
							$cat_ids = array_unique( $cat_ids );
							wp_set_object_terms($ID,$cat_ids,'product_type');
						}break;
						case "_download_expiry":
						{
							update_post_meta( $ID , '_download_expiry',$Row);
						}break;
						case "_download_limit":
						{
							update_post_meta( $ID , '_download_limit', $Row);
						}break;	
						case "_download_type":
						{
							$down_type= "";
							if($Row == "Application")
								$down_type = "application";
							if($Row == "Music")
								$down_type = "music";
							update_post_meta( $ID , '_download_type', $down_type);
						}break;
						case "_downloadable_files":
						{
							 $down_files = array();
							 $files = array();
							 $down_files = explode('*****',$Row);
							 if($down_files)
							 {
							 	 for($i = 0; $i < count($down_files); $i++)
								 {
								 	$itemsarr = $down_files[$i];
									if(!isset($itemsarr) || $itemsarr === "") continue;
								  	  $items =  explode('#####',$itemsarr);
									  $name = "";
									  for($j = 0; $j < count($items); $j++)
								  	  {
									      $item = $items[$j];	
										  if(!isset($item) || $item === "") continue;
										  if($j == 0)
										  {//name
										  	   $name = $item;
										  }else
										  {//url
										  	if($item != "")
											{
										  	   $files[ md5( $item )] = array(
													'name' => $name,
													'file' => $item
												);
											}
										  }
									  }
								  }
							 }else
							 {
							 	  $items =  explode('#####',$Row);
								  $name = "";
								  if($items)
								  {
								  	  for($j = 0; $j < count($items); $j++)
								  	  {
									      $item = $items[$j];	
										  if(!isset($item) || $item === "") continue;
										  if($j == 0)
										  {//name
										  	   $name = $item;
										  }else
										  {//url
										  	if($item != "")
											{
										  	   $files[ md5( $item )] = array(
													'name' => $name,
													'file' => $item
												);
											}
										  }
									  }
								  }
								  
							 }
							self::HandleFiles($ID,$files);
							update_post_meta( $ID , '_downloadable_files', $files );
						}break;
						case "_upsell_ids":
						{
							if($Row === "")
							{
								delete_post_meta( $ID , '_upsell_ids');
							}else
							{
								 $sell_ids = array();
								 $sell_idsch = explode(',',$Row);
								 if($sell_idsch)
								 {
								 	 for($i = 0; $i < count($sell_idsch); $i++)
									 {
									 	$itemsarr = $sell_idsch[$i];
										$itemsarr = trim($itemsarr);
										if(!isset($itemsarr) || $itemsarr === "") continue;
										if(!is_numeric($itemsarr)) continue;
									  	$sell_ids[] = absint($itemsarr);
									  }
								 }
								update_post_meta( $ID , '_upsell_ids', $sell_ids );
							}
						}break;
						case "_crosssell_ids":
						{
							if($Row === "")
							{
								delete_post_meta( $ID , '_crosssell_ids');
							}else
							{
								 $sell_ids = array();
								 $sell_idsch = explode(',',$Row);
								 if($sell_idsch)
								 {
								 	 for($i = 0; $i < count($sell_idsch); $i++)
									 {
									 	$itemsarr = $sell_idsch[$i];
										$itemsarr = trim($itemsarr);
										if(!isset($itemsarr) || $itemsarr === "") continue;
										if(!is_numeric($itemsarr)) continue;
									  	$sell_ids[] = absint($itemsarr);
									  }
								 }
								update_post_meta( $ID , '_crosssell_ids', $sell_ids );
							}
						}break;
						case "post_status":
						{
							$query = "SELECT post_type FROM {$posts} WHERE ID={$ID}";
							$ret = $wpdb->get_var($query);
							if($Row == 'publish' && $ret === 'product')
							{
								$query = "SELECT {$posts}.post_name FROM {$posts} WHERE {$posts}.ID={$ID}";
								$ret = $wpdb->get_var($query);
								if(!is_wp_error($ret) && $ret == '')
								{
									$query = "SELECT post_title, post_date FROM {$posts} WHERE {$posts}.ID={$ID}";
									$ret = $wpdb->get_results($query);
									if(!is_wp_error($ret) && count($ret) == 1)
									{
										$obj = $ret[0];
										$slug = sanitize_title_with_dashes($obj->post_title,'','save');
										$slug = wp_unique_post_slug( $slug, $ID, 'publish', 'product', 0);
										$date_gmt = get_gmt_from_date($obj->post_date);
										$query = "UPDATE {$posts} SET post_name='{$slug}',post_status='publish',post_date_gmt='{$date_gmt}' WHERE ID={$ID}";
										$wpdb->query($query);
//										if($slug != $Row)
										{
											$newvar = new stdClass();
											$newvar->ID = (string)$ID;
											$newvar->post_name = $slug;
											$permalink = get_permalink($ID);
											$newvar->_product_permalink = "";
											if(false !== $permalink)
											{
												$newvar->_product_permalink = $permalink;
											}
											$retarray[] = $newvar;
										}
									}
								}else
								{
									$query = "UPDATE {$posts} SET post_status='".$Row."' WHERE ID={$ID}";
									$wpdb->query($query);
								}
							}else
							{
								$query = "UPDATE {$posts} SET post_status='".$Row."' WHERE ID={$ID}";
								$wpdb->query($query);
							}
						}break;
						case "_sale_price_dates_from":
						{
							$value = strtotime($Row);
							update_post_meta( $ID , $i, $value);
						}break;
						case "_sale_price_dates_to":
						{
							$value = strtotime($Row);
							update_post_meta( $ID , $i, $value);
						}break;
						case "_tax_class":
						{
							$class = "";
							if($Row == "Reduced Rate")
								$class= "reduced-rate";
							if($Row == "Zero Rate")
								$class = "zero-rate";
							update_post_meta( $ID , $i, $class);
						}break;
						case "_tax_status":
						{
							$class = "taxable";
							if($Row == "Shipping only")
								$class= "shipping";
							if($Row == "None")
								$class = "none";
							update_post_meta( $ID , $i, $class);
						}break;
						case "_sold_individually":
						{
							$back = "";
							if($Row == "no")
								$back = "";
							if($Row == "yes")
								$back = "yes";
							update_post_meta( $ID , $i, $back);
						}break;
						case "_backorders":
						{
							$back = "no";
							if($Row == "Do not allow")
								$back = "no";
							if($Row == "Allow but notify")
								$back = "notify";
							if($Row == "Allow")
								$back = "yes";
							update_post_meta( $ID , $i, $back);
						}break;
						case "_default_attributes":
						{
							 $def_attrs = array();
							 $cur_attr = array();
							 $all_attrs = explode(';',$Row);
							 if(is_array($all_attrs) && count($all_attrs) > 0)
							 {
							 	 for($i = 0; $i < count($all_attrs); $i++)
								 {
								 	$itemsarr = $all_attrs[$i];
									$itemsarr = trim($itemsarr);
									if(!isset($itemsarr) || $itemsarr === "") continue;
								  	  $items =  explode(',',$itemsarr);
									  $name = "";
									  if(!is_array($items)) continue;
									  $cur_attr = array();
									  for($j = 0; $j < count($items); $j++)
								  	  {
									      $item = $items[$j];	
										  if(!isset($item) || $item === "") continue;
										  if($j == 0)
										  {//name
										  	   $name = $item;
										  }else
										  {//url
										  	if($item != "")
											{
										  	  $def_attrs[$name] = $item;
											}
										  }
									  }
								  }
							 }
							update_post_meta( $ID , '_default_attributes', $def_attrs );
						}break;
						default:
						{
							if($i !== 'ID' && $i !== 'post_parent' && $i !== 'parent')
							{
								if(strpos($i,"attribute_pa_",0) === 0 && strpos($i,"_visiblefp",0) === FALSE)
								{
//									return $i;
									self::HandleAttrs($ID,$parentid,$parentattrs_cache,$attributes,$Row,$i,count($data),$update_parent_attr);
								}elseif(strpos($i,"attribute_pa_",0) === 0 && strpos($i,"_visiblefp",0) !== FALSE)
								{
									$query = "SELECT post_type FROM {$wpdb->posts} WHERE ID={$ID}";
									$ret = $wpdb->get_var($query);
//									$arrret['i'] = $i;
									if($ret === 'product')//check by post_type if($parentid == 0)
									{
										$patt = get_post_meta($ID,'_product_attributes',true);
										$taxonomy_slug = "";
										$pos = strpos($i,"attribute_");
										if ($pos !== false) {
										    $taxonomy_slug = substr_replace($i,"",$pos,strlen("attribute_"));
										}
										$taxonomy_slug = str_replace('_visiblefp','',$taxonomy_slug);
										if(!is_array($patt))
										{
											$patt = array();
										}
										 if(!isset($patt[$taxonomy_slug]))
										 {
										 	$patt[$taxonomy_slug] = array();
											$patt[$taxonomy_slug]["name"] = $taxonomy_slug;
											$patt[$taxonomy_slug]["is_visible"]   = 0;
											$patt[$taxonomy_slug]["is_taxonomy"]  = 1;
											$patt[$taxonomy_slug]["value"]  = "";
											$patt[$taxonomy_slug]["position"] = count($patt);
										 }
										 {
										 	$val = (int)$Row;
										 	if($val & 1)
												$patt[$taxonomy_slug]["is_visible"]   = 1;
											else
												$patt[$taxonomy_slug]["is_visible"]   = 0;
											if($val & 2)
												$patt[$taxonomy_slug]["is_variation"]   = 1;
											else
												$patt[$taxonomy_slug]["is_variation"]   = 0;													
											update_post_meta($ID,'_product_attributes',$patt);
										 }
									}
									
								}else
								{
									if( strpos($Row,":",0) !== FALSE && strpos($Row,";",0) !== FALSE &&strpos($Row,"{",0) !== FALSE &&strpos($Row,"}",0) !== FALSE)
									{
										$query = "SELECT meta_id FROM {$meta} WHERE post_id={$ID} AND meta_key='{$i}'";
										$ret = $wpdb->get_var($query);
										if($ret === NULL)
										{
											$query = "INSERT INTO {$meta} (post_id,meta_key,meta_value)
							 					 VALUES ({$ID},'{$i}','{$Row}');";
											$ret = $wpdb->query($query);
										}else
										{
											$query = "UPDATE {$meta} SET meta_value='".$Row."' WHERE meta_id={$ret}";
											$wpdb->query($query);
										}
									}else
									{
										update_post_meta( $ID , $i, $Row);
									}
								}
							}
						}
							break;
					}
				}
				if($parentid > 0)
				{
					if(array_key_exists('_stock_status',$arrrow) || array_key_exists('_manage_stock',$arrrow) || array_key_exists('_stock',$arrrow))
					{
						if(function_exists("wc_delete_product_transients"))
							wc_delete_product_transients($parentid);
					}
				}
				if(array_key_exists('_featured',$arrrow))
				{
					delete_transient( 'wc_featured_products' );
				}
				
				if(array_key_exists('_sale_price',$arrrow) || array_key_exists('_regular_price',$arrrow) || array_key_exists('_sale_price_dates_from',$arrrow) || array_key_exists('_sale_price_dates_to',$arrrow))
				{
					self::HandlePriceUpdate($ID,$parentid,$arrrow);	
					if($parentid > 0)
					{
						if(!array_key_exists($parentid,$update_vars_price))
							$update_vars_price[] = $parentid;
					}
				}
				
				if(function_exists('wc_get_product'))
				{
					$curr_settings = get_option('w3exabe_settings');
					if(is_array($curr_settings))
					{
						if(isset($curr_settings['calldoaction']))
						{
							if($curr_settings['calldoaction'] == 1)
							{
								$product = wc_get_product($ID);
								if(!empty($product) && is_object($product))
								{
									do_action( 'woocommerce_product_quick_edit_save',$product);
								}
							}
						}
					}
				}
				
			}
			
				
		}
		
		foreach($update_vars_price as $item_id)
		{
			self::HandleSaleRemove($parentid);
		}
				
		$attrarrays = array();
		if(is_array($attributes) && !empty($attributes))
		{
			foreach($attributes as $attr)
			{
				if(!property_exists($attr,'name'))
					continue;
				$attrarrays[] = 'pa_'.$attr->name;
		    }
		}
		$bdontcheckusedfor = false;
		$curr_settings = get_option('w3exabe_settings');
		if(is_array($curr_settings))
		{
			if(isset($curr_settings['dontcheckusedfor']))
			{
				if($curr_settings['dontcheckusedfor'] == 1)
					$bdontcheckusedfor = true;
			}
		}
		foreach($data as $arrrow)
		{
			if(!is_array($arrrow)) continue;
			$ID = 0;
			if(array_key_exists('ID',$arrrow))
			{
				$ID = (int)$arrrow['ID'];
			
				$parentid = 0;
				if(array_key_exists('post_parent',$arrrow))
					$parentid = (int)$arrrow['post_parent'];
				if($parentid != 0) continue;
				$updatemeta = false;
				
				foreach($arrrow as $i => $Row)
				{
					if(strpos($i,"attribute_pa_",0) === 0)
					{
						$updatemeta = true;
						break;
					}
				}
				if($updatemeta)
				{
					$bvariable = false;
					if(is_object_in_term( $ID, 'product_type', 'variable' ))
						$bvariable = true;
						
					{
						$patt = get_post_meta($ID,'_product_attributes',true);
						if(!is_array($patt))
							$patt = array();
						$attrs = wp_get_object_terms($ID,$attrarrays);
						if(is_array($attrs))
						{
//							foreach($patt as $key => $value)
//							{
//								$haskey = false;
//								foreach($attrs as $attr_obj)
//								{
//									if(!is_object($attr_obj)) continue;
//									if(!property_exists($attr_obj,'taxonomy')) continue;
//									if($key == $attr_obj->taxonomy)
//									{
//										$haskey = true;
//										break;
//									}
//								}
//								if(!$haskey)
//								{
//									unset($patt[$key]);
//								}
//							}
							foreach($attrs as $attr_obj)
							{
								if(!is_object($attr_obj)) continue;
								if(!property_exists($attr_obj,'term_id')) continue;
								if(!property_exists($attr_obj,'name')) continue;
								if(!property_exists($attr_obj,'taxonomy')) continue;
								if(!isset($patt[$attr_obj->taxonomy]))
								{
									$patt[$attr_obj->taxonomy] = array();
									$patt[$attr_obj->taxonomy]["name"] = $taxonomy_slug;
									$patt[$attr_obj->taxonomy]["is_visible"]   = 0;
									$patt[$attr_obj->taxonomy]["is_taxonomy"]  = 1;
									if($bvariable && !$bdontcheckusedfor)
										$patt[$attr_obj->taxonomy]["is_variation"] = 1;
									else
										$patt[$attr_obj->taxonomy]["is_variation"] = 0;
									$patt[$attr_obj->taxonomy]["value"]  = "";
									$patt[$attr_obj->taxonomy]["position"] = count($patt);
								}
							}
							update_post_meta($ID,'_product_attributes',$patt);
						}
					}
				}
				
			}
		}
		foreach($update_parent_attr as $parid => $attrarrays)
		{
			$newpar = new stdClass();
			$newpar->ID = $parid;
			$newpar->post_parent = "0";
			$attrs = wp_get_object_terms($parid,$attrarrays);
			
			if(is_array($attrs))
			{
				foreach($attrs as $attr_obj)
				{
					if(!is_object($attr_obj)) continue;
					if(!property_exists($attr_obj,'term_id')) continue;
					if(!property_exists($attr_obj,'name')) continue;
					if(!property_exists($attr_obj,'taxonomy')) continue;
					$attr_prop = 'attribute_'.$attr_obj->taxonomy;
					if(!property_exists($newpar,$attr_prop))
					{
						$newpar->{$attr_prop} = $attr_obj->name;
						self::UpdateParentMeta($parid,$attr_obj->taxonomy);
						$newpar->{$attr_prop . '_visiblefp'} = 2;
					}else
					{
						$newpar->{$attr_prop} = $newpar->{$attr_prop}.', '. $attr_obj->name;
					}
					$attr_ids = 'attribute_'.$attr_obj->taxonomy.'_ids';
					if(!property_exists($newpar,$attr_ids))
					{
						$newpar->{$attr_ids} = (string)$attr_obj->term_id;
					}else
					{
						$newpar->{$attr_ids} = $newpar->{$attr_ids}.','.(string)$attr_obj->term_id;
					}
				}
				
			}
			$retarray[] = $newpar;
		}
		return $retarray;
	}
	
	public static function HandlePriceUpdate($ID,$parentid,&$arrrow)
	{
		$saleprice = 0;
		$regprice = 0;
		$salefrom = "";
		$saleto = "";
		if(array_key_exists('_sale_price',$arrrow))
		{
			$saleprice = (float)$arrrow['_sale_price'];
		}else
		{
			$saleprice = (float)get_post_meta($ID,'_sale_price',true);
		}
		if(array_key_exists('_regular_price',$arrrow))
		{
			$regprice = (float)$arrrow['_regular_price'];
		}else
		{
			$regprice = (float)get_post_meta($ID,'_regular_price',true);
		}
		
		if($saleprice > 0)
		{
			if(array_key_exists('_sale_price_dates_from',$arrrow))
			{
				$salefrom = $arrrow['_sale_price_dates_from'];
			}else
			{
				$salefrom = get_post_meta($ID,'_sale_price_dates_from',true);
				if($salefrom != "")
				{
					$salefrom = maybe_unserialize($salefrom);
					$salefrom = date('Y-m-d',$salefrom);
				}
			}
			if(array_key_exists('_sale_price_dates_to',$arrrow))
			{
				$saleto = $arrrow['_sale_price_dates_to'];
			}else
			{
				$saleto = get_post_meta($ID,'_sale_price_dates_to',true);
				if($saleto != "")
				{
					$saleto = date('Y-m-d',(float)$saleto);
				}
			}
			if($salefrom !== "")
			{
				$dt = time();
				$salefromd = strtotime($salefrom);//date('Y-m-d', $salefrom);
				if($saleto !== "")
				{
					$saletod = strtotime($saleto);
					if($salefromd <= $dt && $saletod >= $dt)
					{
						update_post_meta($ID,'_price',$saleprice);
						return;
					}else
					{
						update_post_meta($ID,'_price',$regprice);
						return;
					}
				}
			}
			if($saleto !==  "")
			{
				$dt = time();
				$saletod = strtotime($saleto);
				if($saletod >= $dt)
				{
					update_post_meta($ID,'_price',$saleprice);
					return;
				}else
				{
					update_post_meta($ID,'_price',$regprice);
					return;
				}
			}
			update_post_meta($ID,'_price',$saleprice);
			return;
		}
		update_post_meta($ID,'_price',$regprice);
	}
	
	
	public static function addProducts($prodcount)
	{
		global $wpdb;
		$posts = $wpdb->posts;
		$meta = $wpdb->postmeta;
		$temptable = $wpdb->prefix."wpmelon_advbedit_temp";
		$term = $wpdb->term_relationships;
		$retarray = array();
		
		$insfields = array(
			"_sku"  => "",
   			"_virtual"   => "no",
			"_downloadable"  => "no",
			"_weight"   => "",
			"_length"   => "",
			"_height"   => "",
			"_width"   => "",
			"_manage_stock"   => "no",
			"_stock_status"   => "instock",
			"_visibility" => "visible",
			"total_sales" => "0",
			"_purchase_note" => "",
			"_featured" => "no",
			"_backorders" => "no",
			"_sold_individually" => "",
			"_product_image_gallery" => "",
			"_regular_price"   => "",
			"_sale_price"   => "",
			"_sale_price_dates_from"   => "",
			"_sale_price_dates_to"   => "",
			"_price"   => "",
			"_download_limit"   => "",
			"_download_expiry"   => "",
			"_downloadable_files"   => "",
		);
		
		$product_data = array();
		$product_data['post_status'] = 'draft';
		$product_data['post_title'] = 'New Product';
		$product_data['post_type'] = 'product';			
		$product_data['post_parent'] = 0;
		$prod_term = get_term_by('slug','simple','product_type');
		for($i = 0; $i < $prodcount; $i++)
		{
			$post_id = wp_insert_post($product_data,true);
			if(is_wp_error($post_id))
			{
				return $post_id;
			}
			
			wp_set_object_terms($post_id,'simple','product_type',true);
			
			update_post_meta($post_id,'_product_attributes',array());
			
			$newvar = new stdClass();
			$newvar->ID = (string)$post_id;
			$newvar->post_parent = '0';
			if(property_exists($prod_term,'term_id'))
			{
				$newvar->product_type = 'simple';
				$newvar->product_type_ids =(string)$prod_term->term_id;
			}
			$newvar->post_type = 'product';
			
			foreach($insfields as $column => $value)
			{
				$query = "INSERT INTO {$meta} (post_id,meta_key,meta_value)
					  VALUES ({$post_id},'{$column}','{$value}');";
			
				$ret = $wpdb->query($query);
				if ( is_wp_error($ret) )
				{
					return $ret;
				} 
			}

			foreach($insfields as $column => $value)
			{
				$newvar->{$column} = $value;
			}
			$newvar->_visibility = "Catalog/search";
			$newvar->_backorders = "Do not allow";
			$newvar->_tax_class = "Standard";
			$newvar->post_title = 'New Product';
			$newvar->post_status = 'draft';
			$newvar->menu_order = '0';
			$retarray[] = $newvar;
		}
		
		
		return $retarray;
	}
	
	public static function addVariations(&$data,&$children)
	{
		global $wpdb;
		$posts = $wpdb->posts;
		$meta = $wpdb->postmeta;
		$temptable = $wpdb->prefix."wpmelon_advbedit_temp";
		$term = $wpdb->term_relationships;
		$retarray = array();
		$attributes = array();
		$attrmapslugtoname = array();
		$parentattrs_cache = array();
		$update_parent_attr = array();
		self::GetAttributes($attributes,$attrmapslugtoname);
		$attributekeys = array();
		
		$parentid = 0;
		
		$menu_order = 0;
		
		$arr_handled_attr = array();
		
		$insfields = array(
			"_sku"  => "",
   			"_thumbnail_id" => "0",
   			"_virtual"   => "no",
			"_downloadable"  => "no",
			"_weight"   => "",
			"_length"   => "",
			"_height"   => "",
			"_width"   => "",
			"_manage_stock"   => "no",
			"_stock_status"   => "instock",
			"_regular_price"   => "",
			"_sale_price"   => "",
			"_sale_price_dates_from"   => "",
			"_sale_price_dates_to"   => "",
			"_price"   => "",
			"_download_limit"   => "",
			"_download_expiry"   => "",
			"_downloadable_files"   => "",
		);
		
		$madevarparents = array();
		
		foreach($data as $varrow)
		{
			//create variation
			if(array_key_exists('post_parent',$varrow[0]))
				$parentid = (int)$varrow[0]['post_parent'];
			if($parentid == 0)
			{
				return new WPError('Invalid Parent');
			}
			//make sure it is variable
			if(!array_key_exists($parentid,$madevarparents))
			{
				wp_set_object_terms($parentid,'variable','product_type',false);
				$query = "SELECT COUNT({$posts}.ID) FROM {$posts} WHERE post_parent={$parentid} AND post_type='product_variation';";
				$ret = $wpdb->get_var($query);
				$menu_order = 0;
				if ( !is_wp_error($ret) )
				{
					$menu_order = (int)$ret;
				} 
				if(function_exists("wc_delete_product_transients"))
					wc_delete_product_transients($parentid);
				$madevarparents[$parentid] = $menu_order;
			}
			
			
			$product_data = array();
			$menu_order = $madevarparents[$parentid];
			$product_data['menu_order'] = $madevarparents[$parentid];
			$menu_order++;
			$madevarparents[$parentid] = $menu_order;
			$product_data['post_status'] = 'publish';
			$product_data['post_title'] = 'Variation #'.$parentid.' of ';
			$product_data['post_type'] = 'product_variation';			
			$product_data['post_parent'] = $parentid;
			$post_id = wp_insert_post($product_data,true);
			if(is_wp_error($post_id))
			{
				return $post_id;
			}
			$newvar = new stdClass();
			$newvar->ID = (string)$post_id;
			$newvar->post_parent = (string)$parentid;
			$newvar->post_type = 'product_variation';
			$attributename = '';
			foreach($varrow as $arrrow)
			{
				if(!is_array($arrrow)) continue;
				$attrname = $arrrow['attribute'];
				$attvalue = $arrrow['value'];
				$query = "INSERT INTO {$meta} (post_id,meta_key,meta_value)
							  VALUES ({$post_id},'{$attrname}','{$attvalue}');";
					
				$ret = $wpdb->query($query);
				if ( is_wp_error($ret) )
				{
					return $ret;
				} 
			}
			foreach($insfields as $column => $value)
			{
				
				$query = "INSERT INTO {$meta} (post_id,meta_key,meta_value)
					  VALUES ({$post_id},'{$column}','{$value}');";
			
				$ret = $wpdb->query($query);
				if ( is_wp_error($ret) )
				{
					return $ret;
				} 
			}
//			if($attributename == '')
//				$attributename = $product_data['post_title'];
			foreach($insfields as $column => $value)
			{
				$newvar->{$column} = $value;
			}
			foreach($varrow as $arrrow)
			{
				if(!is_array($arrrow)) continue;
				$attrname = $arrrow['attribute'];
				$attvalue = $arrrow['value'];
				
				if($attvalue != '')
				{
					if(isset($attrmapslugtoname[$attvalue]))
					{
						if($attributename !='')
							$attributename.= " (". $attrmapslugtoname[$attvalue] . ")";
						else
							$attributename.= " (". $attrmapslugtoname[$attvalue] . ")";
					}
					$outbreak = false;
					foreach($attributes as $attr)
					{
						foreach($attr->values as $value)
						{
							if($value->slug == $attvalue )
							{
								$newvar->{$attrname} = $value->name;
								$newvar->{$attrname.'_ids'} = $value->term_id;
								if(!array_key_exists($attrname,$arr_handled_attr))
								{
									$arr_handled_attr[] = $attrname;
									self::HandleAttrs($post_id,$parentid,$parentattrs_cache,$attributes,$value->term_id,$attrname,count($varrow),$update_parent_attr,true);
								}
								$outbreak = true;
								break;
							}
						}
						if($outbreak)
							break;
					}
				}
				
				
				
			}
			$newvar->post_title = $attributename;
			$newvar->post_status = 'publish';
			$newvar->_tax_class = "Standard";
			$newvar->menu_order = (string)$madevarparents[$parentid];
			$retarray[] = $newvar;
		}
		foreach($update_parent_attr as $parid => $attrarrays)
		{
			$newpar = new stdClass();
			$newpar->ID = $parid;
			$newpar->post_parent = "0";
			$attrs = wp_get_object_terms($parid,$attrarrays);
			
			if(is_array($attrs))
			{
				foreach($attrs as $attr_obj)
				{
					if(!is_object($attr_obj)) continue;
					if(!property_exists($attr_obj,'term_id')) continue;
					if(!property_exists($attr_obj,'name')) continue;
					if(!property_exists($attr_obj,'taxonomy')) continue;
					$attr_prop = 'attribute_'.$attr_obj->taxonomy;
					if(!property_exists($newpar,$attr_prop))
					{
						$newpar->{$attr_prop} = $attr_obj->name;
						self::UpdateParentMeta($parid,$attr_obj->taxonomy);
						$newpar->{$attr_prop.'_visiblefp'} = 2;
					}else
					{
						$newpar->{$attr_prop} = $newpar->{$attr_prop}.', '. $attr_obj->name;
					}
					$attr_ids = 'attribute_'.$attr_obj->taxonomy.'_ids';
					if(!property_exists($newpar,$attr_ids))
					{
						$newpar->{$attr_ids} = (string)$attr_obj->term_id;
					}else
					{
						$newpar->{$attr_ids} = $newpar->{$attr_ids}.','.(string)$attr_obj->term_id;
					}
				}
				
			}
			$retarray[] = $newpar;
		}
		return $retarray;
	}
	
	public static function deleteProducts(&$data,$type)
	{
		global $wpdb;
		$posts = $wpdb->posts;
		$meta = $wpdb->postmeta;
		$term = $wpdb->term_relationships;
		$updatevarsmeta = array();
		foreach($data as $arrrow)
		{
			if(!is_array($arrrow)) continue;
			$ID = 0;
			if(array_key_exists('ID',$arrrow))
			{
				$ID = (int)$arrrow['ID'];
			
				$parentid = 0;
				$post_status = "draft";
				if(array_key_exists('post_parent',$arrrow))
					$parentid = (int)$arrrow['post_parent'];
				if(array_key_exists('post_status',$arrrow))
					$post_status = (string)$arrrow['post_status'];
				if($ID < 0) continue;
				if($type === "0")
				{
					if($parentid != 0) continue;
					$query = "UPDATE {$posts}
							  SET {$posts}.post_status='trash'
							  WHERE  {$posts}.ID={$ID}";
					$ret = $wpdb->query($query);
					if ( is_wp_error($ret) ) {
						return new WP_Error( 'db_query_error', 
							__( 'Could not execute query' ), $wpdb->last_error );
					} 
					update_post_meta($ID,'_wp_trash_meta_status',$post_status);
					update_post_meta($ID,'_wp_trash_meta_time',time());
				}elseif($type === "1")
				{
					if($parentid == 0)
					{//check if variable
						if(is_object_in_term( $ID, 'product_type', 'variable' ))
						{
							$query = "SELECT ID from {$posts} WHERE post_parent={$ID} AND (post_type='product_variation')";
							$childids =  $wpdb->get_results($query);
							if(!is_wp_error($childids) && is_array($childids))
							{
								foreach($childids as $childobj)
								{
									$childid = $childobj->ID;
									$query = "DELETE FROM {$posts}
											  WHERE  {$posts}.ID={$childid}";
									$ret = $wpdb->query($query);
									if ( is_wp_error($ret) ) {
										return new WP_Error( 'db_query_error', 
											__( 'Could not execute query' ), $wpdb->last_error );
									} 
									$query = "DELETE FROM {$meta}
											  WHERE  {$meta}.post_id={$childid}";
									$ret = $wpdb->query($query);
									if ( is_wp_error($ret) ) {
										return new WP_Error( 'db_query_error', 
											__( 'Could not execute query' ), $wpdb->last_error );
									} 
								}
							}
							if(function_exists("wc_delete_product_transients"))
								wc_delete_product_transients($ID);
						}
					}else
					{
						if(!array_key_exists($parentid,$updatevarsmeta))
							$updatevarsmeta[] = $parentid;
					}
					$query = "DELETE FROM {$posts}
							  WHERE  {$posts}.ID={$ID}";
					$ret = $wpdb->query($query);
					if ( is_wp_error($ret) ) {
						return new WP_Error( 'db_query_error', 
							__( 'Could not execute query' ), $wpdb->last_error );
					} 
					$query = "DELETE FROM {$meta}
							  WHERE  {$meta}.post_id={$ID}";
					$ret = $wpdb->query($query);
					if ( is_wp_error($ret) ) {
						return new WP_Error( 'db_query_error', 
							__( 'Could not execute query' ), $wpdb->last_error );
					} 
					$query = "DELETE FROM {$term}
							  WHERE  {$term}.object_id={$ID}";
					$ret = $wpdb->query($query);
					if ( is_wp_error($ret) ) {
						return new WP_Error( 'db_query_error', 
							__( 'Could not execute query' ), $wpdb->last_error );
					} 
					if($parentid != 0)
					{
						if(function_exists("wc_delete_product_transients"))
							wc_delete_product_transients($parentid);
					}
					
				}				
			}	
		}
		foreach($updatevarsmeta as $item_id)
		{
			self::HandleSaleRemove($item_id);
		}
	}

	public static function DuplicateProduct(&$arrrow,&$retarray)
	{
		global $wpdb;
		$posts = $wpdb->posts;
		$meta = $wpdb->postmeta;
		$term = $wpdb->term_relationships;
		
		$ID = (int)$arrrow['ID'];
			
		$parentid = 0;

		if($ID < 0) return;
		$post = get_post($ID);
		if($post === null || !is_object($post)) return;
		if($post->post_type != 'product' ) return;
		
		$new_post_author    = wp_get_current_user();
		$new_post_date      = current_time( 'mysql' );
		$new_post_date_gmt  = get_gmt_from_date( $new_post_date );
		
		$post_parent = 0;
		$post_status = 'draft';
		$suffix = ' ' . __( '(Copy)', 'woocommerce' );
		if ( $parentid > 0 ) 
		{
			$post_parent        = $parentid;
			$post_status        = 'publish';
			$suffix             = '';
		}
	    
		$arrpostdata = array(
				'post_author'               => $new_post_author->ID,
				'post_date'                 => $new_post_date,
				'post_date_gmt'             => $new_post_date_gmt,
				'post_content'              => $post->post_content,
				'post_content_filtered'     => $post->post_content_filtered,
				'post_title'                => $post->post_title . $suffix,
				'post_excerpt'              => $post->post_excerpt,
				'post_status'               => $post_status,
				'post_type'                 => $post->post_type,
				'comment_status'            => $post->comment_status,
				'ping_status'               => $post->ping_status,
				'post_password'             => $post->post_password,
				'to_ping'                   => $post->to_ping,
				'pinged'                    => $post->pinged,
				'post_modified'             => $new_post_date,
				'post_modified_gmt'         => $new_post_date_gmt,
				'post_parent'               => $post_parent,
				'menu_order'                => $post->menu_order,
				'post_mime_type'            => $post->post_mime_type
			);
			
		$wpdb->insert(
			$wpdb->posts,
			$arrpostdata
		);
		
		$new_post_id = $wpdb->insert_id;
		
		
		$newvar = new stdClass();
		$newvar->ID = (string)$new_post_id;
		$newvar->post_parent = $post_parent;
		$newvar->post_type = 'product';
		

		foreach($arrpostdata as $column => $value)
		{
			$newvar->{$column} = $value;
		}
		
		
	
		self::duplicate_post_taxonomies( $post->ID, $new_post_id, $post->post_type );

		self::duplicate_post_meta( $post->ID, $new_post_id, $newvar);
		
		$retarray[] = $newvar;
		// Copy the children (variations)
		if ( $children_products = get_children( 'post_parent='.$post->ID.'&post_type=product_variation' ) ) 
		{

			if ( $children_products ) 
			{
				$post_parent = $new_post_id;
				foreach ( $children_products as $child ) 
				{
					$varid = absint($child->ID);

					if ( ! $varid ) 
					{
						continue;
					}

					$variations = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID=$varid" );
					
					if(!is_array($variations)) continue;
					if(count($variations) === 0) continue;
					if(!is_object($variations[0])) continue;
					
					$variation = $variations[0];
					
					$arrpostdata = array(
							'post_author'               => $new_post_author->ID,
							'post_date'                 => $new_post_date,
							'post_date_gmt'             => $new_post_date_gmt,
							'post_content'              => $variation->post_content,
							'post_content_filtered'     => $variation->post_content_filtered,
							'post_title'                => $variation->post_title,
							'post_excerpt'              => $variation->post_excerpt,
							'post_status'               => $variation->post_status,
							'post_type'                 => $variation->post_type,
							'comment_status'            => $variation->comment_status,
							'ping_status'               => $variation->ping_status,
							'post_password'             => $variation->post_password,
							'to_ping'                   => $variation->to_ping,
							'pinged'                    => $variation->pinged,
							'post_modified'             => $new_post_date,
							'post_modified_gmt'         => $new_post_date_gmt,
							'post_parent'               => $post_parent,
							'menu_order'                => $variation->menu_order,
							'post_mime_type'            => $variation->post_mime_type
						);
						
					$wpdb->insert(
						$wpdb->posts,
						$arrpostdata
					);
	
					$new_post_id = $wpdb->insert_id;
					$newvar = new stdClass();
					$newvar->ID = (string)$new_post_id;
					$newvar->post_parent = $post_parent;
					$newvar->post_type = 'product_variation';
					

					foreach($arrpostdata as $column => $value)
					{
						$newvar->{$column} = $value;
					}
					
					self::duplicate_post_taxonomies( $variation->ID, $new_post_id, $variation->post_type );

					self::duplicate_post_meta( $variation->ID, $new_post_id, $newvar);
					
					$retarray[] = $newvar;
				}
			}
		}
	}
	
	public static function duplicateProducts(&$data,$count=1)
	{
		$retarray = array();
		
		$counter = 0;
		foreach($data as $arrrow)
		{
			if(!is_array($arrrow)) continue;
			$ID = 0;
			if(!array_key_exists('ID',$arrrow)) continue;
			{
				$counter = 0;
				while($counter < $count && $counter <= 100)
				{
					self::DuplicateProduct($arrrow,$retarray);
					$counter++;
				}
			}	
		}
		$total = 0;
		$hasnext = false;
		$isbegin = false;
		
		if(count($retarray) === 0) return $retarray;
		
		self::loadProducts(null,null,null,null,null,null,$total,false,false,$hasnext,$isbegin,false,null,null,null,null,null,$retarray);
		return $retarray;
	}
	
	public static function duplicate_post_taxonomies( $id, $new_id, $post_type ) 
	{

		$taxonomies = get_object_taxonomies( $post_type );

		foreach ( $taxonomies as $taxonomy ) 
		{

			$post_terms = wp_get_object_terms( $id, $taxonomy );
			$post_terms_count = sizeof( $post_terms );

			for ( $i=0; $i<$post_terms_count; $i++ ) 
			{
				wp_set_object_terms( $new_id, $post_terms[$i]->slug, $taxonomy, true );
			}
		}
	}

	
	public static function duplicate_post_meta( $id, $new_id, &$postobject) 
	{
		global $wpdb;

		$post_meta_infos = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=%d AND meta_key NOT IN ( 'total_sales' );", absint( $id ) ) );

		if ( count( $post_meta_infos ) != 0 ) 
		{

			$sql_query_sel = array();
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

			foreach ( $post_meta_infos as $meta_info ) 
			{
				$meta_key = $meta_info->meta_key;
				$meta_value = addslashes( $meta_info->meta_value );
				$postobject->{$meta_key} = $meta_value;
				$sql_query_sel[]= "SELECT $new_id, '$meta_key', '$meta_value'";
			}

			$sql_query.= implode( " UNION ALL ", $sql_query_sel );
			$wpdb->query($sql_query);
		}
	}
	
	public static function HandleCatParams(&$catparams)
	{
		$newarr = array();
		foreach($catparams as $cat)
		{
			
			 $args = array(
		     'number'     => 99999,
		     'orderby'    => 'slug',
		     'order'      => 'ASC',
		     'hide_empty' => false,
		     'include'    => '',
			 'fields'     => 'all',
			 'child_of'    => (int)$cat
			);
		
		
			$woo_categories = get_terms( 'product_cat', $args );

			foreach($woo_categories as $category)
			{
			    if(!is_object($category)) continue;
			    if(!property_exists($category,'term_taxonomy_id')) continue;
			    if(!property_exists($category,'term_id')) continue;
				if(!in_array($category->term_taxonomy_id,$catparams))
					$newarr[] = $category->term_taxonomy_id;
			};
		}
		$catparams = array_merge($catparams,$newarr);
	}
	
	public static function GetAttributes(&$attributes,&$attrmapslugtoname)
	{
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
		
		foreach($woo_attrs as $attr){
			$att         = new stdClass();
			$att->id     = $attr['attribute_id'];
			$att->name   = $attr['attribute_name'];  
			$att->label  = $attr['attribute_label']; 
			$attr_label = substr($att->label,0,100);
			$attr_label = preg_replace('/\s+/', ' ', trim($attr_label));
			$att->label = $attr_label;
			$attr_name = substr($att->name,0,100);
			$attr_name = preg_replace('/\s+/', ' ', trim($attr_name));
			$att->name = $attr_name;
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
				$val_label = substr($value->slug,0,100);
				$val_label = preg_replace('/\s+/', ' ', trim($val_label));
				$value->slug = $val_label;
				$val_name = substr($value->name,0,100);
				$val_name = preg_replace('/\s+/', ' ', trim($val_name));
				$value->name = $val_name;
				$attrmapslugtoname[$value->slug] = $value->name;
				
				$value->parent  = $val->parent;
				$att->values[]  = $value;
			}
			
		 	if(count($att->values) > 0)
			{
				$attributes[]                = $att;
			}
		}
	}
	
	public static function HandleFiles($ID,&$downloadable_files)
	{
		global $wpdb;
		$product_id = $ID;
		$existing_download_ids = array_keys( (array) get_post_meta($ID, '_downloadable_files', true) );
		$updated_download_ids  = array_keys( (array) $downloadable_files );

		$new_download_ids      = array_filter( array_diff( $updated_download_ids, $existing_download_ids ) );
		$removed_download_ids  = array_filter( array_diff( $existing_download_ids, $updated_download_ids ) );

		if ( $new_download_ids || $removed_download_ids ) {
			// determine whether downloadable file access has been granted via the typical order completion, or via the admin ajax method
			$existing_permissions = $wpdb->get_results( $wpdb->prepare( "SELECT * from {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE product_id = %d GROUP BY order_id", $product_id) );

			foreach ( $existing_permissions as $existing_permission ) {
//				$order = new WC_Order( $existing_permission->order_id );

				if ( $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE ID = %d", $existing_permission->order_id ) ) ) 
				{ 
					// Remove permissions
					if ( $removed_download_ids ) {
						foreach ( $removed_download_ids as $download_id ) {
							if ( apply_filters( 'woocommerce_process_product_file_download_paths_remove_access_to_old_file', true, $download_id, $product_id, $order ) ) {
								$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s", $existing_permission->order_id, $product_id, $download_id ) );
							}
						}
					}
					// Add permissions
					if ( $new_download_ids ) {
						foreach ( $new_download_ids as $download_id ) {
							if ( apply_filters( 'woocommerce_process_product_file_download_paths_grant_access_to_new_file', true, $download_id, $product_id, $order ) ) {
								// grant permission if it doesn't already exist
								if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT 1 FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s", $existing_permission->order_id, $product_id, $download_id ) ) ) {
									self::copied_wc_downloadable_file_permission( $download_id, $product_id, $existing_permission->order_id  );
								}
							}
						}
					}
				}
			}
		}
	}
	
	public static function copied_wc_downloadable_file_permission( $download_id, $product_id, $order_id ) 
	{
		global $wpdb;
	
		$user_email = sanitize_email( get_post_meta($order_id,'_billing_email',true));//$order->billing_email );
		$limit      = trim( get_post_meta( $product_id, '_download_limit', true ) );
		$expiry     = trim( get_post_meta( $product_id, '_download_expiry', true ) );

		$limit      = empty( $limit ) ? '' : absint( $limit );
		$user_id = get_post_meta( $order_id, '_customer_user', true );
		$order_key = get_post_meta( $order_id, '_order_key', true );
		// Default value is NULL in the table schema
		$expiry     = empty( $expiry ) ? null : absint( $expiry );

		if ( $expiry ) {
			$order_completed_date = date_i18n( "Y-m-d", strtotime( get_post_meta($order_id,'_completed_date',true) ) );
			$expiry = date_i18n( "Y-m-d", strtotime( $order_completed_date . ' + ' . $expiry . ' DAY' ) );
		}

		$data = apply_filters( 'woocommerce_downloadable_file_permission_data', array(
			'download_id'			=> $download_id,
			'product_id' 			=> $product_id,
			'user_id' 				=> absint( $user_id ),
			'user_email' 			=> $user_email,
			'order_id' 				=> $order_id,
			'order_key' 			=> $order_key,
			'downloads_remaining' 	=> $limit,
			'access_granted'		=> current_time( 'mysql' ),
			'download_count'		=> 0
		));

		$format = apply_filters( 'woocommerce_downloadable_file_permission_format', array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d'
		), $data);

		if ( ! is_null( $expiry ) ) {
				$data['access_expires'] = $expiry;
				$format[] = '%s';
		}

		// Downloadable product - give access to the customer
		$result = $wpdb->insert( $wpdb->prefix . 'woocommerce_downloadable_product_permissions',
			$data,
			$format
		);

		do_action( 'woocommerce_grant_product_download_access', $data );

		return $result ? $wpdb->insert_id : false;
	}
	
	public static function HandleSaleRemove($parentid)
	{
		$children = array();
		$childids = array();
		global $wpdb;
		$posts = $wpdb->posts;
		$meta = $wpdb->postmeta;
		$temptable = $wpdb->prefix."wpmelon_advbedit_temp";
		$query = "SELECT ID from {$posts} WHERE post_parent={$parentid} AND (post_type='product_variation')";
		$childids =  $wpdb->get_results($query);
		
		if(!is_array($childids)) return;
		if(count($childids) == 0) return;
		
		$idin = "";
		foreach($childids as $id)
		{
			/*$hasid = false;
			foreach($children as $key)
			{
				if($key['ID'] == $id->ID)
				{
					$hasid = true;
					break;
				}
			}
			if($hasid) continue;*/
			if($idin == "")
			{
				$idin = "(".$id->ID;
			}else
			{
				$idin = $idin.",".$id->ID;
			}			
		}
		if($idin != "")
		{
			$idin = $idin.")";
			$query = "SELECT post_id,meta_value,meta_key FROM {$meta} WHERE (meta_key='_sale_price' OR meta_key='_regular_price')  AND post_id IN ".$idin;
			$items =  $wpdb->get_results($query);
			foreach($items as $obj)
			{
				$newitem = array();
				$added = false;
				foreach($children as &$child)
				{
					if($child['ID'] == $obj->post_id)
					{
						$newitem = $child;
						if($obj->meta_key == '_sale_price')
							$child['_sale_price'] = $obj->meta_value;
						else
							$child['_regular_price'] = $obj->meta_value;
						$added = true;
						break;
					}
				}
				if($added) continue;
				$newitem['parentid'] = $parentid;
				$newitem['ID'] = $obj->post_id;
				if($obj->meta_key == '_sale_price')
					$newitem['_sale_price'] = $obj->meta_value;
				else
					$newitem['_regular_price'] = $obj->meta_value;
				$children[] = $newitem;
			}
		}
		$biggestval = $lowestval = $biggestid = $lowestid = -1;
		$hasitemwithnosale = false;
		$arrnosale = array();
		
		
		foreach($children as $key)
		{
			if($key['parentid'] != $parentid) continue;
			$sale_price = $key['_sale_price'];
			$sale_price = trim($sale_price);
			if($sale_price == "")
			{
				$hasitemwithnosale = true;
				$arritem = array();
				$arritem['_regular_price'] = $key['_regular_price'];
				$arritem['ID'] = $key['ID'];
				$arrnosale[] = $arritem;
				continue;
			}
			////check if sale does not apply
			
			$salefrom = "";
			$saleto = "";
			$salefrom = get_post_meta($key['ID'],'_sale_price_dates_from',true);
			if($salefrom != "")
			{
				$salefrom = date('Y-m-d',$salefrom);
			}
			$saleto = get_post_meta($key['ID'],'_sale_price_dates_to',true);
			if($saleto != "")
			{
				$saleto = date('Y-m-d',$saleto);
			}
			if($saleto !== "" || $salefrom !== "")
			{
				if($salefrom !== "")
				{
					$dt = time();
					$salefromd = strtotime($salefrom);//date('Y-m-d', $salefrom);
					if($saleto !== "")
					{
						$saletod = strtotime($saleto);
						if($salefromd > $dt || $saletod < $dt)
						{//sale is off
							$sale_price = "";
							$hasitemwithnosale = true;
							$arritem = array();
							$arritem['_regular_price'] = $key['_regular_price'];
							$arritem['ID'] = $key['ID'];
							$arrnosale[] = $arritem;
							continue;
						}
					}
				}
				if($saleto !==  "")
				{
					$dt = time();
					$saletod = strtotime($saleto);
					if($saletod < $dt)
					{
						$sale_price = "";
						$hasitemwithnosale = true;
						$arritem = array();
						$arritem['_regular_price'] = $key['_regular_price'];
						$arritem['ID'] = $key['ID'];
						$arrnosale[] = $arritem;
						continue;
					}
				}
			}
			/////
			
			if($biggestval == -1 && $lowestval == -1)
			{
				$biggestval = (float)$sale_price;
				$lowestval = (float)$sale_price;
				$biggestid = $key['ID'];
				$lowestid = $key['ID'];
				continue;
			}
			$sale_price = (float)$sale_price;
			if($sale_price > $biggestval)
			{
				$biggestval = $sale_price;
				$biggestid = $key['ID'];
			}elseif($sale_price < $lowestval)
			{
				$lowestval =$sale_price;
				$lowestid = $key['ID'];
			}
		}
		$biggestvalreg = $lowestvalreg = $biggestidreg = $lowestidreg = -1;
		foreach($children as $key)
		{
			if($key['parentid'] != $parentid) continue;
			$reg_price = $key['_regular_price'];
			$reg_price = trim($reg_price);
			if($reg_price == "") continue;
			if($biggestvalreg == -1 && $lowestvalreg == -1)
			{
				$biggestvalreg = (float)$reg_price;
				$lowestvalreg = (float)$reg_price;
				$biggestidreg = $key['ID'];
				$lowestidreg = $key['ID'];
				continue;
			}
			$reg_price = (float)$reg_price;
			if($reg_price > $biggestvalreg)
			{
				$biggestvalreg = $reg_price;
				$biggestidreg = $key['ID'];
			}elseif($reg_price < $lowestvalreg)
			{
				$lowestvalreg =$reg_price;
				$lowestidreg = $key['ID'];
			}
			if($hasitemwithnosale)
			{//take reg as biggest
				foreach( $arrnosale as $arrnosaleitem)
				{
					$regprice1 = $arrnosaleitem['_regular_price'];
					$regprice1 = trim($regprice1);
					if($regprice1 == "") continue;
					$regprice1 = (float)$regprice1;
					if($regprice1 > $biggestval)
					{
						$biggestval = (float)$regprice1;
						$biggestid = $arrnosaleitem['ID'];
					}
				}
				
			}
		}
		
		if($biggestval == -1)
		{// all sale prices deleted
//			$query = "UPDATE {$meta} SET meta_value = CASE meta_key WHEN '_min_variation_sale_price' THEN '' 
//			WHEN '_max_variation_sale_price' THEN ''
//			WHEN '_min_sale_price_variation_id' THEN '' 
//			WHEN '_max_sale_price_variation_id' THEN ''
//			ELSE meta_value END WHERE meta_key IN ('_min_variation_sale_price','_max_variation_sale_price','_min_sale_price_variation_id','_max_sale_price_variation_id') AND post_id={$parentid}";
//			$wpdb->query($query);
			update_post_meta($parentid,'_max_variation_sale_price','');
			update_post_meta($parentid,'_min_sale_price_variation_id','');
			update_post_meta($parentid,'_max_sale_price_variation_id','');
//			$query = "UPDATE {$meta} SET meta_value = CASE meta_key 
//			WHEN '_min_variation_price' THEN '{$lowestvalreg}' 
//			WHEN '_max_variation_price' THEN '{$biggestvalreg}'
//			WHEN '_min_variation_regular_price' THEN '{$lowestvalreg}' 
//			WHEN '_max_variation_regular_price' THEN '{$biggestvalreg}'
//			WHEN '_min_regular_price_variation_id' THEN '{$lowestidreg}' 
//			WHEN '_max_regular_price_variation_id' THEN '{$biggestidreg}'
//			WHEN '_min_price_variation_id' THEN '{$lowestidreg}' 
//			WHEN '_max_price_variation_id' THEN '{$biggestidreg}'
//			WHEN '_price' THEN '{$lowestvalreg}'
//			ELSE meta_value END WHERE meta_key IN ('_min_variation_regular_price','_max_variation_regular_price','_min_regular_price_variation_id','_max_regular_price_variation_id','_min_variation_price','_max_variation_price','_max_price_variation_id','_min_price_variation_id','_price') AND post_id={$parentid}";
//			$wpdb->query($query);
			update_post_meta($parentid,'_min_variation_price',$lowestvalreg);
			update_post_meta($parentid,'_max_variation_price',$biggestvalreg);
			update_post_meta($parentid,'_min_variation_regular_price',$lowestvalreg);
			update_post_meta($parentid,'_max_variation_regular_price',$biggestvalreg);
			update_post_meta($parentid,'_min_regular_price_variation_id',$lowestidreg);
			update_post_meta($parentid,'_max_regular_price_variation_id',$biggestidreg);
			update_post_meta($parentid,'_min_price_variation_id',$lowestidreg);
			update_post_meta($parentid,'_max_price_variation_id',$biggestidreg);
			update_post_meta($parentid,'_price',$lowestvalreg);
		}else
		{
//			$query = "UPDATE {$meta} SET meta_value = CASE meta_key 
//			WHEN '_min_variation_sale_price' THEN '{$lowestval}' 
//			WHEN '_max_variation_sale_price' THEN '{$biggestval}'
//			WHEN '_min_sale_price_variation_id' THEN '{$lowestid}'
//			WHEN '_max_sale_price_variation_id' THEN '{$biggestid}'
//			ELSE meta_value END WHERE meta_key IN ('_min_variation_sale_price','_max_variation_sale_price','_min_sale_price_variation_id','_max_sale_price_variation_id') AND post_id={$parentid}";
//			$wpdb->query($query);
			update_post_meta($parentid,'_min_variation_sale_price',$lowestval);
			update_post_meta($parentid,'_max_variation_sale_price',$biggestval);
			update_post_meta($parentid,'_min_sale_price_variation_id',$lowestid);
			update_post_meta($parentid,'_max_sale_price_variation_id',$biggestid);
//			$query = "UPDATE {$meta} SET meta_value = CASE meta_key 
//			WHEN '_min_variation_price' THEN '{$lowestval}' 
//			WHEN '_max_variation_price' THEN '{$biggestval}'
//			WHEN '_min_variation_regular_price' THEN '{$lowestvalreg}' 
//			WHEN '_max_variation_regular_price' THEN '{$biggestvalreg}'
//			WHEN '_min_regular_price_variation_id' THEN '{$lowestidreg}' 
//			WHEN '_max_regular_price_variation_id' THEN '{$biggestidreg}'
//			WHEN '_min_price_variation_id' THEN '{$lowestid}' 
//			WHEN '_max_price_variation_id' THEN '{$biggestid}'
//			WHEN '_price' THEN '{$lowestval}'
//			ELSE meta_value END WHERE meta_key IN ('_min_variation_regular_price','_max_variation_regular_price','_min_regular_price_variation_id','_max_regular_price_variation_id','_min_variation_price','_max_variation_price','_max_price_variation_id','_min_price_variation_id','_price') AND post_id={$parentid}";
//			$wpdb->query($query);
			update_post_meta($parentid,'_min_variation_price',$lowestval);
			update_post_meta($parentid,'_max_variation_price',$biggestval);
			update_post_meta($parentid,'_min_variation_regular_price',$lowestvalreg);
			update_post_meta($parentid,'_max_variation_regular_price',$biggestvalreg);
			update_post_meta($parentid,'_min_regular_price_variation_id',$lowestidreg);
			update_post_meta($parentid,'_max_regular_price_variation_id',$biggestidreg);
			update_post_meta($parentid,'_min_price_variation_id',$lowestid);
			update_post_meta($parentid,'_max_price_variation_id',$biggestid);
			update_post_meta($parentid,'_price',$lowestval);
		}
//		$handledchildren[] = $parentid;
	}
	
	public static function exportProducts(&$data,&$children)
	{
		$dir = dirname(__FILE__);
		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh))) {
			$ibegin = strpos($filename,"temp.csv",0);
	 		if( $ibegin !== FALSE)
			{
				@unlink($dir."/".$filename);
			}
		}
		$randomint = rand();
		$purl = $dir. "/" .$randomint. "temp.csv";
		$df = fopen($purl, 'w');
		if($df)
		{
//			fputcsv($df, array_keys(reset($data)));
//			foreach ($data as $row) {
//			  fputcsv($df, $row);
//			}
			$data = stripslashes($data);
			if(function_exists('mb_convert_encoding'))
				$data = mb_convert_encoding($data, "UTF-8");
			fwrite($df, pack("CCC",0xef,0xbb,0xbf)); 
			fwrite($df,$data); 
			fclose($df);
		}
		return ($randomint ."temp.csv");
	}
	
	public static function convertSaveArrays(&$data,&$ids,&$children,&$cids,$vars = false)
	{
//		$newarr = array();
//		$ids = array();
		if($vars)
		{
			$counter = 0;
			foreach($data as $field => $items)
			{
				$itemsr = explode('#$',$items);
				foreach($itemsr as $item)
				{
					$values = explode('$#',$item);
					if(count($values) !== 3) continue;
					$newarritem = array();
					$newarritem['post_parent'] = $values[0];
					$newarritem['attribute'] = $values[1];
					$newarritem['value'] = $values[2];
					if(array_key_exists($counter,$ids))
					{
						$ids[$counter][] = $newarritem;
					}else
					{
						$ids[$counter] = array();
						$ids[$counter][] = $newarritem;
					}
				}
				$counter++;
			}
			unset($data);
			return;
		}
		foreach($data as $field => $items)
		{
			$itemsr = explode('#$',$items);
			foreach($itemsr as $item)
			{
				$values = explode('$#',$item);
				if(count($values) !== 3) continue;
				if(array_key_exists($values[0],$ids))
				{
					$arritem = &$ids[$values[0]];
					$arritem[$field] = $values[2];
				}else
				{
					$newarritem = array();
//					$newarr[] = $newarritem;
					$newarritem['ID'] = $values[0];
					$newarritem['post_parent'] = $values[1];
					$newarritem[$field] = $values[2];
					$ids[$values[0]] = $newarritem;
				}
//				$values[0]; //ID
//				$values[1]; //value
			}
		}
		unset($data);
		if(count($children) == 0) return;
		$itemsr = explode('#$',$children['children']);
		foreach($itemsr as $item)
		{
			$values = explode('#',$item);
			if(count($values) !== 4) continue;
			if(array_key_exists($values[0],$cids))
			{
				$arritem = &$cids[$values[0]];
				$newarritem['_regular_price'] = $values[2];
				$newarritem['_sale_price'] = $values[3];
			}else
			{
				$newarritem = array();
				$newarritem['ID'] = $values[0];
				$newarritem['parentid'] = $values[1];
				$newarritem['_regular_price'] = $values[2];
				$newarritem['_sale_price'] = $values[3];
				$cids[$values[0]] = $newarritem;
			}
		}
		unset($children);
	}
	
	public static function UpdateParentMeta($parentid,$taxonomy_slug,$bcreatevars = false)
	{
		$bdontcheckusedfor = true;
		$curr_settings = get_option('w3exabe_settings');
		if(is_array($curr_settings))
		{
			if(isset($curr_settings['dontcheckusedfor']))
			{
				if($curr_settings['dontcheckusedfor'] == 0)
					$bdontcheckusedfor = false;
			}
		}
		if($bcreatevars)
			$bdontcheckusedfor = false;
		$patt = get_post_meta($parentid,'_product_attributes',true);
		if(is_array($patt))
		{
			 if(isset($patt[$taxonomy_slug]))
			 {
			 	if(!$bdontcheckusedfor)
					$patt[$taxonomy_slug]["is_variation"] = 1;
			 }else
			 {
			 	$patt[$taxonomy_slug] = array();
				$patt[$taxonomy_slug]["name"] = $taxonomy_slug;
				$patt[$taxonomy_slug]["is_visible"]   = 0;
				$patt[$taxonomy_slug]["is_taxonomy"]  = 1;
				if($bdontcheckusedfor)
					$patt[$taxonomy_slug]["is_variation"] = 0;
				else
					$patt[$taxonomy_slug]["is_variation"] = 1;
				$patt[$taxonomy_slug]["value"]  = "";
				$patt[$taxonomy_slug]["position"] = count($patt);
			 }
			 update_post_meta($parentid,'_product_attributes',$patt);
		}else
		{
			$patt = array();
			$patt[$taxonomy_slug] = array();
			$patt[$taxonomy_slug]["name"] = $taxonomy_slug;
			$patt[$taxonomy_slug]["is_visible"]   = 0;
			$patt[$taxonomy_slug]["is_taxonomy"]  = 1;
			if($bdontcheckusedfor)
				$patt[$taxonomy_slug]["is_variation"] = 0;
			else
				$patt[$taxonomy_slug]["is_variation"] = 1;
			$patt[$taxonomy_slug]["value"]  = "";
			$patt[$taxonomy_slug]["position"] = 0;
			update_post_meta($parentid,'_product_attributes',$patt);
		}
	}
	
	public static function HandleAttrs($ID,$parentid,&$parentattrs_cache,&$attributes,$values,$attribute,$countdata,&$update_parent_attr,$bcreatevars = false)
	{
		global $wpdb;
		$bdontcheckusedfor = true;
		$curr_settings = get_option('w3exabe_settings');
		if(is_array($curr_settings))
		{
			if(isset($curr_settings['dontcheckusedfor']))
			{
				if($curr_settings['dontcheckusedfor'] == 0)
					$bdontcheckusedfor = false;
			}
		}
		if($bcreatevars)
			$bdontcheckusedfor = false;
		$taxonomy_slug = "";
		$pos = strpos($attribute,"attribute_");
		if ($pos !== false) {
		    $taxonomy_slug = substr_replace($attribute,"",$pos,strlen("attribute_"));
		}
		if($parentid == 0)
			$key_for_cache = ((string)$ID).$taxonomy_slug;
		else
			$key_for_cache = ((string)$parentid).$taxonomy_slug;

		$cat_ids = explode(',',$values);
		
		$query = "SELECT post_type FROM {$wpdb->posts} WHERE ID={$ID}";
		$ret = $wpdb->get_var($query);
		
		if($ret === 'product')//check by post_type
		{
//			$parentid == 0;
//			$key_for_cache = ((string)$ID).$taxonomy_slug;
			$ids_for_insert = array();
			//check for cache from a child, add only, their attribute has been added and cached
			if(array_key_exists($key_for_cache,$parentattrs_cache))
			{
				$cached_ids = $parentattrs_cache[$key_for_cache];
				if(is_array($cached_ids))
				{
					foreach($cat_ids as $val_id)
					{//as ids
						if(!array_key_exists($val_id,$cached_ids))	
						{
							$ids_for_insert[] = $val_id;
						}
					}
					if(count($ids_for_insert) > 0)
					{
						$ids_for_insert = array_map( 'intval', $ids_for_insert );
						$ids_for_insert = array_unique( $ids_for_insert );
						wp_set_object_terms($ID,$ids_for_insert,$taxonomy_slug,true);
						$cached_ids = array_merge($cached_ids,$ids_for_insert);
						$parentattrs_cache[$key_for_cache] = $cached_ids;
					}
				}
			}else
			{//set and DON'T insert in cache
				if(count($cat_ids) === 1 && $cat_ids[0] === "")
				{
					unset($cat_ids);
					$cat_ids = array();
				}
				if(count($cat_ids) === 0)
				{
					wp_set_object_terms($ID,NULL,$taxonomy_slug);
				}else
				{
					$cat_ids = array_map( 'intval', $cat_ids );
					$cat_ids = array_unique( $cat_ids );
					wp_set_object_terms($ID,$cat_ids,$taxonomy_slug);
				}
				
				$bvariable = false;
				if(is_object_in_term( $ID, 'product_type', 'variable' ))
					$bvariable = true;
						
				if($countdata === 1 || !$bvariable)
				{//single parent, check if variable and update meta
					
					$patt = get_post_meta($ID,'_product_attributes',true);
					if(count($cat_ids) === 0)
					{
						if(is_array($patt) && isset($patt[$taxonomy_slug]))
						{
							unset($patt[$taxonomy_slug]);
							update_post_meta($ID,'_product_attributes',$patt);
						}
							
					}else
					{
						if(is_array($patt))
						{
							 if(!isset($patt[$taxonomy_slug]))
							 {
							 	$patt[$taxonomy_slug] = array();
								$patt[$taxonomy_slug]["name"] = $taxonomy_slug;
								$patt[$taxonomy_slug]["is_visible"]   = 0;
								$patt[$taxonomy_slug]["is_taxonomy"]  = 1;
								if($bvariable && !$bdontcheckusedfor)
									$patt[$taxonomy_slug]["is_variation"] = 1;
								else
									$patt[$taxonomy_slug]["is_variation"] = 0;
								$patt[$taxonomy_slug]["value"]  = "";
								$patt[$taxonomy_slug]["position"] = count($patt);
								update_post_meta($ID,'_product_attributes',$patt);
							 }
							
						}else
						{
							$patt = array();
							$patt[$taxonomy_slug] = array();
							$patt[$taxonomy_slug]["name"] = $taxonomy_slug;
							$patt[$taxonomy_slug]["is_visible"]   = 0;
							$patt[$taxonomy_slug]["is_taxonomy"]  = 1;
							if($bvariable && !$bdontcheckusedfor)
								$patt[$taxonomy_slug]["is_variation"] = 1;
							else
								$patt[$taxonomy_slug]["is_variation"] = 0;
							$patt[$taxonomy_slug]["value"]  = "";
							$patt[$taxonomy_slug]["position"] = 0;
							update_post_meta($ID,'_product_attributes',$patt);
						}
					}
				}
			}
			
		}else
		{
			if($parentid === 0)
				return;
			if(count($cat_ids) > 1)
			{
				$cat_ids = array_splice($cat_ids, 1);
			}
			if(count($cat_ids) === 1 && $cat_ids[0] === "")
			{
				unset($cat_ids);
				$cat_ids = array();
			}
			$cat_ids = array_map( 'intval', $cat_ids );
			$cat_ids = array_unique( $cat_ids );
			if(array_key_exists($key_for_cache,$parentattrs_cache))
			{
				$cached_ids = $parentattrs_cache[$key_for_cache];
				if(is_array($cached_ids))
				{
					foreach($cat_ids as $val_id)
					{//as ids
						if(!array_key_exists($val_id,$cached_ids))	
						{
							$ids_for_insert[] = $val_id;
						}
					}
					if(count($ids_for_insert) > 0)
					{
						$ids_for_insert = array_map( 'intval', $ids_for_insert );
						$ids_for_insert = array_unique( $ids_for_insert );
						wp_set_object_terms($parentid,$ids_for_insert,$taxonomy_slug,true);
						$cached_ids = array_merge($cached_ids,$ids_for_insert);
						$parentattrs_cache[$key_for_cache] = $cached_ids;
//						self::UpdateParentMeta($parentid,$taxonomy_slug);
					}
				}
			}else
			{//set and insert in cache
				if(count($cat_ids) > 0)
				{
					$ids_for_insert = array();
					$product_terms = wp_get_object_terms( $parentid, $taxonomy_slug);
					foreach($product_terms as $term_value)
					{//as ids
						if(!is_object($term_value)) continue;
			   			if(!property_exists($term_value,'term_taxonomy_id')) continue;
						$ids_for_insert[] = $term_value->term_taxonomy_id;
					}
					if(is_array($ids_for_insert))
					{
						if(!array_key_exists($cat_ids[0],$ids_for_insert))	
						{//add taxonomy term
							wp_set_object_terms($parentid,(int)$cat_ids[0],$taxonomy_slug,true);
							$ids_for_insert[] = (int)$cat_ids[0];
//							if(isset($update_parent_attr[$parentid]))
//							{
//								$arr_attrs_update = $update_parent_attr[$parentid];
//								if(!isset($arr_attrs_update[$taxonomy_slug]))
//								{
//									$arr_attrs_update[$taxonomy_slug] = 1;
//								}
//							}else
//							{
//								$arr_attrs_update = array();
//								$arr_attrs_update[$taxonomy_slug] = 1;
//								$update_parent_attr[$parentid] = $arr_attrs_update;
//							}
							if(isset($update_parent_attr[$parentid]))
							{
								$arr_attrs_update = $update_parent_attr[$parentid];
								if(!array_key_exists($taxonomy_slug,$arr_attrs_update))
								{
									$arr_attrs_update[] = $taxonomy_slug;
								}
							}else
							{
								$arr_attrs_update = array();
								$arr_attrs_update[] = $taxonomy_slug;
								$update_parent_attr[$parentid] = $arr_attrs_update;
							}
						}
						$parentattrs_cache[$key_for_cache] = $ids_for_insert;
						self::UpdateParentMeta($parentid,$taxonomy_slug,$bcreatevars);
					}
				}
			}
			if(count($cat_ids) > 0)
			{//get term slug
				$term = get_term( $cat_ids[0], $taxonomy_slug );
				if($term && is_object($term) && property_exists($term,'slug'))
				{
					$slug = $term->slug; 
					update_post_meta( $ID , $attribute, $slug);
				}
			}else
			{
				update_post_meta( $ID , $attribute, '');
			}
		}
		
	}
   	
	public static function FindCustomFields($data)
	{
		global $wpdb;
		$meta = $wpdb->postmeta;
		$posts = $wpdb->posts;
		$query = "SELECT post_parent 
					FROM {$posts}
					WHERE ID={$data} AND (post_type='product' OR post_type='product_variation')";
		$metas =  $wpdb->get_var($query);
		if(is_wp_error($metas) || $metas === NULL)
		{
			return -1;
		}		
		
		$query = "SELECT meta_key,meta_value from {$meta} WHERE post_id={$data} AND meta_key NOT IN ('_regular_price','_sale_price','_sku','_weight','_length','_width','_height','_stock','_stock_status','_visibility','_virtual','_download_type','_download_limit','_download_expiry','_downloadable_files','_downloadable','_sale_price_dates_from','_sale_price_dates_to','_tax_class','_tax_status','_backorders','_manage_stock','_featured','_purchase_note','_sold_individually','_product_url','_button_text','_thumbnail_id','_product_image_gallery','_upsell_ids','_crosssell_ids','_product_attributes','_default_attributes','_price','_edit_lock','_edit_last','_min_variation_price','_max_variation_price','_min_price_variation_id','_max_price_variation_id','_min_variation_regular_price','_max_variation_regular_price','_min_regular_price_variation_id','_max_regular_price_variation_id','_min_variation_sale_price','_max_variation_sale_price','_min_sale_price_variation_id','_max_sale_price_variation_id','_file_paths') AND meta_key NOT LIKE 'attribute_pa_%'";
		$metas =  $wpdb->get_results($query);
		return $metas;
	}
	
	public static function FindCustomTaxonomies()
	{
		$taxonomies = get_taxonomies(array('object_type' => array('product'),'_builtin' => false)); 
		$metas = array();
		$attributes = array();
		$attrmapslugtoname = array();
		self::GetAttributes($attributes,$attrmapslugtoname);
		
		foreach ( $taxonomies as $taxonomy ) 
		{
			if($taxonomy !== "product_tag" && $taxonomy !== "product_cat" && $taxonomy !== "product_shipping_class" && $taxonomy !== "product_type")
			{
				$hasit = false;
				if(is_array($attributes) && !empty($attributes))
				{
					foreach($attributes as $attr)
					{
						if($taxonomy === 'pa_'.$attr->name)
						{
							$hasit = true;
							break;
						}
				    }
				}
				if(!$hasit)
				{
					$taxobj = new stdClass();
					$taxobj->tax = $taxonomy;
					$taxobj->terms = "";
					$args = array(
					    'number'     => 99999,
					    'orderby'    => 'slug',
					    'order'      => 'ASC',
					    'hide_empty' => false,
					    'include'    => '',
						'fields'     => 'all'
					);

					$woo_categories = get_terms($taxonomy, $args );
					$termname = "";
					$counter  = 0;
					foreach($woo_categories as $category)
					{
					    if(!is_object($category)) continue;
					    if(!property_exists($category,'name')) continue;
					    if(!property_exists($category,'term_id')) continue;
						$catname = str_replace('"','\"',$category->name);
						$catname = trim(preg_replace('/\s+/', ' ', $catname));
					   	if($termname === "")
						{
							$termname = $catname;
						}else
						{
							$termname.= ', '. $catname;
						}
						
						if($counter >= 2) break;
						
						$counter++;
					}
					$taxobj->terms = $termname;
					$metas[] = $taxobj;
				}
			}
		}
		return $metas;
	}
	
    public static function ajax()
    {
		$nonce = $_POST['nonce'];
		if(!wp_verify_nonce( $nonce, 'w3ex-advbedit-nonce' ) )
		{
			$arr = array(
			  'success'=>'no-nonce',
			  'products' => array()
			);
			echo json_encode($arr);
			die();
		}
//			die ();
		// get the submitted parameters
		$type = $_POST['type'];
		$titleparam = NULL;
		if(isset($_POST['titleparam']))
		   $titleparam = $_POST['titleparam'];
		$catparams = NULL;
		if(isset($_POST['catparams']))
			$catparams = $_POST['catparams'];
		$categoryor = false;
		if(isset($_POST['categoryor']))
			$categoryor = true;	
		$attrparams = NULL;
		if(isset($_POST['attrparams']))
			$attrparams = $_POST['attrparams'];
		$priceparam = NULL;
		if(isset($_POST['priceparam']))
			$priceparam = $_POST['priceparam'];
		$saleparam = NULL;
		if(isset($_POST['saleparam']))
			$saleparam = $_POST['saleparam'];
		$customparam = NULL;
		if(isset($_POST['customparam']))
			$customparam = $_POST['customparam'];
		$skuparam = NULL;
		if(isset($_POST['skuparam']))
		   $skuparam = $_POST['skuparam'];
		$tagsparams = NULL;
		if(isset($_POST['tagsparams']))
			$tagsparams = $_POST['tagsparams'];
		$descparam = NULL;
		if(isset($_POST['descparam']))
			$descparam = $_POST['descparam'];
		$shortdescparam = NULL;
		if(isset($_POST['shortdescparam']))
			$shortdescparam = $_POST['shortdescparam'];
		$data = array();
		if(isset($_POST['data']))
			$data = $_POST['data'];
		$children = array();
		if(isset($_POST['children']))
			$children = $_POST['children'];
		$columns = array();
		if(isset($_POST['columns']))
			$columns = $_POST['columns'];
		$extrafield = '';
		if(isset($_POST['extrafield']))
			$extrafield = $_POST['extrafield'];
		$response = '';
		$arr = array(
		  'success'=>'yes',
		  'products' => array()
		);
		$total = 0;
		$ispagination = false;
		$isnext = true;
		if(isset($_POST['ispagination']))
		{
			if($_POST['ispagination'] == "true")
				$ispagination = true;
		}
		if(isset($_POST['isnext']))
		{
			if($_POST['isnext'] == "false")
				$isnext = false;
		}
			
		switch($type){
			case 'loadproducts':
			{
				$hasnext = false;
				$isbegin = false;
				if(isset($_POST['isvariations']))
				{
					$curr_settings = get_option('w3exabe_settings');
					if(!is_array($curr_settings))
						$curr_settings = array();
					if($_POST['isvariations'] === "true")
						$curr_settings['isvariations'] = 1;
					else
						$curr_settings['isvariations'] = 0;
					update_option('w3exabe_settings',$curr_settings);
				}
				$custsearchparam = '';
				if(isset($_POST['custsearchparam']))
					$custsearchparam = $_POST['custsearchparam'];
				$ret = self::loadProducts($titleparam,$catparams,$attrparams,$priceparam,$saleparam,$customparam,$total,$ispagination,$isnext,$hasnext,$isbegin,$categoryor,$skuparam,$tagsparams,$descparam,$shortdescparam,$custsearchparam);
				if(is_wp_error($ret) || -1 === $ret)
				{
					$arr['success'] = 'no';
					if(is_wp_error($ret))
					{
						$arr['error'] = $ret;
						echo json_encode($arr);
						return;
					}
				}
				$arr['products'] = $ret;
				$arr['total'] = $total;
				$arr['hasnext'] = $hasnext;
				$arr['isbegin'] = $isbegin;
			}break;
			case 'saveproducts':
			{
				$newarr = array();
				$newcarr = array();
				self::convertSaveArrays($data,$newarr,$children,$newcarr);
				$ret = self::saveProducts($newarr,$newcarr);
				if(!is_wp_error($ret) && is_array($ret))
					$arr['products'] = $ret;
				update_option('w3exabe_columns',$columns);
			}break;
			case 'createvariations':
			{
				$newarr = array();
				$newcarr = array();
				self::convertSaveArrays($data,$newarr,$children,$newcarr,true);
				$ret = self::addVariations($newarr,$newcarr);
				if(is_wp_error($ret) || -1 === $ret)
				{
					$arr['success'] = 'no';
					if(is_wp_error($ret))
					{
						$arr['error'] = $ret;
						echo json_encode($arr);
						return;
					}
				}
				$arr['products'] = $ret;
			}break;
			case 'createproducts':
			{
				$prodcount = 1;
				if(isset($_POST['prodcount']))
				{
					$prodcount = (int)$_POST['prodcount'];
					if($prodcount < 1)
						$prodcount = 1;
					if($prodcount > 100)
						$prodcount = 100;	
				}
				$ret = self::addProducts($prodcount);
				if(is_wp_error($ret) || -1 === $ret)
				{
					$arr['success'] = 'no';
					if(is_wp_error($ret))
					{
						$arr['error'] = $ret;
						echo json_encode($arr);
						return;
					}
				}
				$arr['products'] = $ret;
			}break;
			case 'duplicateproducts':
			{
				$newarr = array();
				$newcarr = array();
				$count = 1;
				if(isset($_POST['dupcount']))
				{
					$count = $_POST['dupcount'];
					$count = (int)$count;
					if($count <= 0) $count = 1;
					if($count > 100) $count = 100;
				}
				self::convertSaveArrays($data,$newarr,$children,$newcarr);
				$ret = self::duplicateProducts($newarr,$count);
				if(is_wp_error($ret) || -1 === $ret)
				{
					$arr['success'] = 'no';
					if(is_wp_error($ret))
					{
						$arr['error'] = $ret;
						echo json_encode($arr);
						return;
					}
				}
				$arr['products'] = $ret;
			}break;
			case 'deleteproducts':
			{
				$newarr = array();
				$newcarr = array();
				self::convertSaveArrays($data,$newarr,$children,$newcarr);
				$deltype = "0";
				if(isset($_POST['deletetype']))
				{
					$deltype = $_POST['deletetype'];
				}
				self::deleteProducts($newarr,$deltype);
			}break;
			case 'savecolumns':
			{
				update_option('w3exabe_columns',$data);
			}break;
			case 'savecustom':
			{
				if(is_array($data) && !empty($data))
				{
					foreach($data as $key => $innerarray)
					{
						if(isset($innerarray['type']))
						{
							if($innerarray['type'] === 'customh' || $innerarray['type'] === 'custom')
							{
								if(taxonomy_exists($key))
								{
//									'<td>'
//										.'<input id="set'.$key.'" type="checkbox" class="bulkset" data-id="'.$key.'" data-type="customtaxh"><label for="set'.$key.'">Set '.$key.'</label></td><td></td><td>'
									$bulktext = ' class="makechosen catselset" style="width:250px;" data-placeholder="select" multiple ><option value=""></option>';
										   $args = array(
										    'number'     => 99999,
										    'orderby'    => 'slug',
										    'order'      => 'ASC',
										    'hide_empty' => false,
										    'include'    => '',
											'fields'     => 'all'
										);

										$woo_categories = get_terms($key, $args );

										foreach($woo_categories as $category)
										{
										    if(!is_object($category)) continue;
										    if(!property_exists($category,'name')) continue;
										    if(!property_exists($category,'term_id')) continue;
											$catname = str_replace('"','\"',$category->name);
											$catname = trim(preg_replace('/\s+/', ' ', $catname));
										   	$bulktext.= '<option value="'.$category->term_id.'" >'.$catname.'</option>';
										}
										$bulktext.= '</select>';
										//</td><td></td>
										$arr[$key] = $bulktext;
										if($innerarray['type'] === 'customh')
										{
											$bulktext =  '<div class="'.$key.'">';
											$bulktext.= '<ul class="categorychecklist form-no-clear">';
											$args = array(
												'descendants_and_self'  => 0,
												'selected_cats'         => false,
												'popular_cats'          => false,
												'walker'                => null,
												'taxonomy'              => $key,
												'checked_ontop'         => true
											);
											ob_start();
											wp_terms_checklist( 0, $args );
											$bulktext.= ob_get_clean();
											$bulktext.= '</ul></div>';
											$arr[$key.'edit'] = $bulktext;
										}
								}
								continue;
							}
						}
					}
				}
				update_option('w3exabe_custom',$data);
				$arr['customfieldsdata'] = $data;
				update_option('w3exabe_columns',$columns);
			}break;
			case 'exportproducts':
			{
				$filename = self::exportProducts($data,$children);
				$arr['products'] = plugin_dir_url(__FILE__).$filename;
			}break;
			case 'setthumb':
			{
				$itemids = explode(',',$data[0]);
				foreach($itemids as $id)
				{
					update_post_meta( $id , '_thumbnail_id', $data[1]);
				}
			}break;
			case 'setgallery':
			{
				$itemids = explode(',',$data[0]);
				foreach($itemids as $id)
				{
					update_post_meta( $id , '_product_image_gallery', $data[1]);
				}
			}break;
			case 'removethumb':
			{
				$itemids = explode(',',$data[0]);
				foreach($itemids as $id)
				{
					delete_post_meta( $id , '_thumbnail_id');
				}
				
			}break;
			case 'checkcustom':
			{
				if(!taxonomy_exists($extrafield))
				{
					$arr['error'] = 'does not exist';
				}
				
			}break;
			case 'findcustomfields':
			{
				$arr['customfields'] = self::FindCustomFields($data);
				
			}break;
			case 'findcustomtaxonomies':
			{
				$arr['customfields'] = self::FindCustomTaxonomies();
				
			}break;
			case 'savesettings':
			{
				$curr_settings = get_option('w3exabe_settings');
				if(is_array($curr_settings))
				{
					$curr_settings['settgetall'] = $data['settgetall'];
					$curr_settings['settgetvars'] = $data['settgetvars'];
					if(isset($data['settlimit']))
						$curr_settings['settlimit'] = $data['settlimit'];
					if(isset($data['incchildren']))
						$curr_settings['incchildren'] = $data['incchildren'];
					if(isset($data['disattributes']))
						$curr_settings['disattributes'] = $data['disattributes'];
					if(isset($data['converttoutf8']))
						$curr_settings['converttoutf8'] = $data['converttoutf8'];
					if(isset($data['dontcheckusedfor']))
						$curr_settings['dontcheckusedfor'] = $data['dontcheckusedfor'];
					if(isset($data['showattributes']))
						$curr_settings['showattributes'] = $data['showattributes'];
					if(isset($data['showprices']))
						$curr_settings['showprices'] = $data['showprices'];
					if(isset($data['showskutags']))
						$curr_settings['showskutags'] = $data['showskutags'];
					if(isset($data['showdescriptions']))
						$curr_settings['showdescriptions'] = $data['showdescriptions'];
					if(isset($data['calldoaction']))
						$curr_settings['calldoaction'] = $data['calldoaction'];
					if(isset($data['confirmsave']))
						$curr_settings['confirmsave'] = $data['confirmsave'];
					update_option('w3exabe_settings',$curr_settings);
				}else
				{
					update_option('w3exabe_settings',$data);
				}
				if(isset($data['selcustomfields']))
					update_option('w3exabe_customsel',$data['selcustomfields']);
				else
					update_option('w3exabe_customsel',array());
			}break;
			default:
				break;
		}
		echo json_encode($arr);
    }
}

W3ExABulkEditAjaxHandler::ajax();
