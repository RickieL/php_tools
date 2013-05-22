<?php

echo date('r', strtotime('20130125 20:00:00'));

exit;
define(PATH_ROOT, "/mnt/www/GitHub/MyMailgun");
require("mailgun.class.php");
$mailgun = new mailgun();
// 邮件正文
$result = $mailgun->getLists();
$list = json_decode($result);
$list_arr = array();
foreach ($list->items as $key=>$val)
{
	$list_arr[] = $val->address;
}

var_dump($list_arr);