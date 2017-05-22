<?php

class Mageix_ExtensionManagerLicense_Model_Observer
{
    public function mextensionmanager_license_tabs($observer)
    {

        
        $container = $observer->getEvent()->getContainer();

        $container->addTab('manage_licenses_section', array(
            'label'     => Mage::helper('extensionmanagerlicense')->__('Manage Licenses'),
            'title'     => Mage::helper('extensionmanagerlicense')->__('Manage Licenses'),
            'content'   => $container->getLayout()->createBlock('extensionmanagerlicense/adminhtml_license_grid')->toHtml(),
        ));

        $container->addTab('add_licenses_section', array(
            'label'     => Mage::helper('extensionmanagerlicense')->__('Add Licenses'),
            'title'     => Mage::helper('extensionmanagerlicense')->__('Add Licenses'),
            'content'   => $container->getLayout()->createBlock('adminhtml/template')->setTemplate('extensionmanagerlicense/add_licenses.phtml')->toHtml(),
        ));
    }
}