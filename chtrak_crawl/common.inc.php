<?php

/**
 * 应用初始化程序
 * 
 */

require_once(dirname(__FILE__) . '/framework/' . 'config/application.cfg.php');

require_once($cfg['path']['lib'] . 'base.inc.php');


$cfg['path']['current'] = dirname($_SERVER['SCRIPT_FILENAME']) . '/';

header('Content-type: ' . $cfg['page']['contentType'] . '; charset=' . $cfg['page']['charset']);

if (DEBUG)
{
	import('core.FirePHP');
}

if (!defined('NO_SESSION') && SESSION)
{
	session_start();	
}
	
// 初始化application
$app = new Application();

?>
