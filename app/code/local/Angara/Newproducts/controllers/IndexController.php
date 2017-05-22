<?php
error_reporting(0);
class Angara_Newproducts_IndexController extends Mage_Core_Controller_Front_Action {

	public function getIsNewProductsReportsAction() {
		if(Mage::getBaseUrl()!='http://www.angara.com' || Mage::getBaseUrl()!='https://www.angara.com'){
			if(isset($rowsPlot)){
				unset($rowsPlot);
			}
			$headings = array('Sku','Is New Product Date');
			
			$_productCollection = Mage::getModel('catalog/product')->getCollection()
								->addAttributeToSelect('*')
								->addAttributeToFilter('visibility', 4)
								->addAttributeToFilter('is_new_product', 1)
								->load();
			foreach($_productCollection as $_product):
				$productSku = $_product->getSku();
				$isNewProductDate = date('Y-m-d',strtotime($_product->getIsNewProductDate()));			
				$rowsPlot[] = $productSku.'&&&'.$isNewProductDate;		
			endforeach;
			
			$writeFile = 'file.csv';	
			$fp = fopen($writeFile, 'w');
			fputcsv($fp,$headings);
			
			for($i=0;$i<count($rowsPlot);$i++):
				fputcsv($fp,split('&&&',$rowsPlot[$i]));	
			endfor;
			
			fclose($fp);
			
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename='.$writeFile);
			header('Pragma: no-cache');
			readfile($writeFile);
		}
	}
}
?>