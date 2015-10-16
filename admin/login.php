<?php
//====================================================
//		FileName:login.php
//		Summary: 用户登录及登录检验程序
//		
//====================================================

require_once("../IBinit.php");
//session_cache_limiter("public");
session_start();

//用户是否登录
if(!user::isLogin()) 
{
	if (isset($_POST['action']) && $_POST['action'] == "login") //已经提交表单
	{
		//初始化类
		$db	 = new mysql(DB_HOST,DB_USER,DB_PWD,DB_NAME);
		$user	=  new user($db);
		$log	= $user->login($_POST['uname'], $_POST['pwd']); //检验管理员登录
		if ($log) //登录成功
		{
			forward("index.php");				
		}
		else //登录失败
		{
			$tpl = new SmartTemplate("admin/login.htm");
			$className = "login-msg";
			$message = "用户名或密码错误, 请重试.";
			$tpl->assign("className", $className);
			$tpl->assign("message", $message);
			$tpl->output();
			exit();
		}
	}
	else         //没有提交表单.显示登录界面
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