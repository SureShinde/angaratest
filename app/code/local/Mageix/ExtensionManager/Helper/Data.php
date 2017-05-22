<?php

class Mageix_ExtensionManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_ftpPassword;
    protected $_ftpDirMode = 0775;
    protected $_ftpFileMode = 0664;

    public function __construct()
    {
        Mage::getConfig()->loadModulesConfiguration('mextensionmanager.xml', Mage::getConfig());
        $this->_ftpPassword = Mage::app()->getRequest()->getPost('ftp_password');
    }

    public function download($uri,$moduleName = '')
    {
    			
        	
		if($moduleName != ''){
			
			$uri = $this->_prepareMod($uri,$moduleName);
			
		} else {
			$uri = $this->_prepareMod($uri);
			
		}
		
		$errorReturned = strpos($uri, "Error while connecting:");
				
		if($errorReturned !== false){
        	
	     $error = $this->__($uri);
         Mage::throwException($error);
			
        }

        $dlDir = Mage::getConfig()->getVarDir('mextensionmanager/download');
        Mage::getConfig()->createDirIfNotExists($dlDir);
        
        $filePath = $dlDir.'/'.basename($uri);
        $fd = fopen($filePath, 'wb');

        $ch = curl_init();
		
        curl_setopt_array($ch, array(
            CURLOPT_URL =>  $uri,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FILE => $fd,
        ));
        
        if (curl_exec($ch)===false) {
          curl_close($ch);
          fclose($fd);
          
          $fd = fopen($filePath, 'wb');
          $ch = curl_init();
		
        curl_setopt_array($ch, array(
            CURLOPT_URL =>  $uri,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FILE => $fd,
        ));
        
        }
        
        if (curl_exec($ch)===false) {
            $error = $this->__('Error while downloading file: %s', curl_error($ch));
            curl_close($ch);
            fclose($fd);
            Mage::throwException($error);
        }
        
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE)!=200) {
            $error = $this->__('File not found or error while downloading: %s', $uri);
            curl_close($ch);
            fclose($fd);
            Mage::throwException($error);
        }
        
        
        curl_close($ch);
        fclose($fd);

        return $filePath;
    }

    public function install($uri, $filePath)
    {
        $tempDir = Mage::getConfig()->getVarDir('mextensionmanager/unpacked').'/'.basename($filePath);
        
        Mage::getConfig()->createDirIfNotExists($tempDir);

        $this->unarchive($filePath, $tempDir);
        $this->registerModulesFromDir($uri, $tempDir);

        $useFtp = Mage::getStoreConfig('mextensionmanager/ftp/active');
        if ($useFtp) {
            $errors = $this->ftpUpload($tempDir);
            if ($errors) {
                $logDir = Mage::getConfig()->getVarDir('mextensionmanager/log').'/'.basename($filePath);
                Mage::getConfig()->createDirIfNotExists($logDir);

                $fd = fopen($logDir.'/errors.log', 'a+');
                foreach ($errors as $error) {
                    fwrite($fd, date('Y-m-d H:i:s').' '.$error."\n");
                }
                fclose($fd);
                Mage::throwException($this->__('Errors during FTP upload, see this log file: %s', 'mextensionmanager/log/'.basename($filePath).'/errors.log'));
            }
        } else {
            $this->unarchive($filePath, Mage::getBaseDir());
        }

        return $this;
    }

    public function unarchive($filePath, $target)
    {
        switch (strtolower(pathinfo($filePath, PATHINFO_EXTENSION))) {
            case 'zip':
                $this->unzip($filePath, $target);
                break;
            default:
                Mage::throwException($this->__('Unknown archive format'));
        }
    }

    public function unzip($filePath, $target)
    {
        if (!extension_loaded('zip')) {
            Mage::throwException($this->__('Zip PHP extension is not installed'));
        }
        $zip = new ZipArchive();
        if (!$zip->open($filePath)) {
            Mage::throwException($this->__('Invalid or corrupted zip file'));
        }
        
        
        if (!$zip->extractTo($target)) {
            $zip->close();
            Mage::throwException($this->__('Errors during unpacking zip file. You may set Magento folders permissions to 0755 recursively and revert once you finish installing. Please check destination write permissions: %s', $target));
        }
        $zip->close();
    }

    public function ftpUpload($from)
    {
        if (!extension_loaded('ftp')) {
            Mage::throwException($this->__('FTP PHP extension is not installed'));
        }
        $conf = Mage::getStoreConfig('mextensionmanager/ftp');
        if (!($conn = ftp_connect($conf['host'], $conf['port']))) {
            Mage::throwException($this->__('Could not connect to FTP host'));
        }
        $password = $this->_ftpPassword ? $this->_ftpPassword : Mage::helper('core')->decrypt($conf['password']);
        if (!@ftp_login($conn, $conf['user'], $password)) {
            ftp_close($conn);
            Mage::throwException($this->__('Could not login to FTP host'));
        }
        if (!@ftp_chdir($conn, $conf['path'])) {
            ftp_close($conn);
            Mage::throwException($this->__('Could not navigate to FTP Magento base path'));
        }

        $errors = $this->ftpUploadDir($conn, $from.'/');

        ftp_close($conn);

        return $errors;
    }

    public function ftpUploadDir($conn, $source, $ftpPath='')
    {
        $errors = array();
        $dir = opendir($source);
        while ($file = readdir($dir)) {
            if ($file=='.' || $file=="..") {
                continue;
            }
            if (!is_dir($source.$file)) {
                if (@ftp_put($conn, $file, $source.$file, FTP_BINARY)) {
                    // all is good
                    #ftp_chmod($conn, $this->_ftpFileMode, $file);
                } else {
                    $errors[] = ftp_pwd($conn).'/'.$file;
                }
                continue;
            }
            if (@ftp_chdir($conn, $file)) {
                // all is good
            } elseif (@ftp_mkdir($conn, $file)) {
                ftp_chmod($conn, $this->_ftpDirMode, $file);
                ftp_chdir($conn, $file);
            } else {
                $errors[] = ftp_pwd($conn).'/'.$file.'/';
                continue;
            }
            $errors += $this->ftpUploadDir($conn, $source.$file.'/', $ftpPath.$file.'/');
            ftp_chdir($conn, '..');
        }
        return $errors;
    }
    
     protected function _prepareMod($uri,$moduleName = '') {
    
	$fields_string = '';		

	if($moduleName != ''){	
	$modules = Mage::getModel('mextensionmanager/module')->getCollection()
            ->addFieldToFilter('module_name', $moduleName);	
			
	} elseif (Mage::getSingleton('core/session')->getCycleLicenceKey() != ''){
		      $cycleLicenseKey = Mage::getSingleton('core/session')->getCycleLicenceKey();
			  
			  $modules = Mage::getModel('mextensionmanager/module')->getCollection()
                         ->addFieldToFilter('license_key', $cycleLicenseKey);
			  Mage::getSingleton('core/session')->unsCycleLicenceKey();
			  
	}else {
		
	$modules = Mage::getModel('mextensionmanager/module')->getCollection()
            ->addFieldToFilter('download_uri', $uri);
	}
	
  
        foreach ($modules as $mod) {
            $key = $mod->getLicenseKey();
			$modName = $mod->getModuleName();
			$uri = $mod->getDownloadUri();
			
         }
	
        $domain = Mage::getBaseUrl();
        $data = array('domain' =>$domain, 'key'=>$key);
        
        $fields = array(
			'domain' => urlencode($domain),
			'key' => urlencode($key)
				);
				
			$endPoint = strpos($uri, "mageix.com");
				
		if($endPoint !== false){
			$fields = array(
			'domain' => urlencode($domain),
			'key' => urlencode($key),
			'module_name' => urlencode($modName)
				);
				
				if($modName == "Mageix_ExtensionManager" || $modName == "Mageix_ExtensionManagerLicense"){
					$uri = "https://ixcba.com/ixval/index/memupdate";
				}
		}
		
		
		
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        $fields_string = rtrim($fields_string, '&');
        
        $uri = $uri.'?'.$fields_string;

        $ch = curl_init();
		
        curl_setopt_array($ch, array(
            CURLOPT_URL =>  $uri,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
        ));
        
        $returndata = curl_exec ($ch);
        
         if(curl_errno($ch)) {
         
          curl_close($ch);
          
          $ch = curl_init();
		
        curl_setopt_array($ch, array(
            CURLOPT_URL =>  $uri,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        
        $returndata = curl_exec ($ch);
        
        }
        
		
		if(curl_errno($ch))
        {
        
		 return 'Error while connecting:' . curl_error($ch);	
		
        } else {
        	
		curl_close($ch);
		return $returndata;	
			
        }  

    }

    public function registerModulesFromDir($uri, $dir)
    {
		
		$endPoint = strpos($uri, "memupdate");

        $configFiles = glob($dir.'/app/code/*/*/*/etc/config.xml');
	    
        
        if (!$configFiles) {
            Mage::throwException('Could not find module configuration files');
        }
        foreach ($configFiles as $file) {
            $config = new Varien_Simplexml_Config($file);
            if (!$config->getNode('modules')) {
                continue;
            }
            foreach ($config->getNode('modules')->children() as $modName=>$modConf) {
                if (!isset($modConf->mextensionmanager) || !isset($modConf->mextensionmanager['remote'])) {
                    continue;
                }
                    $module = Mage::getModel('mextensionmanager/module')->load($modName, 'module_name')
                    ->setModuleName($modName)
                    ->setDownloadUri($uri)
                    ->setLastDownloaded(now())
                    ->setLastVersion((string)$modConf->version)
                    ->setRemoteVersion((string)$modConf->version)
                    ->save();
            }
        }
    }


    public function checkUpdates()
    {
        set_time_limit(0);
        $dbModules = Mage::getModel('mextensionmanager/module')->getCollection();
        $uriMods = array();
		foreach ($dbModules as $mod) {
            $modName = $mod->getModuleName();
            if (!$modName) {
        		continue;
            }
			
            $mextensionmanager = Mage::getConfig()->getNode("modules/".$modName."/mextensionmanager");
        	if (!$mextensionmanager || !isset($mextensionmanager['remote'])) {
        		continue;
            }
			
            $uriMods[(string)$mextensionmanager['remote']][$modName] = $mod;
			
		}

		foreach ($uriMods as $uri=>$mods) {
		//$uri = $this->_prepareMod($uri);
                $ch = curl_init();
		    curl_setopt_array($ch, array(
                CURLOPT_URL =>  $uri,
                CURLOPT_RETURNTRANSFER => true,
            ));
            $response = curl_exec($ch);
			curl_close($ch);
			
			
			
	      if ($response===false) {
	      
	      $ch = curl_init();
		    curl_setopt_array($ch, array(
                CURLOPT_URL =>  $uri,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
            ));
               $response = curl_exec($ch);
			curl_close($ch);
	      }
			
            if ($response===false) {
                Mage::throwException($this->__('Error while downloading file: %s', curl_error($ch)));
            }
            /*
            $response = @file_get_contents($uri);
            if (!$response) {
                Mage::throwException($this->__('Invalid meta uri resource: %s', $uri));
            }
            */
            //$xml = new Varien_Simplexml_Element($response);
            try {
                $result = Zend_Json::decode($response);
            } catch (Exception $e) {
                if ($e->getMessage()=='Decoding failed: Syntax error') {
                    $result = array();
                } else {
                    throw $e;
                }
            }
		    foreach ((array)$result as $modName=>$node) {
			
                if (!$modName || empty($mods[$modName]) || !isset($node['version']['latest'])) {
                    continue;
                }

                $mods[$modName]->setLastChecked(now())->setRemoteVersion((string)$node['version']['latest'])->save();
            }
        }
    }

    public function cleanCache($filePath = '')
    {
        Mage::app()->cleanCache();
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
            apc_clear_cache('user');
        }
        
        if($filePath != ''){
        $this->_oblit($filePath);
        $this->_oblit(str_replace("mextensionmanager/download","mextensionmanager/unpacked",$filePath));
        }
        
        return $this;
    }
    
    

    public function installModules($uris)
    {
        set_time_limit(0);
        foreach ($uris as $uri) {
            if (empty($uri)) {
                continue;
            }
            $filePath = $this->download($uri);
            $this->install($uri, $filePath);
        }
        $this->cleanCache($filePath);
    }
    
    
   public function _oblit($path)
   {
    if (is_dir($path) === true)
    {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file)
        {
            $this->_oblit(realpath($path) . '/' . $file);
        }

        return rmdir($path);
    }

    else if (is_file($path) === true)
    {
        return unlink($path);
    }

    return false;
  }

    public function upgradeModules($modules)
    {
        set_time_limit(0);
        $modules = Mage::getModel('mextensionmanager/module')->getCollection()
            ->addFieldToFilter('module_id', array('in'=>$modules));
        foreach ($modules as $mod) {
            $uri = $mod->getDownloadUri();
			$moduleName = $mod->getModuleName();
			
            $filePath = $this->download($uri,$moduleName);
            $this->install($uri, $filePath);
        }
        $this->cleanCache($filePath);
    }
    
    public function _getmodHelper($moduleName)
    {
     if($moduleName == "Mageix_Ixcbadv"){
       $modHelper = "ixcbadv";
     }elseif ($moduleName == "Mageix_ExtensionManager"){
       $modHelper = "mextensionmanager";
     }elseif ($moduleName == "Mageix_ExtensionManagerLicense"){
       $modHelper = "extensionmanagerlicense";
     }elseif ($moduleName == "Mageix_Ixepp"){
       $modHelper = "ixepp";
     }elseif ($moduleName == "Mageix_Diminishingdiscounts"){
       $modHelper = "diminishingdiscounts";
     }elseif ($moduleName == "Mageix_OnepageCheckout"){
       $modHelper = "onepagecheckout";
     }
     return $modHelper;
    }
    
    
    public function _removeModule($moduleId)
    {
     $modManagerModel = Mage::getModel('mextensionmanager/module')->setModuleId($moduleId);
     $modManagerModel->delete();

    }
    
    
    public function _removeLicense($licenseKey)
    {
    
     $licenseCollection = Mage::getModel('extensionmanagerlicense/license')
                           ->getCollection()
                           ->addFieldToFilter('license_key',$licenseKey);
                           
     foreach ($licenseCollection as $licenseRow)
     {
        $license_id = $licenseRow->getLicenseId();
        $licManagerModel = Mage::getModel('extensionmanagerlicense/license')->setLicenseId($license_id);
        $licManagerModel->delete();
     }
    
    }
    
    
    
    
    public function uninstallModules($modules)
    {

   set_time_limit(0);
   $modules = Mage::getModel('mextensionmanager/module')->getCollection()
            ->addFieldToFilter('module_id', array('in'=>$modules));
  foreach ($modules as $module)
     {
       $modHelper = $this->_getmodHelper($module->getModuleName());

       
       $dirs = Mage::helper($modHelper)->_rrtmc();
       $dires = array_filter($dirs);
      if(!empty($dires)){
        foreach ($dirs as $dir)
        {
         $this->_oblit($dir);
        }
        
        $this->_removeModule($module->getModuleId());
        $this->_removeLicense($module->getLicenseKey());
       }
     }
        
        $this->cleanCache();
    }
    
}