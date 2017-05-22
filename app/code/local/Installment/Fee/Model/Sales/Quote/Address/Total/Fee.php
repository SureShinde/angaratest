<?php
class Installment_Fee_Model_Sales_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Abstract{
	protected $_code = 'fee';

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);

		$this->_setAmount(0);
		$this->_setBaseAmount(0);

		$items = $this->_getAddressItems($address);
		if (!count($items)) {
			return $this; //this makes only address type shipping to come through
		}


		$quote = $address->getQuote();
		//var_dump($quote);
		
		$cartItems = $quote->getAllVisibleItems();
				
		if(Installment_Fee_Model_Fee::canApply($address)){
			/*
			$exist_amount = $quote->getFeeAmount();
			$fee = Installment_Fee_Model_Fee::getFee();
			$balance = $fee - $exist_amount;
			
			// 	$balance = $fee;
			//	$this->_setAmount($balance);
			//	$this->_setBaseAmount($balance);
			
			//echo '<br>'.$totEMIAmount;
			//echo '<br>'.$totBaseEMIAmount;	
			
			//$address->setFeeAmount($balance);
			//$address->setBaseFeeAmount($balance);
			//$quote->setFeeAmount($balance);
			*/
			$totEMIAmount = Installment_Fee_Model_Fee::getEMIAmount($cartItems);
			$totBaseEMIAmount = Installment_Fee_Model_Fee::getBaseEMIAmount($cartItems);
			
			$address->setFeeAmount($totEMIAmount);
			$address->setBaseFeeAmount($totBaseEMIAmount);
			$quote->setFeeAmount($totEMIAmount);

			$address->setGrandTotal($address->getGrandTotal() + $address->getFeeAmount());
			$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseFeeAmount());
		}
	}
	
	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$amt = $address->getFeeAmount();
		$address->addTotal(array(
				'code'=>$this->getCode(),
				'title'=>Mage::helper('fee')->__('Installment Pending Amount'),
				'value'=> $amt
		));
		return $this;
	}
}