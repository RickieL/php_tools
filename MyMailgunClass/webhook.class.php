<?php

define(PATH_ROOT,   dirname(__FILE__));
require_once PATH_ROOT . '/db.php';

class webhook
{
	public $key = 'key-3tvceod2xyb67bosd25h1p2f2yvr38b1';
	public $post_data = array();
	public $track_type = '';
	
	public function log($filename, $data)
	{
		$date = date('Ymd');
		$datetime = date('Y-m-d H:i:s');
		$err_info = serialize($data);
		$err_log = $datetime . "\t" . $err_info;
		$file_path = PATH_ROOT . "/logs/" . $filename . "_" . $date . ".txt";
		file_put_contents($file_path, $err_log);
	}
	
	public function CheckSigniture()
	{
        // 防止别人拿到timestamp和token进行验证。
		//增加一层对时间的验证。
		$time = time();
		if (($this->post_data['timestamp'] > ($time - 86400)) && ($this->post_data['timestamp'] < ($time + 16400)))
		{
			if ($this->post_data['signature'] == hash_hmac("sha256", $this->post_data['timestamp'].$this->post_data['token'], $this->key))
			{
				return  true;
			}
		}
        return false;
	}
	
	
	public function CommitEvent()
	{
		if (!$this->CheckSigniture())
		{
			$this->log("signiture_err", $this->post_data);
			exit;
		}
		
		switch ($this->post_data['event'])
		{
			case "opened":
				$this->MailOpened();
				$this->log("opened", $this->post_data);
				break;
			case "clicked":
				$this->MailClicked();
				$this->log("clicked", $this->post_data);
				break;
			case "unsubscribed":
				$this->MailUnsubscribed();
				$this->log("unsubscribed", $this->post_data);
				break;
			case "complained":
				$this->MailComplained();
				$this->log("complained", $this->post_data);
				break;
			case "bounced":
				$this->MailBounced();
				$this->log("bounced", $this->post_data);
				break;
			case "dropped":
				$this->MailDropped();
				$this->log("dropped", $this->post_data);
				break;
			case "delivered":
				$this->MailDelivered();
				$this->log("delivered", $this->post_data);
				break;
			default:
				$this->log("eventErr", $this->post_data);
				break;
		}
	}
	
	/**
	 * 已发送邮件的统计信息入库
	 */ 
	private  function MailDelivered()
	{
		// 获得邮件标识
		$identy = json_decode($this->post_data['identy']);
		$identy->identy;
		$sql = "INSERT INTO `Delivered` (`Recipient`, `Tag`, `Identy`, `Domain`, `TimeStamp`) VALUES (\"{$this->post_data['recipient']}\", \"{$this->post_data['tag']}\", \"{$identy->identy}\", \"{$this->post_data['domain']}\", {$this->post_data['timestamp']})";
		$db = new dbMysql();
		$result = $db->execute($sql);
	}
	
	/**
	 * 被drop的邮件（未发送出去的邮件）统计信息入库
	 */ 
	private function MailDropped()
	{
		// 获得邮件标识
		$identy = json_decode($this->post_data['identy']);
		$identy->identy;
		$sql = "INSERT INTO `Dropped` (`Recipient`, `Tag`, `Identy`, `Domain`, `Reason`, `Code`, `TimeStamp`) VALUES (\"{$this->post_data['recipient']}\", \"{$this->post_data['tag']}\", \"{$identy->identy}\", \"{$this->post_data['domain']}\", \"{$this->post_data['reason']}\", \"{$this->post_data['code']}\", {$this->post_data['timestamp']})";
		$db = new dbMysql();
		$result = $db->execute($sql);
	}
	
	/**
	 * 邮件打开情况统计信息入库
	 */ 
	private function MailOpened()
	{
		// 获得邮件标识
		$identy = json_decode($this->post_data['identy']);
		$identy->identy;
		$sql = "INSERT INTO `Opened` (`Recipient`, `Tag`, `Identy`, `Domain`, `Maillist`, `IP`, `TimeStamp`) VALUES (\"{$this->post_data['recipient']}\", \"{$this->post_data['tag']}\", \"{$identy->identy}\", \"{$this->post_data['domain']}\", \"{$this->post_data['mailing-list']}\", \"{$this->post_data['ip']}\", {$this->post_data['timestamp']})";
		$db = new dbMysql();
		$result = $db->execute($sql);
	}
	
	/**
	 * 邮件内链接点击情况统计信息入库
	 */ 
	private function MailClicked()
	{
		// 获得邮件标识
		$identy = json_decode($this->post_data['identy']);
		$identy->identy;
		$sql = "INSERT INTO `Clicked` (`Recipient`, `Tag`, `Identy`, `Domain`, `Maillist`, `IP`, `Url`, `TimeStamp`) VALUES (\"{$this->post_data['recipient']}\", \"{$this->post_data['tag']}\", \"{$identy->identy}\", \"{$this->post_data['domain']}\", \"{$this->post_data['mailing-list']}\", \"{$this->post_data['ip']}\", \"{$this->post_data['url']}\", {$this->post_data['timestamp']})";
		$db = new dbMysql();
		$result = $db->execute($sql);
	}
	
	/**
	 * 退订邮件的统计信息入库
	 */ 
	private function MailUnsubscribed()
	{
		// 获得邮件标识
		$identy = json_decode($this->post_data['identy']);
		$identy->identy;
		$sql = "INSERT INTO `Unsubscribed` (`Recipient`, `Tag`, `Identy`, `Domain`, `IP`, `TimeStamp`) VALUES (\"{$this->post_data['recipient']}\", \"{$this->post_data['tag']}\", \"{$identy->identy}\", \"{$this->post_data['domain']}\", \"{$this->post_data['ip']}\", {$this->post_data['timestamp']})";
		$db = new dbMysql();
		$result = $db->execute($sql);
	}
	
	/**
	 * 被举报垃圾邮件的情况
	 */ 
	private function MailComplained()
	{
		// 获得邮件标识
		$identy = json_decode($this->post_data['identy']);
		$identy->identy;
		$sql = "INSERT INTO `Complained` (`Recipient`, `Tag`, `Identy`, `Domain`, `Maillist`, `TimeStamp`) VALUES (\"{$this->post_data['recipient']}\", \"{$this->post_data['tag']}\", \"{$identy->identy}\", \"{$this->post_data['domain']}\", \"{$this->post_data['mailing-list']}\", {$this->post_data['timestamp']})";
		$db = new dbMysql();
		$result = $db->execute($sql);
	}
	
	/**
	 * 被直接退回的邮件的情况入库
	 */ 
	private function MailBounced()
	{
		// 获得邮件标识
		$identy = json_decode($this->post_data['identy']);
		$identy->identy;
		$sql = "INSERT INTO `Bounced` (`Recipient`, `Tag`, `Identy`, `Domain`, `Maillist`, `TimeStamp`, `Code`, `ErrorInfo`, `Notification`) VALUES (\"{$this->post_data['recipient']}\", \"{$this->post_data['tag']}\", \"{$identy->identy}\", \"{$this->post_data['domain']}\", \"{$this->post_data['mailing-list']}\", {$this->post_data['timestamp']}, \"{$this->post_data['code']}\", \"{$this->post_data['error']}\", \"{$this->post_data['notification']}\")";
		$db = new dbMysql();
		$result = $db->execute($sql);
	}
}
