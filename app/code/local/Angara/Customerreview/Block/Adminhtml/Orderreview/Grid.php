<?php

class Angara_customerreview_Block_Adminhtml_Orderreview_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  	public function __construct()
  	{
      	parent::__construct();
      	$this->setId('OrderReviewGrid');
  	}

	protected function _prepareCollection()
  	{      	
		$collection = Mage::getModel('sales/order')->getCollection()							
							->addFieldToFilter('main_table.state',Mage_Sales_Model_Order::STATE_COMPLETE)
							->addFieldToFilter('main_table.updated_at',array('lt' => Mage::helper('customerreview/data')->getLaunchedDate()))
							->addFieldToFilter('main_table.customer_email',array('neq' => ''))
							->addFieldToFilter('main_table.customer_email',array('like' => '%angara.com'));		//	S:VA
							//->addAttributeToSelect(array('main_table.customer_email', 'main_table.customer_firstname', 'main_table.created_at'));

		$collection->getSelect()->order('created_at DESC');		
		$collection->getSelect()->group('customer_email');		
				
		$ruleId = Mage::helper('customerreview/data')->getOrderReviewMailRuleId();

		$collection->getSelect()->columns('group_concat(increment_id) as orderids'); 
		$collection->getSelect()->columns('count(*) as totalorders'); 

		$collection->getSelect()->columns('(select count(*) from customerreview_log where customerreview_log.email = main_table.customer_email AND rule_id='.$ruleId. ' ) as totalMailScheduled');  
		$collection->getSelect()->columns('(select count(*) from aw_followup_queue where recipient_email = main_table.customer_email AND rule_id='.$ruleId . ' AND status="S") as mailsSent');  
		$collection->getSelect()->columns('(select count(*) from customerreview_submission where customerreview_submission.email = main_table.customer_email AND rule_id='. $ruleId . ' ) as reviewsDone'); 		


		//echo( (string) $collection->getSelect());	 exit();
		$this->setCollection($collection);
		Mage::register('customerreview', '1');
      	return parent::_prepareCollection();
  	}			
	
	protected function _prepareColumns()
	{	
	    $this->addColumn('customer_email', array(
            'header'=> Mage::helper('customerreview')->__('CustomerEmail'),
            'width' => '150px',
            'type'  => 'text',
			'filter_index' =>'main_table.customer_email',
            'index' => 'customer_email',
			'name'	=> 'customer_email',
        ));	
			
	    $this->addColumn('created_at', array(
            'header'=> Mage::helper('customerreview')->__('Order Date'),
            'width' => '150px',
            'type'  => 'datetime',
			'filter_index' =>'main_table.created_at',
            'index' => 'created_at',
			'name'	=> 'created_at',
        ));
		
	    $this->addColumn('customer_firstname', array(
            'header'=> Mage::helper('customerreview')->__('Customer Name'),
            'width' => '150px',
            'type'  => 'text',
			'filter_index' =>'main_table.customer_firstname',
            'index' => 'customer_firstname',
			'name'	=> 'customer_firstname',
        ));	
		$this->addColumn('totalorders', array(
			'header'=> Mage::helper('customerreview')->__('totalorders'),
			'width' => '150px',
			'type'  => 'text',
			'filter'    => false,
			'sortable'  => false,
			'index' => 'totalorders',
			'name'	=> 'totalorders',
		));
	    $this->addColumn('totalMailScheduled', array(
            'header'=> Mage::helper('customerreview')->__('MailsScheduledTillDate'),
            'width' => '150px',
            'type'  => 'text',
            'filter'    => false,
            'sortable'  => false,
            'index' => 'totalMailScheduled',
			'name'	=> 'totalMailScheduled',
        ));		
	    $this->addColumn('mailsSent', array(
            'header'=> Mage::helper('customerreview')->__('MailsSent'),
            'width' => '150px',
            'type'  => 'text',
            'filter'    => false,
            'sortable'  => false,
            'index' => 'mailsSent',
			'name'	=> 'mailsSent',
        ));			
		
	    $this->addColumn('reviewsDone', array(
            'header'=> Mage::helper('customerreview')->__('reviewsDone'),
            'width' => '150px',
            'type'  => 'text',
            'filter'    => false,
            'sortable'  => false,
            'index' => 'reviewsDone',
			'name'	=> 'reviewsDone',
        ));			
	    $this->addColumn('orderids', array(
            'header'=> Mage::helper('customerreview')->__('OrderIds'),
            'width' => '150px',
            'type'  => 'text',
            'filter'    => false,
            'sortable'  => false,
            'index' => 'orderids',
			'name'	=> 'orderids',
        ));		
	

	}

	protected function _prepareMassaction() {
        $this->setMassactionIdField('customer_email');
        $this->getMassactionBlock()->setFormFieldName('customer_email');
        $this->getMassactionBlock()->addItem('queue', array(
                'label'=> $this->__('queue'),
                'url'  => $this->getUrl('*/*/massactionqueue', array('_current'=>true)),
                'confirm'  => Mage::helper('customerreview')->__('Are you sure you want to do this?')
            )
        );
        return $this;
    }
			
	public function getRowUrl($row)
    {
		$row->setId($row->getCustomerEmail(). '--'. $row->getCustomerName());
		return false;
	// return $this->getUrl('*/*/viewpurchaseorder', array('po_id' =>  $row->getId()));

    }
    public function getRowId($row)
    {
        return $this->getRowUrl($row);
    }
	
	public function getGridUrl()
	{
		//return $this->getUrl('*/*/grid', array('_current'=>true));
	}
	public function getCollectionSize($collection)
	{	echo 'in--------';
		$countSelect = clone $collection->getSelect();
		//$select->reset();
		//$select =  $this->getConnection()->fetchOne("SELECT COUNT(distinct main_table.customer_email) FROM sales_flat_order AS main_table WHERE (main_table.state = 'complete') AND (main_table.created_at < '2012-05-18') AND (main_table.customer_email != '')"); 
		
		$countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

		$countSelect->reset(Zend_Db_Select::GROUP);
		
		$countSelect->columns('COUNT(DISTINCT main_table.customer_id)');
		
		return $countSelect;
	}

}
