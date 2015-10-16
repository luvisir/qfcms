<?php
//====================================================
//		FileName:index.php
//		Summary: 系统首页程序
//		
//====================================================

require_once("IBinit.php");

//初始化
$cat = new category($db);
$art = new article($db);
$tpl = new SmartTemplate("index.htm");

//输出系统名称，样式表及日期
$tpl->assign("appName", APP_NAME);
$tpl->assign("stylePath", STYLE_PATH . APP_STYLE);

$week = array("星期天","星期一","星期二","星期三","星期四","星期五","星期六");
$weekday = $week[date("w")];
$tpl->assign("today", date("Y年m月d日 ") . $weekday);

//输出导航条
$navList = $cat->getChild($cat->homeAbsPath);
//取出前七个
$navList = array_slice($navList, 0 ,7);
$tpl->assign("navList", $navList);

//输出分类文章
//取出左边分类的文章
for($i=0; $i<3; $i++)
{
	if(empty($navList[$i]))	//分类不存在，跳出循环
	{
		break;
	}
	//取出已审核的8条最新新闻
	$condition['audit']		= 1;
	$condition['page']		= 1;
	$condition['rows']		= 8;
	$condition['catPath']	= $navList[$i]['absPath'];
	$artList = $art->listArticle($condition);
	$mainBox['catList'][$i]['stylePath'] = STYLE_PATH . APP_STYLE;
	$mainBox['catList'][$i]['catTitle'] = $navList[$i]['catTitle'];
	$mainBox['catList'][$i]['catImage'] = $navList[$i]['catImage'];
	$mainBox['catList'][$i]['absPath'] = $navList[$i]['absPath'];
	if(!empty($artList))
	{
		$artNumber = count($artList);
		for($j=0; $j<$artNumber; $j++)
		{
			$artList[$j]['title'] = cnString($artList[$j]['title'],40);
		}
		$mainBox['catList'][$i]['artList'] = $artList;
	}
	else
	{
		$noArticle = array("linkPath" => "#",
						   "title"	  => "该分类下暂时没有文章");

		$mainBox['catList'][$i]['artList'] = $noArticle;
	}

}
if(isset($mainBox))
{
	$tpl->assign($mainBox);
}

//取出右边分类的文章
for($i=0; $i<3; $i++)
{
	if(empty($navList[$i]))	//分类不存在，跳出循环
	{
		break;
	}
	//取出已审核的8条最新新闻
	$condition['audit']		= 1;
	$condition['page']		= 1;
	$condition['rows']		= 6;
	//print_r($navList);
	//$condition['catPath']	= $navList[$i+3]['absPath'];
	$condition['catPath']	= $navList[$i]['absPath'];
	$artList = $art->listArticle($condition);

	$mainBox['rightCatList'][$i]['stylePath'] = STYLE_PATH . APP_STYLE;
	$mainBox['rightCatList'][$i]['catTitle'] = $navList[$i]['catTitle'];
	$mainBox['rightCatList'][$i]['absPath'] = $navList[$i]['absPath'];
	if(!empty($artList))
	{
		$artNumber = count($artList);
		for($j=0; $j<$artNumber; $j++)
		{
			$artList[$j]['title'] = cnString($artList[$j]['title'],30);
		}
		$mainBox['rightCatList'][$i]['rightArtList'] = $artList;
	}
	else
	{
		$noArticle = array("linkPath" => "#",
						   "title"	  => "该分类下暂时没有文章");

		$mainBox['rightCatList'][$i]['rightArtList'] = $noArticle;
	}

}
if(isset($mainBox))
{
	$tpl->assign($mainBox);
}

//输出执行时间
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());

$tpl->output();
?>