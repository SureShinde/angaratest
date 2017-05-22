<?php
/*
	S:VA	controllers rewrite	
*/
require_once(Mage::getModuleDir('controllers','Mage_Catalog').DS.'ProductController.php');
class Angara_UtilityBackend_Catalog_ProductController extends Mage_Catalog_ProductController
{
    /**
     * Product view action
     */
    public function viewAction()
    {
		/* $model = Mage::getModel('sales/order_item');
		echo get_class($model);die; */
        // Get initial data from request
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');
		
		// Added by Hitesh to record recently viewed products
		Mage::getBlockSingleton('recentlyviewed/collect')->addItem($productId);

        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                // Angara Modification Start
				if(!empty($productId)){
					//	S:Code added by Vaseem to redirect disable product to master category
					$product 		= 	Mage::getModel('catalog/product')->load($productId);	//	load product collection by product id
					//	Check if master_category defined for this product
					$masterCategory	=	$product->getmasterCategory();
					if($masterCategory){
						$_category 		= 	Mage::getModel('catalog/category')->loadByAttribute('name', $masterCategory);
						//echo '<pre>'; print_r($_category);die;	
						$categoryURL		=	$_category['url_path'];
						//echo 'categoryURL->'.$categoryURL;
					}
					if($categoryURL){
						Mage::app()->getFrontController()->getResponse()->setRedirect('/'.$categoryURL);
					}else{
						Mage::app()->getFrontController()->getResponse()->setRedirect('/jewelry/gemstone-jewelry.html');
					}
					//	E:Code added by Vaseem to redirect disable product to master category
				}
				// Angara Modification End
				if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }

	
	// Angara Modification Start
	public function getFinalShipmentDateAction()
	{
		$pid = $_REQUEST['id'];
		$prod_detail = Mage::getModel('catalog/product')->load($pid);
		$j=1;
		if($prod_detail->getVendorLeadTime())$j=$prod_detail->getVendorLeadTime();
		
		if(date("w",strtotime("+".$j." weekdays"))==0)
			echo date('l\, F j',strtotime("-2 days", strtotime("+".$j." weekdays"))).'<br/>';
		else 
			echo date('l\, F j',strtotime("+".$j." weekdays"));
		
	}
	
	public function setRedirectSessionUrlAction()
	{
		$urlval = $_REQUEST['wishurl'];
		//$urlval = 'http://localhost/wishlist/index/add/product/7237/';
		$wishlist_session = Mage::getSingleton('wishlist/session');
		$wishlist_session->setAddWishlistUrl($urlval);		
	}
	
	public function getsimilaritemsAction()
	{
		echo $this->getLayout()->createBlock('catalog/product_list_related')->setTemplate('catalog/product/list/related.phtml')->toHTML();
	}
	// Angara Modification End
	
	public function getProductDetailsUpdateAction()
	{
		$productId = (int) $this->getRequest()->getParam('product_id');
		$categoryIds = $this->getRequest()->getParam('category_ids');
		if(!is_array($categoryIds)){
			$categoryIds = explode(',', $categoryIds);
		}
		if($productId){
			$productObj	= Mage::getModel('catalog/product')->load($productId);
			if($productObj){				
				$productDescription = $productObj->getDescription();
				if(in_array('482', $categoryIds) || in_array('483', $categoryIds) || in_array('484', $categoryIds)){
					$productName = $productObj->getShortDescription();
					$productDetails = array('name' => $productName,'description' => $productDescription);
				}
				else{
					$productDetails = array('description' => $productDescription);
				}				
			}
			echo json_encode($productDetails);
		}
	}
}
