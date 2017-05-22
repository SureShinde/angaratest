<?php 


class Unbxdsearch_Datafeeder_Block_Index extends Mage_Adminhtml_Block_Template
{
	public function getFullindexFormAction()
	{
			return Mage::getUrl('*/*/fullindex');
	}
	
	
}

?>