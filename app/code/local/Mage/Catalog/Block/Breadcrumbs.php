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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Catalog breadcrumbs
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Breadcrumbs extends Mage_Core_Block_Template
{
    /**
     * Retrieve HTML title value separator (with space)
     *
     * @param mixed $store
     * @return string
     */
    public function getTitleSeparator($store = null)
    {
        $separator = (string)Mage::getStoreConfig('catalog/seo/title_separator', $store);
        return ' ' . $separator . ' ';
    }

    /**
     * Preparing layout
     *
     * @return Mage_Catalog_Block_Breadcrumbs
     */
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('catalog')->__('Home'),
                'title'=>Mage::helper('catalog')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));

			//	S: Code added by Vaseem to show breadcrumb for pages that don't have them
			$current_category   = Mage::registry('current_category');
			$current_product    = Mage::registry('current_product');
			//var_dump($current_category);
			// 	Let's check if magento knows what current category is if it doesn't know, let's feed this info to it's brain :)
			
			//	S:VA	Show fix master category breadcrumb for pp
			//if(!$current_category && $current_product){
			if($current_product){
				//	Check if master_category defined for this product
				$masterCategory		=	$current_product->getmasterCategory();
				//echo '<br>masterCategory->'.$masterCategory;
				if($masterCategory){
					$_category = Mage::getModel('catalog/category')->loadByAttribute('name', $masterCategory);
					//echo '<pre>'; print_r($_category);die;
					Mage::unregister('current_category');
					Mage::register('current_category', $_category);
				}else{
					//	Get product Center Stone and Jewelry Type
					$stone1_name		= 	$current_product['stone1_name'];
					if($stone1_name!=''){
						$stone1Name 	= 	$current_product->getAttributeText('stone1_name');					//	Center Stone
					}
					$jewelry_type		= 	$current_product['jewelry_type'];
					if($jewelry_type!=''){
						$jewelryType 	= 	$current_product->getAttributeText('jewelry_type');					//	Jewelry Type
						if($jewelryType=='Ring' || $jewelryType=='Pendant'){
							$jewelryType	=	$jewelryType.'s';	
						}
						if($jewelryType=='Earrings' || $jewelryType=='Pendants'){
							if($stone1Name=='Blue Sapphire' || $stone1Name=='Pink Sapphire' || $stone1Name=='Yellow Sapphire'){
								$stone1Name	=	'Sapphire';	
							}			
						}
					}
					$categories 		= 	$current_product->getCategoryCollection()->addAttributeToSelect('name');
					//echo '<pre>'; print_r($categories);die;
					$excludeCategoryIds	=	array('414');			//	exclude category who name you don't want to show in breadcrumb
					foreach($categories as $category) {
						//echo '<pre>'; print_r($category);die;
						$categoryId			=	$category['entity_id'];
						if(!in_array($categoryId,$excludeCategoryIds)){
							$categoryNames[]	=	$category['name'];
							$categoryName		=	$category['name'];
							//	Check product attribute data with category name
							if (stristr($categoryName, $stone1Name) && stristr($categoryName, $jewelryType)){
								$modifiedCategoryName[]	=	$category['name'];
								Mage::unregister('current_category');
								Mage::register('current_category', $category);	
								break;
							}elseif( ($stone1Name!=='' || $jewelryType!='')	&& (stristr($categoryName, $stone1Name) || stristr($categoryName, $jewelryType)) ){
								$modifiedCategoryName[]	=	$category['name'];
								Mage::unregister('current_category');
								Mage::register('current_category', $category);
							}
						}
					}
				}
			}
			
			//	E: Code added by Vaseem to show breadcrumb for pages that don't have them
			
            $title = array();
            $path  = Mage::helper('catalog')->getBreadcrumbPath();

            foreach ($path as $name => $breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                $title[] = $breadcrumb['label'];
            }

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle(join($this->getTitleSeparator(), array_reverse($title)));
            }
        }
        return parent::_prepareLayout();
    }
}
