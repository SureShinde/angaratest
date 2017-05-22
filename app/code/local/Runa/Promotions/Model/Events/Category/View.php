<?php

class Runa_Promotions_Model_Events_Category_View {

    public function addRunaTags($event)
    {
        $_layout = mage::app()->getLayout();

        $_block = $_layout->createBlock(
                        'Runa_Promotions_Block_Product_List',
                        'runa_aggregate_tags',
                        array('product_collection'=>$event->getCollection())
        );

        if ($_layout->getBlock('before_body_end') instanceof Mage_Core_Block_Text_List)
        {
            $_layout->getBlock('before_body_end')->append($_block);
        } else
        {
            Mage::getSingleton('runapromotions/log_debug')->err(new Zend_Exception("['before_body_end' BLOCK] is not there in template"));
        }
    }

}