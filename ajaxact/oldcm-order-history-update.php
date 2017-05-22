<?php
include_once('config.php');
$new_cm_orders_sql = "SELECT entity_id, status, updated_at, store_id, increment_id, oldcm_order_num FROM sales_flat_order where store_id=3";
//$new_cm_orders_sql.= " and oldcm_order_num='28189'";
$new_cm_orders_sql.= " and oldcm_order_num in ('0220','24020','26195','27350','28190','28210','28225','28245','28305','29470')";
echo $new_cm_orders_sql.'<br>'; //die;
$rs_new_cm_orders = mysql_query($new_cm_orders_sql);
while ($row = mysql_fetch_array($rs_new_cm_orders, MYSQL_ASSOC)) {
	//echo '<pre>'; print_r($row); die;
	$entity_id = $row['entity_id'];
	$status = $row['status'];
	$increment_id = $row['increment_id'];
	$oldcm_order_num = $row['oldcm_order_num'];
	$updated_at = $row['updated_at'];

	$subsql = "select t2.* from oldcm_orders_comments as t2
				left join sales_flat_order as sfo on t2.orderUID=sfo.oldcm_order_num
				where sfo.oldcm_order_num='".$oldcm_order_num."' and t2.value!='' and t2.attributeUID!='385'
				group by t2.orderUID, t2.attributeUID order by t2.attributeUID asc";
	echo $subsql.'<br>';	//die;
	$data = mysql_query($subsql);
	while ($rowData = mysql_fetch_array($data, MYSQL_ASSOC)) {
	//echo '<pre>'; print_r($rowData); die;
		$name	=	'<b>'.$rowData['name'].'</b>';
		$value	=	$rowData['value'];
		$comment = 	$name.': '.$value;
		echo 'comment->'.$comment.'<br>';	//die;
		if($comment!='')
		{
			// Check if record is already there
			$checkSql =	"select * from sales_flat_order_status_history where parent_id='".$entity_id."' and comment ='".addslashes(trim($comment))."'";
			echo $checkSql.'<br>'; //die;
			$resultCheck = mysql_query($checkSql);
			$count	=	mysql_num_rows($resultCheck);
			//echo $count; die;
			if($count==0){
				$sql = "INSERT INTO sales_flat_order_status_history 
						(entity_id, parent_id, is_customer_notified, is_visible_on_front, comment, status, created_at, entity_name) 
				VALUES (NULL, '".$entity_id."', '0', '0', '".addslashes(trim($comment))."', '".$status."','".$updated_at."','order')";	
				echo $sql.'<br>';//die;	
				echo 'Record inserted for '.$entity_id.'<br><br>';	//die;
				$result = mysql_query($sql);
			}else{
				echo 'Comment is Already inserted for '.$entity_id.'<br><br>';	//die;
			}
		}
	}
	// end loop	
}
?>