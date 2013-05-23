<?php

/**
 * 框架使用样例－数据库操作举例
 * 
 * 
 */

require_once('../../../common.inc.php');

/* Default Module */
class index extends Action   
{
	/**
	 * 显示登录页(默认Action)
	 */
	function doDefault() 
	{	
		/* {{{ 初始化DB操作类,默认使用配置文件中配置引擎 */
		$query = $this->app->orm($this->app->cfg['db_user_r'])->query();
		/* }}} */
		
		
		/* {{{ 通过SQL查询返回记录的数组 */
		echo '<p>$query->getArray(\'select * from User\', 0, 3):<br />';
		print_r($query->getArray('select * from User', 0, 3));
		echo '</p>';
		/* }}} */
		
		
		/* {{{ 通过SQL查询单条记录 */
		echo '<p>$query->getRow(\' select * from User\'):<br />';
		print_r($query->getRow(' select * from User'));
		echo '</p>';
		/* }}}*/
		
		
		/* {{{ 通过SQL查询单条记录的某个值 */
		echo '<p>$query->getValue(\' select * from User\'):<br />';
		print_r($query->getValue(' select * from User'));
		echo '</p>';
		/* }}}*/		

		
		/* {{{ OO方式操作数据库 */
		echo '<p>$query->addTable(\'User\')->addWhere(\'Pass9\',\'root\')->getRow()<br />';
		print_r($query->addTable('User')->addWhere('Pass9','roast')->getRow());
		echo '</p>';
		/* }}} */
		
		
		/* {{{ 将数据库操作的历史记录删除，参数可以为table/value等，具体可以*/
		$query->clear();
		/* }}} */

			
		/* {{{ OO方式操作数据库（添加条件） */
		echo '<p>SQL：select host from user where Pass9=\'root\' group by host order by User<br />';
		$query->addTable('User');
		$query->addField('Pass9');
		$query->addWhere('Pass9', 'roast');	//宏定义参见：OrmMysqlParser.class.php
		$query->addGroupBy('Pass9');
		print_r($query->getArray(null, 0, 10));
		echo '</p>';
		/* }}} */	
		
		
		/* {{{ 获取单列的数据 */
		echo '<p> select Pass9 from User <br />';
		$query->clear();
		print_r($query->getColumn('select Pass9 from User limit 2'));
		echo '</p>';
		/* }}} */
		
	   //更换数据库—配置一般于配置文件
	   $db['params'] = array
		                (
							'driver'		=> 'mysql',
							'host'			=> 'localhost',
							'name'			=> 'test',
							'user'			=> 'root',
							'password'		=> '',
						);
						
		$db['options'] = array
		                (
							'persistent'				=> false,
							'tablePrefix'				=> '',
							'charset'					=> 'utf8',
						);

		$query = $this->app->orm($db['params'], $db['options'])->query();
		
		/* {{{ 插入数据 */
		echo '<p>test.test count:' . $query->getValue('select count(*) from test') . '<br />';
		$query->clear();
		$query->addTable('test');
		$query->addValue('name', 'roast');
		$query->insert();
		echo 'test.test count:' . $query->getValue('select count(*) from test') . '</p>';
		echo '<p>$query->getLastId():<br />';
		print_r($query->getLastId());
		echo '</p>';		
		/* }}} */

		
		/* {{{ 插入数据（另外一种方式) */
		echo '<p>test.test count:' . $query->getValue('select count(*) from test') . '<br />';
		$query->clear('value');
		$query->insert(array('id'=>'1', 'name'=>'roast'));
		echo 'test.test count:' . $query->getValue('select count(*) from test') . '</p>';
		/* }}} */

		
		/* {{{ 修改数据 */
		echo '<p>id:' . $query->getValue('select name from test where id="1"') . '<br />';
		$query->clear();
		$query->addTable('test');
		$query->addValue('name', 'libing');
		$query->addWhere('id', '1');
		$query->update();
		echo 'id:' . $query->getValue('select name from test where id="1"') . '</p>';
		echo '<p>$query->getAffectRows():<br />';
		print_r($query->getAffectRows());
		echo '</p>';		
		/* }}} */
		
		
		/* {{{ 修改数据（另外一种方式) */
		echo '<p>id:' . $query->getValue('select name from test where id="1"') . '<br />';
		$query->clear('value');
		$query->update(array('name'=>'roast'));
		echo 'id:' . $query->getValue('select name from test where id="1"') . '</p>';
		/* }}} */	
			
		
		/* {{{ 删除记录 */
		echo '<p>test.test count:' . $query->getValue('select count(*) from test') . '<br />';
		$query->clear();
		$query->addTable('test');
		$query->addWhere('id', 1);
		$query->delete();
		echo 'test.test count:' . $query->getValue('select count(*) from test') . '</p>';
		/* }}} */
		
		/* {{{ 获取当前的SQL语句 */
		$query->clear();
		$query->addTable('test');
		$query->addValue('id', '1');
		$query->addValue('name', 'libing');
		$query->addWhere('name', 'roastxshare');
		echo '<p>SQL(select):' . $query->asSql('update') . '<br />';
		echo 'SQL(update):' . $query->asSql('update') . '<br />';
		echo 'SQL(insert):' . $query->asSql('insert') . '<br />';
		echo 'SQL(delete):' . $query->asSql('delete') . '<br />';
		echo '</p>';
		/* }}} */
		
		/* {{{ 执行SQL语句 */
		$query->exec('SET NAMES utf8');
		/* }}} */	
	}
	
	/**
	 * 显示登录页(默认Action)
	 */
	function doMssql() 
	{	
		/* {{{ 初始化DB操作类,默认使用配置文件中配置引擎 */
		$cfg['db_w'] = array('driver'=> 'mssql', 'host'=> '127.0.0.1,1433', 'name'=> 'tempdb', 'user'=> 'test','password'=> '123456');
		$query = $this->app->orm($cfg['db_w'])->query();
		/* }}} */
		
		
		/* {{{ 通过SQL查询返回记录的数组 */
		echo '<p>$query->getArray(\'select * from [User]\', 0, 3):<br />';
		print_r($query->getArray('select * from [User]'));
		echo '</p>';
		/* }}} */
		
		
		/* {{{ 通过SQL查询单条记录 */
		echo '<p>$query->getRow(\' select * from [User]\'):<br />';
		print_r($query->getRow('select * from [User]'));
		echo '</p>';
		/* }}}*/
		
		
		/* {{{ 通过SQL查询单条记录的某个值 */
		echo '<p>$query->getValue(\' select * from [User]\'):<br />';
		print_r($query->getValue(' select * from [User]'));
		echo '</p>';
		/* }}}*/		

		
		/* {{{ OO方式操作数据库 */
		echo '<p>$query->addTable(\'[User]\')->addWhere(\'Pass9\',\'root\')->getRow()<br />';
		print_r($query->addTable('[User]')->addWhere('Pass9','roast')->getRow());
		echo '</p>';
		/* }}} */
		
		
		/* {{{ 将数据库操作的历史记录删除，参数可以为table/value等，具体可以*/
		$query->clear();
		/* }}} */

			
		/* {{{ OO方式操作数据库（添加条件） */
		echo '<p>SQL：select Pass9 from [user] where Pass9=\'root\' group by host<br />';
		$query->addTable('[User]');
		$query->addField('Pass9');
		$query->addWhere('Pass9', 'roast');	//宏定义参见：OrmMysqlParser.class.php
		$query->addGroupBy('Pass9');
		print_r($query->getArray(null, 0, 10));
		echo '</p>';
		/* }}} */	
		
		
		/* {{{ 获取单列的数据 */
		echo '<p> select Pass9 from [User] <br />';
		$query->clear();
		print_r($query->getColumn('select Pass9 from [User]'));
		echo '</p>';
		/* }}} */
		
		
		/* {{{ 带有操作符的SQL */
		echo '<p> select Pass9 from [User] where Qq > 349985877 <br />';
		$query->clear();
		$query->addTable('[User]');
		$query->addField('Pass9');
		$query->addWhere('Qq', '349985877', _ORM_OP_LT);	//宏定义参见：OrmMysqlParser.class.php
		$query->addGroupBy('Pass9');
		print_r($query->getArray());
		echo '</p>';
		/* }}} */
		
		
		/* {{{ 带有操作符的SQL */
		echo '<p> select Pass9 from [User] where Qq < 349985877 <br />';
		$query->clear();
		$query->addTable('[User]');
		$query->addField('Pass9');
		$query->addWhere('Qq', '349985877', _ORM_OP_GT);	//宏定义参见：OrmMysqlParser.class.php
		$query->addGroupBy('Pass9');
		print_r($query->getArray());
		echo '</p>';
		/* }}} */	
			
		/* {{{ 带有操作符的SQL */
		echo '<p> select max(UserId) from [User] <br />';
		$query->clear();
		$query->addTable('[User]');
		$query->addField('UserId', 'MaxUserId', _ORM_OP_MAX);
		$MaxUserId = $query->getValue();
		print_r($MaxUserId);
		echo '</p>';
		/* }}} */
		
		/* {{{ 带有操作符的SQL */
		echo '<p> insert into [User] values(' . ($MaxUserId + 1) . ',"china", "shit", 9632568)<br />';
		print_r($query->exec('insert into [User] values(' . ($MaxUserId + 1) . ',"china", "shit", 9632568)'));
		echo '</p>';
		/* }}} */	
		
		/* {{{ OO方式的插入 */
		echo '<p> insert into [User] values(' . ($MaxUserId + 2) . ',"china", "shit", 9632568) <br />';
		$query->clear();
		$query->addTable('[User]');
		$query->addValue('UserId', ($MaxUserId + 2));
		$query->addValue('Pass9', 'china');
		$query->addValue('NickName', 'shit');
		$query->addValue('Qq', '96325683');
		print_r($query->insert());
		echo '</p>';
		/* }}} */
		
		/*{{{ OO方式的删除数据 */
		echo '<p> delete  from [User] values(' . ($MaxUserId + 2) . ',"china", "shit", 9632568) <br />';
		$query->clear();
		$query->addTable('[User]');
		$query->addWhere('UserId', ($MaxUserId + 2));
		print_r($query->delete());
		echo '</p>';
		/* }}} */				
	}	
}

$app->run();
?>