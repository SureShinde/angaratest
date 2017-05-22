<?php
class Sutunam_Sharecart_Model_Mysql4_Cart extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sharecart/cart', 'sharecart_id');
    }
    
    /**
     *
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
       
        if (! $object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }        
        return $this;
    }
}