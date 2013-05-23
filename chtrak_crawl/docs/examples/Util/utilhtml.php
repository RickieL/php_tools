<?php

/**
 * 框架使用样例－HTML操作
 * 
 * 
 */

require_once('../../../common.inc.php');

import('util.Html');

/* Default Module */
class utilhtml extends Action   
{
	/**
	 * 显示登录页(默认Action)
	 */
	function doDefault() 
	{	

	}
}

$app->run();
?>