<?php
	final class Mageix_ExtensionManagerLicense_Helper_Relay {
		private static $_macRegExp = "/([0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2})/i";
		private static $_licenseApiUrl = "https://ixcba.com/ixval/index/";
		private static $_s = "2OOxGXdd0vGTPk7!kmN\$";
		private static $_obfuscateKey;
		
		
		static function callApi($action, $data) {
				
			$key_length = strlen($data['license_key']);
			
			Mage::getSingleton('core/session')->setCycleLicenceKey($data['license_key']);

			if ($key_length == 39){
				$_licenseApiUrl = "https://ixcba.com/ixval/index/";
			}else {
				$_licenseApiUrl = "https://mageix.com/ixval/index/";
			}

			$curl = curl_init(  );
			curl_setopt( $curl, CURLOPT_URL,$_licenseApiUrl.$action );
			curl_setopt( $curl, CURLOPT_POST, 1 );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
			curl_setopt( $curl, CURLOPT_HEADER, 1 );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
			$response = curl_exec( $curl );
			$result = array( 'curl_error' => '', 'http_code' => '', 'header' => '', 'body' => '' );

			if ($error = curl_error( $curl )) {
				$result['curl_error'] = $error;
				return $result;
			}

			$result['http_code'] = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
			$headerSize = curl_getinfo( $curl, CURLINFO_HEADER_SIZE );
			$result['header'] = substr( $response, 0, $headerSize );
			$result['body'] = substr( $response, $headerSize );
			curl_close( $curl );
			
			
			return $result;
		}

	private static function serverMACs( )
    {
			$macs = array(  );

			if (strpos( strtolower( PHP_OS ), 'win' ) === 0) {
				exec( 'ipconfig /all | find "Physical Address"', $output );
			} 
else {
				exec( '/sbin/ifconfig -a | grep -i "HWaddr"', $output );
			}

			foreach ($output as $line) {
				if ( preg_match( self::$_macRegExp, $line, $m ) )
				{
					$macs[] = $m[1];
					continue;
				}
			}

			return $macs;
		}

		private static function licenseSignatureString( $d )
    {
        if ( is_object( $d ) )
        {
			$d = $d->getData(  );
		}

			 return str_replace( "\r\n", "\n", ( !empty( $d['license_key'] ) ? $d['license_key'] : "" )."|".( !empty( $d['license_status'] ) ? $d['license_status'] : "" )."|".( !empty( $d['products'] ) ? $d['products'] : "" )."|".( !empty( $d['server_restriction'] ) ? $d['server_restriction'] : "" )."|".( !empty( $d['license_expire'] ) ? $d['license_expire'] : "" )."|".( !empty( $d['upgrade_expire'] ) ? $d['upgrade_expire'] : "" ) );
		}
		private static function licenseSignature( $d )
		{
			return empty( $d ) ? "" : sha1( self::$_s."|".self::licenseSignatureString( $d ) );
		}
		
		public static function sendServerInfo( )
		{	
			$licenses = Mage::getModel( 'extensionmanagerlicense/license' )->getCollection(  );
			$keys = array(  );
			foreach ($licenses as $license) {
				$keys[] = $license->getLicenseKey(  );
			}

			$data = array( "license_keys" => join( "\n", $keys ),  "mac_addresses" => Zend_Json::encode( self::serverMACs(  ) ), "http_host" => $_SERVER['HTTP_HOST'], "server_name" => $_SERVER['SERVER_NAME'], "server_addr" => $_SERVER['SERVER_ADDR'] );
			$result = self::callApi( 'server_info/', $data );
			return $result;
		}

		public static function retrieveLicense( $key, $installModules = false)
		{	
			$license = (is_object( $key ) ? $key : Mage::getModel( 'extensionmanagerlicense/license' )->load( $key, 'license_key' ));
			
			$key = ($license->getLicenseKey() ? $license->getLicenseKey() : $key);
			$currentUrl = Mage::helper('core/url')->getCurrentUrl();
			$data = array( 'license_key' => $key, 'license' => Zend_Json::encode( $license->getData(  ) ), 'signature' => self::licenseSignature( $license ), 'signature_string' => self::licenseSignatureString( $license ),'HTTP_HOST'=>$_SERVER['HTTP_HOST'],'ENV'=>$currentUrl );

			$result = self::callApi( 'license/', $data );
			
			if ($result['curl_error']) {
				$error = 'Mageix_ExtensionManagerLicense connection error while retrieving license: ' . $result['curl_error'];

				if (!$license->getId(  )) {
					throw new Mageix_ExtensionManagerLicense_Exception( $error );
				} 
else {
					$license->setLastStatus( 'curl_error' )->setLastError( $result['curl_error'] )->setRetryNum( $license->getRetryNum(  ) + 1 )->save(  );
					Mage::log( $error );
					return false;
				}
			}


			if ($result['http_code'] != 200) {
				$error = 'Mageix_ExtensionManagerLicense http error while retrieving license: ' . $result['http_code'];

				if (!$license->getId(  )) {
					throw new Mageix_ExtensionManagerLicense_Exception( $error );
				} 
else {
					$license->setLastStatus( 'http_error' )->setLastError( $result['http_code'] . ': ' . $result['body'] )->setRetryNum( $license->getRetryNum(  ) + 1 )->save(  );
					Mage::log( $error );
					return false;
				}
			}

			$data = Zend_Json::decode( $result['body'] );
			//print_r($result['body']);print_r($data);die;
			if (( !$result['body'] || !$data )) {
				$error = 'Mageix_ExtensionManagerLicense decoding error while retrieving license: <xmp>' . $result['body'] . '</xmp>';

				if (!$license->getId(  )) {
					throw new Mageix_ExtensionManagerLicense_Exception( $error );
				} 
else {
					$license->setLastStatus( "body_error" )->setLastError( $result['headers']."\n\n".$result['body'] )->setRetryNum( $license->getRetryNum( ) + 1 )->save( );
					Mage::log( $error );
					return false;
				}
			}


			if ($data['status'] == 'error') {
				$error = $key . ': ' . $data['message'];

				if ($license->getId(  )) {
					$license->setLastStatus( 'status_error' )->setLastError( $error )->setRetryNum( $license->getRetryNum(  ) + 1 )->save(  );
					Mage::log( $error );
				}

					throw new Mageix_ExtensionManagerLicense_Exception( $error );
			}

			$license->addData( array( 'license_key' => $key, 'license_status' => $data['license_status'], 'last_checked' => now(  ), 'last_status' => $data['status'], 'retry_num' => 0, 'products' => join( '\n', array_keys( $data['modules'] ) ), 'server_restriction' => $data['server_restriction'], 'license_expire' => $data['license_expire'], 'upgrade_expire' => $data['upgrade_expire'] ) )->setSignature( self::licenseSignature( $license ) )->save(  );
			if (!empty( $data['modules'] )) {
				$uris = array(  );
				foreach ($data['modules'] as $name => $m) {
					if (!$name) {
						continue;
					}

					$module = Mage::getModel( 'mextensionmanager/module' )->load( $name, 'module_name' );

					if (!$module) {
						continue;
					}
					
					$module->addData( array( 'module_name' => $name, 'download_uri' => $m['download_uri'], 'last_checked' => now(  ), 'remote_version' => $m['remote_version'], 'license_key' => $license->license_key ) )->save(  );
					$uris[] = $m['download_uri'];
				}

				Mage::helper( 'mextensionmanager' )->checkUpdates(  );

				if ($installModules) {
					Mage::helper( 'mextensionmanager' )->installModules( $uris, Mage::app(  )->getRequest(  )->getPost( 'ftp_password' ) );
				}
			}

		}

		public static function validateLicenseServer( $server )
		{	
			if (!($server = trim( $server ))) {
				return false;
			}

			if ( $server[0] === "{" && preg_match( self::$_macRegExp, $server, $m ) )
			{
				return in_array( $m[1], self::serverMACs(  ) );
			}

			
			$ip = explode( "@", $server[1] ) + array( 1 => "" );
			$domain = explode( "@", $server[0] ) + array( 1 => "" );
			list( $domain, $ip ) = explode( '@', $server ) + array( 1 => '' );
			if (!( $domain === '' || $domain === '*' )) {
				$re = '#^' . str_replace( '\*', '.*', preg_quote( $domain ) ) . '$#i';

				if (!( preg_match( $re, $_SERVER['SERVER_NAME'] ) || preg_match( $re, $_SERVER['HTTP_HOST'] ) )) {
					return false;
				}
			}


			if (!( $ip === '' || $ip === '*' )) {
				$re = '#^' . str_replace( '\*', '.*', preg_quote( $ip ) ) . '$#i';

				if (!preg_match( $re, $_SERVER['SERVER_ADDR'] )) {
					return false;
				}
			}

			return true;
		}

		public static function validateLicense( $key )
		{
			$license = (is_object( $key ) ? $key : Mage::getModel( 'extensionmanagerlicense/license' )->load( $key, 'license_key' ));

			if (!$license->getId(  )) {
			throw new Mageix_ExtensionManagerLicense_Exception( 'License record is not found: ' . $key );
			}

			$key = $license->license_key;

			if (( !Mage::app(  )->loadCache( 'ulicense_' . $key ) && ( !$license->getAuxChecksum(  ) || 2147483647 - $license->getAuxChecksum(  ) < time(  ) - 86400 ) )) {
				Mage::app(  )->saveCache( '1', 'ulicense_' . $key, array( 'ulicense' ), 3600 );
				$license->setAuxChecksum( 2147483647 - time(  ) )->save(  );
				self::retrieveLicense( $license->getLicenseKey(  ) );
			}

			$errors = array( 'inactive' => 'The license is not active', 'expired' => 'The license has expired', 'invalid' => 'The license is not valid for the current server' );

			if (!empty( $errors[$license->getLicenseStatus(  )] )) {
				throw new Mageix_ExtensionManagerLicense_Exception( $errors[$license->getLicenseStatus( )].": ".$license->getLicenseKey( ) );
			}

			$expires = $license->getLicenseExpire(  );

			if (( $license->getLicenseStatus(  ) == 'expired' || ( $expires && strtotime( $expires ) < time(  ) ) )) {
				$license->setLicenseStatus( 'expired' )->setSignature( self::licenseSignature( $license ) )->save(  );
				throw new Mageix_ExtensionManagerLicense_Exception( $errors['expired'].": ".$license->getLicenseKey( ) );
			}


			if (( PHP_SAPI !== 'cli' && $license->getServerRestriction(  ) )) {
				$servers = explode( "\n", $license->getServerRestriction( ) );			
				$found = false;
				foreach ($servers as $server) {
					if (self::validateLicenseServer( $server )) {
						$found = true;
						break;
					}
				}


				if (!$found) {
					$msg = $errors['invalid'] . ': ' . $license->getLicenseKey(  ) . ' ';
					$msg = 'SERVER_NAME: ' . (!empty( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : 'null') . '; ';
					$msg = 'HTTP_HOST: ' . (!empty( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : 'null') . '; ';
					$msg = 'SERVER_ADDR: ' . (!empty( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : 'null' . '; ');
					throw new Mageix_ExtensionManagerLicense_Exception( $msg );
				}
			}

			return $license;
		}

		public static function obfuscate($key) {
			self::$_obfuscateKey = $key;
		}

		public static function validateModuleLicense($name) {
			$module = (is_object( $name ) ? $name : Mage::getModel( 'mextensionmanager/module' )->load( $name, 'module_name' ));

			if (!$module->getId(  )) {
				throw new Mageix_ExtensionManagerLicense_Exception( "Module record not found: ".( is_object( $name ) ? $name->getModuleName( ) : $name ) );
			}

			$license = self::validateLicense( $module->getLicenseKey(  ) );
			$licenseProducts = explode( "\n", $license->getProducts( ) );

			if (!in_array( $name, $licenseProducts )) {
				throw new Mageix_ExtensionManagerLicense_Exception( "Module ".$module->getModuleName( )." is not covered by license: ".$module->getLicenseKey( ) );
			}

			return self::$_obfuscateKey ? sha1( self::$_obfuscateKey.$module->getModuleName( ) ) : true;
		}
	}

?>