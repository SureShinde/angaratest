<?php
class Angara_Digger_Block_Adminhtml_Grammar_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   public function __construct()
   {
       parent::__construct();
       $this->setId('grammarGrid');
       $this->setDefaultSort('id');
       $this->setDefaultDir('DESC');
       $this->setSaveParametersInSession(true);
	   
   }
   protected function _prepareCollection()
   {
  
      $collection = Mage::getModel('digger/grammar')->getCollection();
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
       $this->addColumn('grammar_rule',
               array(
                    'header' => 'Grammar Rule',
                    'align' =>'left',
                    'index' => 'grammar_rule',
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