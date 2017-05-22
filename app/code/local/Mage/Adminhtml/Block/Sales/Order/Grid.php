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
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
		//added by hitesh
		$collection->getSelect()
		->join(
			'sales_flat_order_item',
			'sales_flat_order_item.order_id = main_table.entity_id',
			array(
				'sku'   => new Zend_Db_Expr('group_concat(sales_flat_order_item.sku SEPARATOR ", ")'),
				'qty_ordered'   => new Zend_Db_Expr('group_concat(CAST(sales_flat_order_item.qty_ordered AS UNSIGNED) SEPARATOR ", ")'),
				'total_item_price'   => new Zend_Db_Expr('ROUND(SUM(sales_flat_order_item.original_price),2)'),
				'product_type'   => new Zend_Db_Expr('group_concat(sales_flat_order_item.product_type)'),  // added by saurabh
				'vendor_lead_time' => new Zend_Db_Expr('MAX(sales_flat_order_item.vendor_lead_time)'),
				'lead_sku'   => 'sku',
			)
    	)
		/* ->join(
			'sales_flat_order_item',
			'sales_flat_order_item.order_id = main_table.entity_id AND sales_flat_order_item.sku NOT LIKE "FR%" AND sales_flat_order_item.sku NOT LIKE "FE%"',
			array(
				'vendor_lead_time'   => new Zend_Db_Expr('MAX(sales_flat_order_item.vendor_lead_time)'),
			)
    	) */
		// added and changes by saurabh
		->join(
			'sales_flat_order',
			'sales_flat_order.entity_id = main_table.entity_id',
			array(	
				'billing_name_email' => new Zend_Db_Expr("CONCAT(billing_name, ' [', customer_email,'] ')"),
				'coupon_code' => 'coupon_code',
				'pay_mode' => 'pay_mode',
				'base_subtotal' => 'base_subtotal',
				'subtotal' => 'subtotal', // added by pankaj
				'shipping_method' => 'shipping_method'
				//'oldcm_order_num' => 'oldcm_order_num'
			)
		)
		
		/* ->join(
			'sales_flat_order_payment',
			'sales_flat_order_payment.parent_id = main_table.entity_id',
			array(
				'cc_type' => 'cc_type'
			)
		) */
		
		->joinLeft('sales_flat_order_oktoship', 'sales_flat_order_oktoship.orderid=main_table.entity_id');
		$collection->getSelect()->group('main_table.entity_id');
		$collection->getSelect()->group('main_table.store_id');
		$collection->getSelect()->where('sales_flat_order_item.product_type  = "simple"');
        $this->setCollection($collection);
		
		$rolename = strtolower(Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleName());
		/* S:VA	To limit an user role to view 12 month sales only */
		if( strstr($rolename,'probation') ){
			$collection->addAttributeToFilter('sales_flat_order_item.created_at', array(
				'from' => date("d F Y", strtotime("-4 month") ),
				'date' => true,
			));
		}/*elseif( strstr($rolename,'sales') ){
			$collection->addAttributeToFilter('`sales_flat_order_item`.created_at', array(
				'from' => date("d F Y", strtotime("-12 month") ),
				'date' => true,
			));
		}*/
		/* parent::_prepareCollection();
		echo $collection->getSelect()->__toString(); exit; */
        return parent::_prepareCollection();
    }  

	protected function _remainingDays($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return $this;
		}
		//echo $value;die;
		switch($value){
			case '5': $remainingTime = date('Y-m-d', strtotime("-5 days")); break;
			case '7': $remainingTime = date('Y-m-d', strtotime("-1 week")); break;
			case '8': $remainingTime = date('Y-m-d', strtotime("-8 days")); break;
			case '10': $remainingTime = date('Y-m-d', strtotime("-10 days")); break;
			case '28': $remainingTime = date('Y-m-d', strtotime("-28 days")); break;
			case '30': $remainingTime = date('Y-m-d', strtotime("-1 month")); break;
			default: $remainingTime = ''; break;
		}
		//echo date('Y-m-d').'=='.$remainingTime;die;
		if(strlen($remainingTime) > 0){
			$this->getCollection()->addAttributeToFilter('main_table.'.$column->getData('index'), array('gteq' => $remainingTime));
		}

		return $this;
	}
	
    protected function _prepareColumns()
    {

        $this->addColumn('remaining_days', array(
            'header'=> Mage::helper('sales')->__('Days #'),
            'width' => '80px',
			'index' => 'created_at',
			'type'  => 'options',
            'options' => Mage::getSingleton('sales/order_remaining')->getStatuses(),
			'renderer'  => 'Mage_Adminhtml_Block_Remaining',
			'filter_condition_callback' => array($this, '_remainingDays')			
        ));
		
		/* $this->addColumn('cc_type', array(
            'header'=> Mage::helper('sales')->__('Card Type'),
            'width' => '80px',
            'type'  => 'options',
			'options' => Mage::getSingleton('payment/config')->getCcTypes(),
            'index' => 'cc_type',
        )); */
		
		
		$this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
			'filter_index'  => 'main_table.increment_id'
        ));
		
		/*$this->addColumn('oldcm_order_num', array(
			'header'    => Mage::helper('sales')->__('Old CM Order'),
			'index'     => 'oldcm_order_num',
			'type'      => 'text',
			'filter_index'  => 'sales_flat_order.oldcm_order_num',
			//'filter'    => false,
		));
		
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Store'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => false,
				'filter_index'  => 'main_table.store_id',
				//'filter'    => false,
            ));
        }*/

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
			'filter_index'  => 'main_table.created_at',
            'type' => 'datetime',
            'width' => '100px',
			'filter_condition_callback' => array($this, '_createdAtFilter'),
        ));
		
		$this->addColumn('vendor_lead_time', array(
			'header'    => Mage::helper('catalog')->__('Estimated Delivery Date'),
			'index'     => 'vendor_lead_time',
			'type' => 'text',
			'renderer'  => 'Mage_Adminhtml_Block_Estdd',
			'filter'    => false,
            'sortable'  => false,
		));
		
		$this->addColumn('sku', array(
			'header'    => Mage::helper('catalog')->__('SKU'),
			'index'     => 'sku',
			'type' => 'text'
		));
		
		$this->addColumn('coupon_code', array(
			'header'    => Mage::helper('catalog')->__('Coupon Code'),
			'index'     => 'coupon_code',
			'type' => 'text'
		));

        $this->addColumn('billing_name', array(
            'header' 		=> 	Mage::helper('sales')->__('Bill to Name'),
            'index' 		=> 	'billing_name_email',
			'filter_index'  => 	'main_table.billing_name',
			'filter_condition_callback' => array($this, 'filter_billing_name')
        ));
		
		$this->addColumn('qty_ordered', array(
            'header'    => Mage::helper('sales')->__('Qtys'),
            'index'     => 'qty_ordered',
            'type'      => 'text'
        ));
		
		$this->addColumn('pay_mode', array(
            'header'    => Mage::helper('sales')->__('Pay Mode'),
            'index'     => 'pay_mode',
            'type'      => 'text',
			'filter_index' => 'pay_mode'
			//'filter'    => false
        ));

        $this->addColumn('shipping_name', array(
            'header' 	=> Mage::helper('sales')->__('Ship to Name'),
            'index' 	=> 'shipping_name',
			'filter_condition_callback' => array($this, 'filter_shipping_name')
        ));
		
		$this->addColumn('base_subtotal', array(
            'header'    => Mage::helper('sales')->__('Subtotal'),
            'index'     => 'subtotal', //updated by pankaj
            'type'      => 'currency',
			'filter'    => false,
			'currency' => 'order_currency_code'
        ));		
		
        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
			'filter_index'  => 'main_table.base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code'
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
			'filter_index'  => 'main_table.grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code'
        ));
		
		$this->addColumn('oktoship', array(
            'header' => Mage::helper('sales')->__('OK TO SHIP'),
            'index' => 'oktoship',
			'type'  => 'options',
			'width' => '70px',
			'options' => $this->getOptionArrayOkToShip(),
			'renderer'  => 'Mage_Adminhtml_Block_Renderer',		//	S:VA
        ));
		
        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
			'filter_index'  => 'main_table.status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
		
		/* S:VA		Limiting few features for sales roles */
		$rolename = strtolower(Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleName());
		if( !strstr($rolename,'probation') ){
			$this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));
		}

        return parent::_prepareColumns();
    }
	
	
	/*
		S:VA	Changing the where condition for filtering the results
	*/
	public function filter_billing_name($collection, $column){
		if (!$value = $column->getFilter()->getValue()){
			return $this;
		}
		if(stristr($value, '@')){
			$this->getCollection()->getSelect()->where("customer_email like ?", "%$value%");	
		}else{
			$this->getCollection()->getSelect()->where("billing_name like ?", "%$value%");
		}		
		return $this;
	}
	
	
	public function filter_shipping_name($collection, $column){
		if (!$value = $column->getFilter()->getValue()){
			return $this;
		}
		$this->getCollection()->getSelect()->where("shipping_name like ?", "%$value%");
		return $this;
	}
	//	E:VA
	
	protected function _createdAtFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
		/* print_r($value);die; */
		$where = '';
		if(is_array($value)){
			if(isset($value['orig_from']) && strlen($value['orig_from']) > 0) {
				$dateFrom = date('Y-m-d',strtotime($value['orig_from']));
				$where .= "main_table.created_at >= '".$dateFrom." 00:00:00'";
			}
			if(isset($value['orig_to']) && strlen($value['orig_to']) > 0) {
				$dateFrom = date('Y-m-d',strtotime($value['orig_to']));
				if(strlen($where) > 0) $where .= ' AND ';
				$where .= "main_table.created_at <= '".$dateFrom." 23:59:59'";
			}
			if(strlen($where) > 0) {
				$this->getCollection()->getSelect()->where($where);
			}
		}
        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/sales_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('sales')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('Print Invoices'),
             'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('Print Packingslips'),
             'url'  => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> Mage::helper('sales')->__('Print Credit Memos'),
             'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label'=> Mage::helper('sales')->__('Print All'),
             'url'  => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
	
	// s: added by pankaj
	static public function getOptionArrayOkToShip()
	{
		$data_array = array(); 
		
		$data_array[0] = 'No';
		$data_array[1] = 'Yes';
		$data_array[2] = 'Cancel';		
		return($data_array);
	}
	// e: added by pankaj
}
