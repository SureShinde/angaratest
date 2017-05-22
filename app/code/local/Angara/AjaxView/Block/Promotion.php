<?php
class Angara_AjaxView_Block_Promotion extends Mage_Core_Block_Template
{
    public function showTimer()
    {
		//	Get time when user first open the page
		$currentTimestamp 	= 	Mage::getModel('core/date')->timestamp(time()); //Magento's timestamp function makes a usage of timezone and converts it to timestamp
		$currentTime 		= 	date('Y-m-d h:i:s', $currentTimestamp); //The value may differ than above because of the timezone settings.
		if(!isset($_SESSION['serverTime'])){	
			$_SESSION['serverTime']	=	$currentTime;
			$startTime = $currentTime;
		}else{
			$startTime = $_SESSION['serverTime'];	
		}
		//echo '<br>startTime->'.$startTime;
		//echo '<br>currentTime->'.$currentTime;
		$differenceInSeconds = abs(strtotime($currentTime) - strtotime($startTime));
		//echo '<br>diff in seconds->'.$differenceInSeconds;
		$millisecondsToShowTimer	=	20*60;		//	20 mins * 60 seconds
		$totalRemainingMiliSeconds	=	($millisecondsToShowTimer - $differenceInSeconds)*1000;
		return $totalRemainingMiliSeconds;
    }
	
	public function couponCodes(){
		$couponCodeArray	=	array('ANCLFXL','ANCLFFS','ANCLFNA','ANCLFZQ','ANCLFPO','ANCLFUK','ANCLFEJ','ANCLFTD','ANCLFSY','ANCLFOI');
		$cartCoupon			=	$couponCodeArray[array_rand($couponCodeArray)];
		
		if(!isset($_SESSION['cartCoupon'])){	
			$_SESSION['cartCoupon']	=	$cartCoupon;
			$singleCoupon = $cartCoupon;
		}else{
			$singleCoupon = $_SESSION['cartCoupon'];	
		}
		return $singleCoupon;
		
	}
	
	public function getCartTotal(){
		return $grandtotalwithout_easy 	= 	Mage::getBlockSingleton('checkout/cart')->getTotalWithoutInstallments();
		//return $totals 			= 	Mage::getSingleton('checkout/session')->getQuote()->getTotals(); 
	}
}
?>