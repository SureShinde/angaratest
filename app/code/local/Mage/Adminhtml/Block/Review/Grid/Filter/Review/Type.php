<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml review grid filter by type
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Review_Grid_Filter_Review_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{

    protected function _getOptions()
    {
      $emptyOption = array('value' => null, 'label' => '');

        $optionGroups = $this->getColumn()->getOptionGroups();
        if ($optionGroups) {
            array_unshift($optionGroups, $emptyOption);

            return $optionGroups;
        }
        
        $colOptions = $this->getColumn()->getOptions();
        if (!empty($colOptions) && is_array($colOptions) ) {
            $options = array($emptyOption);
            foreach ($colOptions as $value => $label) {
                $options[] = array('value' => $value, 'label' => $label);
            }
        }
            return $options;
        return array();
    }

    public function getCondition()
    {   $value = $row->getTypeStatusId();
         $str=' '; 
        $v1=explode(',',$value); 
        //print_r($v1);die;      
        foreach($v1 as $v2) {
            
            $str=$str.($this->getString((int)$v2));
                $str.= "<br />";
           }


        return nl2br($str);
        
    }


    public function getString($value){
       if ($value==1) {
            return Mage::helper('review')->__('Shipping and delivery');
        } elseif ($value==2) {
            return Mage::helper('review')->__('Product');
        }elseif ($value==3) {
            return Mage::helper('review')->__('Customer service');
        }elseif ($value==4) {
            return Mage::helper('review')->__('Return and refund');
        }elseif ($value==5) {
            return Mage::helper('review')->__('Website');
        }elseif ($value==6){
            return Mage::helper('review')->__('Others');
        }else{
            return NULL;
        } 
    }

}// Class Mage_Adminhtml_Block_Review_Grid_Filter_Type END
