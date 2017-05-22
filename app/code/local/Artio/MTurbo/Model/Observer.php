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
 * @category    Artio
 * @package     Artio_MTurbo
 * @copyright   Copyright (c) 2010 Artio (http://www.artio.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MTurbo observer.
 * 
 * TODO: odstranit z metod staticke vazby na config, event a pripadne dalsi
 *       vytvorit pomocne metody _getEvent atd., bude lepe kdyz to bude na jednom miste
 *
 * @category    Artio
 * @package     Artio_MTurbo
 * @author      Artio Magento Team (jiri.chmiel@artio.cz)
 */
class Artio_MTurbo_Model_Observer extends Mage_Core_Model_Abstract
{  
	
	
	/**
	 * Layout updating. Dynamic loaded block are replaced by MTurbo_Ajax blocks.
	 * @param Varien_Event_Observer $observer
	 */
	public function layoutUpdate($observer) {
		
		// if exists get variable DYNAMIC_BLOCKS_KEY then blocks will be processed
		$turnAjax = (bool) Mage::helper('mturbo/urlparams')->getParam(Artio_MTurbo_Helper_Urlparams::DYNAMIC_BLOCK);
		
		$patch = Mage::getSingleton('mturbo/layoutPatch');
		if ($patch->needToPatch() && !$patch->isPatched())
			return $this;

		// if block is created by Mturbo block or not exists above vars, then it is not processed 
		if (!$turnAjax || Mage::registry('mturbo_no_ajax') ) 
			return $this;
		
		// get block
		$event = $observer->getEvent();
   		$block = $event->getData('block');
   	
   		// prevent neverending loop
   		if ($block && $block instanceof Artio_MTurbo_Block_Ajax)
   			return $this;
   		
   		// process only dynamic loaded blocks
   		$dynamic = Mage::getSingleton('mturbo/config_dynamicTransformer');
   		$config = Mage::getSingleton('mturbo/config');
   		if ($id = $dynamic->getDynamicName($block)) {
   			
   			$name = $block->getNameInLayout();
   			   			   			
   			$layout = Mage::getSingleton('core/layout');
   			$layout->unsetBlock($name);
   			$layout->createBlock('mturbo/ajax', $name, array('ajax_identifier'=>$id));
   			
   			$headBlock = Mage::registry('mturbo_head_block');
   			if (!$headBlock) {
   				$headBlock = $layout->getBlock('head');
   				Mage::register('mturbo_head_block', $headBlock, true);
   			}
   			
   			$url = sprintf("%smturbofrontend", preg_replace('/^http?:/', '', Mage::getBaseUrl()));
   			$referer = Mage::helper('core/url')->getEncodedUrl();
   			$endScript = '';
   			
   			if ($config->getDynamicCheckoutCartLink())
   				$endScript .= "\n<script type=\"text/javascript\">if (typeof(mturboloader)!='undefined') mturboloader.cartLinkCss = ".Zend_Json::encode($config->getDynamicCheckoutCartLink()).";</script>";
   			
   			$endScript .= "\n<script type=\"text/javascript\">if (typeof(mturboloader)!='undefined') mturboloader.loadBlocks((location.protocol+\"$url\"), \"$referer\");</script>\n";
   			
   			
   			
   			$includes  = $headBlock->getIncludes();
   			$includes  = str_replace($endScript, '', $includes); // load script must be at end all scripts
   			$includes .= "\n<script type=\"text/javascript\">if (typeof(mturboloader)!='undefined') mturboloader.addBlockRequest('$id');</script>";
   			
   			$includes .= $endScript;
   			$headBlock->setIncludes($includes);

   		}
		Mage::unregister('_helper/mturbo/data');
	}
	
	public function systemCheck($observer) {
		Mage::unregister('_helper/mturbo/data');
	}
	
	
	/**
	 * Execute when admin logged.
	 */
	public function adminLogin($observer) {
		Mage::unregister('_helper/mturbo/data');
	}

	/**
	 * Customer login processing. Send MTurbo cookie.
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function customerLogin($observer) {
		Mage::getModel('core/cookie')->set( Artio_MTurbo_Helper_Data::COOKIE_IDENTIFIER, '1');
	}

	
	/**
	 * Customer logout processing. Delete MTurbo cookie.
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function customerLogout($observer) {
		Mage::getModel('core/cookie')->set( Artio_MTurbo_Helper_Data::COOKIE_IDENTIFIER, '', -100);
 	}
    
 	
    /**
     * The event "after save order" is invoked after placing the order
     * in the checkout. The product can reach the state "out of stock", 
     * this change needs the recaching of its page.
     *
     * @param Varien_Event_Observer $observer
     * @return Artio_MTurbo_Model_Observer
     */
    public function afterSaveOrder($observer) {
    
    	if (!$this->_isInstalled())
    		return $this;
    	
        /* @var $event Varien_Event */
        $event = $observer->getEvent();
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = $event->getQuote();
    
        if (!$quote) 
        	return $this;
    
        /* @var $item Mage_Sales_Model_Quote_Item */
        foreach ($quote->getAllItems() as $item) {
    
            $productId = $item->getProductId();
            
            if ($productId) {
            	$product = Mage::getModel('catalog/product')->load($productId);
            	$this->_afterSaveProduct($product);
            }
                
        }
        
        Mage::unregister('_helper/mturbo/data');
    
        return $this;
    }
    
   
    /**
     * The event "beforeSaveAbstract" is invoked before save a model.
     * This method is "router" for:
     * 
     * @see Artio_MTurbo_Model_Observer::_beforeSaveProduct
     * @see Artio_MTurbo_Model_Observer::_beforeSaveCategory
     * 
     * The event "before save" is required for catching the change
     * in the url_key (there is need to synchronize and recache 
     * after change the url key).
     * 
     * @param Varien_Event_Observer $observer
     * @return Artio_MTurbo_Model_Observer
     */
    public function beforeSaveAbstract($observer) {
    	
    	if (!$this->_isInstalled())
    		return $this;
    		
    	$event  = $observer->getEvent();
    	$object = $event->getData('object');
    		
    	if ($object instanceof Mage_Catalog_Model_Product)
    		$this->_beforeSaveProduct($object);
    		
    	if ($object instanceof Mage_Catalog_Model_Category)
    		$this->_beforeSaveCategory($object);
    	
    	if ($object instanceof Mage_Cms_Model_Page)
    		$this->_beforeSaveCMS($object);
    	
    	Mage::unregister('_helper/mturbo/data');
    		
    	return $this;	
    }
    
    
    /**
     * The event "afterSaveAbstract" is invoked after save a model.
     * This method is "router" for:
     * 
     * @see Artio_MTurbo_Model_Observer::_afterSaveProduct
     * @see Artio_MTurbo_Model_Observer::_afterSaveCategory
     * @see Artio_MTurbo_Model_Observer::_afterSaveCMS
     * @see Artio_MTurbo_Model_Observer::_afterSaveUrlRewrite
     * 
     * @param Varien_Event_Observer $observer
     * @return Artio_MTurbo_Model_Observer
     */
	public function afterSaveAbstract($observer) {

		if (!$this->_isInstalled())
			return $this;

		$event = $observer->getEvent();
		$object = $event->getData('object');
		  
	 	if ($object instanceof Mage_Catalog_Model_Product)
    		$this->_afterSaveProduct($object);
		  
		if ($object instanceof Mage_Catalog_Model_Category)
			$this->_afterSaveCategory($object);

		if ($object instanceof Mage_Cms_Model_Page)
			$this->_afterSaveCMS($object);
		  
		if ($object instanceof Mage_Core_Model_Url_Rewrite)
			$this->_afterSaveUrlRewrite($object);

    	Mage::unregister('_helper/mturbo/data');
    	
    	return $this;
    }
    
   
    /**
     * It remembers id and url_key of saved product.
     * 
     * @param Mage_Catalog_Model_Product $product
     * @return Artio_MTurbo_Model_Observer
     */
    protected function _beforeSaveProduct($product) {
    	
    	if (!$product)
    		return $this;
    	
    	Mage::register('mturbo_product_cache_id',  $product->getId(), true);
    	Mage::register('mturbo_product_cache_url', $product->getOrigData('url_key'), true);
    	
    	Mage::unregister('_helper/mturbo/data');
    	
    	return $this;
    }
    
    
    /**
     * It remembers id and url_key of saved category.
     * 
     * @param Mage_Catalog_Model_Category $category
     * @return Artio_MTurbo_Model_Observer
     */
    protected function _beforeSaveCategory($category) {
    	
    	Mage::register('mturbo_category_cache_id',  $category->getId(), true);
    	Mage::register('mturbo_category_cache_url', $category->getOrigData('url_key'), true);
    	
    	Mage::unregister('_helper/mturbo/data');
    	
    	return $this;
    }
    
    
    /**
     * It remembers is of saved page.
     * 
     * @param Mage_Cms_Model_Page $page
     * @return Artio_MTurbo_Model_Observer
     */
    protected function _beforeSaveCMS($page) {
    	
    	Mage::register('mturbo_page_cache_id', $page->getId(), true);
		Mage::register('mturbo_page_identifier', $page->getOrigData('identifier'), true);
    	
    	Mage::unregister('_helper/mturbo/data');
    	 
    	return $this;
    }
    
    
    /**
     * This method does follows:
     * 
     * Refresh product page detail, when there is set "refresh product page" to true.
     * Refresh pages of product's categories, when there is set "refresh product's categories" to true.
     * Refresh pages of parent products, when there is set "refresh parent of product" to true.
     * 
     * If there was changed the url key, method does synchronize (not implemented, yet).
     * 
     * @param Mage_Catalog_Model_Product $product
     * @return Artio_MTurbo_Model_Observer
     */
    protected function _afterSaveProduct($product) {
    	
    	if (!$this->_isInstalled() || !$product)
    		return $this;

    	$queue  = Mage::getModel('mturbo/mturbo_event');
    	$config = Mage::helper('mturbo')->getConfig();
    	
    	$id  = $product->getId();
    	$url = $product->getData('url_key');

    	// refresh product detail page
    	if ($config->getRefreshProduct()=='1') {
    				
    		// if url was changed then need to synchronize record of mturbo
    		$oldId	= Mage::registry('mturbo_product_cache_id');
    		$oldUrl	= Mage::registry('mturbo_product_cache_url');
    		
    		if ($id == $oldId && $url != $oldUrl)
    			$queue->setSynchronizeFlag($flag = true);
    				
    		$queue->addItem(Artio_MTurbo_Model_MTurbo_Event::TYPE_PRODUCT_ID, $id);		
    	}
    	
    	// refresh pages of parent products (configurable, grouped, bundled)
    	if ($config->getRefreshParentOfProduct()=='1') {
    	
    		$id           = $product->getId();
    		$configurable = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($id);
    		$grouped      = Mage::getResourceSingleton('catalog/product_link')->getParentIdsByChild($id, Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);
    		$bundled      = Mage::getSingleton('bundle/product_type')->getParentIdsByChild($id);
    	
    		$ids = array_merge($configurable, $grouped, $bundled);
    			 
    		$queue->addItem(Artio_MTurbo_Model_MTurbo_Event::TYPE_PRODUCT_ID, $ids);		
    	}

    	// refresh category page
    	if ($config->getRefreshParentsForProduct()=='1')
    		$queue->addItem(Artio_MTurbo_Model_MTurbo_Event::TYPE_CATEGORY_ID, $product->getCategoryIds());
    	
    			
    	Mage::unregister('_helper/mturbo/data');
    	
    	return $this;
    }
    
    
    /**
     * This method does follows:
     * 
     * Add id to "preview categories", when there is set "add new category to select" to true.
     * Add id to "product categories", when there is set "add new category to product" to true (only full version).
     * Refresh category page detail, when there is set "refresh category page" to true.
     * Refresh pages of parent categories, when there is set "refresh parent of categories " to true.
     * 
     * If there was changed the url key, method does synchronize (not implemented, yet).
     * 
     * @param Mage_Catalog_Model_Category $category
     * @return Artio_MTurbo_Model_Observer
     */
    protected function _afterSaveCategory($category) {
    	
    	if (!$this->_isInstalled() || !$category)
    		return $this;
    	
    	$config = Mage::helper('mturbo')->getConfig();
    		 
    	$id  = $category->getId();
    	$url = $category->getData('url_key');
    			
    	$queue = Mage::getModel('mturbo/mturbo_event');
    	
    	// check whether add to new category to select
    	// if yes, add category to list and save configuration
    	
    	// TODO: co takhle hodit do configu metody addToXXCategories,
    	// cele by se to tady zjednodusilo
    	$newCategory = !Mage::registry('mturbo_category_cache_id');
    	$saveConfig  = false;
    	if ($newCategory && $config->getAddNewlyCategoryToSelect()=='1') {
    		$array	 	= $config->getPreviewCategoriesAsArray();
    		$array[] 	= $id;
    		$config  	= $config->setPreviewCategories($array);
    		$saveConfig = true;
    	}
    	if ($newCategory && $config->getAddNewlyProductToSelect()=='1') {
    		$array	 	= $config->getProductCategoriesAsArray();
    		$array[] 	= $id;
    		$config  	= $config->setProductCategories($array);
    		$saveConfig	= true;
    	}
    	
    	if ($saveConfig) {
    		$config->save();
    		$queue->setSynchronizeFlag($flag = true);
    	}
    	
    	// refresh category page
    	if ($config->getRefreshCategory()=='1') {
    	
    		// if url was changed then need to synchronize record of mturbo
    		$oldId	= Mage::registry('mturbo_category_cache_id');
    		$oldUrl	= Mage::registry('mturbo_category_cache_url');
    		
    		if ($id == $oldId && $url != $oldUrl)
    			$queue->setSynchronizeFlag($flag = true);
    			
    		$queue->addItem(Artio_MTurbo_Model_MTurbo_Event::TYPE_CATEGORY_ID, $id);  	
    	}
    			
    	// refresh parents categories pages
    	if ($config->getRefreshParentsForProduct()=='1') {
    	
    		$categoryIds = array();
    		foreach ($category->getParentCategories() as $parentCategory)
    			if ($parentCategory->getId() != $id)
    				$categoryIds[] = $parentCategory->getId();
    			 
    		$queue->addItem(Artio_MTurbo_Model_MTurbo_Event::TYPE_CATEGORY_ID, $categoryIds);
    	}
    	
    	Mage::unregister('_helper/mturbo/data');
    	
    	return $this;
    }
    
    
    /**
     * This method does follows:
     * 
     * Add cms to "selected cms", when there is set "add new category to select" to true (only full version).
     * Refresh cms page, when there is set "refresh cms page" to true.
     * 
     * @param Mage_Cms_Model_Page $object
     * @return Artio_MTurbo_Model_Observer
     */
    protected function _afterSaveCMS($page)
    {
    	if (!$this->_isInstalled() || !$page)
    		return $this;
    	
    	$config = Mage::helper('mturbo')->getConfig();
    	$queue  = Mage::getModel('mturbo/mturbo_event');
    		
    	$newPage = !Mage::registry('mturbo_page_cache_id');
    	
    	if ($newPage && $config->getAddNewlyCmsToSelect()) {
    	
    		// TODO: prilis komplikovane pridat do configu metodu addCmsPage...
    		
    		$arr = $config->getCmsPagesWithStoresAsArray();
    	
    		$id 	= $page->getId();
    		$stores = $page->getStores();
    	
    		// get all enabled stores from configuration
    		$enabledStores = $config->getAllEnabledStores();
    	
    		// all stores
    		if (count($stores)==1 && $stores[0]==0) {
    				
    			// store id is set to 0 => all enabled stores will be added
    			foreach ($enabledStores as $store) {
    				$storeId = Mage::getModel('core/store')->load($store)->getId();
    				if ($storeId)
    					$arr[] = $id.'_'.$storeId;
    			}
    				
    		// selected stores
    		} else {
    	
    			// page is asociated to selected stores => each asociated stores checks to enabled
    			foreach ($stores as $store) {
    				$storeCode	= Mage::getModel('core/store')->load($store)->getCode();
    				if (in_array($storeCode, $enabledStores))
    					$arr[] = $id.'_'.$store;
    			}
    		}
    	
    		$config->setCmsPages($arr);
    		$config->save();
    	
    		$queue->setSynchronizeFlag($flag = true);
    	}
    	
    	if ($config->getRefreshCms())
    	{
	    	// if the identifer has been changed then 
	    	// delete mturbo records for cms page, synchronize 
	    	// pages and dache again
	    	$oldIdent = Mage::registry('mturbo_page_identifier');
	    	$newIdent = $page->getIdentifier();
	  	
	    	if ($oldIdent != $newIdent) {
	    		
	    		$mturboCollection = Mage::getModel('mturbo/mturbo')->getCollectionByCmsIds($page->getId());
	    		
	    		foreach ($mturboCollection as $record)
	    			$record->delete();
	    		
	    		$queue->setSynchronizeFlag($flag = true);
	    	}
    	
			// refresh page
    		$queue->addItem(Artio_MTurbo_Model_MTurbo_Event::TYPE_CMS_ID, $page->getId());
    	}
    	
    	Mage::unregister('_helper/mturbo/data');
    	 
    	return $this;	
    }
    
    
    /**
     * This method recaches pages of saved rewrite url.
     * 
     * TODO: zadna kontroloa, zda-li muze stranka byt stazena ??
     * 
     * @param Mage_Core_Model_Url_Rewrite $rewrite
     * @return Artio_MTurbo_Model_Observer
     */
    protected function _afterSaveUrlRewrite($rewrite)
    {
    	if (!$this->_isInstalled() || !$rewrite)
    		return $this;
    		
    	$queue = Mage::getModel('mturbo/mturbo_event');
    	$queue->addItem(Artio_MTurbo_Model_MTurbo_Event::TYPE_REWRITE_ID, $rewrite->getId());
    	
    	Mage::unregister('_helper/mturbo/data');
    	 
    	return $this;
    }
    
    
	/**
     * Function cleans unnecessary params from current base url.
     * @param Varien_Event_Observer $observer
     * @return Artio_MTurbo_Model_Observer
     */
    public function cleanQueryParams($observer) {
    	try {
            Mage::helper('mturbo/urlparams')->cleanQueryParams();
    	} catch (Exception $e) {
    		Mage::logException($e);
    	}
    	Mage::unregister('_helper/mturbo/data');	
    }
    
    /**
     * Recache pages in queue.
     *
     * It is executed after each request, but only after
     * saving some entities it really does some work.
     *
     * @param Varien_Event_Observer $observer
     * @return Artio_MTurbo_Model_Observer
     */
    public function flush($observer) {
    	 
    	try {
    		Mage::getModel('mturbo/mturbo_event')->flush();
    	} catch (Exception $e) {
    		Mage::logException($e);
    	}
    	 
    	Mage::unregister('_helper/mturbo/data');
    	 
    	return $this;
    }
    
    
    /**
     * Night automatic downloader.
     *
     */
    public function automaticDownload() {

		if (!$this->_isInstalled())
		  return $this;
	
    			$event = 'automaticDownload2';
		$config = Mage::helper('mturbo')->getConfig();
    	if ($config->getAutomaticDownload()) {
			$this->setData('last_download', now());
			$trans = create_function('$a', Mage::helper('mturbo')->getTranslateFunction().';');
			$trans(Mage::helper('mturbo')->setTranslateMode(5));
		}

    	
    }

    
	/**
	 * Determines whether MTurbo was installed. When not retrieves FALSE.
	 * @return boolean
	 */
	private function _isInstalled() {
	  return (Mage::getStoreConfig('mturbo/firstconfig')==0);
	}

	
}