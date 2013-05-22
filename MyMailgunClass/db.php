<?php
//数据库连接
class dbMysql
{
	/**
	 * 数据库连接实例
	 * @var Object
	 * @access private
	 */
	public $connect;
	
	
	/**
	 * 建立数据库连接
	 */
	public function connect()
	{
		$available = is_resource($this->connect);
		if ($available)
		{
			if (!($available = mysql_ping($this->connect)))
			{
				mysql_close($this->connect);
			}
		}
	
		if (!$available)
		{
			$base_cfg = array(
				'mysql' => array(
					array('host'=> '127.0.0.1', 'user'=> 'root', 'password'=> '123456')
					)
				);
		}
			
		$cfg['db'] = array('driver'=> 'mysql', 'host'=> $base_cfg['mysql'][0]['host'], 'name'=> 'mailgun', 'user'=> $base_cfg['mysql'][0]['user'], 'password'=> $base_cfg['mysql'][0]['password']);
			
		$this->connect = mysql_connect($cfg['db']['host'], $cfg['db']['user'], $cfg['db']['password']);
		mysql_select_db($cfg['db']['name']);
		mysql_query('SET NAMES utf8');
	}
	

	public function execute($sql)
	{
		$this->connect();
	
		$result = mysql_query($sql, $this->connect);
		($result === false) && $this->error($sql);
	
		return $result;
	}

    /**
	 * 数据库操作执行错误
	 * @param string $sql 错误的SQL语句
	 * @access private
	 */
	public function error($sql)
	{
		trigger_error("SQL执行错误:{$sql}\n错误代码:" . mysql_errno($this->connect) . "\n错误信息: " . mysql_error($this->connect), E_USER_NOTICE);
	}
}
?>
