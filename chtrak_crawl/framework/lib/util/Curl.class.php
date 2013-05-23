<?php
/**
 * 
 * Curl 类 实现HTTP GET & POST请求
 * 
 * 
 * 
 */

class Curl
{	
	private $header = array('Mozilla/5.0 (X11; U; Linux x86_64; zh-CN; rv:1.9.2.17) Gecko/20110422 Ubuntu/10.04 (lucid) Firefox/3.6.17 FirePHP/0.5');
	
	private $cookie = "";
	
	private $ch;
	
	private $timeout = 30;

	
	public function __construct($header = array(), $cookie = "")
	{
		if ( !empty($header) )
		{
			$this->header = array_merge($this->header, $header);
			$this->header = array_unique($this->header);
		}
		
		$this->header = $header;
		$this->cookie = $cookie;
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_HEADER, 0);  
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);	
			
		if (!empty($this->cookie)){  
			curl_setopt($this->ch, CURLOPT_COOKIE, $this->cookies);
		} 
		
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->header);  
		
	}
	
	/**
	 * 
	 * GET
	 * 
	 * @param String $url
	 * @param array $options
	 */
	public function get($url, $options = array())
	{
		
		if ( !empty( $options ) && is_array($options) )
		{
			curl_setopt_array($this->ch , $options);
		}
		
		curl_setopt($this->ch, CURLOPT_URL, $url);  
       	$data = curl_exec($this->ch);
       	
       	if( ($err = curl_error($this->ch)) )
       	{
       		curl_close($this->ch);
       		global $app;
       		$app->log("lib/Curl", array("Curl get error"=> $err, "url" => $url, "time"=> date('Y-m-d H:i:s')));
       		exit;
       	} 
       	
       	curl_close($this->ch);
       	
       	return $data;
       	
	}
	
	/**
	 * 
	 * POST
	 * 
	 * @param String $url
	 * @param String|array $fields   
	 * 			当为x1=dd&x2=dd时 Content-Type是application/x-www-form-urlencoded 如果是数组 
	 * 			则Content-Type是multipart/form-data
	 * 
	 * @param array $options
	 */
	public function post($url, $fields, $options = array())
	{
		if ( !empty( $options ) && is_array($options) )
		{
			curl_setopt_array($ch , $options);
		}
		
		curl_setopt($this->ch, CURLOPT_URL, $url);  
		
		if ( empty( $fields ))
		{
			curl_close($this->ch);
       		global $app;
       		$app->log("lib/Curl", array("Curl post errro:" => "empty fields", "url" => $url, "time"=> date('Y-m-d H:i:s')));
       		exit;
		}
		
		curl_setopt($this->ch, CURLOPT_POST, 1 );  
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
		
		curl_setopt($this->ch, CURLOPT_URL, $url);
		
		$data = curl_exec($this->ch);
       	if( ($err = curl_error($this->ch)) )
       	{
       		curl_close($this->ch);
       		global $app;
       		$app->log("lib/Curl", array("Curl get error"=> $err, "url" => $url, "time"=> date('Y-m-d H:i:s')));
       		exit;
       	} 
       	
       	curl_close($this->ch);
       	
       	return $data;
		
	}
	
	public function query($data, $sep = '&'){  
		$encoded = '';  
		while (list($k,$v) = each($data)) {   
			$encoded .= ($encoded ? "$sep" : "");  
			$encoded .= rawurlencode($k)."=".rawurlencode($v);   
		}   
		return $encoded;    
	} 
	
}