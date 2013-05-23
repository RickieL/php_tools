<?php

import('util.Json');

/**
 * 返回 Json 的Page类
 *
 * @package lib
 * @subpackage core.page
 */

class PageJson extends APageFactory
{
	/**
	 * 值
	 * @var array $value
	 * @access private
	 */
	public $value = array();

	/**
	 * 给变量赋值
	 *
	 * @param string $name 变量名,如果参数类型为数组,则为变量赋值,此时$value参数无效
	 * @param mixed $value 变量值,如果该参数未指定,则返回变量值,否则设置变量值
	 * @return APage 如果参数为NULL则返回Page对象本身,否则返回变量值
	 */
	public function value($name, $value = NULL)
	{
		if ($value === NULL && ! is_array($name))
		{ //取值
			return $this->value[$name];
		}
		else
		{ //赋值
			if (is_array($name))
			{ //如果是数组则批量变量赋值
				foreach ($name as $k => $v)
				{
					$this->value[$k] = $v;
				}
			}
			else
			{
				$this->value[$name] = & $value;
			}
			return $this;
		}
	}

	/**
	 * 页面内容输出
	 * @param boolean $fetch 是否提取输出结果
	 * @return string JSON结果
	 */
	public function output($fetch = false)
	{
		ob_start();
		
		$json = new Json();
		echo $json->encode($this->value);
		
		$content = ob_get_contents();
		if ($fetch)
		{
			ob_end_clean();
		}
		else
		{
			ob_end_flush();
		}
		return $content;
	}
}
