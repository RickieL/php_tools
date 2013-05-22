<?php
require("mailgun.class.php");
require_once 'webhook.class.php';

// 所有列表数组
$ListFlags = array('qq13', 'qq46', 'qq79', 'qqother', '163ak', '163lz', '163other', 'gmail', 'sina', 'yahoo', 'other', 'test1');

// 所有邮箱的数组
$mailaddress_arr = array();
$Alladdress = array();
// 初始化类
$mailgun = new mailgun();
$webhook = new webhook();
foreach ($ListFlags as $val)
{
    // 邮件接收列表
    $mailgun->m_from_addr = "{$val}@newsletter.yongfu.com";
    $mailaddress_arr[$val] = $mailgun->getListMember($mailgun->m_from_addr);
    $mailaddress_arr[$val] = json_decode($mailaddress_arr[$val]);
    
    // 防止列表未空的情况
    if (count($mailaddress_arr[$val]->items))
    {
    	// 检查每一个邮箱的订阅状态
	    foreach ($mailaddress_arr[$val]->items as $v)
	    {
	    	if (!$v->subscribed)
	    	{
	    		// 对取消订阅的进行处理  如在我们数据库进行更新，或记录下来
	    		$webhook->log('unsubscribe_user_log', $mailaddress_arr[$val]->items);
	    	}
	    	// 所有在列表中的邮箱
	    	$Alladdress[] = $v->address;
	    }
    }
}

$file_arr = array('mail_addr_d1', 'mail_addr_d2', 'mail_addr_d3'); 
foreach ($file_arr as $filev)
{
	$emails_f = array();
	// 需要添加的邮箱数组
	$email_file_path = "/mnt/www/GitHub/MyMailgun/mail_addr/{$filev}.txt";
	$csv_file_path = "/mnt/www/GitHub/MyMailgun/mail_addr/{$filev}.csv";
	$emails_f = file($email_file_path);
	
	// 判断邮箱是否已在列表内,对于不在的写入邮件列表，或写入csv文件中
	foreach ($emails_f as $emails_arr)
	{
		list($uid, $mail_a, $nickname) = explode("\t", $emails_arr);
		$mail_a = strtolower($mail_a);
		$nickname = trim($nickname);
		// 判断是否在列表内
		if (!in_array($mail_a, $Alladdress))
		{
			$Alladdress[] = $mail_a;
			file_put_contents($csv_file_path, "{$nickname} <{$mail_a}>\n", FILE_APPEND);
		}
	}
}