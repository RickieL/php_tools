<?php

/**
 * 框架使用样例－文件操作
 * 
 * 
 */

require_once('../../../common.inc.php');

import('util.FileSystem');

/* Default Module */
class utilfile extends Action   
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