<?php
//====================================================
//		FileName:logout.php
//		Summary: 用户注销登录程序
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
	$param["message"] = "您还没有登录系统,不能使用注销功能.";
	forward("error.php", $param);
}
//*/
?>
