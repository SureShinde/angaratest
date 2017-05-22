<?php
/**
 * @category   Angara
 * @package    Angara_Gifts
 * @copyright  Copyright (c) 2014 Angara eCommerce (http://www.angara.com)
 * @license    http://angara.com/LICENSE-COMMUNITY.txt
 */

class Angara_Gifts_Model_Mysql4_Product extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('gifts/product', 'id');
    }
}