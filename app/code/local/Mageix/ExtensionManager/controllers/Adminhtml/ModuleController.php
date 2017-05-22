<?php

class Mageix_ExtensionManager_Adminhtml_ModuleController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        if (!extension_loaded('zip')) {
            Mage::getSingleton('adminhtml/session')->addError('Zip PHP extension is not installed, will not be able to unpack downloaded extensions');
        }
        if (Mage::getStoreConfig('mextensionmanager/ftp/active') && !extension_loaded('ftp')) {
            Mage::getSingleton('adminhtml/session')->addError('FTP PHP extension is not installed, will not be able to install extensions using FTP');
        }

        $this->_setActiveMenu('system/mextensionmanager');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Mageix Upgrades'), Mage::helper('adminhtml')->__('Mageix Upgrades'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('mextensionmanager/container.phtml'));

        $this->renderLayout();
    }

    public function checkUpdatesAction()
    {
        try {
            $modules = $this->getRequest()->getPost('modules');
            Mage::helper('mextensionmanager')->checkUpdates();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Version updates have been fetched'));
        } catch (Exception $e) {
echo "<pre>";
print_r($e);
exit;
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    public function postAction()
    {
        $action = $this->getRequest()->getPost('do');
        switch ($action) {
        case Mage::helper('mextensionmanager')->__('Download and Install'):
            $this->_forward('install');
            break;
        }

        if (($m = Mage::getConfig()->getNode('modules/Mageix_ExtensionManagerLicense')) && $m->is('active')) {
            switch ($action) {
            case Mage::helper('extensionmanagerlicense')->__('Add license key'):
                $this->_forward('addLicense', 'adminhtml_license', 'extensionmanagerlicenseadmin');
                break;
            }
        }
    }

    public function installAction()
    {
        try {
            $uris = $this->getRequest()->getPost('uri');
            foreach ($uris as $i=>$uri) if (!$uri) unset($uris[$i]);
            if (!$uris) {
                Mage::throwException($this->__('No modules to install'));
            }
            Mage::helper('mextensionmanager')->installModules($uris);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('New modules has been downoaded and installed'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    public function massUpgradeAction()
    {
        try {
            $modules = $this->getRequest()->getPost('modules');
            if (!$modules) {
                Mage::throwException($this->__('No modules to upgrade'));
            }
            Mage::helper('mextensionmanager')->upgradeModules($modules);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Modules have been upgraded'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    public function massUninstallAction()
    {
        try {
            $modules = $this->getRequest()->getPost('modules');
            if (!$modules) {
                Mage::throwException($this->__('No modules to uninstall'));
            }
            Mage::helper('mextensionmanager')->uninstallModules($modules);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Modules have been uninstalled'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/tools/mextensionmanager');
    }

    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mextensionmanager/adminhtml_module_grid')->toHtml()
        );
    }
}