<?php
/*
	S:VA	controllers rewrite	
*/
require_once(Mage::getModuleDir('controllers','Mage_Catalog').DS.'CategoryController.php');
class Angara_UtilityBackend_Catalog_CategoryController extends Mage_Catalog_CategoryController
{
    /**
     * Category view action
     */
    public function viewAction()
    {
        if ($category = $this->_initCatagory()) {
			if($category->getId() == 470){	// build your own ring category hardcoded for faster performance
				$byoJewelryModel = Mage::getModel('buildyourown/jewelry_ring');
				Mage::register('byoJewelryModel', $byoJewelryModel);
			}
			else if($category->getId() == 461){	// build your own earrings category hardcoded for faster performance
				$byoJewelryModel = Mage::getModel('buildyourown/jewelry_earrings');
				Mage::register('byoJewelryModel', $byoJewelryModel);
			}
			else if($category->getId() == 469){	// build your own pendant category hardcoded for faster performance
				$byoJewelryModel = Mage::getModel('buildyourown/jewelry_pendant');
				Mage::register('byoJewelryModel', $byoJewelryModel);
			}
			else if($category->getId() == 463){	// build your own threeStoneRing category hardcoded for faster performance
				$byoJewelryModel = Mage::getModel('buildyourown/jewelry_threeStoneRing');
				Mage::register('byoJewelryModel', $byoJewelryModel);
			}
			
            $design = Mage::getSingleton('catalog/design');
            $settings = $design->getDesignSettings($category);

            // apply custom design
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }

            Mage::getSingleton('catalog/session')->setLastViewedCategoryId($category->getId());

            $update = $this->getLayout()->getUpdate();
            $update->addHandle('default');

            if (!$category->hasChildren()) {
                $update->addHandle('catalog_category_layered_nochildren');
            }

            $this->addActionLayoutHandles();
            $update->addHandle($category->getLayoutUpdateHandle());
            $update->addHandle('CATEGORY_' . $category->getId());
            $this->loadLayoutUpdates();

            // apply custom layout update once layout is loaded
            if ($layoutUpdates = $settings->getLayoutUpdates()) {
                if (is_array($layoutUpdates)) {
                    foreach($layoutUpdates as $layoutUpdate) {
                        $update->addUpdate($layoutUpdate);
                    }
                }
            }

            $this->generateLayoutXml()->generateLayoutBlocks();
            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $this->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
            }

            if ($root = $this->getLayout()->getBlock('root')) {
                $root->addBodyClass('categorypath-' . $category->getUrlPath())
                    ->addBodyClass('category-' . $category->getUrlKey());
            }

            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');	
            $this->renderLayout();
        }elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noRoute');
        }
    }
}
