<?php
/**
 * 应用Smarty的Page类
 *
 * @package lib
 * @subpackage core.page
 *
 */

import('smarty.Smarty');

class PageSmarty extends APageFactory 
{
	/**
	 * Smarty 对象
	 *
	 * @var object Smarty
	 * @access private
	 */
	private $smarty;
	
	/**
	 * 构造函数
	 *
	 * @param Application $app
	 */
	public function __construct(& $app) 
	{
		parent::__construct(& $app);
		$this->smarty = new Smarty;
		//设置模板目录
		if (isset($app->cfg['smarty']['template_dir']))
			$this->smarty->setTemplateDir($app->cfg['smarty']['template_dir']);
		//设置编译文件目录
		if (isset($app->cfg['smarty']['compile_dir']))
			$this->smarty->setCompileDir($app->cfg['smarty']['compile_dir']);
		//设置插件目录
		$this->smarty->setPluginsDir(SMARTY_DIR . 'plugins/');
		//赋值引用
		$this->smarty->assignByRef('cfg', $app->cfg);
		//注册插件
		$this->smarty->registerPlugin('modifier', 'head', 'head');
		$this->smarty->registerPlugin('modifier', 'html', '_htmlspecialchars');
		$this->smarty->registerPlugin('modifier', 'pai_phone', 'pai_phone');
		
		//设置默认的图片、样式、js、flash的模版路径
		$this->value('url_images', $app->cfg['url']['images']);
		$this->value('url_css', $app->cfg['url']['css']);
		$this->value('url_js', $app->cfg['url']['js']);
		$this->value('url_swf', $app->cfg['url']['swf']);
		//设置站点名称
		$this->value('site_title', $app->cfg['site']['title']);
	}
	
	/**
	 * 给页面变量赋值
	 *
	 * @param string $name 变量名,如果参数类型为数组,则为变量赋值,此时$value参数无效
	 * @param mixed $value 变量值,如果该参数未指定,则返回变量值,否则设置变量值
	 * @return APage 如果参数为NULL则返回Page对象本身,否则返回变量值
	 */
	public function value($name, $value = NULL) 
	{
		//取值
		if ($value === NULL && !is_array($name))
		{
			return $this->smarty->getTemplateVars($name);
		}
		//赋值
		else
		{
			//如果是数组则批量变量赋值
			if (is_array($name))
			{
				foreach ($name as $k => $v)
				{
					$this->smarty->assign($k, $v);
				}
			}
			else
			{
				$this->smarty->assign($name, $value);
			}
			return $this;
		}
	}
	
	
	/**
	 * 页面内容输出
	 * 
	 * @param string 	指定输出的模板
	 * @param boolean 	$fetch 是否提取输出结果
	 */
	public function output($template = '', $fetch = false)
	{
		if ($template)
		{
			$this->params['template'] = $template;
		}
		elseif (!isset($this->params['template'])) 
		{
			$offsetPath = substr($this->app->cfg['path']['current'], strlen(dirname($this->app->cfg['path']['root'])));
			($offsetPath{0} == '/') && $offsetPath = substr($offsetPath, 1);
			$this->params['template'] = $offsetPath . $this->app->name . $this->app->module . '.tpl';
		}
		
		if ($fetch)
			return $this->smarty->fetch($this->params['template']);
		else 
			$this->smarty->display($this->params['template']);
	}
	
	/**
	 * 返回smarty对象，供使用smarty其他功能
	 */
	public function getSmarty()
	{
		return $this->smarty;
	}
}
?>