<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('feedsamazon')};
CREATE TABLE {$this->getTable('feedsamazon')} (
  `amazon_id` int(11) unsigned NOT NULL auto_increment,
  `stone_name` varchar(50) NOT NULL default '',
  `stone_grade` varchar(15) NOT NULL default '',
  `grade` varchar(15) NOT NULL default '',
  `stone_cut` varchar(50) NOT NULL default '',
  `stone_color` varchar(100) NOT NULL default '',
  `stone_clarity` varchar(100) NOT NULL default '',
  `stone_creation` varchar(50) NOT NULL default '',
  `stone_treatment` varchar(50) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY (`amazon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 



$stoneNames = array(
		"Emerald","Ruby","Blue Sapphire","Tanzanite","Diamond","Amethyst","Opal","Garnet","Aquamarine","Pearl","Peridot","Citrine","Pink Sapphire","Enhanced Blue Diamond","Enhanced Black Diamond"
);
$data = Mage::getModel('feeds/amazon');
foreach($stoneNames as $stoneName){
		if($stoneName == 'Emerald'){
			$stoneGrades = array("A","AA","AAA","AAAA","Lab Created");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of emeralds in terms of quality. Dark green and opaque. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade = 'Good';
					$stoneCut = 'Good';
					$stoneColor = 'Medium Green';
					$stoneClarity = 'Opaque';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Oiled';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of emeralds in terms of quality. Medium green and moderately included. This quality is comparable to that used by leading independent/family jewelers.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Medium Green';
					$stoneClarity = 'Moderately Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Oiled';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of emeralds in terms of quality. Rich medium green, ranges from slightly included to moderately included and exhibits high brilliance. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade = 'Best';
					$stoneCut = 'Ideal';
					$stoneColor = 'Rich Medium Green';
					$stoneClarity = 'Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Oiled';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of emeralds in terms of quality. Truly exceptional rich green, very slightly included and exhibits high brilliance. This quality can be found only at the top boutiques in the world.';
					$grade ='Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Rich Green';
					$stoneClarity = 'Very Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Oiled';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'Lab Created'){
					$grade ='Lab Created';
					$description = '';
					$stoneCut = 'Ideal Cut';
					$stoneColor = 'Bright Deep Green';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Simulated';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
			
			
		}
		else if($stoneName == 'Ruby'){
			$stoneGrades = array("A","AA","AAA","AAAA","Lab Created");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of rubies in terms of quality. Dark pinkish red and opaque. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade ='Good';
					$stoneCut = 'Good';
					$stoneColor = 'Dark Red';
					$stoneClarity = 'Opaque';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of rubies in terms of quality. Medium pinkish red and moderately included. This quality is comparable to that used by leading independent/family jewelers.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Medium Red';
					$stoneClarity = 'Moderately Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of rubies in terms of quality. Medium red, slightly included and exhibits high brilliance. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade ='Best';
					$stoneCut = 'Ideal';
					$stoneColor = 'Medium Red';
					$stoneClarity = 'Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of rubies, in terms of quality. Truly exceptional deep rich red, very slightly included and exhibits high brilliance. This quality can be found only at the top boutiques in the world.';
					$grade = 'Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Deep Rich Red';
					$stoneClarity = 'Very Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'Lab Created'){
					$grade = 'Lab Created';
					$description = '';
					$stoneCut = 'Ideal Cut';
					$stoneColor = 'Bright Deep Red';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Simulated';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
		}
		else if($stoneName == 'Blue Sapphire'){
			$stoneGrades = array("A","AA","AAA","AAAA","Lab Created");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of sapphires in terms of quality. Dark blue and opaque. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade = 'Good';
					$stoneCut = 'Good';
					$stoneColor = 'Dark Blue';
					$stoneClarity = 'Opaque';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of sapphires in terms of quality. Medium blue and moderately included. This quality is comparable to that used by leading independent/family jewelers.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Medium Blue';
					$stoneClarity = 'Moderately Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of sapphires in terms of quality. Medium blue, slightly included and exhibits high brilliance. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade ='Better';
					$stoneCut = 'Ideal';
					$stoneColor = 'Rich Blue';
					$stoneClarity = 'Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of sapphires in terms of quality. Truly exceptional deep rich blue, very slightly included and exhibits high brilliance. This quality can be found only at the top boutiques in the world.';
					$grade = 'Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Deep Rich Blue';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'Lab Created'){
					$grade ='Lab Created';
					$description = '';
					$stoneCut = 'Ideal Cut';
					$stoneColor = 'Bright Deep Blue';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Simulated';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
		
		}
		else if($stoneName == 'Tanzanite'){
			$stoneGrades = array("A","AA","AAA","AAAA","Lab Created");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of tanzanites in terms of quality. Light violet blue and slightly included. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade ='Good';
					$stoneCut = 'Good';
					$stoneColor = 'Light Violet Blue';
					$stoneClarity = 'Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of tanzanites in terms of quality. Medium violet blue and slightly included. This quality is comparable to that used by leading independent/family jewelers.';
					$grade ='Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Medium Violet Blue';
					$stoneClarity = 'Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of tanzanites in terms of quality. Rich violet blue, eye clean and exhibits high brilliance. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade ='Best';
					$stoneCut = 'Ideal';
					$stoneColor = 'Rich Violet blue';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of tanzanites in terms of quality. Truly exceptional rich violet blue, eye clean and exhibits very high brilliance. This quality can be found only at the top boutiques in the world.';
					$grade = 'Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Deep Rich Violet Blue';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'Lab Created'){
					$grade ='Lab Created';
					$description = '';
					$stoneCut = 'Ideal Cut';
					$stoneColor = 'Bright Deep Blue';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Simulated';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
		}
		else if($stoneName == 'Enhanced Black Diamond'){
			$stoneGrades = array("A","AA");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
						$description = 'Enhanced Heated Black color opaque diamonds.';
						$grade = 'Good';
						$stoneCut = 'Very Good';
						$stoneColor = 'Black (Color Enhanced)';
						$stoneClarity = 'I2';
						$stoneCreation = 'Natural';
						$stoneTreatment = 'HPHT/Heat Treated';
						
						$data->setAmazonId(NULL)
							->setStoneName($stoneName)
							->setStoneGrade($stoneGrade)
							->setGrade($grade)
							->setStoneCut($stoneCut)
							->setStoneColor($stoneColor)
							->setStoneClarity($stoneClarity)
							->setStoneCreation($stoneCreation)
							->setStoneTreatment($stoneTreatment)
							->setDescription($description)
							->save();	
					}
				if($stoneGrade == 'AA'){
					$description = 'Enhanced Heated Black color opaque diamonds.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Black (Color Enhanced)';
					$stoneClarity = 'I2';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'HPHT/Heat Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
			}
		}
		else if($stoneName == 'Enhanced Blue Diamond'){
			$stoneGrades = array("AA","AAA");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'AA'){
					$description = 'Enhanced Heated Blue color opaque diamonds.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Blue';
					$stoneClarity = 'Inclusions visible to naked eye';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'HPHT/Heat Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Enhanced Heated Blue color opaque diamonds.';
					$grade = 'Best';
					$stoneCut = 'Very Good';
					$stoneColor = 'Blue';
					$stoneClarity = 'Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'HPHT/Heat Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
			}
		}
		else if($stoneName == 'Diamond'){
			$stoneGrades = array("J I2","I I1","H SI2","G-H VS","Simulated","Black (Color Enhanced) I2","Black I1","Blue (Color Enhanced) I2","Blue I1","GH Black (Color Enhanced) I1-I3","GH I1","GH I1,I3","GH I2,I3","GH I2-I3","GH I4","G-H VS1-VS2","GH, Black (Color Enhanced) I1-I3","GH, Blue (Color Enhanced) I1","GH, Blue (Color Enhanced) I1-I2","GH, Blue (Color Enhanced) I2-I3","GH-SI2","GH-VS2","H I I1","H I4","H SI1-SI2","H-I I1","I1-I4","IJ-I1","JK I1","JK I1-I3","JK I2-I3","JM I2-I3","J-M I2-I3","KM-I2","Yellow I1","H-I1","I I2");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'J I2'){
					$description = 'Top 50% of diamonds in terms of quality. J Color and I2 clarity. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade = '';
					$stoneCut = 'Good';
					$stoneColor = 'J';
					$stoneClarity = 'I2';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'I I1'){
					$description = 'Top 25% of diamonds in terms of quality. I Color and I1 Clarity. This quality is comparable to that used by leading independent/family jewelers.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'I';
					$stoneClarity = 'I1';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'H SI2'){
					$description = 'Top 10% of diamonds in terms of quality. H Color, SI2 Clarity and exhibits high brilliance. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade = '';
					$stoneCut = 'Ideal';
					$stoneColor = 'H';
					$stoneClarity = 'SI2';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'G-H VS'){
					$description = 'Top 1% of diamonds in terms of quality. Truly exceptional G Color, VS Clarity and exhibits very high brilliance. This quality can be found only at the top boutiques in the world.';
					$grade = '';
					$stoneCut = 'Ideal';
					$stoneColor = 'G';
					$stoneClarity = 'VS';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'Simulated'){
					$grade = '';
					$description = '';
					$stoneCut = 'Ideal';
					$stoneColor = 'White';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Simulated';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'Black I1'){
					$description = 'Enhanced Heated Black color opaque diamonds.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'Black';
					$stoneClarity = 'I1';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'HPHT/Heat Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'Blue (Color Enhanced) I2'){
					$description = 'Enhanced Heated Blue color  diamonds.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'Blue (Color Enhanced)';
					$stoneClarity = 'I2';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'HPHT/Heat Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'Blue I1'){
					$description = 'Enhanced Heated Blue color  diamonds.';
					$grade = ''; 
					$stoneCut = 'Very Good';
					$stoneColor = 'Blue';
					$stoneClarity = 'I1';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'HPHT/Heat Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH Black (Color Enhanced) I1-I3'){
					$description = 'Near Colorless diamonds,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade= '';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH Black (Color Enhanced)';
					$stoneClarity = 'I1-I3';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'HPHT/Heat Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH I1'){
					$description = 'Near Colorless diamonds,Included inclusions maybe visible without magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH';
					$stoneClarity = 'I1';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH I1,I3'){
					$description = 'Near Colorless diamonds,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH';
					$stoneClarity = 'I1,I3';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH I2,I3'){
					$description = 'Near Colorless diamonds,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH';
					$stoneClarity = 'I2,I3';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH I2-I3'){
					$description = 'Near Colorless diamonds,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH';
					$stoneClarity = 'I2-I3';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH I4'){
					$description = 'Near Colorless diamonds,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH';
					$stoneClarity = 'I4';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'G-H VS1-VS2'){
					$description = 'Near Colorless diamonds,Very slightly included. Inclusions usually not visible to the naked eye.';
					$grade= '';
					$stoneCut = 'Very Good';
					$stoneColor = 'G-H';
					$stoneClarity = 'VS1-VS2';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH, Black (Color Enhanced) I1-I3'){
					$description = 'Near Colorless diamonds,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade= '';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH, Black (Color Enhanced)';
					$stoneClarity = 'I1-I3';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'HPHT/Heat Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH, Blue (Color Enhanced) I1'){
					$description = 'Near Colorless diamonds,Included inclusions maybe visible without magnification.';
					$grade= '';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH, Blue (Color Enhanced)';
					$stoneClarity = 'I1';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'HPHT/Heat Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH, Blue (Color Enhanced) I1-I2'){
					$description = 'Near Colorless diamonds,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH, Blue (Color Enhanced)';
					$stoneClarity = 'I1-I2';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'HPHT/Heat Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH, Blue (Color Enhanced) I2-I3'){
					$description = 'Near Colorless diamonds,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade ='';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH, Blue (Color Enhanced)';
					$stoneClarity = 'I2-I3';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'HPHT/Heat Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH-SI2'){
					$description = 'Near Colorless diamonds,Slightly included. Inclusions visible under 10X magnification and might be visible with the naked eye.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH';
					$stoneClarity = 'SI2';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'GH-VS2'){
					$description = 'Near Colorless diamonds,Very slightly included. Inclusions usually not visible to the naked eye.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'GH';
					$stoneClarity = 'VS2';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'H I I1'){
					$description = 'Near Colorless diamonds,Included inclusions maybe visible without magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'H I';
					$stoneClarity = 'I1';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'IJ-I1'){
					$description = 'Near Colorless diamonds,Included inclusions maybe visible without magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'IJ';
					$stoneClarity = 'I1';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'JK I1'){
					$description = 'Faint Yellow color diamonds,Included inclusions maybe visible without magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'JK';
					$stoneClarity = 'I1';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'JK I1-I3'){
					$description = 'Faint Yellow color diamonds,,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'JK';
					$stoneClarity = 'I1-I3';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'JK I2-I3'){
					$description = 'Faint Yellow color diamonds,,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'JK';
					$stoneClarity = 'I2-I3';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'JM I2-I3'){
					$description = 'Faint Yellow color diamonds,,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'JM';
					$stoneClarity = 'I2-I3';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'J-M I2-I3'){
					$description = 'Faint Yellow color diamonds,,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade= '';
					$stoneCut = 'Very Good';
					$stoneColor = 'J-M';
					$stoneClarity = 'I2-I3';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'KM-I2'){
					$description = 'Faint Yellow color diamonds,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'KM';
					$stoneClarity = 'I2';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'Yellow I1'){
					$description = 'Yellow color diamondsIncluded inclusions maybe visible without magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'Yellow';
					$stoneClarity = 'I1';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'H-I1'){
					$description = 'Near Colorless diamonds,Included inclusions maybe visible without magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'H';
					$stoneClarity = 'I1';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'I I2'){
					$description = 'Near Colorless diamonds,Inclusions can be seen without magnification and obvious under 10X magnification.';
					$grade = '';
					$stoneCut = 'Very Good';
					$stoneColor = 'I';
					$stoneClarity = 'I2';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Not-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
			}
		}
		else if($stoneName == 'Amethyst'){
			$stoneGrades = array("A","AA","AAA","AAAA");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of amethysts in terms of quality. Light purple and range between slightly included to eye clean. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade ='Good';
					$stoneCut = 'Good';
					$stoneColor = 'Light Purple';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of amethysts in terms of quality. Medium purple and range between slightly included to eye clean. This quality is comparable to that used by leading independent/family jewelers.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Medium Purple';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of amethysts in terms of quality. Deep purple, eye clean and exhibits high brilliance. This quality is comparable to that used by t e top 5th Avenue or Rodeo Drive Jewelers.';
					$grade = 'Best';
					$stoneCut = 'Ideal';
					$stoneColor = 'Deep Purple';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of amethysts in terms of quality. Truly exceptional deep rich purple, eye clean and exhibits very high brilliance. This quality can be found only at the top boutiques in the world.';
					$grade = 'Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Deep Rich Purple';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
		}
		else if($stoneName == 'Opal'){
			$stoneGrades = array("A","AA","AAA","AAAA");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of opals in terms of quality. Dull luster and low iridedcence. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade = 'Good';
					$stoneCut = 'Good';
					$stoneColor = 'Dull Luster';
					$stoneClarity = 'Low Iridescence';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of opals in terms of quality. Medium luster and medium iridescence. This quality is comparable to that used by leading independent/family jewelers.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Medium Luster';
					$stoneClarity = 'Medium Iridescence';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of opals in terms of quality. Rich luster and high iridescence. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade = 'Best';
					$stoneCut = 'Ideal';
					$stoneColor = 'Rich Luster';
					$stoneClarity = 'High Iridescence';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of opals in terms of quality. Truly exceptional luster and very high iridescence. This quality can be found only at the top boutiques in the world.';
					$grade ='Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Exceptional Luster';
					$stoneClarity = 'Very High Iridescence';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
		}
		else if($stoneName == 'Garnet'){
			$stoneGrades = array("A","AA","AAA","AAAA");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of emeralds in terms of quality. Dark green and opaque. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade = 'Good';
					$stoneCut = 'Good';
					$stoneColor = 'Medium Brownish Red';
					$stoneClarity = 'Opaque';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of emeralds in terms of quality. Medium green and moderately included. This quality is comparable to that used by leading independent/family jewelers.';
					$grade ='Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Deep Brownish Red';
					$stoneClarity = 'Moderately Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of emeralds in terms of quality. Rich medium green, ranges from slightly included to moderately included and exhibits high brilliance. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade = 'Best';
					$stoneCut = 'Ideal';
					$stoneColor = 'Deep Brownish Red';
					$stoneClarity = 'Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of emeralds in terms of quality. Truly exceptional rich green, very slightly included and exhibits high brilliance. This quality can be found only at the top boutiques in the world.';
					$grade ='Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Deep Rich Brownish Red';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
		}
		else if($stoneName == 'Aquamarine'){
			$stoneGrades = array("A","AA","AAA","AAAA");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of aquamarines in terms of quality. Very light sea blue and moderately included. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade = 'Good';
					$stoneCut = 'Good';
					$stoneColor = 'Very Light Sea Blue';
					$stoneClarity = 'Moderately Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of aquamarines in terms of quality. Light sea blue and slightly included. This quality is comparable to that used by leading independent/family jewelers.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Light Sea Blue';
					$stoneClarity = 'Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of aquamarines in terms of quality. Medium sea blue, eye clean and exhibits high brilliance. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade ='Best';
					$stoneCut = 'Ideal';
					$stoneColor = 'Medium Sea blue';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of aquamarines in terms of quality. Truly exceptional medium sea blue, eye clean and exhibits very high brilliance. This quality can be found only at the top boutiques in the world.';
					$grade = 'Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Medium Sea blue';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
		}
		else if($stoneName == 'Pearl'){
			$stoneGrades = array("A","AA","AAA","AAAA");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of pearls in terms of quality. Dull luster. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade = 'Good';
					$stoneCut = 'Good';
					$stoneColor = 'Dull Luster';
					$stoneClarity = '';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of pearls in terms of quality. Medium luster. This quality is comparable to that used by leading independent/family jewelers.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Medium Luster';
					$stoneClarity = '';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of pearls in terms of quality. Rich luster. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade = 'Best';
					$stoneCut = 'Ideal';
					$stoneColor = 'Rich Luster';
					$stoneClarity = '';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of pearls in terms of quality. Truly exceptional brilliant luster. This quality can be found only at the top boutiques in the world.';
					$grade = 'Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Exceptional Luster';
					$stoneClarity = '';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
		}
		else if($stoneName == 'Peridot'){
			$stoneGrades = array("A","AA","AAA","AAAA");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of peridots in terms of quality. Pale yellow green and range between slightly included to eye clean. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade = 'Good';
					$stoneCut = 'Good';
					$stoneColor = 'Pale Yellow Green';
					$stoneClarity = 'Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of peridots in terms of quality. Olive green and range between slightly included to eye clean. This quality is comparable to that used by leading independent/family jewelers.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Olive Green';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of peridots in terms of quality. Bottle green, eye clean and exhibits high brilliance. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade ='Best';
					$stoneCut = 'Ideal';
					$stoneColor = 'Bottle Green';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of peridots in terms of quality. Truly exceptional vibrant apple green, eye clean and exhibits very high brilliance. This quality can be found only at the top boutiques in the world.';
					$grade = 'Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Vibrant Apple Green';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
		}
		else if($stoneName == 'Citrine'){
			$stoneGrades = array("A","AA","AAA","AAAA");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of citrines in terms of quality. Light yellow orange and range between slightly included to eye clean. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade = 'Good';
					$stoneCut = 'Good';
					$stoneColor = 'Light Yellow Orange';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of citrines in terms of quality. Medium yellow orange and range between slightly included to eye clean. This quality is comparable to that used by leading independent/family jewelers.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Medium Yellow Orange';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of citrines in terms of quality. Deep yellow orange, eye clean and exhibits high brilliance. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade = 'Best';
					$stoneCut = 'Ideal';
					$stoneColor = 'Deep Yellow Orange';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of citrines in terms of quality. Truly exceptional deep rich yellow orange, eye clean and exhibits very high brilliance. This quality can be found only at the top boutiques in the world.';
					$grade = 'Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Deep Rich Yellow Orange';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
		
		}
		else if($stoneName == 'Pink Sapphire'){
			$stoneGrades = array("A","AA","AAA","AAAA");
			foreach($stoneGrades as $stoneGrade){
				if($stoneGrade == 'A'){
					$description = 'Top 75% of pink sapphires in terms of quality. Dark pink and opaque. This quality is comparable to that used by mall jewelry and chain stores.';
					$grade = 'Good';
					$stoneCut = 'Good';
					$stoneColor = 'Dark Pink';
					$stoneClarity = 'Opaque';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AA'){
					$description = 'Top 33% of pink sapphires in terms of quality. Medium pink and slightly included. This quality is comparable to that used by leading independent/family jewelers.';
					$grade = 'Better';
					$stoneCut = 'Very Good';
					$stoneColor = 'Medium Pink';
					$stoneClarity = 'Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();	
				}
				if($stoneGrade == 'AAA'){
					$description = 'Top 10% of pink sapphires in terms of quality. Medium pink, very slightly included and exhibits high brilliance. This quality is comparable to that used by the top 5th Avenue or Rodeo Drive Jewelers.';
					$grade = 'Best';
					$stoneCut = 'Ideal';
					$stoneColor = 'Medium Pink';
					$stoneClarity = 'Slightly Included';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
				if($stoneGrade == 'AAAA'){
					$description = 'Top 1% of pink sapphires in terms of quality. Truly exceptional deep rich pink, eye clean and exhibits very high brilliance. This quality can be found only at the top boutiques in the world.';
					$grade = 'Heirloom';
					$stoneCut = 'Ideal';
					$stoneColor = 'Deep Rich Pink';
					$stoneClarity = 'Eye Clean';
					$stoneCreation = 'Natural';
					$stoneTreatment = 'Heat-Treated';
					
					$data->setAmazonId(NULL)
                        ->setStoneName($stoneName)
						->setStoneGrade($stoneGrade)
						->setGrade($grade)
						->setStoneCut($stoneCut)
						->setStoneColor($stoneColor)
						->setStoneClarity($stoneClarity)
						->setStoneCreation($stoneCreation)
						->setStoneTreatment($stoneTreatment)
						->setDescription($description)
						->save();
				}
			}
		
		}
	}