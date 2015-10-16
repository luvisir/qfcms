<?php
//====================================================
//		FileName:listUser.php
//		Summary: �û��б����
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$user = new user($db);
$tpl  = new SmartTemplate("admin/listUser.htm");

//�û��б�
$userList = $user->listUser();

$tpl->assign("userList", $userList);
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());

$tpl->output();

?>
