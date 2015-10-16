<?php
//====================================================
//		FileName:search.php
//		Summary: �������������ʾ����
//====================================================

require_once("IBinit.php");

//��ʼ��
$cat = new category($db);
$art = new article($db);
$tpl = new SmartTemplate("search.htm");

//��ֹ������Ϣ
if(!checkAction("search"))
{
	exit("��������,������.");
}

//���ϵͳ���ƣ���ʽ������
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


//�����������
$condition[$_GET['type']] = $_GET['keyword'];
$condition['page']	  = !empty($_GET['page']) ? intval($_GET['page']) : 1;
$condition['rows']	  = 20;
$artList = $art->listArticle($condition);
$noArticle = empty($artList) ? "article-box" : "not-display";
$tpl->assign("noArticle", $noArticle);
$tpl->assign("artList", $artList);

//�������Ŀ����
$tpl->assign("childCat", $navList);

//�������Ŀ���Ƽ�����
$goodArtList = $art->getRecommend($cat->homeAbsPath, 8);
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
