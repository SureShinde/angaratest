<?php
include_once('ajax_byoj_price_cart.php');
/*echo $_REQUEST['shape'].', '.$_REQUEST['stone'].', '.$_REQUEST['size'].', '.$_REQUEST['metal'].', '.$_REQUEST['grade'].', '.$_REQUEST['side_stone'].', '.$_REQUEST['yg_weight'].', '.$_REQUEST['wg_weight'].', '.$_REQUEST['pl_weight'].', '.$_REQUEST['sl_weight'].', '.$_REQUEST['side_stone_cost'].', '.$_REQUEST['extra_labor_cost'].', '.$_REQUEST['number_of_stone'];*/

$final_cost = getFinalProductAmount($_REQUEST['shape'],$_REQUEST['stone'],$_REQUEST['size'],$_REQUEST['metal'],$_REQUEST['grade'],$_REQUEST['side_stone'],$_REQUEST['yg_weight'],$_REQUEST['wg_weight'],$_REQUEST['pl_weight'],$_REQUEST['sl_weight'],$_REQUEST['side_stone_cost'],$_REQUEST['extra_labor_cost'],$_REQUEST['number_of_stone']);

echo $final_cost;
?>