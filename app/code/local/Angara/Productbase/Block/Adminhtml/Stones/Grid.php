<?php
class Angara_Productbase_Block_Adminhtml_Stones_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('stonesGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        //$this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('productbase/stone')->getCollection();

        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            
        }
        return parent::_addColumnFilterToCollection($column);
    }
	
	protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'index' => 'id'
        ));
        $this->addColumn('shape',
            array(
                'header'=> Mage::helper('catalog')->__('Shape'),
                'index' => 'shape',
        ));
		$this->addColumn('title',
            array(
                'header'=> 'Stone',
                'index' => 'title',
        ));

        $this->addColumn('grade',
            array(
                'header'=> Mage::helper('catalog')->__('Grade'),
                'index' => 'grade'
        ));

        $this->addColumn('size',
            array(
                'header'=> Mage::helper('catalog')->__('Size'),
                'index' => 'size'
        ));

        $this->addColumn('weight',
            array(
                'header'=> Mage::helper('catalog')->__('Weight'),
                'index' => 'weight',
        ));

        $this->addColumn('constant',
            array(
                'header'=> Mage::helper('catalog')->__('Multiplier'),
                'index' => 'constant'
        ));
		
		$store = $this->_getStore();
        $this->addColumn('cost',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'cost',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
