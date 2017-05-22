<?php
class Angara_Gemstonecolor_Model_Source_Gemstonecolors
{
	 /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
		$options = array();
		$options[] = array('value' => '', 'label'=>Mage::helper('catalog')->__('-- Please select color image --'));
		$options[] = array('value' => 'yellow.png', 'label'=>Mage::helper('catalog')->__('Yellow Image'));
		$options[] = array('value' => 'red.png', 'label'=>Mage::helper('catalog')->__('Red Image'));
		$options[] = array('value' => 'blue.png', 'label'=>Mage::helper('catalog')->__('Blue Image'));
		$options[] = array('value' => 'white.png', 'label'=>Mage::helper('catalog')->__('White Image'));
		return $options;
		
    }
	
	/**
     * Retrieve options for grid filter
     *
     * @param boolean $defaultValues
	 * 
     * @return Array $filterOptions
     */
	public function toFilterableOptionArray($defaultValues = false) {
		$options = $this->toOptionArray($defaultValues);
		$filterOptions = array();
		if(count($options)) {
			foreach($options as $option) {
				if(isset($option['value']) && isset($option['label'])) {
					$filterOptions[$option['value']] = $option['label'];
				}
			}
		}
		return $filterOptions;
	}
}