<?php
class Angara_CustomerStory_Block_Adminhtml_Story_Grid_Image_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {   
		$storyId = $row->getdata('id');
		if($storyId != ''){
			$storyObj = Mage::getModel('customerstory/story')->load($storyId);
			$imageDetails = unserialize(urldecode($storyObj->getImageDetails()));
			$imageHtml = '<ul>';
			$orderId = $storyObj->getOrderId();
			$imagePath = Mage::getBaseUrl('media').'shareStory/'.$orderId.'/';
			
			for($key=0; $key < count($imageDetails); $key++){
				$imageHtml .= '<li style="display: inline-block; padding-right: 5px;"><img width="80" height="80" src="'.$imagePath.$imageDetails[$key]['name'].'" alt="share-story-image-'.($key + 1).'"></li>';
			}
			$imageHtml .= '</ul>';
			return $imageHtml;
		}
    }
}
