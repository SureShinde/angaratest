<?php

class Angara_Massredirect_Model_Redirector
{
	public function observe($observer) 
	{
		$request = $observer['controller_action']->getRequest();
		$actionName = $request->getActionName();
		$requestUrl = $request->getRequestUri();
		
		//	Code Added by Vaseem to redirect to 404 page request by Nandu jee starts
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		//echo '<br>currentUrl->'.$currentUrl;
		$notFoundURLs	=	explode(',',Mage::helper('function')->getNotFoundURL());
		//echo '<pre>notFoundURLs->'; print_r($notFoundURLs);
		
		$notFoundURLs=array_map('trim',$notFoundURLs);			//	Removing space from array values
		//echo '<pre>notFoundURLs->'; print_r($notFoundURLs);
		
		/*if(in_array($currentUrl,$notFoundURLs)){
			ob_start();
			header("Location: http://sandbox.angara.com/no-route.html", true, 404);
			ob_end_flush();exit;
		}*/
		//	Code Added by Vaseem to redirect to 404 page request by Nandu jee ends
		
		/*if ($actionName == 'noRoute') {
			$requestUrl = substr($requestUrl, 1);
			$mappings  = file(Mage::getModuleDir('etc', 'Angara_Massredirect').'/redirects.csv');
			foreach ($mappings AS $mapping) {
				$pieces = explode(';',$mapping);
				
				$sourceUrl 	= trim($pieces[0]);
				
				if (count($pieces) == 2) {
					$destinationUrl = Mage::getbaseUrl() . trim($pieces[1]);
					if ($sourceUrl == $requestUrl) {
						$response = Mage::app()->getResponse();
						$response->setRedirect($destinationUrl, 301);
						$response->sendResponse();
						exit;
					}
					continue;
				}
				
				$type 		= trim($pieces[1]);
				$entityId 	= trim($pieces[2]);
				$websiteId 	= trim($pieces[3]);
				$storeId 	= trim($pieces[4]);
				
				if ($sourceUrl == $requestUrl) {
					if ($type == 'product') {
						$destinationUrl = $this->getProductUrl($entityId, $websiteId, $storeId);
					} elseif ($type == 'category') {
						$destinationUrl = $this->getCategoryUrl($entityId, $websiteId, $storeId);
					}
					
					$response = Mage::app()->getResponse();
					$response->setRedirect($destinationUrl, 301);
					$response->sendResponse();
					exit;
				}
			}
		}*/
	}

	public function getProductUrl($entityId, $websiteId, $storeId) {
		Mage::app()->getWebsite()->setId($websiteId);
		Mage::app()->getStore()->setId($storeId);
		
		$product = new Mage_Catalog_Model_Product();
		$product->load($entityId);
		
		return $product->getUrlPath();
	}

	public function getCategoryUrl($entityId, $websiteId, $storeId) {
		Mage::app()->getWebsite()->setId($websiteId);
		Mage::app()->getStore()->setId($storeId);
		
		$category = new Mage_Catalog_Model_Category();
		$category->load($entityId);
	
		return $category->getUrlPath();
	}

}