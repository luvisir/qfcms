<?php
//====================================================
//		FileName:login.php
//		Summary: �û���¼����¼�������
//		
//====================================================

require_once("../IBinit.php");
//session_cache_limiter("public");
session_start();

//�û��Ƿ��¼
if(!user::isLogin()) 
{
	if (isset($_POST['action']) && $_POST['action'] == "login") //�Ѿ��ύ��
	{
		//��ʼ����
		$db	 = new mysql(DB_HOST,DB_USER,DB_PWD,DB_NAME);
		$user	=  new user($db);
		$log	= $user->login($_POST['uname'], $_POST['pwd']); //�������Ա��¼
		if ($log) //��¼�ɹ�
		{
			forward("index.php");				
		}
		else //��¼ʧ��
		{
			$tpl = new SmartTemplate("admin/login.htm");
			$className = "login-msg";
			$message = "�û������������, ������.";
			$tpl->assign("className", $className);
			$tpl->assign("message", $message);
			$tpl->output();
			exit();
		}
	}
	else         //û���ύ��.��ʾ��¼����
	{
		$tpl = new SmartTemplate("admin/login.htm");
		$className = "not-display";
		$tpl->assign("className", $className);
		$tpl->assign("message", "");
		$tpl->output();
		exit();
	}
}


?>