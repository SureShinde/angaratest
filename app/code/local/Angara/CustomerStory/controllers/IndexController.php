<?php
class Angara_CustomerStory_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() 
	{      
		$this->loadLayout();   
		$this->getLayout()->getBlock("head")->setTitle($this->__("Share Your Story"));
	    $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home Page"),
            "title" => $this->__("Home Page"),
            "link"  => Mage::getBaseUrl()
		));

		$breadcrumbs->addCrumb("share your story", array(
            "label" => $this->__("Share Your Story"),
            "title" => $this->__("Share Your Story")
		));

		$this->renderLayout(); 	  
    }
}?>