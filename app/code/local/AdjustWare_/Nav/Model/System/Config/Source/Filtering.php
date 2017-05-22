<?php
/**
 * Product:     Layered Navigation Pro for Magento 20/04/11
 * Package:     AdjustWare_Nav_2.1.1_0.1.4_8_74006
 * Purchase ID: QgBMpw5YKeeQ8cDxt9xj7DfR5rzlpaEBHoIuAZvOxv
 * Generated:   2011-06-08 07:09:19
 * File path:   app/code/local/AdjustWare/Nav/Model/System/Config/Source/Filtering.php
 * Copyright:   (c) 2011 AITOC, Inc.
 */
?>
<?php
class AdjustWare_Nav_Model_System_Config_Source_Filtering extends Varien_Object
{
    public function toOptionArray()
    {
        $options = array();   
        
        
        $options[] = array(
                'value'=> 'OR',
                'label' => Mage::helper('adjnav')->__('OR'),
        );

	$options[] = array(
                'value'=> 'AND',
                'label' => Mage::helper('adjnav')->__('AND'),
        );

        return $options;
    }
} 