<?php
class Mage_Shipping_Block_shippinginfopopup extends Mage_Core_Block_Template
{
	public function getCountryParams()
	{
		$countryParams= Mage::getModel('countrymapping/country')->getCountryParameters();
		return $countryParams;
	}
	


}