<?php
//====================================================
//		FileName:listCat.php
//		Summary: 文章类别列表管理程序
//		
//====================================================

require_once("login.php");
require_once("../IBinit.php");

$cat = new category($db, "cat");
$tpl = new SmartTemplate("admin/listCat.htm");
//启用缓存
//$tpl->use_cache();

//条件数组
$condition = array();

//处理action
if(checkAction("listChild"))
{
	$condition['absPath'] = $_GET['absPath'];
}
$tmpList = $cat->listCat($condition);

$catTree = $cat->parseTree();
if(!empty($_GET['absPath']))
{
	$cat->getAllParent($_GET['absPath']);
	$navigator  = $cat->makeNavigator();
	$currentCat = $cat->getNode($_GET['absPath']);
	$navigator .= $currentCat['catTitle'];
}
else
{
	$navigator = $tmpList[0]['catTitle'];
}
$tpl->assign("title", "文章类别管理");
$tpl->assign("navigator", $navigator);

if($catTree) 
{
	$tpl->assign("catList", $catTree);
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
