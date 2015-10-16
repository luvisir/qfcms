<?php
//====================================================
//		FileName:main.php
//		Summary: 管理系统入口
//		
//====================================================

require_once("login.php");
require_once("../IBinit.php");

if(!empty($_GET['action']))
{
	switch($_GET['action'])
	{
		//系统设置部分
		case 'sysInfo':
			forward("sysInfo.php");
			break;
		case 'setPwd':
			forward("setPwd.php");
			break;
		case 'baseSet':
			forward("baseSet.php");
			break;
		case 'logout':
			forward("logout.php");
			break;
		
		//文章管理部分
		case 'addArticle':
			$param['action'] = "addArticle";
			forward("article.php", $param);
			break;
		case 'listArticle':
			forward("listArticle.php");
			break;
		case 'addCat':
			$param['action'] = "addCat";
			forward("articleCat.php", $param);
			break;
		case 'listCat':
			forward("listCat.php");
			break;
		case 'updateArticle':
			forward("updateArticle.php");
			break;
		//相册管理部分
		case 'addPic':
			$param['action'] = "addPic";
			forward("picture.php", $param);
			break;
		case 'listPic':
			forward("listPic.php");
			break;
		case 'addAlbum':
			$param['action'] = "addAlbum";
			forward("album.php", $param);
			break;
		case 'listAlbum':
			forward("listAlbum.php");
			break;
		//帐户管理部分
		case 'addUser':
			$param['action'] = "addUser";
			forward("user.php", $param);
			break;
		case 'listUser':
			forward("listUser.php");
			break;
		default:
			$param["message"] = "参数错误,请重试.";
			forward("error.php", $param);			
			break;
	}
}
else
{
	$tpl = new SmartTemplate("admin/main.htm");
	$tpl->assign("adminName", $_SESSION['uname']);
	$tpl->output();
}
?>

