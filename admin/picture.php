<?php
//====================================================
//		FileName:picture.php
//		Summary: 图片处理程序
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$album	 = new category($db, "album");
$pic	 = new picture($db);
$GDImage = new GDImage(CMS_UPLOAD_PATH, GALLERY_REAL_PATH, GALLERY_PATH);

$errorList = $successList = array();

if(!empty($_GET['action']))	//处理GET方式提交的action
{
	switch($_GET['action'])
	{
		case 'addPic':	//显示添加图片的表单
			//取出所有相册列表
			$album->getTree();
			$attrArray['class'] = "text-box";
			//$attrArray['disabled'] = "true";
			$albumPath = $album->buildSelect("catPath", null, $attrArray);
			
			$tpl = new SmartTemplate("admin/picture.htm");

			$tpl->assign("catPath", $albumPath);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();

			break;

		case 'editPic':	//显示编辑图片的表单
			
			$editPic = $pic->getPic($_GET['id']);
			
			//取出所有相册列表
			$album->getTree();
			$attrArray['class'] = "text-box";
			//$attrArray['disabled'] = "true";
			$albumPath = $album->buildSelect("catPath", null, $attrArray);
			
			$tpl = new SmartTemplate("admin/editPic.htm");
			$varList = array("catPath"		=> $albumPath,
							 "picTitle"		=> $editPic['picTitle'],
							 "description"	=> $editPic['description'],
							 "picID"		=> $editPic['id']
							);
			//取得缩略图或水印图信息

			if($editPic['hasThumb'])
			{
				$varList['picPath'] = array_pop($GDImage->getThumb($editPic['picName']));
			}
			elseif($editPic['hasMark'])
			{
				$varList['picPath'] = array_pop($GDImage->getMark($editPic['picName']));
			}
			else
			{
				$varList['picPath'] = GALLERY_PATH . $editPic['picName'];
			}
			//
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();			break;
		case 'deletePic':	//删除图片操作

			//删除单一图片或图片
			$delID = !empty($_GET['id']) ? $_GET['id'] : $_GET['idList'];
			if(empty($delID))
			{
				$errorList[] = array("message" => "没有选择要删除的图片.");
				showMessage();
			}		
			$picList = $pic->getPicName($delID);

			$GDImage->removeImage($picList);

			$res = $pic->deletePic($delID);

			if($res != -1) 
			{
				$successList[] = array("message" => "图片删除成功.");
				$successList[] = array("message" => "$res 个图片被删除.");
			}
			else
			{
				$errorList[] = array("message" => "图片删除失败,请重试.");
			}	
			
			showMessage();
			break;
		
		default:
			$param["message"] = "参数错误,请重试.";
			forward("error.php", $param);
			break;
	}
}
elseif(!empty($_POST['action']))	//处理POST方式提交的action
{
	switch($_POST['action'])
	{
		case 'addPic':	//添加图片的操作
			
			//验证数据
			validateForm();

			$_POST['picName'] = $GDImage->uploadImage("uploadPic");
			
			//添加到数据库
			if($pic->addPic($_POST))	//添加成功
			{
				//处理图片
				$GDImage->maxWidth  = $pictureSize['maxWidth'];
				$GDImage->maxHeight = $pictureSize['maxHeight'];
				$GDImage->toFile = true;
				if(!empty($_POST['hasThumb'])) //生成缩略图
				{
					$GDImage->makeThumb($_POST['picName'], $thumbSize['width'], $thumbSize['height']);
				}
				if(!empty($_POST['hasMark']))	//加水印
				{
					$GDImage->waterMark($_POST['picName'], $waterText);
					@unlink(CMS_UPLOAD_PATH . $_POST['picName']);
				}
				else
				{
					if(!empty($_POST['noGD']))
					{
						@copy(CMS_UPLOAD_PATH . $_POST['picName'], GALLERY_REAL_PATH . $_POST['picName']);
						@unlink(CMS_UPLOAD_PATH . $_POST['picName']);
					}
					else
					{
						$GDImage->moveToGallery($_POST['picName']); 
						@unlink(CMS_UPLOAD_PATH . $_POST['picName']);
					}
				}
				
				$successList[] = array("message" => "图片添加成功.");
			}
			else
			{
				$errorList[] = array("message" => "添加图片到数据库失败");
			}

			showMessage();

			break;

		case 'editPic':	//编辑图片的操作
			//验证数据
			if(!validate::required($_POST['picTitle']))
			{
				$errorList[] = array("message" => "图片标题不能为空.");
			}
			if(!validate::checkLength($_POST['description'], 200))
			{
				$errorList[] = array("message" => "图片描述不能超过200个字符.");
			}			

			if(!empty($errorList))	//处理错误
			{
				$param["msgList"] = serialize($errorList);
				forward("message.php", $param);
				exit();
			}
			//处理数据
			if($pic->editPic($_POST))
			{
				$successList[] = array("message" => "图片信息修改成功.");
			}
			else
			{
				$errorList[] = array("message" => "修改失败，请重试.");
			}

			showMessage();
			break;

		default:
			$param["message"] = "参数错误,请重试.";
			forward("error.php", $param);
			break;
	}
}
else	//没有提交
{
	$param["message"] = "参数错误,请重试.";
	forward("error.php", $param);
}

//===========一些封装的函数============

//验证本页表单的函数
function validateForm()
{
	//载入全局变量 

	global $errorList, $successList;
	if(!validate::required($_POST['picTitle']))
	{
		$errorList[] = array("message" => "图片标题不能为空.");
	}
	if(!validate::checkLength($_POST['description'], 200))
	{
		$errorList[] = array("message" => "图片描述不能超过200个字符.");
	}

	if(($_FILES['uploadPic']['error'] == 4))
	{
		$errorList[] = array("message" => "您还没有选择图片.");
	}
	if(($_FILES['uploadPic']['error'] == 1))
	{
		$errorList[] = array("message" => "图片大小超过了系统允许范围.");
	}
	if(!checkImgType($_FILES['uploadPic']['tmp_name']))
	{
		$errorList[] = array("message" => "图片类型系统不能识别或者不是图片.");
	}
	if(!empty($errorList))	//处理错误
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}	
}
/* 函数 checkImgType($fileName)
** 功能 检查图片类型是否为允许的类型
** 参数 $fileName 文件名
*/
function checkImgType($fileName)
{

	$data = getimagesize($fileName);

	if($data && $data[2] < 4) //1为GIF 2为JPG 3为PNG,
	{
		return true;;
	}
	else
		return false;
}
?>
