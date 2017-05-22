<?php
class Angara_Ondemand_IndexController extends Mage_Core_Controller_Front_Action {        
	
	public function indexAction() {
		// do something
	}
	
	public function flushAction() {
		$flushed = Angara_Cacher::flushCache();
		if($flushed){
			Mage::getSingleton('core/session')->addError('Cache has been flushed.');
		}
		else{
			Mage::getSingleton('core/session')->addError('Unable to flush cache.');
		}
		$this->_redirect('*/*/');
	}
	
	public function statsAction(){
		$stats = Angara_Cacher::stats();
		foreach($stats['127.0.0.1:11211'] as $key => $stat){
			echo '<span>'.$key.' </span><span style="color:#f00">'.$stat.'</span>';
			echo "<br>";
		}
	}
}
?>