<?php
/**
 * @name         :  Magento Facebook App
 * @version      :  1.1
 * @since        :  Magento 1.4
 * @author       :  Apptha - http://www.apptha.com
 * @copyright    :  Copyright (C) 2011 Powered by Apptha
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  6 October 2011
 * 
 * */
class Apptha_Facebookapp_Model_Mysql4_Facebookapp_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('facebookapp/facebookapp');
    }
}