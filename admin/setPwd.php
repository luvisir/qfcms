<?php
//====================================================
//		FileName:setPwd.php
//		Summary: 密码修改程序
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

if(checkAction("setPwd"))	//表单提交,处理数据
{
	$user = new user($db);
	//处理数据并生成信息列表
	$errorList	 = array();
	$successList = array();
	if($_POST['newPwd'] != $_POST['cfmPwd'])
	{
		$errorList[] = array("message" => "两次密码输入不相同.");
	}

	$res = $user->setPwd($_SESSION['uid'], $_POST['oriPwd'], $_POST['newPwd']);
	if($res == 2)
	{
		$errorList[] = array("message" => "原始密码错误.");
	}
	elseif($res == 1)
	{
		$successList[] = array("message" => "密码修改成功.");
	}
	else
	{
		$errorList[] = array("message" => "密码修改失败,请重试.");
	}

	//根据信息列表显示相关信息
	showMessage();
}
else	//表单未提交,显示表单
{
	$tpl = new SmartTemplate("admin/setPwd.htm");
	$tpl->assign("queryTime", $db->getQueryTimes());
	$tpl->assign("executeTime", $timer->getExecuteTime());

	$tpl->output();
}
?>
