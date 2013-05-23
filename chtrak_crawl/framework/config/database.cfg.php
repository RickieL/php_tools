<?php

/**
 * 配置文件－－数据库配置文件
 */

// 加载全站基本配置
$base_cfg = require(dirname(dirname(dirname(__FILE__))) . '/base.inc.php');

//主数据库，默认连接该数据库
$cfg['db'] = array(
	'params'   => array('driver'=> 'mysql', 'host'=> $base_cfg['mysql'][0]['host'], 'name'=> 'chtrak', 'user'=> $base_cfg['mysql'][0]['user'], 'password'=> $base_cfg['mysql'][0]['password']),
	'options'  => array('persistent'=> false, 'tablePrefix' => '','charset'=>'utf8'),
);
?>