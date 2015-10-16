<?php
//====================================================
//		FileName:listCat.php
//		Summary: ��������б�������
//		
//====================================================

require_once("login.php");
require_once("../IBinit.php");

$cat = new category($db, "cat");
$tpl = new SmartTemplate("admin/listCat.htm");
//���û���
//$tpl->use_cache();

//��������
$condition = array();

//����action
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
$tpl->assign("title", "����������");
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
