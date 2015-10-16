<?php
//====================================================
//		FileName:article.php
//		Summary: 新闻管理程序(添加,删除,修改)
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");
require_once("parseArticle.php");	//解析文章用的函数

$cat	 = new category($db, "cat");
$article = new article($db);

//两个用来保存错误或成功信息的数组
$errorList	 = array();
$successList = array();

if(!empty($_GET['action']))	//负责显示表单
{
	switch($_GET['action'])
	{
		case 'addArticle':		//文章添加

			//取出所有分类列表
			$cat->getTree();
			$attrArray['class'] = "text-box";
			//$attrArray['disabled'] = "true";
			$catPath = $cat->buildSelect("catPath", null, $attrArray);
			$tpl = new SmartTemplate("admin/article.htm");
			$varList = array(
								"title"			=> "文章添加",
								"catPath"		=> $catPath,
								"artTitle"		=> "",
								"postTime"		=> date("Y-m-d"),
								"summary"		=> "",
								"author"		=> "",
								"comeFrom"		=> "",
								"keyword"		=> "",
								"isImg"			=> 0,
								"imgShow"		=> "not-display",
								"imgName"		=> GALLERY_PATH . "no_image.gif",
								"recommend"		=> 0,
								"artContent"	=> "",
								"action"		=> "addArticle",
								"artID"			=> "",
								"linkPath"		=> "",
								"oldPath"		=> "",
								"buttonValue"	=> "添 加"
							);
			//解析模板
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();
			break;

		case 'editArticle':		//文章修改
			//取出文章信息
			$editArt = $article->getArticle($_GET['id']);
			//取出所有分类列表
			$cat->getTree();
			$catSelect = $cat->getCatID($editArt['catPath']);
			$attrArray['class'] = "text-box";
			
			$catPath = $cat->buildSelect("catPath", $catSelect , $attrArray);
			$tpl = new SmartTemplate("admin/article.htm");
			$imgShow = empty($editArt['isImg']) ? "not-display" : "light-row";
			$varList = array(
								"title"			=> "文章修改",
								"catPath"		=> $catPath,
								"artTitle"		=> $editArt['title'],
								"postTime"		=> $editArt['postTime'],
								"summary"		=> $editArt['summary'],
								"author"		=> $editArt['author'],
								"comeFrom"		=> $editArt['comeFrom'],
								"keyword"		=> $editArt['keyword'],
								"isImg"			=> $editArt['isImg'],
								"imgShow"		=> $imgShow,
								"imgName"		=> $editArt['imgName'],
								"recommend"		=> $editArt['recommend'],
								"artContent"	=> transferStr($editArt['content']),
								"action"		=> "editArticle",
								"artID"			=> $editArt['id'],
								"linkPath"		=> $editArt['linkPath'],
								"oldPath"		=> $editArt['catPath'],
								"buttonValue"	=> "修 改"
							);
			//解析模板
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();
			break;			
		case 'auditArticle':
			$auditID = !empty($_GET['id']) ? $_GET['id'] : $_GET['idList'];
			if(empty($auditID))
			{
				$errorList[] = array("message" => "没有选择要审核的文章.");
				showMessage();
			}
			$res = $article->auditArticle($auditID);
			if($res != -1) 
			{
				$successList[] = array("message" => "文章审核成功.");
				$successList[] = array("message" => "$res 条文章被审核.");
			}
			else
			{
				$errorList[] = array("message" => "文章审核失败,请重试.");
			}

			showMessage();
			break;
		case 'lockArticle':
			$lockID = !empty($_GET['id']) ? $_GET['id'] : $_GET['idList'];
			if(empty($lockID))
			{
				$errorList[] = array("message" => "没有选择要锁定的文章.");
				showMessage();
			}
			$res = $article->lockArticle($lockID);
			if($res != -1) 
			{
				$successList[] = array("message" => "文章锁定成功.");
				$successList[] = array("message" => "$res 条文章被锁定.");
			}
			else
			{
				$errorList[] = array("message" => "文章锁定失败,请重试.");
			}

			showMessage();
			break;
		case 'deleteArticle':	//文章删除
			//删除单一文章或文章列表
			$delID = !empty($_GET['id']) ? $_GET['id'] : $_GET['idList'];
			if(empty($delID))
			{
				$errorList[] = array("message" => "没有选择要删除的文章.");
				showMessage();
			}		
			$pathList = $article->getPathByID($delID);
			//print_r($pathList);exit();
			
			//是数组循环删除
			if(is_array($pathList))
			{
				foreach($pathList as $val)
				{
					//将网络路径转换成物理路径再进行删除
					$linkPath = str_replace(ARTICLE_PATH, ARTICLE_REAL_PATH, $val['linkPath']);
					unlink($linkPath);
				}
			}
			else
			{
				//将网络路径转换成物理路径再进行删除
				$linkPath = str_replace(ARTICLE_PATH, ARTICLE_REAL_PATH, $pathList);
				unlink($linkPath);
			}
			$res = $article->delArticle($delID);

			if($res != -1) 
			{
				$successList[] = array("message" => "文章删除成功.");
				$successList[] = array("message" => "$res 条文章被删除.");
			}
			else
			{
				$errorList[] = array("message" => "文章删除失败,请重试.");
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
		case 'addArticle':	//处理添加文章的操作
	
			//验证表单
			validateForm();

			//生成静态页面
			$linkPath = HTMLPage($_POST['catPath']); 
			//将物理路径转换成网络路径
			$_POST['linkPath'] = str_replace(ARTICLE_REAL_PATH, ARTICLE_PATH, $linkPath);
			$_POST['audit'] = 0;
			$artID = $article->addArticle($_POST);
			if($artID)
			{
				$successList = array("message" => "文章添加成功.");
			}
			else
			{
				$errorList = array("message" => "文章添加失败.");
			}
			//处理显示信息
			showMessage();
			break;

		case 'editArticle':	//处理修改文章的操作

			//验证表单
			validateForm();
			
			//删除原来的页面
			$oldPage = str_replace(ARTICLE_PATH, ARTICLE_REAL_PATH, $_POST['linkPath']);
			@unlink($oldPage);

			//生成新的静态页面
			$linkPath = HTMLPage($_POST['catPath'], $oldPage); 
			//将物理路径转换成网络路径
			$_POST['linkPath'] = str_replace(ARTICLE_REAL_PATH, ARTICLE_PATH, $linkPath);
			$res = $article->editArticle($_POST);

			if($res)
			{
				$successList = array("message" => "文章修改成功.");
			}
			else
			{
				$errorList = array("message" => "文章修改失败.");
			}

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
	if(!validate::required($_POST['title']))
	{
		$errorList[] = array("message" => "文章标题不能为空.");
	}
	if(!validate::checkLength($_POST['title'], 50))
	{
		$errorList[] = array("message" => "文章标题不能超过50个字符.");
	}
	if(!validate::required($_POST['postTime']))
	{
		$errorList[] = array("message" => "发布时间不能为空.");
	}
	if(!validate::match($_POST['postTime'], "|^\d{4}-\d{2}-\d{2}$|"))
	{
		$errorList[] = array("message" => "发布时间格式不正确.");		
	}
	if(!validate::checkLength($_POST['summary'], 200))
	{
		$errorList[] = array("message" => "文章摘要不能超过200个字符.");
	}
	if(!validate::required($_POST['author']))
	{
		$errorList[] = array("message" => "文章作者不能为空.");
	}
	if(!validate::checkLength($_POST['author'], 30))
	{
		$errorList[] = array("message" => "文章作者不能超过30个字符.");
	}
	if(!validate::required($_POST['keyword']))
	{
		$errorList[] = array("message" => "关键字不能为空.");
	}
	if(!validate::checkLength($_POST['keyword'], 20))
	{
		$errorList[] = array("message" => "关键字不能超过20个字符.");
	}
	if(!validate::required($_POST['content']))
	{
		$errorList[] = array("message" => "文章内容不能为空.");
	}
	if(!empty($errorList))	//处理错误
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}	
}
/* 函数 HTMLPage($path, $oldPage)
** 功能 在指定目录生成静态页面
** 参数 $path 要生成文件的路径
** 参数 $oldPage 旧页面的物理路径
** 返回 生成的文件路径
*/
function HTMLPage($path, $oldPage=null)
{
	//载入全局变量 
	global $successList, $errorList, $db,$timer;

	if(!empty($path))
	{
		//原路径为0,1,5,14,50 去掉0,1,代表的根目录,生成5,14,50
		//不是根目录
		if(strlen($path) > 3) 
		{		
			$path = substr($path, 4);	
			//将5,14,50转换成5/14/50
			$path = str_replace(",", "/", $path);
			
			//判断本月目录是否存在,不存在则创建
			$dirName = date("Y-m");
			$path = $path . "/" . $dirName;			
		}
		else
		{
			$path = date("Y-m");;
		}
		//添加时使用物理路径
		if(!is_dir(ARTICLE_REAL_PATH . $path))
		{
			if(!@mkdir(ARTICLE_REAL_PATH . $path))
			{
				exit("建立日期目录失败.");
			}
		}
		//parseArticle.php需要一个文章信息的数组$parseArt;
		$parseArt = $_POST;
		$styleName= APP_STYLE;

		//得到HTML字符串
		$htmlStr = parseArticle($_POST, $styleName);

		//使用原来的文件名
		if($oldPage != null && $_POST['catPath'] == $_POST['oldPath'])
		{
			$fileName = $oldPage;
		}
		else
		{
			$prefix = date("Ymd-His");
			//文件名格式为20041027-122101.htm
			$fileName = ARTICLE_REAL_PATH . $path . "/" . $prefix . ".htm";
		}
		$fp = fopen($fileName, "w");
		fwrite($fp, $htmlStr);
		fclose($fp);

		return $fileName;
	}
	else
	{
		$errorList[] = array("message" => "生成页面时发生错误.");
	}
	if(!empty($errorList))	//处理错误
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
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

		return ARTICLE_PATH . $path;	
	}
	return false;
}
/* 函数 transferStr($str)
** 功能 对文章内容进行转换,以便保存在JS变量中
** 参数 $str 要转换的文章内容
*/
function transferStr($str)
{
	return str_replace("\r\n","",str_replace("/","\/",addslashes($str)));
}

?>
