<?php
 
class Angara_Customerreview_Model_Mysql4_Customerreviewlog extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{   
		$this->_init('customerreview/customerreviewlog', 'id');
	}
}
