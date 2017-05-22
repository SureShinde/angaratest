<?php

class Mageix_ExtensionManagerLicense_Model_Mysql4_License_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('extensionmanagerlicense/license');
    }
}