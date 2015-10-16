<?php
//====================================================
//		FileName:index.php
//		Summary: ϵͳ��ҳ����
//		
//====================================================

require_once("IBinit.php");

//��ʼ��
$cat = new category($db);
$art = new article($db);
$tpl = new SmartTemplate("index.htm");

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
//ȡ����߷��������
for($i=0; $i<3; $i++)
{
	if(empty($navList[$i]))	//���಻���ڣ�����ѭ��
	{
		break;
	}
	//ȡ������˵�8����������
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
						   "title"	  => "�÷�������ʱû������");

		$mainBox['catList'][$i]['artList'] = $noArticle;
	}

}
if(isset($mainBox))
{
	$tpl->assign($mainBox);
}

//ȡ���ұ߷��������
for($i=0; $i<3; $i++)
{
	if(empty($navList[$i]))	//���಻���ڣ�����ѭ��
	{
		break;
	}
	//ȡ������˵�8����������
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
						   "title"	  => "�÷�������ʱû������");

		$mainBox['rightCatList'][$i]['rightArtList'] = $noArticle;
	}

}
if(isset($mainBox))
{
	$tpl->assign($mainBox);
}

//���ִ��ʱ��
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());

$tpl->output();
?>