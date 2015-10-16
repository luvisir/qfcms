<?php
//====================================================
//		FileName:listImage.php
//		Summary: �������ʱѡ��ͼƬ�õ�ͼƬ�б���ʾ����
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
	$navigator  = $cat->makeNavigator();
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
$condition['rows'] = 16;
//ȡ����������б�
$cat->getTree();
$attrArray['class'] = "text-box";
$attrArray['onchange'] = "self.location.href='listImage.php?catPath='+this.value";
$catPath = $cat->buildSelect("catPath", $defaultCat, $attrArray);
$tpl	 = new SmartTemplate("admin/listImage.htm");
$tpl->assign("navigator", $navigator);
$tpl->assign("selectCat", $catPath);

//ȡ��ͼƬ�б�
$picList = $picture->listPicThumb($condition);
$GDImage = new GDImage(CMS_UPLOAD_PATH, GALLERY_REAL_PATH, GALLERY_PATH);
if(!empty($picList))
{
	//��ȡ�������ݽ��д���
	$picNumber = count($picList);
	for($i=0; $i<$picNumber; $i++)
	{
		if($picList[$i]['hasMark'])
		{
			$picList[$i]['imagePath'] = array_pop($GDImage->getMark($picList[$i]['picName']));
		}
		else
		{
			$picList[$i]['imagePath'] = GALLERY_PATH . $picList[$i]['picName'];
		}

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
		
		//ѡ��ͼƬ��ѡ������ͼ�����Ǵ�ͼ
		if(checkAction("selectThumb"))
		{
			$picList[$i]['imagePath'] = $picList[$i]['picName'];
			$picList[$i]['action'] = "selectThumb";
		}
		else
		{
			$picList[$i]['action'] = "selectPic";
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

$tpl->output();
?>
