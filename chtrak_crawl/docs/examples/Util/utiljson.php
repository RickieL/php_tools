<?php

/**
 * 框架使用样例－JSON操作
 * 
 * 
 */

require_once('../../../common.inc.php');

import('util.Json');

/* Default Module */
class utiljson extends Action   
{
	/**
	 * 显示登录页(默认Action)
	 */
	function doDefault() 
	{	
	    $data = array('a', 'b'=>'roast');
        $json = new Json();
        
        $str_encoded = $json->encode($data);
        
        var_dump($str_encoded);
        
        var_dump($json->decode($str_encoded));
	}
}

$app->run();
?>