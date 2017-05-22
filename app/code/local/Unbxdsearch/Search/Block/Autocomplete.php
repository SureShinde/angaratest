<?php

class Unbxdsearch_Search_Block_Autocomplete extends Mage_CatalogSearch_Block_Autocomplete 
{
    protected $_suggestData = array();
    protected $first=true;
    

    protected function _toHtml()
    {
        $html = '';

        if (!$this->_beforeToHtml()) {
            return $html;
        }
        
	
	$html.=$this->getSuggestion("autosuggest");
        

        return $html;
    }
	
    public function getdesigned($suggestData,$html,$Header,$count)
    {
    	preg_replace('/ last/', '', $html);
    	
    	if($this->first)
        	$html .= '<ul><li style="display:none"></li>';
	/* remove li tag from this line */
    	$html .=  '<li class="result-group-title" onclick="javascript:void(0);">'.$this->htmlEscape($Header).'</li>';
        foreach ($suggestData as $index => $item) {
            if ($index == 0 && $this->first) {
            	$this->first=false;
                $item['row_class'] .= ' first';
            }

	            if ($index == $count-1) {
	            	
                $item['row_class'] .= ' last';
            }

			$title = $this->htmlEscape($item['title']);
			
			$item['title'] = str_ireplace($this->helper('catalogsearch')->getQueryText(), '<strong>'.$this->helper('catalogsearch')->getQueryText().'</strong>', $this->htmlEscape($item['title']));
			
			$pname = $item['title'];
			$pname_arr = explode(' ',$pname);
			$pname_temp = '';
			for($i=0;$i<count($pname_arr);$i++){
				if($i>=3){
					$pname_temp = $pname_temp.'...';
					break;	
				}
				$pname_temp = $pname_temp.' '.$pname_arr[$i];	
			}
			
			
            $html .=  '<li title="'.stripslashes($title).'" class="'.$item['row_class'].'">'
                .stripslashes($pname_temp).'</li>';
        }
        
	return $html;
    }

    public function getSuggestion($autosuggest)
    {

            $counter = 0;
            $data = array();
            $query = $this->helper('catalogsearch')->getQueryText();
	    $query = str_replace(' ', '%20', $query);
	    $html="";
	  	
	   
		$autoarray=array();
		
		$autoarray[]="autosuggest_category";
		$autoarray[]="autosuggest_name";
		ini_set('default_socket_timeout', 120);
		
		foreach($autoarray as $autosugg)
		{
			
	  		$url="http://angara.autocomplete.unbxdapi.com/select/?q=".$query."&handler=".$autosugg."&rows=5&wt=json&hl=true&hl.fl=".$autosugg."&hl.simple.pre=&hl.simple.post=";
	  		
	  		try{
	  	    	$jdata=json_decode(file_get_contents($url),true);
	  	    	
	  		}
	  		catch(Exception $Ex)
	  		{
	  				  				
	  		}
	  	    $i=0;
	  	    
			
			$eachsuggest=$jdata["response"];
			if ($eachsuggest["numFound"]>0)
			{
			    $data=array();
			    foreach($jdata["highlighting"] as $res)
				{
				    
					foreach($res as $autosuggest_pro) 
					{
						foreach($autosuggest_pro as $autosuggest_product){
	
							$_data=array('title' =>$autosuggest_product,'row_class' => (++$counter)%2?'odd':'even');
						
							$data[]=$_data;
						}
					
	
					}
	
			   }
		
				if($autosugg=="autosuggest_name"){
					$autosugg="Product Name";
				}
				if($autosugg=="autosuggest_category"){
					$autosugg="Category";
				}	
				    
				    $html=$this->getdesigned($data,$html,ltrim($autosugg,"autosuggest_"),$eachsuggest["numFound"]);
				   
				 
			    }
		    }
	    $html.= '</ul>';

        return $html;
    }
/*
 *
*/
}
