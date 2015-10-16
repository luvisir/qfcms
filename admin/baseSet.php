<?php
//====================================================
//		FileName:baseSet.php
//		Summary: 系统的基本设置程序
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$errorList = $successList = array();

if(checkAction("baseSet"))	//表单提交,处理数据
{
	//检验表单数据
	validateForm();

	//无错误,写入配置文件
	$configArray['APP_STYLE']		  = $_POST['appStyle'];
	$configArray['ARTICLE_PAGE_SIZE'] = $_POST['articlePageSize'];
	$configArray['PICTURE_PAGE_SIZE'] = $_POST['picturePageSize'];
	$configArray['PICTURE_SHOW_TYPE'] = $_POST['pictureShowType'];

	$configArray["waterText"] = "array('{$_POST['waterText1']}', '{$_POST['waterText2']}');";
	$configArray["pictureSize"] = "array('maxWidth' => {$_POST['maxWidth']}, 'maxHeight' => {$_POST['maxHeight']});";
	$configArray["thumbSize"] = "array('width' => {$_POST['width']}, 'height' => {$_POST['height']});";

	if(writeConfig("config.php", $configArray))
	{
		$successList[] = array("message" => "基本设置修改成功.");	
	}
	else
	{
		$errorList[] = array("message" => "基本设置修改失败,请重试.");
	}

	showMessage();
}
else
{
	$tpl = new SmartTemplate("admin/baseSet.htm");
	
	//生成风格下拉菜单
	$selectStyle = "<select name=\"appStyle\" class=\"text-box\">";
	foreach($styleList as $key=>$val)
	{
		$selectStyle .= "<option value=\"$key\">$val</option>";
	}
	$selectStyle .= "</select>";

	$varList = array("selectStyle"		=> $selectStyle,
					 "articlePageSize"	=> ARTICLE_PAGE_SIZE,
					 "picturePageSize"	=> PICTURE_PAGE_SIZE,	
					 "pictureShowType"	=> PICTURE_SHOW_TYPE,
					 "waterText1"		=> $waterText[0],
					 "waterText2"		=> $waterText[1],
					 "width"			=> $thumbSize["width"],
					 "height"			=> $thumbSize["height"],
					 "maxWidth"			=> $pictureSize["maxWidth"],
					 "maxHeight"		=> $pictureSize["maxHeight"]
					);

	$tpl->assign($varList);
	$tpl->assign("queryTime", $db->getQueryTimes());
	$tpl->assign("executeTime", $timer->getExecuteTime());

	$tpl->output();
}

/* 函数 writeConfig($fileName, $configArray)
** 功能 写入配置文件
** 参数 $fileName 配置文件名称
** 参数 $configArray 配置信息数组
*/
function writeConfig($fileName, $configArray)
{
	if(empty($configArray)) 
	{
		return false;
	}
	//读取数据
	if(function_exists("file_get_contents"))
	{
		$configText = file_get_contents(CMS_ROOT . $fileName);
	}
	else
	{
		$configText = join("", file(CMS_ROOT . $fileName));
	}
	//循环修改配置
	foreach($configArray as $key => $val) 
	{
		$regV = "|$key\s*=\s*.+;|";	//变量配置参数
		$regC = "|define\(\"$key\".+;|";	//常量配置参数
		

		if(preg_match($regV, $configText))
		{
			$configText = preg_replace($regV, $key." = ".$val, $configText);
		}
		else
		{
			if(!validate::isNumber($val))	//字符串加上引号
			{
				$val = "\"$val\"";
			}
			$configText = preg_replace($regC, "define(\"$key\", $val);", $configText);
		}
	}
	//exit();
	if($fp = fopen(CMS_ROOT . $fileName, "w"))
	{
		fwrite($fp, $configText);
		fclose($fp);
		return true;
	}
	else
		return false;
}

//验证本页表单的函数
function validateForm()
{
	//载入全局变量 
	global $errorList, $successList;
	if(!validate::isNumber($_POST['articlePageSize']))
	{
		$errorList[] = array("message" => "文章每页显示数目不是数字.");
	}
	if(!validate::isNumber($_POST['picturePageSize']))
	{
		$errorList[] = array("message" => "图片每页显示数目不是数字.");
	}
	if($_POST['articlePageSize'] <= 0)
	{
		$errorList[] = array("message" => "文章每页显示数目必须大于零.");
	}
	if($_POST['picturePageSize'] <= 0)
	{
		$errorList[] = array("message" => "图片每页显示数目必须大于零.");
	}
	if(!validate::required($_POST['waterText1']) || !validate::required($_POST['waterText2']))
	{
		$errorList[] = array("message" => "水印文字不能为空.");
	}
	if(!validate::isNumber($_POST['width']) || !validate::isNumber($_POST['height']))
	{
		$errorList[] = array("message" => "缩略图尺寸不是数字.");
	}
	if(!validate::isNumber($_POST['maxWidth']) || !validate::isNumber($_POST['maxHeight']))
	{
		$errorList[] = array("message" => "图片上传后的最大尺寸不是数字.");
	}	
	if($_POST['width'] <= 0 || $_POST['height'] <= 0)
	{
		$errorList[] = array("message" => "图缩略图尺寸必须大于零.");
	}
	if($_POST['maxWidth'] <= 0 || $_POST['maxHeight'] <= 0)
	{
		$errorList[] = array("message" => "图片上传后的最大尺寸必须大于零.");
	}
	if(!empty($errorList))	//处理错误
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}	
}
?>
