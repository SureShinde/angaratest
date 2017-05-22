<?php
class Angara_Wishlist_IndexController extends Mage_Core_Controller_Front_Action {
	
	/*
		S:VA	Login or Register Ajax Popup
	*/
	public function loginAction() {
		$this->loadLayout();
		$this->renderLayout();
    }
}