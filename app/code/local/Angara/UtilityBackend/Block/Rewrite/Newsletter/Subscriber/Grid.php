<?php 

/**
 * @rewrite by Asheesh
 */ 
 
class Angara_UtilityBackend_Block_Rewrite_Newsletter_Subscriber_Grid extends Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
{
    protected function _prepareColumns()
    {
		parent::_prepareColumns();
		$this->addColumnAfter('ref_url', array(
			'header'    => Mage::helper('newsletter')->__('Refrence Url'),
			//the index must match the name of the column created in step 1
			'index'     => 'ref_url',
			'default'   =>    '----'
		),'store');
		$this->addColumnAfter('sub_type', array(
			'header'    => Mage::helper('newsletter')->__('Subscription Type'),
			//the index must match the name of the column created in step 1
			'index'     => 'sub_type',
			'default'   =>    '----'
		),'ref_url');
		$this->addColumnAfter('capture_date', array(
			'header'    => Mage::helper('newsletter')->__('Capture Date'),
			//the index must match the name of the column created in step 1
			'index'     => 'capture_date',
			'default'   =>    '----'
		),'sub_type');
		$this->addColumnAfter('capture_time', array(
			'header'    => Mage::helper('newsletter')->__('Capture Time'),
			//the index must match the name of the column created in step 1
			'index'     => 'capture_time',
			'default'   =>    '----'
		),'capture_date');
		$this->addColumnAfter('user_ip', array(
			'header'    => Mage::helper('newsletter')->__('User IP'),
			//the index must match the name of the column created in step 1
			'index'     => 'user_ip',
			'default'   =>    '----'
		),'capture_time');
        return $this;
    }
}
