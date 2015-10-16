<?php
//====================================================
//		FileName:showImage.php
//		Summary: 显示图片信息程序
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");
if(!empty($_GET['id']))
{
	
	$pic = new picture($db);
	$GDImage = new GDImage(CMS_UPLOAD_PATH, GALLERY_REAL_PATH, GALLERY_PATH);

	$tpl = new SmartTemplate("admin/showImage.htm");
	$textArray = array("无", "有");
	$showPic = $pic->getPic($_GET['id']);
	//得到图片原始信息
	if($showPic['hasMark'])
	{
		//取得水印图的信息
		$picPath = $GDImage->getMark($showPic['picName']);
		$picName = $picPath['netPath'];
		$info	 = $GDImage->getInfo($picPath['realPath']);
		$picByte  = sizeCount(filesize($picPath['realPath']));
	}
	else
	{
		//取得原始图的信息
		$picName = GALLERY_PATH . $showPic['picName'];
		$info	 = $GDImage->getInfo(GALLERY_REAL_PATH . $showPic['picName']);
		$picByte = sizeCount(filesize(GALLERY_REAL_PATH . $showPic['picName']));
	}
	$varList = array("picSize"		=> $info['width'] . " × " .$info['height'] . " px",
					 "picByte"		=> sizeCount($info['size']),
					 "picName"		=> $picName,
					 "picTitle"		=> $showPic['picTitle'],
					 "catTitle"		=> $showPic['catTitle'],
					 "hasThumb"		=> $textArray[$showPic['hasThumb']],
					 "hasMark"		=> $textArray[$showPic['hasMark']],
					 "description"	=> $showPic['description']
					 );
	$tpl->assign($varList);
	$tpl->output();
}
else
{
	$param["message"] = "参数错误,请重试.";
	forward("error.php", $param);
}
?>
