<?php
//====================================================
//		FileName:GDImage.inc.php
//		Summary: 图片处理程序
//		
//====================================================

class GDImage 
{
	var $sourcePath;				//图片存储路径
	var $galleryPath;				//图片缩略图存储路径
	var $displayPath;				//显示图片时使用的路径
	var $toFile	= false;			//是否生成文件
	var $fontName;					//使用的TTF字体名称
	var $maxWidth  = 500;			//图片最大宽度
	var $maxHeight = 600;			//图片最大高度
	var $useTimeAsFileName = true;	//是否使用时间做为上传后的文件名

	//==========================================
	// 函数: GDImage($sourcePath	,$galleryPath, $displayPath)
	// 功能: constructor
	// 参数: $sourcePath		图片源路径(包括最后一个"/")	
	// 参数: $galleryPath	生成图片的路径
	// 参数: $displayPath	显示图片时使用的路径
	//==========================================
	function GDImage($sourcePath, $galleryPath, $displayPath)
	{
		$this->sourcePath	= $sourcePath;
		$this->galleryPath	= $galleryPath;
		$this->displayPath	= $displayPath;
		$this->fontName		= $galleryPath . "04B_08__.TTF";
	}

	//==========================================
	// 函数: makeThumb($sourFile,$width=128,$height=128) 
	// 功能: 生成缩略图(输出到浏览器)
	// 参数: $sourFile 图片源文件
	// 参数: $width 生成缩略图的宽度
	// 参数: $height 生成缩略图的高度
	// 返回: 0 失败 成功时返回生成的图片路径
	//==========================================
	function makeThumb($sourFile,$width=128,$height=128) 
	{
		$sourFile = $this->sourcePath . $sourFile;
		$imageInfo	= $this->getInfo($sourFile);
		$newName	= substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) . "_thumb.jpg";
		switch ($imageInfo["type"])
		{
			case 1:	//gif
				$img = imagecreatefromgif($sourFile);
				break;
			case 2:	//jpg
				$img = imagecreatefromjpeg($sourFile);
				break;
			case 3:	//png
				$img = imagecreatefrompng($sourFile);
				break;
			default:
				return 0;
				break;
		}
		if (!$img) 
			return 0;

		$width  = ($width > $imageInfo["width"]) ? $imageInfo["width"] : $width;
		$height = ($height > $imageInfo["height"]) ? $imageInfo["height"] : $height;
		$srcW	= $imageInfo["width"];
		$srcH	= $imageInfo["height"]; 
		if ($srcW * $width > $srcH * $height)
			$height = round($srcH * $width / $srcW);
		else
			$width = round($srcW * $height / $srcH);
		//*
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{
			$new = imagecreatetruecolor($width, $height);
			ImageCopyResampled($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		else
		{
			$new = imagecreate($width, $height);
			ImageCopyResized($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		//*/
        if ($this->toFile)
		{
			if (file_exists($this->galleryPath . $newName))
				unlink($this->galleryPath . $newName);
			ImageJPEG($new, $this->galleryPath . $newName);
			ImageDestroy($new);
			ImageDestroy($img);
			return $this->galleryPath . $newName;
		}
		else
		{
			ImageJPEG($new);
			ImageDestroy($new);
			ImageDestroy($img);
		}


	}
	//==========================================
	// 函数: waterMark($sourFile, $text)
	// 功能: 给图片加水印
	// 参数: $sourFile 图片文件名
	// 参数: $text 文本数组(包含二个字符串)
	// 返回: 1 成功 成功时返回生成的图片路径
	//==========================================
	function waterMark($sourFile, $text) 
	{
		$sourFile = $this->sourcePath . $sourFile;
		$imageInfo	= $this->getInfo($sourFile);
		$newName	= substr($imageInfo["name"], 0, strrpos($imageInfo["name"], ".")) . "_mark.jpg";
		switch ($imageInfo["type"])
		{
			case 1:	//gif
				$img = imagecreatefromgif($sourFile);
				break;
			case 2:	//jpg
				$img = imagecreatefromjpeg($sourFile);
				break;
			case 3:	//png
				$img = imagecreatefrompng($sourFile);
				break;
			default:
				return 0;
				break;
		}
		if (!$img) 
			return 0;

		$width  = ($this->maxWidth > $imageInfo["width"]) ? $imageInfo["width"] : $this->maxWidth;
		$height = ($this->maxHeight > $imageInfo["height"]) ? $imageInfo["height"] : $this->maxHeight;
		$srcW	= $imageInfo["width"];
		$srcH	= $imageInfo["height"]; 
		if ($srcW * $width > $srcH * $height)
			$height = round($srcH * $width / $srcW);
		else
			$width = round($srcW * $height / $srcH);
		//*
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{
			$new = imagecreatetruecolor($width, $height);
			ImageCopyResampled($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		else
		{
			$new = imagecreate($width, $height);
			ImageCopyResized($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		$white = imageColorAllocate($new, 255, 255, 255);
		$black = imageColorAllocate($new, 0, 0, 0);
		$alpha = imageColorAllocateAlpha($new, 230, 230, 230, 40);
		//$rectW = max(strlen($text[0]),strlen($text[1]))*7;
		ImageFilledRectangle($new, 0, $height-26, $width, $height, $alpha);
		ImageFilledRectangle($new, 13, $height-20, 15, $height-7, $black);
		ImageTTFText($new, 4.9, 0, 20, $height-14, $black, $this->fontName, $text[0]);
		ImageTTFText($new, 4.9, 0, 20, $height-6, $black, $this->fontName, $text[1]);
		//*/
        if ($this->toFile)
		{
			if (file_exists($this->galleryPath . $newName))
				unlink($this->galleryPath . $newName);
			ImageJPEG($new, $this->galleryPath . $newName);
			ImageDestroy($new);
			ImageDestroy($img);

			return $this->galleryPath . $newName;
		}
		else
		{
			ImageJPEG($new);
			ImageDestroy($new);
			ImageDestroy($img);
		}


	}
	//==========================================
	// 函数: moveToGallery($sourFile)
	// 功能: 给图片加水印
	// 参数: $sourFile 图片文件名
	// 返回: 1 成功 成功时返回生成的图片路径
	//==========================================
	function moveToGallery($sourFile) 
	{
		$sourFile = $this->sourcePath . $sourFile;
		$imageInfo	= $this->getInfo($sourFile);
		//图片新名称
		$newName	= $sourFile;
		switch ($imageInfo["type"])
		{
			case 1:	//gif
				$img = imagecreatefromgif($sourFile);
				break;
			case 2:	//jpg
				$img = imagecreatefromjpeg($sourFile);
				break;
			case 3:	//png
				$img = imagecreatefrompng($sourFile);
				break;
			default:
				return 0;
				break;
		}
		if (!$img) 
			return 0;

		$width  = ($this->maxWidth > $imageInfo["width"]) ? $imageInfo["width"] : $this->maxWidth;
		$height = ($this->maxHeight > $imageInfo["height"]) ? $imageInfo["height"] : $this->maxHeight;
		$srcW	= $imageInfo["width"];
		$srcH	= $imageInfo["height"]; 
		if ($srcW * $width > $srcH * $height)
			$height = round($srcH * $width / $srcW);
		else
			$width = round($srcW * $height / $srcH);
		//*
		if (function_exists("imagecreatetruecolor")) //GD2.0.1
		{
			$new = imagecreatetruecolor($width, $height);
			ImageCopyResampled($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}
		else
		{
			$new = imagecreate($width, $height);
			ImageCopyResized($new, $img, 0, 0, 0, 0, $width, $height, $imageInfo["width"], $imageInfo["height"]);
		}

		if (file_exists($this->galleryPath . $newName))
				unlink($this->galleryPath . $newName);

		ImageJPEG($new, $this->galleryPath . $newName);
		ImageDestroy($new);
        ImageDestroy($img);

		return $this->galleryPath . $newName;


	}
	//==========================================
	// 函数: getThumb($file)
	// 功能: 取得指定图片的缩略图名称
	// 参数: $file 文件名
	// 返回: 0 图片不存在 成功返回两个元素的数组
	//       realPath元素表示缩略图的实际物理路径
	//       netPath表示用于显示图片的网络路径
	//==========================================
	function getThumb($file) 
	{
		$thumbName	= substr($file, 0, strrpos($file, ".")) . "_thumb.jpg";
		$file = $this->galleryPath . $thumbName;
		if (!file_exists($file)) 
			return 0;
		$thumb['realPath'] = $this->galleryPath . $thumbName;
		$thumb['netPath']  = $this->displayPath . $thumbName;
		return $thumb;
	}
	//==========================================
	// 函数: getMark($file)
	// 功能: 取得指定图片的水印图名称
	// 参数: $file 文件名
	// 返回: 0 图片不存在 成功返回两个元素的数组
	//       realPath元素表示水印图的实际物理路径
	//       netPath表示用于显示图片的网络路径
	//==========================================
	function getMark($file) 
	{
		$markName	= substr($file, 0, strrpos($file, ".")) . "_mark.jpg";
		$file = $this->galleryPath . $markName;
		if (!file_exists($file)) 
			return 0;
		$mark['realPath'] = $this->galleryPath . $markName;
		$mark['netPath']  = $this->displayPath . $markName;
		return $mark;
	}

	//==========================================
	// 函数: removeImage($file)
	// 功能: 删除图片
	// 参数: $file 文件名称或数组
	// 返回: 删除的文件个数
	//==========================================	
	function removeImage($file)
	{
		if(is_array($file))//是数组，循环删除
		{
			foreach($file as $val)
			{
				$thumbName	= $this->galleryPath . substr($val, 0, strrpos($val, ".")) . "_thumb.jpg";
				$markName  = $this->galleryPath . substr($val, 0, strrpos($val, ".")) . "_mark.jpg";
				$oriName   = $this->galleryPath . $val;	
	
				if(file_exists($thumbName))
				{
					@unlink($thumbName);
				}
				if(file_exists($markName))
				{
					@unlink($markName);
				}
				if(file_exists($oriName))
				{
					@unlink($oriName);
				}
			}
		}
		else
		{
			$thumbName	= $this->galleryPath . substr($file, 0, strrpos($file, ".")) . "_thumb.jpg";
			$markName  = $this->galleryPath . substr($file, 0, strrpos($file, ".")) . "_mark.jpg";
			$oriName   = $this->galleryPath . $file;	
	
			if(file_exists($thumbName))
			{
				@unlink($thumbName);
			}
			if(file_exists($markName))
			{
				@unlink($markName);
			}
			if(file_exists($oriName))
			{
				@unlink($oriName);
			}
		}
	}

	//==========================================
	// 函数: getInfo($file)
	// 功能: 返回图像信息
	// 参数: $file 文件名称
	// 返回: 图片信息数组
	//==========================================
	function getInfo($file) 
	{
		$data	= getimagesize($file);
		$imageInfo["width"]	= $data[0];
		$imageInfo["height"]= $data[1];
		$imageInfo["type"]	= $data[2];
		$imageInfo["name"]	= basename($file);
		$imageInfo["size"]  = filesize($file);
		return $imageInfo;		
	}

	//==========================================
	// 函数: uploadImage($file)
	// 功能: 处理上传图片
	// 参数: $file 上传表单file的name值
	// 返回: $newName 上传后的图片名称
	//==========================================
	function uploadImage($fileName)
	{
		$img = !empty($_FILES[$fileName]) ? $_FILES[$fileName] : null;
		if($img == null)
			return 0;
		if ($this->useTimeAsFileName) 
		{
			$now	  = date("Ymdhis");
			$p		  = strrpos($img['name'], "."); //得到最后一个.的位置
			$ext	  = substr($img['name'], $p+1); //得到图片扩展名
			$newName  = $now . "." . $ext; //要保存图片的全名
		}
		else
		{
			$newName = $img['name'];
		}
		$imgPath = $this->sourcePath . $newName;
		if (move_uploaded_file($img['tmp_name'], $imgPath)) //上传成功
		{
			return $newName;
		}
		else 
		{
			return 0;
		}
	}

}

?>