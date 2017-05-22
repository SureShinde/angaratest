<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product description block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Catalog_Block_Product_View_Description extends Mage_Core_Block_Template{
	
    protected $_product = null;
	protected $_configAttributes = null;

    function getProduct(){
        if (!$this->_product){
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }
	
	function getAttributeHtml($label, $value, $dynamicClass = '', $helpInfo = ''){
		if(!empty($value)){
			return '<div class="field">
				<div class="fieldtitle text-light pull-left text-left">'.$label.':'.$helpInfo.'</div>
				<div class="fieldvalue text-right '.$dynamicClass.'">'.$value.'</div>
				<div style="clear:both"></div>
			</div>';
		}
		else{
			return '';
		}
	}
	
	function getAllowedAttributeCodes(){
		if(!$this->_configAttributes){
			$this->_configAttributes = array();
			if($this->getProduct()->isConfigurable()){
				$attributes = $this->getProduct()->getTypeInstance(true)
					->getConfigurableAttributes($this->getProduct());
				
				foreach($attributes as $attribute){
					$this->_configAttributes[] = $attribute->getProductAttribute()->getAttributeCode(); 
				}
			}
		}
		return $this->_configAttributes;
    }
	
	function getDefaultAttributeValue($attributeCode){
		if(in_array($attributeCode, $this->getAllowedAttributeCodes())){
			return $this->getProduct()->getAttributeText('default_'.$attributeCode);
		}
		else{
			return $this->getProduct()->getAttributeText($attributeCode);
		}
	}
	
	function getStoneUniqueName($product, $stoneIndex){
		$stoneShape = $product->getAttributeText('stone'.$stoneIndex.'_shape');
		$stoneName = $product->getAttributeText('stone'.$stoneIndex.'_name');
		$stoneGrade = (($product->getAttributeText('stone'.$stoneIndex.'_grade') != null) ? $product->getAttributeText('stone'.$stoneIndex.'_grade') : $product->getAttributeText('default_stone'.$stoneIndex.'_grade'));
		if($product->getData('stone'.$stoneIndex.'_weight') / $product->getData('stone'.$stoneIndex.'_count') > .24){
		   return (($stoneShape)?$stoneShape.'-':'').(($stoneName)?$stoneName.'-':'').(($stoneGrade)?$stoneGrade:'').$stoneIndex;
		}
		else{
		  return (($stoneShape)?$stoneShape.'-':'').(($stoneName)?$stoneName.'-':'').(($stoneGrade)?$stoneGrade:'');
		}
	}
	
	function getStones($product){
		$stones = array();
		try{
			// in case of configurable product the default value is collected from default_ATTRIBUTE hence setting configPrefix to 'default_' in case of configurable product
			$configPrefix = 'default_';
			for($i = ($product && $product->getIsBuildYourOwn()?'2':'1'); $i <= $product->getStoneVariationCount(); $i++){
				$stoneName = $this->getStoneUniqueName($this->getProduct(), $i);
				if(!isset($stones[$stoneName])){
					$stones[$stoneName] = array(
						'name'		=> $this->getDefaultAttributeValue('stone'.$i.'_name'),
						'shape'		=> $this->getDefaultAttributeValue('stone'.$i.'_shape'),
						'size'		=> $this->getDefaultAttributeValue('stone'.$i.'_size'),
						'grade'		=> $this->getDefaultAttributeValue('stone'.$i.'_grade'),
						'type'		=> ($product->getIsBuildYourOwn() ? 'Diamond '.$i : $this->getDefaultAttributeValue('stone'.$i.'_type')),
						'cut'		=> $this->getDefaultAttributeValue('stone'.$i.'_cut'),
						'weight'	=> $this->getProduct()->getData('stone'.$i.'_weight'),
						'count'		=> $this->getProduct()->getData('stone'.$i.'_count'),
						'setting'   => $product->getAttributeText('stone'.$i.'_setting'),
						'color'   => $product->getAttributeText('stone'.$i.'_color'),
						'clarity'   => $product->getAttributeText('stone'.$i.'_clarity')
					);
				}
				else{
					if(strpos(' '.$stones[$stoneName]['size'], ' '.$this->getDefaultAttributeValue('stone'.$i.'_size')) === false){
						$stones[$stoneName]['size'] .= ', '.$this->getDefaultAttributeValue('stone'.$i.'_size');
					}
					if(strpos(' '.$stones[$stoneName]['setting'], ' '.$product->getAttributeText('stone'.$i.'_setting')) === false){
						$stones[$stoneName]['setting'] .= ', '.$product->getAttributeText('stone'.$i.'_setting');
					}
					$stones[$stoneName]['weight'] += $this->getProduct()->getData('stone'.$i.'_weight');
					$stones[$stoneName]['count'] += $this->getProduct()->getData('stone'.$i.'_count');
				}
			}
			
			foreach($stones as &$stone){
				// add 's' or 'ies' in case stone count > 1
				if($stone['count'] > 1){
					// replace 'y' with 'ies'
					if(substr($stone['name'], -1) == 'y'){
						$stone['display_name'] = substr_replace($stone['name'], "ies", -1);
					}
					else if(substr($stone['name'], -1) == 'z'){
						$stone['display_name'] = substr_replace($stone['name'], "zes", -1);
					}
					else if(substr($stone['name'], -1) == 'x'){
						$stone['display_name'] = substr_replace($stone['name'], "xes", -1);
					}
					else{
						$stone['display_name'] = $stone['name']."s";
					}
				}
				else{
					$stone['display_name'] = $stone['name'];
				}
				
				if($stone['weight'] > 0){
					if($stone['weight'] == 1){
						$stone['weight'] = number_format(round((float)$stone['weight'], 2), 2, '.', '') . ' carat';
					}
					else{
						$stone['weight'] = number_format(round((float)$stone['weight'], 2), 2, '.', '') . ' carats';
					}
				}
				
				if($stone['type'] == 'Gemstone' && $stone['grade'] != 'Lab Created' && $stone['grade'] != 'Classic Moissanite' && $stone['grade'] != 'Forever Brilliant'){
					$stone['grade'] = 'Natural - '.$stone['grade'];
				}				
			}
		}
		catch(Exception $e){
			
		}	
		return $stones;
	}
	
	function getStonesHtml(){
		$product = $this->getProduct();
		$stones = $this->getStones($product);
		$html = '';
		$categoryIds = $product->getCategoryIds();
		$jewelryStyles = ((is_array($product->getAttributeText('jewelry_styles')))?array_values($product->getAttributeText('jewelry_styles')):array($product->getAttributeText('jewelry_styles')));
		$i=0;

		$freeProdSku = array('fr','fb','fe','fp');
		$strLowerFreeProd = strtolower(substr($product->getSku(), 0, 2));
		
		foreach($stones as $name => $stone){
			$i++;
			if(($stone['color'] && $stone['color'] != null) || ($stone['clarity'] && $stone['clarity'] != null)){
				$showQualityGrade = false;
			}	
			else{
				$showQualityGrade = true;
			}
			
			if($showQualityGrade){	
				if($stone['grade'] == 'Lab Created' || $stone['grade'] == 'Simulated'){
					$QualityGradeData = $this->getAttributeHtml('Quality Grade', $stone['grade'], 'dyn_stone'.$i.'_grade');
				}
				else{
					$QualityGradeData = $this->getAttributeHtml('Quality Grade', $stone['grade'], 'dyn_stone'.$i.'_grade', ((!in_array($strLowerFreeProd, $freeProdSku)) ? $this->getPopup('stone quality', $stone) : ''));
				}	
			}	
			
			if(($stone['grade'] != 'Lab Created' && $stone['grade'] != 'Simulated' && !in_array('457',$categoryIds)) || (in_array('457',$categoryIds) && $stone['type'] == 'Diamond')){
				if($stone['name'] == 'Diamond' || $stone['name'] == 'Peridot' || $stone['name'] == 'Garnet' || $stone['name'] == 'Opal' || $stone['name'] == 'Enhanced Black Diamond' || $stone['name'] == 'Enhanced Blue Diamond' || $stone['name'] == 'Ruby' || $stone['name'] == 'Blue Sapphire' || $stone['name'] == 'Pink Sapphire' || $stone['name'] == 'White Sapphire' || $stone['name'] == 'Yellow Sapphire' || $stone['name'] == 'Turquoise' || $stone['name'] == 'Morganite' || $stone['name'] == 'Moissanite' || $stone['name'] == 'Rose Quartz' || $stone['name'] == 'Tsavorite' || $stone['name'] == 'Tanzanite' || $stone['name'] == 'Aquamarine' || $stone['name'] == 'Amethyst' || $stone['name'] == 'Green Amethyst' || $stone['name'] == 'Citrine' || $stone['name'] == 'Pink Tourmaline' || $stone['name'] == 'Carnelian' || $stone['name'] == 'Emerald' || $stone['name'] == 'Black Onyx' || $stone['name'] == 'Akoya Cultured Pearl' || $stone['name'] == 'Freshwater Cultured Pearl' || $stone['name'] == 'South Sea Cultured Pearl' || $stone['name'] == 'Golden Japanese Cultured Pearl' || $stone['name'] == 'Golden South Sea Cultured Pearl' || $stone['name'] == 'Tahitian Cultured Pearl' || $stone['name'] == 'Blue Topaz'){
					$EnhancementData = $this->getAttributeHtml('Enhancement',$this->stoneEnhancementDetail($stone['name']),'',$this->getPopup('enhancement', $stone));
				}
			}
			
			if($stone['name'] == 'Diamond'){
				//$approxDimensions = $this->getAttributeHtml('Approximate Dimensions', $stone['size'], 'dyn_stone'.$i.'_size');
				$approxDimensions = '';
				$approxTotalCaratWeight = $this->getAttributeHtml('Approximate Carat Total Weight', $stone['weight'], 'dyn_stone'.$i.'_weight');
			}
			else{
				$approxDimensions = $this->getAttributeHtml('Approximate Dimensions', $stone['size'], 'dyn_stone'.$i.'_size', ((!in_array($strLowerFreeProd, $freeProdSku)) ? $this->getPopup('stone size', $stone) : ''));
				$approxTotalCaratWeight = $this->getAttributeHtml('Approximate Carat Total Weight', $stone['weight'], 'dyn_stone'.$i.'_weight', ((!in_array($strLowerFreeProd, $freeProdSku)) ? $this->getPopup('stone size', $stone) : ''));
			}
			
			if($showQualityGrade == false){
				if($stone['color'] && $stone['color'] != null){	
					$diamondColor = $this->getAttributeHtml('Color', $stone['color'], 'dyn_stone'.$i.'_color');
				}
				if($stone['clarity'] && $stone['clarity'] != null){
					$diamondClarity = $this->getAttributeHtml('Clarity', $stone['clarity'], 'dyn_stone'.$i.'_clarity');
				}
			}
			
			$html .= '<div class="col-sm-12 stone-detail-box-container max-margin-bottom stone-detail-box'.(($i % 2 != 0)?'-odd':'').'">
						<div>
						<div class="detail-box-title"><h5 class="no-margin">'.$stone['type'].' Information:</h5></div>';
			
			if(!in_array('347',$categoryIds) && !in_array('Eternity', $jewelryStyles)){
				$html .= (($product->getIsBuildYourOwn()) ? '' : (($stone['count'] > 0) ? $this->getAttributeHtml('Number of <span class="dyn_stone'.$i.'_shape">'.$stone['shape'].'</span> <span class="dyn_stone'.$i.'_name">'.$stone['display_name'].'</span>', $stone['count'], 'dyn_stone'.$i.'_count') : ''));
			}			
						
			$html .= $EnhancementData
						.$approxDimensions
						.$approxTotalCaratWeight
						.$QualityGradeData
						.$diamondColor
						.$diamondClarity
						.$this->getAttributeHtml($product->getResource()->getAttribute('stone1_setting')->getStoreLabel(), $stone['setting'], 'dyn_stone'.$i.'_setting')
					.'</div></div>';
		}
		return $html;
	}
	
	function getPopup($type, $stone){
		switch($type){
			case 'stone size':
				if(($stone['name'] == 'Akoya Cultured Pearl' && $stone['shape'] == 'Round') || ($stone['name'] == 'Freshwater Cultured Pearl' && $stone['shape'] == 'Round') || ($stone['name'] == 'South Sea Cultured Pearl' && $stone['shape'] == 'Round')  || ($stone['name'] == 'Golden Japanese Cultured Pearl' && $stone['shape'] == 'Round') || ($stone['name'] == 'Golden South Sea Cultured Pearl' && $stone['shape'] == 'Round') || ($stone['name'] == 'Tahitian Cultured Pearl' && $stone['shape'] == 'Round') || ($stone['name'] == 'Amethyst' && $stone['shape'] != 'Marquise' && $stone['shape'] != 'Drop') || $stone['name'] == 'Blue Topaz' || ($stone['name'] == 'Black Onyx' && $stone['shape'] != 'Ball' && $stone['shape'] != 'Emerald Cut' && $stone['shape'] != 'Marquise' && $stone['shape'] != 'Rectangle') || ($stone['name'] == 'Citrine' && $stone['shape'] != 'Rectangle' && $stone['shape'] != 'Drop') || $stone['name'] == 'Garnet' || $stone['name'] == 'Opal' || $stone['name'] == 'Peridot' || ($stone['name'] == 'Pink Tourmaline' && $stone['shape'] != 'Trillion') || $stone['name'] == 'Ruby' || $stone['name'] == 'Blue Sapphire' || ($stone['name'] == 'Pink Sapphire' && $stone['shape'] != 'Marquise') || $stone['name'] == 'Emerald' || $stone['name'] == 'Tanzanite' || ($stone['name'] == 'Diamond' && $stone['shape'] != 'Marquise' && $stone['shape'] != 'Emerald Cut') || ($stone['name'] == 'Enhanced Black Diamond' && $stone['shape'] != 'Emerald Cut') || ($stone['name'] == 'Enhanced Blue Diamond' && $stone['shape'] != 'Emerald Cut') || ($stone['name'] == 'Coffee Diamond' && $stone['shape'] == 'Round') || $stone['name'] == 'Kunzite' || ($stone['name'] == 'Morganite' && ($stone['shape'] == 'Cushion' || $stone['shape'] == 'Emerald Cut' || $stone['shape'] == 'Oval' || $stone['shape'] == 'Pear' || $stone['shape'] == 'Round' || $stone['shape'] == 'Trillion')) || ($stone['name'] == 'Aquamarine') || ($stone['name'] == 'Moissanite' && ($stone['shape'] == 'Round' || $stone['shape'] == 'Princess' || $stone['shape'] == 'Oval'))){
					if($stone['shape'] != 'Trapezoid' && $stone['shape'] != 'Half Moon' && $stone['shape'] != 'Baguette'){
						return '<span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="/hprcv/qualitycompare/getweightchart/?stonetype='.urlencode($stone['name']).'&stoneshape='.urlencode($stone['shape']).'" class="hidden-xs pull-right popup-icon gmprd-popup clickable"><i class="fa fa-question-circle low-padding-left fa-fw text-lighter fontsize-type4 clickable"></i></span>';
					}	
				}
				break;
			case 'stone quality':
				if(($stone['name'] == 'Akoya Cultured Pearl' && $stone['shape'] == 'Round') || ($stone['name'] == 'Freshwater Cultured Pearl' && $stone['shape'] == 'Round') || ($stone['name'] == 'South Sea Cultured Pearl' && $stone['shape'] == 'Round')  || ($stone['name'] == 'Golden Japanese Cultured Pearl' && $stone['shape'] == 'Round') || ($stone['name'] == 'Golden South Sea Cultured Pearl' && $stone['shape'] == 'Round') || ($stone['name'] == 'Tahitian Cultured Pearl' && $stone['shape'] == 'Round') || ($stone['name'] == 'Amethyst' && $stone['shape'] != 'Marquise' && $stone['shape'] != 'Drop' && $stone['shape'] != 'Heart') || $stone['name'] == 'Blue Topaz' || ($stone['name'] == 'Citrine' && $stone['shape'] != 'Rectangle' && $stone['shape'] != 'Drop') || ($stone['name'] == 'Garnet' && $stone['shape'] != 'Heart') || ($stone['name'] == 'Opal' && ($stone['shape'] == 'Oval' || $stone['shape'] == 'Pear' || $stone['shape'] == 'Round' || $stone['shape'] == 'Cushion')) || ($stone['name'] == 'Peridot' && $stone['shape'] != 'Heart' && $stone['shape'] != 'Square') || ($stone['name'] == 'Pink Tourmaline' && $stone['shape'] != 'Trillion') || $stone['name'] == 'Ruby' || $stone['name'] == 'Blue Sapphire' || ($stone['name'] == 'Pink Sapphire' && $stone['shape'] != 'Marquise') || $stone['name'] == 'Emerald' || $stone['name'] == 'Tanzanite' || ($stone['name'] == 'Diamond' && $stone['shape'] != 'Marquise' && $stone['shape'] != 'Emerald Cut') || ($stone['name'] == 'Enhanced Black Diamond' && $stone['shape'] != 'Emerald Cut') || ($stone['name'] == 'Enhanced Blue Diamond' && $stone['shape'] != 'Emerald Cut') || ($stone['name'] == 'Coffee Diamond' && $stone['shape'] == 'Round') || /*$stone['name'] == 'Kunzite' ||*/ ($stone['name'] == 'Morganite' && ($stone['shape'] == 'Cushion' || $stone['shape'] == 'Emerald Cut' || $stone['shape'] == 'Oval' || $stone['shape'] == 'Pear' || $stone['shape'] == 'Round' || $stone['shape'] == 'Trillion')) || ($stone['name'] == 'Aquamarine' && $stone['shape'] != 'Trillion' && $stone['shape'] != 'Square Emerald Cut') || ($stone['name'] == 'Moissanite' && ($stone['shape'] == 'Round' || $stone['shape'] == 'Princess' || $stone['shape'] == 'Oval'))){
					if($stone['shape'] != 'Trapezoid' && $stone['shape'] != 'Half Moon' && $stone['shape'] != 'Baguette'){
						return '<span rel="nofollow" data-target="#ajaxModal" data-toggle="modal" href="/hprcv/qualitycompare/get/?stonetype='.urlencode($stone['name']).'&stoneshape='.urlencode($stone['shape']).'" class="hidden-xs pull-right popup-icon gmprd-popup clickable"><i class="fa fa-question-circle low-padding-left fa-fw text-lighter fontsize-type4 clickable"></i></span>';	
					}	
				}
				break;
			case 'enhancement':
				if($stone['name'] == 'Diamond' || $stone['name'] == 'Enhanced Black Diamond' || $stone['name'] == 'Enhanced Blue Diamond' || $stone['name'] == 'Ruby' || $stone['name'] == 'Blue Sapphire' || $stone['name'] == 'Pink Sapphire' || $stone['name'] == 'Emerald' || $stone['name'] == 'Tanzanite' || $stone['name'] == 'Aquamarine' || $stone['name'] == 'Amethyst' || $stone['name'] == 'Green Amethyst' || $stone['name'] == 'Citrine' || $stone['name'] == 'Pink Tourmaline' || $stone['name'] == 'Black Onyx' || $stone['name'] == 'Carnelian' || $stone['name'] == 'Akoya Cultured Pearl' || $stone['name'] == 'Freshwater Cultured Pearl' || $stone['name'] == 'South Sea Cultured Pearl' || $stone['name'] == 'Golden Japanese Cultured Pearl' || $stone['name'] == 'Golden South Sea Cultured Pearl' || $stone['name'] == 'Tahitian Cultured Pearl' || $stone['name'] == 'Blue Topaz' || $stone['name'] == 'Garnet' || $stone['name'] == 'Moissanite' || $stone['name'] == 'Morganite' || $stone['name'] == 'Opal' || $stone['name'] == 'Peridot' || $stone['name'] == 'Rose Quartz' || $stone['name'] == 'Turquoise' || $stone['name'] == 'Tsavorite' || $stone['name'] == 'White Sapphire' || $stone['name'] == 'Yellow Sapphire'){		
					return '<span class="hidden-xs pull-right popup-icon gmprd-popup low-padding-left"><i id="enhancement_aa" class="fa fa-question-circle fa-fw text-lighter fontsize-type4 clickable" data-html="true" data-placement="right" data-trigger="click" data-toggle="tooltip" title="'.$this->getEnhancementPopupHtml($stone['name']).'"></i></span>';					
				}
				break;
		}
	}
	
	function getMetals(){
		$metals = array();
		try{
			for($i = 1; $i <= $this->getProduct()->getMetalVariationCount(); $i++){
				$metals[$i] = array(
					'type' => $this->getDefaultAttributeValue('metal'.$i.'_type'),
				);
			}
		}
		catch(Exception $e){
			
		}
		return $metals;
	}
	
	function getMetalsHtml(){
		$metals = $this->getMetals();
		$html = '';
		$i=0;
		foreach($metals as $metal){
			$i++;
			$html .= $this->getAttributeHtml('Metal', $metal['type'], 'dyn_metal'.$i.'_type');
		}
		return $html;
	}
	
	function getBandWidth(){
		$bandWidth = array();
		try{
			$bandWidth['type'] = $this->getDefaultAttributeValue('band_width');
		}
		catch(Exception $e){
			
		}
		return $bandWidth;
	}
	
	function getBandWidthHtml(){
		$product = $this->getProduct();
		$bandWidth = $this->getBandWidth();
		
		$html = '';
		$html = $this->getAttributeHtml($product->getResource()->getAttribute('band_width')->getStoreLabel(), $bandWidth['type'], 'dyn_band_width','<span class="hidden-xs pull-right popup-icon gmprd-popup low-padding-left"><i id="bandwidth_aa" class="fa fa-question-circle fa-fw text-lighter fontsize-type4 clickable" data-html="true" data-placement="right" data-trigger="click" data-toggle="tooltip" title="The width is determined by vertical measurement of the item at its widest point. All measurements are approximate and may vary slightly."></i></span>');
		return $html;
	}
	
	function getClaspType(){
		$claspType = array();
		try{
			$claspType['type'] = $this->getDefaultAttributeValue('clasp_type');
		}
		catch(Exception $e){
			
		}
		return $claspType;
	}
	
	function getClaspTypeHtml(){
		$product = $this->getProduct();
		$claspType = $this->getClaspType();
		
		$html = '';
		$html = $this->getAttributeHtml($product->getResource()->getAttribute('clasp_type')->getStoreLabel(), $claspType['type'], 'dyn_clasp_type');
		return $html;
	}
	
	function getLength(){
		$length = array();
		try{
			$length['type'] = $this->getDefaultAttributeValue('length');
		}
		catch(Exception $e){
			
		}
		return $length;
	}
	
	function getLengthHtml(){
		$product = $this->getProduct();
		$length = $this->getLength();
		
		$html = '';
		$html = $this->getAttributeHtml($product->getResource()->getAttribute('length')->getStoreLabel(), $length['type'], 'dyn_length','<span class="hidden-xs pull-right popup-icon gmprd-popup low-padding-left"><i id="bandheight_aa" class="fa fa-question-circle fa-fw text-lighter fontsize-type4 clickable" data-html="true" data-placement="right" data-trigger="click" data-toggle="tooltip" title="The length is the horizontal measurement. All measurements are approximate and may vary slightly."></i></span>');
		return $html;
	}
	
	function stoneEnhancementDetail($stoneName){
		if($stoneName){
			if($stoneName == 'Diamond' || $stoneName == 'Peridot' || $stoneName == 'Garnet' || $stoneName == 'Opal' || $stoneName == 'Amethyst' || $stoneName == 'Citrine' || $stoneName == 'Rose Quartz' || $stoneName == 'South Sea Cultured Pearl' || $stoneName == 'Golden South Sea Cultured Pearl' || $stoneName == 'Tahitian Cultured Pearl' || $stoneName == 'Tsavorite'){
				$stoneText = 'None';
			}
			else if($stoneName == 'Turquoise'){
				$stoneText = 'Stabilized';
			}
			else if($stoneName == 'Moissanite'){
				$stoneText = 'Lab Created';
			}
			else if($stoneName == 'Enhanced Black Diamond' || $stoneName == 'Green Amethyst'){
				$stoneText = 'Irradiated';
			}
			else if($stoneName == 'Enhanced Blue Diamond' || $stoneName == 'Blue Topaz'|| $stoneName == 'Morganite'){
				$stoneText = 'Heated and Irradiated';
			}
			else if($stoneName == 'Ruby' || $stoneName == 'Blue Sapphire' || $stoneName == 'Pink Sapphire' || $stoneName == 'Tanzanite' || $stoneName == 'Aquamarine' || $stoneName == 'Pink Tourmaline' || $stoneName == 'Carnelian' || $stoneName == 'White Sapphire' || $stoneName == 'Yellow Sapphire'){
				$stoneText = 'Heated';
			}
			else if($stoneName == 'Emerald'){
				$stoneText = 'Oiling';
			}
			else if($stoneName == 'Black Onyx' || $stoneName == 'Golden Japanese Cultured Pearl'){
				$stoneText = 'Dyed';
			}
			else if($stoneName == 'Akoya Cultured Pearl' || $stoneName == 'Freshwater Cultured Pearl'){
				$stoneText = 'Bleached';
			}
			return $stoneText;			
		}
	}
	
	function getEnhancementPopupHtml($stoneType){
		if($stoneType){
			if($stoneType == 'Diamond'){
				$stoneHtml = "Angara supports and deals in conflict free diamonds. Conflict-free diamonds are guaranteed not to be obtained through the use of violence, human rights abuses, child labor, or environmental destruction. Angara completely supports the Kimberley Process, which is an International process to track and certify diamonds.";
			}
			else if($stoneType == 'Enhanced Black Diamond'){
				$stoneHtml = "Irradiation is an age-old  process where gemstones are irradiated to enhance their optical properties. Irradiation may be followed by a heating/annealing process.";
			}
			else if($stoneType == 'Enhanced Blue Diamond'){
				$stoneHtml = "Irradiation is an age-old  process where gemstones are irradiated to enhance their optical properties. Irradiation may be followed by a heating/annealing process. HPHT process involves use of high heat and high pressure to affect desired alterations of color.";
			}
			else if($stoneType == 'Ruby'){
				$stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all rubies in order to maximize brilliance and purity.";
			}
			else if($stoneType == 'Blue Sapphire'){
				$stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all blue sapphires in order to maximize brilliance and purity.";
			}
			else if($stoneType == 'Pink Sapphire'){
				$stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all pink sapphires in order to maximize brilliance and purity.";
			}
			else if($stoneType == 'White Sapphire'){
				$stoneHtml = "Heat enhancement is a commonly accepted age-old high temperature treatment that is performed on all white sapphires in order to maximize brilliance and purity.";
			}
			else if($stoneType == 'Yellow Sapphire'){
				$stoneHtml = "Heat enhancement is a commonly accepted age-old high temperature treatment that is performed on all yellow sapphires in order to maximize brilliance and purity.";
			}	
			else if($stoneType == 'Emerald'){
				$stoneHtml = "The filling of surface-breaking fissures with a colorless oil, wax or other colorless substance except glass or plastic, to improve the gemstone's clarity is an age-old process adopted for virtually all emeralds.";	
			}
			else if($stoneType == 'Tanzanite'){
				$stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all tanzanites in order to maximize brilliance and purity.";
			}
			else if($stoneType == 'Aquamarine'){
				$stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all aquamarines in order to maximize brilliance and purity.";
			}
			else if($stoneType == 'Amethyst'){
				$stoneHtml = "Amethysts are not enhanced in any way.";
			}
			else if($stoneType == 'Green Amethyst'){
				$stoneHtml = "Irradiation is an age-old process in which green amethysts are treated to improve their optical properties.";
			}
			else if($stoneType == 'Citrine'){
				$stoneHtml = "Citrines are not enhanced in any way.";
			}
			else if($stoneType == 'Garnet'){
				$stoneHtml = "Garnets are not enhanced in any way.";
			}
			else if($stoneType == 'Opal'){
				$stoneHtml = "Opals are not enhanced in any way.";
			}
			else if($stoneType == 'Peridot'){
				$stoneHtml = "Peridots are not enhanced in any way.";
			}
			else if($stoneType == 'Rose quartz'){
				$stoneHtml = "Rose quartzes are not enhanced in any way.";
			}
			else if($stoneType == 'Moissanite'){
				$stoneHtml = "Moissanites are created in controlled environments using advanced technologies.";
			}
			else if($stoneType == 'Turquoise'){
				$stoneHtml = "Stabilization is a process in which turquoises are treated for enhanced color, hardness and durability. Almost all turquoises used in jewelry are stabilized.";
			}
			else if($stoneType == 'Morganite'){
				$stoneHtml = "Irradiation is an age-old process in which morganites are treated to enhance their optical properties. Irradiation is followed by a heating/annealing process.";
			}
			else if($stoneType == 'Tsavorite'){
				$stoneHtml = "Tsavorites are not enhanced in any way.";
			}
			else if($stoneType == 'Pink Tourmaline'){
				$stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all pink tourmalines in order to maximize brilliance and purity.";
			}
			else if($stoneType == 'Black Onyx'){
				$stoneHtml = "The introduction of coloring matter into a gemstone to give it new color, intensify existing color or improve color uniformity.";
			}
			else if($stoneType == 'Carnelian'){
				$stoneHtml = "Heat Enhancement is a commonly accepted age-old high temperature Enhancement that is performed on virtually all carnelians in order to maximize brilliance and purity.";
			}
			else if($stoneType == 'Akoya Cultured Pearl'){
				$stoneHtml = "Bleaching is a commonly accepted method used to lighten and even out the color of Akoya cultured pearls.";	
			}
			else if($stoneType == 'South Sea Cultured Pearl'){
				$stoneHtml = "South Sea cultured pearls are not enhanced in any way.";
			}
			else if($stoneType == 'Freshwater Cultured Pearl' || $stoneType == 'Golden Japanese Cultured Pearl' || $stoneType == 'Golden South Sea Cultured Pearl' || $stoneType == 'Tahitian Cultured Pearl'){
				$stoneHtml = "Cultured pearl is a natural pearl grown under controlled conditions. This is a process where a seed pearl is inserted into the mantle of an oyster and kept in the sea bed for some years for complete formation.";
			}
			else if($stoneType == 'Blue Topaz'){
				$stoneHtml = "Irradiation is an age-old  process where gemstones are irradiated to enhance their optical properties. Irradiation may be followed by a heating/annealing process.";
			}
		return $stoneHtml;		
		}
	}
}