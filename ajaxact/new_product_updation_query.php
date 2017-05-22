<?php
include_once('config.php');
$sql_1 = "INSERT INTO catalog_product_entity_media_gallery (attribute_id, entity_id, `value`)
		SELECT ga.attribute_id, v.entity_id, v.value
		FROM catalog_product_entity_varchar v
		INNER JOIN eav_entity_type et ON et.entity_type_code='catalog_product'
		INNER JOIN eav_attribute va ON va.entity_type_id=et.entity_type_id AND va.frontend_input='media_image' AND va.attribute_id=v.attribute_id
		INNER JOIN eav_attribute ga ON va.entity_type_id=et.entity_type_id AND ga.attribute_code='media_gallery'
		LEFT JOIN catalog_product_entity_media_gallery g ON g.entity_id=v.entity_id AND g.value=v.value
		WHERE v.value<>'no_selection' AND v.value<>'' AND g.value IS NULL";
$result_1 = mysql_query($sql_1);

$sql_2 = "UPDATE catalog_product_entity SET has_options = 1, required_options = 1 where has_options = 0 AND entity_id 
		IN (SELECT distinct `product_id` FROM `newangara`.`catalog_product_option` CPO)";
$result_2 = mysql_query($sql_2);

$sql_3 = "INSERT INTO catalog_product_option_type_price
		SELECT option_type_price_id, OTV.option_type_id, 0 as store_id, 0.0000 as price, fixed as price_type from catalog_product_option_type_value OTV
		LEFT JOIN catalog_product_option_type_price OTP 
		ON OTP.option_type_id = OTV.option_type_id 
		WHERE OTP.option_type_id IS NULL";
$result_3 = mysql_query($sql_3);

if(isset($temp_con)){
	mysql_close($temp_con);
	echo 'Successfully updated all queries after upload new products.';
}
?>