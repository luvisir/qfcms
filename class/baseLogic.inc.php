<?php
//====================================================
//		FileName:baseLogic.inc.php
//		Summary: �������ݲ���ģ��,ʵ�����,ɾ��,�޸ĵ�
//				 ʹ��ʱӦע����������ֶ���ӦΪid
//		Author: 
//		CreateTime: 2004-10-23     
//		LastModifed:2004-10-27 
//		
//====================================================
class baseLogic
{
	var $db;			//���ݷ��ʲ�
	var $tblName;		//�������
	var $fieldList;		//�ֶ�������
	//==========================================
	// ����: baseLogic($db)
	// ����: ���캯��
	// ����: &$db mysql����
	//==========================================
	function baseLogic($db) 
	{
		$this->db = $db;
	}
	//==========================================
	// ����: add($postList)
	// ����: ���
	// ����: $postList �ύ�ı����б�
	// ����: �ղ��������ID
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
		//ȥ�����һ������
		$value = rtrim($value,",");
		$sql .= $value.")";
		//print $sql;
		//exit;
		$this->db->query($sql);

		return $this->db->insertID();
	}
	//==========================================
	// ����: update($postList)
	// ����: �޸ı�����
	// ����: $postList �ύ�ı����б�
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
		//ȥ�����һ������
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
	// ����: delete($id)
	// ����: ɾ��
	// ����: $id ��Ż�ID�б�����
	// ����: 0 ʧ�� �ɹ�Ϊɾ���ļ�¼��
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