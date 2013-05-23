<?php

/**
 * Action类
 *
 * @package lib
 * @subpackage core
 */

class Action
{
	
	/**
	 * 应用程序类
	 * @var Application
	 * @access protected
	 */
	public $app;
	
	/**
	 * 构造函数
	 *
	 * @param Application &$app 应用程序类
	 */
	public function __construct(& $app)
	{
		$this->app = $app;
		if (!defined('NO_CHECK_LOGIN'))
		{
			$this->initUserState();
		}
	}
	
	public function initUserState()
	{
		if ($_SESSION['chtrak_id'])
		{
			
			$this->db = $this->app->orm()->query();
			$this->db->addTable('User');
			$this->db->addField('*');
			$this->db->addWhere('UserId', $_SESSION['chtrak_id']);
			
			$user_info = $this->db->getRow();
			if (!$user_info)
			{
				$this->app->redirect($this->app->cfg['url']['root'] . "index.php");
			}
		}
		else
		{
			$this->app->redirect($this->app->cfg['url']['root'] . "index.php");
		}
	}
	
	/**
	 * 默认Action
	 */
	public function doDefault()
	{ /* nothing */	}
	
	/**
	 * 通过FriePHP进行调试，将调试信息输出到头消息中
	 *
	 * 相关链接：http://www.firephp.org/
	 * 
	 * @return void
	 */
	public function debug()
	{
		if (DEBUG && false)
		{
			$instance = FirePHP::getInstance(true);
			
			$args = func_get_args();
			return call_user_func_array(array($instance, 'fb'), $args);
			
			return true;
		}
	}
	
	/**
	 * 统一输出
	 * 
	 * @param mixed $data		输出数据
	 * @param string $type		输出类型
	 * @param boolean $exit		是否结束程序, true结束, false不结束
	 * @param mixed $extra		附加数据
	 * 
	 * @return mixed
	 */
	public function output($data, $type = 'json', $exit = true, $extra = '')
	{
		switch ($type)
		{
			case 'debug' :
				echo '<pre>'; print_r($data); echo '</pre>';
				break;
			case 'xml' :
				// echo $this->_toXml();
				break;
			case 'json' :
			default :
				if (isset($_GET['callback']))
				{
					$jsonp = $_GET['callback'];
					echo $jsonp . '(' . json_encode($data) . ')';
				}
				else
				{
					echo json_encode($data);
				}
		}
		
		if ($exit)
		{
			exit();
		}
		return true;
	}
	
	/**
	 * 输出验证输出处理 
	 */
	public function doCode()
	{
		import('util.Securimage');
		$options = array(
			'image_width' => 96,
			'image_height' => 27,
			'text_scale' => 0.6, //字体比例
		);
		$img = new Securimage($options);
		$img->show(); 
	}
	
	/**
	 * 验证验证码是否正确
	 *
	 * @param string $code
	 * @return boolean
	 */
	public function checkCode($code, $namespace = null)
	{
		import('util.Securimage');
		$img = new Securimage();
		$namespace !== null && $img->namespace = $namespace;
		$chk = $img->check($code);
		return $chk;
	}
	
	/**
	 * 记录搜索时间
	 *
	 * @return Float
	 */
	public function timer()
	{
	   list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);
	} 	
}
?>
