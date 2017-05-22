<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Sphinx Search Ultimate
 * @version   2.2.9
 * @revision  370
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


if (!@class_exists('SphinxClient')) {
    include Mage::getBaseDir().DS.'lib'.DS.'Sphinx'.DS.'sphinxapi.php';
}

class Mirasvit_SearchSphinx_Model_Engine_Sphinx extends Mirasvit_SearchIndex_Model_Engine
{
    const SEARCHD                   = 'searchd';
    const INDEXER                   = 'indexer';
    const REINDEX_SUCCESS_MESSAGE   = 'rotating indices: succesfully sent SIGHUP to searchd';
    const PAGE_SIZE                 = 1000;

    protected $_config              = null;
    protected $_configFilepath      = null;
    protected $_synonymsFilepath    = null;
    protected $_stopwordsFilepath   = null;
    protected $_indexerCommand      = null;
    protected $_searchdCommand      = null;
    protected $_sphinxCfgTpl        = null;
    protected $_sphinxSectionCfgTpl = null;

    protected $_spxHost             = null;
    protected $_spxPort             = null;

    protected $_matchMode           = null;

    public function __construct()
    {
        $binPath = Mage::getStoreConfig('searchsphinx/advanced/bin_path');

        $this->_config              = Mage::getSingleton('searchsphinx/config');
        $this->_configFilepath      = Mage::getBaseDir('var').'/sphinx/sphinx.conf';
        $this->_synonymsFilepath    = Mage::getBaseDir('var').'/sphinx/synonyms.txt';
        $this->_stopwordsFilepath   = Mage::getBaseDir('var').'/sphinx/stopwords.txt';
        
        $this->_indexerCommand      = $binPath.self::INDEXER;
        $this->_searchdCommand      = $binPath.self::SEARCHD;
        $this->_sphinxCfgTpl        = Mage::getModuleDir('etc', 'Mirasvit_SearchSphinx').DS.'conf'.DS.'sphinx.conf';
        $this->_sphinxSectionCfgTpl = Mage::getModuleDir('etc', 'Mirasvit_SearchSphinx').DS.'conf'.DS.'section.conf';
        
        $this->_spxHost             = Mage::getStoreConfig('searchsphinx/advanced/host');
        $this->_spxPort             = Mage::getStoreConfig('searchsphinx/advanced/port');
        
        $this->_spxHost             = $this->_spxHost ? $this->_spxHost : 'localhost';
        $this->_spxPort             = intval($this->_spxPort ? $this->_spxPort : '9315');
        
        $this->_matchMode           = Mage::getStoreConfig('searchsphinx/advanced/match_mode', 0);
    }
    
    public function query($queryText, $store, $index)
    {
        $indexCode  = $index->getCode();
        $primaryKey = $index->getPrimaryKey();
        $attributes = $index->getAttributes();

        if ($store) {
            $store = array($store);
        }

        return $this->_query($queryText, $store, $indexCode, $primaryKey, $attributes);
    }

    protected function _query($query, $storeId, $indexCode, $primaryKey, $attributes, $offset = 1)
    {
        $uid = Mage::helper('mstcore/debug')->start();

        $client = new SphinxClient();
        $client->setMaxQueryTime(5000); //5 seconds
        $client->setLimits(($offset - 1) * self::PAGE_SIZE, self::PAGE_SIZE, $this->_config->getResultLimit());
        $client->setSortMode(SPH_SORT_RELEVANCE);
        $client->setMatchMode($this->_matchMode);
        $client->setServer($this->_spxHost, $this->_spxPort);
        $client->SetFieldWeights($attributes);

        if ($storeId) {
            $client->SetFilter('store_id', $storeId);
        }

        $sphinxQuery = $this->_buildQuery($query, $storeId);

        Mage::helper('mstcore/debug')->dump($uid, array('query' => $query, 'sphinxQuery' => $sphinxQuery));

        if (!$sphinxQuery) {
            return array();
        }
        // echo $sphinxQuery;
        $sphinxResult = $client->query($sphinxQuery, $indexCode);

        Mage::helper('mstcore/debug')->dump($uid, array('sphinxResult' => $sphinxResult));

        // pr($sphinxResult);
        if ($sphinxResult === false) {
            $this->restart();
            Mage::throwException($client->GetLastError()."\nQuery: ".$query);
        } elseif ($sphinxResult['total'] > 0) {
            $entityIds = array();
            foreach ($sphinxResult['matches'] as $data) {
                $entityIds[$data['attrs'][strtolower($primaryKey)]] = $data['weight'];
            }

            if ($sphinxResult['total'] > $offset * self::PAGE_SIZE
                && $offset * self::PAGE_SIZE < $this->_config->getResultLimit()) {
                $newIds = $this->_query($query, $storeId, $indexCode, $primaryKey, $attributes, $offset + 1);
                foreach ($newIds as $key => $value) {
                   $entityIds[$key] = $value;
                }
            }
        } else {
            $entityIds = array();
        }

        $entityIds = $this->_normalize($entityIds);

        Mage::helper('mstcore/debug')->end($uid, $entityIds);

        return $entityIds;
    }

    protected function _buildQuery($query, $storeId)
    {
        if ($this->_matchMode != SPH_MATCH_EXTENDED) {
            return $query;
        }

        // Extended query syntax
        if (substr($query, 0, 1) == '=') {
            return substr($query, 1);
        }

        // Search by field
        if (substr($query, 0, 1) == '@') {
            return $query;
        }

        $arQuery = Mage::helper('searchsphinx/query')->buildQuery($query, $storeId, true);

        foreach ($arQuery as $key => $array) {
            if ($key == 'not like') {
                $result[] = '-'.$this->_buildWhere($key, $array);
            } else {

                $result[] = $this->_buildWhere($key, $array);
            }
        }
        $query = '(' . join(' & ', $result) . ')';
            
        return $query;
    }

    protected function _buildWhere($type, $array)
    {
        if (!is_array($array)) {
            if (substr($array, 0, 1) == ' ') {
                return $array;
            } else {
                $array = preg_replace('/[\- ]/', '*', $array);
                $array = preg_replace('/[.]/', '', $array);
                $array = str_replace(' ', '*', $array);
                return '*'.$array.'*';  
            }
            
        }

        foreach ($array as $key => $subarray) {
            if ($key == 'or') {
                $array[$key] = $this->_buildWhere($type, $subarray);
                if (is_array($array[$key])) {
                    $array = '('.implode(' | ', $array[$key]).')';
                }
            } elseif ($key == 'and') {
                $array[$key] = $this->_buildWhere($type, $subarray);
                if (is_array($array[$key])) {
                    $array = '('.implode(' & ', $array[$key]).')'; 
                }
            } else {
                $array[$key] = $this->_buildWhere($type, $subarray);
            }
        }

        return $array;

    }

    /**
     * Configuration build section
     */

    public function makeConfigFile()
    {
        if (!file_exists(Mage::getBaseDir('var').DS.'sphinx')) {
            mkdir(Mage::getBaseDir('var').DS.'sphinx');
        }

        $basePath = Mage::getBaseDir('var').DS.'sphinx';

        if (Mage::getStoreConfig('searchsphinx/manage/search_engine') == 'sphinx_external') {
            $basePath = Mage::getStoreConfig('searchsphinx/manage/path');
        }

        $data = array(
            'time'      => date('d.m.Y H:i:s'),
            'host'      => $this->_spxHost,
            'port'      => $this->_spxPort,
            'logdir'    => $basePath,
            'sphinxdir' => $basePath,
        );

        $formater = new Varien_Filter_Template();
        $formater->setVariables($data);
        $config   = $formater->filter(file_get_contents($this->_sphinxCfgTpl));

        $indexes = Mage::helper('searchindex/index')->getIndexes();
        foreach ($indexes as $index) {
            $indexer = $index->getIndexer();
            $config  .= "\n".$this->_getSectionConfig($index->getCode(), $indexer);
        }

        file_put_contents($this->_configFilepath, $config);

        $this->_makeSynonymsFile();
        $this->_makeStopwordsFile();

        return $this->_configFilepath;
    }

    protected function _getSectionConfig($name, $indexer)
    {
        $basePath = Mage::getBaseDir('var').DS.'sphinx';

        if (Mage::getStoreConfig('searchsphinx/manage/search_engine') == 'sphinx_external') {
            $basePath = Mage::getStoreConfig('searchsphinx/manage/path');
        }

        $data = array(
            'name'             => $name,
            'sql_host'         => Mage::getConfig()->getNode('global/resources/default_setup/connection/host'),
            'sql_user'         => Mage::getConfig()->getNode('global/resources/default_setup/connection/username'),
            'sql_pass'         => Mage::getConfig()->getNode('global/resources/default_setup/connection/password'),
            'sql_db'           => Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname'),
            'sql_query_pre'    => $this->_getSqlQueryPre($indexer),
            'sql_query'        => $this->_getSqlQuery($indexer),
            'sql_query_delta'  => $this->_getSqlQueryDelta($indexer),
            'sql_attr_uint'    => $indexer->getPrimaryKey(),
            'min_word_len'    => Mage::getStoreConfig(Mage_CatalogSearch_Model_Query::XML_PATH_MIN_QUERY_LENGTH),
            'stopwords'        => $basePath.DS.'stopwords.txt',
            'exceptions'       => $basePath.DS.'synonyms.txt',
            'index_path'       => $basePath.DS.$name,
            'delta_index_path' => $basePath.DS.$name.'_delta',
        );

        foreach ($data as $key => $value) {
            $data[$key] = str_replace('#', '\#', $value);
        }

        $formater = new Varien_Filter_Template();
        $formater->setVariables($data);
        $config   = $formater->filter(file_get_contents($this->_sphinxSectionCfgTpl));

        return $config;
    }

    protected function _makeStopwordsFile()
    {
        $tofile = array();
        // user stopwords
        // foreach (Mage::getSingleton('searchsphinx/config')->getStopWords() as $word) {
        //     $tofile[$word] = $word;
        // }

        // // dictionary of stopwords
        // foreach (Mage::getModel('searchsphinx/stopword')->getCollection() as $item) {
        //     $word = $item->getWord();
        //     $tofile[$word] = $word;
        // }

        ksort($tofile);
        file_put_contents($this->_stopwordsFilepath, implode("\n", $tofile));
    }

    protected function _makeSynonymsFile()
    {
        $tofile   = array('word => synonym');
        
        // user synonyms
        // foreach (Mage::getSingleton('searchsphinx/config')->getSynonyms() as $word => $synonyms) {
        //     foreach ($synonyms as $synonym) {
        //         $tofile[$word.$synonym] = $word.' => '.$synonym;
        //     }
            
        // }

        // // dictionary of synonyms
        // foreach (Mage::getModel('searchsphinx/synonym')->getCollection() as $item) {
        //     $word = $item->getWord();
        //     foreach (explode(',', $item->getSynonyms()) as $synonym) {
        //         if (strlen($synonym) < 20 && strlen($synonym) > 0)
        //         $tofile[$word.$synonym] = $word.' => '.$synonym;
        //     }
        // }

        ksort($tofile);
        file_put_contents($this->_synonymsFilepath, implode("\n", $tofile));
    }

    /**
     * Manage
     */

    public function reindex($delta = false)
    {
        $this->_request('reindex');
    }

    public function reindexDelta()
    {
        return $this->_request('reindexdelta');
    }

    public function start()
    {
        $error = $this->_request('start');
        
        if ($error) {
            Mage::throwException($error);
        }

        return $this;
    }

    public function stop()
    {
        $error = $this->_request('stop');

        if ($error) {
            Mage::throwException($error);
        }
        
        return $this;
    }

    public function restart()
    {
        $this->stop();
        $this->start();

        return $this;
    }

    /**
     * Execution section
     */

    public function doReindex($delta = false)
    {
        $this->makeConfigFile();

        if (!$this->isIndexerFounded()) {
            Mage::throwException($this->_indexerCommand.': command not found');
        }

        if (!$this->isIndexerRunning()) {
            $indexes = Mage::helper('searchindex/index')->getIndexes();
            foreach ($indexes as $index) {
                $indexCode = $index->getCode();
                if ($delta) {
                    $indexCode = 'delta_'.$indexCode;
                }
                $exec   = $this->_exec($this->_indexerCommand.' --config '.$this ->_configFilepath.' --rotate '.$indexCode);
                $result = ($exec['status'] == 0) || (strpos($exec['data'], self::REINDEX_SUCCESS_MESSAGE) !== FALSE);

                if (!$result) {
                    Mage::throwException('Error on reindex '.$exec['data']);
                }
            }
            if ($delta) {
                $this->mergeDeltaWithMain();
            }
            $this->restart();
        } else {
            Mage::throwException('Reindex already run, please wait... '.$this->isIndexerRunning());
        }

        return $this;
    }

    public function doReindexDelta()
    {
        return $this->doReindex(true);
    }

    public function doStart()
    {
        $this->stop();
        if (!$this->isSearchdFounded()) {
            Mage::throwException($this->_searchdCommand.': command not found');
        }

        $command = $this->_searchdCommand.' --config '.$this->_configFilepath;
        $exec = $this->_exec($command);
        if ($exec['status'] !== 0) {
            Mage::throwException('Error when running searchd '.$exec['data']);
        }
    }

    public function doStop()
    {
        $command = '/usr/bin/killall -9 '.self::SEARCHD;
        $exec = $this->_exec($command);
    }

    public function isIndexerRunning()
    {
        $status = false;

        $command = 'ps aux | grep '.self::INDEXER.' | grep '.$this->_configFilepath;
        $exec = $this->_exec($command);
        if ($exec['status'] === 0) {
            $pos = strpos($exec['data'], '--rotate');
            if ($pos !== false) {
                $status = $exec['data'];
                return $status;
            }
        }

        return $status;
    }

    public function isSearchdRunning()
    {
        if (!$this->isSearchdFounded()) {
            return false;
        }

        $command = 'ps aux | grep '.self::SEARCHD.' | grep '.$this->_configFilepath;
        $exec = $this->_exec($command);

        if ($exec['status'] === 0) {
            $pos = strpos($exec['data'], self::SEARCHD.' --config');

            if ($pos !== false) {
                return true;
            }
        }

        return false;
    }

    public function isSearchdFounded()
    {
        $exec = $this->_exec('which '.$this->_searchdCommand);
        if ($exec['status'] !== 0) {
            return false;
        }

        return true;
    }

    public function isIndexerFounded()
    {
        $exec = $this->_exec('which '.$this->_indexerCommand);
        if ($exec['status'] !== 0) {
            return false;
        }

        return true;
    }

    public function mergeDeltaWithMain()
    {
        $indexes = Mage::helper('searchindex/index')->getIndexes();
        foreach ($indexes as $index) {
            $exec = $this->_exec($this->_indexerCommand.' --config '.$this ->_configFilepath.' --merge '.$index->getCode().' delta_'.$index->getCode().' --merge-dst-range deleted 0 0 --rotate');
        }
    }

    protected function _exec($command)
    {
        $status = null;
        $data   = array();

        if (function_exists('exec')) {
            exec($command, $data, $status);
            Mage::helper('mstcore/logger')->log($this, __FUNCTION__, $command."\n".implode("\n", $data));
        } else {
            Mage::helper('mstcore/logger')->log($this, __FUNCTION__, 'php function "exec" not exists');
        }

        return array('status' => $status, 'data' => implode("\n", $data));
    }

    protected function _request($command)
    {
        $httpClient = new Zend_Http_Client();
        $httpClient->setConfig(array('timeout' => 60000));

        Mage::register('custom_entry_point', true, true);

        $store  = Mage::app()->getStore(1);
        $url    = $store->getUrl('searchsphinx/action/'.$command);
        $result = $httpClient->setUri($url)->request()->getBody();
        
        Mage::helper('mstcore/logger')->log($this, __FUNCTION__, $url."\n".$result);

        return $result;
    }

    protected function _getSqlQueryPre($indexer)
    {
        $table = $indexer->getTableName();

        $sql = 'UPDATE '.$table.' SET updated=0';

        return $sql;
    }

    protected function _getSqlQuery($indexer)
    {
        $table = $indexer->getTableName();

        $sql = 'SELECT CONCAT('.$indexer->getPrimaryKey().', store_id) AS id, '.$table.'.* FROM '.$table;

        return $sql;
    }

    protected function _getSqlQueryDelta($indexer)
    {
        $sql = $this->_getSqlQuery($indexer);
        $sql .= ' WHERE updated = 1';

        return $sql;
    }
}