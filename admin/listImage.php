<?php
//====================================================
//		FileName:listImage.php
//		Summary: 添加文章时选择图片用的图片列表显示程序
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$cat	 = new category($db, "album");
$picture = new picture($db);


//构造查询条件
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
//取出所有相册列表
$cat->getTree();
$attrArray['class'] = "text-box";
$attrArray['onchange'] = "self.location.href='listImage.php?catPath='+this.value";
$catPath = $cat->buildSelect("catPath", $defaultCat, $attrArray);
$tpl	 = new SmartTemplate("admin/listImage.htm");
$tpl->assign("navigator", $navigator);
$tpl->assign("selectCat", $catPath);

//取出图片列表
$picList = $picture->listPicThumb($condition);
$GDImage = new GDImage(CMS_UPLOAD_PATH, GALLERY_REAL_PATH, GALLERY_PATH);
if(!empty($picList))
{
	//对取出的数据进行处理
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

		if($picList[$i]['hasThumb'])	//有缩略图
		{
			$picList[$i]['picName'] = array_pop($GDImage->getThumb($picList[$i]['picName']));
		}
		elseif($picList[$i]['hasMark'])	//有水印图
		{
			$picList[$i]['picName'] = array_pop($GDImage->getMark($picList[$i]['picName']));
		}
		else
		{
			$picList[$i]['picName'] = GALLERY_PATH .$picList[$i]['picName'];
		}			
		
		//选择图片是选择缩略图而不是大图
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
//输出图片列表信息
if(!empty($picList))
{
	$tpl->assign("picList", $picList);
	$tpl->assign("noChildClass", "not-display");
}
else
{
	$tpl->assign("noChildClass", "light-row");
}
//输出分页信息
$pageParam['recordCount']	= $picture->totalPic($condition);
$pageParam['pageCount']		= ceil($picture->totalPic($condition) / $condition['rows']);
$pageParam['pageSize']		= $condition['rows'];
$pageParam['currentPage']	= $condition['page'];
$tpl->assign("pageParam", makePage($pageParam));

$tpl->output();
?>
