<?php

/**
 *  短信发送处理类
 *
 * @package lib
 * @subpackage util
 */

/*立即发送消息，而不使用队列*/
define('SMS_SEND_NOW', 1);

/* 通过队列发送消息，而不是实时发送 */
define('SMS_SEND_QUEUE', 2);

import("util.Curl");

class Sms 
{  
	
	private static $per_sms_length = 490;
	
	private static $sn = "SDK-HBY-010-00003";
	
	private static $pwd = "310348";
	
	private static $send_url = "http://sdk2.entinfo.cn/webservice.asmx/SendSMS";
	
	private static $rec_sms_api = "http://sdk2.entinfo.cn/webservice.asmx/RECSMS";
	
	private static $all_info_api = "http://sdk2.entinfo.cn/webservice.asmx/GetAllInfo";
	
	
	
	/**
	 * 短信发送接口
	 * 所有发送失败的短信会使用crontab重发
	 * 
	 *
	 * @param String	$mobile		接收短信的手机号码,多个号码用半角逗号[,]隔开
	 * @param String	$message	消息内容
	 * @param Integer 	$type		短信发送途径 1:发送菜谱内容到手机2:注册验证码3:好拍优惠卷, 4:发送店铺信息, 5:找回密码验证码，6：绑定好豆验证码,，7：红楼优惠码, 100：监控短信
	 * @param Integer   $user_id	多手机号发送时，此参数传递无效
	 * @param Integer	$flag		发送内容标志:SMS_SEND_NOW为立即发送,SMS_SEND_QUEUE为通过队列异步发送
	 * @return Boolean
	 */
	public static function send($mobile, $message, $type = 0, $flag = SMS_SEND_QUEUE, $user_id = 0)
	{
		if ( empty( $mobile ) || empty($message))
			return false;
		
		$message .= "[好豆网]";
		
		//$message = preg_replace("/\s/", $replacement, $subject)
		
		$header = array(
			"Referer" => "/mobile/sendpost.php"
		);
		
		if ( strpos($mobile, ",") )
		{
			$arr_mobile = explode(",", $mobile);
		}
		else 
		{
			$arr_mobile = array( $mobile );			
		}
		
		$arr_mobile = array_unique($arr_mobile);
		
		importModule("Sms.SmsLog");
		$obj_sms = new SmsLog();
		
		$time = time();
		$data = array();
		
		if ( $flag ==  SMS_SEND_QUEUE) //写入到队列
		{
			foreach ( $arr_mobile as $v )
			{
				if ( !empty( $v ) )
				{
					$data[] = array(
						'Mobile'	=> $v,
						'Content'	=> $message,
						'SendTime'	=> $time,
						'SendResult'=> 2,
						'RetryTimes'=> 0,
						'UserId'	=> $user_id,
						'Type'		=> $type			
					);
				}
			}
			
			$result = $obj_sms->addSmsData('SendSms', $data);
			return $result;
		}
		else //立刻发送
		{
			$arr_content = array();
			$words = mb_strlen( $message, "UTF-8" );
			
			if ( $words > self::$per_sms_length ) //内容太长，拆分发送
			{
				for( $i = 0; $i < $words; $i++ )
				{
					
					if ( $i % self::$per_sms_length == 0 )
					{
						$arr_content[] = mb_substr($message, $i, self::$per_sms_length, "UTF-8");					
					}
				}
				
				$flag = false;	
				$count = count($arr_content);			
				foreach ( $arr_content as $index=> $val )
				{
					if ( $index != $count - 1 )
					{
						$val = $val . "[好豆网]";
					}
					
					$obj_curl = new Curl($header);
					$field = array(
						'sn'		=> self::$sn, 
					 	'pwd'		=> self::$pwd, 
					 	'mobile'	=> $mobile,
						'content'	=> iconv('UTF-8', 'GB2312', $val)
					);
					
					$field = $obj_curl->query($field);
					$result = $obj_curl->post(self::$send_url, $field);
					
					if ( strpos( $result, '<string xmlns="http://tempuri.org/">0 成功</string>' ) !== false )
					{
						foreach ( $arr_mobile as $v )
						{
							if ( !empty( $v ) )
							{
								$data[] = array(
									'Mobile'	=> $v,
									'Content'	=> $val,
									'SendTime'	=> $time,
									'SendResult'=> 1,
									'RetryTimes'=> 0,		
									'UserId'	=> $user_id,
									'Type'		=> $type		
								);
							}
						}
							
						$obj_sms->addSmsData('SendSms', $data);
					}
					else 
					{
						foreach ( $arr_mobile as $v )
						{
							if ( !empty( $v ) )
							{
								$data[] = array(
									'Mobile'	=> $v,
									'Content'	=> $val,
									'SendTime'	=> $time,
									'SendResult'=> 2,
									'RetryTimes'=> 0,	
									'UserId'	=> $user_id,
									'Type'		=> $type			
								);
							}
						}
						
						$obj_sms->addSmsData('SendSms', $data);
						
						$flag = true;
						
					}
				}
				
				return !$flag;
			}
			else //单挑发送 
			{
				$obj_curl = new Curl($header);
				
				$field = array(
					'sn'		=> self::$sn, 
				 	'pwd'		=> self::$pwd, 
				 	'mobile'	=> $mobile,
					'content'	=> iconv('UTF-8', 'GB2312', $message)
				);
				
				$field = $obj_curl->query($field);
				$result = $obj_curl->post(self::$send_url, $field);
				
				if ( strpos( $result, '<string xmlns="http://tempuri.org/">0 成功</string>' ) !== false ) //发送成功
				{
					foreach ( $arr_mobile as $v )
					{
						if ( !empty( $v ) )
						{
							$data[] = array(
								'Mobile'	=> $v,
								'Content'	=> $message,
								'SendTime'	=> $time,
								'SendResult'=> 1,
								'RetryTimes'=> 0,
								'UserId'	=> $user_id,
								'Type'		=> $type				
							);
						}
					}
					
					$result = $obj_sms->addSmsData('SendSms', $data);
					return true;
				} 
				else //发送失败
				{
					foreach ( $arr_mobile as $v )
					{
						if ( !empty( $v ) )
						{
							$data[] = array(
								'Mobile'	=> $v,
								'Content'	=> $message,
								'SendTime'	=> $time,
								'SendResult'=> 2,
								'RetryTimes'=> 0,
								'UserId'	=> $user_id,
								'Type'		=> $type			
							);
						}
					}
					
					$result = $obj_sms->addSmsData('SendSms', $data);
					
					return false;
				}
			}
		}

	}
	
	/**
	 * 
	 * 获取指定手机号码上行的短信内容
	 * @param String|array $mobile  手机号码 
	 * @return String 返回一个XML字符串 
	 */
	public static function getRecSms()
	{		
		$obj_curl = new Curl();
		
		$field = array(
			'sn'		=>self::$sn, 
		 	'pwd'		=>self::$pwd
		);
		
		$result = $obj_curl->get(self::$rec_sms_api . "?sn=" . self::$sn . "&pwd=" . self::$pwd);
		return $result;
	}
	
	/**
	 * 
	 * 获取基本信息
	 * 
	 * @return fixed boolean|array  成功返回数组，失败返回false
	 * 
	 */
	public static function getInfo ()
	{
		$obj_curl = new Curl();
		$field = array(
			'sn'		=>self::$sn, 
		 	'pwd'		=>self::$pwd
		);
		
		$result = $obj_curl->get(self::$all_info_api . "?sn=" . self::$sn . "&pwd=" . self::$pwd);
		
		$xml = simplexml_load_string($result);
		
		if ( $xml instanceof SimpleXMLElement )
		{
			$info = array();
			
			foreach ( $xml->children() as $child )
			{
				$info[$child->getName()] = strval($child);
			}
			
			return $info;
		}
		else 
		{
			return false;
		}
	}	
}
?>