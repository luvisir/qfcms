<?php
//====================================================
//		FileName:article.inc.php
//		Summary: ���²�����
//		Author: 
//		CreateTime: 2004-10-27     
//		LastModifed:2004-11-04
//		
//====================================================

class article extends baseLogic
{
/*	//���ݿ��ֶ�����
	var $id;			//����ID
	var $title;			//���±���
	var $summary;		//����ժҪ
	var $postTime;		//����ʱ��
	var $author;		//����
	var $comeFrom;		//��Դ
	var $content;		//����
	var $keyword;		//�ؼ���
	var $catPath;		//����·��
	var $isImg;			//�Ƿ�ΪͼƬ���� 0 ���� 1��
	var $imgName;		//ͼƬ���ŵ�����ͼ����
	var $linkPath;		//����ҳ������ӵ�ַ
	var $audit;			//�Ƿ����
	var $recommend;		//�Ƿ��Ƽ�
*/

	/* ���� article($db)
	** ���� ���캯��
	** ���� $db mysql��
	*/
	function article($db)
	{
		$this->baseLogic($db);
		//���±��������·������
		$this->tblName = CMS_PREFIX . "article";
		$this->catTbl  = CMS_PREFIX . "cat";
		$this->fieldList = array("title","summary","postTime","author","comeFrom",
								 "content","keyword","catPath","isImg","imgName","linkPath","audit","recommend");
	}

	/* ���� addArticle($postList)
	** ���� �������
	** ���� $$postList �ֶκ�ֵ�Ĺ�������
	*/
	function addArticle($postList)
	{
		return $this->add($postList);	
	}

	/* ���� editArticle($postList)
	** ���� �޸�����
	** ���� $postList �ֶκ�ֵ�Ĺ�������
	*/
	function editArticle($postList)
	{
		return $this->update($postList);
	}

	/* ���� delArticle($id)
	** ���� ɾ������/֧�ֶ���
	** ���� $id ����ID��ID�б�����
	*/	
	function delArticle($id)
	{
		return $this->delete($id);	
	}

	/* ���� delArticleByCat($catPath)
	** ���� ɾ��һ�����������
	** ���� $catPath ���·���·��
	*/	
	function delArticleByCat($catPath)
	{
		$sql = "DELETE FROM {$this->tblName}
				WHERE catPath LIKE '$catPath%'";
		$this->db->query($sql);
		//echo $sql;
		return $this->db->affectedRows();
	}

	/* ���� getArticle($id)
	** ���� ����IDȡ����������
	** ���� $id ����ID
	** ���� ������Ϣ����
	*/
	function getArticle($id)
	{
		$sql = "SELECT * FROM {$this->tblName} 
				WHERE id = '$id'";
		$this->db->query($sql);

		return $this->db->fetchRow();
	
	}

	/* ���� listArticle($condition)
	** ���� ��������ȡ�������б�
	** ���� $condition ��������
	** ���� �����б�����
	*/
	function listArticle($condition)
	{

		$sql = "SELECT art.id,art.title,art.postTime,art.catPath,
				art.summary,art.linkPath,art.audit,cat.catTitle
				FROM {$this->tblName} AS art, {$this->catTbl} AS cat
				WHERE art.catPath = CONCAT(cat.catPath,',',cat.catID) ";

		$tmp = "";
		if(isset($condition['audit']))	//���Ƿ��������
		{
			$tmp .= "AND art.audit = {$condition['audit']} ";
		}
		if(!empty($condition['catPath']))	//����������
		{
			$tmp .= "AND art.catPath LIKE '{$condition['catPath']}%' ";
		}
		if(!empty($condition['title']))	//����������
		{
			$tmp .= "AND art.title LIKE '%{$condition['title']}%' ";
		}
		if(!empty($condition['content']))	//����������
		{
			$tmp .= "AND art.content LIKE '%{$condition['content']}%' ";
		}
		if(!empty($condition['keyword']))	//���ؼ�������
		{
			$tmp .= "AND art.keyword LIKE '%{$condition['keyword']}%' ";
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
	/* ���� auditArticle($id)
	** ���� �������
	** ���� $id ���µ�ID��ID����
	** ���� -1ʧ�� 
	*/
	function auditArticle($id)
	{
		$sql = "UPDATE {$this->tblName} SET audit=1 WHERE id ";
		if(is_array($id)) 
		{
			$sql .= "IN (" . join(",", $id) . ")";

		}
		else
		{
			$sql .= "= $id";
		}
		//echo $sql;
		if($this->db->query($sql))
		{
			return $this->db->affectedRows();
		}
		else
		{
			return -1;
		}

	}
	/* ���� lockArticle($id)
	** ���� ��������
	** ���� $id ���µ�ID��ID����
	** ���� 0ʧ�� 
	*/
	function lockArticle($id)
	{
		$sql = "UPDATE {$this->tblName} SET audit=0 WHERE id ";
		if(is_array($id)) 
		{
			$sql .= "IN (" . join(",", $id) . ")";

		}
		else
		{
			$sql .= "= $id";
		}
		//echo $sql;
		$this->db->query($sql);
		return $this->db->affectedRows();
	}
	/* ���� getRecommend($catPath)
	** ���� ȡ�����������е��Ƽ�����
	** ���� $catPath ���µķ���·��
	** ���� $number ȡ������������
	*/
	function getRecommend($catPath,$number)
	{
		$sql = "select title, linkPath FROM {$this->tblName} 
				WHERE catPath LIKE '$catPath%' 
				AND recommend=1 AND audit=1
				LIMIT $number";
		//echo $sql;
		$this->db->query($sql);

		return $this->db->fetchAll();		
	}
	/* ���� getRelLink($keyword,$number)
	** ���� ���ݹؼ���ȡ�����µ��������
	** ���� $keyword�ؼ��� �����Ϊ��,�������ַ���)
	** ���� $number ȡ������������
	*/
	function getRelLink($keyword,$number)
	{
		$sql = "SELECT title, linkPath FROM {$this->tblName} WHERE ";
		if(is_array($keyword))
		{
			$keyList = explode(",", $keyword);
			$sql .= "(keyword LIKE '%" . array_shift($keyList) . "%' ";
			foreach($keyList as $val)
			{
				$sql .= "|| keyword LIKE '%$val%' ";
			}
			$sql .= ") AND audit=1 LIMIT $number";
		}
		else
		{
			$sql .= "keyword LIKE '%$keyword%' AND audit=1 LIMIT $number";
		}
		//echo $sql;
		$this->db->query($sql);

		return $this->db->fetchAll();

	}
	/* ���� listAllArticle($catPath)
	** ���� �г�һ��������������µ�������Ϣ,�����������³���
	** ���� $catPath,Ҫ�г��ķ���
	*/
	function listAllArticle($catPath)
	{
		$sql = "SELECT title, postTime, author, comeFrom ,content,
				keyword, catPath, linkPath 
				FROM {$this->tblName}
				WHERE catPath LIKE '$catPath%' ";
		//echo $sql;
		$this->db->query($sql);

		return $this->db->fetchAll();
	}
	/* ���� totalArticle($condition)
	** ���� ȡ��ָ����ѯ�����ļ�¼����
	** ���� $condition��ѯ��������
	*/
	function totalArticle($condition)
	{

		$sql = "SELECT count(*) AS totalArticle
				FROM {$this->tblName} ";

		$tmp = "";
		if(isset($condition['audit']))	//���Ƿ��������
		{
			$tmp .= "WHERE audit = {$condition['audit']} ";
		}
		if(!empty($condition['catPath']))	//����������
		{
			$tmp .= (empty($tmp)?"WHERE":"AND")." catPath LIKE '{$condition['catPath']}%' ";
		}
		if(!empty($condition['keyword']))	//���ؼ�������
		{
			$tmp .= (empty($tmp)?"WHERE":"AND")." keyword LIKE '%{$condition['keyword']}%' ";
		}

		$sql .= $tmp;
		//echo $sql;
		$this->db->query($sql);
		$this->db->fetchRow();	

		return $this->db->getValue("totalArticle");	
	}
	/* ���� getPathByID($id)
	** ���� �������µ�ID��ID����ȡ�����µ�����·��
	** ���� $id ���µ�ID��ID����
	** ���� ����·�����ַ���������
	*/
	function getPathByID($id)
	{
		$sql = "SELECT linkPath FROM {$this->tblName} WHERE id ";
		if(is_array($id)) 
		{
			$sql .= "IN (" . join(",", $id) . ")";
			$this->db->query($sql);
			return $this->db->fetchAll();
		}
		else
		{
			$sql .= "= $id";
			$this->db->query($sql);
			$this->db->fetchRow();
			return $this->db->getValue("linkPath");
		}
	}
}

?>
