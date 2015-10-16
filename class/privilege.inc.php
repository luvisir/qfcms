<?php
/* 文件名 privilege.inc.php
** 说  明 权限操作类
*/ 

class privilege
{
	var $id;        //权限编号
	var $cid;       //栏目编号
	var $uid;		//用户编号 
	var $operation;	//对应栏目的权限
	var $db;		//mysql 类的实例
	
	/* 函数 privilege(&$db)
	** 功能 构造函数
	** 参数 $db mysql类的实例
	*/
	function privilege(&$db)
	{
		$this->db = &$db;
	}
	
	/* 函数 add($cid, $uid, $opeartion)
	** 功能 添加一个权限
	** 参数 见类的变量定义 
	*/
	function add($cid, $uid, $operation)
	{
		$sql = "INSERT INTO privilege VALUES(null,'$cid','$uid','$operation')";
		//echo $sql;
		$this->db->query($sql);
		if ($this->db->success())
			return 1;
		else
			return 0;
	}
	/* 函数 del($id)
	** 功能 从数据库中删除一条权限
	** 参数 $id 权限编号
	*/
	function del($id)
	{
		$sql = "DELETE FROM privilege WHERE id = '$id'";
		$this->db->query($sql);
		if ($this->db->success())
			return 1;
		else
			return 0;
	}
	/* 函数 update($id, $operation)
	** 功能 修改权限
	** 参数 参见类的变量定义
	*/
	function update($id, $operation)
	{
		$sql = "UPDATE privilege SET operation = '$operation' 
				WHERE id = '$id'";
		//echo $sql;
		if($this->db->query($sql))
			return 1;
		else
			return 0;
	}
	/* 函数 list_privilege($uid)
	** 功能 取得用户所属组的权限列表
	** 参数 $uid 组编号
	*/
	function list_privilege($uid)
	{
		$sql = "SELECT * FROM privilege 
				WHERE uid = '$uid'";
		if ($this->db->query($sql))
			return 1;
		else
			return 0;
	}
	/* 函数 get_privilege($uid)
	** 功能 从记录集中取得一条权限记录
	** 参数 无
	*/
	function get_privilege()
	{
		if ($record = $this->db->get_record())
			{
		      	foreach ($record as $key => $value)
				{
					$this->$key = $value;
				}
				return 1;
			}	
		else
			return 0;
	}
	/* 函数 privilege_num($uid)
	** 功能 返回一个用户组的权限总数
	** 参数 无
	*/
	function privilege_num($uid)
	{
		$sql = "SELECT count(id) as num FROM privilege 
				WHERE uid = '$uid'";
		$this->db->query($sql);
		$this->db->get_record();
		return $this->db->data("num");
	}
}
?>