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



require_once 'abstract.php';

class Mirasvit_Shell_Misspell extends Mage_Shell_Abstract
{
    public function run()
    {
        // while (1) {
        //     $text = $this->getRandText(rand(5, 10));
        //     echo $text."\n";
        //     $suggest = Mage::getModel('misspell/suggest');
        //     $suggest->loadByQuery($text);
        // }

        // return;
        // $misspell = Mage::getModel('misspell/misspell');
        // echo $misspell->similarity('Camera Canon', 'Camera Canun')."\n";
        // die();
        // $result = $misspell->getBest('ssamsugn');
        // print_r($result);
        // die();
        // echo $misspell->similarity('case', 'card')."\n";
        // echo $misspell->similarity('case', 'cae')."\n";

        // echo $misspell->similarity('cano', 'canon')."\n";
        // echo $misspell->similarity('cano', 'romano')."\n";

        $standards = array(
            'canon' => array(
                'canin',
                'canun',
                'cnon',
                'canmon',
                'cenun',
            ),
            'samsung' => array(
                'simsung',
                'samsing',
                'asmsung',
                'samsng',
                'samung',
                'sansung',
                'samsg'
            ),
            'camera Canon' => array(
                'cameta Canon',
                'camera Canun',
                'camera Cinon',
                'camira Cinon',
                'camera Cann',
                'cameraCanon',
                'cameraCano',
                'cameraCann',
                'camera Canon',
            ),
            'diamond' => array(
                'diamand',
                'dimond',
                'diamant',
                'dfimond',
                'dimand'
            ),
            'Samsung phone' => array(
                'Samsung phone',
                'Samsungphone',
                'Samsungphine',
                'Samsingphine',
                'Simsungphine',
                'Ssamsugnphone',
                'Smsungphne'
            ),
            'camera case digital' => array(
                'cameracasedigital',
                'camera casedigital',
                'cameracase digital',
                'camra case digial',
                'camera caedigital',
                'camer cise digutal'
            ),
            'case digital' => array(
                'casedigital',
                'casedigutal',
                'cise digutal'
            ),
            'Digital camera' => array(
                'Digtl camra',
                'Digtl carma',
            ),
            'diamond phone' => array(
                'diamondphone',
                'diamantphone',
            ),
            'htc diamond phone' => array(
                'htc diamond phone',
                'htc diamondphone',
                'htcdiamondphone',
            ),
            'sony' => array(
                'siny',
                'sona',
            ),
            'apple' => array(
                'applee',
                'aplle',
            ),
            'Canon Camera M17' => array(
                'Canon Camera M17',
                'CanonCamera M17',
                'CanonCameraM17'
            ),

            'camera, or phone' => array(
                'camera, or phine',
            ),

            'canon rebel' => array(
                'canunrebel',
                'canunribel',
                'canonribel',
                'cenunribel'
            ),
        );
        $i = 0;
        foreach ($standards as $need => $keywords) {
            foreach ($keywords as $keyword) {
                if (!$this->getArg('query') || $this->getArg('query') == $i) {
                    $ts = microtime(true);
                    // for ($i = 0; $i < 10; $i++) {
                        $suggest = Mage::getModel('misspell/misspell');
                        $suggest = $suggest->getSuggest($keyword);
                    // }
                    $te = microtime(true);

                    $this->output($i, $keyword, $suggest, $need, $te - $ts);
                }
                $i++;
            }
        }
    }

    public function output($num, $keyword, $result, $need, $time) {
        if ($need == $result) {
            return false;
        }
        $len = 25;
        $misspell = Mage::getSingleton('misspell/misspell');

        echo '#'.$num;
        echo str_repeat(' ', 5 - strlen($num));

        echo $need;
        echo str_repeat(' ', $len - strlen($need)).'| ';

        echo $keyword;
        echo str_repeat(' ', $len - strlen($keyword));

        echo $result;
        echo str_repeat(' ', $len - strlen($result));

        $a = $misspell->similarity($need, $keyword);
        $b = $misspell->similarity($result, $need);
        echo number_format($a, 0);
        if ($a == $b) {
            echo ' = ';
        } elseif ($a > $b) {
            echo ' > ';
        } elseif ($a < $b) {
            echo ' < ';
        }
        echo number_format($b, 0);
        echo str_repeat(' ', 3);

        // echo number_format($time * 1000, 1);
        // echo str_repeat(' ', 3);

        if ($need != $result) {
            echo str_repeat('!', 3);
        }



        echo "\n";
    }

    public function getRandText($len = 5)
    {
        $str = '';
        for ($i = 0; $i < $len; $i++)
        {
            $str .= chr(rand(ord('a'), ord('z')));
        }

        return $str;
    }
}

$shell = new Mirasvit_Shell_Misspell();
$shell->run();
