<?php

/**
 * mailgun 发送邮件的类
 * @author yongfu
 *
 */

class mailgun
{
	//基本变量的设定
	// api key
	public $key = 'key-3tvceod2xyb67bosd25h1p2f2yvr38b1';
	// api_url
	public $api_url = 'https://api.mailgun.net/v2/';
	// mail sender domain
	public  $m_domain = 'newsletter.yongfu.com';
	// from name
	public $m_from_name = "好豆网";
	// from mail address
	public $m_from_addr = "weekly@newsletter.yongfu.com";
	// to mail address or list
	public $m_to_addr = "test1@newsletter.yongfu.com";
	// subject
	public $subject = "";
	// mail body
	public $m_html_body = "";
	// tag
	public $tag = array();
	
	// 邮件发送的整个数据
	public $postfields = array(); 
	
	/**
	 * 增加tag
	 * @param string $onetag
	 */
	public function AddTag($onetag)
	{
		if ($onetag)
		{
			$this->tag[]= $onetag;
		}
	}
	
	/**
	 * 增加的自定义var
	 * @param string $identy  邮件的标识
	 */
	public function AddIdenty($identy)
	{
		$identification = array('identy'=>$identy);
		if (is_array($identification))
		{
			$this->postfields["v:identy"] = json_encode($identification);
		}
	}
	
	// 生成post的元素数组
	public function MakePostFields()
	{
		// from的生成
		$this->postfields["from"] = $this->m_from_name . " <" . $this->m_from_addr . ">";
		
		// to 的生成
		$this->postfields["to"] = $this->m_to_addr;
		
		// subject 标题的生成
		$this->postfields["subject"] = $this->subject;
		
		// html 内容的生成
		$this->postfields["html"] = $this->m_html_body;
		
		// tag的生成
		if (count($this->tag))
		{
			foreach ($this->tag as $key=>$val)
			{
				$count = $key + 1;
				$this->postfields["o:tag[$count]"] = $val;
			}
		}
	}
	
	// 获取邮件内容
	public function GetMailContent($mailfile)
	{
		if(file_exists($mailfile))
		{
			$content = file_get_contents($mailfile);
			preg_match('@<title>(.*)</title>@Ui', $content,$_m);
			$this->subject = $_m[1];
			$this->m_html_body = $content;
		}
		else
		{
			exit("the mail msg file is not exist.");
		}
	}
	
	//发送http api邮件
	public function SendMail() 
	{
		$this->MakePostFields();
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $this->key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_URL, $this->api_url . $this->m_domain . '/messages');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postfields);
  	
  		$result = curl_exec($ch);
  		curl_close($ch);
  		return $result;
	}
	
	/**
	 * 创建一个列表
	 * @param array $list
	 * $list =  array('address' => 'dev@samples.mailgun.org',
	 *			      'description' => 'Mailgun developers list')
	 */
	public function CreateList($list) {
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $this->key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt($ch, CURLOPT_URL, $this->api_url . 'lists');
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $list);
	
		$result = curl_exec($ch);
		curl_close($ch);
	
		return $result;
	}
	
	/**
	 * 获取账户下的所有邮件列表
	 */
	public function getLists() {
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $this->key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt($ch, CURLOPT_URL, $this->api_url . "lists");
	
		$result = curl_exec($ch);
		curl_close($ch);
	
		$list = json_decode($result);
		$list_arr = array();
		foreach ($list->items as $key=>$val)
		{
			$list_arr[] = $val->address;
		}
		
		return $list_arr;
	}
	
	/**
	 * 获取一个list的所有邮件
	 * @param string $list   列表名，如dev@mailservices.yongfu.com
	 */
	public function getListMember($list) {
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $this->key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt($ch, CURLOPT_URL, $this->api_url . "lists/{$list}/members");
	
		$result = curl_exec($ch);
		curl_close($ch);
	
		return $result;
	}
	
	/**
	 * 将一个member添加到list
	 * @param string $list   列表名，如dev@mailservices.yongfu.com
	 * @param array $addrinfo  邮件地址的具体信息
	 * $addrinfo = array('address' => 'bob@gmail.com',
	 *			'name' => 'Bob Bar',
	 *			'description' => 'Developer',
	 *			'subscribed' => true,
	 *			'vars' => '{"age": 26}');
	 */
	public function addListMember($list, $addrinfo) {
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $this->key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt($ch, CURLOPT_URL, $this->api_url . "lists/{$list}/members");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $addrinfo);
	
		$result = curl_exec($ch);
		curl_close($ch);
	
		return $result;
	}
	
	/**
	 * 删除某个列表中的某个邮箱
	 * @param string $list
	 * @param string $useraddress
	 */
	public function delListMember($list, $useraddress) 
	{
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, 'api' . $this->key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_URL, $this->api_url . "lists/{$list}/members" . "/{$useraddress}");
	
		$result = curl_exec($ch);
		curl_close($ch);
	
		return $result;
	}
	
	/**
	 * 删除某个列表
	 * @param string $list
	 */
	public function delList($list) 
	{
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, 'api' . $this->key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_URL, $this->api_url . "lists/{$list}");
	
		$result = curl_exec($ch);
		curl_close($ch);
	
		return $result;
	}
}

?>