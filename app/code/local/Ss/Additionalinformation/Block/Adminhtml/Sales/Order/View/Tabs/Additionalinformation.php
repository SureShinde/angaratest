<?php
/*
This module is made by Saurabh Sharma. This file is for add tab in sales order view section with acl.
*/
class Ss_Additionalinformation_Block_Adminhtml_Sales_Order_View_Tabs_Additionalinformation
    extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
   /**
     * Get tab label
     *
     * @return string
     */
	 
    public function getTabLabel()
    {
        return Mage::helper('additionalinformation')->__('Fraud Check');
    }

    public function getTabTitle()
    {
        return Mage::helper('additionalinformation')->__('Fraud Check');
    }

	/**
     * Check tab acl user
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/sales/additionalinformation');
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
		$id = $this->getRequest()->getParam('order_id');
		
        $order = Mage::getModel('sales/order')->load($id);
		
		$resultHtml .= $this->getLayout()->createBlock('additionalinformation/adminhtml_additionalinformation')->setTemplate('additionalinformation/form.phtml')->toHtml();	
        //$resultHtml .= $grid->toHtml();
        return $resultHtml;
    }
}