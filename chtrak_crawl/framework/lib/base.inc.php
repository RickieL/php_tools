<?php
 
/**
 * 转换数组中所有元素的字符集
 * @package lib
 * 
 */

/**
 * 导入文件
 * @param string $classString 导入文件路径字符串,可以用"."代替"/"
 * @param string $fileType 导入文件类型的扩展名(带"."号),也可以是class/inc(简写方式)
 * @return Exception 如果导入成功则返回true，否则返回异常对象
 * 
 * @example 
 * 		import('interface.Account') => include_once('interface/Account.class.php');
 */
function import($classString, $fileType = 'class') 
{
	global $cfg;
	$filename = $cfg['path']['lib'] . strtr($classString, '.', '/');
	switch ($fileType) {
		//导入类文件 
		case 'class': $filename .= '.class.php'; break;
		//导入包含文件
		case 'inc': $filename .= '.inc.php'; break;
		//自定义导入文件的扩展名
		default: $filename .= $fileType; break;
	}
	if (is_file($filename)) {
		include_once($filename);
	} else {
		exit('file "' . $filename . '" is not found.');
	}
}

//导入Application应用程序处理类
import('core.Action');
import('core.Application');

if (get_magic_quotes_gpc()) 
{
	stripslashesForArray($_COOKIE);
	stripslashesForArray($_POST);
	stripslashesForArray($_GET);
	stripslashesForArray($_REQUEST);
}


/**
 * 为数组中的每个元素取消魔术引用
 * @param mixed $var 要取消魔术引用的变量,可以是数组或字符串
 */
function stripslashesForArray(& $var) 
{
    //$var = is_array($var) ? array_map('stripslashesForArray', $var) : stripslashes($var); --php bug??

    // return $value;
    
	if (is_array($var)) {
		foreach ($var as $key => $val) {
		    if (is_array($val))
                stripslashesForArray($val);
		    else 
                $var[$key] = stripslashes($val);
		}
	} else {
		$var = stripslashes($var);
	}
}


/**
 * 导入模块文件
 * @param string $classString 导入文件路径字符串,可以用"."代替"/"
 * @param string $fileType 导入文件类型的扩展名(带"."号),也可以是class/inc(简写方式)
 * @return Exception 如果导入成功则返回true，否则返回异常对象
 * 
 * @example 
 * 		importModule('gapi.Account') => include_once('modules/Account.class.php');
 */
function importModule($classString, $fileType = 'class') 
{
	global $cfg;
	$filename = $cfg['path']['module'] . strtr($classString, '.', '/');
	switch ($fileType) {
		//导入类文件 
		case 'class': $filename .= '.class.php'; break;
		//导入包含文件
		case 'inc': $filename .= '.inc.php'; break;
		//自定义导入文件的扩展名
		default: $filename .= $fileType; break;
	}
	if (is_file($filename)) {
		include_once($filename);
	} else {
		exit('file "' . $filename . '" is not found.');
	}	
}


/**
 * Debug 函数,可以包含多个参数,逐个输出变量的值
 * 注: 输出中debug_0代表第一个参数,debug_1代表第二个参数,以此类推.
 */
function debug() 
{
	extract(func_get_args(), EXTR_PREFIX_ALL, 'debug');
	trigger_error('Debug');
}


/**
 * 抛出异常处理
 *
 * @param string $msg
 * @param string $code
 */
function throwException($msg, $code) {
	trigger_error($msg . '(' . $code . ')');
}

/**
 * 用户异常监控
 * 
 * @param string $msg	错误信息
 * @return void
 */
function debug_user_notce_handder($errno, $errstr, $errfile, $errline, $errcontext)
{
	ob_start();
	debug_print_backtrace(); 
	$trace = ob_get_contents(); 
	ob_end_clean();
	
	$data = array(
		'URL:' . $_SERVER['REQUEST_URI'],
		'REF:' . $_SERVER['HTTP_REFERER'],
		'Date:' . date("Y-m-d H:i:s"),		
		'INFO:' . "错误号{$errno}, 文件:{$errfile} 行号: {$errno}",
		'MSG:' . $errstr,
		$trace
	);
	import('plugins.Log');
	$log = new Log("Debug/E_USER_NOTICE", $data, "\n");
	$log->write();
	throwException($errstr, E_NOTICE);
}
set_error_handler('debug_user_notce_handder', E_USER_NOTICE);

/**
 * 转换数组中所有元素的字符集
 * @param array $array 源数组
 * @param string $to 目标字符集
 * @param string $from 源字符集
 */
function arrayCharsetConvert(& $array, $to, $from = 'GBK') {
	if (is_array($array)) {
		foreach ($array as $key => $item) {
			if (is_array($item)) {
				arrayCharsetConvert($array[$key], $to, $from);
			} else {
				$array[$key] = iconv($from, $to, $item);
			}
		}
	}
}


/**
 * 提供给游戏API返回的XML格式数据转换为数组格式
 *
 * @param array $list
 * @param int $count
 * @return array
 */
function apiXmlToArray($list, $count=0) {
	$result = array();
	if (isset($list['Context'])) {
		$result = apiXmlToArray($list['Context']);
	} elseif (isset($list['Resource'])) {
		$count = intval($list['Resource attr']['length']);
		$result = apiXmlToArray($list['Resource'], $count);
	} elseif (isset($list['Parameters'])) {
		for ($i=0; $i<$count; $i++) {
			$name = $list['Parameters'][$i . ' attr']['name'];
			$n = intval($list['Parameters'][$i . ' attr']['length']);
			($n==0) && $n = 1;
			if (empty($name)) {
				if ($count == 1) {
					$result = apiXmlToArray($list['Parameters'], $n);
				} elseif (empty($result)) {
					$result = apiXmlToArray($list['Parameters'][$i], $n);
				} else {
					$result = array_merge($result,
							apiXmlToArray($list['Parameters'][$i], $n));
				}
			} else {
				if ($count == 1) {
					$result[$name] = apiXmlToArray($list['Parameters'], $n);
				} else {
					$result[$name] = apiXmlToArray($list['Parameters'][$i], $n);
				}
			}
		}
	} elseif (isset($list['Data'])) {
		for ($i=0; $i<$count; $i++) {
			$name = $list['Data'][$i . ' attr']['name'];
			$n = intval($list['Data'][$i . ' attr']['length']);
			$data = ($count == 1) ? $list['Data'] : $list['Data'][$i];
			if ($n==0) {
				$n = (isset($data['Parameters'][1])) ? count($data['Parameters']) : 1;
			}
			if (empty($name)) {
				if ($count == 1) {
					$result = apiXmlToArray($data, $n);
				} else {
					$result[] = apiXmlToArray($data, $n);
				}
			} else {
				$result[$name] = apiXmlToArray($data, $n);
			}
		}
	} elseif (isset($list['parameter'])) {
		$result[$list['parameter']] = trim($list['result']);
	}
	return $result;
}

/**
 * 根据用户的UserId来散列获取分表名称
 * 
 * @param integer $user_id 用户表中的UserId
 * @return boolean
 */
function table($user_id)
{
    return sprintf('%02x', intval($user_id) % 256);
}

/**
 * 对Smarty中的html标记添加引号的过滤
 *
 * @param String $str
 * @return String
 */
function _htmlspecialchars($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}


/**
 * 根据用户的UserId来获取uc_server的图片地址
 *
 * @param string $userid userid  
 * @param string $size  默认='middle',可以选择 big/small
 * @param string $type (视频认证/暂无) 
 * @return string 头像的路径绝对路径
 */
function head($userid,$size='middle',$type='')
{
	global $base_cfg;
	$size = in_array($size,array('big','middle','small')) ? $size : 'middle';
	$userid = abs(intval($userid));
    $userid = sprintf("%09d", $userid);
	$dir1 = substr($userid, 0, 3);
	$dir2 = substr($userid, 3, 2);
	$dir3 = substr($userid, 5, 2);
	$typeadd = $type == 'real' ? '_real' : '';
	$avatar =  $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($userid, -2).$typeadd."_avatar_$size.jpg";
	$absdir = $base_cfg['imgdir']['avater_dir']. $avatar;
	
	if(!file_exists($absdir))
	{
	   $avatar = 'images/noavatar_'. $size. '.gif';
	}
	
	return $base_cfg['imgdir']['avater_url']. $avatar;
	
}

/**
 * 获取自定义域名
 *
 * @param Integer $uid 用户uid
 * @param String $type USER  GUILD
 */
function domain($uid, $type)
{
	return isset($GLOBALS['DOMAIN'][$type][$uid])?$GLOBALS['DOMAIN'][$type][$uid] : $uid;
}

/**
 * 判断是否为认证用户，如果是，则返回图标
 * @param $uid
 * @param $type
 */
function isauth($uid, $type = 'small')
{
	global $cfg;
	if(!empty($GLOBALS['AUTH_USER']) && array_key_exists($uid, $GLOBALS['AUTH_USER']))
	{
		if($type== 'small')
		{
			if ($GLOBALS['AUTH_USER'][$uid]['Type'] == 1)
				return '<img title="' . $GLOBALS['AUTH_USER'][$uid]['Title'] . '" class="icoCer" src="' . $cfg['url']['root'] . 'public/images/b.gif">';
			else
				return '<img title="' . $GLOBALS['AUTH_USER'][$uid]['Title'] . '" class="ico_sns_famous" src="' . $cfg['url']['root'] . 'public/images/b.gif">';
			
		}
		else
		{
			if ($GLOBALS['AUTH_USER'][$uid]['Type'] == 1)
				return '<img align="absmiddle" title="' . $GLOBALS['AUTH_USER'][$uid]['Title'] . '" src="' . $cfg['url']['root'] . 'public/space/default/images/certification.gif">';
			else
				return '<img align="absmiddle" title="' . $GLOBALS['AUTH_USER'][$uid]['Title'] . '" src="' . $cfg['url']['root'] . 'public/space/default/images/suppoer.gif">';
		}
		
	}

	else
		return '';
}

/**
 * 该函数仅用于返回裁剪头像时的保存目录
 *
 * @param int $userid
 * @return string 头像在本地存放路径
 */
function headCut($user_id)
{
	// 一级目录
	$t1 = sprintf("%02x", intval($user_id) % 256);
	
	// 二级目录
	$t2 = sprintf("%02x", (intval($user_id) / 256) % 256);

	// 表示存放在哪台机子
	$t3 = intval($user_id) % 2;
	
	return "/avatar$t3/$t1/$t2";
}

/**
 * 获取餐馆图片地址
 * 
 * @param integer $pid		图片ID
 * @param integer $type		图片类型 0: 美食, 1: 餐馆
 * @param string $size		图片尺寸 small: 缩略图, big: 大图, 其他: 原图
 * @param integer $server	服务器数,暂时保留
 * @return string			图片路径
 */
function pai_photo($pid, $type = 0, $size = 'small', $server = 2)
{
	$ssize = null;
	if ($type == 0)
	{
		// 美食图片
		$stype = 'share';
		// 175*175
		$size == 'small' && $ssize = '_175';
		// 580*X
		$size == 'big' && $ssize = '_580';
	}
	else
	{
		// 餐馆图片
		$stype = 'shop';
		// 120*90
		$size == 'small' && $ssize = '_120';
		// 580*X
		$size == 'big' && $ssize = '_580';
	}
	$hash1 = sprintf("%02x", $pid % 256);
	$hash2 = sprintf("%02x", $pid / 256 % 256);
	$stype = $type == 0 ? 'share' : 'shop';
	return "http://img1.hoto.cn/{$stype}/{$hash1}/{$hash2}/{$pid}{$ssize}.jpg";	
}

/**
 * 餐馆自增ID加密算法
 * 
 * @param integer $sid		餐馆ID
 * @param integer $type		类型: 0 加密, 1: 解密
 */
function pai_shopid($sid, $type = 0)
{
	return $type == 0 ? $sid ^ 64301908 ^ 45215781 : $sid ^ 45215781 ^ 64301908;
}

/**
 * 好拍电话号码替换加密
 * 
 * @param string $phone		电话/手机号码 只接受半角字符
 */
function pai_phone($phone)
{	
	if (empty($phone))
	{
		return '暂无数据';
	}
	import("util.Safe");
	$code = urlencode(Safe::paicode($phone));
	return '<img class="telpic" src="http://www.haodou.com/pai?do=Phone&code=' . $code . '" alt="" />';
}

/**
 * 刷新squid缓存
 *
 * @param String|Array $urls
 * @param String $squid_key
 * @return boolean
 */
function squid_clear($urls, $squid_key = 'squid_sns')
{
	global $app;	

	if (!is_array($urls))
	{
		$urls = array($urls);
	}
	
	$mc = $app->cache('memcached', $app->cfg['memcacheq_squid']);
	
	foreach ($urls as $v)
	{
		if (!empty($v))
		{
			$mc->set($squid_key, 'squid ' . trim($v));
		}
	}
	
	return true;
}

/**
 * 清除nginx缓存
 * 
 * @param string|array $urls	url地址,支持数组
 * @param boolean|array			单个地址返回true/false; 多个地址返回数组, url => true/false
 */
function nginx_cache_purge($urls)
{
	if (!is_array($urls))
	{
		$urls = array($urls);
	}	
	
	$status = array();
	foreach ($urls as $v)
	{
		if (!empty($v))
		{
			$v = trim($v);			
			if (strpos($v, '/purge/') === false)
			{
				$v = str_replace('.hoto.cn/', '.hoto.cn/purge/', $v);
			}			
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $v);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_exec($ch);
			
			$status[$v] = false;
			if (curl_errno($ch) == 0)
			{
				$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($code == 200 || $code == 404)
				{
					$status[$v] = true;
				}				
			}
			curl_close($ch);
		}
	}
	
	if (count($status) == 1)
	{
		return current($status);
	}
	return $status;
}

/**
 *
 * 计算时间差  
 *
 * @param Integer $timestamp Unix时间戳，如果是数据库过来的，请先strtotime($date)一次
 *  
 *       例如：pgmdate(strtotime($share['CreateTime']),'u') 
 * @param Stirng $format 转换类型，默认是date($format)，传'u'后友好显示时间差
 * @param Integer $timeoffset  
 * @param String uformat  自定义format
 * 
 * @return String 友好时间差|标准format  
 *  
 */ 
function pgmdate($timestamp, $format = 'dt', $timeoffset = 9999, $uformat = '') 
{
	static $dformat, $tformat, $dtformat, $offset;
	$TIMESTAMP = time();
	
	if ($dformat === null) 
	{
		$dformat = 'Y-n-j';
		$tformat = 'H:i';
		$dtformat = $dformat.' '.$tformat;
		$offset = '8';
	}
	
	$timeoffset = $timeoffset == 9999 ? $offset : $timeoffset;
	$timestamp += $timeoffset * 3600;
	$format = empty($format) || $format == 'dt' ? $dtformat : ($format == 'd' ? $dformat : ($format == 't' ? $tformat : $format));
	if ($format == 'u') 
	{
		$todaytimestamp = $TIMESTAMP - ($TIMESTAMP + $timeoffset * 3600) % 86400 + $timeoffset * 3600;
		$s = gmdate(!$uformat ? $dtformat : $uformat, $timestamp);
		$time = $TIMESTAMP + $timeoffset * 3600 - $timestamp;
		
		if ($timestamp >= $todaytimestamp) 
		{
			if ($time > 3600) 
			{
				return intval($time / 3600).'小时前';
			} elseif($time > 1800) 
			{
				return '半小时前';
			} elseif($time > 60) 
			{
				return intval($time / 60).'分钟前';
			} elseif($time > 0) 
			{
				return $time.'秒前';
			} elseif($time == 0) 
			{
				return '刚刚';
			} else {
				return $s;
			}
		} 
		elseif (($days = intval(($todaytimestamp - $timestamp) / 86400)) >= 0 && $days < 7)
		{
			if ($days == 0) 
			{
				return '昨天'.gmdate($tformat, $timestamp);
			} elseif($days == 1) 
			{
				return '前天'.gmdate($tformat, $timestamp);
			} else 
			{
				return ($days + 1).'天前';
			}
		} else {
			return $s;
		}
	} else {
		return gmdate($format, $timestamp);
	}
}

/**
 * 记录日志
 *
 * @param string $path			保存路径和文件名前缀，必选。如"a/b/c", "a/b"为目录， "c"为文件名前缀
 * @param string|array $data	需要保存的数据，必选。如"abcdefg"或array('a','b','c','d')
 * @param string $title			自定义日志文件名部分，非必选。默认为空，如果设置则文件名跟着变化，如设置d，文件名则是：c_d_20120315.log或c_d.log
 * @param boolean $logtime		设置文件名是否需要按时间命名。默认true, 文件名格式：c_20120315.log, 可设置false，文件名格式：c.log
 * @param string $sep			设置数据的分隔符，默认为\t
 * @return boolean				返回记录日志是否成功。 true|false
 */
function chtrak_log($path, $data, $title = "", $logtime = true, $sep = "\t")
{
	if (!$path || !$data)
	{
		return false;
	}
	global $app;
	$log = $app->log();
	$log->setPath($path);
	if ($title)
	{
		$log->setTitle($title);
	}
	if ($logtime === false)
	{
		$log->setNoTime($logtime);
	}
	$log->setData($data);
	$flag = $log->write();
	return $flag;
}
?>