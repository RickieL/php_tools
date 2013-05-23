<?php

/**
 * 社区平台-首页
 * 
 * 
 */

require_once('../../common.inc.php');

class au extends Action   
{
	/**
	 * 显示首页
	 */
	function doDefault() 
	{	
        $user_id = 65531;
        
        /* {{{ 分表操作演示，根据用户表中的UserId来进行数据切分 */
        //获取分表后缀
        $tbl_prefix = table($user_id);
        
        //重新创建用户数据库的数据连接，所有的数据库等配置都放在config目录中
        $db = $this->app->orm($this->app->cfg['db_user_r'])->query();  
       
        //获取用户昵称 
        var_dump($db->getRow('select NickName from User_' . $tbl_prefix));
        /* }}} */
        
        /* {{{ Memcached分组及单台操作演示 */
        //单台Memcache操作处理
        $cache = $this->app->cache('memcached', '172.18.61.212', 10001);
        var_dump($cache->set('sa', 'china', 100, 0));
        
        //分组Memcached操作处理，$this->app->cfg['memcache']的定义见database.cfg.php
        $cache = $this->app->cache('memcached', $this->app->cfg['memcache']);
        var_dump($cache->set('a', 'china', 100, 0));
        var_dump($cache->set('b', 'china', 100, 0));
        /* }}} */
        
        $_SESSION['name'] = 'roast';
        
        $page = $this->app->page();
        $page->output();
	}
}

$app->run();

?>