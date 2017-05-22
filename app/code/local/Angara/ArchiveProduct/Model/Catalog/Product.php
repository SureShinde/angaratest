<?php 
class Angara_ArchiveProduct_Model_Catalog_Product extends Mage_Catalog_Model_Product
{	
    /* Archive @Asheesh */ 
    public function loadByAttribute($attribute, $value, $additionalAttributes = '*')
    {
        $object = parent::loadByAttribute($attribute, $value, $additionalAttributes = '*');
        if($object){
			return $object;
		} elseif($attribute == 'sku') {
			$file_cache = $this->getArchivedProductsFile(0,$value);
			$file_stock_cache = $this->getArchivedProductsStockFile(0,$value);
			if(is_file($file_cache)){
				$archiveFile = fopen($file_cache, "r");			
				if ($cacheContent = fread($archiveFile,filesize($file_cache))) {
					$cacheContent = preg_replace("/<\?php\nreturn '/i","",$cacheContent);
					$cacheContent = preg_replace("/';\n\?>/i","",$cacheContent);
					$data = unserialize($cacheContent);
					$class_name = Mage::getConfig()->getModelClassName('catalog/product');
                    
					if (!empty($data)) {
						$object = new $class_name();
						foreach ($data as $key => &$value){
							if(is_string($value))
								$object->$key = str_replace("~","'",$value);
							else
								$object->$key = $value;	
						}
						unset($value);
					}
				}
				fclose($archiveFile);
				$archiveStockFile = fopen($file_stock_cache, "r");	
				if ($cacheStock = fread($archiveStockFile,filesize($file_stock_cache))) {
					$cacheStock = preg_replace("/<\?php\nreturn '/i","",$cacheStock);
					$cacheStock = preg_replace("/';\n\?>/i","",$cacheStock);
					$datas = unserialize($cacheStock);
					if (!empty($datas)) {
						$object->setStockItem($datas);
					}
				}
				fclose($archiveStockFile);
			}
		}
		return $object;
    }
	
	public function getIdBySku($sku)
    {
		$id = parent::getIdBySku($sku);
		if($id) { 
			return $id;
		} else {
			$dir = Mage::getBaseDir('media') . DS . 'import' .DS . 'Archived' . DS . 'Products' . DS ;
				
			foreach (glob($dir."*.".$sku) as $filename) {
				return (int)basename($filename);
			}

		} 
        return false;
    }
	
	
	public function getArchivedProductsFile($id = 0,$sku = ''){
		if (!is_dir(Mage::getBaseDir('media'). DS. 'import' . DS . 'Archived' . DS . 'Products')) {
			@mkdir(Mage::getBaseDir('media') . DS. 'import' . DS . 'Archived' . DS . 'Products', 0777);
		}
		$dir = Mage::getBaseDir('media') . DS. 'import' . DS . 'Archived' . DS . 'Products' . DS ;
		if($id> 0 && strlen($sku) > 0){
			return $dir.$id.'.'.$sku;
		}elseif($id> 0) {
			foreach (glob($dir.$id.".*") as $filename) {
				return $filename;
			}
		} elseif(strlen($sku) > 0) {
			foreach (glob($dir."*.".$sku) as $filename) {
				return $filename;
			}
		}
		return '';
	}
	
	public function getArchivedProductsStockFile($id = 0,$sku = ''){
		if (!is_dir(Mage::getBaseDir('media') . DS. 'import' . DS . 'Archived' . DS . 'Stock')) {
			@mkdir(Mage::getBaseDir('media') . DS. 'import' . DS . 'Archived' . DS . 'Stock', 0777);
		}
		$dir = Mage::getBaseDir('media') . DS. 'import' . DS . 'Archived' . DS . 'Stock' . DS ;
		if($id> 0 && strlen($sku) > 0){
			return $dir.$id.'.'.$sku;
		}elseif($id > 0) {
			foreach (glob($id.".*") as $filename) {
				return $filename;
			}
		} elseif(strlen($sku) > 0) {
			foreach (glob("*.".$sku) as $filename) {
				return $filename;
			}
		}
		return '';
	}
	
	public function load($id, $field = null)
    {
		$product = parent::load($id);
		if($product && $product->getId()) {
			return $product;
		} else {
			$file_cache = $this->getArchivedProductsFile($id);
			$file_stock_cache = $this->getArchivedProductsStockFile($product->getId());
			if(is_file($file_cache)){
				$archiveFile = fopen($file_cache, "r");			
				if ($cacheContent = fread($archiveFile,filesize($file_cache))) {
					$cacheContent = preg_replace("/<\?php\nreturn '/i","",$cacheContent);
					$cacheContent = preg_replace("/';\n\?>/i","",$cacheContent);
					$data = unserialize($cacheContent);
					if (!empty($data)) {
						foreach ($data as $key => &$value){
							if(is_string($value))
								$this->$key = str_replace("~","'",$value);
							else
								$this->$key = $value;	
						}
						unset($value);
					}
				}
				fclose($archiveFile);
				$archiveStockFile = fopen($file_stock_cache, "r");	
				if ($cacheStock = fread($archiveStockFile,filesize($file_stock_cache))) {
					$cacheStock = preg_replace("/<\?php\nreturn '/i","",$cacheStock);
					$cacheStock = preg_replace("/';\n\?>/i","",$cacheStock);
					$datas = unserialize($cacheStock);
					if (!empty($datas)) {
						$this->setStockItem($datas);
					}
				}
				fclose($archiveStockFile);
			}
		} 
        return $this;
    }
	
	public function archive($id = null){
		if(!is_null($id)) {
			if (is_numeric($id)) {
				$id = (int)$id;
			} elseif ($id instanceof Varien_Object) {
				$id = (int)$id->getId();
			} else {
				$id = 0;
			}
		} else if($this->getId()) {
			$id = (int)$this->getId();
		} else {
			$id = 0;
		}
		if($id > 0) {
			$product = $this->load($id);
			if($product && $product->getId()) {
				$file_cache = $this->getArchivedProductsFile($product->getId(),$product->getSku());
				$file_stock_cache = $this->getArchivedProductsStockFile($product->getId(),$product->getSku());
				$cacheContent = serialize(get_object_vars($product));
				$cacheContent = str_replace("'","~",$cacheContent);
				$cacheStock = serialize(get_object_vars($product->getStockItem()));
				$cacheStock = str_replace("'","~",$cacheStock);
				$product->unsetData('stock_item');
				$fd = fopen($file_cache, 'wb+');
				fwrite($fd, "<?php\nreturn '".$cacheContent."';\n?>");
				fclose($fd);
				$fds = fopen($file_stock_cache, 'wb+');
				fwrite($fds, "<?php\nreturn '".$cacheStock."';\n?>");
				fclose($fds);
				$product->delete();
				
			}
		}
		return $this;
	}
	/* Archive @Asheesh */
}
