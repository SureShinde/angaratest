<?php
class Angara_Testimonials_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		// Code modified by Vaseem to redirect this url issue reported by Nandu Ji - http://www.angara.com/testimonials
		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		$checkURL	=	explode('/',$currentUrl);
		if(in_array('testimonials',$checkURL)){
			$redirectURL	=	substr(Mage::getUrl('customer-testimonials.html'),0,-1);
			Mage::app()->getFrontController()->getResponse()->setRedirect($redirectURL);
		}
		else{
			$page=$this->getRequest()->getParams();
			$this->loadLayout();
			$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
			$this->renderLayout();
		}
	}
	
    public function reviewModelAction() {
		$params = $this->getRequest()->getParams();
		$testimonial = Mage::getModel('testimonials/reviewdetail');
    }	
	
	//	Added by Vaseem to show product reviews based on category starts
	//	A public url created for - http://192.168.1.162/testimonials/index/category/
	public function categoryAction() {
		$page=$this->getRequest()->getParams();
	    $this->loadLayout();

		$headBlock = $this->getLayout()->getBlock('head');
		$headBlock->setTitle('Angara Jewelry Reviews, Jewelry Testimonials');
		$headBlock->setKeywords('Angara Jewelry Reviews, Jewelry Testimonials, Rings Reviews, Earrings Reviews, Pendants Reviews, Necklaces Reviews, Gemstone Jewelry Reviews, Diamond Jewelry Reviews, Engagement Rings Reviews');
		$headBlock->setDescription('Angara Jewelry reviews. Read Angara.com customer reviews on gemstone jewelry, diamond jewelry, engagement rings and customer service.');

		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
	    $this->renderLayout();
	}
	
	public function moreReviewsAction() {
		$categoryId = $this->getRequest()->getParam('id');
		if($categoryId) {
			Mage::register('review_category_id',$categoryId ,false);		//	Register category id so that we can use it on Block/Index.php
			$this->loadLayout();
			
			$category = Mage::getModel('catalog/category')->load($categoryId);
			
			$testimonial_title		=	$category->gettestimonialTitle();
			$testimonial_keyword	=	$category->gettestimonialKeyword();
			$testimonial_description=	$category->gettestimonialDescription();
			
			$headBlock = $this->getLayout()->getBlock('head');
			$headBlock->setTitle($testimonial_title);
			$headBlock->setKeywords($testimonial_keyword);
			$headBlock->setDescription($testimonial_description);
			
			$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
			$this->renderLayout();
			Mage::unregister('review_category_id');
		}
	}
	
	public function getcustomertestimonialAction(){
		echo $this->getLayout()->createBlock('testimonials/items')->setTemplate('homepage/category_testimonials.phtml')->toHTML();
	}
}?>