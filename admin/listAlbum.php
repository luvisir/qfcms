<?php
//====================================================
//		FileName:listAlbum.php
//		Summary: 相册列表管理程序
//		
//====================================================

require_once("login.php");
require_once("../IBinit.php");

$album = new category($db, "album");
$tpl = new SmartTemplate("admin/listAlbum.htm");
//启用缓存
//$tpl->use_cache();

//条件数组
$condition = array();

//处理action
if(checkAction("listChild"))
{
	$condition['absPath'] = $_GET['absPath'];
}
$tmpList = $album->listCat($condition);
//解析树时按相册解析
$albumTree = $album->parseTree("album");
if(!empty($_GET['absPath']))
{
	$album->getAllParent($_GET['absPath']);
	$navigator  = $album->makeNavigator();
	$currentAlbum = $album->getNode($_GET['absPath']);
	$navigator .= $currentAlbum['catTitle'];
}
else
{
	$navigator = $tmpList[0]['catTitle'];
}
$tpl->assign("title", "相册管理");
$tpl->assign("navigator", $navigator);

if($albumTree) 
{
	$tpl->assign("albumList", $albumTree);
	$tpl->assign("noChildClass", "not-display");
}
else
{
	$tpl->assign("noChildClass", "light-row");
}
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());
$tpl->output();
?>
