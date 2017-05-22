<?php
/*
	S:VA	Block Rewrite
*/
class Angara_UtilityBackend_Block_Rewrite_Sales_Order_Email_Items_Order_Default extends Mage_Sales_Block_Order_Email_Items_Order_Default
{
   
    public function getItemOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
               	// Angara Modification Start
				//$result = array_merge($result, $options['options']);
				for($i=0;$i<count($options['options']);$i++)
				{
					if($options['options'][$i]['label']!="VariationInfoToOrder" && $options['options'][$i]['label']!="Price Factor"){
						$arr['options'][] = $options['options'][$i];
					}
				}
                $result = array_merge($result, $arr['options']);
				// Angara Modification End
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }
}
