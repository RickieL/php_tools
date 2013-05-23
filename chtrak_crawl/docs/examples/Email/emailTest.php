<?php

require_once('common.inc.php');
importModule("Email.Email");
importModule("User.UserInfo");

class emailTest extends Action
{
	public function doDefault()
	{
		
		$action = is_numeric($_GET["action"]) ? $_GET['action'] : 0;
		$mail = $_GET["mail"] ? $_GET['mail'] : null;
		
		if (!$mail)
		{
			exit( "请输入要发送的人的Email");
		}
		
		//发送人ID
		$msg['uid'] = 100900;
		
		//发送对象Email
		//$maillists = array("saverobort@yahoo.com.cn", "349985877@qq.com", "saverobort@sina.com");
		
		//创建Email实例
		$email = new Email($msg['uid']);
		
		switch ($action)
		{
			/*********邮箱激活*********/
			case 0:
				
				$email = new Email($msg['uid']);
				$email->setContent(0)->sendEmail($mail);	
				break;
			/*********好友邀请*********/
			case 1:
				$msg['nick'] = "听雨";
				$msg['link'] = "http://play9.the9.com/reg/?code=2c491f835a4c19871afd46063dba2e4c_100955_0_%E5%90%AC%E9%9B%A8";
				$msg['pic'] = head($msg['uid'] . '_2');
				$msg['site_url'] = $this->app->cfg['url']['root'];
				$msg['site_name'] = $this->app->cfg['site']['name'];
				
				$email->setContent(1, $msg)->sendEmail($mail);
				break;
			/*********请求加好友*********/
			case 2:
				//模板未定
				//$email->setContent(2)->sendEmail($maillists);
				echo "还没模板，不能发送。";
				break;
		}
		echo "发送成功！";
	}
}
$app->run();
?>