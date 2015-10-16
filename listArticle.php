<?php
//====================================================
//		FileName:listArticle.php
//		Summary: ���·�����ʾ����
//====================================================

require_once("IBinit.php");

//��ʼ��
$cat = new category($db);
$art = new article($db);
$tpl = new SmartTemplate("listArticle_front.htm");

//��ֹ������Ϣ
if(empty($_GET['catPath']))
{
	exit("��������,������.");
}

//���ϵͳ���ƣ��������
$tpl->assign("appName", APP_NAME);
$tpl->assign("stylePath", STYLE_PATH . APP_STYLE);

$week = array("������","����һ","���ڶ�","������","������","������","������");
$weekday = $week[date("w")];
$tpl->assign("today", date("Y��m��d�� ") . $weekday);

//���������
$navList = $cat->getChild($cat->homeAbsPath);
//ȡ��ǰ�߸�
$navList = array_slice($navList, 0 ,7);
$tpl->assign("navList", $navList);

//�������Ŀ�ĵ���
$cat->getAllParent($_GET['catPath']);
$smallNav  = $cat->makeNavigator();
$currentCat = $cat->getNode($_GET['catPath']);
$smallNav .= $currentCat['catTitle'];
$tpl->assign("smallNav", $smallNav);

//�����������
$condition['audit']	  = 1;
$condition['catPath'] = $_GET['catPath'];
$condition['page']	  = !empty($_GET['page']) ? $_GET['page'] : 1;
$condition['rows']	  = 20;
$artList = $art->listArticle($condition);
$noArticle = empty($artList) ? "article-box" : "not-display";
$tpl->assign("noArticle", $noArticle);
$tpl->assign("artList", $artList);

//�����Ŀ������Ŀ
$childCat = $cat->getChild($_GET['catPath']);
$tpl->assign("childCat", $childCat);

//���������������Ŀ���Ƽ�����
$goodArtList = $art->getRecommend($_GET['catPath'], 8);
$tpl->assign("goodArtList", $goodArtList);

//�����ҳ��Ϣ
$pageParam['recordCount']	= $art->totalArticle($condition);
$pageParam['pageCount']		= ceil($art->totalArticle($condition) / $condition['rows']);
$pageParam['pageSize']		= $condition['rows'];
$pageParam['currentPage']	= $condition['page'];
$tpl->assign("pageParam", makePage($pageParam));

//���ִ��ʱ��
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());
$tpl->output();
