<?php
class Angara_Productbase_Block_Adminhtml_Customproduct extends Mage_Core_Block_Template
{
    public function getCartLink(){
		$url = $this->getBaseUrl()."checkout/cart/personalized/product/".$this->getProduct()->getId();
		$data = $this->getCartParams();
		$additionalPids = array();
		if($data['freesku1']!= '0'){
			$additionalPids[] = $data['freesku1'];
		}
		if($data['freesku2']!= '0'){
			$additionalPids[] = $data['freesku2'];
		}
		if($data['freesku3']!= '0'){
			$additionalPids[] = $data['freesku3'];
		}
		
		$url .= '?related_product='.implode(',',$additionalPids);
		
		if($data['easyopt']!= '0'){
			$url .= '&easyopt='.$data['easyopt'];
		}
		if($data['appraisal']!= '0'){
			$url .= '&appraisal=on';
		}
		if($data['insurance']!= '0'){
			$url .= '&insurance=on';
		}

		return $url;
	}
	
	protected function _toHtml() {
		$html = '
			<h3>Please copy the following link and send the same to customer in order to add the customized product in his cart.</h3>
			<h2 id="cplink">'.$this->getCartLink().'</h2>
			<h4>Details</h4>
			'.nl2br($this->getProduct()->getDescription()).'
		';
		return $html;
	}
}
