<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders creation process controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Hprahi @ AurigaIT
 */
class Mage_Adminhtml_Sales_Order_OktoshipController extends Mage_Adminhtml_Controller_Action
{
	public function adAction()
	{
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$result = $db->query("SELECT * FROM `sales_flat_order_oktoship` where orderid='" . $_POST['orderid'] . "'");
		$rows = $result->fetch(PDO::FETCH_ASSOC);
		if(!$rows) {
			$db->query("insert into `sales_flat_order_oktoship` set orderid='" . $_POST['orderid'] . "', oktoship = '" . addslashes(trim($_POST['oktoshipradio'])) . "', comment='" . addslashes(trim($_POST['oktoshiptext'])) . "'");
		}
		else
		{
			$db->query("update `sales_flat_order_oktoship` set oktoship = '" . addslashes(trim($_POST['oktoshipradio'])) . "', comment='" . addslashes(trim($_POST['oktoshiptext'])) . "' where orderid='" . $_POST['orderid'] . "'");
		}
		//print_r($rows);
		
		 $this->_redirect('*/sales_order/view/order_id/' . $_POST['orderid'] . '/key/fc76d8995581f2def56a78cc890ea630/');
	}
	
	public function updateShippingDateAction()
	{
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$result = $db->query("SELECT * FROM `sales_flat_order_estimated_shippingdate` where orderid='" . $_POST['orderid'] . "'");
		$rows = $result->fetch(PDO::FETCH_ASSOC);
		if(!$rows) {
			$db->query("insert into `sales_flat_order_estimated_shippingdate` set orderid='" . $_POST['orderid'] . "', shippingdate='" . date("Y-m-d", strtotime($_POST['shippingdate'])) . "'");
		}
		else
		{
			$db->query("update `sales_flat_order_estimated_shippingdate` set shippingdate = '" . date("Y-m-d", strtotime($_POST['shippingdate'])) . "' where orderid='" . $_POST['orderid'] . "'");
		}
		//print_r($rows);
		
		 $this->_redirect('*/sales_order/view/order_id/' . $_POST['orderid']);
	}
}
