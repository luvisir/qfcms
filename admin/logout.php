<?php
//====================================================
//		FileName:logout.php
//		Summary: �û�ע����¼����
//		
//====================================================

require_once("../IBinit.php");

session_start();

if(user::isLogin())
{
	user::logout();
	forward("index.php");
}

//*
else
{
	$param["message"] = "����û�е�¼ϵͳ,����ʹ��ע������.";
	forward("error.php", $param);
}
//*/
?>
