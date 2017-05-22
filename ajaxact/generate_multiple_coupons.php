<?php
exit;
set_time_limit('10000000000000');
class GenerateCouponClass 
{	
	function __construct() {
	   	error_reporting(1);
		include_once('config.php');		  
	}
	
	function generatePassword($length=8) 
	{
		$prefix = 'ASB';
		$chars = "123456789CDEFHIJKLMNPQRTUVWXYZ";	 
		$i = 3;
		$str = "";
		while ($i < $length) {
			$str.=$chars[mt_rand(0,strlen($chars))];
			$i++;
		}
		$final_code = $prefix.''.$str;
		return $final_code;
	}
	
   	function checkCouponCodeExist($coupon_code=NULL, $x=NULL) 
	{
		$row_arr = array();
		$sql_1 = "SELECT count(*) as count FROM salesrule_coupon WHERE (code='".$coupon_code."')";
		$result_1 = mysql_query($sql_1);
		$row_1 = mysql_fetch_row($result_1);					
		$coupon_count = $row_1[0];
		if($coupon_count > 0){	
			return 'yes';
		}else{
			//insertion start
				$name = 'Angara Sister Blogs - Discount: 12% - '.$coupon_code;
				$description = '<?xml version="1.0"?><promotions><prod id="FR0003AM"><orderamount>0</orderamount></prod><prod id="FR0005GC"><orderamount>500</orderamount></prod>
</promotions>';
				//$description = '';
				$base_date = '2012-09-17';
				
				//echo $tomorrow  = date('Y-m-d', mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));//exit;
								
				$from_date = date('Y-m-d', mktime(0, 0, 0, 9, 17 + $x, 2012)).' 00:00:00';
				//$from_date = date('2012-08-31 00:00:00');
				
				$to_date = date('Y-m-d', mktime(0, 0, 0, 9, 19 + $x, 2012)).' 00:00:00';				
				//$to_date = '2013-03-31 00:00:00';

				$uses_per_customer = '100'; 
				$customer_group_ids = '0,1,2,3'; 
				$is_active = '1';
				$category_id = 99;
				$conditions_serialized = 'a:6:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}'; 
				/*$actions_serialized = 'a:7:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:12:"category_ids";s:8:"operator";s:2:"()";s:5:"value";s:2:"'.$category_id.'";s:18:"is_value_processed";b:0;}}}'; */
				
				$actions_serialized ='a:6:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}';
				
				$stop_rules_processing = '0'; 
				$is_advanced = '1'; 
				$product_ids = ''; 
				$sort_order = '1'; 
				$simple_action = 'by_percent'; 
				$discount_amount = '12'; // need to change for other coupons
				$discount_qty = NULL; 
				$discount_step = '0'; 
				$simple_free_shipping = '0'; 
				$apply_to_shipping = '0';
				$times_used = '0'; 
				$is_rss = '0'; 
				$website_id = '1'; 
				$coupon_type = '2';	
				$use_auto_generation = '0';
				$uses_per_coupon = '1';			
				
				$usage_limit = '100'; 
				
				
				$sql_salesrule_insert = "INSERT INTO salesrule (name, description, from_date, to_date, uses_per_customer, 
				is_active, conditions_serialized, actions_serialized, stop_rules_processing, is_advanced, sort_order, simple_action, 
				discount_amount, discount_step, simple_free_shipping, apply_to_shipping, times_used, is_rss, coupon_type, use_auto_generation, uses_per_coupon)
				VALUES 
				('".$name."', '".$description."', '".$from_date."', '".$to_date."', 
				'".$uses_per_customer."',
				'".$is_active."', '".$conditions_serialized."', '".$actions_serialized."', 
				'".$stop_rules_processing."', '".$is_advanced."', '".$sort_order."', '".$simple_action."', 
				'".$discount_amount."', '".$discount_step."', '".$simple_free_shipping."', 
				'".$apply_to_shipping."', '".$times_used."','".$is_rss."', '".$coupon_type."','".$use_auto_generation."','".$uses_per_coupon."')";
				$result_salesrule_insert = mysql_query($sql_salesrule_insert);
				$rule_id = mysql_insert_id();
				
				$sql_salesrule_website_insert = "INSERT INTO salesrule_website (website_id,rule_id) VALUES ('".$website_id."', '".$rule_id."')";
				$result_salesrule_website_insert = mysql_query($sql_salesrule_website_insert);
								
				$sql_salesrule_customer_group_insert = "INSERT INTO salesrule_customer_group (customer_group_id,rule_id) 
				VALUES (0, '".$rule_id."'), (1, '".$rule_id."'), (2, '".$rule_id."'), (3, '".$rule_id."')";
				$result_salesrule_customer_group_insert = mysql_query($sql_salesrule_customer_group_insert);
								
				$sql_salesrule_coupon_insert = "INSERT INTO salesrule_coupon (rule_id, code, usage_limit, usage_per_customer, expiration_date, is_primary) 
				VALUES ('".$rule_id."', '".$coupon_code."', '".$usage_limit."', '".$uses_per_customer."', '".$to_date."', '1')";
				$result_salesrule_coupon_insert = mysql_query($sql_salesrule_coupon_insert);
				$coupon_id = mysql_insert_id();				
			return 'no';
		}		
	}	
}

$objCoupon = new GenerateCouponClass();
for($x=0;$x<=199;$x++)
{
	$coupon_code = '';
	$coupon_code = $objCoupon->generatePassword(8); // Displays a 6+2 character string
	$checkcoupon_exist = $objCoupon->checkCouponCodeExist($coupon_code, $x);
}
/*
SELECT salesrule.rule_id, salesrule.name, salesrule.description, salesrule_coupon.code AS coupon_code, salesrule.from_date, salesrule.to_date, salesrule.discount_amount
FROM salesrule
LEFT JOIN salesrule_coupon ON salesrule_coupon.rule_id = salesrule.rule_id
WHERE salesrule.name LIKE 'Social Annex%'
GROUP BY salesrule.rule_id
ORDER BY salesrule.rule_id ASC 
*/
?>