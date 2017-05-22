<?php
/**
 * ClickTale - PHP Integration Module
 *
 * LICENSE
 *
 * This source file is subject to the ClickTale(R) Integration Module License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.clicktale.com/Integration/0.2/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@clicktale.com so we can send you a copy immediately.
 *
 */
?>
<?php
	/*
	ClickTale Control Script v1.4 08.18.2009
	Copyright 2008 ClickTale Ltd.
	*/
	define ("ON", "on");
	define ("OFF", "off");
	define ("ERROR", -1);
	define ("COUNTRY_COOKIE","CT_c_c"); // ClickTale Country Cookie

	/* Return values for all functions 
	   1 - User passed filter.
	   0 - User did not pass filter */
	class ClickTaleControl 	
	{
		public $settings;

		/***********************************************
		*	constructor, loads configuration settings file.
		***********************************************/
		public function __construct($config_file = "ClickTaleControl.ini") 
		{
			$this->settings = @parse_ini_file($config_file, TRUE) or exit("// [ClickTale] ERROR: Unable to open config file ($config_file) */");
		}
		
		/***********************************************
		*	if [ignore_classification] is not defined in settings or set to ON then returns 1 (true) otherwise 0 (false)
		*	
		*	ON - don't use WRUID cookie value for optimization of delivery (needed if the script is on a different domains than the page)
		*	OFF - use WRUID cookie
		***********************************************/
		public function ignore_classification() 
		{
			if (isset($this->settings['ignore_classification']) &&  $this->settings['ignore_classification'] == ON) return 1;
			return 0;
		} 
		
		/***********************************************
		*	if [rec_repeat_always] is not defined in settings or set to ON then returns 1 (true) otherwise 0 (false)
		*	
		*	ON - will always record a user who has been previously marked for recording
		*	OFF - will record a user who has been previously marked for recording based on filters
		***********************************************/
		public function rec_repeat() 
		{
			if (isset($this->settings['rec_repeat_always']) &&  $this->settings['rec_repeat_always'] == ON) return 1;
			return 0;
		} 
		
		/***********************************************
		*	if [useragent][filter] is not defined in settings or set to OFF then returns 1 (true) otherwise checks:
		*		if [useragent][filter] is equal to "whitelist" then checks:
		*			if current useragent matches one of the filters that in the list of [useragent_list] then returns 1 (true) otherwise return 0 (false)
		*		if [useragent][filter] is equal to "blacklist" then checks:
		*			if current useragent matches one of the filters that in the list of [useragent_list] then returns 0 (false) otherwise return 1 (true)
		***********************************************/
		public function useragent_filter() 
		{
			if (isset($this->settings['useragent']['filter']) && $this->settings['useragent']['filter'] != OFF) 
			{
				if ($this->bad_mode_setting($this->settings['useragent']['filter'])) exit("// [ClickTale] ERROR: Invalid setting for [useragent] \"filter\". */");
				$list = $this->settings['useragent_list'] or exit("// [ClickTale] ERROR: [useragent_list] does not exist. */");
				$useragent = $_SERVER['HTTP_USER_AGENT'];
				foreach($list as $text) 
				{
					$match = preg_match($text,$useragent);
					
					if ($match != 0) 
					{
						break;
					}
				}
				if (strtolower($this->settings['useragent']['filter']) == "blacklist" && $match) return 0;
				if (strtolower($this->settings['useragent']['filter']) == "whitelist" && empty($match)) return 0;
			}
			return 1;
		}
		
		/***********************************************
		*	if [day] is not defined in settings or set to OFF then returns 1 (true) otherwise checks:
		*		if current weekday is in the list of [day] then returns 1 (true) otherwise return 0 (false)
		***********************************************/
		public function day_filter() 
		{
			if (isset($this->settings['day']) && $this->settings['day'] != OFF) 
			{
				$this->settings['day'] = str_replace(" ","",$this->settings['day']);
				$dayArr = explode(",", $this->settings['day']);
				if (!in_array(date("N"), $dayArr)) return 0;
			}
			return 1;
		}
		
		/***********************************************
		*	if [time] is not defined in settings or set to OFF then returns 1 (true) otherwise checks:
		*		if [time_filter] is equal to "record" then checks:
		*			if current time is between [time_start] and [time_stop] based on [time_zone] then return 1 (true) otherwise 0 (false)
		*		if [time_filter] is equal to "norecord" then checks:
		*			if current time is between [time_start] and [time_stop] based on [time_zone] then return 0 (false) otherwise 1 (true)
		***********************************************/
		public function time_filter() 
		{
			if (isset($this->settings['time']) && $this->settings['time'] != OFF) 
			{
				$time_filter = strtolower($this->settings['time']);
				if ($time_filter != "record" && $time_filter != "norecord") exit("// [ClickTale] ERROR: Invalid setting for [time]. */");
				if (isset($this->settings['time_start']) && isset($this->settings['time_stop'])) 
				{
					if (isset($this->settings['time_zone'])) date_default_timezone_set($this->settings['time_zone']);
					$time_stop = strtotime($this->settings['time_stop']);
					$time_start = strtotime($this->settings['time_start']);
					/* if ($this->settings['time_start'] > $this->settings['time_stop']) {
						$time_stop = mktime(date("H",$time_stop),date("i",$time_stop),0,date("n"),date("j")+1,date("Y"));
					} */
					$result  = (time()>$time_start && time()<$time_stop);
					if ($time_filter == "record" && !$result) return 0;
					if ($time_filter == "norecord" && $result) return 0;
				} else exit("// [ClickTale] ERROR: time_start/stop not set. */");
			}
			return 1;
		}
		
		/***********************************************
		*	if [country][filter] is not defined in settings or set to OFF then returns 1 (true) otherwise checks:
		*		if [country][filter] is equal to "whitelist" then checks:
		*			if current country is in the list of [country_list] then returns 1 (true) otherwise return 0 (false)
		*		if [country][filter] is equal to "blacklist" then checks:
		*			if current country is in the list of [country_list] then returns 0 (false) otherwise return 1 (true)
		*
		*	for better performance the current country is stored in cookie
		***********************************************/
		public function country_filter() 
		{
			if (isset($this->settings['country']['filter']) && $this->settings['country']['filter'] != OFF) 
			{
				if ($this->bad_mode_setting($this->settings['country']['filter'])) exit("// [ClickTale] ERROR: Invalid setting for [country] \"filter\". */");
				if (!isset($_COOKIE[COUNTRY_COOKIE])) 
				{				
					if (isset($this->settings['country']['mode']) && strtolower($this->settings['country']['mode'])=="http")
						$country = getCountryIP(getIP());
					else if (isset($this->settings['country']['mode']) && strtolower($this->settings['country']['mode'])=="db")
						$country = getCountryDB(getIP(),$this->settings['country']['db_path']);
					else exit("// [ClickTale] ERROR: Invalid setting for [country] \"mode\". */");
					if ($country == "") $country = "xx";
					$country = strtolower($country);
					setcookie(COUNTRY_COOKIE, $country, time() + 3600*24*30, '/');
				} else $country = $_COOKIE[COUNTRY_COOKIE];
				$country_list = $this->settings['country_list'] or exit("// [ClickTale] ERROR: Countries not defined. */");
				if (strtolower($this->settings['country']['filter']) == "blacklist" && in_array($country, $country_list)) 
					return 0;
				if (strtolower($this->settings['country']['filter']) == "whitelist" && !in_array($country, $country_list)) 
					return 0;
			}
			return 1;
		}
	
		/***********************************************
		*	if [ip][filter] is not defined in settings or set to OFF then returns 1 (true) otherwise checks:
		*		if [ip][filter] is equal to "whitelist" then checks:
		*			if current ip is in the list of [ip_list] then returns 1 (true) otherwise return 0 (false)
		*		if [ip][filter] is equal to "blacklist" then checks:
		*			if current ip is in the list of [ip_list] then returns 0 (false) otherwise return 1 (true)
		*
		*	if one of ip that defined in [ip_list] has mask then it also checked against current ip based on the mask
		***********************************************/
		public function ip_filter() 
		{
			if (isset($this->settings['ip']['filter']) && $this->settings['ip']['filter'] != OFF) 
			{
				if ($this->bad_mode_setting($this->settings['ip']['filter'])) exit("// [ClickTale] ERROR: Invalid setting for [ip] \"filter\". */");
				$list = $this->settings['ip_list'] or exit("// [ClickTale] ERROR: [ip_list] does not exist. */");
				foreach($list as $filterIP) 
				{
					if  (($mask=substr_count($filterIP, "*")) == 0) 
					{ 
						$filterIP = explode("/", $filterIP);
						$mask = $filterIP[1];
						$filterIP = $filterIP[0];						
						if (substr_count($mask, ".") == 0) $mask = bytesToMask($mask);
						else $mask = ipToInt($mask);
					} else {
						if ($mask == 4) $mask = 0;
						else $mask = bytesToMask(32-8*$mask);
						$filterIP = str_replace("*","0",$filterIP);
					}
					$userIP = ipToInt(getIP());
					$filterIP = ipToInt($filterIP);	
					//print("userIP: " . $userIP . ", filterIP: " . $filterIP . "  |  ");
					if ($mask == 0) // No mask supplied
						$match = (floatval($filterIP) == floatval($userIP));
					else
						$match = (floatval($filterIP&$mask) == floatval($userIP&$mask));
					if ($match != 0) 
					{
						break;
					}
				}
				if (strtolower($this->settings['ip']['filter']) == "blacklist" && $match) return 0;
				if (strtolower($this->settings['ip']['filter']) == "whitelist" && empty($match)) return 0;
			}
			return 1;
		}
			
		/***********************************************
		*	if [referer][filter] is not defined in settings or set to OFF then returns 1 (true) otherwise checks:
		*		if [referer][filter] is equal to "whitelist" then checks:
		*			if referer url is in the list of [ref_list] then returns 1 (true) otherwise return 0 (false)
		*		if [referer][filter] is equal to "blacklist" then checks:
		*			if referer url is in the list of [ref_list] then returns 0 (false) otherwise return 1 (true)
		***********************************************/
		public function ref_filter() 
		{
			if (isset($this->settings['referer']['filter']) && $this->settings['referer']['filter'] != OFF) 
			{
				$result = $this->refURL_filter($_GET['ref'],$this->settings['referer'],$this->settings['ref_list']);
				if ($result == ERROR) exit("// [ClickTale] ERROR: Referer rule not configured correctly. */");
				else return $result;
			}
			return 1;
		}

		/***********************************************
		*	if [url][filter] is not defined in settings or set to OFF then returns 1 (true) otherwise checks:
		*		if [url][filter] is equal to "whitelist" then checks:
		*			if referer url is in the list of [url_list] then returns 1 (true) otherwise return 0 (false)
		*		if [url][filter] is equal to "blacklist" then checks:
		*			if referer url is in the list of [url_list] then returns 0 (false) otherwise return 1 (true)
		***********************************************/
		public function url_filter() 
		{
			if (isset($this->settings['url']['filter']) && $this->settings['url']['filter'] != OFF) 
			{
				$callpage = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] :
					 ((!empty($_ENV['HTTP_REFERER'])) ? $_ENV['HTTP_REFERER'] : @$HTTP_REFERER);
				$result = $this->refURL_filter($callpage,$this->settings['url'],$this->settings['url_list']);
				if ($result == ERROR) exit ("// [ClickTale] ERROR: URL rule not configured correctly. */");
				else return $result;
			}
			return 1;
		}
			
		/***********************************************
		*	if [$filter_type] is equal to "whitelist" then checks:
		*		if [$user_ref] is in [$list] based on [mode - (regex/simple)] then returns 1 (true) otherwise return 0 (false)
		*	if [$filter_type] is equal to "blacklist" then checks:
		*		if [$user_ref] is in [$list] based on [mode - (regex/simple)] then returns 0 (false) otherwise return 1 (true)
		***********************************************/
		private function refURL_filter($user_ref, $settings, $list) 
		{
				$filter_type = strtolower($settings['filter']);
				$mode = strtolower($settings['mode']);
				if ($this->bad_mode_setting($filter_type)) return ERROR;
				if (empty($list)) return ERROR;
				if (!empty($mode) && $mode == "simple") 
				{
					foreach ($list as $r) 
					{
						if ($r != "") $matches = substr_count($user_ref, trim($r));
						if ($matches > 0) break;
					}
				} else if (!empty($mode) && $mode == "regex") 
				{
					foreach ($list as $pattern) 
					{
						if ($pattern != "") $matches = preg_match($pattern, $user_ref);
						if ($matches > 0) break;
					}
					
				} else return ERROR;
				if ($filter_type == "whitelist" && $matches == 0) return 0; // Not in the Whitelist.
				if ($filter_type == "blacklist" && $matches >  0) return 0; // the Ref-URL is Blacklisted.
				return 1;
		}				
			
		/***********************************************
		*	if [$setting] is equal to "whitelist" or "blacklist" then returns 1 (true) otherwise return 0 (false)
		***********************************************/
		public function bad_mode_setting($setting) 
		{
			if (strtolower($setting) != "blacklist" && strtolower($setting) != "whitelist") return 1;
			return 0;
		}
			
		/***********************************************
		*	if all of filters defined in settings are matched then returns 1 (true) otherwise return 0 (false)
		***********************************************/
		public function filter_all() 
		{
			if (!$this->day_filter()) return 0;		
			if (!$this->time_filter()) return 0;		
			if (!$this->url_filter()) return 0;		
			if (!$this->ref_filter()) return 0;		
			if (!$this->ip_filter()) return 0;		
			if (!$this->country_filter()) return 0;		
			if (!$this->useragent_filter()) return 0;		
			
			return 1;
		}
		
		/***********************************************
		*	 Debug function.
		***********************************************/
		public function print_status() 
		{
			echo "/* ClickTale Control Script Debug\n";
			echo " * Version: ".CT_VERSION." ".CT_VERSIION_DATE."\n";
			// 1 - passed filter, 0 - did not pass filter.
			echo " * Day: ".$this->day_filter()."\n";
			echo " * Time: ".$this->time_filter()."\n";
			echo " * Country: ".$this->country_filter()."\n";
			echo " * IP: ".$this->ip_filter()."\n";
			echo " * UserAgent: ".$this->useragent_filter()."\n";;
			echo " * Referer: ".$this->ref_filter();
			if (isset($_GET['ref'])) echo " [".$_GET['ref']."]";
			echo "\n";
			echo " * URL: ".$this->url_filter()." [".$_SERVER['HTTP_REFERER']."]\n";
			echo " */\n";
		}
	}


	/***********************************************
	*	Returns current ip address
	***********************************************/
	function getIP() 
	{
		$ip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] :
			 ((!empty($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : @$REMOTE_ADDR);
		return trim($ip);
	}

	/***********************************************
	*	Converts $ip address to numeric value, if is IPv4 converts to int type otherwise if is IPv6 converts to long type
	***********************************************/
	function ipToInt($ip) 
	{
		// if mode of ip is IPv4 
		if (strpos($ip, '::') === false)
		{
			$ip = explode(".", $ip);
			return ($ip[3] + 256*$ip[2] + 256*256*$ip[1] + 256*256*256*$ip[0]);
		}	
		// if mode of ip is IPv6
		else 
			return IPv6ToLong($ip, 1);
	}
	
	/***********************************************
	*	Replace '::' with appropriate number of ':0'
	***********************************************/
	function ExpandIPv6Notation($Ip) {
		if (strpos($Ip, '::') !== false)
			$Ip = str_replace('::', str_repeat(':0', 8 - substr_count($Ip, ':')).':', $Ip);
		if (strpos($Ip, ':') === 0) $Ip = '0'.$Ip;
		return $Ip;
	}

	/***********************************************
	 * Convert IPv6 address to an number
	 *
	 * Optionally split in to two parts.
	 ***********************************************/
	function IPv6ToLong($Ip, $DatabaseParts= 2) {
		$Ip = ExpandIPv6Notation($Ip);
		$Parts = explode(':', $Ip);
		$Ip = array('', '');
		for ($i = 0; $i < 4; $i++) $Ip[0] .= str_pad(base_convert($Parts[$i], 16, 2), 16, 0, STR_PAD_LEFT);
		for ($i = 4; $i < 8; $i++) $Ip[1] .= str_pad(base_convert($Parts[$i], 16, 2), 16, 0, STR_PAD_LEFT);

		if ($DatabaseParts == 2)
				return array(base_convert($Ip[0], 2, 10), base_convert($Ip[1], 2, 10));
		else    return base_convert($Ip[0], 2, 10) + base_convert($Ip[1], 2, 10);
	}
	
	/***********************************************
	*	 
	***********************************************/
	function bytesToMask($bytes) 
	{
		$mask = str_pad("", $bytes, "1");
		$mask = str_pad($mask, 32, "0");
		$mask = str_split($mask, 8);
		/*$mask = rtrim(chunk_split($mask, 8, "."),".");
		$mask = explode(".", $mask); // PHP4 */
	
		for ($i=0; $i<4; $i++)
			$mask[$i]=base_convert($mask[$i], 2, 10);
		return ipToInt(implode(".",$mask));
	}

	/***********************************************
	*	 Returns country based on ip address.
	***********************************************/
	function getCountryIP($ip) 
	{
		ob_start();
   		readfile("http://api.hostip.info/country.php?ip=$ip");
   		$country = ob_get_contents();
   		ob_end_clean();
		$country = strtolower($country);
		return trim($country); 
	}

	/***********************************************
	* This product includes GeoLite data created by MaxMind, available from http://www.maxmind.com/
	***********************************************/
	function getCountryDB($ip, $path) 
	{
		require_once($path."geoip.inc");
		$gi = geoip_open($path."GeoIP.dat",GEOIP_STANDARD);
		$country = geoip_country_code_by_addr($gi, $ip);
		geoip_close($gi);
		return strtolower(trim($country));
	}
	
	/***********************************************
	* Displays ClickTale execution script
	***********************************************/
	function exec_script($url) 
	{
   		$path = pathinfo($url);
		if (preg_match("/^http:/",$path['dirname'])) {
			$script = @file_get_contents($url);
			if ($script === false) exit("// [ClickTale] ERROR: Unable to read $url.\n");
			else echo $script;
		} else exit("// [ClickTale] ERROR: Only HTTP requests allowed.");
	}
?>
