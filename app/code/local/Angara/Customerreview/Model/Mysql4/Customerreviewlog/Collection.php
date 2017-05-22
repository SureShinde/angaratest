<?php
 
class Angara_Customerreview_Model_Mysql4_Customerreviewlog_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		//parent::__construct();
		$this->_init('customerreview/customerreviewlog');
	}
}