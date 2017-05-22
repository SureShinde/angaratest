<?php

class Angara_Feeds_Model_Mysql4_Feeds extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the bannerslider_id refers to the key field in your database table.
        $this->_init('feeds/feeds', 'feeds_id');
    }
}