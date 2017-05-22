<?php
/**
 * @category   Angara
 * @package    Angara_Gifts
 * @copyright  Copyright (c) 2014 Angara eCommerce (http://www.angara.com)
 * @license    http://angara.com/LICENSE-COMMUNITY.txt
 */

class Angara_Gifts_Block_Adminhtml_Gifts_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('gifts_form', array('legend' => Mage::helper('gifts')->__('Item information')));

        $fieldset->addField('title', 'text', array(
                'label' => Mage::helper('gifts')->__('Title'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'title',
        ));

        $fieldset->addField('status', 'select', array(
                'label' => Mage::helper('gifts')->__('Status'),
                'name' => 'gift_status',
                'values' => array(
                        array(
                                'value' => 1,
                                'label' => Mage::helper('gifts')->__('Enabled'),
                        ),
                        array(
                                'value' => 2,
                                'label' => Mage::helper('gifts')->__('Disabled'),
                        ),
                ),
        ));
        
        $fieldset->addField('amount', 'text', array(
                'label' => Mage::helper('gifts')->__('Minimum Purchase Price'),
                'name' => 'amount',
                'class' => 'validate-currency-dollar',
        ));
        
        
        $fieldset->addField('qty', 'text', array(
                'label' => Mage::helper('gifts')->__('Minimum Purchase Qty'),
                'name' => 'qty',
                'class' => 'validate-number'
        ));

        if (Mage::getSingleton('adminhtml/session')->getGiftsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getGiftsData());
            Mage::getSingleton('adminhtml/session')->setGiftsData(null);
        } elseif (Mage::registry('gifts_data')) {
            $form->setValues(Mage::registry('gifts_data')->getData());
        }
        return parent::_prepareForm();
    }

}