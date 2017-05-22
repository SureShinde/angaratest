<?php

class Mageix_ExtensionManager_Model_Mysql4_Module extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('mextensionmanager/module', 'module_id');
    }
}