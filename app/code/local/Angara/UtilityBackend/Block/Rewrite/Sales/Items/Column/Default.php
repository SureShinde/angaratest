<?php 

/**
 * @rewrite by Asheesh
 */ 
 
class Angara_UtilityBackend_Block_Rewrite_Sales_Items_Column_Default extends Mage_Adminhtml_Block_Sales_Items_Column_Default
{
    public function getOrderOptions()
    {
        $result = array();
		$arr = array();
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
				for($i=0;$i<count($options['options']);$i++)
				{
					if($options['options'][$i]['label']!="VariationInfoToOrder" && $options['options'][$i]['label']!="Price Factor"){
						$arr['options'][] = $options['options'][$i];
					}
				}
                $result = array_merge($result, $arr['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }

}
