<?php
class Angara_Crossreviews_IndexController extends Mage_Core_Controller_Front_Action {        
	
	public function indexAction() {
		try{
			$params = $this->getRequest()->getParams();
			$this->loadLayout();
			if(!empty($params['id']))
				echo $this->getLayout()->getBlock('cross')->assign('product_id', $params['id'])->toHtml();
		}
		catch(Exception $e){
			echo "Error fetching reviews.";
		}
	}
	
	public function stonereviewsAction(){
		try{
			$params = $this->getRequest()->getParams();
			$this->loadLayout();
			if(!empty($params['id']) && !empty($params['quality']))
				$stone = Mage::getModel('crossreviews/productvariation')->getProductStone($params['id']);
				if($stone)
					echo $this->getLayout()->getBlock('stonereviews')->assign('product_id', $params['id'])->assign('stone', $stone)->assign('quality', $params['quality'])->toHtml();
		}
		catch(Exception $e){
			echo "Error fetching reviews.";
		}
	}
}
?>