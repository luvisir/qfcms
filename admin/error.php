<?php
//====================================================
//		FileName:error.php
//		Summary: 处理显示错误信息,显示单一错误信息
//		
//====================================================

require_once("../IBinit.php");

$title	 = !empty($_GET['title']) ? $_GET['title'] : "系统提示信息";
$message = !empty($_GET['message']) ? $_GET['message'] : "没有任何提示信息.";  
$tpl = new SmartTemplate("admin/error.htm");
$tpl->assign("title", $title);
$tpl->assign("message", $message);
$tpl->output();

?>

