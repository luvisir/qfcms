<?php
//====================================================
//		FileName:message.php
//		Summary: ������ʾ��Ϣ,����ʾ�����б�,���з��ذ�ť
//		
//====================================================

require_once("login.php");
require_once("../IBinit.php");

$title	 = !empty($_GET['title']) ? $_GET['title'] : "ϵͳ��ʾ��Ϣ";
$msgList = !empty($_GET['msgList']) ? unserialize($_GET['msgList']) : "û���κ���ʾ��Ϣ.";  
$msgType = !empty($_GET['msgType']) ? $_GET['msgType'] : "error-msg";  

$tpl = new SmartTemplate("admin/message.htm");
$tpl->assign("title", $title);
$tpl->assign("msgType", $msgType);
$tpl->assign("msgList", $msgList);
$tpl->output();

?>

