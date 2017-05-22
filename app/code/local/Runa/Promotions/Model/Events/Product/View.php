<?php

class Runa_Promotions_Model_Events_Product_View {

    public function addRunaProductTag($event)
    {
        $_layout = mage::app()->getLayout();

        $_block = $_layout->createBlock(
                        'Runa_Promotions_Block_Product_Tag',
                        'runa_product_view_tag',
                        array('product' => mage::registry('product'))
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