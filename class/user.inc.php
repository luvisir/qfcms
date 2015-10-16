<?php
//====================================================
//		FileName: user.inc.php
//		Summary:  �ʻ�������
//		
//====================================================

class user extends baseLogic
{

	/*//���ݿ��ֶ�����
	var $id;        //�ʻ����
	var $name;      //�ʻ��û���
	var $pwd;       //�ʻ����� 
	//*/

	/* ���� user($db)
	** ���� ���캯��
	** ���� $db mysql��
	*/
	function user($db)
	{
		$this->baseLogic($db);
		$this->fieldList = array("id", "name", "pwd");
		$this->tblName	 = CMS_PREFIX . "user";
	}
	
	/* ���� login($uname, $pwd)
	** ���� �ʻ���¼������
	** ���� $name �ʻ���
	** ���� $pwd  �ʻ�����
	*/
	function login($uname, $pwd)
	{
		$sql = "SELECT id from {$this->tblName} 
				WHERE name = '$uname' AND pwd = md5('$pwd')";
		//echo $sql;
		$this->db->query($sql);
		if ($record = $this->db->fetchRow())
		{	//��¼�ɹ�
			$_SESSION['isLogin'] 	= true;
			$_SESSION['uid']		= $record['id'];
			$_SESSION['uname']		= $uname;
			return 1;
		}
		else
			return 0;
	}
	/* ���� isLogin
	** ���� �ж��û��Ƿ��¼
	** ���� ��
	** ���� true �ѵ�¼ false δ��¼
	*/
	function isLogin() {
		//echo $_SESSION['isLogin'];
		if(!empty($_SESSION['isLogin']))
			return 1;	
		else
			return 0;
	}

	/* ���� logout()
	** ���� �û�ע����¼
	** ���� ��
	*/ 
	function logout()
	{
		$_SESSION = array();
		session_destroy();

	}
	/* ���� addUser($name, $pwd)
	** ���� ���һ���ʻ�
	** ���� ����ı������� 
	** ���� -1 �û�������  0 ʧ��
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
	/* ���� delUser($id)
	** ���� �����ݿ���ɾ��һ���ʻ�
	** ���� $id �ʻ����
	*/
	function delUser($id)
	{
		return $this->delete($id);
	}
	/* ���� editUser($id, $pwd)
	** ���� �޸��ʻ�
	** ���� �μ���ı�������
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

	/* ���� getUser($id)
	** ���� ȡ��һ���ʻ���¼
	** ���� $id �ʻ����
	*/
	function getUser($id)
	{
		$sql = "SELECT id, name FROM {$this->tblName}
				WHERE id = '$id'";
		$this->db->query($sql);
		return $this->db->fetchRow();
	}
	/* ���� listUser()
	** ���� ȡ���ʻ��б�
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
	/* ���� setPwd($id, $oriPwd, $newPwd)
	** ���� �ʻ��޸�����
	** ���� $id �ʻ����
	** ���� $oriPwd ԭʼ����
	** ���� $newPwd ������
	** ���� 2 ԭʼ������� 1 �ɹ� 0 ʧ��
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