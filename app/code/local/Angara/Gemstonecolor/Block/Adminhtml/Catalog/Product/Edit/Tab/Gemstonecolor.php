<?php 

class Angara_Gemstonecolor_Block_Adminhtml_Catalog_Product_Edit_Tab_Gemstonecolor extends Mage_Adminhtml_Block_Widget_Grid
{

	const LINK_TYPE_GEMSTONECOLOR	= 8;
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('gemstonecolor_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(array('in_products'=>1));
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Add filter
     *
     * @param object $column
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Upsell
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_getProduct()->getGemstonecolorReadonly();
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product_link')
			->setLinkTypeId(self::LINK_TYPE_GEMSTONECOLOR)
            ->getProductCollection()
            ->setProduct($this->_getProduct())
            ->addAttributeToSelect('*');
        if ($this->isReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = array(0);
            }
            $collection->addFieldToFilter('entity_id', array('in'=>$productIds));
        }
		//echo count($collection);
		//echo $collection->getSelect();die;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        if (!$this->_getProduct()->getGemstonecolorReadonly()) {
            $this->addColumn('in_products', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_products',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id'
            ));
        }

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
		
		$this->addColumn('image', array(
            'header'    => Mage::helper('catalog')->__('Image'),
            'index'     => 'image',
			'sortable'  => false,
			'searchable' => false,
			'renderer' => $this->getLayout()->createBlock('gemstonecolor/adminhtml_catalog_product_edit_tab_renderer_image'),
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('catalog')->__('Type'),
            'width'     => 100,
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', array(
            'header'    => Mage::helper('catalog')->__('Attrib. Set Name'),
            'width'     => 130,
            'index'     => 'attribute_set_id',
            'type'      => 'options',
            'options'   => $sets,
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'width'     => 90,
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('visibility', array(
            'header'    => Mage::helper('catalog')->__('Visibility'),
            'width'     => 90,
            'index'     => 'visibility',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => 80,
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('catalog')->__('Price'),
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'price'
        ));

			
		
	
        $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Position'),
            'name'              => 'position',
            'type'              => 'number',
            'width'             => 60,
            'validate_class'    => 'validate-number',
            'index'             => 'position',
			'filter_index'      => '`link_attribute_position_int`.`value`',
            'editable'          => !$this->_getProduct()->getGemstonecolorReadonly(),
            'edit_only'         => !$this->_getProduct()->getId(),
			'filter_condition_callback' => array($this, '_positionFilter'),
        ));
		
	/* $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Gemstone Color'),
            'name'              => 'gemcolorimage',
            'width'             => 60,
            'index'             => 'gemcolorimage',
			'sortable'  => false,
			'searchable' => false,
			//'options'   		=> Mage::getSingleton('gemstonecolor/source_gemstonecolors')->toFilterableOptionArray(),
            'editable'          => !$this->_getProduct()->getGemstonecolorReadonly(),
            'edit_only'         => !$this->_getProduct()->getId()
        )); */

        return parent::_prepareColumns();
    }
	
	protected function _positionFilter($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
			return $this;
		}
		/*
		if(isset($value['from'])) 
			$where .= 'link_attribute_position_int.value >='.$value['from'];
		if(isset($value['to'])) 
			$where .= 'link_attribute_position_int.value <='.$value['to'];	
		
		$collection->getSelect()->where($where); */

		return $this;
	}

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/adminhtml_gemstonecolor/gemstonecolorGrid', array('_current'=>true));
    }

    /**
     * Retrieve selected gemstonecolor products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getProductsGemstonecolor();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedGemstonecolorProducts());
        }
        return $products;
    }

    /**
     * Retrieve Gemstonecolor products
     *
     * @return array
     */
    public function getSelectedGemstonecolorProducts()
    {
        $products = array();
        foreach (Mage::registry('current_product')->getGemstonecolorProducts() as $product) {
            $products[$product->getId()] = array('position' => $product->getPosition());
        }
        return $products;
    }

}
