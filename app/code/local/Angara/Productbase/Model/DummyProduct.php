<?php
class Angara_Productbase_Model_DummyProduct extends Mage_Core_Model_Abstract
{
	
	
	public function calculatePrice($data){
		
		$this->setSku($data['sku']);
		$this->setMetal($data['metal']);
		$this->setAvgMetalWeight($data['avg_metal_weight']);
		$this->setfinding_14kgold($data['finding_14kgold']);
		$this->setFindingSilver($data['finding_silver']);
		$this->setFindingPlatinum($data['finding_platinum']);
		$this->setAttributeSetId($data['set']);
		$this->setAngaraPbCategoryId($data['category']);
		
		$this->fetchStones($data);
		
		
		$price = Mage::getModel('productbase/priceProcessor')->getProductPrice($this);
		
		return $price;
	}
	
	public function fetchStones($data){
		
		$stones = Mage::getModel('productbase/stone_name')->getCollection();
		$shapes = Mage::getModel('productbase/stone_shape')->getCollection();
		
		$dummyStones = array();
		if($data['stone1count']){
			$dummyStone1 = Mage::getModel('productbase/dummyStone');
			$dummyStone1->setCount($data['stone1count']);
			$dummyStone1->setShape($shapes->getItemById($data['stone1shape'])->getShape());
			$dummyStone1->setName($stones->getItemById($data['stone1name'])->getTitle());
			$dummyStone1->setGrade($data['grade']);
			$dummyStone1->setSize($data['stone1size']);
			$dummyStone1->setSettingType($data['stone1setting']);
			$dummyStones[] = $dummyStone1;
		}
		
		if($data['stone2count']){
			$dummyStone2 = Mage::getModel('productbase/dummyStone');
			$dummyStone2->setCount($data['stone2count']);
			$dummyStone2->setShape($shapes->getItemById($data['stone2shape'])->getShape());
			$dummyStone2->setName($stones->getItemById($data['stone2name'])->getTitle());
			$dummyStone2->setGrade($data['grade']);
			$dummyStone2->setSize($data['stone2size']);
			$dummyStone2->setSettingType($data['stone2setting']);
			$dummyStones[] = $dummyStone2;
		}
		
		if($data['stone3count']){
			$dummyStone3 = Mage::getModel('productbase/dummyStone');
			$dummyStone3->setCount($data['stone3count']);
			$dummyStone3->setShape($shapes->getItemById($data['stone3shape'])->getShape());
			$dummyStone3->setName($stones->getItemById($data['stone3name'])->getTitle());
			$dummyStone3->setGrade($data['grade']);
			$dummyStone3->setSize($data['stone3size']);
			$dummyStone3->setSettingType($data['stone3setting']);
			$dummyStones[] = $dummyStone3;
		}
		$this->setStones($dummyStones);
	}
	
	public function save($data){
		
		$product = Mage::getModel('catalog/product')
   			->loadByAttribute('sku',$data['sku']);
   
   		if(!$product){
			$product = new Mage_Catalog_Model_Product();
			
			$product->setTypeId('simple');
			$product->setWeight(0.0000);
			$product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
			$product->setStatus(1);	
			//$product->setSku($data['sku']);
			$product->setTaxClassId(4);
			$product->setWebsiteIds(array(1));
			$product->setStoreIds(array(1));
			
			
			if(!empty($data["price"])){
				$product->setPrice((float)$data['price']);
			}
			else{
				return false;
			}
			$product->setAttributeSetId(4);
			$product->setCategoryIds(array(3));
			if(!empty($data["name"])){
				$name = $data['name'];
			}
			else{
				$name = "Custom Product";
			}
			$product->setName($name);
			
			$product->setCreatedAt(strtotime('now'));
			
			$description = $name;
			$shortDescription = $name;
				
			if(!empty($data["comments"])){
				$description .= " \r\n Comments: ".$data["comments"];
			}
			
			$description .= " \r\n Returnable: ".$data["returnable"];
			$description .= " \r\n Restocking Fee Taken: ".$data["restocking"];
			$freeProducts = array();
			if($data["freesku1"]) $freeProducts[] = Mage::getModel('catalog/product')->load($data["freesku1"])->getName();
			if($data["freesku2"]) $freeProducts[] = Mage::getModel('catalog/product')->load($data["freesku2"])->getName();
			if($data["freesku3"]) $freeProducts[] = Mage::getModel('catalog/product')->load($data["freesku3"])->getName();
			$description .= " \r\n Free Products: ".implode(', ',$freeProducts);
			$description .= " \r\n\r\n Price $: ".$data["price"];
			$description .= " \r\n\r\n Installments: ".($data["easyopt"] + 1);
			$description .= " \r\n Appraisal: ".($data["appraisal"]?'Yes':'No');
			$description .= " \r\n Insurance: ".($data["insurance"]?'Yes':'No');
			if(!empty($data["stone_information"]))
				$description .= " \r\n\r\n Stones Information: ".$data["stone_information"];
			if(!empty($data["metal_information"]))
				$description .= " \r\n\r\n Metals Information: ".$data["metal_information"];
			if(!empty($data["other_information"]))
				$description .= " \r\n\r\n Extra Information: ".$data["other_information"];
			if(!empty($data["engraving"]))
				$description .= " \r\n\r\n Engraving Text + Font: ".$data["engraving"];
			
			$product->setShortDescription($shortDescription);
			$product->setDescription($description);
		
			try{
				$product->save();
				
				$stockData = $product->getStockData();
				$stockData['qty'] = 10;
				$stockData['is_in_stock'] = 1;
				$stockData['manage_stock'] = 0;
				
				$product->setStockData($stockData);
				
				$user = Mage::getSingleton('admin/session');
				
				$product->setSku("ANGCP".$product->getId().substr($user->getUser()->getFirstname(), 0, 1).substr($user->getUser()->getLastname(), 0, 1));
				Mage::helper('function')->writeTextOnImage("jpg", str_replace(Mage::getBaseUrl('media'), 'media/', Mage::helper('catalog/image')->init($product, 'image')), "Custom Product\n".$product->getSku(), 'media/catalog/customproduct/'.$product->getSku().'.jpg', 'skin/frontend/angara/default/fonts/opensans-regular-webfont.ttf');
					
				$product->setMediaGallery(array('images'=>array (), 'values'=>array ()));
				if(is_file('media/catalog/customproduct/'.$product->getSku().'.jpg'))
				{
					$product->addImageToMediaGallery('media/catalog/customproduct/'.$product->getSku().'.jpg', array ('image', 'small_image', 'thumbnail'), false, false);
				}
				
				$product->save();
				
				return $product;
			} catch(Exception $e){
				Mage::getSingleton('admin/session')->addError("There is a problem saving product. Please report error to technical team. [".$e->getMessage()."]");
				return false;
			}
			
		}
		else{
			return false;
		}
	}

}
