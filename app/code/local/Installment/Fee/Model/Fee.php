<?php
class Installment_Fee_Model_Fee extends Varien_Object{
	const FEE_AMOUNT = 10;

	public static function getFee(){
		return self::FEE_AMOUNT;
	}
		
	public static function getEMIAmount($cartItems){
		$totEMIAmount = 0;
		foreach ($cartItems as $item) {
			//echo '<br><br>'.$item->getCalculationPrice().', '.$item->getNoOfInstallment().', '.$item->getQty().', '.$item->getDiscountAmount();
			if($item->getCalculationPrice()<=0){
				continue;
			}				
			if($item->getNoOfInstallment() > 1){
			   $ItemInstallmentAmt=self::getCartItemInstallmentWithDiscAmount($item->getCalculationPrice(),$item->getNoOfInstallment(),$item->getQty(),$item->getDiscountAmount());
			   $ItemInstallmentAmt = round($ItemInstallmentAmt,2);
			   $totEMIAmount = $totEMIAmount + ($ItemInstallmentAmt * ($item->getNoOfInstallment() - 1));
			}
		}
		$totEMIAmount = -$totEMIAmount;
		return $totEMIAmount;
	}
	
	public static function getBaseEMIAmount($cartItems){
		$totBaseEMIAmount = 0;		
		foreach ($cartItems as $item) {
			//echo $item->getCalculationPrice().', '.$item->getNoOfInstallment().', '.$item->getQty().', '.$item->getDiscountAmount();
			if($item->getCalculationPrice()<=0){
				continue;
			}				
			if($item->getNoOfInstallment() > 1){				
				$ItemBaseInstallmentAmt = self::getCartItemInstallmentWithDiscAmount($item->getBaseCalculationPrice(),$item->getNoOfInstallment(),$item->getQty(),$item->getBaseDiscountAmount());
				$ItemBaseInstallmentAmt = round($ItemBaseInstallmentAmt,2);
				$totBaseEMIAmount+=$ItemBaseInstallmentAmt * ($item->getNoOfInstallment() - 1);
			}
		}
		$totBaseEMIAmount = -$totBaseEMIAmount;
		//echo '--'.$totBaseEMIAmount;
		return $totBaseEMIAmount;
	}
	
	public static function getCartItemInstallmentWithDiscAmount($ItemTotAmt=NULL, $ItemTotEMI=NULL, $ItemQty=NULL, $ItemDiscountAmt=NULL)
	{	
		//echo $ItemTotAmt.'---'.$ItemTotEMI.'---'.$ItemQty.'---'.$ItemDiscountAmt;
		//$EMIAmount = ($ItemTotAmt - $ItemDiscountAmt) * $ItemQty / $ItemTotEMI;		
		$perItemDiscountAmt = ($ItemDiscountAmt / $ItemQty);		
		$EMIAmount = (($ItemTotAmt - $perItemDiscountAmt) * $ItemQty ) / $ItemTotEMI;
		return $EMIAmount;
	}
	
	public static function canApply($address){
		//put here your business logic to check if fee should be applied or not
		//if($address->getAddressType() == 'billing'){
		return true;
		//}
	}
}