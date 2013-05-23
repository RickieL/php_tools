<?php

/**
 * 应用程序
 * 
 * @package lib
 * @subpackage core
 */

class Application 
{
	/**
	 * 配置信息
	 * @var array
	 */
	public $cfg;
	
	/**
	 * 应用程序的名字
	 * @var string
	 */
	public $name;
	
	/**
	 * 模块名称,用于确定引用哪个 $name . $module . 'do.php' 文件
	 * @var string
	 */
	public $module;
	
	/**
	 * 动作名称,用于确定调用模块类中的哪个 do . $action 方法, 默认为 Default
	 * @var string
	 */
	public $action;
	
	/**
	 * application的对象池
	 * @var APage
	 * @access private
	 */
	public $pool = array();

	/**
	 * 构造函数
	 * @param string $name 应用程序名称
	 * @param array $cfg 系统配置数组
	 */
	public function __construct($name = NULL)
	{
		global $cfg;
		$this->cfg = & $cfg;
		if ($name === NULL) 
		{
			$name = basename($_SERVER['SCRIPT_FILENAME'], '.php');
			$p = strpos($name, '.');
			if ($p !== false) 
			{
				$name = substr($name, 0, $p);
			}
		}
		
		$this->name = $name;
		$this->module = isset($_GET['mo']) ? ucfirst($_GET['mo']) : '';
		$this->action = isset($_GET['do']) ? ucfirst($_GET['do']) : 'Default';

		$this->pool['orm'] = array();
		$this->pool['cache'] = array();
	}
	
	
	/**
	 * 设置/返回页面标题
	 * @param string $title
	 * @return Application
	 */
	public function title($title = NULL)
	{
		if ($title === NULL)
		{
			return $this->cfg['page']['title'];
		}
		else
		{
			$this->cfg['page']['title'] = $title;
			return $this;
		}
	}
	
	
	/**
	 * 运行应用程序
	 * @param string $do 动作名称
	 * @param string $mo 模块名称
	 * @return Application
	 */
	public function run($do = NULL, $mo = NULL)
	{
		($mo === NULL) && $mo = $this->module;
		($do === NULL) && $do = $this->action;
		$moduleClass = ($this->name == 'index' && $mo) 
						? $mo : ($this->name . $mo);
		$moduleFile = $moduleClass . '.do.php';
		if (is_file($moduleFile) || empty($mo))
		{
			if (!class_exists($moduleClass))
			{
				if (is_file($moduleFile))
				{
					include_once($moduleFile);
				}
				else 
				{
					$moduleClass = 'Action';
				}
			}
			if (class_exists($moduleClass))
			{
				$module = new $moduleClass($this);
				$actionMethod = 'do' . $do;
				if (method_exists($module, $actionMethod))
				{
					$module->$actionMethod();
				}
				else
				{
					header('HTTP/1.0 404 Not Found');
					header('Content-Type: text/html; charset=UTF-8');
					readfile(APP_ROOT . '404.html');
				}
			}
			else
			{
				throwException('应用程序运行出错.文件 ' . $moduleFile . ' 中找不到类定义:' . $moduleClass, 1002);
			}
		}
		else
		{
			throwException('应用程序运行出错.找不到文件:' . $moduleFile, 1001);
		}
		
		if (PROFILE_SQL && class_exists('MysqlOrmParser') && count(MysqlOrmParser::$suspected_querys) > 0)
		{
			array_unshift(MysqlOrmParser::$suspected_querys, '#', count(MysqlOrmParser::$suspected_querys) .'|'. date('Y-m-d H:i:s') . '|' . $_SERVER['REQUEST_URI'] . '|' . json_encode($_COOKIE));
			array_push(MysqlOrmParser::$suspected_querys, '#');
			$log = $this->log('Profile/QueryProfile', MysqlOrmParser::$suspected_querys, "\r\n");
			$log->write();
		}
		
		if (defined('__ENV__') && __ENV__ != 'ONLINE' && DEBUG) 
		{
			/* MySQL操作达5次以上的记录下来 */	
			if (class_exists('MysqlOrmParser') && count(MysqlOrmParser::$querys) > 0)
			{
				array_unshift(MysqlOrmParser::$querys, $_SERVER['REQUEST_URI']);
				array_push(MysqlOrmParser::$querys, "\r\n");
				$log = $this->log('Profile/MysqlOrmParser', MysqlOrmParser::$querys, "\r\n");
				$log->write();
			}
			
			/* MSSQL操作达5次以上的记录下来 */	
			if (class_exists('MssqlOrmParser') && count(MssqlOrmParser::$querys) > 0)
			{
				array_unshift(MssqlOrmParser::$querys, $_SERVER['REQUEST_URI']);
				array_push(MssqlOrmParser::$querys, "\r\n");
				$log = $this->log('Profile/MssqlOrmParser', MssqlOrmParser::$querys, "\r\n");
				$log->write();
			}
						
			/* Memcached操作达10次以上的记录下来 */	
			if (class_exists('Memcached') && count(Memcached::$querys) > 3)
			{
				array_unshift(Memcached::$querys, $_SERVER['REQUEST_URI']);
				array_push(Memcached::$querys, "\r\n");
				$log = $this->log('Profile/Memcached', Memcached::$querys, "\r\n");
				$log->write();
			}	
			
			/* 运行时间操作0.5秒的记下来 */	
			if (function_exists('xdebug_time_index'))
			{
				if (xdebug_time_index() > 0.5)
				{
					$log = $this->log('Profile/Time', $_SERVER['REQUEST_URI'], "\r\n");
					$log->write();
				}
			}
		}

		return $this;
	}
	
	
	/**
	 * 返回application的page对象,第一次调用时会自动根据配置文件自动创建实例
	 * @param string $engine Page引擎,默认按application.cfg.php中的
	 * 						  $cfg['page']['engine']设置
	 * @return APageFactory
	 */
	public function page($engine = NULL)
	{
		if (!isset($this->pool['page'])) 
		{
		    import('plugins.page.APageFactory');
			($engine === NULL) && $engine = $this->cfg['page']['engine'];
			$this->pool['page'] = APageFactory::create($this, $engine);
		}
		return $this->pool['page'];
	}
	
	
	/**
	 * 返回application的cache对象,第一次调用时会自动根据配置文件自动创建实例
	 * @param string $engine Page引擎,默认按application.cfg.php中的
	 * 						  $cfg['page']['engine']设置
	 * @return Object
	 */
	public function cache($engine = NULL, $path = NULL, $port = NULL, $timeout = NULL)
	{
		$key = md5($engine . '_' . serialize($path) . '_' . $port); 
		if (!isset($this->pool['cache'][$key])) 
		{
			($engine === NULL) && $engine = $this->cfg['cache']['engine'];
			($path === NULL) && $path = $this->cfg['cache']['root'];
			($port === NULL) && $port = $this->cfg['cache']['port'];
			($timeout === NULL && isset($this->cfg['cache']['timeout'])) && $timeout = $this->cfg['cache']['timeout'];
			
			$engine = strtolower($engine);
			switch ($engine)
			{
				case 'redis':
					$className = 'Redisv';
					break;
				case 'memcached':
					$className = 'Memcached';
					break;
				case 'eacache':
					$className = 'eAcache';
					break;
				default:
					$className = 'FileCache';
			}
			
			import('plugins.cache.' . $className);
			
			$this->pool['cache'][$key] = new $className($path, $port, $timeout);
		}
		
		return $this->pool['cache'][$key];
	}
	
	
	/**
	 * 返回application的orm对象,第一次调用时会自动根据配置文件自动创建实例
	 * @param string $params 连接参数
	 * @param array $options 选项
	 * @return OrmSession
	 */
	public function orm($params = NULL, $options = NULL)
	{
		$key = md5(serialize($params));
		if (!isset($this->pool['orm'][$key]))
		{
		    import('plugins.orm.OrmQuery');
			($params === NULL) && $params = $this->cfg['db']['params'];
			($options === NULL) && $options = $this->cfg['db']['options'];
			$this->pool['orm'][$key] = new OrmQuery($params, $options);
		}
		
		return $this->pool['orm'][$key];
	}
	
	
	/**
	 * 返回application的log对象
	 * 
	 * @param string $path 日志路径,在未指定文件名时,将自动使用路径的最后一部分作为文件名
	 * @param array|string $data 日志数据
	 * @param string $sep 日志数据分割符
	 * @return log对象
	 */
	public function log($path = "", $data = array(), $sep = "\t")
	{
		if (!isset($this->pool['log']))
		{
			import('plugins.Log');
			$this->pool['log'] = new Log($path, $data, $sep);
		}
		else
		{
			$this->pool['log']->reset();
		}
		return $this->pool['log'];
	}
	
	
	/**
	 * 返回客户端IP
	 * @return string
	 */
	public function ip()
	{
		if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != 'unknown')
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != 'unknown')
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	
	/**
	 * 页面重定向
	 * @param string $url 重定向目标URL
	 * @param int $mode 重定向模式, 值意义如下:
	 * 						0 通过PHP的header()函数实现
	 * 						1 通过JavaScript的Location实现
	 * 						2 通过JavaScript的Location.replace实现
	 */
	public function redirect($url, $mode = 1)
	{
		switch ($mode)
		{
			case 1: echo '<script>location="' . $url . '";</script>'; break;
			case 2: echo '<script>location.replace("' . $url . '");</script>'; break;
			default: header('Location: ' . $url); break;
		}
		exit;
	}
	
	/**
	 * 获取跳转地址
	 *
	 * @param String $defualt 默认地址
	 * @return String referer 跳转地址
	 */
	public function referer($defualt = '')
	{
		if (!isset($_GET['referer']) && !isset($_SERVER['HTTP_REFERER']))
			return $defualt;
			
		if (isset($_GET['referer']))
			$referer = trim($_GET['referer']);
		elseif (isset($_SERVER['HTTP_REFERER']))
			$referer = trim($_SERVER['HTTP_REFERER']);
			
		if (empty($referer))
			return $defualt;
		else 
		{
			$referer = preg_replace("/([\?&])((sid\=[a-z0-9]{6})(&|$))/i", '\\1', $referer);
			$referer = substr($referer, -1) == '?' ? substr($referer, 0, -1) : $referer;	
			
			return strip_tags($referer);	
		}
	}
}
?>
