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

class Mageix_Ixcbadv_IndexController extends Mage_Core_Controller_Front_Action{
    
	public function IndexAction() {
	  $this->loadLayout();
      $this->renderLayout();
    }
    

	
	public function SessionAction() {
		$session = Mage::getSingleton("core/session");
		$session->setData("contract_id", $this->getRequest()->getParam('contract_id'));
		echo "SUCCESS"; exit;
	}

	public function activenotificationAction() {
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		try {
			
			$tableName = Mage::getSingleton('core/resource')->getTableName('ixcbadv_notification');
			$sql_qry = "update $tableName set notification = '1'";
			$write->query($sql_qry);

		} catch (Exception $e) {
			$tbl_qry = "CREATE TABLE IF NOT EXISTS `ixcbadv_notification` (`ID` int(11) NOT NULL AUTO_INCREMENT, `notification` int(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			$write->query($tbl_qry);
			
			$tableName = Mage::getSingleton('core/resource')->getTableName('ixcbadv_notification');
			$ins_qry = "INSERT INTO $tableName (`ID`, `notification`) VALUES (NULL, '1');";
			$write->query($ins_qry);
		}
		echo "SUCCESS"; exit;
	}
	
	public function inactivenotificationAction() {
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		try {
			
			$tableName = Mage::getSingleton('core/resource')->getTableName('ixcbadv_notification');
			$sql_qry = "update $tableName set notification = '0'";
			$write->query($sql_qry);

		} catch (Exception $e) {
			$tbl_qry = "CREATE TABLE IF NOT EXISTS `ixcbadv_notification` (`ID` int(11) NOT NULL AUTO_INCREMENT, `notification` int(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			$write->query($tbl_qry);

			$tableName = Mage::getSingleton('core/resource')->getTableName('ixcbadv_notification');
			$ins_qry = "INSERT INTO $tableName (`ID`, `notification`) VALUES (NULL, '0');";
			$write->query($ins_qry);
		}
		echo "SUCCESS"; exit;
	}
}