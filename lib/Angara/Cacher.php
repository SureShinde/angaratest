<?php
class Angara_Cacher {
	static function get($key){
		if (class_exists('Angara_Memcache')) {
			try{
				return self::cache()->get($key);
			}
			catch(Exception $e){
				// log exception
			}
		} else {
			return false;
		}
	}
	
	static function set($key, $value, $time = 0){
		if (class_exists('Angara_Memcache')) {
			try{
				return self::cache()->set($key, $value, false, $time);
			}
			catch(Exception $e){
				// log exception
			}
		} else {
			return false;
		}
	}
	
	static function cache() {
		if (class_exists('Angara_Memcache')) {
			try{
				return Angara_Memcache::cache();
			}
			catch(Exception $e){
				// log exception
			}
		} else {
			return false;
		}
	}

	static function flushCache() {
		if (class_exists('Angara_Memcache')) {
			try{
				return Angara_Memcache::flushCache();
			}
			catch(Exception $e){
				// log exception
			}
		} else {
			return false;
		}
	}

	static function stats() {
		if (class_exists('Angara_Memcache')) {
			try{
				return Angara_Memcache::stats();
			}
			catch(Exception $e){
				// log exception
			}
		} else {
			return false;
		}
	}
}