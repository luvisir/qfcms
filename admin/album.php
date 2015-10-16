<?php
//====================================================
//		FileName:album.php
//		Summary: ���������
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$cat = new category($db, "album");

//����������������ɹ���Ϣ������
$errorList	 = array();
$successList = array();

if(!empty($_GET['action']))	//������ʾ��
{
	switch($_GET['action'])
	{
		case 'addAlbum':		//������

			//ȡ����������б�
			$cat->getTree();
			$attrArray['class'] = "text-box";
			//$attrArray['disabled'] = "true";
			$parentNode = $cat->buildSelect("parentNode", null, $attrArray);
			$tpl = new SmartTemplate("admin/album.htm");
			$varList = array(
								"title"			=> "������",
								"parentNode"	=> $parentNode,
								"action"		=> "addAlbum",
								"absPath"		=> "",
								"buttonValue"	=> "�� ��",
								"catTitle"		=> "",
								"description"	=> ""
							);
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());
			$tpl->output();
			break;

		case 'editAlbum':		//����޸�

			//ȡ�õ�ǰ������Ϣ
			$currentCat = $cat->getNode($_GET['absPath']);
			$parentID	= $cat->getParent($_GET['absPath']);
			//ȡ����������б�
			$cat->getTree();
			$attrArray['class'] = "text-box";
			$attrArray['disabled'] = "true";
			$parentNode = $cat->buildSelect("parentNode", $parentID, $attrArray);

			$tpl = new SmartTemplate("admin/album.htm");
			$varList = array(
								"title"			=> "����޸�",
								"parentNode"	=> $parentNode,
								"action"		=> "editAlbum",
								"absPath"		=> $currentCat['absPath'],
								"buttonValue"	=> "�� ��",
								"catTitle"		=> $currentCat['catTitle'],
								"description"	=> $currentCat['description']
							);
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();
			break;			

		case 'deleteAlbum':	//���ɾ��

			if($res = $cat->remove($_GET['absPath']))
			{
				
				$pic = new picture($db);
				//ɾ��ʵ�ʵ��ļ�
				$picList = $pic->getPicCat($_GET['absPath']);
				$GDImage = new GDImage(CMS_UPLOAD_PATH, GALLERY_REAL_PATH, GALLERY_PATH);
				$GDImage->removeImage($picList);

				//ɾ�����ݿ��¼
				$delPicNum = $pic->deletePicByCat($_GET['absPath']);
			}
			if ($res == -1) 
				$errorList[] = array("message" => "����ɾ�������.");
			elseif($res == 0)
				$errorList[] = array("message" => "ɾ��ʧ��,������.");
			else
			{
				$successList[] = array("message" => "���ɾ���ɹ�.");
				$successList[] = array("message" => "$res ����ᱻɾ��.");
				$successList[] = array("message" => "$delPicNum ��ͼƬ��ɾ��.");
			}
			showMessage();
			break;

		default:
			$param["message"] = "��������,������.";
			forward("error.php", $param);			
			break;
	}
}
elseif(!empty($_POST['action']))	//������ύ������ݴ���
{
	switch($_POST['action'])
	{
		case 'addAlbum':	//����������Ĳ���
	
			//��֤��
			validateForm();

			//�޴���,������
			$newPath = $cat->add($_POST['parentNode'], $_POST['catTitle'], $_POST['description'], "");
			if($newPath) 
			{
				$successList[] = array("message" => "�����ӳɹ�");
			}
			else
			{
				$errorList[] = array("message" => "������ʧ��");
			}
			//������ʾ��Ϣ
			showMessage();
			break;

		case 'editAlbum':	//�����޸����Ĳ���

			//��֤��
			validateForm();
			//�޸������Ϣ
			$res2 = $cat->setNode($_POST['absPath'], $_POST['catTitle'], $_POST['description'], "");
			if($res2 != -1)
			{
				$successList[] = array("message" => "����޸ĳɹ�.");
			}
			else
				$errorList[] = array("message" => "����޸�ʧ��,������.");

			//��ʾ��Ϣ
			showMessage();

			break;

		default:

			$param["message"] = "��������,������.";
			forward("error.php", $param);			
			break;
	}
}
else
{
	$param["message"] = "��������,������.";
	forward("error.php", $param);	
}

//===========һЩ��װ�ĺ���============

//��֤��ҳ���ĺ���
function validateForm()
{
	//����ȫ�ֱ��� 
	global $errorList, $successList;
	if(!validate::required($_POST['catTitle']))
	{
		$errorList[] = array("message" => "������Ʋ���Ϊ��.");
	}
	if(validate::match($_POST['catTitle'], "|[\\\/\'\"]|"))
	{
		$errorList[] = array("message" => "������ƺ��зǷ��ַ�, ���ܰ���\\ / ' \"���ַ�.");
	}
	if(!validate::checkLength($_POST['description'], 200))
	{
		$errorList[] = array("message" => "����������ܳ���100������.");
	}
	if(!empty($errorList))	//�������
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}	
}

?>
