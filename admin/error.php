<?php
//====================================================
//		FileName:error.php
//		Summary: ������ʾ������Ϣ,��ʾ��һ������Ϣ
//		
//====================================================

require_once("../IBinit.php");

$title	 = !empty($_GET['title']) ? $_GET['title'] : "ϵͳ��ʾ��Ϣ";
$message = !empty($_GET['message']) ? $_GET['message'] : "û���κ���ʾ��Ϣ.";  
$tpl = new SmartTemplate("admin/error.htm");
$tpl->assign("title", $title);
$tpl->assign("message", $message);
$tpl->output();

?>

