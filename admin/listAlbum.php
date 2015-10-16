<?php
//====================================================
//		FileName:listAlbum.php
//		Summary: ����б�������
//		
//====================================================

require_once("login.php");
require_once("../IBinit.php");

$album = new category($db, "album");
$tpl = new SmartTemplate("admin/listAlbum.htm");
//���û���
//$tpl->use_cache();

//��������
$condition = array();

//����action
if(checkAction("listChild"))
{
	$condition['absPath'] = $_GET['absPath'];
}
$tmpList = $album->listCat($condition);
//������ʱ��������
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
$tpl->assign("title", "������");
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
