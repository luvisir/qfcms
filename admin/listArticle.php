<?php
//====================================================
//		FileName:listArticle.php
//		Summary: 新闻列表显示程序
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$cat	 = new category($db, "cat");
$article = new article($db);
$tpl	 = new SmartTemplate("admin/listArticle.htm");

//构造查询条件
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
//取出所有分类列表
$cat->getTree();
$attrArray['class'] = "text-box";
$attrArray['onchange'] = "location.href='listArticle.php?catPath='+this.value";
$catPath = $cat->buildSelect("catPath", $defaultCat, $attrArray);

$artList = $article->listArticle($condition);

$tpl->assign("navigator", $navigator);
$tpl->assign("selectCat", $catPath);
//输出文章列表

if(!empty($artList))
{
	$artNumber = count($artList);
	//查看是否审核
	for($i=0; $i<$artNumber; $i++)
	{
		if($artList[$i]['audit'] == 0) //未审核
		{
			$artList[$i]['auditAction'] = "auditArticle";
			$artList[$i]['auditText']	= "审核";
		}
		else
		{
			$artList[$i]['auditAction'] = "lockArticle";
			$artList[$i]['auditText']	= "锁定";
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

//输出分页信息
$pageParam['recordCount']	= $article->totalArticle($condition);
$pageParam['pageCount']		= ceil($article->totalArticle($condition) / $condition['rows']);
$pageParam['pageSize']		= $condition['rows'];
$pageParam['currentPage']	= $condition['page'];
$tpl->assign("pageParam", makePage($pageParam));
//输出执行时间
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());
$tpl->output();
?>
