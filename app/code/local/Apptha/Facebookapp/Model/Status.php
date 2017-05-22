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
class Apptha_Facebookapp_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('facebookapp')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('facebookapp')->__('Disabled')
        );
    }
}