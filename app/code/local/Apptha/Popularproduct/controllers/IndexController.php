<?php
class Apptha_Popularproduct_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/popularproduct?id=15 
    	 *  or
    	 * http://site.com/popularproduct/id/15 	
    	 */
    	/* 
		$popularproduct_id = $this->getRequest()->getParam('id');

  		if($popularproduct_id != null && $popularproduct_id != '')	{
			$popularproduct = Mage::getModel('popularproduct/popularproduct')->load($popularproduct_id)->getData();
		} else {
			$popularproduct = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($popularproduct == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$popularproductTable = $resource->getTableName('popularproduct');
			
			$select = $read->select()
			   ->from($popularproductTable,array('popularproduct_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$popularproduct = $read->fetchRow($select);
		}
		Mage::register('popularproduct', $popularproduct);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}