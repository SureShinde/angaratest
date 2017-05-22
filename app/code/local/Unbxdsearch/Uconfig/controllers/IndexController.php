<?php 
class Unbxdsearch_Uconfig_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
    	
    	$collection=Mage::getModel('uconfig/conf')->getCollection()
    				-> addFieldToFilter('action','address');
    	
    	foreach($collection as $coll){
    	
    		$coll->getvalue();
    	}
    	
    	
    	
    	
    	
	$this->loadLayout();
 
	 $this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('signup/aform.phtml')->setFormAction(Mage::getUrl('*/*/new')));
	
 
         $this->renderLayout();

    }

    public function newAction()
    {

		$address="";
		$postData = Mage::app()->getRequest()->getParam('address');
		if (isset($postData)) {
			$address=   $postData;

		}
	        $adapter =Mage::getSingleton('core/resource')->getConnection('core_write');
	
	        try
	        {
	        	 
	        	$query="show tables like 'unbxdConf'";
	        	$result=$adapter->query($query);
	        
	        
	        	 
	        }
	        catch(Exception $Ex)
	        {
	        	$q="create table unbxdConf (action varchar(100),value varchar(100))";
	        	$adapter->query($q);
	        	$q="insert into unbxdConf values('Fullexport','true')";
	        	$adapter->query($q);
	        	$sql="insert into unbxdConf values('Lastindex','".$currentDate."')";
	        	$adapter->query($sql);
	        	$this->getInserted('RecoverIndex', '0');	
    			$this->getInserted('doUpload', 'true');
    			$this->getInserted('address', 'empty');
	        	 
	        }
	        $this->getInserted('address', $address);

	$this->loadLayout();
 
	 $this->_addContent($this->getLayout()->createBlock('adminhtml/template')->setTemplate('signup/success.phtml'));
	
 
         $this->renderLayout();

    }
    
    public function getUpdated($action,$value)
    {
    
    	$adapter=Mage::getSingleton('core/resource')->getConnection('core_write');
    	$sql="update unbxdConf set value='".$value."' where action='".$action."'";
    	$result=$adapter->query($sql);
    }
    
    public function getInserted($action,$value)
    {
    
    	$adapter=Mage::getSingleton('core/resource')->getConnection('core_write');
    	$sql="insert into unbxdConf values('".$action."','".$value."')";
    	$result=$adapter->query($sql);
    }

  


}

?>
