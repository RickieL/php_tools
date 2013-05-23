<?php
/**
 * 日志类
 *
 * @package lib
 * @subpackage plugins
 * 
 */

class Log
{
	/**
	 * 日志存放根目录
	 *
	 * @var string
	 */
	private $logPath = "";
	
	/**
	 * 日志数据
	 *
	 * @var array
	 */
	private $data = array();
	
	/**
	 * 设置自定义文件名
	 *
	 * @var string
	 */
	private $title = "";
	
	/**
	 * 文件路径
	 *
	 * @var string
	 */
	private $path = null;
	
	/**
	 * 日志数据分割符号
	 *
	 * @var string
	 */
	private $sep = "\t";
	
	/**
	 * 记录日志时是否附带时间标记
	 *
	 * @var boolean
	 */
	private $logTime = true;

	/**
	 * 构造函数, 初始化参数
	 *
	 * @param string $path 日志路径,在未指定文件名时,将自动使用路径的最后一部分作为文件名
	 * @param array|string $data 日志数据
	 * @param string $sep 日志数据分割符
	 */
	public function __construct($path = "", $data = array(), $sep = "\t")
	{
		$this->logPath = $GLOBALS['cfg']['path']['root'] . 'logs';
		$this->setData($data);
		$this->path = $path;
		$this->sep = $sep;
	}

	/**
	 * 重设所有参数,用于重复利用日志对象
	 *
	 * @return Log
	 */
	public function reset()
	{
		$this->data = array();
		$this->path = "";
		$this->logTime = true;
		$this->sep = "\t";
		$this->title = "";
		return $this;
	}

	/**
	 * 设置自定义文件名
	 *
	 * @param string $title
	 * @return Log
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * 设置日志路径
	 *
	 * @param string $path 日志路径,在未指定文件名时,将自动使用路径的最后一部分作为文件名
	 * @return Log
	 */
	
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * 设置日志数据分割符号
	 *
	 * @param string $sep
	 * @return Log
	 */
	public function setSep($sep)
	{
		$this->sep = $sep;
		return $this;
	}

	/**
	 * 设置记录日志时,不附带时间标记
	 *
	 * @return Log
	 */
	public function setNoTime()
	{
		$this->logTime = false;
		return $this;
	}

	/**
	 * 设置日志数据
	 *
	 * @param array|string $data 日志数据
	 * @return Log
	 */
	public function setData($data)
	{
		if (is_array($data) && count($data) > 0)
		{
			$this->data = array_merge($this->data, $data);
		}
		else if (! empty($data))
		{
			$this->data[] = $data;
		}
		return $this;
	}

	/**
	 * 写入日志
	 *
	 * @return boolean 成功返回true,失败返回false
	 */
	public function write()
	{
		if (empty($this->path))
		{
			return false;
		}
		$filepath = $this->_getPath();
		$data = $this->_getData();
		if ($filepath)
		{
			if (!file_exists($filepath))
			{
				$data = "# LogFrom:" . $_SERVER['SCRIPT_FILENAME'] . " LogSetPath:" . $this->path . " #\n\n" . $data;
			}
			$fp = fopen($filepath, 'a+');
			if ($fp)
			{
				flock($fp, LOCK_EX);
				fwrite($fp, $data);
				flock($fp, LOCK_UN);
				fclose($fp);
				return true;
			}
		}
		return false;
	}

	/**
	 * 取文件的路径
	 *
	 * @return string
	 */
	private function _getPath()
	{
		$path_flag = strpos($this->path, '/');
		if ($path_flag == false || $path_flag > 0)
		{
			$path = $this->logPath . '/' . $this->path;
		}
		else
		{
			$path = $this->logPath . $this->path;
		}
		$path = rtrim($path, '/');
		$filename = basename($path);
		if ($path_flag == false || $path_flag > 0)
		{
			$path = dirname($path);
		}
		$this->_pathValidate($path);
		if (! is_dir($path))
		{
			return false;
		}
		if ($this->title != "")
		{
			$filename .= '_' . $this->title;
		}
		if ($this->logTime)
		{
			$filename .= '_' . date("Ymd");
		}
		$filename .= '.log';
		
		$path .= '/' . $filename;
		
		return $path;
	}

	/**
	 * 取数据
	 *
	 * @return string
	 */
	private function _getData()
	{
		return ltrim(implode($this->sep, $this->data)) . "\n";
	}

	/**
	 * 检测路径(存在则返回,不存在则建立)
	 *
	 * @param string   $path    路径
	 * @return boolean		 	成功返回true,失败返回false
	 */
	private function _pathValidate($path)
	{
		$path = str_replace($this->logPath, '', $path);
		$path_array = preg_split('/\/+/', $path);
		$path_count = count($path_array);
		$tmppath = $this->logPath;
		for ($i = 0; $i < $path_count; $i++)
		{
			if ($path_array[$i] == "")
				continue;
			$tmppath .= ($i == 0 ? "" : "/") . $path_array[$i];
			if (!file_exists($tmppath))
			{
				if (!mkdir($tmppath))
				{
					return false;
				}
			}
		}
		return true;
	}
}
?>