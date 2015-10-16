<?php
//====================================================
//		FileName:articleCat.php
//		Summary: 新闻分类管理程序
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$cat = new category($db, "cat");

//两个用来保存错误或成功信息的数组
$errorList	 = array();
$successList = array();

if(!empty($_GET['action']))	//负责显示表单
{
	switch($_GET['action'])
	{
		case 'addCat':		//分类添加

			//取出所有分类列表
			$cat->getTree();
			$attrArray['class'] = "text-box";
			//$attrArray['disabled'] = "true";
			$parentNode = $cat->buildSelect("parentNode", null, $attrArray);
			$tpl = new SmartTemplate("admin/articleCat.htm");
			$varList = array(
								"title"			=> "文章分类添加",
								"parentNode"	=> $parentNode,
								"action"		=> "addCat",
								"absPath"		=> "",
								"buttonValue"	=> "添 加",
								"catTitle"		=> "",
								"description"	=> "",
								"imgName"		=> GALLERY_PATH . "no_image.gif"
							);
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();
			break;

		case 'editCat':		//分类修改

			//取得当前分类的信息
			$currentCat = $cat->getNode($_GET['absPath']);
			$parentID	= $cat->getParent($_GET['absPath']);
			//取出所有分类列表
			$cat->getTree();
			$attrArray['class'] = "text-box";
			$attrArray['disabled'] = "disabled";
			$parentNode = $cat->buildSelect("parentNode", $parentID, $attrArray);

			$tpl = new SmartTemplate("admin/articleCat.htm");
			$varList = array(
								"title"			=> "文章分类修改",
								"parentNode"	=> $parentNode,
								"action"		=> "editCat",
								"absPath"		=> $currentCat['absPath'],
								"buttonValue"	=> "修 改",
								"catTitle"		=> $currentCat['catTitle'],
								"description"	=> $currentCat['description'],
								"imgName"		=> $currentCat['catImage']
							);
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();
			break;			

		case 'deleteCat':	//分类删除

			$res = $cat->remove($_GET['absPath']);
			if ($res == -1) 
				$errorList[] = array("message" => "不能删除根分类.");
			elseif($res == 0)
				$errorList[] = array("message" => "删除失败,请重试.");
			else
			{

				//删除所有新闻
				$art = new article($db);
				$delArtNum = $art->delArticleByCat($_GET['absPath']);
				//删除物理文件及目录
				fileSystem::removeDir(getRealPath($_GET['absPath']));

				$successList[] = array("message" => "分类删除成功.");
				$successList[] = array("message" => "$res 条记录被删除");
				$successList[] = array("message" => "$delArtNum 篇文章被删除");
				
			}
			
			showMessage();
			break;

		default:
			$param["message"] = "参数错误,请重试.";
			forward("error.php", $param);			
			break;
	}
}
elseif(!empty($_POST['action']))	//负责表单提交后的数据处理
{
	switch($_POST['action'])
	{
		case 'addCat':	//处理添加分类的操作
	
			//验证表单
			validateForm();

			//无错误,添加分类
			$newAbsPath = $cat->add($_POST['parentNode'], $_POST['catTitle'], $_POST['description'], $_POST['catImage']);

			//建立目录
			makeCatdir($newAbsPath);

			//处理显示信息
			showMessage();
			break;

		case 'editCat':	//处理修改分类的操作

			//验证表单
			validateForm();

			/*/移动结点
			$res1 = $cat->moveTo($_POST['absPath'], $_POST['parentNode']);
			if($res1 == -1) //移动出错
			{
				$errorList[] = array("message" => "不能将分类添加到自己或子分类中.");
			}
			else
			{
			*/

			//修改分类信息
			$res2 = $cat->setNode($_POST['absPath'], $_POST['catTitle'], $_POST['description'], $_POST['catImage']);
			if($res2 != -1)
			{
				$successList[] = array("message" => "分类修改成功.");
				//$successList[] = array("message" => "$res1 个记录被移动.");
				//$successList[] = array("message" => "$res2 个记录被修改.");
			}
			else
				$errorList[] = array("message" => "分类修改失败,请重试.");

			//显示信息
			showMessage();

			break;

		default:

			$param["message"] = "参数错误,请重试.";
			forward("error.php", $param);			
			break;
	}
}
else
{
	$param["message"] = "参数错误,请重试.";
	forward("error.php", $param);	
}

//===========一些封装的函数============

//验证本页表单的函数
function validateForm()
{
	//载入全局变量 
	global $errorList, $successList;
	if(!validate::required($_POST['catTitle']))
	{
		$errorList[] = array("message" => "分类标题不能为空.");
	}
	if(validate::match($_POST['catTitle'], "|[\\\/\'\"]|"))
	{
		$errorList[] = array("message" => "分类标题含有非法字符, 不能包含\\ / ' \"等字符.");
	}
	if(!validate::checkLength($_POST['description'], 200))
	{
		$errorList[] = array("message" => "分类描述不能超过200个字符.");
	}
	if(!empty($errorList))	//处理错误
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}	
}
/* 函数 makeCatdir($absPath)
** 功能 根据结点路径信息建立目录
** 参数 $absPath 结点绝对路径
*/
function makeCatDir($absPath)
{
	//载入全局变量 
	global $successList, $errorList, $cat;

	if(!empty($absPath))
	{
		//原路径为0,1,5,14,50 去掉0,1,代表的根目录,生成5,14,50
		$path = substr($absPath, 4);	
		//将5,14,50转换成5/14/50
		$path = str_replace(",", "/", $path);
	
		if(@mkdir(ARTICLE_REAL_PATH . $path))
		{
			$successList[] = array("message" => "文章分类添加成功.");
		}
		else
		{
			$cat->remove($absPath);
			$errorList[] = array("message" => "建立分类目录失败,请重试.");
		}
	}
	else
	{
		$errorList[] = array("message" => "文章分类添加失败,请重试.");
	}
}
/* 函数 getRealPath($absPath)
** 功能 根据结点路径信息返回物理绝对路径
** 参数 $absPath 结点路径信息
*/
function getRealPath($absPath)
{
	if(!empty($absPath))
	{
		//原路径为0,1,5,14,50 去掉0,1,代表的根目录,生成5,14,50
		$path = substr($absPath, 4);	
		//将5,14,50转换成5/14/50
		$path = str_replace(",", "/", $path);

		return ARTICLE_REAL_PATH . $path;	
	}
	return false;
}
?>
