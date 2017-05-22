<?php
class Angara_Reminder_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('add');

    /**
     * If true, authentication in this controller (reminder) could be skipped
     *
     * @var bool
     */
	 
    protected $_skipAuthentication = false;	

    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_skipAuthentication && !Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if (!Mage::getSingleton('customer/session')->getBeforeReminderUrl()) {
                Mage::getSingleton('customer/session')->setBeforeReminderUrl($this->_getRefererUrl());
            }
            Mage::getSingleton('customer/session')->setBeforeReminderRequest($this->getRequest()->getParams());
        }        
    }
	
	public function indexAction()
    {	
		$customerId = Mage::getSingleton('customer/session')->getCustomerId();
        
		//  Creating model object on which we want to do manipulation
		$model = Mage::getModel("reminder/reminder");
		// Generating Collection of above model.
		$collection = $model->getCollection();
		
		//print_r($gdata);exit;
		//We are adding filter on collection that
		// we want the data of currently logged in user only.
		//$collection->addCustomerFilter($customerId);
		//Adding where condition into the query
		 //$collection->addFieldToFilter('status', array('neq' => '0'));
		// Adding order by clause to the mysql query through collection		
				
		$collection->getSelect()->Where('customer_id='.$customerId);
		
		$collection->getSelect()->order(new Zend_Db_Expr('reminder_id DESC'));
		// Adding limit to mysql query through collection
		$collection->getSelect()->limit(100);
		
		//echo $collection->getSelect(); //exit;
		
		$gdata = $collection->getData();
		
		//Below code will  print full mysql query for you so that you can see it
		//echo $collection->getSelect();
		// Now use your collection. Simply loop through it
		$model->setCollection($collection);
		
		$this->loadLayout();    
  		$this->renderLayout();
    }
	
	public function newAction()
    {		
		$errors = array();
        // Save data
        if ($this->getRequest()->isPost()) {
			if (!$this->_validateFormKey()) {
				return $this->_redirect('*/*/new');
			}
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $reminder  = Mage::getModel('reminder/reminder');
			$storeId = Mage::app()->getStore()->getStoreId();
			$data = $this->getRequest()->getPost();
			//echo '<pre>';print_r($data);echo '</pre>';exit;			
			$reminder->setReminderId(NULL);
			$reminder->setStoreId($storeId);
			$reminder->setCustomerId($customerId);		
			$reminder->setFirstname($data['firstname']);
			$reminder->setLastname($data['lastname']);			
			$reminder->setRelationship($data['relationship']);
			$reminder->setOccasion($data['occasion']);
			$reminder->setOccasionDate($data['occasion_date']);
			$reminder->setRingSize($data['ring_size']);
			$reminder->setFavoriteGemstone($data['favorite_gemstone']);
			$reminder->setComments($data['comments']);
						
			$reminder_save = $reminder->save();
			//var_dump($reminder_save);exit;
			if(!$reminder_save){
				$this->_getSession()->addError("Cannot save reminder.");
			} else {
				//$this->_getSession()->addSuccess("The reminder has been saved.");
				$this->_redirect('*/*/');
			}			
		}else{
			$this->loadLayout();    
	  		$this->renderLayout();	
		}	
    }
	
	public function editAction()
    {
		$errors = array();
        // Save data
        if ($this->getRequest()->isPost()) {
			if (!$this->_validateFormKey()) {
				return $this->_redirect('*/*/');
			}
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $reminder  = Mage::getModel('reminder/reminder');
			$storeId = Mage::app()->getStore()->getStoreId();
			$data = $this->getRequest()->getPost();
			//echo '<pre>';print_r($data);echo '</pre>';exit;			
			$reminder_id = $data['reminder_id']; //$this->getRequest()->getParam('id');			
			$reminder->setReminderId($reminder_id);
			$reminder->setStoreId($storeId);
			$reminder->setCustomerId($customerId);		
			$reminder->setFirstname($data['firstname']);
			$reminder->setLastname($data['lastname']);			
			$reminder->setRelationship($data['relationship']);
			$reminder->setOccasion($data['occasion']);
			$reminder->setOccasionDate($data['occasion_date']);
			$reminder->setRingSize($data['ring_size']);
			$reminder->setFavoriteGemstone($data['favorite_gemstone']);
			$reminder->setComments($data['comments']);
						
			$reminder_save = $reminder->save();
			//var_dump($reminder_save);exit;
			if(!$reminder_save){
				$this->_getSession()->addError("Cannot save reminder.");
			} else {
				//$this->_getSession()->addSuccess("The reminder has been saved.");
				$this->_redirect('*/*/');
			}
		}else{
			if(!$this->getRequest()->getParam('id')){					
				$this->_redirect('*/*/');
			}else{
				if($this->getRequest()->getParam('id') != ''){
					$this->loadLayout();    
			  		$this->renderLayout();			
				}else{
					$this->_redirect('*/*/');
				}
			}
		}
    }
	
	public function deleteAction() {
	    $idToDelete = $this->getRequest()->getParam('id');
	    $reminder = Mage::getModel('reminder/reminder')->load($idToDelete);
	    $reminder->delete();
	    $this->_redirect('*/');
	}	
}