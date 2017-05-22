<?php
class Studs_Diamondstud_Block_Diamondstud extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
	    $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Home Page'),  'link'=>Mage::getBaseUrl()));
	   $breadcrumbs->addCrumb('Earrings', array('label'=>'Earrings', 'title'=>'Earrings', 'link'=>'../earrings.html'));
	   
	   $breadcrumbs->addCrumb('Diamondstuds', array('label'=>'Diamondstuds', 'title'=>'Diamondstuds', 'link'=>''));

	return parent::_prepareLayout();
		
		return parent::_prepareLayout();
    }
    
     public function getDiamondstud()     
     { 
        if (!$this->hasData('diamondstud')) {
            $this->setData('diamondstud', Mage::registry('diamondstud'));
        }
        return $this->getData('diamondstud');
        
    }
}