<?php
require("mailgun.class.php");
require_once 'webhook.class.php';

// 所有列表数组
$ListFlags = array('qq13', 'qq46', 'qq79', 'qqother', '163ak', '163lz', '163other', 'gmail', 'sina', 'yahoo', 'other', 'test1', 'test2');

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

$dir='/mnt/www/GitHub/MyMailgunclass/';
// 需要添加的邮箱数组
$email_file_path = $dir . 'mail_addr/test_mail_addr.txt';
$csv_file_path = $dir . 'mail_addr/csv_mail_addr.csv';
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
		// 是否是qq邮箱
		if (preg_match('/@(vip\.)?qq\.com/', $mail_a))
		{
			if (preg_match('/[1-3]/', $mail_a[0]))
			{
				file_put_contents($csv_file_path, "$nickname <$mail_a> qq13\n", FILE_APPEND);
			}
			elseif (preg_match('/[4-6]/', $mail_a[0])) 
			{
				file_put_contents($csv_file_path, "$nickname <$mail_a> qq46\n", FILE_APPEND);
			}
			elseif (preg_match('/[7-9]/', $mail_a[0]))
			{
				file_put_contents($csv_file_path,  "$nickname <$mail_a> qq79\n", FILE_APPEND);
			}
			else 
			{
				file_put_contents($csv_file_path, "$nickname <$mail_a> qqother\n", FILE_APPEND);
			}
		}
		// 是否是163邮箱
		elseif (preg_match('/@163.com/', $mail_a))
		{
			if (preg_match('/[a-k]/', $mail_a[0]))
			{
				file_put_contents($csv_file_path, "$nickname <$mail_a> 163ak\n", FILE_APPEND);
			}
			elseif (preg_match('/[l-z]/', $mail_a[0]))
			{
				file_put_contents($csv_file_path, "$nickname <$mail_a> 163lz\n", FILE_APPEND);
			}
			else
			{
				file_put_contents($csv_file_path, "$nickname <$mail_a> 163other\n", FILE_APPEND);
			}
		}
		// 是否126邮箱
		elseif (preg_match('/@126.com/', $mail_a))
		{
			file_put_contents($csv_file_path, "$nickname <$mail_a> 126\n", FILE_APPEND);
		}
		// 是否gmail邮箱
		elseif (preg_match('/@gmail.com/', $mail_a))
		{
			file_put_contents($csv_file_path, "$nickname <$mail_a> gmail\n", FILE_APPEND);
		}
		// 是否sina邮箱
		elseif (preg_match('/@sina.(com|cn|com.cn)/', $mail_a))
		{
			file_put_contents($csv_file_path, "$nickname <$mail_a> sina\n", FILE_APPEND);
		}
		// 是否yahoo邮箱
		elseif (preg_match('/@yahoo.com/', $mail_a))
		{
			file_put_contents($csv_file_path, "$nickname <$mail_a> yahoo\n", FILE_APPEND);
		}
		// other邮箱
		else
		{
			file_put_contents($csv_file_path, "$nickname <$mail_a> other\n", FILE_APPEND);
		}
	}
}
