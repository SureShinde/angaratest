<?php

class Mageix_ExtensionManager_Model_Observer
{
    public function controller_action_predispatch(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            Mage::helper('mextensionmanager')->checkUpdatesScheduled();
        }
    }
}