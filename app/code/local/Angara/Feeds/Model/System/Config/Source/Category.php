<?php
class Angara_Feeds_Model_System_Config_Source_Category
{
    public function toOptionArray($addEmpty = true)
    {
		$collection= Mage::getModel('catalog/category')->getCollection() 
			->addAttributeToSelect('name') 
			->addAttributeToSelect('is_active');
        $options = array();

        if ($addEmpty) {
            $options[] = array(
                'label' => Mage::helper('adminhtml')->__('-- Please Select a Category --'),
                'value' => ''
            );
        }
		$options[] = array('label' => Mage::helper('adminhtml')->__('-- Please Select a Category --'));
        foreach ($collection as $category) {
            $options[] = array(
               'label' => $category->getName(),
               'value' => $category->getId()
            );
        }
		return $options;
    }
}
