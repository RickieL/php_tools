<?php

/**
 * 页面接口类
 * 
 * @package lib
 * @subpackage core.page
 */

class APageFactory
{
	/**
	 * 页面参数,如title等
	 *
	 * @var array
	 */
	public $params = array();
	
	/**
	 * 应用程序类
	 * @var Aplication
	 * @var protected
	 */
	public $app;

	/**
	 * 构造函数
	 * @param Appcation $app
	 */
	public function __construct(& $app)
	{
		$this->app = & $app;
	}

	/**
	 * 创建一个页面类
	 *
	 * @param Application $app 应用程序类
	 * @param string $engine 页面引擎
	 * @return APage
	 */
	public function create(& $app, $engine = NULL)
	{
		$className = 'Page' . ucfirst($engine);
		import('plugins.page.' . $className);
		return new $className($app);
	}

	/**
	 * 页面变量取值/赋值
	 *
	 * @param string $name 变量名
	 * @param mixed $value 变量值,如果该参数未指定,则返回变量值,否则设置变量值
	 * @return APage 如果参数为NULL则返回Page对象本身,否则返回变量值
	 */
	public function value($name, $value = NULL)
	{ /*nothing*/	}

	/**
	 * 页面内容输出
	 *
	 * @param boolean $return
	 */
	public function output($return = false)
	{ /*nothing*/	}

	/**
	 * 生成分页中除上一页、下一页、首页、最后页的连续部分
	 *
	 * @param integer $page_total
	 * @param integer $page_size
	 * @param integer $page_cur
	 * @param string $style
	 * @return string 
	 * @example 
	 * $style = 'index.php?page=%d&type=fuck'; //%d替换成页码
	 * $nav = getNav(11,3,4,$style);
	 */
	public function getNav($page_total, $page_size = PAGR_NAV_SIZE, $page_cur, $style, $cur_style = null)
	{
		$nav = '';
		
		if (isset($page_total) && $page_total > 1)
		{
			if ($page_cur <= (ceil(($page_size / 2)) + 1)) //当页码小于分页规格时，起始页码为第一页
				$nav_start_page = 0;
			elseif (($page_total - $page_cur) + 1 < $page_size && $page_total > $page_size) //当前页面到最后一页的长度小于分页规格时，设置开始分页为总页面长度减少页面规格   
				$nav_start_page = $page_total - $page_size;
			elseif ($page_cur > (ceil(($page_size / 2)) + 1))
				$nav_start_page = $page_cur - (ceil(($page_size / 2)) + 1);
			else
				$nav_start_page = $page_cur;
			
			for ($i = $nav_start_page, $m = 0; $i < $page_total && $m < $page_size; $i ++, $m ++)
			{
				if (! empty($cur_style) && ($i + 1) == $page_cur)
					$nav .= str_replace('%d', ($i + 1), $cur_style);
				else
					$nav .= str_replace('%d', ($i + 1), $style);
			}
		
		}
		
		return $nav;
	}

	/**
	 * 分页函数，输出全部html代码
	 *
	 * @param Integer $page_total	总页数
	 * @param Integer $page_current	当前页      
	 * @param String $page_alias 分页参数别名 如：index.php?$page_alias =$d
	 * @param String $url	地址，如：index.php?page=%d 默认为当前页，page=%d
	 * @return String 分页字符串
	 */
	public function getPageStr($page_total, $page_current, $page_alias = 'p', $url = '', $append = false, $page_num = 1)
	{
		
		$page_current = intval($page_current);
		
		if ($page_current < 1)
		{
			$page_current = 1;
		}
		
		if ($page_total < 2)
			return "";
		
		// 默认url地址处理
		if ($url == '')
		{
			$url = "?" . $page_alias . "={pageval}";
		
		}
		else
		{
			if ($append == false)
			{
				$url = "?" . $page_alias . "={pageval}" . '&' . $url;
			}
			else
			{
				$url = "?" . $page_alias . "={pageval}" . $url;
			}
		}
		
		$html = '';
		
		//当前选中页大于第一页，出现上一页
		if ($page_current > 1)
		{
			$html .= '<li><a href="' . str_replace('{pageval}', $page_current - 1, $url) . '">上一页</a></li>';
		}
		//第一页
		$html .= ($page_current == 1) ? "<li class='active'><a href='#'>1</a></li>" : '<li><a href="' . str_replace('{pageval}', 1, $url) . '">1</a></li>';
		//当前选中页前的页数大于$page_num(默认为4)时，第一页后出现 ...
		if ($page_current - 2 >= $page_num)
		{
			$html .= '<li class="disabled"><a href="#">...</a></li>';
		}
		//循环列出页数除第一页和最后一页的其他当前选中页的前$page_num(默认为4)页和后$page_num(默认为4)页
		for ($i = $page_current - $page_num; $i <= $page_current + $page_num; $i ++)
		{
			//不显示第一页、最后页、不存在页
			if ($i > 1 && $i < $page_total)
			{
				$html .= ($i == $page_current) ? "<li class='active'><a href='#'>".$i."</a></li>" : "<li><a href=\"" . str_replace('{pageval}', $i, $url) . '">' . $i . '</a></li>';
			}
		}
		//当前选中页后页数大于$page_num(默认为4)时，最后一页前出现 ...
		if ($page_current < $page_total - $page_num)
		{
			$html .= '<li class="disabled"><a href="#">...</a></li>';
		}
		//最后一页
		$html .= ($page_current == $page_total) ? "<li class='active'><a href='#'>".$page_total."</a></li>" : '<li><a href="' . str_replace('{pageval}', $page_total, $url) . '">' . $page_total . '</a></li>';
		//当前页小于总页数，出现下一页
		if ($page_current < $page_total)
		{
			$html .= '<li><a href="' . str_replace('{pageval}', $page_current + 1, $url) . '">下一页</a></li>';
		}
		if ($html)
		{
			$html = "<ul>" . $html . "</ul>";
		}
		
		//返回分页
		return $html;
	}

	public function getPageAjax($page_max, $page_dango, $page_dango_class, $page_attr_id, $page_pre_class = 'next', $page_num = 4)
	{
		if ($page_max < 2)
			return "";
		if ($page_dango < 1)
			$page_dango = 1;
		if ($page_max > 100)
			$page_max = 100;
		if ($page_dango > $page_max)
			$page_dango = $page_max;
		
		$elide = "<span style='margin-right:4px;'>...</span>";
		$html = "";
		//当前选中页大于第一页，显示上一页
		if ($page_dango > 1)
		{
			$html .= "<span class='" . $page_pre_class . "'><a href='javaScript:;' page='" . ($page_dango - 1) . "' id='" . $page_attr_id . "' class='" . $page_pre_class . "'>上一页</a></span>";
		}
		//第一页
		if ($page_dango == 1)
		{
			$html .= "<a href='javaScript:;' page='1' id='" . $page_attr_id . "' class='" . $page_dango_class . "'>1</a>";
		}
		else
		{
			$html .= "<a href='javaScript:;' page='1' id='" . $page_attr_id . "'>1</a>";
		}
		//当前选中页前的页数大于$page_num(默认为4)，第一页后出现...
		if ($page_dango - 2 >= $page_num)
		{
			$html .= $elide;
		}
		//循环显示除第一页、最终页的当前选中页的前$page_num(默认为4)页和后$page_num(默认为4)页
		for ($i = $page_dango - $page_num; $i <= $page_dango + $page_num; $i ++)
		{
			if ($i > 1 && $i < $page_max)
			{
				if ($i == $page_dango)
				{
					$html .= "<a href='javaScript:;' page='" . $i . "' id='" . $page_attr_id . "' class='" . $page_dango_class . "'>" . $i . "</a>";
				}
				else
				{
					$html .= "<a href='javaScript:;' page='" . $i . "' id='" . $page_attr_id . "'>" . $i . "</a>";
				}
			}
		}
		//当前选中页后页数大于$page_num(默认为4)页，最终页前出现...
		if ($page_dango < $page_max - $page_num)
		{
			$html .= $elide;
		}
		//最终页
		if ($page_dango == $page_max)
		{
			$html .= "<a href='javaScript:;' page='" . $page_max . "' id='" . $page_attr_id . "' class='" . $page_dango_class . "'>" . $page_max . "</a>";
		}
		else
		{
			$html .= "<a href='javaScript:;' page='" . $page_max . "' id='" . $page_attr_id . "'>" . $page_max . "</a>";
		}
		//当前选中页小于最终页，显示下一页
		if ($page_dango < $page_max)
		{
			$html .= "<span class='" . $page_pre_class . "'><a href='javaScript:;' page='" . ($page_dango + 1) . "' id='" . $page_attr_id . "' class='" . $page_pre_class . "'>下一页</a></span>";
		}
		//返回分页
		return $html;
	}

	/**
	 * 分页函数，输出全部html代码
	 *
	 * @param Integer $page_total	总页数
	 * @param Integer $page_current	当前页      
	 * @param String $url 分页参数别名 如：http://www.haodou.com/recipe-chuancai/{pageval}  {pageval}替换的页码
	 * @return String $page_num 分页字符串
	 */
	public function getRewritePageStr($page_total, $page_current, $url = '', $page_num = 4)
	{
		$page_current = intval($page_current);
		
		if ($page_current < 1)
		{
			$page_current = 1;
		}
		
		if ($page_total < 2)
			return "";
		
		$html = '';
		
		//当前选中页大于第一页，出现上一页
		if ($page_current > 1)
		{
			$html .= ($page_current - 1 == 1) ? '<span class="pre"><a href="' . str_replace('/{pageval}', '', $url) . '"><span class="pageNext"></span>上一页</a></span>' : '<span class="pre"><a href="' .
			 str_replace('{pageval}', $page_current - 1, $url) . '"><span class="pageNext"></span>上一页</a></span>';
		}
		//第一页
		$html .= ($page_current == 1) ? "<span class='cur'>1</span>" : '<a href="' . str_replace('/{pageval}', '', $url) . '">1</a>';
		//当前选中页前的页数大于$page_num(默认为4)时，第一页后出现 ...
		if ($page_current - 2 >= $page_num)
		{
			$html .= '<span style="margin-right:4px;">...</span>';
		}
		//循环列出页数除第一页和最后一页的其他当前选中页的前$page_num(默认为4)页和后$page_num(默认为4)页
		for ($i = $page_current - $page_num; $i <= $page_current + $page_num; $i ++)
		{
			//不显示第一页、最后页、不存在页
			if ($i > 1 && $i < $page_total)
			{
				$html .= ($i == $page_current) ? "<span class='cur'>$i</span>" : "<a href=\"" . str_replace('{pageval}', $i, $url) . '">' . $i . '</a>';
			}
		}
		//当前选中页后页数大于$page_num(默认为4)时，最后一页前出现 ...
		if ($page_current < $page_total - $page_num)
		{
			$html .= '<span style="margin-right:4px;">...</span>';
		}
		//最后一页
		$html .= ($page_current == $page_total) ? "<span class='cur'>$page_total</span>" : '<a href="' . str_replace('{pageval}', $page_total, $url) . '">' . $page_total . '</a>';
		//当前页小于总页数，出现下一页
		if ($page_current < $page_total)
		{
			$html .= '<span class="next"><a href="' . str_replace('{pageval}', $page_current + 1, $url) . '">下一页<span class="pagePre"></span></a></span>';
		}
		//返回分页
		return $html;
	}
}
