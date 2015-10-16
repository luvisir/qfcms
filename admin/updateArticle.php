<?php
//====================================================
//		FileName:updateArticle.php
//		Summary: �����������³���
//		
//====================================================

//��ֹ����ʱ�����������ű���ʱ.
set_time_limit(0);

require_once("login.php");
require_once("../IBinit.php");
require_once("parseArticle.php");	//���������õĺ���

//��ʼ��
$cat = new category($db);
$art = new article($db);
$tpl = new SmartTemplate("admin/updateArticle.htm");

if(checkAction("updateArticle"))	//���ύ,��������
{
	$styleName = $_POST['style'];
	//ȡ��Ҫ���µ�����
	$artList = $art->listAllArticle($_POST['catPath']);
	foreach($artList as $article)
	{
		//�õ�HTML�ַ���
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
			$errorList[] = array("message" => "�ļ�".$fileName."��ʧ�ܣ�����ʧ��.");
			break;
		}

	}

	$successList[] = array("message" => "���¸��³ɹ�");
	$successList[] = array("message" => count($artList)."ƪ���±�����.");
	showMessage();

}
else	//��ʾ��
{
	//ȡ�����з����б�
	$cat->getTree();
	$attrArray['class'] = "text-box";
	$catPath = $cat->buildSelect("catPath", null, $attrArray);
	$tpl->assign("catPath", $catPath);
	//���ɷ��������
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
