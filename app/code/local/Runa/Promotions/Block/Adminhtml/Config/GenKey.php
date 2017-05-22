<?php

class Runa_Promotions_Block_Adminhtml_Config_GenKey
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Set template to itself
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('runa/promotions/setup/gen_key.phtml');
        }
        return $this;
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(array(
            'button_label' => "Setup Module",
            'html_id' => $element->getHtmlId(),
            'ajax_url' => $this->getUrl('runa_promotions/adminhtml_service/setup'),
            'name'=> $element->getName(),
            'value'=> $element->getValue()
        ));

        return $this->_toHtml();
    }
}
