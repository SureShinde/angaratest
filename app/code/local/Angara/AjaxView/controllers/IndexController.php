<?php	
class Angara_AjaxView_IndexController extends Mage_Core_Controller_Front_Action{
    
	public function indexAction() {
		$related_products	=	$this->getRequest()->getParam('related_products');
		$productId 			= 	$this->getRequest()->getParam('pid');
		if( $productId && count ($related_products) > 0 ) {
			Mage::register('related_products', $related_products);		//	Register product id so that we can use it on Block/Index.php
		}
		$this->loadLayout();
		$this->renderLayout();
		//Mage::unregister('related_products');
    }
	
	
	/*
		S:VA
		returns the html of cart items
	*/
	public function cartSummaryAction() {

		$sidecart = Mage::app()->getLayout()->createBlock('checkout/cart_sidebar','cart_sidebar');
		$sidecart->addItemRender('simple','checkout/cart_item_renderer','checkout/cart/item/default-opc.phtml');
		$sidecart->addItemRender('grouped','checkout/cart_item_renderer_grouped','checkout/cart/item/default-opc.phtml');
		$sidecart->addItemRender('configurable','checkout/cart_item_renderer_configurable','checkout/cart/item/default-opc.phtml');
		foreach($sidecart->getItems() as $_item){
			$html .= $sidecart->getItemHtml($_item);
		}
		echo $html;
    }
	
	
	/*
		S:VA
		returns the html of cart subtotal, promo code, tax, shipping and order total
	*/
	public function cartReviewAction() {
		$this->loadLayout(false);
		$this->renderLayout();
    }
	
}