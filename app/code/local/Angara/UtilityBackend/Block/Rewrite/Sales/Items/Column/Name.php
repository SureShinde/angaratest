<?php

class Angara_UtilityBackend_Block_Rewrite_Sales_Items_Column_Name extends Angara_UtilityBackend_Block_Rewrite_Sales_Items_Column_Default
{
    /**
     * Add line breaks and truncate value
     *
     * @param string $value
     * @return array
     */
    public function getFormattedOption($value)
    {
        $_remainder = '';
        $value = Mage::helper('core/string')->truncate($value, 55, '', $_remainder);
        $result = array(
            'value' => nl2br($value),
            'remainder' => nl2br($_remainder)
        );

        return $result;
    }
}
?>
