<?php
//====================================================
//		FileName:message.php
//		Summary: 处理显示信息,可显示错误列表,并有返回按钮
//		
//====================================================

require_once("login.php");
require_once("../IBinit.php");

$title	 = !empty($_GET['title']) ? $_GET['title'] : "系统提示信息";
$msgList = !empty($_GET['msgList']) ? unserialize($_GET['msgList']) : "没有任何提示信息.";  
$msgType = !empty($_GET['msgType']) ? $_GET['msgType'] : "error-msg";  

$tpl = new SmartTemplate("admin/message.htm");
$tpl->assign("title", $title);
$tpl->assign("msgType", $msgType);
$tpl->assign("msgList", $msgList);
$tpl->output();

?>

