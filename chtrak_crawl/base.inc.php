<?php

/**
 * 全站基础配置文件
 * 
 */

// 定义JS版本号
define('HD_JS_VER', '1');

// 定义CSS版本号
define('HD_CSS_VER', '1');

if (isset($_SERVER['SERVER_ADDR']))
{
	$ip = $_SERVER['SERVER_ADDR'];
	if ($ip == '127.0.0.1' || $ip == '127.0.0.1')
	{
		// 定义开发环境
		define('__ENV__', 'DEV');
	}
}
else
{
	// PHP CLI 模式
	$hostname = php_uname('u');
	if (DIRECTORY_SEPARATOR == "\\")
	{
		// 定义开发环境
		define('__ENV__', 'DEV');
	}

}


$__CFG__ = array(
	'DEV' => array(
		'mysql' => array(
			array('host'=> '192.168.101.20', 'user'=> 'root', 'password'=> 'yongfu')
			//array('host'=> '192.168.1.71', 'user'=> 'chtrak', 'password'=> 'yongfu')
		),
		'imgdir' => array(
			'temp_dir' => dirname(__FILE__).'/public/temp/',
		)
	),
);
return $__CFG__[__ENV__];
?>