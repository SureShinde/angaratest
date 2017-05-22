<?php
/**
 * Adminhtml reviews grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('reviwGrid');
        $this->setDefaultSort('created_at');
    }

    protected function _prepareCollection()
    {
        $model = Mage::getModel('review/review');
        $collection = $model->getProductCollection();

        if ($this->getProductId() || $this->getRequest()->getParam('productId', false)) {
            $productId = $this->getProductId();
            if (!$productId) {
                $productId = $this->getRequest()->getParam('productId');
            }
            $this->setProductId($productId);
            $collection->addEntityFilter($this->getProductId());
        }

        if ($this->getCustomerId() || $this->getRequest()->getParam('customerId', false)) {
            $customerId = $this->getCustomerId();
            if (!$customerId){
                $customerId = $this->getRequest()->getParam('customerId');
            }
            $this->setCustomerId($customerId);
            $collection->addCustomerFilter($this->getCustomerId());
        }

        if (Mage::registry('usePendingFilter') === true) {
            $collection->addStatusFilter($model->getPendingStatus());
        }

        $collection->addStoreData();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $statuses = Mage::getModel('review/review')
            ->getStatusCollection()
            ->load()
            ->toOptionArray();

        $tmpArr = array();
        foreach( $statuses as $key => $status ) {
            $tmpArr[$status['value']] = $status['label'];
        }

        $statuses = $tmpArr;

        $this->addColumn('review_id', array(
            'header'        => Mage::helper('review')->__('ID'),
            'align'         => 'right',
            'width'         => '50px',
            'filter_index'  => 'rt.review_id',
            'index'         => 'review_id',
        ));

        $this->addColumn('created_at', array(
            'header'        => Mage::helper('review')->__('Created On'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
            'filter_index'  => 'rt.created_at',
            'index'         => 'review_created_at',
        ));

        if( !Mage::registry('usePendingFilter') ) {
            $this->addColumn('status', array(
                'header'        => Mage::helper('review')->__('Status'),
                'align'         => 'left',
                'type'          => 'options',
                'options'       => $statuses,
                'width'         => '100px',
                'filter_index'  => 'rt.status_id',
                'index'         => 'status_id',
            ));
        }

        $this->addColumn('title', array(
            'header'        => Mage::helper('review')->__('Title'),
            'align'         => 'left',
            'width'         => '100px',
            'filter_index'  => 'rdt.title',
            'index'         => 'title',
            'type'          => 'text',
            //'truncate'      => 50,
            'escape'        => true,
        ));

        $this->addColumn('nickname', array(
            'header'        => Mage::helper('review')->__('Nickname'),
            'align'         => 'left',
            'width'         => '100px',
            'filter_index'  => 'rdt.nickname',
            'index'         => 'nickname',
            'type'          => 'text',
            //'truncate'      => 50,
            'escape'        => true,
        ));

        $this->addColumn('detail', array(
            'header'        => Mage::helper('review')->__('Review'),
            'align'         => 'left',
            'index'         => 'detail',
            'filter_index'  => 'rdt.detail',
            'type'          => 'text',
            'truncate'      => 50000,
            'nl2br'         => true,
            'escape'        => true,
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', array(
                'header'    => Mage::helper('review')->__('Visible In'),
                'index'     => 'stores',
                'type'      => 'store',
                'store_view' => true,
            ));
        }

        $this->addColumn('type', array(
            'header'    => Mage::helper('review')->__('Type'),
            'type'      => 'select',
            'index'     => 'type',
            'filter'    => 'adminhtml/review_grid_filter_type',
            'renderer'  => 'adminhtml/review_grid_renderer_type'
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('review')->__('Product Name'),
            'align'     =>'left',
            'type'      => 'text',
            'index'     => 'name',
            'escape'    => true
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('review')->__('Product SKU'),
            'align'     => 'right',
            'type'      => 'text',
            'width'     => '50px',
            'index'     => 'sku',
            'escape'    => true
        ));
		
		//	S:VA
		$this->addColumn('order_id', array(
            'header'        => Mage::helper('review')->__('Order Id'),
            'align'         => 'left',
            'width'         => '100px',
            'filter_index'  => 'rdt.order_id',
            'index'         => 'order_id',
            'type'          => 'text',
            //'truncate'      => 50,
            'escape'        => true,
        ));
		
		$this->addColumn('rating', array(
            'header'        => Mage::helper('review')->__('Rating'),
            'align'         => 'right',
            'width'         => '50px',
            'filter_index'  => 'rov.value',
            'index'         => 'value',
			//'filter_index'  => 'rt.review_id',
            //'index'         => 'review_id',
			//'renderer'  	=> 'Mage_Adminhtml_Block_Review_Renderer',
        ));
		//	E:VA
 
        
         $this->addColumn('type_status_id', array(
            'header'    => Mage::helper('review')->__('Review Type'),
            'type'      => 'options',
            'options' => $this->getOptionsStatusType(),
            'index'     => 'type_status_id',
            'filter_index'  => 'rt.type_status_id',
           //'filter'    => 'adminhtml/review_grid_filter_review_type',
            'filter_condition_callback' => array($this, 'reviewTypeFilter'),
             'renderer'  => 'adminhtml/review_grid_renderer_review_type'
        ));
         //E:SG

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('adminhtml')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getReviewId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('adminhtml')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/catalog_product_review/edit',
                            'params'=> array(
                                'productId' => $this->getProductId(),
                                'customerId' => $this->getCustomerId(),
                                'ret'       => ( Mage::registry('usePendingFilter') ) ? 'pending' : null
                            )
                         ),
                         'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false
        ));

        $this->addRssList('rss/catalog/review', Mage::helper('catalog')->__('Pending Reviews RSS'));
		//	S:VA
		$this->addExportType('*/*/exportCsv', Mage::helper('catalog')->__('CSV')); 			//	Used to show Export button at Grid
		//$this->addExportType('*/*/exportExcel', Mage::helper('catalog')->__('Excel'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('review_id');
        $this->setMassactionIdFilter('rt.review_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('reviews');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('review')->__('Delete'),
            'url'  => $this->getUrl(
                '*/*/massDelete',
                array('ret' => Mage::registry('usePendingFilter') ? 'pending' : 'index')
            ),
            'confirm' => Mage::helper('review')->__('Are you sure?')
        ));

        $statuses = Mage::getModel('review/review')
            ->getStatusCollection()
            ->load()
            ->toOptionArray();
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('update_status', array(
            'label'         => Mage::helper('review')->__('Update Status'),
            'url'           => $this->getUrl(
                '*/*/massUpdateStatus',
                array('ret' => Mage::registry('usePendingFilter') ? 'pending' : 'index')
            ),
            'additional'    => array(
                'status'    => array(
                    'name'      => 'status',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => Mage::helper('review')->__('Status'),
                    'values'    => $statuses
                )
            )
        ));
    }
    protected function reviewTypeFilter($collection, $column)
    {
            if (!$value = $column->getFilter()->getValue()) {
            return $this;
            }
        if($value<7){
            $this->getCollection()->getSelect()->where("type_status_id like ?", "%$value%");
           } 
           return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product_review/edit', array(
            'id' => $row->getReviewId(),
            'productId' => $this->getProductId(),
            'customerId' => $this->getCustomerId(),
            'ret'       => ( Mage::registry('usePendingFilter') ) ? 'pending' : null,
        ));
    }

    public function getGridUrl()
    {
        if( $this->getProductId() || $this->getCustomerId() ) {
            return $this->getUrl(
                '*/catalog_product_review/' . (Mage::registry('usePendingFilter') ? 'pending' : ''),
                array(
                    'productId' => $this->getProductId(),
                    'customerId' => $this->getCustomerId(),
                )
            );
        } else {
            return $this->getCurrentUrl();
        }
    }
    static public function getOptionsStatusType(){
        $data_array = array();      
        $data_array['1']       =   'Shipping and delivery';
        $data_array['2']       =   'Product';
        $data_array['3']       =   'Customer service';
        $data_array['4']       =   'Return and refund';
        $data_array['5']       =   'Website';
        $data_array['6']       =   'Others';
        
        return($data_array);
    }
}
