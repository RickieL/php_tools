<?php

/**
 * 验证类（EMAIL）
 *
 * @package lib
 * @subpackage util
 */

class Check 
{

	/**
	 * 检查mail是否有效
	 *
	 * @param string $email
	 * @access public
	 * @return boolean
	 */
	static public function isemail($email) {
		return strlen($email) > 6 && preg_match("/^([\w{1,}])([\w-]*(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/", $email);
	}

	/**
	 * 检查QQ是否符合标准
	 *
	 * @param int $qq
	 * @return boolean
	 */
	static public function isqq($qq)
	{
		return ereg("[1-9][0-9]{4,}",$qq);
	}
	
	
	/**
	 * 检查是否符合手机标准
	 *
	 * @param int $mobile
	 * @return boolean
	 */
	static static public function ismobile($mobile)
	{
		return ereg("[1-9][0-9]{10,}",$mobile);
	}
	
	
	/**
	 * 验证是否为数字
	 *
	 * @param int $num
	 * @return boolean
	 */
	static public function isnum($num)
	{
		return is_numeric($num);
	}
	
	
	/**
	 * 验证是否合格为DOMAIN
	 *
	 * @param string $domain
	 * @return boolean
	 */
	static public function isdomain($domain)
	{
		return !is_numeric ($domain) && ereg("^[a-zA-Z0-9_]{4,20}$",$domain);
	}
	
	/**
	 * 验证是否是保留域名
	 *
	 * @param String $domain
	 * @return boolean
	 */
	static public function isReserveDomain($domain)
	{
		$domain = strtolower($domain);
		
		$arr = file( dirname(dirname(dirname(__FILE__))) ."/config/domainreserve.cfg.php");
		unset($arr[0]);

		$flag = 0;
		foreach ($arr as $keywords)
		{
			$keywords = rtrim($keywords);
			if ($keywords == '#INCLUDE')
				$flag = 1;
			else if ($keywords == '#EQUAL')
				$flag = 2;
			else
			{
				if ($flag == 1)
				{
					if ( stripos($domain, $keywords) !== false )
						return true;
				}
				else if ($flag == 2)
				{
					if (strtolower($keywords) == $domain)
						return true;					
				}
			}
		}
		
		return false;
	}
}

?>