<?php
//====================================================
//		FileName:user.php
//		Summary: �ʻ��������
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$user = new user($db);
//����������������ɹ���Ϣ������
$errorList	 = array();
$successList = array();

if(!empty($_GET['action']))	//������ʾ��
{
	switch($_GET['action'])
	{
		case 'addUser':		//����û�

			$tpl = new SmartTemplate("admin/user.htm");
			$varList = array("title"	=> "�ʻ����",
							 "userName"	=> "",
							 "disabled" => "", 
							 "action"	=> "addUser",
							 "userID"	=> "",
							 "buttonValue" => "�� ��"
							);
			
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();

			break;
		case 'editUser':	//�޸��û�

			$editUser = $user->getUser($_GET['id']);
			$tpl = new SmartTemplate("admin/user.htm");
			$varList = array("title"	=> "�ʻ��޸�",
							 "userName"	=> $editUser['name'],
							 "disabled" => "disabled=\"disabled\"", 
							 "action"	=> "editUser",
							 "userID"	=> $editUser['id'],
							 "buttonValue" => "�� ��"
							);
			
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();			
			break;

		case 'delUser':		//ɾ���û�
			
			$delID = !empty($_GET['id']) ? $_GET['id'] : $_GET['idList'];

			if(empty($delID))
			{
				$errorList[] = array("message" => "û��ѡ��Ҫɾ�����ʻ�.");
				showMessage();
			}

			$res = $user->delUser($delID);

			if($res != -1) 
			{
				$successList[] = array("message" => "�ʻ�ɾ���ɹ�.");
				$successList[] = array("message" => "$res ���ʻ���ɾ��.");
			}
			else
			{
				$errorList[] = array("message" => "�ʻ�ɾ��ʧ��,������.");
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
		case 'addUser':		//����û�

			//��֤����
			validateForm();

			$res = $user->addUser($_POST['userName'], $_POST['pwd']);
			if($res == -1)
			{
				$errorList[] = array("message" => "�ʻ����Ѿ����ڣ�����������.");
			}
			elseif($res > 0)
			{
				$successList[] = array("message" => "�ʻ���ӳɹ�.");
			}
			else
			{
				$errorList[] = array("message" => "�ʻ����ʧ��");
			}

			showMessage();
			break;

		case 'editUser':	//�޸��û�

			validateForm();

			$res = $user->editUser($_POST['userID'], $_POST['pwd']);

			if($res)
			{
				$successList[] = array("message" => "�ʻ��޸ĳɹ�.");
			}
			else
			{
				$errorList[] = array("message" => "�ʻ��޸�ʧ��");
			}

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

//��֤��ҳ���ĺ���
function validateForm()
{
	//����ȫ�ֱ��� 
	global $errorList, $successList;
	if(checkAction("addUser") && !validate::required($_POST['userName']))
	{
		$errorList[] = array("message" => "�û�������Ϊ��.");
	}
	if(!validate::required($_POST['pwd']) || !validate::required($_POST['pwd2']))
	{
		$errorList[] = array("message" => "���������ȷ��Ϊ��.");
	}
	if(!validate::equal($_POST['pwd'], $_POST['pwd2']))
	{
		$errorList[] = array("message" => "�����������벻��ͬ");
	}

	if(!empty($errorList))	//�������
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}

}
?>
