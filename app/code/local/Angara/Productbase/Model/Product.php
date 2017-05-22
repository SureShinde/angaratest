<?php
class Angara_Productbase_Model_Product extends Mage_Core_Model_Abstract
{
    /*
     * Class constructor
     */
    public function _construct()
    {
        $this->_init('productbase/product');
    }
	
	public function getPrice(){
		//$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$this->getSku());
	}
}
