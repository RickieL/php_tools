<?php

/**
 * 配置文件－－主配置文件
 */

//是否需要进行SESSION处理
!defined('SESSION') && define('SESSION', true);

//当前发布的静态压缩的文件版本号
define('MIN_VERSION', 1);

//设置时区
date_default_timezone_set('Asia/Shanghai');

//在线活动时间长度,单位：秒
define('ONLINE_TIME_GAP', 600);

//统一分页长度
define('PAGE_NAV_SIZE', 8);

//初始化配置变量
$cfg = array();

$cfg['path']['conf'] = dirname(__FILE__) . '/';
$cfg['path']['root'] = dirname($cfg['path']['conf']) . '/';

//网站根目录
define('APP_ROOT',substr(dirname(__FILE__), 0, -16));


//加载数据库配置文件
include($cfg['path']['conf'] .'database.cfg.php');

// SQL性能分析开关
define('PROFILE_SQL', true);
// 执行时间超过 MAX_QUERY_TIME秒的SQL语句将被记录下来
define('MAX_QUERY_TIME', 0.5);

//调试开关
if (__ENV__ != 'ONLINE')
{
	define('DEBUG', true);
}
else
{
	define('DEBUG', false);
}

//页面信息
$cfg['page'] = array(
	'charset'			=> 'UTF-8',
	'contentType'		=> 'text/html',
	'title'			=> '',
	'cached'			=> true,
	'engine'			=> 'smarty',
	'css'				=> array(),
	'js'				=> array(),
	);
        
//风格
$cfg['theme'] = array(
	'root'			=> '',
	'current'			=> '',
	);
		
//其他路径
$cfg['path'] = array_merge($cfg['path'], array(
	'lib'				=> $cfg['path']['root'] . 'lib/',
	'class'			=> $cfg['path']['root'] . 'lib/',
	'common'			=> $cfg['path']['root'] . 'lib/',
	'cache'			=> $cfg['path']['root'] . 'cache/',
	'upload'			=> APP_ROOT . 'public/upload/',
	'fonts'			=> APP_ROOT . 'public/fonts/',
	'temp'			=> APP_ROOT . 'public/temp/',
	'module'		=> $cfg['path']['root'] . 'modules/',
	));
    

$cfg['url'] = array();
$cfg['url']['root'] = 'http://yf.chtrak.com/';

//URL设置
$cfg['url'] = array_merge($cfg['url'], array(
	'js'				=> $cfg['url']['root'] . 'public/js/',
	'css'				=> $cfg['url']['root'] . 'public/css/',
	'swf'				=> $cfg['url']['root'] . 'public/swf/',
	'images'			=> $cfg['url']['root'] . 'public/images/',
	'theme'			    => $cfg['url']['root'] . 'public/theme/',
	));

    
//Smarty
$cfg['smarty'] = array(
	'template_dir'	=> $cfg['path']['root'] . $cfg['theme']['current'] . 'templates/',
	'compile_dir'		=> $cfg['path']['cache'] . 'smarty/',
	);
    
	
//cache
$cfg['cache'] = array(
	'root'			=> $cfg['path']['cache'],  // engine=memcached 时为服务器地址 
	'engine'			=> 'file', //file|memcached
	'port'			=> 11211, //engine=memcached 时才有意义 
	'timeout'			=> 60, //engine=memcached 时才有意义 
	);


// 在线时间配置
$cfg['online'] = array(
	'min' => 60,	// 在线时长,最短计算时间,单位秒
	'max' => 300    // 在线时长,最长计算时间,单位秒
);

?>
