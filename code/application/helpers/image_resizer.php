<?php
/**
 * Класс для изменения размеров изображений
 *
 */
class image_resizer{
	var $cache_url;
	var $cache_dir;
	
	var $clip = false;
	var $match_size = true;
	var $bg_color = array(255, 255, 255);
	var $use_timeout_mtime = false;
	
	/**
	 * Изменяет размер изображения
	 *
	 * @param string $src путь к файлу изображения
	 * @param int $width ширина
	 * @param int $height высота
	 * @return string путь к файлу с измененным изображением
	 */
	function resize($src, $width, $height){
		if(!file_exists($src))
			return false;
		$ext = strrpos('.', $src);
		$ext = substr($src, $ext);
				
		$name = 
			((int)$this->clip).
			((int)$this->match_size).
			($this->bg_color[0]<<16+$this->bg_color[1]<<8+$this->bg_color[2]).'_'.
			$width.'_'.$height.'_'.crc32(dirname($src)).'_'.basename($src);
			
		$resize = false;
		if(file_exists($this->cache_dir.'/'.$name)){
			$mtime = (int)@file_get_contents($this->cache_dir.'/'.$name.'.mtime');
			if(($this->use_timeout_mtime && ($mtime < time() - $this->use_timeout_mtime)) ||
				 ($this->use_timeout_mtime === false && $mtime != filemtime($src)))
				$resize = true;
		}
		else
			$resize = true;
			
		if($resize){
			if(!$this->resize_image($src, $this->cache_dir.'/'.$name, $width, $height, $this->clip, $this->match_size, $this->bg_color))
				return false;
			else{
				$f = fopen($this->cache_dir.'/'.$name.'.mtime', 'w');
				fwrite($f, filemtime($src));
				fclose($f);				
			}
		}
				
		return $this->cache_url.'/'.$name;
	}
	
	/**
	 * Изменяет размер изображения
	 *
	 * @param string $source путь к файлу с исходным изображением
	 * @param string $destination путь к файлу с измененным изображением
	 * @param int $width ширина
	 * @param int $height высота
	 * @param bool $clip Если в true - размер изображения изменяется до совпадения меньшей стороны, лишнее обрезается, если false - размер изображения изменяется до совпадения большей стороны, оно будет вписано в width и height
	 * @param bool $match_size Если в true - пространство, не заполненное изображением до width или height, будет заполнено цветом из параметра $bg_color. Имеет смысл при $clip = false
	 * @param array $bg_color цвет заливки фона
	 * @return bool
	 */
	function resize_image($source,$destination,$width,$height,$clip=0,$match_size = false, $bg_color=array(255, 255, 255)) {
		$info = @getimagesize($source);
		if(!$info)
			return false;
		
		$srcw = $info[0];
		$srch = $info[1];
		
		if (($srcw==0) or ($srch==0)) {
		    return false;
		}
		
			
		switch($info[2]){			  		
			case 1:	
				if(function_exists('imagecreatefromgif'))
					$srcim =  imagecreatefromgif($source); 
				else{
					copy($source, $destination);
					return true;
				}
			break;			
			case 2: 
				if(function_exists('imagecreatefromjpeg'))
					$srcim =  imagecreatefromjpeg($source);
				else{
					copy($source, $destination);
					return true;
				}
				break;
			case 3:
				if(function_exists('imagecreatefrompng'))
					$srcim =  imagecreatefrompng($source); 
				else{
					copy($source, $destination);
					return true;
				}
				break;
			case 15: 
				if(function_exists('imagecreatefromwbmp'))
					$srcim = imagecreatefromwbmp($source); 
				else{
					copy($source, $destination);
					return true;
				}
				break;
			case 16: 
				if(function_exists('imagecreatefromxbm'))
					$srcim = imagecreatefromxbm($source); 
				else{
					copy($source, $destination);
					return true;
				}
				break;
			default: 
				return false;
		} // switch	
		
		if (!$srcim) { return false; }
		
		$srcx = 0;
		$srcy = 0;
		
		$winw = $srcw;
		$winh = $srch;
			
		
		if (!$clip) {		
			
			$k = $width/$srcw;
			
			// подгоняем масштаб по ширине
			$dstw = $width;
			$dsth = round($srch*$k);
			
			if ($dsth>$height) {
				//если вылезла по высоте - подгоняем по высоте
			    $k = $height/$srch;
				$dstw = round($srcw*$k);
				$dsth = $height;		
			}		    
			
		} else {			
			
			$k = $width/$srcw;		
					
			$dstw = $width;
			$dsth = round($srch*$k);								
			
			if ($dsth<$height) {
				
			    $k = $height/$srch;
				$dstw = $width;
				$dsth = $height;		
				
				$winw = round($width/$k);				
				$srcx = round(($srcw-$winw)/2);
					
			} else {
				$dsth = $height;
				
				$winh = round($height/$k);
				$srcy = round(($srch-$winh)/2);			
			}		
				
		}
	
		if($match_size){
			$outim = imagecreatetruecolor($width, $height);
			if(count($bg_color) >= 3){
				$color = imagecolorallocate($outim, $bg_color[0], $bg_color[1], $bg_color[2]);
				imagefilledrectangle($outim, 0, 0, $width, $height, $color);
			}

			$neww = $width;
			$newh = $height;
		}
		else{
			$outim = imageCreateTrueColor($dstw,$dsth);
			$neww = $dstw;
			$newh = $dsth;
		}

		imagecopyresampled($outim, $srcim, ($neww - $dstw)/2, ($newh - $dsth)/2, $srcx, $srcy, $dstw, $dsth, $winw, $winh);
	
		$p = strrpos($destination, '.');
		$ext = substr($destination, $p + 1);
		
		switch($ext){
		default:
		case 'jpg':
		case 'jpeg':
			imagejpeg($outim, $destination);			
			break;
		case 'png':
			imagepng($outim, $destination);
			break;
		case 'gif':
			if(function_exists('imagegif'))
				imagegif($outim, $destination);
			break;
		}
		
		return true;	
	}
}

?>