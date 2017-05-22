<?php

require_once 'Mage/Catalog/controllers/ProductController.php';

class Angara_Wishlist_ProductController extends Mage_Catalog_ProductController {

    public function quickViewAction() {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->_redirect('/');
        }
		//	Add product to wishlist
		/*if(Mage::getSingleton('customer/session')->isLoggedIn()){
			$this->addAction();
		}else{*/
			if ($product = $this->_initProduct()) {
				$this->getResponse()
						->setBody($this->getLayout()
								->createBlock('ajax/product')
								->setProduct($product)
								->toHtml());
			} else {
				echo Mage::helper('catalog')->__('Product not found');
			}
		//}
    }
	
	/*
		creating wishlist object for individual customer thus we have to rewrite it
	*/
	protected function _getWishlist()
    {
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }
        try {
            $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,Mage::helper('wishlist')->__('Cannot create wishlist.'));
            return false;
        }
 
        return $wishlist;
    }
	
	/*
		add product to wishlist using ajax
	*/
    public function addAction()
    {
		$params = Mage::app()->getRequest()->getParams(); 
		//var_dump($params);
		/*$wishlistId	=	$params['wishlist_id'];
		if($wishlistId){
			$this->removeAction($wishlistId);
			return;
		}*/
		
        $response = array();
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $response['status'] = 'ERROR';
            $response['message'] = $this->__('Wishlist Has Been Disabled By Admin');
        }
        if(!Mage::getSingleton('customer/session')->isLoggedIn()){
            $response['status'] = 'ERROR';
            $response['message'] = $this->__('Please Login First');
        }
 
        if(empty($response)){ 
            $session = Mage::getSingleton('customer/session');
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                $response['status'] = 'ERROR';
                $response['message'] = $this->__('Unable to Create Wishlist');
            }else{
                $productId = (int) $this->getRequest()->getParam('id');
                if (!$productId) {
                    $response['status'] = 'ERROR';
                    $response['message'] = $this->__('Product Not Found');
                }else{
                    $product = Mage::getModel('catalog/product')->load($productId);
                    if (!$product->getId() || !$product->isVisibleInCatalog()) {
                        $response['status'] = 'ERROR';
                        $response['message'] = $this->__('Cannot specify product.');
                    }else{
                        try {
                            $requestParams = $this->getRequest()->getParams();
                            $buyRequest = new Varien_Object($requestParams);
 
                            $result = $wishlist->addNewItem($product, $buyRequest);
                            if (is_string($result)) {
                                Mage::throwException($result);
                            }
                            $wishlist->save();
							//echo '<pre>';print_r($result->getData());die;
							$wishlistItemId	=	 $result->getWishlistItemId();
 
                            Mage::dispatchEvent(
                                'wishlist_add_product',
                            array(
									'wishlist'  => $wishlist,
									'product'   => $product,
									'item'      => $result
								)
                            );
 
                            Mage::helper('wishlist')->calculate();
 
                            $message = $this->__('%1$s has been added to your wishlist.', $product->getName(), $referer);
                            $response['status'] = 'SUCCESS';
                            $response['message'] = $message;
							$response['wishlist_id'] = $wishlistItemId;
 
                            Mage::unregister('wishlist');
 
                            $this->loadLayout();
                            //$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                            //$sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
                            //$sidebar = $sidebar_block->toHtml();
                            //$response['toplink'] = $toplink;
                            //$response['sidebar'] = $sidebar;
                        }
                        catch (Mage_Core_Exception $e) {
                            $response['status'] = 'ERROR';
                            $response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
                        }
                        catch (Exception $e) {
                            Mage::log($e->getMessage());
                            $response['status'] = 'ERROR';
                            $response['message'] = $this->__('An error occurred while adding item to wishlist.');
                        }
                    }
                }
            }
 
        }
		//echo '<pre>';print_r($response);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
	
	
	/**
     * Remove item
     */
    public function removeAction($wishlistId){
		$wishlistId = (int) $this->getRequest()->getParam('wishlist_id');
        $item = Mage::getModel('wishlist/item')->load($wishlistId);
		//echo '<pre>';print_r($item->getData());die;
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		$session = Mage::getSingleton('customer/session');
        if (!$item->getId()) {
            return $this->norouteAction();
        }
		//echo $item->getWishlistId();die;
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            return $this->norouteAction();
        }
		$response = array();
       	
        try {
            $item->delete();
            $wishlist->save();
			$message = $this->__('%1$s has been removed from your wishlist.', $product->getName());
			$response['status'] = 'SUCCESS';
			$response['message'] = $message;
			$response['wishlist_id'] = '';
        } catch (Mage_Core_Exception $e) {
			$response['status'] = 'ERROR';
            $response['message'] = $this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage());            
        } catch(Exception $e) {
            $response['status'] = 'ERROR';
			$response['message'] = $this->__('An error occurred while deleting the item from wishlist.');
        }

        Mage::helper('wishlist')->calculate();
		
		/*$message = $this->__('%1$s has been removed from your wishlist.', $product->getName());
		$response['status'] = 'SUCCESS';
		$response['message'] = $message;*/
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;
    }
	
	
	/**
     * 		Add/Update Wishlist Comment
     */
    public function updateCommentAction($wishlistId, $comment){
		$params = $this->getRequest()->getParams();
		//echo '<pre>';print_r($params);die;
	
		$wishlist_item_id 	= 	(int) $this->getRequest()->getParam('wishlist_item_id');
		$description 		= 	(string)$this->getRequest()->getParam('comment');

		$wishlist = $this->_getWishlist();
		$response = array();
        if($wishlist_item_id) {
			$item = Mage::getModel('wishlist/item')->load($wishlist_item_id);
			//var_dump($item);
			if ($item->getWishlistId() != $wishlist->getId()) {
				continue;
			}

			// Extract new values
			/*if (!strlen($description)) {
				$description = $item->getDescription();
			}*/

			try {
				$item->setDescription($description)
					//->setQty($qty)
					->save();
					
				$wishlist->save();		// save wishlist model for setting date of last update
				Mage::helper('wishlist')->calculate();
				$response['status'] = 'SUCCESS';
			} catch (Mage_Core_Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('An error occurred while updating the description: %s', $e->getMessage());
			} catch (Exception $e) {
				Mage::log($e->getMessage());
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('An error occurred while updating the description.');
			}
        }
		//echo '<pre>';print_r($response);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        return;		
	}
	
}