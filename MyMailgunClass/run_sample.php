<?php
define(PATH_ROOT, "/mnt/www/GitHub/MyMailgun");
require("mailgun.class.php");

// 所用用于发送邮件的邮箱
$MailBoxes = array('foodweekly1@newsletter.yongfu.com',
					'foodweekly2@newsletter.yongfu.com',
					'foodweekly3@newsletter.yongfu.com',
					'foodweekly4@newsletter.yongfu.com'
				);

// 所有的邮件接收列表


// 确认日期
$dayofweek = date('N');
if ($dayofweek == 5)
{
	$date = date('Ymd');
}
else 
{
	$date = date('Ymd', strtotime("last friday"));
}

//检查邮件正文是否存在
if (!file_exists(PATH_ROOT . "/mail_body/weekly_recipe_{$date}.htm"))
{
	echo "File not exist, please check it.\r\n";
	exit();
}
 
$mailgun = new mailgun();
// 邮件正文
$mailgun->GetMailContent(PATH_ROOT . "/mail_body/weekly_recipe_20130118.htm");
// 指定邮件发送者
// 四个发送者，随机选择一个。
$temp = '0123';
$random_num = mt_rand(0, 3);
$mailgun->m_from_addr = $MailBoxes[$random_num];


// 获取所有已存在的邮件列表
$lists_arr = $mailgun->getLists();
if (!in_array($argv[1]. "@newsletter.yongfu.com", $lists_arr))
{
	echo "Mail list not exist.\n";
	exit();
}

// 指定邮件接收者或邮件接收列表
$mailgun->m_to_addr = "{$argv[1]}@newsletter.yongfu.com";
 
//添加邮件类别
$mailgun->AddIdenty("week{$date}");
//添加标签
$mailgun->AddTag('weekly');
$mailgun->SendMail();

?>
