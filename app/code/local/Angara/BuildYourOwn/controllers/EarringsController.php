<?php
class Angara_BuildYourOwn_EarringsController extends Mage_Core_Controller_Front_Action
{
	private $_categoryId = 461;
	public function indexAction(){
		$byoModel = Mage::getModel('buildyourown/jewelry_earrings');
		$userStartWithPreference = ($byoModel->getUserStartWithPreference() == 'setting');
		if(!$byoModel->hasDiamondSelected() && !$byoModel->hasSettingSelected()){
			if($userStartWithPreference){
				$this->_forward('settings');
			}
			else{
				$this->_forward('diamondPairs');
			}
		}
		else if($byoModel->hasDiamondPairSelected() && !$byoModel->hasSettingSelected()){
			$this->_forward('settings');
		}
		else if($byoModel->hasSettingSelected() && !$byoModel->hasDiamondPairSelected()){
			$this->_forward('diamondPairs');
		}
		else{
			$this->_forward('review');
		}
	}
	
	public function filterDiamondsAction(){
		$filterParams = $this->getRequest()->getParam('userFilters');
		$ringModel = Mage::getModel('buildyourown/jewelry_earrings')->setUserFilters($filterParams);
		$this->_forward('diamondPairGrid');
	}
		
	public function selectDiamondPairAction(){
		$diamondIds = $this->getRequest()->getParam('diamondIds');
		if($diamondIds){
			$diamondIds = explode(',',$diamondIds);
		}
		Mage::getModel('buildyourown/jewelry_earrings')->selectDiamondPair($diamondIds);
		$this->_forward('index');
	}
		
	public function removeDiamondPairAction(){
		Mage::getModel('buildyourown/jewelry_earrings')->selectDiamondPair(false);
		$this->_forward('index');
	}
	
	public function removeSettingAction(){
		Mage::getModel('buildyourown/jewelry_earrings')->selectSetting(false);
		$this->_forward('index');
	}
	
	public function diamondPairsAction(){
		$byoJewelryModel = Mage::getModel('buildyourown/jewelry_earrings');
		Mage::register('byoJewelryModel', $byoJewelryModel);
		$this->loadLayout();
		$this->getLayout()->getBlock("head")->setTitle($this->__("Build Your Own Jewelry"));
		/*$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
			"label" => $this->__("Home Page"),
			"title" => $this->__("Home Page"),
			"link"  => Mage::getBaseUrl()
		));

      	$breadcrumbs->addCrumb("build your own earrings", array("label" => $this->__("Build Your Own Earrings"),"title" => $this->__("Build Your Own Earrings")));*/
      	$this->renderLayout();
	}
	
	public function diamondPairGridAction(){
		$page = (int)$this->getRequest()->getParam('page', 1);
		$sortBy = $this->getRequest()->getParam('sidx');
		$sortDir = $this->getRequest()->getParam('sord');
		$ringModel = Mage::getModel('buildyourown/jewelry_earrings')->setUserFilters(array('sortBy' => $sortBy, 'sortDir' => $sortDir));
		
		$diamondsFeed = $ringModel->getDiamondFeed($page);
		if($diamondsFeed){
			$result = array();
			$result['page'] = $page;
			$result['total'] = ceil($diamondsFeed['search_results']['total_diamonds_found'] / 50);
			$result['records'] = $diamondsFeed['search_results']['total_diamonds_found'];
			$diamonds = $diamondsFeed['diamonds'];
			foreach($diamonds as $iCounter => $diamond){
				$result['rows'][$iCounter]['id'] = $diamond['diamond_id'];
				$result['rows'][$iCounter]['cell'] = array($diamond['diamond_id'],$diamond['shape'],(($diamond['size'] == '') ? 'NA' : $diamond['size']),(($diamond['color'] == '') ? 'NA' : $diamond['color']),(($diamond['clarity'] == '') ? 'NA' : $diamond['clarity']),(($diamond['cut'] == '') ? 'NA' : $diamond['cut']),Mage::helper('core')->currency($diamond['total_sales_price_in_currency'], true, false));
			}
			echo json_encode($result);
		}
	}
	
	public function settingsAction(){
		$_category = Mage::getModel('catalog/category')->load($this->_categoryId);
		$redirect = $_category->getUrl($_category);
		$this->getResponse()->setRedirect($redirect);
	}
	
	public function reviewAction(){
		$setting = Mage::getModel('buildyourown/jewelry_earrings')->getSettingSelected();
		if($setting['productId']){
			$product = Mage::getModel('catalog/product')->load($setting['productId']);
			if($product->getId()){
				$this->getResponse()->setRedirect('/catalog/product/view/id/'.$product->getId());
			}
			else{
				$this->_forward('settings');
			}
		}
		else{
			$this->_forward('settings');
		}
	}
}