<?php

/**
 * 框架首页
 *
 *
 */
require_once ('common.inc.php');

define("NO_CHECK_LOGIN", true);


class index extends Action
{
    /**
     * 用户列表
     */
	public function doDefault()
	{
		global $app;
		$page = $app->page();
		$page->output();
	}
	
	/**
	 * 用户登录
	 */
	public function doLogin()
	{
		global $app;
		$this->db = $app->orm()->query();
		
		$username = trim($_POST['username']);
		$pass = trim($_POST['pass']);
		$pass_md5 = md5($pass);
		
		$clientip = $_SERVER['REMOTE_ADDR'];
		
		//更新station表
		$this->db->clear();
		$this->db->addTable('User');
		$this->db->addField('UserId');
		$this->db->addWhere('UserName', $username);
		$this->db->addWhere('PassWord', $pass_md5);
		$is_uid = $this->db->getValue();
		if ($is_uid)
		{
			$_SESSION['chtrak_id'] = $is_uid;
			$this->app->redirect($this->app->cfg['url']['root'] . "chadmin/ManageStation.php");
		}
		else 
		{
			$page = $app->page();
			$page->value('lonin_err', '用户名或密码错误！');
			$page->output();
		}
	}
}
$app->run();

?>
