<?php
/**
 * Mageix LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageix.com/LICENSE-M1.txt
 *
 * @category   Mageix
 * @package    Mageix_Dropship
 * @copyright  Copyright (c) 2008-2009 Mageix LLC (http://www.mageix.com)
 * @license    http:///www.mageix.com/LICENSE-M1.txt
 */

class Mageix_ExtensionManager_Block_Adminhtml_Module_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('mextensionmanager_module_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('');
    }

    protected function _beforeToHtml()
    {
        $id = Mage::app()->getRequest()->getParam('id', 0);

        Mage::dispatchEvent('mextensionmanager_license_tabs', array('container'=>$this));

        $this->addTab('manage_modules_section', array(
            'label'     => Mage::helper('mextensionmanager')->__('Manage Modules'),
            'title'     => Mage::helper('mextensionmanager')->__('Manage Modules'),
            'content'   => $this->getLayout()->createBlock('mextensionmanager/adminhtml_module_grid')->toHtml(),
        ));
        /*
        $this->addTab('add_modules_section', array(
            'label'     => Mage::helper('mextensionmanager')->__('Add Modules'),
            'title'     => Mage::helper('mextensionmanager')->__('Add Modules'),
            'content'   => $this->getLayout()->createBlock('core/template')->setTemplate('mextensionmanager/add_modules.phtml')->toHtml(),
        ));
         */
        return parent::_beforeToHtml();
    }
}