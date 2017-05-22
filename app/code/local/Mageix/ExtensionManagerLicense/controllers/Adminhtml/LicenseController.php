<?php

class Mageix_ExtensionManagerLicense_Adminhtml_LicenseController extends Mage_Adminhtml_Controller_Action
{
    public function addLicenseAction()
    {
        try {
            $key = $this->getRequest()->getPost('license_key');
            $install = !!$this->getRequest()->getPost('download_install');
            Mageix_ExtensionManagerLicense_Helper_Relay::retrieveLicense($key, $install);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('extensionmanagerlicense')->__('The license has been added: %s. Log out and log back in.', $key));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('mextensionmanageradmin/adminhtml_module/');
    }

    public function checkUpdatesAction()
    {
        try {
            $licenses = Mage::getModel('extensionmanagerlicense/license')->getCollection();
            foreach ($licenses as $license) {
                Mageix_ExtensionManagerLicense_Helper_Relay::retrieveLicense($license);
                try {
                    Mageix_ExtensionManagerLicense_Helper_Relay::validateLicense($license);
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('License updates have been fetched'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('mextensionmanageradmin/adminhtml_module/');
    }

    public function serverInfoAction()
    {
        try {
            Mageix_ExtensionManagerLicense_Helper_Relay::sendServerInfo();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Server Info has been sent'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('mextensionmanageradmin/adminhtml_module/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/tools/mextensionmanager');
    }

    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('extensionmanagerlicense/adminhtml_license_grid')->toHtml()
        );
    }

    public function massRemoveAction()
    {
        try {
            $ids = $this->getRequest()->getPost('licenses');
            if (!$ids) {
                Mage::throwException($this->__('No licenses to remove'));
            }
            $licenses = Mage::getModel('extensionmanagerlicense/license')->getCollection()->addFieldToFilter('license_id', $ids);
            foreach ($licenses as $l) {
                $l->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Licenses have been removed'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('mextensionmanageradmin/adminhtml_module/');
    }
}