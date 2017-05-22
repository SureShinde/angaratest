<?php
/*
	S:VA	Helper Rewrite
*/

class Angara_UtilityBackend_Helper_Rewrite_Catalog_Product_Configuration extends Mage_Catalog_Helper_Product_Configuration
{
    
    public function getCustomOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $product = $item->getProduct();
        $options = array();
        $optionIds = $item->getOptionByCode('option_ids');
        if ($optionIds) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option) {
                    $itemOption = $item->getOptionByCode('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItem($item)
                        ->setConfigurationItemOption($itemOption);

                    if ('file' == $option->getType()) {
                        $downloadParams = $item->getFileDownloadParams();
                        if ($downloadParams) {
                            $url = $downloadParams->getUrl();
                            if ($url) {
                                $group->setCustomOptionDownloadUrl($url);
                            }
                            $urlParams = $downloadParams->getUrlParams();
                            if ($urlParams) {
                                $group->setCustomOptionUrlParams($urlParams);
                            }
                        }
                    }
					// Angara Modification Start
					if($option->getTitle() != "VariationInfoToOrder" && $option->getTitle()!="Price Factor"){
						$value = $group->getFormattedOptionValue($itemOption->getValue());
						$arrval = explode('|',$value);
						$value = $arrval[0];						
						$options[] = array(
							'label' => $option->getTitle(),
							'value' => $value, //$group->getFormattedOptionValue($itemOption->getValue()),
							'print_value' => $group->getPrintableOptionValue($itemOption->getValue()),
							'option_id' => $option->getId(),
							'option_type' => $option->getType(),
							'custom_view' => $group->isCustomizedView(),
							'price' => $option->getPrice()
						);
					}
					// Angara Modification End
                }
            }
        }

        $addOptions = $item->getOptionByCode('additional_options');
        if ($addOptions) {
            $options = array_merge($options, unserialize($addOptions->getValue()));
        }

        return $options;
    }

    
}
