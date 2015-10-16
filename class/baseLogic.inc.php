<?php
//====================================================
//		FileName:baseLogic.inc.php
//		Summary: 基本数据操作模型,实现添加,删除,修改等
//				 使用时应注意表中自增字段名应为id
//		Author: 
//		CreateTime: 2004-10-23     
//		LastModifed:2004-10-27 
//		
//====================================================
class baseLogic
{
	var $db;			//数据访问层
	var $tblName;		//表的名称
	var $fieldList;		//字段名数组
	//==========================================
	// 函数: baseLogic($db)
	// 功能: 构造函数
	// 参数: &$db mysql对象
	//==========================================
	function baseLogic($db) 
	{
		$this->db = $db;
	}
	//==========================================
	// 函数: add($postList)
	// 功能: 添加
	// 参数: $postList 提交的变量列表
	// 返回: 刚插入的自增ID
	//==========================================
	function add($postList)
	{
		$sql = "INSERT INTO {$this->tblName} VALUES(null,";
		$value = ""; 
		foreach ($this->fieldList as $v) 
		{
			if (isset($postList[$v]))
			{
				if (!get_magic_quotes_gpc())
					$value .= "'".addslashes($postList[$v])."',";
				else
					$value .= "'".$postList[$v]."',";
			}
			else
				$value .= "'',";
		}
		//去掉最后一个逗号
		$value = rtrim($value,",");
		$sql .= $value.")";
		//print $sql;
		//exit;
		$this->db->query($sql);

		return $this->db->insertID();
	}
	//==========================================
	// 函数: update($postList)
	// 功能: 修改表数据
	// 参数: $postList 提交的变量列表
	//==========================================
	function update($postList) 
	{
		$sql = "UPDATE {$this->tblName} SET ";
		$value = ""; 
		foreach ($this->fieldList as $v) 
		{
			if (isset($postList[$v]))
			{
				if (!get_magic_quotes_gpc())
					$value .= $v." = '".addslashes($postList[$v])."',";
				else
					$value .= $v." = '".$postList[$v]."',";
			}
		}
		//去掉最后一个逗号
		$value = rtrim($value,",");
		$sql .= $value." WHERE id = '".$postList['id']."'";
		//echo $sql;exit();
		if ($this->db->query($sql)) 
		{
			return 1;
		}
		else 
		{
			return 0;
		}

	}
	//==========================================
	// 函数: delete($id)
	// 功能: 删除
	// 参数: $id 编号或ID列表数组
	// 返回: 0 失败 成功为删除的记录数
	//==========================================
	function delete($id)
	{
		if(is_array($id)) 
		{
			$tmp = "IN (" . join(",", $id) . ")";
		}
		else
		{
			$tmp = "= $id";
		}
		$sql = "DELETE FROM {$this->tblName} WHERE id " . $tmp ;
		$this->db->query($sql);

		return $this->db->affectedRows();
	}
}

?>