<?php
include_once('config.php');
function getFinalProductAmount($shape=NULL,$stone=NULL,$size=NULL,$metal=NULL,$grade=NULL,$side_stone=NULL,$yg_weight=NULL,$wg_weight=NULL,$pl_weight=NULL,$sl_weight=NULL,$side_stone_cost=NULL,$extra_labor_cost=NULL,$number_of_stone=NULL)
{
	/*echo $shape.', '.$stone.', '.$size.', '.$metal.', '.$grade.', '.$side_stone.', '.$yg_weight.', '.$wg_weight.', '.$pl_weight.', '.$sl_weight.', '.$side_stone_cost.', '.$extra_labor_cost.', '.$number_of_stone;*/	
	$shape = trim($shape);
	$stone = trim($stone);
	$size = trim(str_replace('mm','',$size));
	$metal = trim($metal);	
	if(trim($metal)!=''){		
		if(stripos($metal, 'Yellow Gold') !== false) {
			$metal = 'YG';
			$weight = $yg_weight;
		}elseif(stripos($metal, 'White Gold') !== false) {
			$metal = 'WG';
			$weight = $wg_weight;
		}elseif(stripos($metal, 'Platinum') !== false) {
			$metal = 'PL';
			$weight = $pl_weight;
		}elseif(stripos($metal, 'Silver') !== false) {
			$metal = 'SL';
			$weight = $sl_weight;	
		}
	}else{
		$metal = 'YG';
		$weight = $yg_weight;
	}
	if(trim($grade)!=''){		
		switch (trim($grade)) {
			case 'A':
				$grade = 'GOOD';
				break;
			case 'AA':
				$grade = 'BETTER';
				break;
			case 'AAA':
				$grade = 'BEST';
				break;
			case 'AAAA':
				$grade = 'HEIRLOOM';
				break;	
			default:
			   $grade = 'BETTER';
		}			
	}else{
		$grade = 'BETTER';
	}
	if(trim($side_stone)!=''){
		$side_stone = trim($side_stone);			
	}else{
		$side_stone = 'no';	
	}	
	
	// start stone price calculation
	$stone_cost = 0;	
	$sql_stone = "SELECT price FROM build_your_jewelry_stone_prices WHERE stone='".$stone."' AND grade='".$grade."' AND shape='".$shape."' AND size='".$size."'
	order by price desc LIMIT 0,1";
	$result_stone = mysql_query($sql_stone);
	$row_stone = mysql_fetch_row($result_stone);					
	if(count($row_stone)>0){	
		$stone_cost = $row_stone[0] * $number_of_stone; // per stone cost * number of stone
	}else{
		$stone_cost = 0;	
	}
	// end stone price calculation
	
	// start matel price calculation
	$matel_cost = 0;	
	$sql_matel = "SELECT price FROM build_your_jewelry_matel_prices WHERE type='".$metal."' order by price desc LIMIT 0,1";
	$result_matel = mysql_query($sql_matel);
	$row_matel = mysql_fetch_row($result_matel);					
	if(count($row_matel)>0){		
		$matel_cost = $row_matel[0] * $weight;	// per gram cost * matel weight
	}else{
		$matel_cost = 0;	
	}
	// end matel price calculation
	
	// start side stone cost price calculation		
	if($side_stone=='yes'){	
		$side_stone_cost = $side_stone_cost;
	}else{
		$side_stone_cost = 0;	
	}
	//echo '<br>'.$side_stone_cost.'<br>';
	// end side stone cost price calculation
	
	// start extra price calculation
	$extra_cost = 0;	
	if(isset($extra_labor_cost)){	
		$extra_cost = $extra_labor_cost;
	}else{
		$extra_cost = 0;	
	}
	//echo '<br>'.$extra_cost.'<br>';
	// end side stone cose less price calculation
	$final_total_cost = 0;
	$final_total_cost = round(($stone_cost + $matel_cost + $extra_cost + $side_stone_cost), 2);
	//echo '<br>'.$stone_cost.' + '.$matel_cost.' + '.$extra_cost.' + '.$side_stone_cost.'<br>';	
	//$final_total_cost = round(($stone_cost + $matel_cost + $extra_cost + $side_stone_cost),2);	
	//echo '<br>'.$final_total_cost.'<br>';
	mysql_free_result($result_matel);
	if(isset($temp_con)){
		mysql_close($temp_con);
	}
	return $final_total_cost;	
}
?>