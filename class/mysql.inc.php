<?php
//==========================================
//		mysql database driver
//==========================================

//make sure all sytax error are reported.
//error_reporting(E_ALL);

class mysql
{
	var $host     = "";			//mysql������
	var $user     = "";			//mysql�û���
	var $pwd      = "";			//mysql����
	var $dbName   = "";			//mysql���ݿ�����
	var $linkID   = 0;			//������������ID
	var $queryID  = 0;			//���������ѯID
	var $fetchMode= MYSQL_ASSOC;//ȡ��¼ʱ��ģʽ
	var $queryTimes = 0;		//�����ѯ�Ĵ���
	var $errno    = 0;			//mysql�������
	var $error    = "";			//mysql������Ϣ
	var $record   = array();	//һ����¼����

	//======================================
	// ����: mysql()
	// ����: ���캯��
	// ����: ������ı�������
	// ˵��: ���캯�����Զ��������ݿ�
	//      ������ֶ�����ȥ�����Ӻ���
	//======================================
	function mysql($host,$user,$pwd,$dbName)
	{	if(empty($host) || empty($user) || empty($dbName))
			$this->halt("���ݿ�������ַ,�û��������ݿ����Ʋ���ȫ,����!");
		$this->host    = $host;
		$this->user    = $user;
		$this->pwd     = $pwd;
		$this->dbName  = $dbName;
		$this->connect();//����Ϊ�Զ�����
	}
	//======================================
	// ����: connect($host,$user,$pwd,$dbName)
	// ����: �������ݿ�
	// ����: $host ������, $user �û���
	// ����: $pwd ����, $dbName ���ݿ�����
	// ����: 0:ʧ��
	// ˵��: Ĭ��ʹ�����б����ĳ�ʼֵ
	//======================================
	function connect($host = "", $user = "", $pwd = "", $dbName = "")
	{
		if ("" == $host)
			$host = $this->host;
		if ("" == $user)
			$user = $this->user;
		if ("" == $pwd)
			$pwd = $this->pwd;
		if ("" == $dbName)
			$dbName = $this->dbName;
		//now connect to the database
		$this->linkID = mysql_pconnect($host, $user, $pwd);
		if (!$this->linkID)
		{
			$this->halt();
			return 0;
		}
		if (!mysql_select_db($dbName, $this->linkID))
		{
			$this->halt();
			return 0;
		}
		mysql_query("set names gb2312;");
		return $this->linkID;			
	}
	//======================================
	// ����: query($sql)
	// ����: ���ݲ�ѯ
	// ����: $sql Ҫ��ѯ��SQL���
	// ����: 0:ʧ��
	//======================================
	function query($sql)
	{
		$this->queryTimes++;
		$this->queryID = mysql_query($sql, $this->linkID);
		if (!$this->queryID)
		{	
			$this->halt();
			return 0;
		}
		return $this->queryID;
	}
	//======================================
	// ����: setFetchMode($mode)
	// ����: ����ȡ�ü�¼��ģʽ
	// ����: $mode ģʽ MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH
	// ����: 0:ʧ��
	//======================================
	function setFetchMode($mode)
	{
		if ($mode == MYSQL_ASSOC || $mode == MYSQL_NUM || $mode == MYSQL_BOTH) 
		{
			$this->fetchMode = $mode;
			return 1;
		}
		else
		{
			$this->halt("�����ģʽ.");
			return 0;
		}
		
	}
	//======================================
	// ����: fetchRow()
	// ����: �Ӽ�¼����ȡ��һ����¼
	// ����: 0: ���� record: һ����¼
	//======================================
	function fetchRow()
	{
		$this->record = mysql_fetch_array($this->queryID,$this->fetchMode);
		return $this->record;
	}
	//======================================
	// ����: fetchAll()
	// ����: �Ӽ�¼����ȡ�����м�¼
	// ����: ��¼������
	//======================================
	function fetchAll()
	{
		$arr = array();
		while($this->record = mysql_fetch_array($this->queryID,$this->fetchMode))
		{
			$arr[] = $this->record;
		}
		mysql_free_result($this->queryID);
		return $arr;
	}
	//======================================
	// ����: getValue()
	// ����: ���ؼ�¼��ָ���ֶε�����
	// ����: $field �ֶ������ֶ�����
	// ����: ָ���ֶε�ֵ
	//======================================
	function getValue($field)
	{
		return $this->record[$field];
	}
	//======================================
	// ����: affectedRows()
	// ����: ����Ӱ��ļ�¼��
	//======================================
	function affectedRows()
	{
		return mysql_affected_rows($this->linkID);
	}
	//======================================
	// ����: recordCount()
	// ����: ���ز�ѯ��¼������
	// ����: ��
	// ����: ��¼����
	//======================================
	function recordCount()
	{
		return mysql_num_rows($this->queryID);
	}
	//======================================
	// ����: getQueryTimes()
	// ����: ���ز�ѯ�Ĵ���
	// ����: ��
	// ����: ��ѯ�Ĵ���
	//======================================
	function getQueryTimes()
	{
		return $this->queryTimes;
	}
	//======================================
	// ����: getVersion()
	// ����: ����mysql�İ汾
	// ����: ��
	//======================================
	function getVersion() 
	{
		$this->query("select version() as ver");
		$this->fetchRow();
		return $this->getValue("ver");
	}
	//======================================
	// ����: getDBSize($dbName, $tblPrefix=null)
	// ����: �������ݿ�ռ�ÿռ��С
	// ����: $dbName ���ݿ���
	// ����: $tblPrefix ���ǰ׺,��ѡ
	//======================================
	function getDBSize($dbName, $tblPrefix=null) 
	{
		$sql = "SHOW TABLE STATUS FROM " . $dbName;
		if($tblPrefix != null) {
			$sql .= " LIKE '$tblPrefix%'";
		}
		$this->query($sql);
		$size = 0;
		while($this->fetchRow())
			$size += $this->getValue("Data_length") + $this->getValue("Index_length");
		return $size;
	}
	//======================================
	// ����: insertID()
	// ����: �������һ�β��������ID
	// ����: ��
	//======================================
	function insertID() {
		return mysql_insert_id();
	}
	//====================================== 
	// ����: halt($err_msg)
	// ����: �������г�����Ϣ
	// ����: $err_msg �Զ���ĳ�����Ϣ
	//=====================================
	function halt($err_msg="")
	{
		if ("" == $err_msg)
		{
			$this->errno = mysql_errno();
			$this->error = mysql_error();
			echo "<b>mysql error:<b><br>";
			echo $this->errno.":".$this->error."<br>";
			exit;
		}
		else
		{
			echo "<b>mysql error:<b><br>";
			echo $err_msg."<br>";
			exit;
		}
	}
}
?>