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
 * @package     Mage_Page
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page data helper
 */
class Mage_Page_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function explodeCurrentUrl(){
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		$currentUrl=explode('?',$currentUrl);	
		return $currentUrl; 
	}
	
	public function getCurrentPage(){
		$current_page = Mage::getBlockSingleton('page/html_pager')->getCurrentPage();
		return $current_page;
	} 
	 
	public function getPages(){
		$product_collection = Mage::getSingleton('catalog/layer')->getProductCollection()->getSize();
		$limit = Mage::getSingleton('core/app')->getRequest()->getParam('limit');
		if(empty($limit))
		{
			$limit = Mage::getStoreConfig('catalog/frontend/grid_per_page');
		}
		
		$pages = $product_collection / $limit;
		$pages = ceil($pages);
		return $pages;
	}
	
	public function getNextPageUrl($currentUrl,$current_page){
		//$currentUrl	=	substr($currentUrl,0,-1);
		$nextUrl	=	$currentUrl.'?p='.$this->getNextPage($current_page);
		return  $nextUrl;
	}
	
	public function getNextPage($current_page){
		$next_page	=	$current_page+1;
		return $next_page;
	}
	
	public function getPrevPageUrl($currentUrl,$current_page){
		//$currentUrl	=	substr($currentUrl,0,-1);
		$prevUrl	=	$currentUrl.'?p='.$this->getPrevPage($current_page);
		return $prevUrl;
	}
	
	public function getPrevPage($current_page){
		$prev_page=$current_page-1;
		return $prev_page;
	}
}
?>