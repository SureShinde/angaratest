<?php
Class Countrymapping
{	
	const logFile = 'countrymapping.log';
	
	public static function getCountryMapping() 
	{
		$memcache = Mage::getMemcache();
		if(!$memcache) {
			return self::readCountryMappingFromExcel();
		}else {
			//get from memcache
			$country = $memcache->get('countryArr');
			if($country) {
				return $country;
			} else {
				$country = self::readCountryMappingFromExcel();
				if($country) {
					/*$memcache->set('countryArr', $country, false, 0) or Mage::log('Could not connect to memcache server- couldnot save data to memcache server', null, self::logFile);*/					
					$memcache->set('countryArr', $country, false, 0);
				} else {
					$country=false;
				}				
				return $country;	
			}
			
		}
 
	}	
	
	protected static function readCountryMappingFromExcel() 
	{ 
		//Mage::log('reading country mapping from excel', null, self::logFile);
		$excelFile = $_SERVER['DOCUMENT_ROOT'].'/app/etc/Excel/reader.php';
		require_once $excelFile;

		$filename = $_SERVER['DOCUMENT_ROOT'].'/app/etc/country-mapping.xls';
		//$filename = 'jxlrwtest2.xls1';

		$reader=new Spreadsheet_Excel_Reader();
		$reader->setUTFEncoder('iconv');
		$reader->setOutputEncoding('UTF-8');
		$reader->read($filename);
		
		if(!$reader->sheets) {
			//Mage::log('country mapping file not readable or doest exist.', null, self::logFile);
			return false;
		}
		foreach($reader->sheets as $k=>$data)
		 {
			$rowCnt=1;
			foreach($data['cells'] as $row)
			{
				$cellCnt = 1;
				if($rowCnt==1) {
					$rowCnt=$rowCnt+1;
					continue;
				}
				$key= $row[1];
				foreach($row as $cell)
				{
				   if($cellCnt==1) {
						$country[$data['cells'][1][1]][$key] = $cell;
				   }elseif($cellCnt==2) {
						$country[$data['cells'][1][2]][$key] = $cell;
				   }elseif($cellCnt==3) {
						$country[$data['cells'][1][3]][$key] = $cell;
				   }elseif($cellCnt==4) {
						$country[$data['cells'][1][4]][$key] = $cell;
				   }
				   
					$cellCnt = $cellCnt+1;
				}
				$rowCnt=$rowCnt+1;
			}
		 }
		 return $country;
 }
}
