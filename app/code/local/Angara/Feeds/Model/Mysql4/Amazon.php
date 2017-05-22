<?php

class Angara_Feeds_Model_Mysql4_Amazon extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the feeds_amazon_id refers to the key field in your database table.
		$this->_init('feeds/amazon', 'amazon_id');
    }
}