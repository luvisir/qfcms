<?php
//====================================================
//		FileName: user.inc.php
//		Summary:  帐户操作类
//		
//====================================================

class user extends baseLogic
{

	/*//数据库字段描述
	var $id;        //帐户编号
	var $name;      //帐户用户名
	var $pwd;       //帐户密码 
	//*/

	/* 函数 user($db)
	** 功能 构造函数
	** 参数 $db mysql类
	*/
	function user($db)
	{
		$this->baseLogic($db);
		$this->fieldList = array("id", "name", "pwd");
		$this->tblName	 = CMS_PREFIX . "user";
	}
	
	/* 函数 login($uname, $pwd)
	** 功能 帐户登录检查程序
	** 参数 $name 帐户名
	** 参数 $pwd  帐户密码
	*/
	function login($uname, $pwd)
	{
		$sql = "SELECT id from {$this->tblName} 
				WHERE name = '$uname' AND pwd = md5('$pwd')";
		//echo $sql;
		$this->db->query($sql);
		if ($record = $this->db->fetchRow())
		{	//登录成功
			$_SESSION['isLogin'] 	= true;
			$_SESSION['uid']		= $record['id'];
			$_SESSION['uname']		= $uname;
			return 1;
		}
		else
			return 0;
	}
	/* 函数 isLogin
	** 功能 判断用户是否登录
	** 参数 无
	** 返回 true 已登录 false 未登录
	*/
	function isLogin() {
		//echo $_SESSION['isLogin'];
		if(!empty($_SESSION['isLogin']))
			return 1;	
		else
			return 0;
	}

	/* 函数 logout()
	** 功能 用户注销登录
	** 参数 无
	*/ 
	function logout()
	{
		$_SESSION = array();
		session_destroy();

	}
	/* 函数 addUser($name, $pwd)
	** 功能 添加一个帐户
	** 参数 见类的变量定义 
	** 返回 -1 用户名存在  0 失败
	*/
	function addUser($name, $pwd)
	{
		$sql = "SELECT id FROM {$this->tblName} 
				WHERE name = '$name'";

		$this->db->query($sql);
		if($this->db->recordCount())
		{
			return -1;
		}
		$sql = "INSERT INTO {$this->tblName} 
				VALUES(null,'$name',MD5('$pwd'))";
		$this->db->query($sql);

		return $this->db->insertID();
	}
	/* 函数 delUser($id)
	** 功能 从数据库中删除一个帐户
	** 参数 $id 帐户编号
	*/
	function delUser($id)
	{
		return $this->delete($id);
	}
	/* 函数 editUser($id, $pwd)
	** 功能 修改帐户
	** 参数 参见类的变量定义
	*/
	function editUser($id, $pwd)
	{
		$sql = "UPDATE {$this->tblName}
				SET pwd	= MD5('$pwd') 
				WHERE id = '$id'";
		if($this->db->query($sql))
			return 1;
		else
			return 0;
	}

	/* 函数 getUser($id)
	** 功能 取得一个帐户记录
	** 参数 $id 帐户编号
	*/
	function getUser($id)
	{
		$sql = "SELECT id, name FROM {$this->tblName}
				WHERE id = '$id'";
		$this->db->query($sql);
		return $this->db->fetchRow();
	}
	/* 函数 listUser()
	** 功能 取得帐户列表
	*/
	function listUser()
	{
		$sql = "SELECT id, name AS userName FROM {$this->tblName}";
		$this->db->query($sql);
		if($this->db->recordCount())
			return $this->db->fetchAll();
		else
			return 0;
	}
	/* 函数 setPwd($id, $oriPwd, $newPwd)
	** 功能 帐户修改密码
	** 参数 $id 帐户编号
	** 参数 $oriPwd 原始密码
	** 参数 $newPwd 新密码
	** 返回 2 原始密码错误 1 成功 0 失败
	*/
	function setPwd($id, $oriPwd, $newPwd)
	{
		$sql = "SELECT id FROM {$this->tblName}
				WHERE pwd = MD5('$oriPwd')";
		$this->db->query($sql);
		if($this->db->recordCount() == 0) 
			return 2;	

		$sql = "UPDATE {$this->tblName}
				SET pwd = MD5('$newPwd')
				WHERE id = '$id'";
		if ($this->db->query($sql))
			return 1;
		else
			return 0;
	}
}
?>