<?php
//====================================================
//		FileName:updateArticle.php
//		Summary: 文章批量更新程序
//		
//====================================================

//防止更新时间过长而引起脚本超时.
set_time_limit(0);

require_once("login.php");
require_once("../IBinit.php");
require_once("parseArticle.php");	//解析文章用的函数

//初始化
$cat = new category($db);
$art = new article($db);
$tpl = new SmartTemplate("admin/updateArticle.htm");

if(checkAction("updateArticle"))	//表单提交,处理数据
{
	$styleName = $_POST['style'];
	//取出要更新的文章
	$artList = $art->listAllArticle($_POST['catPath']);
	foreach($artList as $article)
	{
		//得到HTML字符串
		$htmlStr = parseArticle($article, $styleName);

		$fileName = str_replace(ARTICLE_PATH, ARTICLE_REAL_PATH, $article['linkPath']);

		$fp = @fopen($fileName, "w");
		if($fp)
		{
			@fwrite($fp, $htmlStr);
			@fclose($fp);
		}
		else
		{
			$errorList[] = array("message" => "文件".$fileName."打开失败，更新失败.");
			break;
		}

	}

	$successList[] = array("message" => "文章更新成功");
	$successList[] = array("message" => count($artList)."篇文章被更新.");
	showMessage();

}
else	//显示表单
{
	//取出所有分类列表
	$cat->getTree();
	$attrArray['class'] = "text-box";
	$catPath = $cat->buildSelect("catPath", null, $attrArray);
	$tpl->assign("catPath", $catPath);
	//生成风格下拉框
	$template = "<select name='style' class='text-box'>";
	foreach($styleList as $key=>$val)
	{
		$template .= "<option value='$key'>$val</option>";
	}
	$template .= "</select>";

	$tpl->assign("template", $template);
	$tpl->assign("queryTime", $db->getQueryTimes());
	$tpl->assign("executeTime", $timer->getExecuteTime());
	$tpl->output();
}

?>
