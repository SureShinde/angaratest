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
 
class Mageix_Ixcbadv_Block_Adminhtml_Error_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
		$this->setId('ID');
        // This is the primary key of the database
        $this->setDefaultSort('created_date');
        $this->setDefaultDir('DESC');
        //$this->setSaveParametersInSession(true);
    }
 
    protected function _prepareCollection()
    {	
        $collection = Mage::getModel('ixcbadv/error')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('ID', array(
            'header'    => Mage::helper('ixcbadv')->__('ID'),
            'align'     =>'left',
            'width'     => '50px',
            'index'     => 'ID',
        ));

		$this->addColumn('created_date', array(
            'header'    => Mage::helper('ixcbadv')->__('Date'),
            'align'     =>'left',
            'width'     => '100px',
            'index'     => 'created_date',
        ));

		$this->addColumn('api_name', array(
            'header'    => Mage::helper('ixcbadv')->__('API Name'),
            'align'     =>'left',
            'width'     => '100px',
            'index'     => 'api_name',
        ));
        
        
        
        	$this->addColumn('error_type', array(
            'header'    => Mage::helper('ixcbadv')->__('Error Type'),
            'width'     => '120px',
            'align'     => 'left',
            'index'     => 'error_type',
	    'type'      => 'options',
            'options'   => array('Sender' => $this->__('400'), 'Forbidden' => $this->__('403'), 'Internal Server Error' => $this->__('500'), 'Bad Gateway' => $this->__('502'), 'Service Unavailable' => $this->__('503')),
        ));
        
        
        
        $this->addColumn('error_code', array(
            'header'    => Mage::helper('ixcbadv')->__('Error Code'),
            'width'     => '120px',
            'align'     => 'left',
            'index'     => 'error_code',
        ));
 
		$this->addColumn('description', array(
            'header'    => Mage::helper('ixcbadv')->__('Description'),
            'align'     =>'left',
            'filter'    => false,
            'width'     => '250px',
            'index'     => 'description',
        ));
        
        
        	$this->addColumn('request_id', array(
            'header'    => Mage::helper('ixcbadv')->__('Request ID'),
            'align'     =>'left',
            'filter'    => false,
            'width'     => '250px',
            'index'     => 'request_id',
        ));

        return parent::_prepareColumns();
    }
 
}