<?php
class Hp_Hprcv_Model_Hprcv{	
	public function qualitygradecomparison($stone){
		switch($stone){
			case 'Diamond':
				return true;
			case 'Enhanced Black Diamond':
				return true;
			case 'Enhanced Blue Diamond':
				return true;
			case 'Coffee Diamond':
				return true;	
			case 'Blue Sapphire':
				return true;
			case 'Pink Sapphire':
				return true;
			case 'Ruby':
				return true;
			case 'Aquamarine':
				return true;
			case 'Tanzanite':
				return true;
			case 'Emerald':
				return true;	
			case 'Moissanite':
				return true;	
			case 'Amethyst':
				return true;	
			case 'Blue Topaz':
				return true;
			case 'Citrine':
				return true;
			case 'Garnet':
				return true;
			case 'Morganite':
				return true;
			case 'Opal':
				return true;
			case 'Peridot':
				return true;
			case 'Pink Tourmaline':
				return true;	
			case 'Akoya Cultured Pearl':
				return true;
			case 'Freshwater Cultured Pearl':
				return true;
			case 'South Sea Cultured Pearl':
				return true;	
			case 'Golden Japanese Cultured Pearl':
				return true;
			case 'Golden South Sea Cultured Pearl':
				return true;
			case 'Tahitian Cultured Pearl':
				return true;
			case 'Kunzite':
				return true;	
			default:
				return false;
		}
	}
	
	public function getStoneImage($stone, $shape, $grade){
		$skinUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/ress/default/images/stones/182/'; 
		$baseDir = Mage::getBaseDir('skin').'/frontend/ress/default/images/stones/182/';
		if(file_exists($baseDir.strtolower(str_replace(' ','-',$stone)).'-'.$grade.'-quality-'.strtolower(str_replace(' ','-',$shape)).'.jpg')){
			return '<img src="'.$skinUrl.strtolower(str_replace(' ','-',$stone)).'-'.$grade.'-quality-'.strtolower(str_replace(' ','-',$shape)).'.jpg" alt="'.$grade.' Quality" class="img-responsive" />';
		}
		else{
			return '<img src="/skin/frontend/angara/default/images/catalog/product/placeholder/chart-noimage.jpg" class="img-responsive" />';
		}
	}
	
	public function getStoneChartData($stone, $shape){
		switch($stone){
			case 'Diamond':
				return array(					
					array(
						"",
						/*"<center>J I2</center>",*/
						"<center>I I1</center>",
						"<center>H SI2</center>",
						"<center>G-H VS</center>"						
					),
					array(
						"",
						/*$this->getStoneImage($stone, $shape, "good"),*/
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						/*"Near Colorless",*/
						"Near Colorless",
						"Colorless",
						"Colorless"
					),
					array(
						"<strong>Clarity:</strong>",
						/*"Inclusions Clearly Visible to Unaided eye",*/
						"Inclusions may be visible to unaided eye",
						"Slightly Included",
						"Very Slightly Included"
					),
					array(
						"<strong>Brilliance:</strong>",
						/*"Low",*/
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"22%",
						"33%",
						"26%",
						"19%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Diamonds Available Worldwide:</strong>",
						/*"Top 50%",*/
						"Top 25%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						/*"Mall jewelers",*/
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					),
					array(
						"<strong>Description:</strong>",
						/*"Inclusions can be seen without magnification and obvious under 10X magnification. May affect transparency and brilliance.",*/
						"Inclusions maybe visible without magnification.",
						"Slightly included. Inclusions visible under 10X magnification only.",
						"Very slightly included with high brilliance and sparkle."
					)					
				);	
			case 'Enhanced Black Diamond':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>"											
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better")						
					),
					array(
						"<strong>Color:</strong>",
						"Black",
						"Black"						
					),
					array(
						"<strong>Clarity:</strong>",
						"Opaque",
						"Opaque"						
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Low"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"11.5%",
						"88.45%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Enhanced Black Diamonds Available Worldwide:</strong>",
						"50%",
						"25%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores"
					),
					array(
						"<strong>Description:</strong>",
						"Enhanced Black Diamond with visible cracks on surface.",
						"Enhanced Black Diamond with clean surface."
					)					
				);	
			case 'Enhanced Blue Diamond':
				return array(
					array(
						"",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>"											
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best")						
					),
					array(
						"<strong>Color:</strong>",
						"Greenish Blue",
						"Teal Blue"						
					),
					array(
						"<strong>Clarity:</strong>",
						"Included",
						"Slightly Included"						
					),
					array(
						"<strong>Brilliance:</strong>",
						"Medium",
						"High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"22%",
						"78%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Enhanced Blue Diamonds Available Worldwide:</strong>",
						"25%",
						"10%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores"
					),
					array(
						"<strong>Description:</strong>",
						"Enhanced Blue Diamond with greenish hue. Inclusions may be visible to unaided eye.",
						"Enhanced Blue Diamond with clear teal blue hue and slight inclusions that are visible under 10X magnification only."
					)					
				);	
			case 'Coffee Diamond':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"	
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")	
					),
					array(
						"<strong>Color:</strong>",
						"Fancy Light Brown (C4)",
						"Fancy Medium Brown (C5)",
						"Fancy Dark Brown (C6)",
						"Fancy Deep Brown (C7)"						
					),
					array(
						"<strong>Clarity:</strong>",
						"Included (I2)",
						"Included (I1)",
						"Slightly Included (SI2)",
						"Very Slightly Included (VS2)"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Moderate",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"22%",
						"78%"
					),*/
					/*array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Enhanced Blue Diamonds Available Worldwide:</strong>",
						"25%",
						"10%"
					),*/
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the world"
					),
					array(
						"<strong>Description:</strong>",
						"Good (A): Fancy light brown (C4) in color. This quality is comparable to that used by mall jewelers and chain stores.",
						"Better (AA): Fancy Medium Brown (C5) in color. This quality is comparable to that used by leading independent/family jewelers.",
						"Best (AAA): Fancy Dark Brown (C6) in color. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.",
						"Heirloom (AAAA): Fancy Deep Brown (C7) in color. This quality can be found only at the top boutiques in the world."
					)					
				);	
			case 'Blue Sapphire':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Dark Blue",
						"Dark to Medium Blue",
						"Medium to Rich Blue",
						"Deep Rich Blue"
					),
					array(
						"<strong>Clarity:</strong>",
						"Opaque",
						"Moderately Included",
						"Slightly Included",
						"Very Slightly Included to Eye Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"22%",
						"33%",
						"26%",
						"19%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Blue Sapphires Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					),
					array(
						"<strong>Description:</strong>",
						"Midnight blue color; light does not pass through sapphire; entry level for fine jewelry.",
						"Blue with slight grey undertones; limited amount of light passes through; small natural inclusions visible with the naked eye.",
						"Medium rich blue; very minor visible inclusions; great value for a high quality sapphire; well cut for high brilliance.",
						"Deep, intense, royal velvety blue; primarily mined from coveted Ceylon region; incredible brilliance in daylight and artificial light; exceptional cut to maximize color and brilliance; amongst the best available in the world; the famous Princess Di and Kate Middleton sapphire would be in this category."
					)					
				);
			case 'Pink Sapphire':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Baby Pink",
						"Light to Medium Pink",
						"Medium Pink",
						"Rich Pink"
					),
					array(
						"<strong>Clarity:</strong>",
						"Included",
						"Moderately Included",
						"Slightly Included",
						"Very Slightly Included to Eye Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"22%",
						"33%",
						"26%",
						"19%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Pink Sapphires Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					),
					array(
						"<strong>Description:</strong>",
						"Midnight pink color; light does not pass through sapphire; entry level for fine jewelry.",
						"Pink with slight grey undertones; limited amount of light passes through; small natural inclusions visible with the naked eye.",
						"Medium rich pink; very minor visible inclusions; great value for a high quality sapphire; well cut for high brilliance.",
						"Deep, intense, royal velvety pink; primarily mined from coveted Ceylon region; incredible brilliance in daylight and artificial light; exceptional cut to maximize color and brilliance; amongst the best available in the world; the famous Princess Di and Kate Middleton sapphire would be in this category."
					)					
				);
			case 'Ruby':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Dark Red",
						"Medium Pinkish Red",
						"Medium Red",
						"Deep Rich Red"
					),
					array(
						"<strong>Clarity:</strong>",
						"Opaque",
						"Heavily Included",
						"Moderately Included",
						"Moderately to Very Slightly Included"
					),
					array(
						"<strong>Brilliance:",
						"Low",
						"Medium",
						"Medium to High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"13%",
						"36%",
						"31%",
						"20%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Rubies Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					),
					array(
						"<strong>Description:</strong>",
						"Dark red and opaque; light does not pass through ruby; entry level for fine jewelry.",
						"Medium pinkish red; limited amount of light passes through the ruby; some inclusions are visible with the naked eye.",
						"Medium rich red with hints of pink; very minor visible inclusions; great value for a high quality ruby; well cut for high brilliance.",
						"Deep, intense red with hints of pink; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)					
				);      
			case 'Aquamarine':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Light Sky Blue",
						"Sky Blue",
						"Light Swiss Blue",
						"Swiss Blue"
					),
					array(
						"<strong>Clarity:</strong>",
						"Slightly Included",
						"Slightly Included",
						"Eye Clean",
						"Eye Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"13%",
						"36%",
						"31%",
						"20%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Aquamarines Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					),
					array(
						"<strong>Description:</strong>",
						"Very light sky blue and slightly included; light does not pass through aquamarine; entry level for fine jewelry.",
						"Sky blue and slightly included; limited amount of light passes through the aquamarine; some inclusions are visible with the naked eye.",
						"Light swiss blue, eye clean; great value for a high quality aquamarine; well cut for high brilliance.",
						"Truly exceptional swiss blue, eye clean; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)					
				);				
			case 'Tanzanite':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Light Violet Blue",
						"Medium Violet Blue",
						"Rich Violet blue",
						"Deep Rich Violet Blue"
					),
					array(
						"<strong>Clarity:</strong>",
						"Slightly Included",
						"Slightly Included",
						"Eye Clean",
						"Eye Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"14%",
						"49%",
						"25%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Tanzanites Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					),
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)					
				);				
			case 'Emerald':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Medium Green",
						"Medium Green",
						"Rich Medium Green",
						"Rich Green"
					),
					array(
						"<strong>Clarity:</strong>",
						"Opaque",
						"Heavily Included",
						"Moderately Included",
						"Moderately to Slightly Included"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Emeralds Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					),
					array(
						"<strong>Description:</strong>",
						"Medium green and opaque; light does not pass through emerald; entry level for fine jewelry.",
						"Medium green; limited amount of light passes through the emerald; inclusions can be seen with the naked eye.",
						"Rich medium green, moderate inclusions can be seen with the naked eye (inclusions are typical for emeralds, no inclusions typically indicate that the emerald is not genuine); great value for a high quality emerald; well cut for high brilliance.",
						"Rich deep green; moderate to minor inclusions can be seen with the naked eye (inclusions are typical for emeralds, no inclusions typically indicate that the emerald is not genuine); incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)					
				);
			case 'Moissanite':
				return array(
					array(
						"",
						"<center>Classic Moissanite<sup>TM</sup></center>",
						"<center>Forever Brilliant<sup class='fontsize-type1'>&reg;</sup></center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Off White",
						"Next to White"
					),
					/*array(
						"<strong>Clarity:</strong>",
						"Moderately Included",
						"Moderately to Slightly Included"
					),*/
					array(
						"<strong>Brilliance:</strong>",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"19%",
						"12%"
					),
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Emeralds Available Worldwide:</strong>",
						"Top 10%",
						"Top 1%"
					),*/
					array(
						"<strong>Quality Comparable To:</strong>",
						"Comparable Diamonds used by Top 5th Avenue or Rodeo Drive Jewelers",
						"Comparable Diamonds used by Finest Boutiques in the World"
					),
					array(
						"<strong>Description:</strong>",
						"This Charles & Colvard Created Moissanite<sup>TM</sup> is off white in color; transparent; well cut for high brilliance; value only at a fraction of comparable quality diamonds.",
						"This Charles and Colvard Created Forever Brilliant<sup class='fontsize-type1'>&reg;</sup> Moissanite has 10% more brilliance than diamond, incredible brilliance in daylight and artificial light; exceptional cut to maximize intrinsic fire and purity; one-tenth the price of comparable quality diamonds."
					)					
				);
			case 'Amethyst':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Light Purple",
						"Medium Purple",
						"Medium Dark Purple",
						"Dark Purple"
					),
					array(
						"<strong>Clarity:</strong>",
						"Slightly Included",
						"Slightly Included",
						"Eye Clean",
						"Eye Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Amethysts Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Medium green and opaque; light does not pass through emerald; entry level for fine jewelry.",
						"Medium green; limited amount of light passes through the emerald; inclusions can be seen with the naked eye.",
						"Rich medium green, moderate inclusions can be seen with the naked eye (inclusions are typical for emeralds, no inclusions typically indicate that the emerald is not genuine); great value for a high quality emerald; well cut for high brilliance.",
						"Rich deep green; moderate to minor inclusions can be seen with the naked eye (inclusions are typical for emeralds, no inclusions typically indicate that the emerald is not genuine); incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'Blue Topaz':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Light Sky Blue",
						"Sky Blue",
						"Light Swiss Blue",
						"Swiss Blue"
					),
					array(
						"<strong>Clarity:</strong>",
						"Slightly Included",
						"Slightly Included",
						"Eye Clean",
						"Eye Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Blue Topazs Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light sky blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Sky blue; very minor inclusions can be seen with the naked eye.",
						"Light swiss blue; great value for a high quality blue topaz; well cut for high brilliance.",
						"Swiss blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'Citrine':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Light Yellow",
						"Yellow",
						"Golden",
						"Deep Golden"
					),
					array(
						"<strong>Clarity:</strong>",
						"Slightly Included",
						"Slightly Included",
						"Eye Clean",
						"Eye Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Citrines Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'Garnet':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Dark Red",
						"Dark to Medium Red",
						"Medium Red",
						"Rich Red"
					),
					array(
						"<strong>Clarity:</strong>",
						"Slightly Included",
						"Slightly Included",
						"Eye Clean",
						"Eye Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Garnets Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'Morganite':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Very Light Peach",
						"Light Peach",
						"Peach",
						"Peachy Pink"
					),
					array(
						"<strong>Clarity:</strong>",
						"Moderate Included",
						"Slightly Included",
						"Eye Clean",
						"Eye Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Morganites Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'Opal':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Milky with Very Low Play of Colour",
						"Milky with Low Play of Colour",
						"Milky with medium Play of Colour",
						"Milky with high Play of Colour"
					),
					array(
						"<strong>Clarity:</strong>",
						"Opaque & Surface Blemishes",
						"Opaque & Slight Surface Blemishes",
						"Opaque & Very Slight Surface Blemishes",
						"Opaque & Surface Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Opals Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'Peridot':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Light Yellowish Green",
						"Medium Yellowish Green",
						"Yellowish Green",
						"Green"
					),
					array(
						"<strong>Clarity:</strong>",
						"Slightly Included",
						"Slightly Included",
						"Eye Clean",
						"Eye Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Peridots Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'Pink Tourmaline':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Baby Pink",
						"Light to Medium Pink",
						"Medium Pink",
						"Rich Pink"
					),
					array(
						"<strong>Clarity:</strong>",
						"Heavily Included",
						"Moderately Included",
						"Slightly Included",
						"Very Slightly Included to Eye Clean"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Pink Tourmalines Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'Akoya Cultured Pearl':
				return array(
					array(
						"",
						/*"<center>Good - A</center>",
						"<center>Better - AA</center>",*/
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						/*$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),*/
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						/*"Baby Pink",
						"Light to Medium Pink",*/
						"White",
						"White"
					),
					array(
						"<strong>Clarity:</strong>",
						/*"Heavily Included",
						"Moderately Included",*/
						"Very Slightly Blemished Surface",
						"Blemish Free Surface"
					),
					array(
						"<strong>Brilliance:</strong>",
						/*"Low",
						"Medium",*/
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Pearls Available Worldwide:</strong>",
						/*"Top 75%",
						"Top 33%",*/
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						/*"Mall jewelers",
						"Leading Independent Stores",*/
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'Freshwater Cultured Pearl':
				return array(
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						/*"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"*/						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						/*$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")*/
					),
					array(
						"<strong>Color:</strong>",
						"White",
						"White",
						/*"White",
						"White"*/
					),
					array(
						"<strong>Clarity:</strong>",
						"Blemished Surface",
						"Slighty Blemished Surface",
						/*"Very Slightly Blemished Surface",
						"Blemish Free Surface"*/
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						/*"High",
						"Very High"*/
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Pearls Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						/*"Top 10%",
						"Top 1%"*/
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						/*"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"*/
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'South Sea Cultured Pearl':
				return array(	
					array(
						"",
						/*"<center>Good - A</center>",
						"<center>Better - AA</center>",*/
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						/*$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),*/
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						/*"Baby Pink",
						"Light to Medium Pink",*/
						"White",
						"White"
					),
					array(
						"<strong>Clarity:</strong>",
						/*"Heavily Included",
						"Moderately Included",*/
						"Very Slightly Blemished Surface",
						"Blemish Free Surface"
					),
					array(
						"<strong>Brilliance:</strong>",
						/*"Low",
						"Medium",*/
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Pearls Available Worldwide:</strong>",
						/*"Top 75%",
						"Top 33%",*/
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						/*"Mall jewelers",
						"Leading Independent Stores",*/
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);	
			case 'Golden Japanese Cultured Pearl':
				return array(	
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Very Light Golden",
						"Light Golden",
						"Golden",
						"Bright Golden"
					),
					array(
						"<strong>Clarity:</strong>",
						"Blemished Surface",
						"Slighty Blemished Surface",
						"Very Slightly Blemished Surface",
						"Blemish Free Surface"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Pearls Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'Golden South Sea Cultured Pearl':
				return array(	
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Very Light Golden",
						"Light Golden",
						"Golden",
						"Bright Golden"
					),
					array(
						"<strong>Clarity:</strong>",
						"Blemished Surface",
						"Slighty Blemished Surface",
						"Very Slightly Blemished Surface",
						"Blemish Free Surface"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Pearls Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);
			case 'Tahitian Cultured Pearl':
				return array(	
					array(
						"",
						"<center>Good - A</center>",
						"<center>Better - AA</center>",
						"<center>Best - AAA</center>",
						"<center>Heirloom - AAAA</center>"						
					),
					array(
						"",
						$this->getStoneImage($stone, $shape, "good"),
						$this->getStoneImage($stone, $shape, "better"),
						$this->getStoneImage($stone, $shape, "best"),
						$this->getStoneImage($stone, $shape, "heirloom")
					),
					array(
						"<strong>Color:</strong>",
						"Black",
						"Black",
						"Black to Bronz",
						"Super Peacock"
					),
					array(
						"<strong>Clarity:</strong>",
						"Blemished Surface",
						"Slighty Blemished Surface",
						"Very Slightly Blemished Surface",
						"Blemish Free Surface"
					),
					array(
						"<strong>Brilliance:</strong>",
						"Low",
						"Medium",
						"High",
						"Very High"
					),
					/*array(
						"<strong>Percentage of Clients Who Bought This Quality:</strong>",
						"24%",
						"45%",
						"19%",
						"12%"
					),*/
					array(
						"<strong>Percentile&nbsp;vs&nbsp;All&nbsp;Pearls Available Worldwide:</strong>",
						"Top 75%",
						"Top 33%",
						"Top 10%",
						"Top 1%"
					),
					array(
						"<strong>Quality Comparable To:</strong>",
						"Mall jewelers",
						"Leading Independent Stores",
						"Top 5th Avenue or Rodeo Drive Jewelers",
						"Finest Boutiques in the World"
					)/*,
					array(
						"<strong>Description:</strong>",
						"Light violet blue; minor inclusions can be seen with the naked eye; entry level for fine jewelry.",
						"Medium violet blue; very minor inclusions can be seen with the naked eye.",
						"Rich violet blue; great value for a high quality tanzanite; well cut for high brilliance.",
						"Deep rich violet blue; incredible brilliance in daylight and artificial light; exceptionally cut to maximize brilliance; amongst the best available in the world; quality typically worn by royalty."
					)*/					
				);	
			default:
				return false;
		}
	}
	
	public function getStoneChartHtml($stone, $shape){
		if($stone == 'Akoya Cultured Pearl' || $stone == 'Freshwater Cultured Pearl' || $stone == 'South Sea Cultured Pearl' || $stone == 'Golden Japanese Cultured Pearl' || $stone == 'Golden South Sea Cultured Pearl' || $stone == 'Tahitian Cultured Pearl'){
			$stoneTitle = 'Pearl';
		}
		else{
			$stoneTitle = $stone;
		}
		$html = '<div class="block-sub-heading text-left max-padding-bottom ">'.$stoneTitle.' Quality Comparison Chart</div><table class="table table-condensed table-no-border table-striped text-left fontsize-type2">';
		$stoneChartData = $this->getStoneChartData($stone, $shape);
		foreach($stoneChartData as $row){
			$html .= '<tr>';
			foreach($row as $cell){
				$html .= '<td style="width: 20%;">'.$cell.'</td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table>';
		return $html;
	}
	
	public function getqualitygardecomparisonhtml(){
		$params = Mage::app()->getRequest()->getParams();
		if(substr($params['stonetype'], -3) == 'ies'){
			$stoneType = substr_replace($params['stonetype'], "y", -3);
		}
		else if(substr($params['stonetype'], -3) == 'zes'){
			$stoneType = substr_replace($params['stonetype'], "", -2);
		}
		else if(substr($params['stonetype'], -1) == 's'){
			$stoneType = substr_replace($params['stonetype'], "", -1);
		}
		else{
			$stoneType = $params['stonetype'];
		}
		echo $this->getStoneChartHtml($stoneType, $params['stoneshape']);
	}
	
	public $arr = array();	
	
	public function stoneweightcomparison($stone,$shape){
		$this->fillarr();
		if(isset($this->arr[$stone][$shape])){
			return true;
		}
		return false;
	}
	
	public function getweightcharthtml(){
		$getSkinUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
		$this->fillarr(); 
		
		if(substr($_GET['stonetype'], -3) == 'ies'){
			$stoneType = substr_replace($_GET['stonetype'], "y", -3);
		}
		else if(substr($_GET['stonetype'], -3) == 'zes'){
			$stoneType = substr_replace($_GET['stonetype'], "", -2);
		}
		else if(substr($_GET['stonetype'], -3) == 'xes'){
			$stoneType = substr_replace($_GET['stonetype'], "", -2);
		}
		else if(substr($_GET['stonetype'], -1) == 's'){
			$stoneType = substr_replace($_GET['stonetype'], "", -1);
		}		
		else{
			$stoneType = $_GET['stonetype'];
		}
		
		if($stoneType == 'Diamond' || $stoneType == 'Enhanced Black Diamond' || $stoneType == 'Enhanced Blue Diamond'){	 
			if($stoneType == 'Enhanced Black Diamond' || $stoneType == 'Enhanced Blue Diamond'){
				$stoneTypeName = trim(str_replace('Enhanced','',$stoneType)); 
			}
			else{
				$stoneTypeName = $stoneType;
			}?>
			<div class="block-sub-heading">
				<img src="<?php echo $getSkinUrl;?>frontend/angara/diamondstud/images/<?php echo strtolower($_GET['stoneshape']);?>-<?php echo str_replace(' ','-',strtolower($stoneTypeName));?>-carat-weight-chart.jpg" alt="" class="img-responsive center-block"  />				
				<div style="font-size:11px;">*Diamond carat weights represent the approximate total weight of all diamonds in each setting and may vary no more than 0.07 cts below the stated weight.</div>
				<div class="clear"></div>
			</div>
		<?php 
		}
		else{
			if($stoneType == 'Akoya Cultured Pearl' || $stoneType == 'Freshwater Cultured Pearl' || $stoneType == 'South Sea Cultured Pearl' || $stoneType == 'Golden Japanese Cultured Pearl' || $stoneType == 'Golden South Sea Cultured Pearl' || $stoneType == 'Tahitian Cultured Pearl'){
				$stoneTitle = 'Pearl';
			}
			else{
				$stoneTitle = $stoneType;
			}?>
			<div class="block-sub-heading text-left"><?php echo $stoneTitle;?> Stone Size  &amp; Weight Comparison Chart</div>
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td valign="bottom"><img src="<?php echo $getSkinUrl;?>frontend/ress/default/images/eraserpoint.jpg" class="img-responsive" /></td>
					<?php
					$width='';
					$heighestwidth;
					if(isset($this->arr[$stoneType][$_GET['stoneshape']])){
						$i = 0;
						foreach($this->arr[$stoneType][$_GET['stoneshape']] as $key => $value){
							$i++;				
							$strarr = explode('x',$key);
							if(count($strarr)>1){					
								$width = 55 * $strarr[0]/6;
								$height = 55 * $strarr[1]/6;	
							}
							else{
								$width = 55 * $strarr[0]/6;
								$height = 55 * $strarr[0]/6;	
							} ?>
							<td class="padding-type-5 valign-bottom"><img src="<?php echo $getSkinUrl;?>frontend/angara/default/images/dimcompare/<?php echo (($stoneType == 'Pink Sapphire' || $stoneType == 'Blue Topaz' || $stoneType == 'Black Onyx' || $stoneType == 'Pink Tourmaline' || $stoneType == 'Akoya Cultured Pearl' || $stoneType == 'Freshwater Cultured Pearl' || $stoneType == 'South Sea Cultured Pearl' || $stoneType == 'Golden Japanese Cultured Pearl' || $stoneType == 'Golden South Sea Cultured Pearl' || $stoneType == 'Tahitian Cultured Pearl' || $stoneType == 'Coffee Diamond') ? str_replace(' ','-',$stoneType) : str_replace('Blue Sapphire','Sapphire',$stoneType));?>-<?php echo str_replace(' ','-',$_GET['stoneshape']);?>.png" style="width:<?php echo $width?>px; height:<?php echo $height?>px" alt="" id="imgcompsize<?php echo $i?>" /></td>
						<?php
						}
					}?>
				</tr>
				<tr>
					<td class="text-right high-padding-right"><strong>Size:</strong></td>
					<?php
					if(isset($this->arr[$stoneType][$_GET['stoneshape']])){
						$i = 0;
						foreach($this->arr[$stoneType][$_GET['stoneshape']] as $key => $value){
							$i++; ?>
							<td class="padding-type-5"><?php echo $key;?>mm </td>
					<?php
						}
					}?>
				</tr>
				<tr>
					<td class="text-right high-padding-right"><strong>Approximate Carat Weight:</strong></td>
					<?php
					if(isset($this->arr[$stoneType][$_GET['stoneshape']])){
						$i = 0;
						foreach($this->arr[$stoneType][$_GET['stoneshape']] as $key => $value){
							$i++; ?>
							<td class="padding-type-5"><?php echo $value;?> cts </td>
					<?php
						}
					}?>
				</tr>
			</table>
            
            <?php
			if($stoneType == 'Moissanite'){?>
            	<div class="left max-margin-top" style="font-size:11px;">*All moissanite weights are approximate and listed as diamond equivalent.</div>
            <?php 
			}
		}
    }
	
	public function fillarr(){
		$this->arr['Amethyst']['Cushion']['5'] = '0.6';
		$this->arr['Amethyst']['Cushion']['6'] = '0.9';
		$this->arr['Amethyst']['Cushion']['7'] = '1.3';
		$this->arr['Amethyst']['Cushion']['8'] = '2';
		$this->arr['Amethyst']['Cushion']['9'] = '2.5';
		
		$this->arr['Amethyst']['Emerald Cut']['6x4'] = '0.6';
		$this->arr['Amethyst']['Emerald Cut']['7x5'] = '0.9';
		$this->arr['Amethyst']['Emerald Cut']['8x6'] = '1.3';
		$this->arr['Amethyst']['Emerald Cut']['9x7'] = '2.1';
		$this->arr['Amethyst']['Emerald Cut']['10x8'] = '2.9';
		$this->arr['Amethyst']['Emerald Cut']['11x9'] = '3.8';
		
		$this->arr['Amethyst']['Heart']['4'] = '0.2';
		$this->arr['Amethyst']['Heart']['5'] = '0.35';
		$this->arr['Amethyst']['Heart']['6'] = '0.7';
		$this->arr['Amethyst']['Heart']['7'] = '1.1';
		
		$this->arr['Amethyst']['Marquise']['4x2'] = '0.05';
		$this->arr['Amethyst']['Marquise']['5x2.5'] = '0.1';
		$this->arr['Amethyst']['Marquise']['5x3'] = '0.15';
		$this->arr['Amethyst']['Marquise']['6x3'] = '0.2';
		$this->arr['Amethyst']['Marquise']['8x4'] = '0.55';
		$this->arr['Amethyst']['Marquise']['10x5'] = '0.85';
		$this->arr['Amethyst']['Marquise']['12x6'] = '1.4';
		
		$this->arr['Amethyst']['Oval']['5x3'] = '0.2';
		$this->arr['Amethyst']['Oval']['6x4'] = '0.4';
		$this->arr['Amethyst']['Oval']['7x5'] = '0.7';
		$this->arr['Amethyst']['Oval']['8x6'] = '1.1';
		$this->arr['Amethyst']['Oval']['9x7'] = '1.6';
		$this->arr['Amethyst']['Oval']['10x8'] = '2.3';
		
		$this->arr['Amethyst']['Pear']['6x4'] = '0.35';
		$this->arr['Amethyst']['Pear']['7x5'] = '0.60';
		$this->arr['Amethyst']['Pear']['8x6'] = '1';
		$this->arr['Amethyst']['Pear']['9x6'] = '1.2';
		$this->arr['Amethyst']['Pear']['9x7'] = '1.5';
		
		$this->arr['Amethyst']['Round']['3'] = '0.1';
		$this->arr['Amethyst']['Round']['4'] = '0.25';
		$this->arr['Amethyst']['Round']['5'] = '0.45';
		$this->arr['Amethyst']['Round']['6'] = '0.8';
		$this->arr['Amethyst']['Round']['7'] = '1.1';
		$this->arr['Amethyst']['Round']['8'] = '1.7';
		
		$this->arr['Amethyst']['Square']['3'] = '0.16';
		$this->arr['Amethyst']['Square']['4'] = '0.3';
		$this->arr['Amethyst']['Square']['5'] = '0.6';
		$this->arr['Amethyst']['Square']['6'] = '1';
		
		$this->arr['Amethyst']['Square Emerald Cut']['7'] = '1.3';
		$this->arr['Amethyst']['Square Emerald Cut']['8'] = '2';
		$this->arr['Amethyst']['Square Emerald Cut']['9'] = '2.75';
		
		$this->arr['Amethyst']['Trillion']['4'] = '0.2';
		$this->arr['Amethyst']['Trillion']['5'] = '0.35';
		$this->arr['Amethyst']['Trillion']['6'] = '0.7';
		$this->arr['Amethyst']['Trillion']['7'] = '1.1';
		
		$this->arr['Aquamarine']['Cushion']['4'] = '0.35';
		$this->arr['Aquamarine']['Cushion']['5'] = '0.6';
		$this->arr['Aquamarine']['Cushion']['6'] = '0.8';
		$this->arr['Aquamarine']['Cushion']['9x7'] = '1.7';
		
		$this->arr['Aquamarine']['Emerald Cut']['6x4'] = '0.5';
		$this->arr['Aquamarine']['Emerald Cut']['7x5'] = '0.8';
		$this->arr['Aquamarine']['Emerald Cut']['8x6'] = '1.3';
		$this->arr['Aquamarine']['Emerald Cut']['9x7'] = '2';
		$this->arr['Aquamarine']['Emerald Cut']['10x8'] = '3.0';
		
		$this->arr['Aquamarine']['Square Emerald Cut']['3.5'] = '0.18';
		$this->arr['Aquamarine']['Square Emerald Cut']['4'] = '0.3';
		$this->arr['Aquamarine']['Square Emerald Cut']['7'] = '1.35';
		$this->arr['Aquamarine']['Square Emerald Cut']['8'] = '2.1';	
		$this->arr['Aquamarine']['Square Emerald Cut']['9'] = '2.8';	
		
		$this->arr['Aquamarine']['Heart']['4'] = '0.2';
		$this->arr['Aquamarine']['Heart']['5'] = '0.35';
		$this->arr['Aquamarine']['Heart']['6'] = '0.7';
		$this->arr['Aquamarine']['Heart']['7'] = '1.1';
		
		$this->arr['Aquamarine']['Marquise']['4x2'] = '0.05';
		$this->arr['Aquamarine']['Marquise']['5x3'] = '0.12';
		$this->arr['Aquamarine']['Marquise']['6x3'] = '0.2';
		$this->arr['Aquamarine']['Marquise']['8x4'] = '0.4';
		$this->arr['Aquamarine']['Marquise']['10x5'] = '0.85';
				
		$this->arr['Aquamarine']['Oval']['5x3'] = '0.2';
		$this->arr['Aquamarine']['Oval']['6x4'] = '0.4';
		$this->arr['Aquamarine']['Oval']['7x5'] = '0.6';
		$this->arr['Aquamarine']['Oval']['8x6'] = '1';
		$this->arr['Aquamarine']['Oval']['9x7'] = '1.5';
		$this->arr['Aquamarine']['Oval']['10x8'] = '2.2';
		
		$this->arr['Aquamarine']['Pear']['6x4'] = '0.3';
		$this->arr['Aquamarine']['Pear']['7x5'] = '0.55';	
		$this->arr['Aquamarine']['Pear']['9x6'] = '1';
		$this->arr['Aquamarine']['Pear']['9x7'] = '1.2';
		$this->arr['Aquamarine']['Pear']['10x7'] = '1.3';
		
		$this->arr['Aquamarine']['Round']['3'] = '0.1';
		$this->arr['Aquamarine']['Round']['4'] = '0.2';
		$this->arr['Aquamarine']['Round']['5'] = '0.4';
		$this->arr['Aquamarine']['Round']['6'] = '0.65';
		$this->arr['Aquamarine']['Round']['7'] = '1';
		$this->arr['Aquamarine']['Round']['8'] = '1.55';
		
		$this->arr['Aquamarine']['Square']['2'] = '0.03';	
		$this->arr['Aquamarine']['Square']['3'] = '0.1';
		$this->arr['Aquamarine']['Square']['4'] = '0.25';
		$this->arr['Aquamarine']['Square']['5'] = '0.6';
		$this->arr['Aquamarine']['Square']['6'] = '1.1';
	
		$this->arr['Aquamarine']['Trillion']['4'] = '0.20';
		$this->arr['Aquamarine']['Trillion']['5'] = '0.35';
		$this->arr['Aquamarine']['Trillion']['6'] = '0.65';
		$this->arr['Aquamarine']['Trillion']['7'] = '1';
		$this->arr['Aquamarine']['Trillion']['8'] = '1.50';
		
		$this->arr['Blue Topaz']['Cushion']['6'] = '1.1';
		$this->arr['Blue Topaz']['Cushion']['7'] = '1.7';
		$this->arr['Blue Topaz']['Cushion']['8'] = '2.5';
		$this->arr['Blue Topaz']['Cushion']['9'] = '3.3';
		$this->arr['Blue Topaz']['Cushion']['10'] = '4.5';
		
		$this->arr['Blue Topaz']['Emerald Cut']['6x4'] = '0.7';
		$this->arr['Blue Topaz']['Emerald Cut']['7x5'] = '1.2';
		$this->arr['Blue Topaz']['Emerald Cut']['8x6'] = '1.8';
		$this->arr['Blue Topaz']['Emerald Cut']['9x7'] = '2.8';
		$this->arr['Blue Topaz']['Emerald Cut']['10x8'] = '4';
		
		$this->arr['Blue Topaz']['Oval']['4x3'] = '0.2';
		$this->arr['Blue Topaz']['Oval']['5x4'] = '0.4';
		$this->arr['Blue Topaz']['Oval']['7x5'] = '0.95';
		$this->arr['Blue Topaz']['Oval']['9x7'] = '2.25';
		$this->arr['Blue Topaz']['Oval']['11x9'] = '4.5';
		
		$this->arr['Blue Topaz']['Pear']['6x4'] = '0.5';
		$this->arr['Blue Topaz']['Pear']['7x5'] = '0.8';
		$this->arr['Blue Topaz']['Pear']['8x6'] = '1.3';
		$this->arr['Blue Topaz']['Pear']['9x7'] = '1.8';
		$this->arr['Blue Topaz']['Pear']['10x7'] = '2.1';
		
		$this->arr['Blue Topaz']['Round']['2'] = '0.04';
		$this->arr['Blue Topaz']['Round']['4'] = '0.32';
		$this->arr['Blue Topaz']['Round']['6'] = '1';
		$this->arr['Blue Topaz']['Round']['8'] = '2.25';
		$this->arr['Blue Topaz']['Round']['10'] = '3.9';
		
		$this->arr['Blue Topaz']['Trillion']['5'] = '0.40';
		$this->arr['Blue Topaz']['Trillion']['6'] = '0.80';
		$this->arr['Blue Topaz']['Trillion']['7'] = '1.20';
		$this->arr['Blue Topaz']['Trillion']['8'] = '1.50';
		
		$this->arr['Blue Topaz']['Square']['4'] = '0.44';
		$this->arr['Blue Topaz']['Square']['5'] = '0.75';
		$this->arr['Blue Topaz']['Square']['6'] = '1.35';
		
		$this->arr['Blue Topaz']['Square Emerald Cut']['7'] = '2';
		$this->arr['Blue Topaz']['Square Emerald Cut']['8'] = '3';
		$this->arr['Blue Topaz']['Square Emerald Cut']['9'] = '4.25';
		
		$this->arr['Citrine']['Cushion']['5'] = '0.6';
		$this->arr['Citrine']['Cushion']['6'] = '0.9';
		$this->arr['Citrine']['Cushion']['7'] = '1.4';
		$this->arr['Citrine']['Cushion']['8'] = '2';
		$this->arr['Citrine']['Cushion']['9'] = '2.7';
		$this->arr['Citrine']['Cushion']['10'] = '3.5';
		$this->arr['Citrine']['Cushion']['12'] = '5.7';
		
		$this->arr['Citrine']['Emerald Cut']['6x4'] = '0.6';
		$this->arr['Citrine']['Emerald Cut']['7x5'] = '0.9';
		$this->arr['Citrine']['Emerald Cut']['8x6'] = '1.3';
		$this->arr['Citrine']['Emerald Cut']['9x7'] = '2.1';
		$this->arr['Citrine']['Emerald Cut']['10x8'] = '2.9';
		$this->arr['Citrine']['Emerald Cut']['11x9'] = '3.8';
		
		$this->arr['Citrine']['Square Emerald Cut']['7'] = '1.3';
		$this->arr['Citrine']['Square Emerald Cut']['8'] = '2';
		$this->arr['Citrine']['Square Emerald Cut']['9'] = '2.75';
		
		$this->arr['Citrine']['Heart']['4'] = '0.2';
		$this->arr['Citrine']['Heart']['5'] = '0.35';
		$this->arr['Citrine']['Heart']['6'] = '0.7';
		
		$this->arr['Citrine']['Marquise']['4x2'] = '0.05';
		$this->arr['Citrine']['Marquise']['5x2.5'] = '0.1';
		$this->arr['Citrine']['Marquise']['5x3'] = '0.15';
		$this->arr['Citrine']['Marquise']['6x3'] = '0.2';
		$this->arr['Citrine']['Marquise']['8x4'] = '0.55';
		$this->arr['Citrine']['Marquise']['10x5'] = '0.85';
		$this->arr['Citrine']['Marquise']['12x6'] = '1.4';
		
		$this->arr['Citrine']['Oval']['5x3'] = '0.2';
		$this->arr['Citrine']['Oval']['6x4'] = '0.4';
		$this->arr['Citrine']['Oval']['7x5'] = '0.7';
		$this->arr['Citrine']['Oval']['8x6'] = '1.1';
		$this->arr['Citrine']['Oval']['9x7'] = '1.8';
		$this->arr['Citrine']['Oval']['10x8'] = '2.3';
		$this->arr['Citrine']['Oval']['12x10'] = '4.1';
		
		$this->arr['Citrine']['Pear']['6x4'] = '0.35';
		$this->arr['Citrine']['Pear']['7x5'] = '0.65';
		$this->arr['Citrine']['Pear']['8x6'] = '1';
		$this->arr['Citrine']['Pear']['9x6'] = '1.2';
		$this->arr['Citrine']['Pear']['9x7'] = '1.5';
		$this->arr['Citrine']['Pear']['12x8'] = '2.6';
		
		$this->arr['Citrine']['Round']['3'] = '0.1';
		$this->arr['Citrine']['Round']['4'] = '0.25';
		$this->arr['Citrine']['Round']['5'] = '0.45';
		$this->arr['Citrine']['Round']['6'] = '0.8';
		$this->arr['Citrine']['Round']['7'] = '1.35';
		$this->arr['Citrine']['Round']['8'] = '1.8';
		$this->arr['Citrine']['Round']['9'] = '2.4';
		
		$this->arr['Citrine']['Square']['4'] = '0.3';
		$this->arr['Citrine']['Square']['5'] = '0.6';
		$this->arr['Citrine']['Square']['6'] = '1.1';
		
		$this->arr['Citrine']['Trillion']['4'] = '0.20';
		$this->arr['Citrine']['Trillion']['5'] = '0.35';
		$this->arr['Citrine']['Trillion']['6'] = '0.70';
		$this->arr['Citrine']['Trillion']['7'] = '1.10';
		$this->arr['Citrine']['Trillion']['8'] = '1.70';
		
		$this->arr['Citrine']['Drop']['25x15'] = '24.00';
		
		$this->arr['Emerald']['Cushion']['5'] = '0.4';
		$this->arr['Emerald']['Cushion']['6'] = '0.8';
		$this->arr['Emerald']['Cushion']['9x7'] = '1.85';
		
		$this->arr['Emerald']['Emerald Cut']['6x4'] = '0.5';
		$this->arr['Emerald']['Emerald Cut']['7x5'] = '0.8';
		$this->arr['Emerald']['Emerald Cut']['8x6'] = '1.2';
		$this->arr['Emerald']['Emerald Cut']['9x7'] = '1.8';
		$this->arr['Emerald']['Emerald Cut']['10x8'] = '2.75';
		
		$this->arr['Emerald']['Heart']['4'] = '0.2';
		$this->arr['Emerald']['Heart']['5'] = '0.35';
		$this->arr['Emerald']['Heart']['6'] = '0.7';
		
		$this->arr['Emerald']['Marquise']['4x2'] = '0.05';
		$this->arr['Emerald']['Marquise']['5x2.5'] = '0.2';
		$this->arr['Emerald']['Marquise']['5x3'] = '0.15';
		$this->arr['Emerald']['Marquise']['6x3'] = '0.3';
		$this->arr['Emerald']['Marquise']['8x4'] = '0.5';
		$this->arr['Emerald']['Marquise']['10x5'] = '0.85';
		$this->arr['Emerald']['Marquise']['12x6'] = '1.3';
		
		$this->arr['Emerald']['Oval']['5x3'] = '0.2';
		$this->arr['Emerald']['Oval']['6x4'] = '0.4';
		$this->arr['Emerald']['Oval']['7x5'] = '0.7';
		$this->arr['Emerald']['Oval']['8x6'] = '1.1';
		$this->arr['Emerald']['Oval']['9x7'] = '1.5';
		$this->arr['Emerald']['Oval']['10x8'] = '2.25';
		
		$this->arr['Emerald']['Pear']['6x4'] = '0.35';
		$this->arr['Emerald']['Pear']['7x5'] = '0.5';
		$this->arr['Emerald']['Pear']['8x6'] = '0.8';
		$this->arr['Emerald']['Pear']['9x6'] = '1';
		$this->arr['Emerald']['Pear']['9x7'] = '1.5';
		
		$this->arr['Emerald']['Round']['3'] = '0.1';
		$this->arr['Emerald']['Round']['4'] = '0.25';
		$this->arr['Emerald']['Round']['5'] = '0.45';
		$this->arr['Emerald']['Round']['6'] = '0.8';
		$this->arr['Emerald']['Round']['7'] = '1.35';
		$this->arr['Emerald']['Round']['8'] = '1.8';
		
		$this->arr['Emerald']['Square']['4'] = '0.3';
		$this->arr['Emerald']['Square']['5'] = '0.55';
		$this->arr['Emerald']['Square']['6'] = '1';
		
		$this->arr['Emerald']['Trillion']['4'] = '0.25';
		$this->arr['Emerald']['Trillion']['5'] = '0.35';
		$this->arr['Emerald']['Trillion']['6'] = '0.7';
		$this->arr['Emerald']['Trillion']['7'] = '1.1';
		
		$this->arr['Garnet']['Cushion']['5'] = '0.8';
		$this->arr['Garnet']['Cushion']['6'] = '1.2';
		$this->arr['Garnet']['Cushion']['7'] = '1.35';
		$this->arr['Garnet']['Cushion']['8'] = '2.5';
		$this->arr['Garnet']['Cushion']['9'] = '3.6';
		$this->arr['Garnet']['Cushion']['10'] = '4.5';
		
		$this->arr['Garnet']['Emerald Cut']['6x4'] = '0.65';
		$this->arr['Garnet']['Emerald Cut']['7x5'] = '1.1';
		$this->arr['Garnet']['Emerald Cut']['8x6'] = '1.6';
		$this->arr['Garnet']['Emerald Cut']['9x7'] = '3';
		$this->arr['Garnet']['Emerald Cut']['10x8'] = '3.7';
		$this->arr['Garnet']['Emerald Cut']['11x9'] = '5.3';
		
		$this->arr['Garnet']['Heart']['4'] = '0.25';
		$this->arr['Garnet']['Heart']['5'] = '0.45';
		$this->arr['Garnet']['Heart']['6'] = '0.9';
		$this->arr['Garnet']['Heart']['7'] = '1.4';
		
		$this->arr['Garnet']['Marquise']['4x2'] = '0.05';
		$this->arr['Garnet']['Marquise']['5x2.5'] = '0.15';
		$this->arr['Garnet']['Marquise']['5x3'] = '0.195';
		$this->arr['Garnet']['Marquise']['6x3'] = '0.25';
		$this->arr['Garnet']['Marquise']['8x4'] = '0.7';
		$this->arr['Garnet']['Marquise']['10x5'] = '1.1';
		$this->arr['Garnet']['Marquise']['12x6'] = '1.8';
		
		$this->arr['Garnet']['Oval']['5x3'] = '0.25';
		$this->arr['Garnet']['Oval']['6x4'] = '0.5';
		$this->arr['Garnet']['Oval']['7x5'] = '0.9';
		$this->arr['Garnet']['Oval']['8x6'] = '1.4';
		$this->arr['Garnet']['Oval']['9x7'] = '2.3';
		$this->arr['Garnet']['Oval']['10x8'] = '3.25';
		
		$this->arr['Garnet']['Pear']['6x4'] = '0.45';
		$this->arr['Garnet']['Pear']['7x5'] = '0.85';
		$this->arr['Garnet']['Pear']['8x6'] = '1.4';
		$this->arr['Garnet']['Pear']['9x7'] = '1.8';
		$this->arr['Garnet']['Pear']['10x7'] = '2.1';
		
		$this->arr['Garnet']['Round']['3'] = '0.15';
		$this->arr['Garnet']['Round']['4'] = '0.35';
		$this->arr['Garnet']['Round']['5'] = '0.6';
		$this->arr['Garnet']['Round']['6'] = '1';
		$this->arr['Garnet']['Round']['7'] = '1.6';
		$this->arr['Garnet']['Round']['8'] = '2.2';
		$this->arr['Garnet']['Round']['9'] = '3.2';
		
		$this->arr['Garnet']['Square']['3'] = '0.16';
		$this->arr['Garnet']['Square']['4'] = '0.35';
		$this->arr['Garnet']['Square']['5'] = '0.75';
		$this->arr['Garnet']['Square']['6'] = '1.4';
		
		$this->arr['Garnet']['Square Emerald Cut']['4'] = '0.35';
		$this->arr['Garnet']['Square Emerald Cut']['7'] = '1.9';
		$this->arr['Garnet']['Square Emerald Cut']['8'] = '2.8';
		$this->arr['Garnet']['Square Emerald Cut']['9'] = '4.2';
		
		$this->arr['Garnet']['Trillion']['4'] = '0.25';
		$this->arr['Garnet']['Trillion']['5'] = '0.45';
		$this->arr['Garnet']['Trillion']['6'] = '0.90';
		$this->arr['Garnet']['Trillion']['7'] = '1.40';
		$this->arr['Garnet']['Trillion']['8'] = '1.80';
		
		$this->arr['Garnet']['Drop']['8x5'] = '1.6';
		$this->arr['Garnet']['Drop']['14x8'] = '6';
		
		$this->arr['Opal']['Cushion']['5'] = '0.32';
		$this->arr['Opal']['Cushion']['6'] = '0.62';
		$this->arr['Opal']['Cushion']['7'] = '0.85';
		$this->arr['Opal']['Cushion']['8'] = '1.25';
		$this->arr['Opal']['Cushion']['9'] = '1.65';
		
		$this->arr['Opal']['Emerald Cut']['6x4'] = '0.45';
		$this->arr['Opal']['Emerald Cut']['7x5'] = '0.65';
		$this->arr['Opal']['Emerald Cut']['8x6'] = '1';
		$this->arr['Opal']['Emerald Cut']['9x7'] = '1.495';
		$this->arr['Opal']['Emerald Cut']['10x8'] = '2.1';
		
		$this->arr['Opal']['Heart']['4'] = '0.15';
		$this->arr['Opal']['Heart']['5'] = '0.25';
		$this->arr['Opal']['Heart']['6'] = '0.5';
		
		$this->arr['Opal']['Marquise']['4x2'] = '0.0325';
		$this->arr['Opal']['Marquise']['5x2.5'] = '0.065';
		$this->arr['Opal']['Marquise']['5x3'] = '0.0975';
		$this->arr['Opal']['Marquise']['6x3'] = '0.15';
		$this->arr['Opal']['Marquise']['8x4'] = '0.4';
		$this->arr['Opal']['Marquise']['10x5'] = '0.6';
		$this->arr['Opal']['Marquise']['12x6'] = '1';
		
		$this->arr['Opal']['Oval']['5x3'] = '0.16';
		$this->arr['Opal']['Oval']['6x4'] = '0.3';
		$this->arr['Opal']['Oval']['7x5'] = '0.5';
		$this->arr['Opal']['Oval']['8x6'] = '0.75';
		$this->arr['Opal']['Oval']['9x7'] = '1.1';
		$this->arr['Opal']['Oval']['10x8'] = '1.6';
		
		$this->arr['Opal']['Pear']['6x4'] = '0.23';
		$this->arr['Opal']['Pear']['7x5'] = '0.35';
		$this->arr['Opal']['Pear']['8x6'] = '0.5';
		$this->arr['Opal']['Pear']['9x6'] = '0.7';
		$this->arr['Opal']['Pear']['10x7'] = '1';
		
		$this->arr['Opal']['Round']['3'] = '0.065';
		$this->arr['Opal']['Round']['4'] = '0.16';
		$this->arr['Opal']['Round']['5'] = '0.3';
		$this->arr['Opal']['Round']['6'] = '0.55';
		$this->arr['Opal']['Round']['7'] = '0.75';
		$this->arr['Opal']['Round']['8'] = '1.15';
		$this->arr['Opal']['Round']['9'] = '1.5';
		
		$this->arr['Opal']['Square']['4'] = '0.25';
		$this->arr['Opal']['Square']['5'] = '0.45';
		$this->arr['Opal']['Square']['6'] = '0.8';
		
		$this->arr['Opal']['Trillion']['4'] = '0.15';
		$this->arr['Opal']['Trillion']['5'] = '0.25';
		$this->arr['Opal']['Trillion']['6'] = '0.5';
		$this->arr['Opal']['Trillion']['7'] = '0.8';
		
		$this->arr['Peridot']['Cushion']['4'] = '0.35';
		$this->arr['Peridot']['Cushion']['5'] = '0.6';
		$this->arr['Peridot']['Cushion']['6'] = '1.1';
		$this->arr['Peridot']['Cushion']['7'] = '1.4';		
		$this->arr['Peridot']['Cushion']['8'] = '2.3';
		$this->arr['Peridot']['Cushion']['9'] = '2.8';
		$this->arr['Peridot']['Cushion']['10'] = '3.5';		
		
		$this->arr['Peridot']['Emerald Cut']['6x4'] = '0.6';
		$this->arr['Peridot']['Emerald Cut']['7x5'] = '0.9';
		$this->arr['Peridot']['Emerald Cut']['8x6'] = '1.6';
		$this->arr['Peridot']['Emerald Cut']['9x7'] = '2.3';
		$this->arr['Peridot']['Emerald Cut']['10x8'] = '2.9';
		$this->arr['Peridot']['Emerald Cut']['11x9'] = '3.8';
		
		$this->arr['Peridot']['Heart']['4'] = '0.2';
		$this->arr['Peridot']['Heart']['5'] = '0.35';
		$this->arr['Peridot']['Heart']['6'] = '0.7';
		$this->arr['Peridot']['Heart']['7'] = '1.1';
		
		$this->arr['Peridot']['Marquise']['4x2'] = '0.05';
		$this->arr['Peridot']['Marquise']['5x2.5'] = '0.1';
		$this->arr['Peridot']['Marquise']['5x3'] = '0.15';
		$this->arr['Peridot']['Marquise']['6x3'] = '0.2';
		$this->arr['Peridot']['Marquise']['8x4'] = '0.6';
		$this->arr['Peridot']['Marquise']['10x5'] = '0.95';
		$this->arr['Peridot']['Marquise']['12x6'] = '1.5';
		
		$this->arr['Peridot']['Oval']['5x3'] = '0.2';
		$this->arr['Peridot']['Oval']['6x4'] = '0.4';
		$this->arr['Peridot']['Oval']['7x5'] = '0.7';
		$this->arr['Peridot']['Oval']['8x6'] = '1.1';
		$this->arr['Peridot']['Oval']['9x7'] = '1.8';
		$this->arr['Peridot']['Oval']['10x8'] = '2.3';
		
		$this->arr['Peridot']['Pear']['6x4'] = '0.35';
		$this->arr['Peridot']['Pear']['7x5'] = '0.65';
		$this->arr['Peridot']['Pear']['8x6'] = '1.1';
		$this->arr['Peridot']['Pear']['9x6'] = '1.2';
		$this->arr['Peridot']['Pear']['9x7'] = '1.6';
		
		$this->arr['Peridot']['Round']['3'] = '0.1';
		$this->arr['Peridot']['Round']['4'] = '0.25';
		$this->arr['Peridot']['Round']['5'] = '0.45';
		$this->arr['Peridot']['Round']['6'] = '0.8';
		$this->arr['Peridot']['Round']['7'] = '1.35';
		$this->arr['Peridot']['Round']['8'] = '2';
		
		$this->arr['Peridot']['Square']['3'] = '0.16';
		$this->arr['Peridot']['Square']['4'] = '0.3';
		$this->arr['Peridot']['Square']['5'] = '0.6';
		$this->arr['Peridot']['Square']['6'] = '1.1';
		
		$this->arr['Peridot']['Square Emerald Cut']['7'] = '1.3';
		$this->arr['Peridot']['Square Emerald Cut']['8'] = '2';
		$this->arr['Peridot']['Square Emerald Cut']['9'] = '2.75';
		
		$this->arr['Peridot']['Trillion']['4'] = '0.20';
		$this->arr['Peridot']['Trillion']['5'] = '0.50';
		$this->arr['Peridot']['Trillion']['6'] = '0.70';
		$this->arr['Peridot']['Trillion']['7'] = '1.10';
		$this->arr['Peridot']['Trillion']['8'] = '1.80';
		
		$this->arr['Ruby']['Cushion']['5'] = '0.6';
		$this->arr['Ruby']['Cushion']['6'] = '1';
		$this->arr['Ruby']['Cushion']['9x7'] = '2.25';
		
		$this->arr['Ruby']['Emerald Cut']['6x4'] = '0.65';
		$this->arr['Ruby']['Emerald Cut']['7x5'] = '1.1';
		$this->arr['Ruby']['Emerald Cut']['8x6'] = '1.7';
		$this->arr['Ruby']['Emerald Cut']['9x7'] = '3';
		$this->arr['Ruby']['Emerald Cut']['10x8'] = '4.3';
		
		$this->arr['Ruby']['Heart']['4'] = '0.25';
		$this->arr['Ruby']['Heart']['5'] = '0.5';
		$this->arr['Ruby']['Heart']['6'] = '0.8';
		
		$this->arr['Ruby']['Marquise']['4x2'] = '0.1';
		$this->arr['Ruby']['Marquise']['5x2.5'] = '0.2';
		$this->arr['Ruby']['Marquise']['5x3'] = '0.2';
		$this->arr['Ruby']['Marquise']['6x3'] = '0.3';
		$this->arr['Ruby']['Marquise']['8x4'] = '0.8';
		$this->arr['Ruby']['Marquise']['10x5'] = '1.3';
		$this->arr['Ruby']['Marquise']['12x6'] = '2.1';
		
		$this->arr['Ruby']['Oval']['5x3'] = '0.25';
		$this->arr['Ruby']['Oval']['6x4'] = '0.6';
		$this->arr['Ruby']['Oval']['7x5'] = '0.90';
		$this->arr['Ruby']['Oval']['8x6'] = '1.25';
		$this->arr['Ruby']['Oval']['9x7'] = '2';
		$this->arr['Ruby']['Oval']['10x8'] = '3.5';
		
		$this->arr['Ruby']['Pear']['6x4'] = '0.4';
		$this->arr['Ruby']['Pear']['7x5'] = '0.75';
		$this->arr['Ruby']['Pear']['8x6'] = '1';
		$this->arr['Ruby']['Pear']['9x6'] = '1.6';
		$this->arr['Ruby']['Pear']['9x7'] = '2';
		
		$this->arr['Ruby']['Round']['3'] = '0.15';
		$this->arr['Ruby']['Round']['4'] = '0.30';
		$this->arr['Ruby']['Round']['5'] = '0.6';
		$this->arr['Ruby']['Round']['6'] = '1';
		$this->arr['Ruby']['Round']['7'] = '1.40';
		$this->arr['Ruby']['Round']['8'] = '2.2';
		
		$this->arr['Ruby']['Square']['4'] = '0.4';
		$this->arr['Ruby']['Square']['5'] = '0.8';
		$this->arr['Ruby']['Square']['6'] = '1.25';
		
		$this->arr['Ruby']['Trillion']['4'] = '0.25';
		$this->arr['Ruby']['Trillion']['5'] = '0.5';
		$this->arr['Ruby']['Trillion']['6'] = '1';
		$this->arr['Ruby']['Trillion']['7'] = '1.65';
		
		$this->arr['Blue Sapphire']['Cushion']['5'] = '0.6';
		$this->arr['Blue Sapphire']['Cushion']['6'] = '1';
		$this->arr['Blue Sapphire']['Cushion']['9x7'] = '2.25';
		
		$this->arr['Blue Sapphire']['Emerald Cut']['6x4'] = '0.65';
		$this->arr['Blue Sapphire']['Emerald Cut']['7x5'] = '1.1';
		$this->arr['Blue Sapphire']['Emerald Cut']['8x6'] = '1.5';
		$this->arr['Blue Sapphire']['Emerald Cut']['9x7'] = '2.25';
		$this->arr['Blue Sapphire']['Emerald Cut']['10x8'] = '3.5';
		
		$this->arr['Blue Sapphire']['Heart']['4'] = '0.25';
		$this->arr['Blue Sapphire']['Heart']['5'] = '0.35';
		$this->arr['Blue Sapphire']['Heart']['6'] = '0.8';
		
		$this->arr['Blue Sapphire']['Marquise']['4x2'] = '0.1';
		$this->arr['Blue Sapphire']['Marquise']['5x2.5'] = '0.24';
		$this->arr['Blue Sapphire']['Marquise']['5x3'] = '0.2';
		$this->arr['Blue Sapphire']['Marquise']['6x3'] = '0.35';
		$this->arr['Blue Sapphire']['Marquise']['8x4'] = '0.60';
		$this->arr['Blue Sapphire']['Marquise']['10x5'] = '1.25';
		$this->arr['Blue Sapphire']['Marquise']['12x6'] = '2.1';
		
		$this->arr['Blue Sapphire']['Oval']['5x3'] = '0.3';
		$this->arr['Blue Sapphire']['Oval']['6x4'] = '0.6';
		$this->arr['Blue Sapphire']['Oval']['7x5'] = '0.9';
		$this->arr['Blue Sapphire']['Oval']['8x6'] = '1.35';
		$this->arr['Blue Sapphire']['Oval']['9x7'] = '2';
		$this->arr['Blue Sapphire']['Oval']['10x8'] = '3';
		
		$this->arr['Blue Sapphire']['Pear']['6x4'] = '0.4';
		$this->arr['Blue Sapphire']['Pear']['7x5'] = '0.75';
		$this->arr['Blue Sapphire']['Pear']['8x6'] = '1.25';
		$this->arr['Blue Sapphire']['Pear']['9x6'] = '1.55';
		$this->arr['Blue Sapphire']['Pear']['9x7'] = '1.85';
		
		$this->arr['Blue Sapphire']['Round']['3'] = '0.13';
		$this->arr['Blue Sapphire']['Round']['4'] = '0.35';
		$this->arr['Blue Sapphire']['Round']['5'] = '0.6';
		$this->arr['Blue Sapphire']['Round']['6'] = '1';
		$this->arr['Blue Sapphire']['Round']['7'] = '1.5';
		$this->arr['Blue Sapphire']['Round']['8'] = '2.2';
		
		$this->arr['Blue Sapphire']['Square']['4'] = '0.4';
		$this->arr['Blue Sapphire']['Square']['5'] = '0.6';
		$this->arr['Blue Sapphire']['Square']['6'] = '1';
		
		$this->arr['Blue Sapphire']['Trillion']['4'] = '0.25';
		$this->arr['Blue Sapphire']['Trillion']['5'] = '0.5';
		$this->arr['Blue Sapphire']['Trillion']['6'] = '0.8';
		$this->arr['Blue Sapphire']['Trillion']['7'] = '1.65';
		
		$this->arr['Pink Tourmaline']['Cushion']['5'] = '0.42';	
		$this->arr['Pink Tourmaline']['Cushion']['6'] = '0.7';	
		$this->arr['Pink Tourmaline']['Cushion']['7'] = '0.95';	
		$this->arr['Pink Tourmaline']['Cushion']['8'] = '1.4';	
		$this->arr['Pink Tourmaline']['Cushion']['9'] = '2.1';
		$this->arr['Pink Tourmaline']['Cushion']['10'] = '2.94';
		
		$this->arr['Pink Tourmaline']['Emerald Cut']['6x4'] = '0.42';	
		$this->arr['Pink Tourmaline']['Emerald Cut']['7x5'] = '0.7';	
		$this->arr['Pink Tourmaline']['Emerald Cut']['8x6'] = '1.12';	
		$this->arr['Pink Tourmaline']['Emerald Cut']['9x7'] = '1.75';	
		$this->arr['Pink Tourmaline']['Emerald Cut']['10x8'] = '2.52';
		
		$this->arr['Pink Tourmaline']['Oval']['4x3'] = '0.13';
		$this->arr['Pink Tourmaline']['Oval']['5x4'] = '0.25';	
		$this->arr['Pink Tourmaline']['Oval']['7x5'] = '0.63';	
		$this->arr['Pink Tourmaline']['Oval']['8x6'] = '0.84';
		$this->arr['Pink Tourmaline']['Oval']['9x7'] = '1.61';
		
		$this->arr['Pink Tourmaline']['Pear']['6x4'] = '0.32';	
		$this->arr['Pink Tourmaline']['Pear']['7x5'] = '0.46';	
		$this->arr['Pink Tourmaline']['Pear']['8x5'] = '0.6';	
		$this->arr['Pink Tourmaline']['Pear']['8x6'] = '0.63';	
		$this->arr['Pink Tourmaline']['Pear']['9x6'] = '0.84';
		$this->arr['Pink Tourmaline']['Pear']['9x7'] = '1.12';
		
		$this->arr['Pink Tourmaline']['Round']['2'] = '0.03';	
		$this->arr['Pink Tourmaline']['Round']['3'] = '0.08';	
		$this->arr['Pink Tourmaline']['Round']['4'] = '0.17';	
		$this->arr['Pink Tourmaline']['Round']['5'] = '0.32';	
		$this->arr['Pink Tourmaline']['Round']['6'] = '0.63';
		$this->arr['Pink Tourmaline']['Round']['7'] = '1.09';	
		$this->arr['Pink Tourmaline']['Round']['8'] = '1.4';
		
		$this->arr['Pink Tourmaline']['Square Emerald Cut']['7'] = '0.95';	
		$this->arr['Pink Tourmaline']['Square Emerald Cut']['8'] = '1.4';
		$this->arr['Pink Tourmaline']['Square Emerald Cut']['9'] = '2.1';
		
		$this->arr['Pink Sapphire']['Cushion']['9x7'] = '2.25';		
		
		$this->arr['Pink Sapphire']['Emerald Cut']['7x5'] = '1.15';
		$this->arr['Pink Sapphire']['Emerald Cut']['8x6'] = '1.6';
		$this->arr['Pink Sapphire']['Emerald Cut']['9x7'] = '2';
				
		$this->arr['Pink Sapphire']['Heart']['4'] = '0.25';
		$this->arr['Pink Sapphire']['Heart']['5'] = '0.5';
		$this->arr['Pink Sapphire']['Heart']['6'] = '0.8';
		
		$this->arr['Pink Sapphire']['Trillion']['4'] = '0.25';
		$this->arr['Pink Sapphire']['Trillion']['5'] = '0.5';
		$this->arr['Pink Sapphire']['Trillion']['6'] = '0.8';
		
		$this->arr['Pink Sapphire']['Marquise']['4x2'] = '0.05';		
		
		$this->arr['Pink Sapphire']['Oval']['4x3'] = '0.18';
		$this->arr['Pink Sapphire']['Oval']['6x4'] = '0.6';
		$this->arr['Pink Sapphire']['Oval']['7x5'] = '0.9';
		$this->arr['Pink Sapphire']['Oval']['8x6'] = '1.35';
		$this->arr['Pink Sapphire']['Oval']['9x7'] = '2';
		$this->arr['Pink Sapphire']['Oval']['10x8'] = '3';
		
		$this->arr['Pink Sapphire']['Pear']['4x3'] = '0.2';
		$this->arr['Pink Sapphire']['Pear']['6x4'] = '0.4';
		$this->arr['Pink Sapphire']['Pear']['7x5'] = '0.75';
		$this->arr['Pink Sapphire']['Pear']['8x6'] = '1.25';
		$this->arr['Pink Sapphire']['Pear']['9x6'] = '1.55';
		$this->arr['Pink Sapphire']['Pear']['10x7'] = '2.5';
		
		$this->arr['Pink Sapphire']['Square']['3'] = '0.15';
		$this->arr['Pink Sapphire']['Square']['3.5'] = '0.25';
		$this->arr['Pink Sapphire']['Square']['4'] = '0.4';
		$this->arr['Pink Sapphire']['Square']['5'] = '0.75';
		
		$this->arr['Pink Sapphire']['Round']['2'] = '0.05';
		$this->arr['Pink Sapphire']['Round']['3'] = '0.15';
		$this->arr['Pink Sapphire']['Round']['4'] = '0.35';
		$this->arr['Pink Sapphire']['Round']['5'] = '0.6';
		$this->arr['Pink Sapphire']['Round']['6'] = '1';
		$this->arr['Pink Sapphire']['Round']['7'] = '1.6';				
		
		$this->arr['Tanzanite']['Cushion']['5'] = '0.6';
		$this->arr['Tanzanite']['Cushion']['6'] = '0.9';
		$this->arr['Tanzanite']['Cushion']['9x7'] = '2.15';
				
		$this->arr['Tanzanite']['Emerald Cut']['6x4'] = '0.6';
		$this->arr['Tanzanite']['Emerald Cut']['7x5'] = '0.9';
		$this->arr['Tanzanite']['Emerald Cut']['8x6'] = '1.3';
		$this->arr['Tanzanite']['Emerald Cut']['9x7'] = '2.1';
		$this->arr['Tanzanite']['Emerald Cut']['10x8'] = '2.9';
		
		$this->arr['Tanzanite']['Heart']['4'] = '0.2';
		$this->arr['Tanzanite']['Heart']['5'] = '0.35';
		$this->arr['Tanzanite']['Heart']['6'] = '0.7';
		
		$this->arr['Tanzanite']['Marquise']['4x2'] = '0.05';
		$this->arr['Tanzanite']['Marquise']['5x2.5'] = '0.1';
		$this->arr['Tanzanite']['Marquise']['5x3'] = '0.15';
		$this->arr['Tanzanite']['Marquise']['6x3'] = '0.2';
		$this->arr['Tanzanite']['Marquise']['8x4'] = '0.5';
		$this->arr['Tanzanite']['Marquise']['10x5'] = '0.85';
		$this->arr['Tanzanite']['Marquise']['12x6'] = '1.4';
		
		$this->arr['Tanzanite']['Oval']['5x3'] = '0.2';
		$this->arr['Tanzanite']['Oval']['6x4'] = '0.4';
		$this->arr['Tanzanite']['Oval']['7x5'] = '0.7';
		$this->arr['Tanzanite']['Oval']['8x6'] = '1.1';
		$this->arr['Tanzanite']['Oval']['9x7'] = '1.8';
		$this->arr['Tanzanite']['Oval']['10x8'] = '2.5';		
		
		$this->arr['Tanzanite']['Pear']['6x4'] = '0.35';
		$this->arr['Tanzanite']['Pear']['7x5'] = '0.65';
		$this->arr['Tanzanite']['Pear']['8x6'] = '1';
		$this->arr['Tanzanite']['Pear']['9x6'] = '1.2';
		$this->arr['Tanzanite']['Pear']['9x7'] = '1.6';
		
		$this->arr['Tanzanite']['Round']['3'] = '0.1';
		$this->arr['Tanzanite']['Round']['4'] = '0.25';
		$this->arr['Tanzanite']['Round']['5'] = '0.45';
		$this->arr['Tanzanite']['Round']['6'] = '0.8';
		$this->arr['Tanzanite']['Round']['7'] = '1.35';
		$this->arr['Tanzanite']['Round']['8'] = '1.8';
		
		$this->arr['Tanzanite']['Square']['4'] = '0.3';
		$this->arr['Tanzanite']['Square']['5'] = '0.6';
		$this->arr['Tanzanite']['Square']['6'] = '1';
		
		$this->arr['Tanzanite']['Square Emerald Cut']['4'] = '0.3';
		$this->arr['Tanzanite']['Square Emerald Cut']['7'] = '1.5';
		$this->arr['Tanzanite']['Square Emerald Cut']['8'] = '2.25';
		$this->arr['Tanzanite']['Square Emerald Cut']['9'] = '3.25';
		
		$this->arr['Tanzanite']['Trillion']['4'] = '0.2';
		$this->arr['Tanzanite']['Trillion']['5'] = '0.35';
		$this->arr['Tanzanite']['Trillion']['6'] = '0.7';
		$this->arr['Tanzanite']['Trillion']['7'] = '1.1';
		
		$this->arr['Moissanite']['Oval']['4x2'] = '0.1';
		$this->arr['Moissanite']['Oval']['6x4'] = '0.5';
		$this->arr['Moissanite']['Oval']['8x6'] = '1.5';
		$this->arr['Moissanite']['Oval']['10x8'] = '3';
		$this->arr['Moissanite']['Oval']['12x10'] = '5.8';
		$this->arr['Moissanite']['Oval']['14x12'] = '9.58';
		
		$this->arr['Moissanite']['Princess']['3'] = '0.16';
		$this->arr['Moissanite']['Princess']['5'] = '0.7';
		$this->arr['Moissanite']['Princess']['7'] = '1.8';
		$this->arr['Moissanite']['Princess']['9'] = '3.9';
		
		$this->arr['Moissanite']['Round']['3'] = '0.1';
		$this->arr['Moissanite']['Round']['5'] = '0.5';
		$this->arr['Moissanite']['Round']['7'] = '1.2';
		$this->arr['Moissanite']['Round']['9'] = '2.7';
		$this->arr['Moissanite']['Round']['11'] = '4.75';
	
		$this->arr['Akoya Cultured Pearl']['Round']['5'] = '0.9';
		$this->arr['Akoya Cultured Pearl']['Round']['6'] = '1.6';
		$this->arr['Akoya Cultured Pearl']['Round']['7'] = '2.5';
		$this->arr['Akoya Cultured Pearl']['Round']['8'] = '3.7';
		
		$this->arr['Freshwater Cultured Pearl']['Round']['5'] = '0.9';
		$this->arr['Freshwater Cultured Pearl']['Round']['6'] = '1.6';
		$this->arr['Freshwater Cultured Pearl']['Round']['7'] = '2.5';
		$this->arr['Freshwater Cultured Pearl']['Round']['8'] = '3.7';
		$this->arr['Freshwater Cultured Pearl']['Round']['9'] = '5.25';
		
		$this->arr['South Sea Cultured Pearl']['Round']['9'] = '5.25';
		$this->arr['South Sea Cultured Pearl']['Round']['10'] = '7.2';
		$this->arr['South Sea Cultured Pearl']['Round']['11'] = '9.6';
		$this->arr['South Sea Cultured Pearl']['Round']['12'] = '12.5';
		
		$this->arr['Golden Japanese Cultured Pearl']['Round']['5'] = '0.9';
		$this->arr['Golden Japanese Cultured Pearl']['Round']['6'] = '1.6';
		$this->arr['Golden Japanese Cultured Pearl']['Round']['7'] = '2.5';
		$this->arr['Golden Japanese Cultured Pearl']['Round']['8'] = '3.7';
		
		$this->arr['Golden South Sea Cultured Pearl']['Round']['9'] = '5.25';
		$this->arr['Golden South Sea Cultured Pearl']['Round']['10'] = '7.2';
		$this->arr['Golden South Sea Cultured Pearl']['Round']['11'] = '9.6';
		$this->arr['Golden South Sea Cultured Pearl']['Round']['12'] = '12.5';
		
		$this->arr['Tahitian Cultured Pearl']['Round']['9'] = '5.25';
		$this->arr['Tahitian Cultured Pearl']['Round']['10'] = '7.2';
		$this->arr['Tahitian Cultured Pearl']['Round']['11'] = '9.6';
		$this->arr['Tahitian Cultured Pearl']['Round']['12'] = '12.5';
		
		$this->arr['Black Onyx']['Cushion']['7'] = '1.1';
		$this->arr['Black Onyx']['Cushion']['9'] = '3.5';
		
		$this->arr['Black Onyx']['Drop']['14x8'] = '5.35';
		$this->arr['Black Onyx']['Drop']['16x12'] = '14.5';
		
		$this->arr['Black Onyx']['Oval']['8x6'] = '1.4';
		$this->arr['Black Onyx']['Oval']['9x7'] = '1.9';
		$this->arr['Black Onyx']['Oval']['10x8'] = '2.5';
		
		$this->arr['Black Onyx']['Pear']['9x6'] = '1.1';
		$this->arr['Black Onyx']['Pear']['10x6'] = '1.25';
		$this->arr['Black Onyx']['Pear']['10x7'] = '1.85';
		
		$this->arr['Black Onyx']['Round']['5'] = '0.35';
		$this->arr['Black Onyx']['Round']['6'] = '0.7';
		$this->arr['Black Onyx']['Round']['7'] = '1';
		$this->arr['Black Onyx']['Round']['7.5'] = '2.1';
		
		$this->arr['Black Onyx']['Square']['5'] = '0.5';
		$this->arr['Black Onyx']['Square']['6.7'] = '2';
		
		$this->arr['Morganite']['Round']['5'] = '0.42';
		$this->arr['Morganite']['Round']['6'] = '0.7';
		$this->arr['Morganite']['Round']['7'] = '1.1';
		$this->arr['Morganite']['Round']['8'] = '1.65';
		$this->arr['Morganite']['Round']['9'] = '3';
		$this->arr['Morganite']['Round']['10'] = '3.4';
		
		$this->arr['Morganite']['Pear']['6x4'] = '0.38';
		$this->arr['Morganite']['Pear']['8x5'] = '0.85';
		$this->arr['Morganite']['Pear']['9x7'] = '1.4';
		$this->arr['Morganite']['Pear']['10x8'] = '2.1';
		$this->arr['Morganite']['Pear']['12x8'] = '3';
		
		$this->arr['Morganite']['Oval']['6x4'] = '0.42';
		$this->arr['Morganite']['Oval']['7x5'] = '0.7';
		$this->arr['Morganite']['Oval']['8x6'] = '1.1';
		$this->arr['Morganite']['Oval']['9x7'] = '1.65';
		$this->arr['Morganite']['Oval']['10x8'] = '2.5';
		
		$this->arr['Morganite']['Emerald Cut']['6x4'] = '0.6';
		$this->arr['Morganite']['Emerald Cut']['7x5'] = '0.9';
		$this->arr['Morganite']['Emerald Cut']['8x6'] = '1.4';
		$this->arr['Morganite']['Emerald Cut']['9x7'] = '2.05';
		$this->arr['Morganite']['Emerald Cut']['10x8'] = '3.1';
		
		$this->arr['Morganite']['Cushion']['6'] = '0.75';
		$this->arr['Morganite']['Cushion']['8'] = '1.8';
		$this->arr['Morganite']['Cushion']['10'] = '3.7';
		
		$this->arr['Morganite']['Trillion']['6'] = '0.6';
		$this->arr['Morganite']['Trillion']['7'] = '0.9';
		$this->arr['Morganite']['Trillion']['8'] = '1.35';
		
		$this->arr['Coffee Diamond']['Round']['1'] = '0.01';
		$this->arr['Coffee Diamond']['Round']['2'] = '0.04';
		$this->arr['Coffee Diamond']['Round']['3'] = '0.11';
		$this->arr['Coffee Diamond']['Round']['4'] = '0.23';
		$this->arr['Coffee Diamond']['Round']['5'] = '0.47';
		$this->arr['Coffee Diamond']['Round']['6'] = '0.8';
		
		$this->arr['Kunzite']['Pear']['14x9'] = '5';
		
		$this->arr['Kunzite']['Round']['5'] = '0.7';
		$this->arr['Kunzite']['Round']['6'] = '1.1';
	}
	
	public function couponadded(){
		
	}
	
	public function getSlides($_category){
		$_coreHelper = Mage::helper('core');
		$_imageHelper = Mage::helper('catalog/image');		
		$getSkinUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);	
			
		if($_category == 'ruby-jewelry'){ ?>
			<div class="catimg"><a href="/rings/ruby-rings.html?icid=home|Slider|R|R"><img width="194" height="240" alt="Ruby Rings" title="Ruby Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Ruby-Rings.jpg" /><h3 class="categories-name">Ruby Rings</h3></a></div>
			<div class="catimg"><a href="/earrings/ruby-earrings.html?icid=home|Slider|R|E"><img width="194" height="240" alt="Ruby Earrings" title="Ruby Earrings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Ruby-Earrings.jpg" /><h3 class="categories-name">Ruby Earrings</h3></a></div>
			<div class="catimg"><a href="/pendants/ruby-necklace-pendants.html?icid=home|Slider|R|P"><img width="194" height="240" alt="Ruby Pendants" title="Ruby Pendants" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Ruby-Pendants.jpg" /><h3 class="categories-name">Ruby Pendants</h3></a></div>
			<div class="catimg catboerdernone"><a href="/engagement-rings/ruby-engagement-rings.html?icid=home|Slider|R|ER"><img width="194" height="240" alt="Ruby Engagement Rings" title="Ruby Engagement Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Ruby-Engagement-Rings.jpg" /><h3 class="categories-name">Ruby Engagement Rings</h3></a></div>
		<?php
		}			
		else if($_category == 'sapphire-jewelry'){ ?>
			<div class="catimg"><a href="/rings/sapphire-rings.html?icid=home|Slider|S|R"><img width="194" height="240" alt="Sapphire Rings" title="Sapphire Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Sapphire-Rings.jpg" /><h3 class="categories-name">Sapphire Rings</h3></a></div>
			<div class="catimg"><a href="/earrings/sapphire-earrings.html?icid=home|Slider|S|E"><img width="194" height="240" alt="Sapphire Earrings" title="Sapphire Earrings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Sapphire-Earrings.jpg" /><h3 class="categories-name">Sapphire Earrings</h3></a></div>
			<div class="catimg"><a href="/pendants/sapphire-necklace-pendants.html?icid=home|Slider|S|P"><img width="194" height="240" alt="Sapphire Pendants" title="Sapphire Pendants" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Sapphire-Pendants.jpg" /><h3 class="categories-name">Sapphire Pendants</h3></a></div>
			<div class="catimg catboerdernone"><a href="/engagement-rings/sapphire-engagement-rings.html?icid=home|Slider|S|ER"><img width="194" height="240" alt="Sapphire Engagement Rings" title="Sapphire Engagement Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Sapphire-Engagement-Rings.jpg" /><h3 class="categories-name">Sapphire Engagement Rings</h3></a></div>
		<?php
		}	
		else if($_category == 'emerald-jewelry'){ ?>
			<div class="catimg"><a href="/rings/emerald-rings.html?icid=home|Slider|E|R"><img width="194" height="240" alt="Emerald Rings" title="Emerald Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Emerald-Rings.jpg" /><h3 class="categories-name">Emerald Rings</h3></a></div>
			<div class="catimg"><a href="/earrings/emerald-earrings.html?icid=home|Slider|E|E"><img width="194" height="240" alt="Emerald Earrings" title="Emerald Earrings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Emerald-Earrings.jpg" /><h3 class="categories-name">Emerald Earrings</h3></a></div>
			<div class="catimg"><a href="/pendants/emerald-necklace-pendants.html?icid=home|Slider|E|P"><img width="194" height="240" alt="Emerald Pendants" title="Emerald Pendants" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Emerald-Pendants.jpg" /><h3 class="categories-name">Emerald Pendants</h3></a></div>
			<div class="catimg catboerdernone"><a href="/engagement-rings/emerald-engagement-rings.html?icid=home|Slider|E|ER"><img width="194" height="240" alt="Emerald Engagement Rings" title="Emerald Engagement Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Emerald-Engagement-Rings.jpg" /><h3 class="categories-name">Emerald Engagement Rings</h3></a></div>
		<?php
		}	
		else if($_category == 'tanzanite-jewelry'){ ?>
			<div class="catimg"><a href="/rings/tanzanite-rings.html?icid=home|Slider|T|R"><img width="194" height="240" alt="Tanzanite Rings" title="Tanzanite Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Tanzanite-Rings.jpg" /><h3 class="categories-name">Tanzanite Rings</h3></a></div>
			<div class="catimg"><a href="/earrings/tanzanite-earrings.html?icid=home|Slider|T|E"><img width="194" height="240" alt="Tanzanite Earrings" title="Tanzanite Earrings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Tanzanite-Earrings.jpg" /><h3 class="categories-name">Tanzanite Earrings</h3></a></div>
			<div class="catimg"><a href="/pendants/tanzanite-necklace-pendants.html?icid=home|Slider|T|P"><img width="194" height="240" alt="Tanzanite Pendants" title="Tanzanite Pendants" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Tanzanite-Pendants.jpg" /><h3 class="categories-name">Tanzanite Pendants</h3></a></div>
			<div class="catimg catboerdernone"><a href="/engagement-rings/tanzanite-engagement-rings.html?icid=home|Slider|T|ER"><img width="194" height="240" alt="Tanzanite Engagement Rings" title="Tanzanite Engagement Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Tanzanite-Engagement-Rings.jpg" /><h3 class="categories-name">Tanzanite Engagement Rings</h3></a></div>
		<?php
		}
		else if($_category == 'diamond-jewelry'){ ?>
			<div class="catimg"><a href="/rings/diamond-rings.html?icid=home|Slider|D|R"><img width="194" height="240" alt="Diamond Rings" title="Diamond Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Diamond-Rings.jpg" /><h3 class="categories-name">Diamond Rings</h3></a></div>
			<div class="catimg"><a href="/earrings/diamond-earrings.html?icid=home|Slider|D|E"><img width="194" height="240" alt="Diamond Earrings" title="Diamond Earrings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Diamond-Earrings.jpg" /><h3 class="categories-name">Diamond Earrings</h3></a></div>
			<div class="catimg"><a href="/pendants/diamond-necklace-pendants.html?icid=home|Slider|D|P"><img width="194" height="240" alt="Diamond Pendants" title="Diamond Pendants" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Diamond-Pendants.jpg" /><h3 class="categories-name">Diamond Pendants</h3></a></div>
			<div class="catimg catboerdernone"><a href="/engagement-rings/diamond-engagement-rings.html?icid=home|Slider|D|ER"><img width="194" height="240" alt="Diamond Engagement Rings" title="Diamond Engagement Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Diamond-Engagement-Rings.jpg" /><h3 class="categories-name">Diamond Engagement Rings</h3></a></div>
		<?php
		}
		else if($_category == 'aquamarine-jewelry'){ ?>
			<div class="catimg"><a href="/rings/aquamarine-rings.html?icid=home|Slider|AQ|R"><img width="194" height="240" alt="Aquamarine Rings" title="Aquamarine Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Aquamarine-Rings.jpg" /><h3 class="categories-name">Aquamarine Rings</h3></a></div>
			<div class="catimg"><a href="/earrings/aquamarine-earrings.html?icid=home|Slider|AQ|E"><img width="194" height="240" alt="Aquamarine Earrings" title="Aquamarine Earrings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Aquamarine-Earrings.jpg" /></a><h3 class="categories-name">Aquamarine Earrings</h3></div>
			<div class="catimg"><a href="/pendants/aquamarine-necklace-pendants.html?icid=home|Slider|AQ|P"><img width="194" height="240" alt="Aquamarine Pendants" title="Aquamarine Pendants" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Aquamarine-Pendants.jpg" /><h3 class="categories-name">Aquamarine Pendants</h3></a></div>
			<div class="catimg catboerdernone"><a href="/jewelry/aquamarine-jewelry.html?icid=home|Slider|AQ|AQJ"><img width="194" height="240" alt="Aquamarine Jewelry" title="Aquamarine Jewelry" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Aquamarine-Jewelry.jpg" /><h3 class="categories-name">Aquamarine Jewelry</h3></a></div>
		<?php
		}
		else if($_category == 'amethyst-jewelry'){ ?>
			<div class="catimg"><a href="/rings/amethyst-rings.html"><img width="194" height="240" alt="Amethyst Rings" title="Amethyst Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Amethyst-Rings.jpg" /><h3 class="categories-name">Amethyst Rings</h3></a></div>
			<div class="catimg"><a href="/earrings/amethyst-earrings.html"><img width="194" height="240" alt="Amethyst Earrings" title="Amethyst Earrings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Amethyst-Earrings.jpg" /><h3 class="categories-name">Amethyst Earrings</h3></a></div>
			<div class="catimg"><a href="/pendants/amethyst-necklace-pendants.html"><img width="194" height="240" alt="Amethyst Pendants" title="Amethyst Pendants" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Amethyst-Pendants.jpg" /><h3 class="categories-name">Amethyst Pendants</h3></a></div>
			<div class="catimg catboerdernone"><a href="/jewelry/aquamarine-jewelry.html"><img width="194" height="240" alt="Amethyst Jewelry" title="Amethyst Jewelry" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Amethyst-Jewelry.jpg" /><h3 class="categories-name">Aquamarine Jewelry</h3></a></div>
		<?php
		}	
		else if($_category == 'garnet-jewelry'){ ?>
			<div class="catimg"><a href="/rings/garnet-rings.html"><img width="194" height="240" alt="Garnet Rings" title="Garnet Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Garnet-Rings.jpg" /><h3 class="categories-name">Garnet Rings</h3></a></div>
			<div class="catimg"><a href="/earrings/garnet-earrings.html"><img width="194" height="240" alt="Garnet Earrings" title="Garnet Earrings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Garnet-Earrings.jpg" /><h3 class="categories-name">Garnet Earrings</h3></a></div>
			<div class="catimg"><a href="/pendants/garnet-necklace-pendants.html"><img width="194" height="240" alt="Garnet Pendants" title="Garnet Pendants" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Garnet-Pendants.jpg" /><h3 class="categories-name">Garnet Pendants</h3></a></div>
			<div class="catimg catboerdernone"><a href="/jewelry/garnet-jewelry.html"><img width="194" height="240" alt="Garnet Jewelry" title="Garnet Jewelry" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Garnet-Jewelry.jpg" /><h3 class="categories-name">Garnet Jewelry</h3></a></div>
		<?php
		}
		else if($_category == 'citrine-jewelry'){ ?>
			<div class="catimg"><a href="/rings/citrine-rings.html"><img width="194" height="240" alt="Citrine Rings" title="Citrine Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Citrine-Rings.jpg" /><h3 class="categories-name">Citrine Rings</h3></a></div>
			<div class="catimg"><a href="/earrings/citrine-earrings.html"><img width="194" height="240" alt="Citrine Earrings" title="Citrine Earrings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Citrine-Earrings.jpg" /><h3 class="categories-name">Citrine Earrings</h3></a></div>
			<div class="catimg"><a href="/pendants/citrine-necklace-pendants.html"><img width="194" height="240" alt="Citrine Pendants" title="Citrine Pendants" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Citrine-Pendants.jpg" /><h3 class="categories-name">Citrine Pendants</h3></a></div>
			<div class="catimg catboerdernone"><a href="/jewelry/citrine-jewelry.html"><img width="194" height="240" alt="Citrine Jewelry" title="Citrine Jewelry" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Citrine-Jewelry.jpg" /><h3 class="categories-name">Citrine Jewelry</h3></a></div>
		<?php
		}
		else if($_category == 'peridot-jewelry'){ ?>
			<div class="catimg"><a href="/rings/peridot-rings.html"><img width="194" height="240" alt="Peridot Rings" title="Peridot Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Peridot-Rings.jpg" /><h3 class="categories-name">Peridot Rings</h3></a></div>
			<div class="catimg"><a href="/earrings/peridot-earrings.html"><img width="194" height="240" alt="Peridot Earrings" title="Peridot Earrings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Peridot-Earrings.jpg" /><h3 class="categories-name">Peridot Earrings</h3></a></div>
			<div class="catimg"><a href="/pendants/peridot-necklace-pendants.html"><img width="194" height="240" alt="Peridot Pendants" title="Peridot Pendants" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Peridot-Pendants.jpg" /><h3 class="categories-name">Peridot Pendants</h3></a></div>
			<div class="catimg catboerdernone"><a href="/jewelry/peridot-jewelry.html"><img width="194" height="240" alt="Peridot Jewelry" title="Peridot Jewelry" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Peridot-Jewelry.jpg" /><h3 class="categories-name">Peridot Jewelry</h3></a></div>
		<?php
		}
		else if($_category == 'opal-jewelry'){ ?>
			<div class="catimg"><a href="/rings/opal-rings.html"><img width="194" height="240" alt="Opal Rings" title="Opal Rings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Opal-Rings.jpg" /><h3 class="categories-name">Opal Rings</h3></a></div>
			<div class="catimg"><a href="/earrings/opal-earrings.html"><img width="194" height="240" alt="Opal Earrings" title="Opal Earrings" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Opal-Earrings.jpg" /><h3 class="categories-name">Opal Earrings</h3></a></div>
			<div class="catimg"><a href="/pendants/opal-necklace-pendants.html"><img width="194" height="240" alt="Opal Pendants" title="Opal Pendants" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Opal-Pendants.jpg" /><h3 class="categories-name">Opal Pendants</h3></a></div>
			<div class="catimg catboerdernone"><a href="/jewelry/opal-jewelry.html"><img width="194" height="240" alt="Opal Jewelry" title="Opal Jewelry" src="<?php echo $getSkinUrl;?>frontend/angara/default/images/home-banner/categories/Opal-Jewelry.jpg" /><h3 class="categories-name">Opal Jewelry</h3></a></div>
		<?php
		}		
	}	
	
	public function getMainBannerSlides($_category){
		$_coreHelper = Mage::helper('core');
		$_imageHelper = Mage::helper('catalog/image'); ?>
		<script>		
		var runMainBannerSlider = true;
		
		function changeCurrentSliderThumb(thumb){
			jQuery('.catimgthumbnail').removeClass('catimgthumbnail-current');
			thumb.addClass('catimgthumbnail-current');
			jQuery('#bigimageholder a').attr('href',thumb.attr('href'));
			jQuery('#bigimageholder img').attr('src',thumb.find('img').attr('src'));
		}
		
		function rotateCurrentSliderThumb(){
			if(runMainBannerSlider){
				var index = 0;
				jQuery('.catimgthumbnail').each(function(i,thumb){
					if(jQuery(thumb).hasClass('catimgthumbnail-current')){
						index = i;
					}
				});
				
				if( index < (jQuery('.catimgthumbnail').length - 1) ){
					changeCurrentSliderThumb(jQuery('.catimgthumbnail:eq('+(index+1)+')'));
				}
				else{
					changeCurrentSliderThumb(jQuery('.catimgthumbnail:eq(0)'));
				}
				
				if(index == 4){
					rotateMainSliderRight();
				}
				
				if(index == 9){
					rotateMainSliderLeft();
				}				
			}
		}
		
		jQuery(function(){
			jQuery('.catimgthumbnail').hover(function(e){				
				changeCurrentSliderThumb(jQuery(this));				
			});
			
			changeCurrentSliderThumb(jQuery('#thumbimgslider .catimgthumbnail:first'));
			
			setInterval(rotateCurrentSliderThumb, 3000);
			
			jQuery('#leftbanner').mouseenter(function(e) {
				runMainBannerSlider = false;
			});
			
			jQuery('#leftbanner').mouseleave(function(e) {
				runMainBannerSlider = true;
			});
		});
		</script>
	<?php		
	}	
	
	//gets the data from a URL  
	public function get_tiny_url($url)  {  
		/*$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data; */ 
	}

	public function getShortPageURL($longURL = NULL) {
		//test it out!
		$tinyurl = file_get_contents("http://tinyurl.com/api-create.php?url=".$longURL);
	    return $tinyurl;		
	}	
}?>