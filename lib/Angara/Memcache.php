<?php
if(class_exists('Memcache')){
class Angara_Memcache extends Memcache {
	static private $MEMCACHE_SERVERS = array(
		"127.0.0.1", //ADD SERVER IP 1 HERE
	);
	static public $memcacheObj = NULL;
	static function cache() {
		if (self::$memcacheObj == NULL) {
			self::$memcacheObj = new Memcache;
			foreach(self::$MEMCACHE_SERVERS as $server){
				self::$memcacheObj->addServer ($server);
			}
		}
		return self::$memcacheObj;
	}

	static function flushCache() {
		if (self::$memcacheObj == NULL) {
			self::cache();
		}
		return self::$memcacheObj->flush();
	}

	static function stats() {
		if (self::$memcacheObj == NULL) {
			self::cache();
		}
		return self::$memcacheObj->getExtendedStats();
	}
}
}