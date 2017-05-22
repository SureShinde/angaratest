<?php
class Angara_Digger_Block_Adminhtml_Synonym_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   public function __construct()
   {
       parent::__construct();
       $this->setId('synonymGrid');
       $this->setDefaultSort('id');
       $this->setDefaultDir('DESC');
       $this->setSaveParametersInSession(true);
	   
   }
   
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('digger/synonym')->getCollection();
		$this->setCollection($collection);
		
		$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
		$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));
		
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
       $this->addColumn('master_keyword',
               array(
                    'header' => 'Master Keyword',
                    'align' =>'left',
                    'index' => 'master_keyword',
					'sortable'  => bool,
					
              ));
       $this->addColumn('synonym', array(
                    'header' => 'Synonym',
                    'align' =>'left',
                    'index' => 'synonym',
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
	
	/*
		S:VA	Remove Item(s)
	*/
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('ids');
		$this->getMassactionBlock()->setUseSelectAll(true);
		$this->getMassactionBlock()->addItem('remove_digger', array(
			 'label'=> Mage::helper('digger')->__('Remove Synonyms'),
			 'url'  => $this->getUrl('*/adminhtml_index/massRemove'),
			 'confirm' => Mage::helper('digger')->__('Are you sure?')
		));
		return $this;
	}
}
?>