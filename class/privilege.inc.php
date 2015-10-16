<?php
/* �ļ��� privilege.inc.php
** ˵  �� Ȩ�޲�����
*/ 

class privilege
{
	var $id;        //Ȩ�ޱ��
	var $cid;       //��Ŀ���
	var $uid;		//�û���� 
	var $operation;	//��Ӧ��Ŀ��Ȩ��
	var $db;		//mysql ���ʵ��
	
	/* ���� privilege(&$db)
	** ���� ���캯��
	** ���� $db mysql���ʵ��
	*/
	function privilege(&$db)
	{
		$this->db = &$db;
	}
	
	/* ���� add($cid, $uid, $opeartion)
	** ���� ���һ��Ȩ��
	** ���� ����ı������� 
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
	/* ���� del($id)
	** ���� �����ݿ���ɾ��һ��Ȩ��
	** ���� $id Ȩ�ޱ��
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
	/* ���� update($id, $operation)
	** ���� �޸�Ȩ��
	** ���� �μ���ı�������
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
	/* ���� list_privilege($uid)
	** ���� ȡ���û��������Ȩ���б�
	** ���� $uid ����
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
	/* ���� get_privilege($uid)
	** ���� �Ӽ�¼����ȡ��һ��Ȩ�޼�¼
	** ���� ��
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
	/* ���� privilege_num($uid)
	** ���� ����һ���û����Ȩ������
	** ���� ��
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