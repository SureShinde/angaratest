<?php
class Angara_Helloworld_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $key;
	protected $_cache;
	
	protected $data;
	protected $tags = array(__CLASS__);
	
	public function canUseCache() {
		//verify if the cache is enabled 
		return Mage::app()->useCache('block_html'); 
	}
	
	protected function _getCacheObject()
    {
        if (!$this->_cache) {
            $this->_cache = Mage::app()->getCache();
        }
        return $this->_cache;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
	
	public function saveDataInCache()
    {
        if ($this->canUseCache()) {
            $cookie = Mage::getModel('core/cookie');
            $this->_getCacheObject()->save(serialize($this->data), $this->key, $this->tags, $cookie->getLifetime());
        }
        return $this;
    }

    public function getDataFromCache($key)
    {
        $this->key = $key;
        return $this->_getCachedData();
    }
	
	public function clearDataInCache($key){
		$this->_getCacheObject()->remove($key);
		return $this;
	}

    protected function _getCachedData($key = null)
    {
        if ($key !== null) {
            $this->key = $key;
        }
        /** * clear the data variable */
        $this->data = false;
        if ($data = $this->_getCacheObject()->load($this->key)) {
            $this->data = unserialize($data);
        }
        return $this->data;
    }
}
