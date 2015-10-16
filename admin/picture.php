<?php
//====================================================
//		FileName:picture.php
//		Summary: ͼƬ�������
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$album	 = new category($db, "album");
$pic	 = new picture($db);
$GDImage = new GDImage(CMS_UPLOAD_PATH, GALLERY_REAL_PATH, GALLERY_PATH);

$errorList = $successList = array();

if(!empty($_GET['action']))	//����GET��ʽ�ύ��action
{
	switch($_GET['action'])
	{
		case 'addPic':	//��ʾ���ͼƬ�ı�
			//ȡ����������б�
			$album->getTree();
			$attrArray['class'] = "text-box";
			//$attrArray['disabled'] = "true";
			$albumPath = $album->buildSelect("catPath", null, $attrArray);
			
			$tpl = new SmartTemplate("admin/picture.htm");

			$tpl->assign("catPath", $albumPath);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();

			break;

		case 'editPic':	//��ʾ�༭ͼƬ�ı�
			
			$editPic = $pic->getPic($_GET['id']);
			
			//ȡ����������б�
			$album->getTree();
			$attrArray['class'] = "text-box";
			//$attrArray['disabled'] = "true";
			$albumPath = $album->buildSelect("catPath", null, $attrArray);
			
			$tpl = new SmartTemplate("admin/editPic.htm");
			$varList = array("catPath"		=> $albumPath,
							 "picTitle"		=> $editPic['picTitle'],
							 "description"	=> $editPic['description'],
							 "picID"		=> $editPic['id']
							);
			//ȡ������ͼ��ˮӡͼ��Ϣ

			if($editPic['hasThumb'])
			{
				$varList['picPath'] = array_pop($GDImage->getThumb($editPic['picName']));
			}
			elseif($editPic['hasMark'])
			{
				$varList['picPath'] = array_pop($GDImage->getMark($editPic['picName']));
			}
			else
			{
				$varList['picPath'] = GALLERY_PATH . $editPic['picName'];
			}
			//
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();			break;
		case 'deletePic':	//ɾ��ͼƬ����

			//ɾ����һͼƬ��ͼƬ
			$delID = !empty($_GET['id']) ? $_GET['id'] : $_GET['idList'];
			if(empty($delID))
			{
				$errorList[] = array("message" => "û��ѡ��Ҫɾ����ͼƬ.");
				showMessage();
			}		
			$picList = $pic->getPicName($delID);

			$GDImage->removeImage($picList);

			$res = $pic->deletePic($delID);

			if($res != -1) 
			{
				$successList[] = array("message" => "ͼƬɾ���ɹ�.");
				$successList[] = array("message" => "$res ��ͼƬ��ɾ��.");
			}
			else
			{
				$errorList[] = array("message" => "ͼƬɾ��ʧ��,������.");
			}	
			
			showMessage();
			break;
		
		default:
			$param["message"] = "��������,������.";
			forward("error.php", $param);
			break;
	}
}
elseif(!empty($_POST['action']))	//����POST��ʽ�ύ��action
{
	switch($_POST['action'])
	{
		case 'addPic':	//���ͼƬ�Ĳ���
			
			//��֤����
			validateForm();

			$_POST['picName'] = $GDImage->uploadImage("uploadPic");
			
			//��ӵ����ݿ�
			if($pic->addPic($_POST))	//��ӳɹ�
			{
				//����ͼƬ
				$GDImage->maxWidth  = $pictureSize['maxWidth'];
				$GDImage->maxHeight = $pictureSize['maxHeight'];
				$GDImage->toFile = true;
				if(!empty($_POST['hasThumb'])) //��������ͼ
				{
					$GDImage->makeThumb($_POST['picName'], $thumbSize['width'], $thumbSize['height']);
				}
				if(!empty($_POST['hasMark']))	//��ˮӡ
				{
					$GDImage->waterMark($_POST['picName'], $waterText);
					@unlink(CMS_UPLOAD_PATH . $_POST['picName']);
				}
				else
				{
					if(!empty($_POST['noGD']))
					{
						@copy(CMS_UPLOAD_PATH . $_POST['picName'], GALLERY_REAL_PATH . $_POST['picName']);
						@unlink(CMS_UPLOAD_PATH . $_POST['picName']);
					}
					else
					{
						$GDImage->moveToGallery($_POST['picName']); 
						@unlink(CMS_UPLOAD_PATH . $_POST['picName']);
					}
				}
				
				$successList[] = array("message" => "ͼƬ��ӳɹ�.");
			}
			else
			{
				$errorList[] = array("message" => "���ͼƬ�����ݿ�ʧ��");
			}

			showMessage();

			break;

		case 'editPic':	//�༭ͼƬ�Ĳ���
			//��֤����
			if(!validate::required($_POST['picTitle']))
			{
				$errorList[] = array("message" => "ͼƬ���ⲻ��Ϊ��.");
			}
			if(!validate::checkLength($_POST['description'], 200))
			{
				$errorList[] = array("message" => "ͼƬ�������ܳ���200���ַ�.");
			}			

			if(!empty($errorList))	//�������
			{
				$param["msgList"] = serialize($errorList);
				forward("message.php", $param);
				exit();
			}
			//��������
			if($pic->editPic($_POST))
			{
				$successList[] = array("message" => "ͼƬ��Ϣ�޸ĳɹ�.");
			}
			else
			{
				$errorList[] = array("message" => "�޸�ʧ�ܣ�������.");
			}

			showMessage();
			break;

		default:
			$param["message"] = "��������,������.";
			forward("error.php", $param);
			break;
	}
}
else	//û���ύ
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
	if(!validate::required($_POST['picTitle']))
	{
		$errorList[] = array("message" => "ͼƬ���ⲻ��Ϊ��.");
	}
	if(!validate::checkLength($_POST['description'], 200))
	{
		$errorList[] = array("message" => "ͼƬ�������ܳ���200���ַ�.");
	}

	if(($_FILES['uploadPic']['error'] == 4))
	{
		$errorList[] = array("message" => "����û��ѡ��ͼƬ.");
	}
	if(($_FILES['uploadPic']['error'] == 1))
	{
		$errorList[] = array("message" => "ͼƬ��С������ϵͳ����Χ.");
	}
	if(!checkImgType($_FILES['uploadPic']['tmp_name']))
	{
		$errorList[] = array("message" => "ͼƬ����ϵͳ����ʶ����߲���ͼƬ.");
	}
	if(!empty($errorList))	//�������
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}	
}
/* ���� checkImgType($fileName)
** ���� ���ͼƬ�����Ƿ�Ϊ���������
** ���� $fileName �ļ���
*/
function checkImgType($fileName)
{

	$data = getimagesize($fileName);

	if($data && $data[2] < 4) //1ΪGIF 2ΪJPG 3ΪPNG,
	{
		return true;;
	}
	else
		return false;
}
?>
