<?php
/* Need to work. Not Completed */
class Angara_BuildYourOwn_ThreeStoneRingController extends Mage_Core_Controller_Front_Action
{
	private $_categoryId = 463;
	public function indexAction(){
		$byoModel = Mage::getModel('buildyourown/jewelry_threeStoneRing');
		$userStartWithPreference = ($byoModel->getUserStartWithPreference() == 'setting');
		if(!$byoModel->hasDiamondSelected() && !$byoModel->hasSettingSelected()){
			if($userStartWithPreference){
				$this->_forward('settings');
			}
			else{
				$this->_forward('diamondGrid');
			}
		}
		else if($byoModel->hasDiamondSelected() && !$byoModel->hasDiamondPairSelected()){
			$this->_forward('diamondPairGrid');
		}
		else if($byoModel->hasDiamondSelected() && !$byoModel->hasSettingSelected()){
			$this->_forward('settings');
		}
		else if($byoModel->hasSettingSelected() && !$byoModel->hasDiamondSelected()){
			$this->_forward('diamonds');
		}
		else{
			$this->_forward('review');
		}
	}
	
	public function filterDiamondsAction(){
		$filterParams = $this->getRequest()->getParam('userFilters');
		$ringModel = Mage::getModel('buildyourown/jewelry_threeStoneRing')->setUserFilters($filterParams);
		$this->_forward('diamondGrid');
	}
	
	public function selectDiamondAction(){
		$diamondId = $this->getRequest()->getParam('diamondId');
		Mage::getModel('buildyourown/jewelry_threeStoneRing')->selectDiamond($diamondId);
		$this->_forward('index');
	}
	
	public function selectDiamondPairAction(){
		$diamondIds = $this->getRequest()->getParam('diamondIds');
		if($diamondIds){
			$diamondIds = explode(',',$diamondIds);
		}
		Mage::getModel('buildyourown/jewelry_threeStoneRing')->selectDiamondPair($diamondIds);
		$this->_forward('index');
	}
	
	public function removeDiamondAction(){
		Mage::getModel('buildyourown/jewelry_threeStoneRing')->selectDiamond(false);
		$this->_forward('index');
	}
	
	public function removeDiamondPairAction(){
		Mage::getModel('buildyourown/jewelry_threeStoneRing')->selectDiamondPair(false);
		$this->_forward('index');
	}
	
	public function removeSettingAction(){
		Mage::getModel('buildyourown/jewelry_threeStoneRing')->selectSetting(false);
		$this->_forward('index');
	}
	
	public function diamondsAction(){
		$byoJewelryModel = Mage::getModel('buildyourown/jewelry_threeStoneRing');
		Mage::register('byoJewelryModel', $byoJewelryModel);
		$this->loadLayout();
		$this->getLayout()->getBlock("head")->setTitle($this->__("Build Your Own Jewelry"));
		/*$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
			"label" => $this->__("Home Page"),
			"title" => $this->__("Home Page"),
			"link"  => Mage::getBaseUrl()
		));

      	$breadcrumbs->addCrumb("build your own ring", array("label" => $this->__("Build Your Own Ring"),"title" => $this->__("Build Your Own Ring")));*/
      	$this->renderLayout();
	}
	
	public function diamondGridAction(){
		$page = (int)$this->getRequest()->getParam('page', 1);
		$sortBy = $this->getRequest()->getParam('sidx');
		$sortDir = $this->getRequest()->getParam('sord');
		$ringModel = Mage::getModel('buildyourown/jewelry_threeStoneRing')->setUserFilters(array('sortBy' => $sortBy, 'sortDir' => $sortDir));
		
		$diamondsFeed = $ringModel->getDiamondFeed($page);
		if($diamondsFeed){
			$result = array();
			$result['page'] = $page;
			$result['total'] = ceil($diamondsFeed['search_results']['total_diamonds_found'] / 50);
			$result['records'] = $diamondsFeed['search_results']['total_diamonds_found'];
			$diamonds = $diamondsFeed['diamonds'];
			foreach($diamonds as $iCounter => $diamond){
				$result['rows'][$iCounter]['id'] = $diamond['diamond_id'];
				//$result['rows'][$iCounter]['cell'] = array($diamond['diamond_id'],$diamond['shape'],$diamond['size'],$diamond['color'],$diamond['clarity'],$diamond['cut'],Mage::helper('core')->currency($diamond['total_sales_price_in_currency'], true, false));
				$result['rows'][$iCounter]['cell'] = array($diamond['diamond_id'],$diamond['shape'],(($diamond['size'] == '') ? 'NA' : $diamond['size']),(($diamond['color'] == '') ? 'NA' : $diamond['color']),(($diamond['clarity'] == '') ? 'NA' : $diamond['clarity']),(($diamond['cut'] == '') ? 'NA' : $diamond['cut']),Mage::helper('core')->currency($diamond['total_sales_price_in_currency'], true, false));
			}
			echo json_encode($result);
		}
	}
	
	public function diamondPairGridAction(){
		$page = (int)$this->getRequest()->getParam('page', 1);
		$sortBy = $this->getRequest()->getParam('sidx');
		$sortDir = $this->getRequest()->getParam('sord');
		$ringModel = Mage::getModel('buildyourown/jewelry_threeStoneRing')->setUserFilters(array('sortBy' => $sortBy, 'sortDir' => $sortDir));
		
		$diamondsFeed = $ringModel->getDiamondFeed($page);
		if($diamondsFeed){
			$result = array();
			$result['page'] = $page;
			$result['total'] = ceil($diamondsFeed['search_results']['total_diamonds_found'] / 50);
			$result['records'] = $diamondsFeed['search_results']['total_diamonds_found'];
			$diamonds = $diamondsFeed['diamonds'];
			foreach($diamonds as $iCounter => $diamond){
				$result['rows'][$iCounter]['id'] = $diamond['diamond_id'];
				$result['rows'][$iCounter]['cell'] = array($diamond['diamond_id'],$diamond['shape'],$diamond['size'],$diamond['color'],$diamond['clarity'],$diamond['cut'],Mage::helper('core')->currency($diamond['total_sales_price_in_currency'], true, false));
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
		$setting = Mage::getModel('buildyourown/jewelry_threeStoneRing')->getSettingSelected();
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