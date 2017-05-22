<?php
class Angara_Productbase_Block_Adminhtml_Pricing_Tabs extends Mage_Adminhtml_Block_Widget_Tabs 
{
    public function __construct() {
        parent::__construct();
        $this->setId('productbase_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Product Price Calculator'));
    }
    
    protected function _beforeToHtml() {
        $this->addTab('general', array(
                'label'   => $this->__('General'),
                'title'   => $this->__('General'),
                'content' => $this->getLayout()->createBlock('productbase/adminhtml_pricing_tab_general')->toHtml()
            )
        );

        if($tabName = $this->getRequest()->getParam('tab'))
        {
            $tabName = (strpos($tabName, 'productbase_tabs_')!==false)
                        ? substr($tabName, strlen('productbase_tabs_'))
                        : $tabName.'_section';

            if(isset($this->_tabs[$tabName])) $this->setActiveTab($tabName);
        }

        return parent::_beforeToHtml();
    }
}