<?php
class Angara_CustomerStory_Model_Mysql4_Story_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		$this->_init("customerstory/story");
	}
}?>	 