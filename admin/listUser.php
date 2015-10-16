<?php
//====================================================
//		FileName:listUser.php
//		Summary: 用户列表程序
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$user = new user($db);
$tpl  = new SmartTemplate("admin/listUser.htm");

//用户列表
$userList = $user->listUser();

$tpl->assign("userList", $userList);
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());

$tpl->output();

?>
