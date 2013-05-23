<?php

/**
 *  安全相关
 *
 * @package lib
 * @subpackage util
 * 
 */

class Safe
{
	
	const HaoPaiKey = 'xOgPWdx@#vv0';
	// XorKey的长度应小于或等于HaoPaiKey的长度
	const XorKey = '*(#JLKjl3_92';

	public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) 
	{
		$ckey_length = 4;
		$key = md5($key);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for ($i = 0; $i <= 255; $i++) 
		{
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for ($j = $i = 0; $i < 256; $i++) 
		{
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for ($a = $j = $i = 0; $i < $string_length; $i++) 
		{
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if ($operation == 'DECODE') 
		{
			if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			}
			else
			{
				return '';
			}
		} 
		else 
		{
			return $keyc.str_replace('=', '', base64_encode($result));
		}

	}

	/**
	 * 主体为异或的加/解密算法
	 * 
	 *    self::XorKey的长度应小于或等于 self::HaoPaiKey的长度
	 * 
	 * @param string  $string  需要加/解密的字符串
	 * @param boolean $decode  true表解密，false表加密，默认为加密
	 * 
	 * @return string 经过加/解密后的字符串
	 */
	function paicode($string, $decode = false)
	{
		$coded = '';
		$len = strlen($string);
		$keylen = strlen(self::XorKey);
		$keys = array(self::XorKey, self::HaoPaiKey);
		if ($decode)
		{
			$string = base64_decode($string);
			$keys = array(self::HaoPaiKey, self::XorKey);
		}
		for($i = 0; $i < $len; ++$i)
		{
			$k = $i % $keylen;
			$coded .= $string[$i] ^ $keys[0][$k] ^ $keys[1][$k];
		}
		if (!$decode)
		{
			$coded = base64_encode($coded);
		}
		return $coded;
	}
}