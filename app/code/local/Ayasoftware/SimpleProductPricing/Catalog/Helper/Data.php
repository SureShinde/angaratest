<?php
/**
 * 
 *
 * @category   Ayasoftware
 * @package    Ayasoftware_SimpleProductPricing
 * @author     EL Hassan Matar <support@ayasoftware.com>
 */
class Ayasoftware_SimpleProductPricing_Catalog_Helper_Data extends Mage_Core_Helper_Abstract
{
    
public function addAdminhtmlVersion($module)
{
    $layout = Mage::app()->getLayout();
    $version = (string)Mage::getConfig()
        ->getNode("modules/{$module}/version");
    $layout->getBlock('before_body_end')->append(
        $layout->createBlock('core/text')->setText('<script type="text/javascript">
           $$(".legality")[0].insert({after:"'.$module.' ver. '.$version.'<br/>"});
           </script>
        ')
    );
    return $this;
}
}