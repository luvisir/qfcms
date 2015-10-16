<?php
//====================================================
//		FileName:user.php
//		Summary: 帐户管理程序
//====================================================
require_once("login.php");
require_once("../IBinit.php");

$user = new user($db);
//两个用来保存错误或成功信息的数组
$errorList	 = array();
$successList = array();

if(!empty($_GET['action']))	//负责显示表单
{
	switch($_GET['action'])
	{
		case 'addUser':		//添加用户

			$tpl = new SmartTemplate("admin/user.htm");
			$varList = array("title"	=> "帐户添加",
							 "userName"	=> "",
							 "disabled" => "", 
							 "action"	=> "addUser",
							 "userID"	=> "",
							 "buttonValue" => "添 加"
							);
			
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();

			break;
		case 'editUser':	//修改用户

			$editUser = $user->getUser($_GET['id']);
			$tpl = new SmartTemplate("admin/user.htm");
			$varList = array("title"	=> "帐户修改",
							 "userName"	=> $editUser['name'],
							 "disabled" => "disabled=\"disabled\"", 
							 "action"	=> "editUser",
							 "userID"	=> $editUser['id'],
							 "buttonValue" => "修 改"
							);
			
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();			
			break;

		case 'delUser':		//删除用户
			
			$delID = !empty($_GET['id']) ? $_GET['id'] : $_GET['idList'];

			if(empty($delID))
			{
				$errorList[] = array("message" => "没有选择要删除的帐户.");
				showMessage();
			}

			$res = $user->delUser($delID);

			if($res != -1) 
			{
				$successList[] = array("message" => "帐户删除成功.");
				$successList[] = array("message" => "$res 个帐户被删除.");
			}
			else
			{
				$errorList[] = array("message" => "帐户删除失败,请重试.");
			}	
			
			showMessage();
			break;

		default:
			$param["message"] = "参数错误,请重试.";
			forward("error.php", $param);			
			break;
	}
}
elseif(!empty($_POST['action']))	//负责表单提交后的数据处理
{
	switch($_POST['action'])
	{
		case 'addUser':		//添加用户

			//验证数据
			validateForm();

			$res = $user->addUser($_POST['userName'], $_POST['pwd']);
			if($res == -1)
			{
				$errorList[] = array("message" => "帐户名已经存在，请重新输入.");
			}
			elseif($res > 0)
			{
				$successList[] = array("message" => "帐户添加成功.");
			}
			else
			{
				$errorList[] = array("message" => "帐户添加失败");
			}

			showMessage();
			break;

		case 'editUser':	//修改用户

			validateForm();

			$res = $user->editUser($_POST['userID'], $_POST['pwd']);

			if($res)
			{
				$successList[] = array("message" => "帐户修改成功.");
			}
			else
			{
				$errorList[] = array("message" => "帐户修改失败");
			}

			showMessage();
			break;		

		default:
			$param["message"] = "参数错误,请重试.";
			forward("error.php", $param);			
			break;
	}
}
else
{
	$param["message"] = "参数错误,请重试.";
	forward("error.php", $param);	
}

//验证本页表单的函数
function validateForm()
{
	//载入全局变量 
	global $errorList, $successList;
	if(checkAction("addUser") && !validate::required($_POST['userName']))
	{
		$errorList[] = array("message" => "用户名不能为空.");
	}
	if(!validate::required($_POST['pwd']) || !validate::required($_POST['pwd2']))
	{
		$errorList[] = array("message" => "密码或密码确认为空.");
	}
	if(!validate::equal($_POST['pwd'], $_POST['pwd2']))
	{
		$errorList[] = array("message" => "两次密码输入不相同");
	}

	if(!empty($errorList))	//处理错误
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}

}
?>
