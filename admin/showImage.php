<?php
//====================================================
//		FileName:showImage.php
//		Summary: ��ʾͼƬ��Ϣ����
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");
if(!empty($_GET['id']))
{
	
	$pic = new picture($db);
	$GDImage = new GDImage(CMS_UPLOAD_PATH, GALLERY_REAL_PATH, GALLERY_PATH);

	$tpl = new SmartTemplate("admin/showImage.htm");
	$textArray = array("��", "��");
	$showPic = $pic->getPic($_GET['id']);
	//�õ�ͼƬԭʼ��Ϣ
	if($showPic['hasMark'])
	{
		//ȡ��ˮӡͼ����Ϣ
		$picPath = $GDImage->getMark($showPic['picName']);
		$picName = $picPath['netPath'];
		$info	 = $GDImage->getInfo($picPath['realPath']);
		$picByte  = sizeCount(filesize($picPath['realPath']));
	}
	else
	{
		//ȡ��ԭʼͼ����Ϣ
		$picName = GALLERY_PATH . $showPic['picName'];
		$info	 = $GDImage->getInfo(GALLERY_REAL_PATH . $showPic['picName']);
		$picByte = sizeCount(filesize(GALLERY_REAL_PATH . $showPic['picName']));
	}
	$varList = array("picSize"		=> $info['width'] . " �� " .$info['height'] . " px",
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
	$param["message"] = "��������,������.";
	forward("error.php", $param);
}
?>
