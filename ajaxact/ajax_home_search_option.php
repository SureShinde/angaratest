<?php
class SearchHomeClass 
{	
	function __construct() {
	   	include_once('config.php');		  
	}
	
   	function getStoneOptions($cat_id=NULL) 
	{
		// start stone options
		$row_stone_arr = array();
		$sql_stone = "SELECT `main_table`.option_id, IF(store_value.value_id>0, store_value.value,store_default_value.value) AS `value` 
				FROM `eav_attribute_option` AS `main_table`
				INNER JOIN `eav_attribute_option_value` AS `store_default_value` ON store_default_value.option_id=main_table.option_id
				LEFT JOIN `eav_attribute_option_value` AS `store_value` ON store_value.option_id=main_table.option_id AND store_value.store_id='1' 
				WHERE (main_table.attribute_id='128') AND (store_default_value.store_id=0) 
				AND main_table.option_id IN 
				(
					SELECT `index`.`value` FROM `catalog_product_entity` AS `e`
					INNER JOIN `catalog_category_product_index` AS `cat_index` ON cat_index.product_id=e.entity_id AND cat_index.store_id='1' 
					AND cat_index.visibility IN(2, 4) AND cat_index.category_id='".$cat_id."'
					INNER JOIN `catalog_product_index_price` AS `price_index` ON price_index.entity_id = e.entity_id AND price_index.website_id = '1' 
					AND price_index.customer_group_id = 0
					INNER JOIN `catalog_product_index_eav` AS `index` ON index.entity_id=e.entity_id 
					WHERE (index.store_id = '1') AND (index.attribute_id = '128') GROUP BY `index`.`value`
				)
				ORDER BY main_table.sort_order ASC, store_default_value.value ASC";
		$result_stone = mysql_query($sql_stone);
		$i=0;
		$stone_select_html = '<select name="de_stone_type" id="de_stone_type" onchange="javascript:getHomeSearchOption(1);" class="searchlist">';
		$stone_select_html.='<option value="">Select Gemstone</option>';
		while ($row = mysql_fetch_array($result_stone, MYSQL_ASSOC)) {
			$row_stone_arr[$i]['option_id'] = $row["option_id"];
			$row_stone_arr[$i]['value'] = $row["value"];	
			$stone_select_html.='<option value="'.$row["option_id"].'">'.trim($row["value"]).'</option>';
			$i++;
		}
		$stone_select_html.='</select>';
		return $stone_select_html;
	}
	
	function getStonePriceOptions($cat_id=NULL,$option_id=NULL) 
	{
		// start stone price calculation
		$row_stone_price_arr = array();
		if($option_id > 0){		
			$sql_stone_price = "SELECT 
			MAX(IF(price_idx.min_price between 1 and 251,1,0)) as p0,
			MAX(IF(price_idx.min_price between 251 and 500,1,0)) as p1,
			MAX(IF(price_idx.min_price between 501 and 750,1,0)) as p2,
			MAX(IF(price_idx.min_price between 751 and 1000,1,0)) as p3,
			MAX(IF(price_idx.min_price between 1001 and 2000,1,0)) as p4,
			MAX(IF(price_idx.min_price between 2001 and 1000000,1,0)) as p5
			FROM `catalog_product_entity` AS `e`
			INNER JOIN `catalog_category_product_index` AS `cat_index` ON cat_index.product_id=e.entity_id AND cat_index.store_id='1' 
			AND cat_index.visibility IN(2, 4) AND cat_index.category_id='".$cat_id."'
			INNER JOIN `catalog_product_index_price` AS `price_index` ON price_index.entity_id = e.entity_id AND price_index.website_id = '1' 
			AND price_index.customer_group_id = 0
			INNER JOIN `catalog_product_index_eav` AS `attr_index_128` ON attr_index_128.entity_id=e.entity_id
			LEFT JOIN `catalog_product_index_price` AS `price_idx` ON price_idx.entity_id=e.entity_id 
			WHERE (attr_index_128.store_id = '1') AND (attr_index_128.attribute_id = '128') AND (attr_index_128.value IN ('".$option_id."')) 
			AND (price_idx.website_id = '1') AND (price_idx.customer_group_id = 0)";
		}else{
			$sql_stone_price = "SELECT 
			MAX(IF(price_idx.min_price between 1 and 251,1,0)) as p0,
			MAX(IF(price_idx.min_price between 251 and 500,1,0)) as p1,
			MAX(IF(price_idx.min_price between 501 and 750,1,0)) as p2,
			MAX(IF(price_idx.min_price between 751 and 1000,1,0)) as p3,
			MAX(IF(price_idx.min_price between 1001 and 2000,1,0)) as p4,
			MAX(IF(price_idx.min_price between 2001 and 1000000,1,0)) as p5
			FROM `catalog_product_entity` AS `e`
			INNER JOIN `catalog_category_product_index` AS `cat_index` ON cat_index.product_id=e.entity_id AND cat_index.store_id='1' 
			AND cat_index.visibility IN(2, 4) AND cat_index.category_id='".$cat_id."'
			INNER JOIN `catalog_product_index_price` AS `price_index` ON price_index.entity_id = e.entity_id AND price_index.website_id = '1' AND price_index.customer_group_id = 0
			LEFT JOIN `catalog_product_index_price` AS `price_idx` ON price_idx.entity_id=e.entity_id WHERE (price_idx.website_id = '1') AND (price_idx.customer_group_id = 0)";
		}
		//echo $sql_stone_price;
		$result_stone_price = mysql_query($sql_stone_price);
		
		$row_stone_price = mysql_fetch_row($result_stone_price);					
		if(count($row_stone_price)>0){	
			$p0 = $row_stone_price[0];
			$p1 = $row_stone_price[1];
			$p2 = $row_stone_price[2];
			$p3 = $row_stone_price[3];
			$p4 = $row_stone_price[4];
			$p5 = $row_stone_price[5];	
		}else{
			$p0 = 0;
			$p1 = 0;
			$p2 = 0;
			$p3 = 0;
			$p4 = 0;
			$p5 = 0;
		}
		$stone_price_select_html = '<select name="price_range_opt" id="price_range_opt" class="searchlist">';
		$stone_price_select_html.='<option value="">Select Price</option>';
		
		for($i=0;$i<=5;$i++){
			$var_name = 'p'.$i;
			switch ($i) {
				case 0:
					$start_limit = 1;
					$end_limit = 250;
					$label = $start_limit.'-'.$end_limit;					
					break;
				case 1:
					$start_limit = 251;
					$end_limit = 500;			
					$label = $start_limit.'-'.$end_limit;
					break;
				case 2:
					$start_limit = 501;
					$end_limit = 750;
					$label = $start_limit.'-'.$end_limit;
					break;
				case 3:
					$start_limit = 751;
					$end_limit = 1000;			
					$label = $start_limit.'-'.$end_limit;
					break;
				case 4:
					$start_limit = 1001;
					$end_limit = 2000;			
					$label = $start_limit.'-'.$end_limit;
					break;		
				case 5:
					$start_limit = 2001;
					$end_limit = 10000000;
					$label = 'Over 2000';		
					break;
			}
			if($$var_name > 0){
				$stone_price_select_html.='<option value="'.$start_limit.'%2C'.$end_limit.'">'.$label.'</option>';
			}			
		}				
		$stone_price_select_html.='</select>';
		return $stone_price_select_html;
	}
}

$objSearch = new SearchHomeClass();
if(isset($_REQUEST['section'])){
	if($_REQUEST['section']==0){
		echo $objSearch->getStoneOptions($_REQUEST['cat_id']);		
	}else{
		echo $objSearch->getStonePriceOptions($_REQUEST['cat_id'],$_REQUEST['option_id']);		
	}
}
?>