<?php
//====================================================
//		FileName:parseArticle.php
//		Summary: 文章内容解析程序(用于生成静态页面)
//		
//====================================================

require_once("../IBinit.php");

//初始化
$Pcat = new category($db);
$Part = new article($db);


/* 函数 parseArticle ($parseArt, $styleName)
** 功能 解析文章程序
** 参数 $parseArt 文章的主要信息数组
** 参数 $styleName 文章的风格
*/
function parseArticle($parseArt, $styleName)
{

	global $db, $timer, $Pcat, $Part;
	$timer->start();
	$db->queryTimes = 0;
	$Ptpl = new SmartTemplate("parseArticle.htm");
	//输出系统名称,路径
	$Ptpl->assign("appName", APP_NAME);
	$Ptpl->assign("appPath", APP_PATH);
	$Ptpl->assign("stylePath", STYLE_PATH . $styleName);
	//输出总导航条
	$navList = $Pcat->getChild($Pcat->homeAbsPath);
	//取出前七个
	$navList = array_slice($navList, 0 ,7);
	$navNumber= count($navList);
	for($i=0; $i<$navNumber; $i++)
	{
		$navList[$i]['appPath']= APP_PATH;
	}
	$Ptpl->assign("navList", $navList);
	$Ptpl->assign($parseArt);
	//输出该文章所在栏目的条型导航
	$Pcat->getAllParent($parseArt['catPath']);
	$smallNav  = $Pcat->makeNavigator();
	$currentCat = $Pcat->getNode($parseArt['catPath']);
	$smallNav .= $currentCat['catTitle'];
	$Ptpl->assign("smallNav", $smallNav);

	//输出文章所在的栏目导航
	$siblingCat = $Pcat->getSibling($parseArt['catPath']);
	$PcatNumber= count($siblingCat);
	for($i=0; $i<$PcatNumber; $i++)
	{
		$siblingCat[$i]['appPath']= APP_PATH;
	}
	$Ptpl->assign("siblingCat", $siblingCat);

	//输出该文章所在栏目的推荐文章
	$goodArtList = $Part->getRecommend($parseArt['catPath'], 8);
	$Ptpl->assign("goodArtList", $goodArtList);

	//输出该文章的相关链接
	$relLink = $Part->getRelLink($parseArt['keyword'], 8);
	$Ptpl->assign("relLink", $relLink);

	//输出执行时间
	$Ptpl->assign("queryTime", $db->getQueryTimes());
	$Ptpl->assign("executeTime", $timer->getExecuteTime());
	return $Ptpl->result();
}

?>