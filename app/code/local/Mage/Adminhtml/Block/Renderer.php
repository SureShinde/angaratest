<?php
class Mage_Adminhtml_Block_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {   
		$okToShip			=	$row->getdata('oktoship');
		if($okToShip==''){
			 return 'No';
		}else{
			$valuesArray	=	Mage_Adminhtml_Block_Sales_Order_Grid::getOptionArrayOkToShip();
			return $valuesArray[$okToShip];
		}
    }
}
