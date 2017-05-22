<?php
class Angara_Arrivaldate_Block_Adminhtml_Leadtimerules_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   public function __construct()
   {
       parent::__construct();
       $this->setId('leadtimerulesGrid');
       $this->setDefaultSort('id');
       $this->setDefaultDir('DESC');
       $this->setSaveParametersInSession(true);
	   
   }
   protected function _prepareCollection()
   {
  
      $collection = Mage::getModel('arrivaldate/leadtimerules')->getCollection();
      $this->setCollection($collection);
	   
      return parent::_prepareCollection();
	  
    }
   protected function _prepareColumns()
   {
       $this->addColumn('id',
             array(
                    'header' => 'ID',
                    'align' =>'right',
                    'width' => '10px',
                    'index' => 'id',
               ));
       $this->addColumn('lead_time',
               array(
                    'header' => 'Lead Time',
                    'align' =>'left',
                    'index' => 'lead_time',
					'sortable'  => bool,
					
              ));
       $this->addColumn('shipping_method', array(
                    'header' => 'Shipping Method',
                    'align' =>'left',
                    'index' => 'shipping_method',
					'sortable'  => bool,
					
             ));
			 
			 $this->addColumn('arrival_text', array(
                    'header' => 'Arrival text',
                    'align' =>'left',
                    'index' => 'arrival_text',
					'sortable'  => bool,
					
             ));
			 
			$this->addColumn('action',
            array(
                'header'    =>  'Action',
                'width'     => '50',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => 'Edit',
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

       
	            return parent::_prepareColumns();
    }
    public function getRowUrl($row)
    {
         return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
?>