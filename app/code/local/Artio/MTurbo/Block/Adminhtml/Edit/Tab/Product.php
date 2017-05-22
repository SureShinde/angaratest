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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * 
 *
 * @category   Artio
 * @package    Artio_MTurbo
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Artio_MTurbo_Block_Adminhtml_Edit_Tab_Product
    extends Artio_MTurbo_Block_Adminhtml_Edit_Tab_Abstract
{
	
	public function __construct() {
		parent::__construct();
		$this->setId('product_section');
		$this->_title = $this->getMyHelper()->__('Products');
	}
	
    protected function _prepareForm() {
    	
    	$config = Mage::getSingleton('mturbo/config');			
    	
    	$form = new Varien_Data_Form();
    	
    	/* fieldset for automatic refresh */
    	$layoutFieldsetRefresh = $form->addFieldset('products_refresh_fieldset', array(
            'legend' => $this->getMyHelper()->__( 'Automatic refresh settings' ),
            'class'  => 'fieldset'
        ));
        
        $layoutFieldsetRefresh->addField('add_newly_product_to_select', 'select', array(
            'name'      => 'add_newly_product_to_select',
            'label'     => $this->getMyHelper()->__('Add newly created categories to select').':',
        	'options'	=> array(
        					0 => $this->getMyHelper()->__('No'),
        					1 => $this->getMyHelper()->__ ( 'Yes' ) ) ) );

        $layoutFieldsetRefresh->addField('refresh_product', 'select', array(
            'name'      => 'refresh_product',
            'label'     => $this->getMyHelper()->__('Enable automatic refresh for preview of categories of saved product').':',
        	'options'	=> array(
        					0 => $this->getMyHelper()->__('No'),
        					1 => $this->getMyHelper()->__ ( 'Yes' ) ) ) );
        
        $layoutFieldsetRefresh->addField('refresh_parent_of_product', 'select', array(
                            'name'      => 'refresh_parent_of_product',
                            'label'     => $this->getMyHelper()->__('Enable automatic refresh for parent of saved product (configurable, grouped, bundled)').':',
                        	'options'	=> array(
                                0 => $this->getMyHelper()->__('No'),
                                1 => $this->getMyHelper()->__ ( 'Yes' ) ) ) );
        					
        $layoutFieldsetRefresh->addField('refresh_parents_for_product', 'select', array(
            'name'      => 'refresh_parents_for_product',
            'label'     => $this->getMyHelper()->__('Enable automatic refresh for previews of parents of categories of saved product').':',
        	'options'	=> array(
        					0 => $this->getMyHelper()->__('No'),
        					1 => $this->getMyHelper()->__ ( 'Yes' ) ) ) );
    	
        /* fieldset for tree */
    	$layoutFieldset = $form->addFieldset('products_fieldset', array(
            'legend' => $this->getMyHelper()->__( 'Select categories, where to cache product pages' ),
            'class'  => 'fieldset'
        ));
        
        /* tree */
     	$layoutFieldset->addType('categories_tree', Artio_MTurbo_Helper_Data::FORM_CATEGORY_TREE);
        $layoutFieldset->addField('categories2', 'categories_tree', array(                    
          'name'      => 'category_chooser2',
          'treeId'	  => 'category_chooser2',
          'categoryIds'	  => $config->getProductCategoriesAsArray(),
          'updateElementId' => 'product_categories',
          'formName' => 'edit_form'
      	));


        /* bind data */
      	$form->setValues($config->getData());
        $this->setForm($form);
       
        return parent::_prepareForm();
    }

}