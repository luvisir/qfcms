<?php
//====================================================
//		FileName:search.php
//		Summary: 文章搜索结果显示程序
//====================================================

require_once("IBinit.php");

//初始化
$cat = new category($db);
$art = new article($db);
$tpl = new SmartTemplate("search.htm");

//防止错误信息
if(!checkAction("search"))
{
	exit("参数错误,请重试.");
}

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
$condition[$_GET['type']] = $_GET['keyword'];
$condition['page']	  = !empty($_GET['page']) ? intval($_GET['page']) : 1;
$condition['rows']	  = 20;
$artList = $art->listArticle($condition);
$noArticle = empty($artList) ? "article-box" : "not-display";
$tpl->assign("noArticle", $noArticle);
$tpl->assign("artList", $artList);

//输出子栏目导航
$tpl->assign("childCat", $navList);

//输出该栏目的推荐文章
$goodArtList = $art->getRecommend($cat->homeAbsPath, 8);
$tpl->assign("goodArtList", $goodArtList);

//输出分页信息
$pageParam['recordCount']	= $art->totalArticle($condition);
$pageParam['pageCount']		= ceil($art->totalArticle($condition) / $condition['rows']);
$pageParam['pageSize']		= $condition['rows'];
$pageParam['currentPage']	= $condition['page'];
$tpl->assign("pageParam", makePage($pageParam));

//输出执行时间
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());
$tpl->output();
