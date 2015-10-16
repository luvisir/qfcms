<?php
//====================================================
//		FileName:listPic.php
//		Summary: 图片列表显示程序
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
//取出所有相册列表
$cat->getTree();
$attrArray['class'] = "text-box";
$attrArray['onchange'] = "location.href='listPic.php?catPath='+this.value";
$catPath = $cat->buildSelect("catPath", $defaultCat, $attrArray);

//列表方式查看图片
if(PICTURE_SHOW_TYPE == "list")
{
	//输出图片列表
	$tpl	 = new SmartTemplate("admin/listPic.htm");
	$tpl->assign("navigator", $navigator);
	$tpl->assign("selectCat", $catPath);

	$picList = $picture->listPic($condition);
	if(!empty($picList))
	{
		$picNumber = count($picList);
		//标识的中文显示
		$textArray = array("无", "有");
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
//输出执行时间
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());
$tpl->output();
?>
