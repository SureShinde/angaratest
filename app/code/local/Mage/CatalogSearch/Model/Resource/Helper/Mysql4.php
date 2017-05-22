<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CatalogSearch Mysql resource helper model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Resource_Helper_Mysql4 extends Mage_Eav_Model_Resource_Helper_Mysql4
{
	
	private static $_dictionary;
	private static $_synonyms;
	private static $_tags;
	private static $_attributes;
	private static $_rules;
	private static $_refinedSearches;
	private static $_jewelryTypes;

    /**
     * Join information for usin full text search
     *
     * @param  Varien_Db_Select $select
     * @return Varien_Db_Select $select
     */
    public function chooseFulltext($table, $alias, $select)
    {
        $field = new Zend_Db_Expr('MATCH ('.$alias.'.data_index) AGAINST (:query IN BOOLEAN MODE)');
        //$select->columns(array('relevance' => $field));
		$select->columns(array('relevance' => $alias.'.search_weight'));
        return $field;
    }
	
	/* train the dictionary */
	function _train(){
		if(empty(self::$_dictionary)){
			self::$_dictionary = array();
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$query = 'select distinct dks.synonym, dks.master_keyword from `digger_keyword_synonym` as dks';
			$results = $readConnection->fetchAll($query);
			self::$_synonyms = array();
			foreach($results as $word){
				self::$_synonyms[strtolower($word['synonym'])] = strtolower($word['master_keyword']);
				self::$_dictionary[strtolower($word['synonym'])] = 1;
			}
			
			$query = 'select t.tag_id, t.name from `tag` as t where t.status = 1';
			$results = $readConnection->fetchAll($query);
			self::$_tags = array();
			foreach($results as $tag){
				self::$_tags[strtolower($tag['name'])] = strtolower($word['tag_id']);
				self::$_dictionary[strtolower($tag['name'])] = 1;
			}
			
			$query = 'select distinct ea.attribute_id, ea.attribute_code, eaov.value from `eav_attribute` as ea
left join `catalog_eav_attribute` as cea on cea.attribute_id = ea.attribute_id
left join `eav_attribute_option` as eao on eao.attribute_id = ea.attribute_id
left join `eav_attribute_option_value` as eaov on eaov.option_id = eao.option_id
where cea.is_searchable = 1 and eaov.value is not null';
			$results = $readConnection->fetchAll($query);
			self::$_attributes = array();
			self::$_jewelryTypes = array();
			foreach($results as $value){
				$value['value'] = strtolower($value['value']);
				self::$_attributes[$value['value']][] = $value['attribute_code'];
				if($value['attribute_code'] == 'jewelry_type'){
					self::$_jewelryTypes[] = $value['value'];
				}
				foreach(explode(' ',$value['value']) as $word){
					$word = preg_replace("/[^A-Z0-9a-z_-\w]/u", '', $word);
					if(!isset(self::$_dictionary[$word])) {
							self::$_dictionary[$word] = 0;
					}
					self::$_dictionary[$word] += 1;
				}
			}
		}
		return self::$_dictionary;
	}
	
	function _correct($word) {
		$dictionary = self::$_dictionary;
        $word = strtolower($word);
        if(isset($dictionary[$word])) {
                return strtolower($word);
        }

        $edits1 = $edits2 = $edits3 = $edits4 = array();
        foreach($dictionary as $dictWord => $count) {
                $dist = levenshtein($word, $dictWord); 
                if($dist == 1 && strlen($word) > 2) {
                        $edits1[$dictWord] = $count;
                } else if($dist == 2 && strlen($word) > 4) {
                        $edits2[$dictWord] = $count;
                }
				else if($dist==3 && strlen($word) > 7) {
					$edits3[$dictWord]=$count;
				}
				else if($dist==4 && strlen($word) > 10) {
					$edits4[$dictWord]=$count;
				}
        }

        if(count($edits1)) {
                arsort($edits1);
                return key($edits1);
        } else if(count($edits2)) {
                arsort($edits2);
                return key($edits2);
        } else if(count($edits3)) {
		        arsort($edits3);
				return key($edits3);
		}
		else if(count($edits4)) {
		        arsort($edits4);
				return key($edits4);
		}
		
        // Nothing better
        return strtolower($word);
	}
	
	function _findMatchingTerms($terms){
		$matchingTerms = array();
		foreach($terms as $term){
			foreach(self::$_attributes as $value => $attributes){
				if(stripos($value, $term) !== false){
					if(in_array(strtolower($term), explode(' ', strtolower($value)))){
						$matchingTerms[$term][$value] = $attributes;
					}
				}
			}
		}
		return $matchingTerms;
	}

	function _findExactMatchingTerms($matchingTerms, $filteredQuery){
		$ExactMatchingTerms = array();
		
		foreach($matchingTerms as $keyTerm => $matchingTermSet){
			foreach($matchingTermSet as $matchingTerm => $attributes){
				if(stripos($filteredQuery, $matchingTerm) !== false){
					$ExactMatchingTerms[$keyTerm][$matchingTerm] = $attributes;
				}
			}
		}
		return $ExactMatchingTerms;
	}
	
	function _findBestMatchingTerms($partialTerms, $terms){
		$intersectTerms = array();
		
		$partialTermsKeys = array_keys($partialTerms);
		$partialTermsTmp = array_values($partialTerms);
		
		for($iCounter = 0; $iCounter < count($partialTermsTmp); $iCounter++){
			if($iCounter < count($partialTermsTmp) - 1){
				$interSect = array_intersect_key($partialTermsTmp[$iCounter], $partialTermsTmp[$iCounter + 1]);
				if(count($interSect) > 0){
					$intersectTerms[$iCounter] = $interSect;
				}
			}
		}
		$query = implode(" ",$terms);
		$keysToCombine = array();
		foreach($intersectTerms as $partialTermsKey => $partialTermSet){
			if(isset($intersectTerms[$partialTermsKey + 1]) || isset($intersectTerms[$partialTermsKey - 1])){
				if(isset($intersectTerms[$partialTermsKey + 1])){
					$maxSimilarity = 0;
				}
				foreach($partialTermSet as $term => $attributeSet){
					similar_text($term, $query, $percent);
					if($percent > $maxSimilarity){
						$maxSimilarity = $percent;
						$keyToCombine = $partialTermsKey;
					}
				}
				if(isset($intersectTerms[$partialTermsKey - 1])){
					$keysToCombine[] = $keyToCombine;
				}
			}
			else{
				$keysToCombine[] = $partialTermsKey;
			}
		}
		foreach($keysToCombine as $keyToCombine){
			unset($partialTerms[$partialTermsKeys[$keyToCombine]]);
			unset($partialTerms[$partialTermsKeys[$keyToCombine + 1]]);
			$partialTerms[$partialTermsKeys[$keyToCombine]." ".$partialTermsKeys[$keyToCombine + 1]] = $intersectTerms[$keyToCombine];
		}
		foreach($partialTerms as $key => $bestMatchingValues){
			$tmpArray = array();
			foreach($bestMatchingValues as $value => $attributes){
				foreach($attributes as $attribute){
					$tmpArray[$attribute][] = $value;
				}
			}
			//var_dump($tmpArray);
			$max = 0;
			foreach($tmpArray as $attribute => $values){
				if(count($values) > $max){
					$max = count($values);
					$partialTerms[$key] = array();
					foreach($values as $value){
						$partialTerms[$key][$value][] = $attribute;
					}
				}
			}
		}
		return $partialTerms;
	}
	
	function _findFinalTerms($mergedMatches, $terms){
		$termCounts = array_count_values($terms);
		$mergedTerms = array();
		foreach($mergedMatches as $Match){
			$mergedTerms = array_merge($mergedTerms, $Match);
		}

		$finalTerms = array();
		foreach($mergedTerms as $term1 => $attributes1){
			$isApplicable = true;
			foreach($mergedTerms as $term2 => $attributes2){
				if($term1 != $term2){
					if(stripos($term2, $term1) !== false){
						if(in_array($term1, explode(' ', $term2))){
							if(isset($termCounts[$term1]) && ($termCounts[$term1] == 1)){
								$isApplicable = false;
							}
						}
					}
				}
			}
			if($isApplicable){
				$finalTerms[$term1] = $attributes1;
			}
		}
		return $finalTerms;
	}
	
	function _generateSubQueries($finalTerms){
		$finalAttributes = array();
		
		# @todo we need to take care of did you mean?
		$optionalAttributes = array();
		foreach($finalTerms as $term => $attributes){
			foreach($attributes as $attribute){
				
				
				if(count($attributes) > 1){
					if(isset($finalAttributes[$attribute])){
						if(count(array_intersect(explode(' ',$term), explode(' ', $finalAttributes[$attribute][0]))) == 0 )
							continue;
					}
				}
				
				$isExist = false;
				// start from filtering attributes  +-+-+-+-+-++-+
				foreach($finalAttributes as $finalAttribute){
					if(in_array($term, $finalAttribute)){
						$isExist = true;
					}
				}
				if(!$isExist){
					$finalAttributes[$attribute][] = $term;
				}
			}
		}
		return self::_matchGrammerRule($finalAttributes);
	}
	
	
	function _matchGrammerRule($attributeSets){
		$_rules = Mage::helper('catalogsearch')->getGrammerRules();
		
		$userAttributes = array();
		foreach($attributeSets as $attribute => $attributeSet){
			$userAttributes[] = $attribute;
		}
		if(!in_array('jewelry_type',$userAttributes)){
			$refinedRule = '';
		}
		$matchLevels = array();
		$exactMatch = false;
		foreach($_rules as $_rule){
			preg_match_all('/{(.*?)}/', $_rule, $matches);
			if(isset($matches[0])){
				$replaceArray = $matches[0];
				$attributes = $matches[1];
				if(array_diff($userAttributes, $attributes) === array_diff($attributes, $userAttributes)){
					$rule = $_rule;
					$exactMatch = true;
					$matchLevels = array();
				}
				$matchLevels[count(array_intersect($userAttributes, $attributes))][] = $_rule;
			}
		}
		
		$minMatchedAttributesCount = 9999;
		$minMatchedRefinedAttributesCount = 9999;
		foreach($matchLevels[max(array_keys($matchLevels))] as $_rule){
			preg_match_all('/{(.*?)}/', $_rule, $matches);
			if(isset($matches[0])){
				$replaceArray = $matches[0];
				$attributes = $matches[1];
				if(!$exactMatch){
					$attributesDifference = count(array_diff($attributes, $userAttributes));
					if($minMatchedAttributesCount >= $attributesDifference){
						$minMatchedAttributesCount = $attributesDifference;
						$rule = $_rule;
					}
				}
				if(isset($refinedRule)){
					$refinedAttributes = array_merge($userAttributes, array('jewelry_type'));
					$refinedAttributesDifference = count(array_diff($attributes, $refinedAttributes));
					if($minMatchedRefinedAttributesCount >= $refinedAttributesDifference){
						$minMatchedRefinedAttributesCount = $refinedAttributesDifference;
						$refinedRule = $_rule;
					}
				}
				
			}
		}
		
		if(!empty($refinedRule)){
			self::$_refinedSearches = self::_replaceAttributes($refinedRule, array_merge($attributeSets, array('jewelry_type' => self::$_jewelryTypes)) );
		}
		
		if(isset($rule)){
			return self::_replaceAttributes($rule, $attributeSets );
		}
	}
	
	function _replaceAttributes($rule, $data){
		preg_match_all('/{(.*?)}/', $rule, $matches);
		if(isset($matches[0])){
			$replaceArray = $matches[0];
			$attributes = $matches[1];
			$results = array($rule);
			foreach($attributes as $key => $attribute){
				if(!$data[$attributes[$key]]){
					$data[$attributes[$key]] = array('""');
				}
			}
			foreach($replaceArray as $key => $replaceString){
				$values = $data[$attributes[$key]];
				if($values){
					$multipleResults = array();
					foreach($values as $value){
						foreach($results as $result){
							$multipleResults[] = str_ireplace($replaceString, $value, $result);
						}
					}
					$results = array_merge($multipleResults);
				}
			}
			return $results;
		}
		else{
			return false;
		}
	}
	
	function _generateUniqueQuery($finalTerms, $separator, $tags){
		$subQueries = self::_generateSubQueries($finalTerms);
		if(!empty($tags)){
			foreach($subQueries as $key => $subQuery){
				foreach($tags as $tag){
					$subQueries[$key] = $subQuery .' '. $tag;
				}
			}
		}
		if(!empty($tags) && empty($subQueries)){
			foreach($tags as $tag){
				$subQueries[] = $tag;
			}
		}
		return '\'"'.$separator.implode($separator.'""'.$separator, $subQueries).$separator.'"\'';
	}

    /**
     * Prepare Terms
     *
     * @param string $str The source string
     * @return array(0=>words, 1=>terms)
     */
	 
	function buildQueryDetails($terms, $separator){
		// spell check
		self::_train();
		foreach($terms as $key => $term){
			$terms[$key] = self::_correct($term);
		}
		
		
		$query = implode(' ',$terms);
		
		// replace synonyms
		foreach(self::$_synonyms as $synonym => $synonym_for){
			if(stripos($query, $synonym) !== false){
				if(count(array_diff(explode(' ',$synonym), $terms)) == 0){
					if(empty($synonym_for))
						$synonym_for = ' ';
					$query = preg_replace('/\b'.$synonym.'\b/', $synonym_for, $query);
				}
			}
		}
		// extract tags
		$matchedTags = array();
		foreach(self::$_tags as $tag => $tagId){
			if(stripos($query, $tag) !== false){
				if(count(array_diff(explode(' ',$tag), $terms)) == 0){
					$matchedTags[$tagId] = $tag;
					//$query = str_ireplace(' '.$tag.' ', ' ', $query);
					$query = preg_replace('/\b'.$tag.'\b/', '', $query);
				}
			}
		}
		$terms = explode(' ', $query);
		// find matching values with attributes
		$matchingTerms = self::_findMatchingTerms($terms);
		if(!empty($matchingTerms)){
			$exactMatchingTerms = self::_findExactMatchingTerms($matchingTerms, implode(' ', $terms));
			if(!empty($exactMatchingTerms)){
				// find remaining partial terms
				$partialTerms = array_diff_key($matchingTerms, $exactMatchingTerms);
				// filter best fit partial match in partial terms
				$partialTerms = self::_findBestMatchingTerms($partialTerms, $terms);
				// merge keys with common values in exact matching terms
				$mergedMatches = array_merge($exactMatchingTerms, $partialTerms);
				
				$finalTerms = self::_findFinalTerms($mergedMatches, $terms);
				
			}
			else{	// all partials
				// filter best fit partial match in partial terms
				$partialTerms = self::_findBestMatchingTerms($matchingTerms, $terms);
				$finalTerms = self::_findFinalTerms($partialTerms, $terms);
			}
			$generatedQuery = self::_generateUniqueQuery($finalTerms, $separator, $matchedTags);
			$refinedSearches = '';
			if(!empty(self::$_refinedSearches)){
				$refinedSearches = implode(',', self::$_refinedSearches);
			}
			return array(
				'generated_query' => $generatedQuery,
				'refined_searches' => $refinedSearches
			);
		}
		else{ // go with original terms as no matching values and attributes found
			if($matchedTags){
				return array('generated_query' => '\'"'.$separator.implode($separator.'""'.$separator, $matchedTags).$separator.'"\''); 
			}
			return array('generated_query' => '+'.implode('* +', $terms).'*');
		}
	}
	
	 
    function prepareTerms($str, $maxWordLength = 0)
    {
        $boolWords = array(
            '+' => '+',
            '-' => '-',
            '|' => '|',
            '<' => '<',
            '>' => '>',
            '~' => '~',
            '*' => '*',
        );
        $brackets = array(
            '('       => '(',
            ')'       => ')'
        );
        $words = array(0=>"");
        $terms = array();
        preg_match_all('/([\(\)]|[\"\'][^"\']*[\"\']|[^\s\"\(\)]*)/uis', $str, $matches);
        $isOpenBracket = 0;
        foreach ($matches[1] as $word) {
            $word = trim($word);
            if (strlen($word)) {
                $word = str_replace('"', '', $word);
                $isBool = in_array(strtoupper($word), $boolWords);
                $isBracket = in_array($word, $brackets);
                if (!$isBool && !$isBracket) {
                    $terms[$word] = $word;
                    //$word = '"'.$word.'"';
                    $words[] = $word;
                } else if ($isBracket) {
                    if ($word == '(') {
                        $isOpenBracket++;
                    } else {
                        $isOpenBracket--;
                    }
                    $words[] = $word;
                } else if ($isBool) {
                    $words[] = $word;
                }
            }
        }
        if ($isOpenBracket > 0) {
            $words[] = sprintf("%')".$isOpenBracket."s", '');
        } else if ($isOpenBracket < 0) {
            $words[0] = sprintf("%'(".$isOpenBracket."s", '');
        }
        if ($maxWordLength && count($terms) > $maxWordLength) {
            $terms = array_slice($terms, 0, $maxWordLength);
        }
        $result = array($words, $terms);
        return $result;
    }

    /**
     * Use sql compatible with Full Text indexes
     *
     * @param mixed $table The table to insert data into.
     * @param array $data Column-value pairs or array of column-value pairs.
     * @param arrat $fields update fields pairs or values
     * @return int The number of affected rows.
     */
    public function insertOnDuplicate($table, array $data, array $fields = array()) {
        return $this->_getWriteAdapter()->insertOnDuplicate($table, $data, $fields);
    }
}
