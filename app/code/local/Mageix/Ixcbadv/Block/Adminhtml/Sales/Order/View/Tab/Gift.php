<?php
/**
 * Mageix LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Mageix LLC's  End User License Agreement
 * that is bundled with this package in the file LICENSE.pdf
 * It is also available through the world-wide-web at this URL:
 * http://ixcba.com/index.php/license-guide/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to webmaster@mageix.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 * 
 * Magento Mageix IXCBADV Module
 *
 * @category	Checkout & Payments, Customer Registration & Login
 * @package 	Mageix_Ixcbadv
 * @copyright   Copyright (c) 2014 -  Mageix LLC 
 * @author      Brian Graham
 * @license	    http://ixcba.com/index.php/license-guide/   End User License Agreement
 *
 *
 *
 * Magento Mageix IXCBA Module
 * 
 * @category   Checkout & Payments
 * @package	   Mageix_Ixcba
 * @copyright  Copyright (c) 2011 Mageix LLC
 * @author     Brian Graham
 * @license   http://mageix.com/index.php/license-guide/   End User License Agreement
 */


class Mageix_Ixcbadv_Block_Adminhtml_Sales_Order_View_Tab_Gift
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_chat = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ixcbadv/backend/gift.phtml');
    }

    public function getTabLabel() {
        return $this->__('Ixcbadv Gift Information');
    }

    public function getTabTitle() {
        return $this->__('Ixcbadv Gift Information');
    }

    public function canShowTab() {
		$show = 'no';
		if($this->getOrder()->getExtOrderId()) {
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$tableName = Mage::getSingleton('core/resource')->getTableName('ixcbadv_gift_information');

			$sql_qry = "select * from $tableName where order_id = '".$this->getOrder()->getExtOrderId()."' and order_comments != '' ";
			$readresult = $write->query($sql_qry);

			if($res = $readresult->fetch()) {
				$show = 'yes';
			}
		}
        if($show == 'no') {
			return false;
		}else{
			return true;
		}
    }

    public function isHidden() {
        return false;
    }

    public function getOrder(){
        return Mage::registry('current_order');
    }
}

