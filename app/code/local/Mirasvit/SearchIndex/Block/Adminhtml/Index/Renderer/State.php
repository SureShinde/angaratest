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
 * @version   2.2.9
 * @revision  370
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_SearchIndex_Block_Adminhtml_Index_Renderer_State extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
        $status = $row->getStatus();
        $isActive = $row->getIsActive();
        
        $label = 'Disabled';
        $class = 'grid-severity-major';

        if ($isActive) {
            if ($status == 1) {
                $class = 'grid-severity-notice';
                $label = 'Ready';
            } else {
                $class = 'grid-severity-critical';
                $label = 'Reindex Required';
            }
        }
        
        // switch($data) {
        //     case AW_Advancedsearch_Model_Source_Catalogindexes_State::READY:
        //         $cssClass = 'grid-severity-notice';
        //         break;
        //     case AW_Advancedsearch_Model_Source_Catalogindexes_State::REINDEX_REQUIRED:
        //         $cssClass = 'grid-severity-critical';
        //         break;
        //     case AW_Advancedsearch_Model_Source_Catalogindexes_State::DISABLED:
        //     case AW_Advancedsearch_Model_Source_Catalogindexes_State::NOT_INDEXED:
        //     default:
        //         $cssClass = 'grid-severity-major';
        // }
        return $formatString = "<span class='$class'><span>$label</span></span>";
    }
}
