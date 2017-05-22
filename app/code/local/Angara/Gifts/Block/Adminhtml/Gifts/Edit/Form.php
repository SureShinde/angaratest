<?php
/**
 * @category   Angara
 * @package    Angara_Gifts
 * @copyright  Copyright (c) 2014 Angara eCommerce (http://www.angara.com)
 * @license    http://angara.com/LICENSE-COMMUNITY.txt
 */
 
class Angara_Gifts_Block_Adminhtml_Gifts_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                        'id' => 'edit_form',
                        'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                        'method' => 'post',
                        'enctype' => 'multipart/form-data'
                        )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}