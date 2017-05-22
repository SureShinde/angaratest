<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.2.8
 * @revision  334
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Mirasvit_SearchIndex
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com)
 */


/**
 * Abstract block for render attribute list
 *
 * @category Mirasvit
 * @package  Mirasvit_SearchIndex
 */
class Mirasvit_SearchIndex_Block_Adminhtml_System_Attribute extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('attribute', array(
            'label' => Mage::helper('adminhtml')->__('Attribute'),
            'style' => 'width:120px',
        ));
        $this->addColumn('value', array(
            'label' => Mage::helper('adminhtml')->__('Weight'),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Attribute');
        parent::__construct();
    }

    protected function _getAttributes()
    {
        $originalData = $this->getElement()->getData('original_data');
        $indexCode    = $originalData['index_code'];
        $index        = Mage::helper('searchindex/index')->getIndexModel($indexCode);

        return $index->getAvailableAttributes();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column    = $this->_columns[$columnName];
        $inputName = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($columnName == 'attribute') {
            $attributes = $this->_getAttributes();

            $html = '<select class="select" name="' . $inputName . '">';
            foreach ($attributes as $code => $label) {
                $html .= '<option value="'.$code.'" #{option_'.$code.'}>'
                    .addslashes($label).' ['.$code.']'
                    .'</option>';
            }
            $html .= '</select>';
            return $html;

        } else {
            return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}"'.
               ' class="input-text" style="width:50px;" />';
        }
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row['option_'.$row['attribute']] = 'selected';
    }
}