<?php
class Angara_Feedback_IndexController extends Mage_Core_Controller_Front_Action{
	
    public function IndexAction() {
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("titlename", array(
                "label" => $this->__("Titlename"),
                "title" => $this->__("Titlename")
		   ));

      $this->renderLayout(); 
    }
	
	
	/*
		Show the feedback form on ajax request
	*/
	public function AjaxAction() {
		$this->loadLayout();   
		$this->renderLayout(); 
	}
	
	
	/*
		Process the feedback form data on ajax form submit
	*/
	public function AjaxPostAction() {
		$params = 	$this->getRequest()->getParams();
		$model 	= 	Mage::getModel('feedback/feedback');
		$model->setData('email', 			$params['email']);
		$model->setData('contact_number', 	$params['contact_number']);
		$model->setData('category_id', 		$params['category_id']);
		$model->setData('message', 			$params['message']);

		try {
			$model->save();
			echo 'Thank you for your feedback :)';
		} catch (Exception $e) {
			Mage::log($e->getMessage());
			echo 'Sorry! There is some issue submiting the feedback.';
		}
	}
}