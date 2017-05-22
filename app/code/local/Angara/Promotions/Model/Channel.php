<?php
class Angara_Promotions_Model_Channel extends Mage_Core_Model_Abstract
{
    protected function _construct(){
       $this->_init("promotions/channel");
    }
	
	public function getChannelByUrl($url){
		$channels = $this->getCollection()->addFieldToFilter('status', 1);
		foreach($channels as $channel){
			if(stristr($url, $channel->getUrlIdentifier())!==false){
				return $channel;
			}
		}
		// assuming 1 to be direct channel
		return $this->load(1);
	}
}	 