<?php 

/**
 * @rewrite by Asheesh
 */ 
 
class Angara_UtilityBackend_Model_Sales_Order_Item extends Mage_Sales_Model_Order_Item
{
	/**
     * Get product options array
     *
     * @return array
     */
    public function getProductOptions()
    {
        if ($options = $this->_getData('product_options')) {
             // Angara Modification Start
			//return unserialize($options);
			// hprahi codes customisation
			$arr = unserialize($options);
			if(isset($arr['options'])){
				for($i=0;$i<count($arr['options']);$i++)
				{
					$val = $arr['options'][$i]['value'];
					if($arr['options'][$i]['label']!="VariationInfoToOrder" && $arr['options'][$i]['label']!="Price Factor"){
						$arrval = explode('|',$val);
						$arr['options'][$i]['value'] = $arrval[0];
					}
				}
			}
			return $arr;
			// hprahi code ends
			 // Angara Modification End
        }
        return array();
    }
}
