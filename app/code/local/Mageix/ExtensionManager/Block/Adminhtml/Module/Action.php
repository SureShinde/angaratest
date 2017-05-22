<?php

class Mageix_ExtensionManager_Block_Adminhtml_Module_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $mextensionmanager = Mage::getConfig()->getNode("modules/{$row->getModuleName()}/mextensionmanager");
        return isset($mextensionmanager['changelog']) ? '<a href="'.$mextensionmanager['changelog'].'">'.$this->__('Changelog').'</a>' : '';
    }
}