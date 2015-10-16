<?php
//====================================================
//		FileName:parseArticle.php
//		Summary: �������ݽ�������(�������ɾ�̬ҳ��)
//		
//====================================================

require_once("../IBinit.php");

//��ʼ��
$Pcat = new category($db);
$Part = new article($db);


/* ���� parseArticle ($parseArt, $styleName)
** ���� �������³���
** ���� $parseArt ���µ���Ҫ��Ϣ����
** ���� $styleName ���µķ��
*/
function parseArticle($parseArt, $styleName)
{

	global $db, $timer, $Pcat, $Part;
	$timer->start();
	$db->queryTimes = 0;
	$Ptpl = new SmartTemplate("parseArticle.htm");
	//���ϵͳ����,·��
	$Ptpl->assign("appName", APP_NAME);
	$Ptpl->assign("appPath", APP_PATH);
	$Ptpl->assign("stylePath", STYLE_PATH . $styleName);
	//����ܵ�����
	$navList = $Pcat->getChild($Pcat->homeAbsPath);
	//ȡ��ǰ�߸�
	$navList = array_slice($navList, 0 ,7);
	$navNumber= count($navList);
	for($i=0; $i<$navNumber; $i++)
	{
		$navList[$i]['appPath']= APP_PATH;
	}
	$Ptpl->assign("navList", $navList);
	$Ptpl->assign($parseArt);
	//���������������Ŀ�����͵���
	$Pcat->getAllParent($parseArt['catPath']);
	$smallNav  = $Pcat->makeNavigator();
	$currentCat = $Pcat->getNode($parseArt['catPath']);
	$smallNav .= $currentCat['catTitle'];
	$Ptpl->assign("smallNav", $smallNav);

	//����������ڵ���Ŀ����
	$siblingCat = $Pcat->getSibling($parseArt['catPath']);
	$PcatNumber= count($siblingCat);
	for($i=0; $i<$PcatNumber; $i++)
	{
		$siblingCat[$i]['appPath']= APP_PATH;
	}
	$Ptpl->assign("siblingCat", $siblingCat);

	//���������������Ŀ���Ƽ�����
	$goodArtList = $Part->getRecommend($parseArt['catPath'], 8);
	$Ptpl->assign("goodArtList", $goodArtList);

	//��������µ��������
	$relLink = $Part->getRelLink($parseArt['keyword'], 8);
	$Ptpl->assign("relLink", $relLink);

	//���ִ��ʱ��
	$Ptpl->assign("queryTime", $db->getQueryTimes());
	$Ptpl->assign("executeTime", $timer->getExecuteTime());
	return $Ptpl->result();
}

?>