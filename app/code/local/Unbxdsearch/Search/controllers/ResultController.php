<?php
# Controllers are not autoloaded so we will have to do it manually:
require_once 'Mage/CatalogSearch/controllers/ResultController.php';
class Unbxdsearch_Search_ResultController extends Mage_CatalogSearch_ResultController
{
    # Overloaded indexAction
    public function indexAction() {
        # Just to make sure

        $query = Mage::helper('catalogsearch')->getQuery();
        /* @var $query Mage_CatalogSearch_Model_Query */

        $query->setStoreId(Mage::app()->getStore()->getId());
		
        
        $query->save();
            $this->loadLayout();
            $this->_initLayoutMessages('catalog/session');
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();


                
          
    }
}	



