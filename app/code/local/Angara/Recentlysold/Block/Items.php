<?php

class Angara_Recentlysold_Block_Items extends Mage_Core_Block_Template
{
	
	protected $_itemsCollection;
	
	public function getRecentItems()
    {
		if (null === $this->_reviewsCollection) {
			$itemscount = Mage::getStoreConfig('recentlysold/general/itemscount');
			$itemscount = $itemscount?$itemscount:15;
			$this->_itemsCollection = Mage::getModel('recentlysold/item')->getCollection()->setPageSize($itemscount)->setCurPage(0)->setOrder('time','DESC');
			//var_dump(get_class_methods(get_class($this->_itemsCollection))); //exit;
		}
		return $this->_itemsCollection;
    }
}