<?php
/**
 * @category   Angara
 * @package    Angara_Gifts
 * @copyright  Copyright (c) 2014 Angara eCommerce (http://www.angara.com)
 * @license    http://angara.com/LICENSE-COMMUNITY.txt
 */

class Angara_Gifts_Model_Mysql4_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('gifts/product');
    }
    
    public function getProductsPosition($id = 0)
    { 
        $collection = $this->addFieldToFilter('gift_id', $id);
        $result = array();
        foreach ($collection as $c) {
            $result[] = $c->getProductId();
        }
        return $result;
    }    
}