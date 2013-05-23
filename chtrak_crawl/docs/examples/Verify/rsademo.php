<?php

/**
 * RSA使用样例－页面输出处理示例
 * 
 * 
 */

require_once('../../../common.inc.php');
importModule("Rsa.Rsa");
/* Default Module */
class rsademo extends Action   
{
	/**
	 * 显示登录页(默认Action)
	 */
	function doDefault()
	{
		$this->debug(session_id());
		$page = $this->app->page();
		$page->params['template'] = $base = dirname(__FILE__) . '/rsademo.tpl';

		$result = null;
		//$_POST['en']是客户端用RSA加密后的字符串
		if ($_POST['en'])
		{
			$result = Rsa::decrypt($_POST['en']);
			$result = "The plain text is:".$result;
		}

		list($pwd, $ssid) = explode("\n", $result);
		$this->debug($pwd);
		$this->debug($ssid);

		$page->value("result", $result);
		$page->output();
	}
}

$app->run();
?>
