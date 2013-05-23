<?php

/**
 * 框架使用样例－验证码
 * 
 * 
 * $Id$
 */

require_once('../../../common.inc.php');

import('util.Securimage');

/**
 * 常用参数说明
 *
 * @param int image_width				图片宽
 * @param int image_height				图片高，注意尺寸越大，扭曲性能越差
 * @param int offset_x					字间距补偿
 * @param string|array $text_color		字体颜色，可传多个 如：array('#FAD1AD', '#FFD9FB', '#D1D1E0')
 * @param string|array $line_color      干扰线颜色，可传多个 如：array('#FAD1AD', '#FFD9FB', '#D1D1E0')
 * @param string|array $noise_color     噪点颜色，可传多个 如：array('#FAD1AD', '#FFD9FB', '#D1D1E0')
 * @param string|array $image_bg_color  背景颜色，可传多个 如：array('#FAD1AD', '#FFD9FB', '#D1D1E0')
 * @param float $noise_level			噪点等级，等级越高点越多
 * @param int $num_lines				干扰线数量 
 * @param int $captcha_type				值为1时使用简单数学问题 
 * @param bool $lang_cn					使用中文验证码，字体需支持中文
 * @param string $ttf_file				自定义字体
 * @param float $text_scale				字体相对画布的大小，使用中文时相对调小，如 0.4
 * @param float $perturbation			扭曲度，默认为1，值越大变形越大
 * @param int $iscale					值为1时候表示正弦扭曲，此时$perturbation失效
 * @param string $namespace				名字空间，需同时支持多验证码时使用，
 *
 * @return boolean            返回记录日志是否成功。 true|false
 */


class utilsecurimage extends Action   
{
	/**
	 * 默认显示(默认Action)
	 */
	function doDefault() 
	{	
		$img = new Securimage();
		$img->show(); 
	}

	/**
	 * 改变图片尺寸
	 * 注意：尺寸加大，扭曲性能降低
	 */
	function doChangeSize() 
	{	
		$options = array(
			'image_width' => 180,
			'image_height' => 57,
			'offset_x' => 30, //需调节字体间距
		);
		$img = new Securimage($options);
		$img->show(); 
	}

	/**
	 * 使用中文
	 * 注意：字体需支持中文，默认MSYH.TTF
	 */
	function doUseCn() 
	{	
		$options = array(
			'image_width' => 180,
			'image_height' => 50,
			'offset_x' => 40,	//需调节字体间距
			'lang_cn' => true,	//使用中文
			'text_scale' => 0.4,
			'ttf_file' => $this->app->cfg['path']['fonts'].'MSYH.TTF', //指定字体路径, 需支持中文
		);
		$img = new Securimage($options);
		$img->show(); 
	}

	/**
	 * 颜色改变
	 * 
	 */
	function doChangecolor() 
	{	
		$options = array(
			'image_bg_color' => array('#801D00', '#4B0082', '#8B0000', '#008080', '#B22222'),
			'text_color' => array('#FAD1AD', '#FFD9FB', '#D1D1E0'),
			'line_color' => array('#8B0000', '#FFEEE1', '#E1F4FF'),	//需调节字体间距
			'noise_color' => array('#FEFDCF', '#F0FFF0', '#FFEEE1', '#E1F4FF'),	//使用中文
			'noise_level' => 0.5,
			'offset_x' => 15,	//需调节字体间距
			'captcha_type' => 1 //使用数字问题
		);
		$img = new Securimage($options);
		$img->show(); 
	}

	/**
	 * 正弦扭曲
	 * 
	 */
	function doSin() 
	{	
		$options = array(
			'iscale' => 1,
			'image_width' => 180,
			'image_height' => 50,
			'offset_x' => 30,	//需调节字体间距
			'text_scale' => 0.6,
		);
		$img = new Securimage($options);
		$img->show(); 
	}

	/**
	 * 名字空间，需同时支持多验证码时使用
	 * 
	 * 对指定 namespace 进行验证使用以下代码：
	 * $img = new Securimage();
	 * $img->namespace = 'myname';
	 * $chk = $img->check($code);
	 */
	function doSpacename() 
	{	
		$options = array(
			'spacename' => 'myname',
		);
		$img = new Securimage($options);
		$img->show(); 
	}

	/**
	 * 名字空间，需同时支持多验证码时使用
	 */
	function doCheck() 
	{	
		if($_POST['code'])
		{
			$code = trim($_POST['code']);
			$img = new Securimage();
			//$img->namespace = 'myname'; 对特定命名验证时需特别指定 namespace
			$chk = $img->check($code);  // return true or false
			if($chk)
				echo ' <b style="color:green">验证通过!</b> '; //do something...
			else
				echo '<b style="color:red">验证失败!</b> '; //do something...
		}
		$output = <<<EOF
		<FORM action="" method="post">
		<p><img id="captche" onclick="javascript:this.src='?do=code&'+Math.random()" src="?do=code" style="cursor:pointer" title="看不清? 换一张"></p>
		code: <INPUT type="text" name="code" value=""><BR>
		<INPUT type="submit" value="Send"> 
		</P></FORM>
EOF;
		echo $output;
	}


}

$app->run();
?>