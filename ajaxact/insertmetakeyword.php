<?php
//attribute_id will remain 72 always
//category_id = 70 -- Diamond rings => cat-diamond-ring || category_id = 72 -- Diamond pendant necklace => cat-diamond-pendant || category_id = 71 -- Diamond Earrings => cat-diamond-earrings
include('config.php');
$sql = "delete FROM catalog_product_entity_text where entity_type_id='4' and attribute_id= '72' and entity_id in (select product_id FROM catalog_category_product where category_id=72)";
$result = mysql_query($sql);
if(!$result)
{
	exit;
}
$result = mysql_query('select product_id FROM catalog_category_product where category_id=72');
$i=0;
while ($row = mysql_fetch_assoc($result)) {
	$i++;
	//echo $i;
	
	$str = "insert into catalog_product_entity_text set entity_type_id='4', attribute_id= '72', store_id=0, entity_id='" . $row['product_id'] . "', value='cat-diamond-pendant'";
	echo $str . ";<br>";
	$res = mysql_query($str);
	if(!$res)
	{
		echo "failed<br>";
	}
}

?>