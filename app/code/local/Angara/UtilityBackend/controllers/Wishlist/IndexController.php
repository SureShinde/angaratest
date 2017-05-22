<?php
/**
 * Rewrite by Asheesh
 */ 

require_once(Mage::getModuleDir('controllers','Mage_Wishlist').DS.'IndexController.php');
class Angara_UtilityBackend_Wishlist_IndexController extends Mage_Wishlist_IndexController
{
	
	public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_skipAuthentication) {
			//	S:VA
			$request	=	$this->getRequest()->getParams();
			if($request['c']==1){
				$check	=	Mage::getSingleton('customer/session')->authenticate($this, Mage::getUrl('customer/account/create', array('_current' => true)))	;
			}else{
				$check	=	Mage::getSingleton('customer/session')->authenticate($this);
			}
			if(!$check){
				$this->setFlag('', 'no-dispatch', true);
				if (!Mage::getSingleton('customer/session')->getBeforeWishlistUrl()) {
					Mage::getSingleton('customer/session')->setBeforeWishlistUrl($this->_getRefererUrl());
				}
				Mage::getSingleton('customer/session')->setBeforeWishlistRequest($this->getRequest()->getParams());
			}
        }
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $this->norouteAction();
            return;
        }
    }
	
	/**
     * Adding new item
     */
    public function addAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $session = Mage::getSingleton('customer/session');

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_redirect('*/');
            return;
        }

        try {
            $requestParams = $this->getRequest()->getParams();
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }
            $buyRequest = new Varien_Object($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                Mage::throwException($result);
            }
            $wishlist->save();

            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist'  => $wishlist,
                    'product'   => $product,
                    'item'      => $result
                )
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been added to your wishlist. ', $product->getName(), Mage::helper('core')->escapeUrl($referer));
            //$session->addSuccess($message);
			Mage::getSingleton('core/session')->addSuccess($message);
			Mage::getSingleton('core/session')->setWishlistProductSku($product->getSku()); // to set wishlist product sku for fb pixel
			header('Location:'.$referer);exit;
			//$this->_redirect($referer);
        }
        catch (Mage_Core_Exception $e) {
            $session->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
        }
        catch (Exception $e) {
            $session->addError($this->__('An error occurred while adding item to wishlist.'));
        }		
        $this->_redirect('*', array('wishlist_id' => $wishlist->getId()));
    }
	
	    /**
     * Remove item
     */
    public function removeAction()
    {
        $id = (int) $this->getRequest()->getParam('item');
        $item = Mage::getModel('wishlist/item')->load($id);
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		$session = Mage::getSingleton('customer/session');
        if (!$item->getId()) {
            return $this->norouteAction();
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            return $this->norouteAction();
        }
        try {
            $item->delete();
            $wishlist->save();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('customer/session')->addError(
                $this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage())
            );
        } catch(Exception $e) {
            Mage::getSingleton('customer/session')->addError(
                $this->__('An error occurred while deleting the item from wishlist.')
            );
        }

        Mage::helper('wishlist')->calculate();
		
		$message = $this->__('%1$s has been removed from your wishlist.', $product->getName());
        $session->addSuccess($message);

        $this->_redirectReferer(Mage::getUrl('*/*'));
    }
	
	/**
     * Add cart item to wishlist and remove from cart
     */
    public function fromcartAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var Mage_Checkout_Model_Cart $cart */
        $cart = Mage::getSingleton('checkout/cart');
        $session = Mage::getSingleton('checkout/session');

        try{
            $item = $cart->getQuote()->getItemById($itemId);
            if (!$item) {
                Mage::throwException(
                    Mage::helper('wishlist')->__("Requested cart item doesn't exist")
                );
            }

            $productId  = $item->getProductId();
            $buyRequest = $item->getBuyRequest();

            $wishlist->addNewItem($productId, $buyRequest);

            $productIds[] = $productId;
            
			//	S:VA	Preventing cart item getting removed from cart after adding them to wishlist
            //$cart->getQuote()->removeItem($itemId);
            //$cart->save();
						
            Mage::helper('wishlist')->calculate();
            $productName = Mage::helper('core')->escapeHtml($item->getProduct()->getName());
            $wishlistName = Mage::helper('core')->escapeHtml($wishlist->getName());
            $session->addSuccess(
                Mage::helper('wishlist')->__("%s has been moved to wishlist %s", $productName, $wishlistName)
            );
            $wishlist->save();
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, Mage::helper('wishlist')->__('Cannot move item to wishlist'));
        }

        return $this->_redirectUrl(Mage::helper('checkout/cart')->getCartUrl());
    }

}
