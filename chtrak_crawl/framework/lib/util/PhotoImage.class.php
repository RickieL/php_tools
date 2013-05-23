<?php
/**
 * 图片操作类
 * 
 * @package modules
 * @subpackage Photo
 * 
 */

class PhotoImage {
	
	/**
	 * 判断图片类型,并返回
	 * 用于相册,支持jpg,png,bmp,gif
	 *
	 * @param string $path 图片路径
	 * @return boolean|string 失败返回false,成功返回图片扩展名
	 */
	public static function getImageType($path) {
		$type = exif_imagetype ( $path );
		switch ($type) {
			case IMAGETYPE_JPEG :
				return 'jpg';
				break;
			case IMAGETYPE_GIF :
				return 'gif';
				break;
			case IMAGETYPE_PNG :
				return 'png';
				break;
			case IMAGETYPE_BMP :
				return 'bmp';
				break;
			default :
				return false;
		}
	}
	
	/**
	 * 创建缩略图,相册专用
	 *
	 * @param string $filepath			图片路径
	 * @param string $sava_path			缩略图生成后,存放的完整路径
	 * @param integer $new_width		图片新宽度,默认150
	 * @param integer $new_height		图片新高度,默认100
	 * @param boolean $keep_proportion	是否保持比例,默认保持比例
	 * @param integer $quality			JPEG图片生成质量,默认80
	 * @return boolean 					成功返回true,失败返回false
	 */
	public static function imageResize($filepath, $sava_path, $new_width = 150, $new_height = 100, $keep_proportion = true, $quality = 80) {
		$image_type = self::getImageType ( $filepath );
		switch ($image_type) {
			case 'jpg' :
				$image = imagecreatefromjpeg ( $filepath );
				break;
			case 'gif' :
				$image = imagecreatefromgif ( $filepath );
				break;
			case 'png' :
				$image = imagecreatefrompng ( $filepath );
				break;
			case 'bmp' :
				$image = self::imagecreatefrombmp ( $filepath );
				break;
			default :
				return false;
		}
		
		if ($image == false) {
			return false;
		}
		
		list ( $width, $height ) = getimagesize ( $filepath );
		
		// 保持比例
		if ($keep_proportion == true) {
			if ($width <= $new_width && $height <= $new_height) {
				$new_width = $width;
				$new_height = $height;
			} else if ($height * $new_width > $width * $new_height) {
				$new_height = round ( ($new_width / $width) * $height );
			
		//$new_width = round($width / ($height / $new_height));
			} else {
				$new_width = round ( ($new_height / $height) * $width );
			
		//$new_height = round($height / ($width / $new_width));
			}
			if ($new_width < 1)
				$new_width = 1;
			if ($new_height < 1)
				$new_height = 1;
		} else {
			$width = $new_width;
			$height = $new_height;
		}
		
		$image_color = imagecreatetruecolor ( $new_width, $new_height );
		$trans_colour = imagecolorallocate ( $image_color, 255, 255, 255 );
		imagefill ( $image_color, 0, 0, $trans_colour );
		if (! imagecopyresampled ( $image_color, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height )) {
			return false;
		}
		
		switch ($image_type) {
			case 'jpg' :
			case 'bmp' :
				if (imagejpeg ( $image_color, $sava_path, $quality )) {
					imagedestroy ( $image_color );
					return true;
				}
				break;
			case 'gif' :
				imagecolortransparent ( $image_color, imagecolorallocate ( $image_color, 0, 0, 0 ) );
				if (imagegif ( $image_color, $sava_path )) {
					imagedestroy ( $image_color );
					return true;
				}
				break;
			case 'png' :
				imagecolortransparent ( $image_color, imagecolorallocate ( $image_color, 0, 0, 0 ) );
				if (imagepng ( $image_color, $sava_path )) {
					imagedestroy ( $image_color );
					return true;
				}
				break;
			
			default :
				return false;
		}
		imagedestroy ( $image_color );
		return false;
	}
	
	/**
	 * 创建缩略图,相册专用
	 *
	 * @param string $filepath			图片路径
	 * @param string $sava_path			缩略图生成后,存放的完整路径
	 * @param integer $new_width		图片新宽度,默认150
	 * @param integer $new_height		图片新高度,默认100
	 * @param boolean $keep_proportion	是否保持比例,默认保持比例
	 * @param integer $quality			JPEG图片生成质量,默认80
	 * @return boolean 					成功返回true,失败返回false
	 */
	public static function imageResizeRecipe($filepath, $sava_path, $new_width = 175, $new_height = 175, $keep_proportion = true, $quality = 80, $is_watermark = false) {
		$image_type = self::getImageType ( $filepath );
		switch ($image_type) {
			case 'jpg' :
				$image = imagecreatefromjpeg ( $filepath );
				break;
			case 'gif' :
				$image = imagecreatefromgif ( $filepath );
				break;
			case 'png' :
				$image = imagecreatefrompng ( $filepath );
				break;
			case 'bmp' :
				$image = self::imagecreatefrombmp ( $filepath );
				break;
			default :
				return false;
		}
		
		if ($image == false) {
			return false;
		}
		
		list ( $width, $height ) = getimagesize ( $filepath );
		
		// 保持比例
		if ($keep_proportion == true) {
			if ($width <= $new_width && $height <= $new_height) {
				$new_width = $width;
				$new_height = $height;
				$srcX = 0;
				$srcY = 0;
			} else if ($height * $new_width > $width * $new_height) {
				$test_height = round ( $new_height * $width / $new_width );
				$srcX = 0;
				$srcY = round ( ($height - $test_height) / 2 );
				$height = $test_height;
			} else {
				$text_width = round ( $new_width * $height / $new_height );
				$srcX = round ( ($width - $text_width) / 2 );
				$srcY = 0;
				$width = $text_width;
			}
			if ($new_width < 1)
				$new_width = 1;
			if ($new_height < 1)
				$new_height = 1;
		}
		
		$image_color = imagecreatetruecolor ( $new_width, $new_height );
		$trans_colour = imagecolorallocate ( $image_color, 255, 255, 255 );
		imagefill ( $image_color, 0, 0, $trans_colour );
		if (! imagecopyresampled ( $image_color, $image, 0, 0, $srcX, $srcY, $new_width, $new_height, $width, $height )) {
			return false;
		}
		
		if ($is_watermark == true) {
			self::_watermark ( $image_color, $new_width, $new_height );
		}
		
		switch ($image_type) {
			case 'jpg' :
			case 'bmp' :
				if (imagejpeg ( $image_color, $sava_path, $quality )) {
					imagedestroy ( $image_color );
					return true;
				}
				break;
			case 'gif' :
				imagecolortransparent ( $image_color, imagecolorallocate ( $image_color, 0, 0, 0 ) );
				if (imagegif ( $image_color, $sava_path )) {
					imagedestroy ( $image_color );
					return true;
				}
				break;
			case 'png' :
				imagecolortransparent ( $image_color, imagecolorallocate ( $image_color, 0, 0, 0 ) );
				if (imagepng ( $image_color, $sava_path )) {
					imagedestroy ( $image_color );
					return true;
				}
				break;
			
			default :
				return false;
		}
		imagedestroy ( $image_color );
		return false;
	}
	
	/**
	 * 特定裁剪580大小的图
	 * @param string $filepath			图片路径
	 * @param string $sava_path			缩略图生成后,存放的完整路径
	 * @param integer $new_width		图片新宽度,默认150
	 * @param integer $new_height		图片新高度,默认100
	 * @param boolean $keep_proportion	是否保持比例,默认保持比例
	 * @param integer $quality			JPEG图片生成质量,默认80
	 * @return boolean 					成功返回true,失败返回false
	 */
	public static function imageResize580($filepath, $sava_path, $new_width = 580, $new_height = 580, $keep_proportion = true, $quality = 80) {
		$image_type = self::getImageType ( $filepath );
		switch ($image_type) {
			case 'jpg' :
				$image = imagecreatefromjpeg ( $filepath );
				break;
			case 'gif' :
				$image = imagecreatefromgif ( $filepath );
				break;
			case 'png' :
				$image = imagecreatefrompng ( $filepath );
				break;
			case 'bmp' :
				$image = self::imagecreatefrombmp ( $filepath );
				break;
			default :
				return false;
		}
		
		if ($image == false) {
			return false;
		}
		
		list ( $width, $height ) = getimagesize ( $filepath );
		
		if ($width <= $new_width && $height <= $new_height) {
			$new_width = $width;
			$new_height = $height;
		} else {
			$new_width = 580;
			$new_height = round ( (580 / $width) * $height );
		}
		
		$image_color = imagecreatetruecolor ( $new_width, $new_height );
		$trans_colour = imagecolorallocate ( $image_color, 255, 255, 255 );
		imagefill ( $image_color, 0, 0, $trans_colour );
		if (! imagecopyresampled ( $image_color, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height )) {
			return false;
		}
		
		// 添加水印
		self::_watermark ( & $image_color, $new_width, $new_height );
		
		switch ($image_type) {
			case 'jpg' :
			case 'bmp' :
				if (imagejpeg ( $image_color, $sava_path, $quality )) {
					imagedestroy ( $image_color );
					return true;
				}
				break;
			case 'gif' :
				imagecolortransparent ( $image_color, imagecolorallocate ( $image_color, 0, 0, 0 ) );
				if (imagegif ( $image_color, $sava_path )) {
					imagedestroy ( $image_color );
					return true;
				}
				break;
			case 'png' :
				imagecolortransparent ( $image_color, imagecolorallocate ( $image_color, 0, 0, 0 ) );
				if (imagepng ( $image_color, $sava_path )) {
					imagedestroy ( $image_color );
					return true;
				}
				break;
			
			default :
				return false;
		}
		imagedestroy ( $image_color );
		return false;
	}
	
	/**
	 * 生成水印
	 * 
	 * @param object $im			图片资源
	 * @param string $width			图片资源的宽度
	 * @param string $height		图片资源的高度
	 * @return object|boolean		图片资源
	 */
	private function _watermark(& $im, $width, $height) {
		global $app;
		$water_path = dirname ( $app->cfg ['path'] ['root'] ) . '/public/images/waterimg_min.png';
		if (file_exists ( $water_path )) {
			// 加载水印图片
			$water = imagecreatefrompng ( $water_path );
			if ($water) {
				return imagecopy ( $im, $water, $width - 15 - 100, $height - 15 - 29, 0, 0, 100, 29 );
			}
		}
		return false;
	}
	
	/**
	 * 转换BMP为GD格式
	 *
	 * @param string $src	输入文件
	 * @param string $dest	输出文件	
	 * @return boolean 		成功返回true,失败返回false
	 */
	private function ConvertBMP2GD($src, $dest) {
		if (! ($src_f = fopen ( $src, "rb" ))) {
			return false;
		}
		if (! ($dest_f = fopen ( $dest, "wb" ))) {
			return false;
		}
		$header = unpack ( "vtype/Vsize/v2reserved/Voffset", fread ( $src_f, 14 ) );
		$info = unpack ( "Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant", fread ( $src_f, 40 ) );
		
		extract ( $info );
		extract ( $header );
		
		if ($type != 0x4D42) {
			return false;
		}
		
		$palette_size = $offset - 54;
		$ncolor = $palette_size / 4;
		$gd_header = "";
		
		$gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
		$gd_header .= pack ( "n2", $width, $height );
		$gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
		if ($palette_size) {
			$gd_header .= pack ( "n", $ncolor );
		}
		$gd_header .= "\xFF\xFF\xFF\xFF";
		fwrite ( $dest_f, $gd_header );
		
		if ($palette_size) {
			$palette = fread ( $src_f, $palette_size );
			$gd_palette = "";
			$j = 0;
			while ( $j < $palette_size ) {
				$b = $palette {$j ++};
				$g = $palette {$j ++};
				$r = $palette {$j ++};
				$a = $palette {$j ++};
				$gd_palette .= "$r$g$b$a";
			}
			$gd_palette .= str_repeat ( "\x00\x00\x00\x00", 256 - $ncolor );
			fwrite ( $dest_f, $gd_palette );
		}
		
		$scan_line_size = (($bits * $width) + 7) >> 3;
		$scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size & 0x03) : 0;
		
		for($i = 0, $l = $height - 1; $i < $height; $i ++, $l --) {
			fseek ( $src_f, $offset + (($scan_line_size + $scan_line_align) * $l) );
			$scan_line = fread ( $src_f, $scan_line_size );
			if ($bits == 24) {
				$gd_scan_line = "";
				$j = 0;
				while ( $j < $scan_line_size ) {
					$b = $scan_line {$j ++};
					$g = $scan_line {$j ++};
					$r = $scan_line {$j ++};
					$gd_scan_line .= "\x00$r$g$b";
				}
			} else if ($bits == 8) {
				$gd_scan_line = $scan_line;
			} else if ($bits == 4) {
				$gd_scan_line = "";
				$j = 0;
				while ( $j < $scan_line_size ) {
					$byte = ord ( $scan_line {$j ++} );
					$p1 = chr ( $byte >> 4 );
					$p2 = chr ( $byte & 0x0F );
					$gd_scan_line .= "$p1$p2";
				}
				$gd_scan_line = substr ( $gd_scan_line, 0, $width );
			} else if ($bits == 1) {
				$gd_scan_line = "";
				$j = 0;
				while ( $j < $scan_line_size ) {
					$byte = ord ( $scan_line {$j ++} );
					$p1 = chr ( ( int ) (($byte & 0x80) != 0) );
					$p2 = chr ( ( int ) (($byte & 0x40) != 0) );
					$p3 = chr ( ( int ) (($byte & 0x20) != 0) );
					$p4 = chr ( ( int ) (($byte & 0x10) != 0) );
					$p5 = chr ( ( int ) (($byte & 0x08) != 0) );
					$p6 = chr ( ( int ) (($byte & 0x04) != 0) );
					$p7 = chr ( ( int ) (($byte & 0x02) != 0) );
					$p8 = chr ( ( int ) (($byte & 0x01) != 0) );
					$gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
				}
				$gd_scan_line = substr ( $gd_scan_line, 0, $width );
			}
			fwrite ( $dest_f, $gd_scan_line );
		}
		fclose ( $src_f );
		fclose ( $dest_f );
		return true;
	}
	
	/**
	 * 生成BMP图片资源
	 *
	 * @param string $filename	图片文件名
	 * @return res|boolean		成功返回图片资源,失败返回false
	 */
	public static function imagecreatefrombmp($filename) {
		$tmp_name = tempnam ( ini_get ( 'upload_tmp_dir' ), "GD" );
		if (self::ConvertBMP2GD ( $filename, $tmp_name )) {
			$img = imagecreatefromgd ( $tmp_name );
			unlink ( $tmp_name );
			return $img;
		}
		return false;
	}
}
?>