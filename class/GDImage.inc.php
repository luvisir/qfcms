<?php
//====================================================
//		FileName:GDImage.inc.php
//		Summary: ͼƬ�������
//		
//====================================================

class GDImage 
{
	var $sourcePath;				//ͼƬ�洢·��
	var $galleryPath;				//ͼƬ����ͼ�洢·��
	var $displayPath;				//��ʾͼƬʱʹ�õ�·��
	var $toFile	= false;			//�Ƿ������ļ�
	var $fontName;					//ʹ�õ�TTF��������
	var $maxWidth  = 500;			//ͼƬ�����
	var $maxHeight = 600;			//ͼƬ���߶�
	var $useTimeAsFileName = true;	//�Ƿ�ʹ��ʱ����Ϊ�ϴ�����ļ���

	//==========================================
	// ����: GDImage($sourcePath	,$galleryPath, $displayPath)
	// ����: constructor
	// ����: $sourcePath		ͼƬԴ·��(�������һ��"/")	
	// ����: $galleryPath	����ͼƬ��·��
	// ����: $displayPath	��ʾͼƬʱʹ�õ�·��
	//==========================================
	function GDImage($sourcePath, $galleryPath, $displayPath)
	{
		$this->sourcePath	= $sourcePath;
		$this->galleryPath	= $galleryPath;
		$this->displayPath	= $displayPath;
		$this->fontName		= $galleryPath . "04B_08__.TTF";
	}

	//==========================================
	// ����: makeThumb($sourFile,$width=128,$height=128) 
	// ����: ��������ͼ(����������)
	// ����: $sourFile ͼƬԴ�ļ�
	// ����: $width ��������ͼ�Ŀ��
	// ����: $height ��������ͼ�ĸ߶�
	// ����: 0 ʧ�� �ɹ�ʱ�������ɵ�ͼƬ·��
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
	// ����: waterMark($sourFile, $text)
	// ����: ��ͼƬ��ˮӡ
	// ����: $sourFile ͼƬ�ļ���
	// ����: $text �ı�����(���������ַ���)
	// ����: 1 �ɹ� �ɹ�ʱ�������ɵ�ͼƬ·��
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
	// ����: moveToGallery($sourFile)
	// ����: ��ͼƬ��ˮӡ
	// ����: $sourFile ͼƬ�ļ���
	// ����: 1 �ɹ� �ɹ�ʱ�������ɵ�ͼƬ·��
	//==========================================
	function moveToGallery($sourFile) 
	{
		$sourFile = $this->sourcePath . $sourFile;
		$imageInfo	= $this->getInfo($sourFile);
		//ͼƬ������
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
	// ����: getThumb($file)
	// ����: ȡ��ָ��ͼƬ������ͼ����
	// ����: $file �ļ���
	// ����: 0 ͼƬ������ �ɹ���������Ԫ�ص�����
	//       realPathԪ�ر�ʾ����ͼ��ʵ������·��
	//       netPath��ʾ������ʾͼƬ������·��
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
	// ����: getMark($file)
	// ����: ȡ��ָ��ͼƬ��ˮӡͼ����
	// ����: $file �ļ���
	// ����: 0 ͼƬ������ �ɹ���������Ԫ�ص�����
	//       realPathԪ�ر�ʾˮӡͼ��ʵ������·��
	//       netPath��ʾ������ʾͼƬ������·��
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
	// ����: removeImage($file)
	// ����: ɾ��ͼƬ
	// ����: $file �ļ����ƻ�����
	// ����: ɾ�����ļ�����
	//==========================================	
	function removeImage($file)
	{
		if(is_array($file))//�����飬ѭ��ɾ��
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
	// ����: getInfo($file)
	// ����: ����ͼ����Ϣ
	// ����: $file �ļ�����
	// ����: ͼƬ��Ϣ����
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
	// ����: uploadImage($file)
	// ����: �����ϴ�ͼƬ
	// ����: $file �ϴ���file��nameֵ
	// ����: $newName �ϴ����ͼƬ����
	//==========================================
	function uploadImage($fileName)
	{
		$img = !empty($_FILES[$fileName]) ? $_FILES[$fileName] : null;
		if($img == null)
			return 0;
		if ($this->useTimeAsFileName) 
		{
			$now	  = date("Ymdhis");
			$p		  = strrpos($img['name'], "."); //�õ����һ��.��λ��
			$ext	  = substr($img['name'], $p+1); //�õ�ͼƬ��չ��
			$newName  = $now . "." . $ext; //Ҫ����ͼƬ��ȫ��
		}
		else
		{
			$newName = $img['name'];
		}
		$imgPath = $this->sourcePath . $newName;
		if (move_uploaded_file($img['tmp_name'], $imgPath)) //�ϴ��ɹ�
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