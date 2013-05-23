<?php

/**
 * 自定义异常处理
 *
 * 
 * $Id$
 */

class CustomException extends Exception
{
	
	/**
	 * 异常信息存放根目录
	 *
	 * @var string
	 */
	private $logPath = "";
	
	/**
	 * 是否输出异常追踪
	 *
	 * @var boolean
	 */
	private $trace = true;
	
	/**
	 * 异常代码
	 *
	 * @var integer
	 */
	protected $code = 0;

	/**
	 * 构造函数, 初始化参数
	 *
	 * @param string $msg 		异常消息内容
	 * @param string $logPath 	日志记录路径 e.g. Exception/Error, 值为空时 开发环境下直接输出异常信息
	 * @param integer $code 	异常代码, 默认0
	 * @param boolean $trace 	是否输出异常追踪, 默认是
	 */
	public function __construct($msg = "", $logPath = '', $code = 0, $trace = 1)
	{
		$this->code = (int)$code;
		$this->trace = (boolean)$trace;
		$this->logPath = (! $logPath && (__ENV__ == 'ONLINE')) ? 'Exception/Error' : $logPath;
		parent::__construct($msg, $this->code);
	}

	/**
	 * 异常信息输出
	 */
	public function output()
	{
		$message = $this->getMsg();
		
		if (! empty($this->logPath))
		{
			$this->_log($message);
		}
		elseif (__ENV__ != 'ONLINE')
		{
			$message = implode("\n", $message);
			echo '<pre>';
			echo $message;
			echo '</pre>';
		}
	}

	/**
	 * 获取完整的异常信息内容
	 */
	public function getMsg()
	{
		global $app;
		$data = array('Date: ' . date("Y-m-d H:i:s"), 'IP: ' . $app->ip(), 'URL: ' . $_SERVER['REQUEST_URI'], 'REF: ' . $_SERVER['HTTP_REFERER'], 
		'INFO: ' . $this->getMessage() . ' in ' . $this->getFile() . ' on line ' . $this->getLine());
		if ($this->code != 0)
		{
			$data[] = 'Exception Code: ' . $this->code;
		}
		if ($this->trace)
		{
			$data[] = 'TRACE: ' . $this->getTraceAsString() . "\n";
		}
		return $data;
	}

	/**
	 * 将异常信息记录日志
	 */
	private function _log($data)
	{
		global $app;
		$log = $app->log();
		$save_path = $this->logPath;
		$log->reset()
			->setSep("\n")
			->setPath($save_path)
			->setData($data)
			->write();
	}

}
