<?php
//====================================================
//		FileName:picture.inc.php
//		Summary: ͼƬ���ݲ�����
//		
//====================================================

class picture extends baseLogic
{
	/*////���ݿ��ֶ�˵��
	var $id;	
	var $picTitle;		//ͼƬ����
	var $description;	//ͼƬ����
	var $picName;		//ͼƬʵ������
	var $catPath;		//ͼƬ���·��
	var $hasThumb;		//�Ƿ�������ͼ
	var $hasMark;		//�Ƿ���ˮӡͼ
	//*/

	/* ���� picture($db)
	** ���� ���캯��
	** ���� $db mysql��
	*/
	function picture($db)
	{
		$this->baseLogic($db);
		$this->tblName = CMS_PREFIX . "picture";
		$this->albumTbl = CMS_PREFIX . "album";
		$this->fieldList = array("picTitle", "description", "picName", "catPath", "hasThumb", "hasMark");
	}

	/* ���� addPic($postList)
	** ���� �����ݿ������һ��ͼƬ��¼
	** ���� $postList �ֶ�ֵ����
	*/
	function addPic($postList)
	{
		return $this->add($postList);
	}

	/* ���� editPic($postList)
	** ���� �༭ͼƬ��Ϣ
	** ���� $postList �ֶ�ֵ����
	*/
	function editPic($postList)
	{
		return $this->update($postList);
	}

	/* ���� deletePic($id)
	** ���� ɾ��ͼƬ�����ݿ��еļ�¼
	** ���� 
	*/
	function deletePic($id)
	{
		return $this->delete($id);
	}
	
	/* ���� deletePicByCat($catPath)
	** ���� ɾ��һ������ͼƬ
	** ���� $catPath ���·��
	*/
	function deletePicByCat($catPath)
	{
		$sql = "DELETE FROM {$this->tblName}
				WHERE catPath LIKE '$catPath%'";

		$this->db->query($sql);

		return $this->db->affectedRows();
	}

	/* ���� getPicName($id)
	** ���� ����ID��ID�б�ȡ��ͼƬ����
	** ���� $id ��Ż�����
	** ���� ͼƬ���ƻ�����
	*/
	function getPicName($id)
	{
		$sql = "SELECT picName FROM {$this->tblName} WHERE id ";
		if(is_array($id)) 
		{
			$picName = array();
			$sql .= "IN (" . join(",", $id) . ")";
			$this->db->query($sql);
			while($this->db->fetchRow())
				$picName[] = $this->db->getValue("picName");
			return $picName;
		}
		else
		{
			$sql .= "= $id";
			$this->db->query($sql);
			$this->db->fetchRow();
			return $this->db->getValue("picName");
		}
	}
	/* ���� getPicCat($catPath)
	** ���� ȡ��һ�����ͼƬ�������б�
	** ���� $catPath ���·��
	** ���� ͼƬ��������
	*/
	function getPicCat($catPath)
	{
		$sql = "SELECT picName FROM {$this->tblName} 
				WHERE catPath LIKE '$catPath%' ";
		$picName = array();
		$this->db->query($sql);
		while($this->db->fetchRow())
			$picName[] = $this->db->getValue("picName");

		return $picName;
	}
	/* ���� getPic($id)
	** ���� ȡ��ͼƬ�����ݿ���Ϣ
	** ���� $id ͼƬID
	*/
	function getPic($id)
	{
		$sql = "SELECT pic.id, pic.picTitle, pic.picName, pic.description, 
				pic.hasThumb, pic.hasMark, album.catTitle 
				FROM {$this->tblName} AS pic, {$this->albumTbl} AS album
				WHERE pic.catPath = CONCAT(album.catPath,',',album.catID) 
				AND pic.id = $id";
		$this->db->query($sql);
		return $this->db->fetchRow();
	}

	/* ���� listPic($condition)
	** ���� ��ʾͼƬ�б�
	** ���� $condition ��������
	*/
	function listPic($condition)
	{
		$sql = "SELECT pic.id, pic.picTitle,
				pic.catPath, pic.hasThumb, pic.hasMark, album.catTitle 
				FROM {$this->tblName} AS pic, {$this->albumTbl} AS album
				WHERE pic.catPath = CONCAT(album.catPath,',',album.catID) ";
		$tmp = "";
		if(!empty($condition['catPath']))	//���������
		{
			$tmp .= "AND pic.catPath LIKE '{$condition['catPath']}%' ";
		}
		if(isset($condition['hasThumb']))
		{
			$tmp .= "AND pic.hasThumb = {$condition['hasThumb']} ";
		}
		if(isset($condition['hasMark']))
		{
			$tmp .= "AND pic.hasMark {$condition['hasMark']} ";
		}
		$tmp .= "ORDER BY id DESC ";

		if(!empty($condition['page']) && !empty($condition['rows'])) //��ȡָ�������ļ�¼
		{
			$start = ($condition['page'] - 1) * $condition['rows'];
			$tmp .= "LIMIT " . $start . ", " . $condition['rows'];
		}

		$sql .= $tmp;
		//echo $sql;
		$this->db->query($sql);
		return $this->db->fetchAll();
	}
	/* ���� listPicThumb($condition)
	** ���� ��ʾͼƬ����ͼ�б�
	** ���� $condition ��������
	*/
	function listPicThumb($condition)
	{
		$sql = "SELECT id, picTitle, picName, hasThumb, hasMark
				FROM {$this->tblName} ";
		$tmp = "";
		if(!empty($condition['catPath']))	//���������
		{
			$tmp .= "WHERE catPath LIKE '{$condition['catPath']}%' ";
		}
		$tmp .= "ORDER BY id DESC ";

		if(!empty($condition['page']) && !empty($condition['rows'])) //��ȡָ�������ļ�¼
		{
			$start = ($condition['page'] - 1) * $condition['rows'];
			$tmp .= "LIMIT " . $start . ", " . $condition['rows'];
		}

		$sql .= $tmp;
		//echo $sql;
		$this->db->query($sql);
		return $this->db->fetchAll();
	}	
	/* ���� totalPic($condition)
	** ���� �г�ָ����ѯ�����ļ�¼����
	** ���� 
	*/
	function totalPic($condition)
	{
		$sql = "SELECT count(*)	AS totalPic FROM {$this->tblName} ";
		$tmp = "";
		if(!empty($condition['catPath']))	//���������
		{
			$tmp .= "WHERE catPath LIKE '{$condition['catPath']}%' ";
		}
		if(isset($condition['hasThumb']))
		{
			$tmp .= (empty($tmp)?"WHERE":"AND")." AND hasThumb = {$condition['hasThumb']} ";
		}
		if(isset($condition['hasMark']))
		{
			$tmp .= (empty($tmp)?"WHERE":"AND")." AND hasMark {$condition['hasMark']} ";
		}

		$sql .= $tmp;
		//echo $sql;
		$this->db->query($sql);
		$this->db->fetchRow();	

		return $this->db->getValue("totalPic");
	}
}

?>
