<?php
//====================================================
//		FileName:articleCat.php
//		Summary: ���ŷ���������
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$cat = new category($db, "cat");

//����������������ɹ���Ϣ������
$errorList	 = array();
$successList = array();

if(!empty($_GET['action']))	//������ʾ��
{
	switch($_GET['action'])
	{
		case 'addCat':		//�������

			//ȡ�����з����б�
			$cat->getTree();
			$attrArray['class'] = "text-box";
			//$attrArray['disabled'] = "true";
			$parentNode = $cat->buildSelect("parentNode", null, $attrArray);
			$tpl = new SmartTemplate("admin/articleCat.htm");
			$varList = array(
								"title"			=> "���·������",
								"parentNode"	=> $parentNode,
								"action"		=> "addCat",
								"absPath"		=> "",
								"buttonValue"	=> "�� ��",
								"catTitle"		=> "",
								"description"	=> "",
								"imgName"		=> GALLERY_PATH . "no_image.gif"
							);
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();
			break;

		case 'editCat':		//�����޸�

			//ȡ�õ�ǰ�������Ϣ
			$currentCat = $cat->getNode($_GET['absPath']);
			$parentID	= $cat->getParent($_GET['absPath']);
			//ȡ�����з����б�
			$cat->getTree();
			$attrArray['class'] = "text-box";
			$attrArray['disabled'] = "disabled";
			$parentNode = $cat->buildSelect("parentNode", $parentID, $attrArray);

			$tpl = new SmartTemplate("admin/articleCat.htm");
			$varList = array(
								"title"			=> "���·����޸�",
								"parentNode"	=> $parentNode,
								"action"		=> "editCat",
								"absPath"		=> $currentCat['absPath'],
								"buttonValue"	=> "�� ��",
								"catTitle"		=> $currentCat['catTitle'],
								"description"	=> $currentCat['description'],
								"imgName"		=> $currentCat['catImage']
							);
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();
			break;			

		case 'deleteCat':	//����ɾ��

			$res = $cat->remove($_GET['absPath']);
			if ($res == -1) 
				$errorList[] = array("message" => "����ɾ��������.");
			elseif($res == 0)
				$errorList[] = array("message" => "ɾ��ʧ��,������.");
			else
			{

				//ɾ����������
				$art = new article($db);
				$delArtNum = $art->delArticleByCat($_GET['absPath']);
				//ɾ�������ļ���Ŀ¼
				fileSystem::removeDir(getRealPath($_GET['absPath']));

				$successList[] = array("message" => "����ɾ���ɹ�.");
				$successList[] = array("message" => "$res ����¼��ɾ��");
				$successList[] = array("message" => "$delArtNum ƪ���±�ɾ��");
				
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
		case 'addCat':	//������ӷ���Ĳ���
	
			//��֤��
			validateForm();

			//�޴���,��ӷ���
			$newAbsPath = $cat->add($_POST['parentNode'], $_POST['catTitle'], $_POST['description'], $_POST['catImage']);

			//����Ŀ¼
			makeCatdir($newAbsPath);

			//������ʾ��Ϣ
			showMessage();
			break;

		case 'editCat':	//�����޸ķ���Ĳ���

			//��֤��
			validateForm();

			/*/�ƶ����
			$res1 = $cat->moveTo($_POST['absPath'], $_POST['parentNode']);
			if($res1 == -1) //�ƶ�����
			{
				$errorList[] = array("message" => "���ܽ�������ӵ��Լ����ӷ�����.");
			}
			else
			{
			*/

			//�޸ķ�����Ϣ
			$res2 = $cat->setNode($_POST['absPath'], $_POST['catTitle'], $_POST['description'], $_POST['catImage']);
			if($res2 != -1)
			{
				$successList[] = array("message" => "�����޸ĳɹ�.");
				//$successList[] = array("message" => "$res1 ����¼���ƶ�.");
				//$successList[] = array("message" => "$res2 ����¼���޸�.");
			}
			else
				$errorList[] = array("message" => "�����޸�ʧ��,������.");

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
		$errorList[] = array("message" => "������ⲻ��Ϊ��.");
	}
	if(validate::match($_POST['catTitle'], "|[\\\/\'\"]|"))
	{
		$errorList[] = array("message" => "������⺬�зǷ��ַ�, ���ܰ���\\ / ' \"���ַ�.");
	}
	if(!validate::checkLength($_POST['description'], 200))
	{
		$errorList[] = array("message" => "�����������ܳ���200���ַ�.");
	}
	if(!empty($errorList))	//�������
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}	
}
/* ���� makeCatdir($absPath)
** ���� ���ݽ��·����Ϣ����Ŀ¼
** ���� $absPath ������·��
*/
function makeCatDir($absPath)
{
	//����ȫ�ֱ��� 
	global $successList, $errorList, $cat;

	if(!empty($absPath))
	{
		//ԭ·��Ϊ0,1,5,14,50 ȥ��0,1,����ĸ�Ŀ¼,����5,14,50
		$path = substr($absPath, 4);	
		//��5,14,50ת����5/14/50
		$path = str_replace(",", "/", $path);
	
		if(@mkdir(ARTICLE_REAL_PATH . $path))
		{
			$successList[] = array("message" => "���·�����ӳɹ�.");
		}
		else
		{
			$cat->remove($absPath);
			$errorList[] = array("message" => "��������Ŀ¼ʧ��,������.");
		}
	}
	else
	{
		$errorList[] = array("message" => "���·������ʧ��,������.");
	}
}
/* ���� getRealPath($absPath)
** ���� ���ݽ��·����Ϣ�����������·��
** ���� $absPath ���·����Ϣ
*/
function getRealPath($absPath)
{
	if(!empty($absPath))
	{
		//ԭ·��Ϊ0,1,5,14,50 ȥ��0,1,����ĸ�Ŀ¼,����5,14,50
		$path = substr($absPath, 4);	
		//��5,14,50ת����5/14/50
		$path = str_replace(",", "/", $path);

		return ARTICLE_REAL_PATH . $path;	
	}
	return false;
}
?>
