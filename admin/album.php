<?php
//====================================================
//		FileName:album.php
//		Summary: 相册管理程序
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$cat = new category($db, "album");

//两个用来保存错误或成功信息的数组
$errorList	 = array();
$successList = array();

if(!empty($_GET['action']))	//负责显示表单
{
	switch($_GET['action'])
	{
		case 'addAlbum':		//相册添加

			//取出所有相册列表
			$cat->getTree();
			$attrArray['class'] = "text-box";
			//$attrArray['disabled'] = "true";
			$parentNode = $cat->buildSelect("parentNode", null, $attrArray);
			$tpl = new SmartTemplate("admin/album.htm");
			$varList = array(
								"title"			=> "相册添加",
								"parentNode"	=> $parentNode,
								"action"		=> "addAlbum",
								"absPath"		=> "",
								"buttonValue"	=> "添 加",
								"catTitle"		=> "",
								"description"	=> ""
							);
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());
			$tpl->output();
			break;

		case 'editAlbum':		//相册修改

			//取得当前相册的信息
			$currentCat = $cat->getNode($_GET['absPath']);
			$parentID	= $cat->getParent($_GET['absPath']);
			//取出所有相册列表
			$cat->getTree();
			$attrArray['class'] = "text-box";
			$attrArray['disabled'] = "true";
			$parentNode = $cat->buildSelect("parentNode", $parentID, $attrArray);

			$tpl = new SmartTemplate("admin/album.htm");
			$varList = array(
								"title"			=> "相册修改",
								"parentNode"	=> $parentNode,
								"action"		=> "editAlbum",
								"absPath"		=> $currentCat['absPath'],
								"buttonValue"	=> "修 改",
								"catTitle"		=> $currentCat['catTitle'],
								"description"	=> $currentCat['description']
							);
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();
			break;			

		case 'deleteAlbum':	//相册删除

			if($res = $cat->remove($_GET['absPath']))
			{
				
				$pic = new picture($db);
				//删除实际的文件
				$picList = $pic->getPicCat($_GET['absPath']);
				$GDImage = new GDImage(CMS_UPLOAD_PATH, GALLERY_REAL_PATH, GALLERY_PATH);
				$GDImage->removeImage($picList);

				//删除数据库记录
				$delPicNum = $pic->deletePicByCat($_GET['absPath']);
			}
			if ($res == -1) 
				$errorList[] = array("message" => "不能删除根相册.");
			elseif($res == 0)
				$errorList[] = array("message" => "删除失败,请重试.");
			else
			{
				$successList[] = array("message" => "相册删除成功.");
				$successList[] = array("message" => "$res 个相册被删除.");
				$successList[] = array("message" => "$delPicNum 张图片被删除.");
			}
			showMessage();
			break;

		default:
			$param["message"] = "参数错误,请重试.";
			forward("error.php", $param);			
			break;
	}
}
elseif(!empty($_POST['action']))	//负责表单提交后的数据处理
{
	switch($_POST['action'])
	{
		case 'addAlbum':	//处理添加相册的操作
	
			//验证表单
			validateForm();

			//无错误,添加相册
			$newPath = $cat->add($_POST['parentNode'], $_POST['catTitle'], $_POST['description'], "");
			if($newPath) 
			{
				$successList[] = array("message" => "相册添加成功");
			}
			else
			{
				$errorList[] = array("message" => "相册添加失败");
			}
			//处理显示信息
			showMessage();
			break;

		case 'editAlbum':	//处理修改相册的操作

			//验证表单
			validateForm();
			//修改相册信息
			$res2 = $cat->setNode($_POST['absPath'], $_POST['catTitle'], $_POST['description'], "");
			if($res2 != -1)
			{
				$successList[] = array("message" => "相册修改成功.");
			}
			else
				$errorList[] = array("message" => "相册修改失败,请重试.");

			//显示信息
			showMessage();

			break;

		default:

			$param["message"] = "参数错误,请重试.";
			forward("error.php", $param);			
			break;
	}
}
else
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
	if(!validate::required($_POST['catTitle']))
	{
		$errorList[] = array("message" => "相册名称不能为空.");
	}
	if(validate::match($_POST['catTitle'], "|[\\\/\'\"]|"))
	{
		$errorList[] = array("message" => "相册名称含有非法字符, 不能包含\\ / ' \"等字符.");
	}
	if(!validate::checkLength($_POST['description'], 200))
	{
		$errorList[] = array("message" => "相册描述不能超过100个汉字.");
	}
	if(!empty($errorList))	//处理错误
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}	
}

?>
