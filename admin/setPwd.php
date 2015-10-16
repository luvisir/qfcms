<?php
//====================================================
//		FileName:setPwd.php
//		Summary: �����޸ĳ���
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

if(checkAction("setPwd"))	//���ύ,��������
{
	$user = new user($db);
	//�������ݲ�������Ϣ�б�
	$errorList	 = array();
	$successList = array();
	if($_POST['newPwd'] != $_POST['cfmPwd'])
	{
		$errorList[] = array("message" => "�����������벻��ͬ.");
	}

	$res = $user->setPwd($_SESSION['uid'], $_POST['oriPwd'], $_POST['newPwd']);
	if($res == 2)
	{
		$errorList[] = array("message" => "ԭʼ�������.");
	}
	elseif($res == 1)
	{
		$successList[] = array("message" => "�����޸ĳɹ�.");
	}
	else
	{
		$errorList[] = array("message" => "�����޸�ʧ��,������.");
	}

	//������Ϣ�б���ʾ�����Ϣ
	showMessage();
}
else	//��δ�ύ,��ʾ��
{
	$tpl = new SmartTemplate("admin/setPwd.htm");
	$tpl->assign("queryTime", $db->getQueryTimes());
	$tpl->assign("executeTime", $timer->getExecuteTime());

	$tpl->output();
}
?>
