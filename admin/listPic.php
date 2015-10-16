<?php
//====================================================
//		FileName:listPic.php
//		Summary: ͼƬ�б���ʾ����
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$cat	 = new category($db, "album");
$picture = new picture($db);


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
$condition['rows'] = PICTURE_PAGE_SIZE;
//ȡ����������б�
$cat->getTree();
$attrArray['class'] = "text-box";
$attrArray['onchange'] = "location.href='listPic.php?catPath='+this.value";
$catPath = $cat->buildSelect("catPath", $defaultCat, $attrArray);

//�б�ʽ�鿴ͼƬ
if(PICTURE_SHOW_TYPE == "list")
{
	//���ͼƬ�б�
	$tpl	 = new SmartTemplate("admin/listPic.htm");
	$tpl->assign("navigator", $navigator);
	$tpl->assign("selectCat", $catPath);

	$picList = $picture->listPic($condition);
	if(!empty($picList))
	{
		$picNumber = count($picList);
		//��ʶ��������ʾ
		$textArray = array("��", "��");
		for($i=0; $i<$picNumber; $i++)
		{
			$picList[$i]['hasThumb'] = $textArray[$picList[$i]['hasThumb']];
			$picList[$i]['hasMark']	 = $textArray[$picList[$i]['hasMark']];
		}
	}
}

if(PICTURE_SHOW_TYPE == "thumb")
{
	$tpl	 = new SmartTemplate("admin/listPicThumb.htm");
	$tpl->assign("navigator", $navigator);
	$tpl->assign("selectCat", $catPath);

	$picList = $picture->listPicThumb($condition);
	$GDImage = new GDImage(CMS_UPLOAD_PATH, GALLERY_REAL_PATH, GALLERY_PATH);
	if(!empty($picList))
	{
		$picNumber = count($picList);
		for($i=0; $i<$picNumber; $i++)
		{
			if($picList[$i]['hasThumb'])	//������ͼ
			{
				$picList[$i]['picName'] = array_pop($GDImage->getThumb($picList[$i]['picName']));
			}
			elseif($picList[$i]['hasMark'])	//��ˮӡͼ
			{
				$picList[$i]['picName'] = array_pop($GDImage->getMark($picList[$i]['picName']));
			}
			else
			{
				$picList[$i]['picName'] = GALLERY_PATH .$picList[$i]['picName'];
			}			
		}
	}
}
//���ͼƬ�б���Ϣ
if(!empty($picList))
{
	$tpl->assign("picList", $picList);
	$tpl->assign("noChildClass", "not-display");
}
else
{
	$tpl->assign("noChildClass", "light-row");
}
//�����ҳ��Ϣ
$pageParam['recordCount']	= $picture->totalPic($condition);
$pageParam['pageCount']		= ceil($picture->totalPic($condition) / $condition['rows']);
$pageParam['pageSize']		= $condition['rows'];
$pageParam['currentPage']	= $condition['page'];
$tpl->assign("pageParam", makePage($pageParam));
//���ִ��ʱ��
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());
$tpl->output();
?>
