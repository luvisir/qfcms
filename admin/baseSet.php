<?php
//====================================================
//		FileName:baseSet.php
//		Summary: ϵͳ�Ļ������ó���
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$errorList = $successList = array();

if(checkAction("baseSet"))	//���ύ,��������
{
	//���������
	validateForm();

	//�޴���,д�������ļ�
	$configArray['APP_STYLE']		  = $_POST['appStyle'];
	$configArray['ARTICLE_PAGE_SIZE'] = $_POST['articlePageSize'];
	$configArray['PICTURE_PAGE_SIZE'] = $_POST['picturePageSize'];
	$configArray['PICTURE_SHOW_TYPE'] = $_POST['pictureShowType'];

	$configArray["waterText"] = "array('{$_POST['waterText1']}', '{$_POST['waterText2']}');";
	$configArray["pictureSize"] = "array('maxWidth' => {$_POST['maxWidth']}, 'maxHeight' => {$_POST['maxHeight']});";
	$configArray["thumbSize"] = "array('width' => {$_POST['width']}, 'height' => {$_POST['height']});";

	if(writeConfig("config.php", $configArray))
	{
		$successList[] = array("message" => "���������޸ĳɹ�.");	
	}
	else
	{
		$errorList[] = array("message" => "���������޸�ʧ��,������.");
	}

	showMessage();
}
else
{
	$tpl = new SmartTemplate("admin/baseSet.htm");
	
	//���ɷ�������˵�
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

/* ���� writeConfig($fileName, $configArray)
** ���� д�������ļ�
** ���� $fileName �����ļ�����
** ���� $configArray ������Ϣ����
*/
function writeConfig($fileName, $configArray)
{
	if(empty($configArray)) 
	{
		return false;
	}
	//��ȡ����
	if(function_exists("file_get_contents"))
	{
		$configText = file_get_contents(CMS_ROOT . $fileName);
	}
	else
	{
		$configText = join("", file(CMS_ROOT . $fileName));
	}
	//ѭ���޸�����
	foreach($configArray as $key => $val) 
	{
		$regV = "|$key\s*=\s*.+;|";	//�������ò���
		$regC = "|define\(\"$key\".+;|";	//�������ò���
		

		if(preg_match($regV, $configText))
		{
			$configText = preg_replace($regV, $key." = ".$val, $configText);
		}
		else
		{
			if(!validate::isNumber($val))	//�ַ�����������
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

//��֤��ҳ���ĺ���
function validateForm()
{
	//����ȫ�ֱ��� 
	global $errorList, $successList;
	if(!validate::isNumber($_POST['articlePageSize']))
	{
		$errorList[] = array("message" => "����ÿҳ��ʾ��Ŀ��������.");
	}
	if(!validate::isNumber($_POST['picturePageSize']))
	{
		$errorList[] = array("message" => "ͼƬÿҳ��ʾ��Ŀ��������.");
	}
	if($_POST['articlePageSize'] <= 0)
	{
		$errorList[] = array("message" => "����ÿҳ��ʾ��Ŀ���������.");
	}
	if($_POST['picturePageSize'] <= 0)
	{
		$errorList[] = array("message" => "ͼƬÿҳ��ʾ��Ŀ���������.");
	}
	if(!validate::required($_POST['waterText1']) || !validate::required($_POST['waterText2']))
	{
		$errorList[] = array("message" => "ˮӡ���ֲ���Ϊ��.");
	}
	if(!validate::isNumber($_POST['width']) || !validate::isNumber($_POST['height']))
	{
		$errorList[] = array("message" => "����ͼ�ߴ粻������.");
	}
	if(!validate::isNumber($_POST['maxWidth']) || !validate::isNumber($_POST['maxHeight']))
	{
		$errorList[] = array("message" => "ͼƬ�ϴ�������ߴ粻������.");
	}	
	if($_POST['width'] <= 0 || $_POST['height'] <= 0)
	{
		$errorList[] = array("message" => "ͼ����ͼ�ߴ���������.");
	}
	if($_POST['maxWidth'] <= 0 || $_POST['maxHeight'] <= 0)
	{
		$errorList[] = array("message" => "ͼƬ�ϴ�������ߴ���������.");
	}
	if(!empty($errorList))	//�������
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}	
}
?>
