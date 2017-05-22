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
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


#define('COMPILER_INCLUDE_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'src');
#define('COMPILER_COLLECT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'stat');

define('debug',1);

function ec($code)	{	if(debug==1){	echo '<br>'.$code;		}	}
function ecd($code)	{	if(debug==1){	echo '<br>'.$code;die;	}	}
function pr($array)	{	echo '<pre>';print_r($array);			}
function prd($array){	echo '<pre>';print_r($array);die;		}


function debug_caller_data()
{
    $backtrace = debug_backtrace();

    if (count($backtrace) > 2)
        return $backtrace[count($backtrace)-2];
    elseif (count($backtrace) > 1)
        return $backtrace[count($backtrace)-1];
    else
        return false;
}

function write($var)
{
    var_dump(debug_caller_data());
}


function caller_function()
{
    $abc = '123';
    write($abc);
}


$abc = '123';
//write($abc);
//caller_function();



function back_trace($exit = true) {
  $call_back_methods = '';
  $call_back_methods .= '';
  $call_back_methods .= 'S.N.Function NameLine NumberFile Name';

  $counter = 1;
  foreach (debug_backtrace() as $index => $data) {
    //if (0 == $index) continue;

    $call_back_methods .= '' . $counter++ . '';
    $call_back_methods .= '' . $data['function'] . '';
    $call_back_methods .= '' . $data['line'] . '';
    $call_back_methods .= '' . $data['file'] . '';
  }

  $call_back_methods .= '';

  print $call_back_methods;

  if (true == $exit) exit;
}