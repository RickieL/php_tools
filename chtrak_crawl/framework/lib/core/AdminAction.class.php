<?php
/**
 * Action类
 * 
 * @package lib
 * @subpackage core
 * 
 */

class AdminAction
{
	
	/**
	 * 应用程序类
	 * 
	 * @var Application 
	 * @access protected 
	 */
	protected $app;
	
	/**
	 * 构造函数(兼容PHP4)
	 * 
	 * @param Application $ &$app 应用程序类
	 */
	public function AdminAction(& $app)
	{
		$this->__construct($app);
	}
	
	/**
	 * 构造函数
	 * 
	 * @param Application $ &$app 应用程序类
	 */
	public function __construct(& $app)
	{
		$this->app = $app;
		
		$this->db = $app->orm()->query();
		$this->mc = $app->cache('memcached', $app->cfg ['memcache']);
		$this->mc_pfx = $mx_pfx;
		if (!defined('NO_CHECK'))
		{
			$this->initAdminUserState();
		}
	}
	
	/**
	 * 默认Action
	 */
	public function doDefault()
	{
		/**
		 * nothing
		 */ 
	}

	/**
	 * 统一输出
	 * 
	 * @param mixed $data	 输出数据
	 * @param string $type	 输出类型
	 * @param boolean $exit	 是否结束程序, true结束, false不结束
	 * @param mixed $extra	 附加数据
	 * @return mixed 
	 */
	public function output($data, $type = 'json', $exit = true, $extra = '')
	{
		switch ($type)
		{
			case 'debug' :
				echo '<pre>'; print_r($data); echo '</pre>';
				break;
			case 'xml' :
				// echo $this->_toXml();
				break;
			case 'json' :
			default :
				if (isset($_GET['callback']))
				{
					$jsonp = $_GET['callback'];
					echo $jsonp . '(' . json_encode($data) . ')';
				}
				else
				{
					echo json_encode($data);
				}
		}
		
		if ($exit)
		{
			exit();
		}
		return true;
	}

	/**
	 * 输出验证输出处理
	 */
	public function doCode()
	{
		import('util.ValidateCode');
		$img = new ValidateCode();
		
		/** 设置字体文件与临时目录 **/
		$img->font_dir = $this->app->cfg['path']['fonts'];
		$img->temp_dir = $this->app->cfg['path']['temp'];
		
		$img->session_name = 'VALIDATE_CODE';
		$img->background_color(array('#FEFDCF', '#DFFEFF', '#FFEEE1', '#E1F4FF'));
		$img->grid_color(array('#FAD1AD', '#FFD9FB', '#D1D1E0'));
		$img->text_color(array('#801D00', '#5C0497', '#0289B0'));
		$img->overlap_text(false);
		$img->random_y_factor(4);
		$img->string_length(4);
		$img->frame_number(3);
		$img->frame_delay(80);
		$img->generate();
	}

	/**
	 * 验证管理员登录状态
	 */
	public function initAdminUserState($site = null, $AdminUserId = null)
	{
		if (defined(SITE_AUTH) && !$site)
		{
			return TRUE;
			exit();
		}
		if (isset($AdminUserId))
		{
			$this->db->clear();
			$AdminUserInfo = $this->db->addTable('AdminUser')->addWhere('AdminUserId', $AdminUserId)->getRow();
		}
		else
		{
			import('util.Safe');
			$safestr = Safe :: authcode($_COOKIE ['__admin'], 'DECODE', __KEY__);
			list($admin_uid, $admin_group) = explode("\t", $safestr);
			if (!empty($admin_uid))
			{
				$this->db->clear();
				$AdminUserInfo = $this->db->addTable('AdminUser')->addWhere('AdminUserId', $admin_uid)->getRow();
			}
		}
		$this->db->clear();
		$AdminGroupInfo = $this->db->addTable('AdminGroup')->addWhere('AdminGroupId', $AdminUserInfo['AdminUserGroup'])->getRow();
		$adminauth = $AdminGroupInfo['AdminAuth'];
		$admin_auth = explode('|', $adminauth);
		$site_auth = SITE_AUTH;
		if ($site)
		{
			$site_auth = $site;
		}
		if (in_array($site_auth, $admin_auth))
		{
			$auth = 1;
		}
		else
		{
			$auth = 0;
		}
		if ($AdminUserInfo['AdminUserGroup'] < 1 || $auth == 0)
		{
			import('plugins.Log');
			$log = new Log();
			//记录日志 时间\用户ID\是否自动登录\是否来自开放平台
			$data = time() . ' ' . $admin_uid . ' 2 0';
			$log->reset()->setPath("Admin/login/info")->setData($data)->write();
			$msg = "权限不够或密码不对！";
			HDshowMsg($msg, 'http://www.haodou.com/admin/v2/index.php');
		}
		else
		{
			return $AdminUserInfo;
		}
	}

	/**
	 * 验证管理员登录状态
	 */
	public function GetAdminUserInfo($site = null, $AdminUserId = null)
	{
		if (isset($AdminUserId))
		{
			$this->db->clear();
			$AdminUserInfo = $this->db->addTable('AdminUser')->addWhere('AdminUserId', $AdminUserId)->getRow();
		}
		else
		{
			import('util.Safe');
			$safestr = Safe :: authcode($_COOKIE ['__admin'], 'DECODE', __KEY__);
			list($admin_uid, $admin_group) = explode("\t", $safestr);
			if (!empty($admin_uid))
			{
				$this->db->clear();
				$AdminUserInfo = $this->db->addTable('AdminUser')->addWhere('AdminUserId', $admin_uid)->getRow();
			}
		}
		$this->db->clear();
		$AdminGroupInfo = $this->db->addTable('AdminGroup')->addWhere('AdminGroupId', $AdminUserInfo['AdminUserGroup'])->getRow();
		$adminauth = $AdminGroupInfo['AdminAuth'];
		$admin_auth = explode('|', $adminauth);
		
		$site_auth = SITE_AUTH;
		if ($site)
		{
			$site_auth = $site;
		}
		if (in_array($site_auth, $admin_auth))
		{
			$auth = 1;
		}
		else
		{
			$auth = 0;
		}
		if ($AdminUserInfo['AdminUserGroup'] < 1 || $auth == 0)
		{
			import('plugins.Log');
			$log = new Log();
			// 记录日志 时间\用户ID\用户名\是否自动登录\是否来自开放平台
			$data = time() . ' ' . $admin_uid . ' 2 0';
			$log->reset()->setPath("Admin/login/info")->setData($data)->write();
			$msg = "权限不够或密码不对！";
			HDshowMsg($msg, 'http://www.haodou.com/admin/v2/index.php');
		}
		else
		{
			return $AdminUserInfo;
		}
	}

	/**
	 * 验证验证码是否正确
	 * 
	 * @param string $code 
	 * @return boolean 
	 */
	public function checkCode($code)
	{
		if ($code && $code == $_SESSION ['VALIDATE_CODE'])
		{
			unset($_SESSION ['VALIDATE_CODE']);
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 存管理员的操作至数据库
	 * @param int 		 $id		操作对象的ID,如菜谱操作的话。就输入为 菜谱ID  RecipesId
	 * @param varchar	 $name		操作对象的名称 如操作菜谱的话。就是菜谱的title
	 * @param varchar	 $log		操作内容 如：删除菜谱
	 * @param int		 $logtype	记录操作类型 (方便后台进行某项功能进行分类) 默认为0，0=>'全部',11=>'菜谱审核通过',12=>'菜谱屏蔽',13=>'菜谱编辑',14=>'删除菜谱',15=>'设置活动菜谱',16=>'成果图片审核通过',17=>'成果图片审核屏蔽',18=>'成果图片删除',19=>'标签归类 自行约定。
	 * @param int		 $adminid	操作管理员ID;
	 * @param int		 $type		操作类型:默认为操作页面的设定权限[AUTH]值 比如:外聘人员的菜谱审核AUTH值为2-1
	 * @return boolean 如果参数不全返回false;
	 */
	public function dblog($id, $name, $log, $logtype = 0, $adminid = 0, $type = '')
	{
		if ($type == '')
		{
			$type = SITE_AUTH;
		}
		$t = date('Y-m-d H:i:s', time());
		import('util.Safe');
		$safestr = Safe :: authcode($_COOKIE ['__admin'], 'DECODE', __KEY__);
		list($admin_uid, $admin_group) = explode("\t", $safestr);
		if ($adminid != 0)
		{ // 需将帮手管理组ID设为100；
			$admin_uid = $adminid;
		}
		global $app;
		$this->db = $app->orm()->query();
		$this->db->clear();
		$adminusername = $this->db->addTable('AdminUser')->addField('AdminUserName')->addWhere('AdminUserId', $admin_uid)->getValue();
		$this->db->clear();
		$log = mysql_escape_string($log);
		$id = intval($id);
		$sql = "INSERT INTO `AdminLog` (`UserId`, `UserType`, `UserName`, `ItemId`, `Type`, `ItemName`, `Log`,`LogType`, `CreateTime`) VALUES ('" . $admin_uid . "', '" . $usertype . "', '" . $adminusername . "', '" . $id . "', '" . $type . "', '" . $name . "', '" . $log . "','" . $logtype . "', '" . $t . "')";
		$this->db->exec($sql);
		return $sql;
	}

	/**
	 * 记录搜索时间
	 *
	 * @return Float
	 */
	public function timer()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}
?>