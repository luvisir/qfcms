<?php
//====================================================
//		FileName:DBSession.class.php
//		Summary: session�������ݿ�Ĺ�����
//		
//====================================================

class DBSession
{
	var $sessName;	//Ҫʹ�õ�session����	
	var $tblname;	//����session�ı���
	var $db;		//���ݿ���ʲ�

	/* ���� DBSession($db) 
	** ���� ���캯��
	** ���� $db ���ݿ���ʲ�
	*/
	function DBSession($db)
	{
		/*
		$this->db = $db;
		$this->tblName = "CMS_session";
		$this->sessName= "IBID";
		$this->lifeTime= ini_get("session.gc_maxlifetime");	//��ʱ��ʱ��
		*/
		//session_name($this->sessName);
		//session_save_path($this->tblName);
		session_set_save_handler("IBOpen", "IBClose", "IBRead", "IBWrite", "IBDestroy", "IBgc");
		session_start();
	}

	/* ���� set()
	** ���� 
	** ���� 
	*/
	function set()
	{
		
	}
}

define("SESSION_TABLE", "CMS_session");
define("SESSION_EXPIRE_TIME", ini_get("session.gc_maxlifetime"));

//=====================================================
//  session����ϵ�к���
//  author: ice_berg16 
//  time:   2004-11-09
//   networks studio 
//=====================================================
function IBOpen($sessPath, $sessName)
{
	global $db;

	$db->connect(DB_HOST,DB_USER,DB_PWD,DB_NAME);

	return true;
}

function IBClose()
{
	return true;
}

function IBead($sessID)
{
	global $db;
	$sql = "SELECT sessData FROM " . SESSION_TABLE . " 
			WHERE sessID = '$sessID' AND expireTime > " . time();
	$db->query($sql);
	$db->fetchRow();	

	return $db->getValue("sessData");
}

function IBWrite($sessID, $sessData)
{
	global $db;
	$expireTime = time() + SESSION_EXPIRE_TIME;
	$sql = "REPLACE INTO " . SESSION_TABLE . "
			VALUES('$sessID', '$expireTime', '$sessData')";
	$db->query($sql);

	return $db->affectedRows();
}

function IBDestory($sessID)
{
	global $db;
	$sql = "DELETE FROM " . SESSION_TABLE . " 
			WHERE sessID = '$sessID'";
	$db->query($sql);

	return $db->affectedRows();
}

function IBgc()
{

	$sql = "DELETE FROM " . SESSION_TABLE . " 
			WHERE expireTime < " . time();
	$db->query($sql);

	return $db->affectedRows();
}
?>
