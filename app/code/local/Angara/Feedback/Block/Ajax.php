<?php   
class Angara_Feedback_Block_Ajax extends Mage_Core_Block_Template{ 

	/*
		Block function to show the feedback categories dropdown on the form
	*/
	public function getFeedbackCategories(){
		$data_array		=	array(); 
		//	Find the active parent categories
		$collection 	= 	Mage::getModel("feedback/category")->getCollection()
								->addFieldToSelect('*')
								->addFieldToFilter('status', array('eq' => '1'))
								->addFieldToFilter('parent_category_id', array('eq' => '0'))
								//->setOrder('name',ASC)
								->setOrder('category_id',ASC)
								->load();
								//->load(1);die;
		//echo '<pre>';print_r($collection->getdata());die;
		//$data_array[0]	=	'None';
		if( $collection->count() ){
			foreach($collection as $_method) {
				$data_array[$_method->getData('category_id')] = 	$_method->getData('name');
			}
		}
		return($data_array);	
	}
}