<?php
//====================================================
//		FileName:listArticle.php
//		Summary: �����б���ʾ����
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$cat	 = new category($db, "cat");
$article = new article($db);
$tpl	 = new SmartTemplate("admin/listArticle.htm");

//�����ѯ����
if(!empty($_GET['catPath'])) 
{
	$condition['catPath'] = $_GET['catPath'];
	$defaultCat = $cat->getCatID($_GET['catPath']);
	$cat->getAllParent($_GET['catPath']);
	$admin = true;
	$navigator  = $cat->makeNavigator($admin);
	$currentCat = $cat->getNode($_GET['catPath']);
	$navigator .= $currentCat['catTitle'];
}
else
{
	$defaultCat = null;
	$home		= $cat->getHome();
	$navigator = $home['catTitle'];
}
if(!empty($_GET['page']) && intval($_GET['page']) > 0)
{
	$condition['page'] = $_GET['page'];
}
else
{
	$condition['page'] = 1;
}
$condition['rows'] = ARTICLE_PAGE_SIZE;
//ȡ�����з����б�
$cat->getTree();
$attrArray['class'] = "text-box";
$attrArray['onchange'] = "location.href='listArticle.php?catPath='+this.value";
$catPath = $cat->buildSelect("catPath", $defaultCat, $attrArray);

$artList = $article->listArticle($condition);

$tpl->assign("navigator", $navigator);
$tpl->assign("selectCat", $catPath);
//��������б�

if(!empty($artList))
{
	$artNumber = count($artList);
	//�鿴�Ƿ����
	for($i=0; $i<$artNumber; $i++)
	{
		if($artList[$i]['audit'] == 0) //δ���
		{
			$artList[$i]['auditAction'] = "auditArticle";
			$artList[$i]['auditText']	= "���";
		}
		else
		{
			$artList[$i]['auditAction'] = "lockArticle";
			$artList[$i]['auditText']	= "����";
		}
		$artList[$i]['title'] = cnString($artList[$i]['title'],40);
	}
	$tpl->assign("artList", $artList);
	$tpl->assign("noChildClass", "not-display");
}
else
{
	$tpl->assign("noChildClass", "light-row");
}

//�����ҳ��Ϣ
$pageParam['recordCount']	= $article->totalArticle($condition);
$pageParam['pageCount']		= ceil($article->totalArticle($condition) / $condition['rows']);
$pageParam['pageSize']		= $condition['rows'];
$pageParam['currentPage']	= $condition['page'];
$tpl->assign("pageParam", makePage($pageParam));
//���ִ��ʱ��
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());
$tpl->output();
?>
