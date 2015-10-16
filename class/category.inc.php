<?php
//====================================================
//		FileName:		category.inc.php
//		Summary:		category business logic layer
//		Author:			
//		CreateTime:		2004-10-08     
//		LastModifed:	2004-11-04
//		
//		======== ���ԭ��˵����� =======================
//		����ʹ��catID,catPath,catTitle,description�ĸ��ֶ�	
//		catIDΪ���ID��catPathΪ����·��
//		catTitleΪ������,descriptionΪ�������
//		$homeIDΪ����㣬�����ݿ�������ֻ��һ��catPathΪ0�Ľ��
//		Ϊ�����,���ͽṹ����:
//		+-------+---------+----------+
//		| catID | catPath | catTitle |
//		+-------+---------+----------+
//		|     1 | 0       | ��Ŀ��ҳ  |
//		|     2 | 0,1     | ����		 |
//		|     3 | 0,1     | ��̳		 |	
//		|     4 | 0,1     | ����		 |
//		|     5 | 0,1,2   | ��������  |
//		|     6 | 0,1,2   | ��������  |
//		|     7 | 0,1,2   | ��ҵ����  |
//		+-------+---------+----------+
//		����ʱʹ��concat(catPath,',',catID) AS absPath ��Ϊ����
//		+---------+-------+---------+----------+
//		| absPath | catID | catPath | catTitle |
//		|---------+-------+---------+----------+
//		| 0,1     |     1 | 0       | ��Ŀ��ҳ	|
//		| 0,1,2   |     2 | 0,1     | ����		|
//		| 0,1,2,5 |     5 | 0,1,2   | ��������	|
//		| 0,1,2,6 |     6 | 0,1,2   | ��������	|
//		| 0,1,2,7 |     7 | 0,1,2   | ��ҵ����	|
//		| 0,1,3   |     3 | 0,1     | ��̳		|
//		| 0,1,4   |     4 | 0,1     | ����		|
//		+---------+-------+---------+-----------+
//		�Խ��ĸ��ֲ�����ʹ��absPath,�ɼ������ݿ�Ĳ�ѯ
//==================================================== 

class category
{
/*///���ݿ��ֶ�����
	var $catID;					//����ID
	var $catPath;				//����·��
	var $catTitle;				//�������
	var $description;			//��������
	var $catImage;				//�����ͼƬ
//*/
	var $homeAbsPath = "0,1";	//���ľ���·��
	var $record;				//���ݽ����
	var $tblName;				//���ݱ������
	var $db;					//mysql ���ʵ��
	
	/* ���� category($db)
	** ���� ���캯��
	** ���� $db mysql���ʵ��
	*/
	function category($db, $type="cat")
	{
		$this->db = $db;
		$this->tblName = CMS_PREFIX . $type;
	}
	
	/* ���� add($parentAbsPath, $catTitle, $description, $catImage)
	** ���� ���һ�����
	** ���� $parentAbsPath Ҫ����ӽ��ľ���·����ΪcatPath+catID)
	** ���� $catTitle �ڵ����
	** ���� $description �ڵ�������Ϣ
	** ���� ����ӵĽ���absPath
	*/
	function add($parentAbsPath, $catTitle, $description, $catImage)
	{	
		//�������ַ�ת��
		if (!get_magic_quotes_gpc())
		{
			$catTitle = addslashes($catTitle);
			$description = addslashes($description);
		}
		$sql = "INSERT INTO {$this->tblName} 
				VALUES(null,'$parentAbsPath','$catTitle','$description', '$catImage')";
		$this->db->query($sql);

		return $parentAbsPath . "," . $this->db->insertID();
	}

	/* ���� remove($absPath)
	** ���� ɾ����㼰���иýڵ����µ��ӽڵ�
	** ���� $absPath �ڵ�ľ���·��
	** ���� -1 ɾ����Ϊ������,0ʧ�� �ɹ�����ɾ���ķ������
	*/
	function remove($absPath)
	{
		//�ж��Ƿ�Ϊ��
		if($absPath == $this->homeAbsPath)
			return -1;
		//����absPathȡ��catPath
		$catID = $this->getCatID($absPath);
		$sql = "DELETE FROM {$this->tblName}
				WHERE catID = '$catID' OR catPath LIKE '$absPath%'";
		//echo $sql;
		$this->db->query($sql);

		return $this->db->affectedRows();
	}

	/* ���� 
	($absPath, $catTitle, $description)
	** ���� ���Ľڵ���Ϣ
	** ���� $absPath �ڵ�ľ���·��
	** ���� $catTitle �ڵ����
	** ���� $description �ڵ�����
	** ���� -1 ʧ�� �ɹ������޸ĵļ�¼��
	*/
	function setNode($absPath, $catTitle, $description, $catImage)
	{
		//�������ַ�ת��
		if (!get_magic_quotes_gpc())
		{
			$catTitle	 = addslashes($catTitle);
			$description = addslashes($description);
			$catImage	 = addslashes($catImage);
		}

		//����absPathȡ��catID
		$catID = $this->getCatID($absPath);
		$sql = "UPDATE {$this->tblName}
				SET catTitle	= '$catTitle', 
					description = '$description',
					catImage	= '$catImage'
				WHERE catID = '$catID'";
		//echo $sql;
		if($this->db->query($sql))
			return $this->db->affectedRows();
		else
			return -1;
	}

	/* ���� moveTo($fromAbsPath, $toAbsPath) 
	** ���� �ƶ��ڵ�
	** ���� $fromAbsPath Ҫ�ƶ��Ľڵ�ľ���·��
	** ���� $toAbsPath Ҫ�Ƶ���λ��
	** ���� -1 Ŀ����ΪԴ�����ӽڵ�
	*/
	function moveTo($fromAbsPath, $toAbsPath) 
	{
		if (strpos($toAbsPath, $fromAbsPath) === false)
		{
			$fromCatPath = $this->getCatPath($fromAbsPath);
			$fromCatID	 = $this->getCatID($fromAbsPath);
			$sql = "UPDATE {$this->tblName} 
					SET catPath = REPLACE(catPath, '$fromCatPath', '$toAbsPath') 
					WHERE catID = $fromCatID OR catPath LIKE '$fromAbsPath%'";
			//echo $sql;
			$this->db->query($sql);
			return $this->db->affectedRows();
			//*/
		}
		else
			return -1;
	}

	/* ���� getNode($absPath)
	** ���� ȡ�ý�����Ϣ(����,������)
	** ���� $absPath �ڵ�ľ���·��
	** ���� array �����Ϣ����
	*/
	function getNode($absPath)
	{
		$catID = $this->getCatID($absPath);
		$sql = "SELECT concat(catPath,',',catID) AS absPath, catID, catPath, catTitle, description, catImage
				FROM {$this->tblName}
				WHERE catID = '$catID'";
		$this->db->query($sql);
		$this->record = $this->db->fetchRow();
		return $this->record;
	}
	
	/* ���� getParent($absPath)
	** ���� ȡ���ֵܽ�����Ϣ
	** ���� $absPath �ڵ�ľ���·��
	** ���� �ֵܽ������
	*/
	function getSibling($absPath)
	{
		//����absPathȡ��catPath
		$catPath = $this->getCatPath($absPath);
		$sql = "SELECT concat(catPath,',',catID) AS absPath, catID, catPath, catTitle
				FROM {$this->tblName}
				WHERE catPath = '$catPath'
				ORDER BY catID ";
		//echo $sql;
		$this->db->query($sql);
		$this->record = $this->db->fetchAll();

		return $this->record;
	}
	/* ���� getParent($absPath)
	** ���� ȡ�ø�������Ϣ
	** ���� $absPath �ڵ�ľ���·��
	** ���� ������ID
	*/
	function getParent($absPath) 
	{
		//����absPathȡ��catPath
		$catPath = $this->getCatPath($absPath);
		//������absPath��Ϊ�ӽڵ��catPath
		$parentID = $this->getCatID($catPath);
		return $parentID;
	}

	/* ���� getAllParent($absPath)
	** ���� ȡ�ýڵ�����и����
	** ���� $absPath �ڵ�ľ���·��
	*/
	function getAllParent($absPath)
	{
		//����absPathȡ��catPath
		$catPath = $this->getCatPath($absPath);
		$sql = "SELECT concat(catPath,',',catID) AS absPath, catID, catPath, catTitle
				FROM {$this->tblName}
				WHERE catID in ($catPath)";
		$this->db->query($sql);
		$this->record = $this->db->fetchAll();

		return $this->record;
	}

	/* ���� getChild($absPath)
	** ���� ȡ�ýڵ�������ӽ��
	** ���� $absPath �ڵ�ľ���·��
	*/
	function getChild($absPath)
	{
		$sql = "SELECT concat(catPath,',',catID) AS absPath, catID, catPath, catTitle, catImage
				FROM {$this->tblName}
				WHERE catPath = '$absPath'
				ORDER BY catID ";
		$this->db->query($sql);
		$this->record = $this->db->fetchAll();

		return $this->record;
	}

	/* ���� getTree($absPath)
	** ���� ȡ�ýڵ������������
	** ���� $absPath �ڵ�ľ���·��
	*/
	function getTree($absPath=null)
	{
		if($absPath == null)
			$absPath = $this->homeAbsPath;
		//ȡ��catPath
		$catPath = $this->getCatPath($absPath);
		$sql = "SELECT concat(catPath,',',catID) AS absPath, catID, catPath, catTitle
				FROM {$this->tblName}
				WHERE catPath = '$catPath' OR catPath LIKE '$absPath%'
				ORDER BY absPath, catID ";
		//echo $sql;
		$this->db->query($sql);
		$this->record = $this->db->fetchAll();

		return $this->record;
	}

	/* ���� getHome()
	** ���� ȡ�ø��ڵ���Ϣ
	** ���� ��
	*/
	function getHome() 
	{
		$sql = "SELECT concat(catPath,',',catID) AS absPath, catID, catPath, catTitle 
				FROM  {$this->tblName}
				WHERE catPath = 0";
		$this->db->query($sql);
		if ($this->db->recordCount()) 
		{
			$this->record = $this->db->fetchRow();
			return $this->record;
		}
		else
			return 0;
	}

	/* ���� getCatID($absPath)
	** ���� ���ݽ��ľ���·��ȡ�ýڵ�ID
	** ���� $absPath �ڵ�ľ���·��
	*/
	function getCatID($absPath) 
	{
		return 	substr($absPath, strrpos($absPath, ',')+1);
	}
	/* ���� getCatPath($absPath)
	** ���� ���ݽ��ľ���·��ȡ�ýڵ�catPath
	** ���� $absPath �ڵ�ľ���·��
	*/
	function getCatPath($absPath) 
	{
		return 	substr($absPath, 0, strrpos($absPath, ','));
	}

	/* ���� buildSelect($name,$default)
	** ���� �������ݹ��������������
	** ���� $name: select������
	** ���� $default: Ĭ��ѡ�е�ѡ��
	** ���� $attrArray ��������
	** ���� html����
	** ˵�� ���ݽ�ʹ��SQL���������
	*/
	function buildSelect($name, $default=null,$attrArray=null) 
	{
		$option = $this->record;

		if (!is_array($option) || empty($option)) 
			exit("buildSelect error:the option is not an array or the array is empty");
		
		$htmlStr = "<select name=\"$name\" id=\"$name\"";
		$attrStr = " ";
		if(!empty($attrArray) && is_array($attrArray))
		{
			foreach($attrArray as $key => $value)
			{
				$attrStr .= "$key=\"$value\" ";
			}
		}
		$htmlStr .= $attrStr . ">";

		foreach($option as $key => $value)
		{
			if ($value['absPath'] == $this->homeAbsPath && $default == null) 
			{
				$catTitle = "<" . $value['catTitle'] . ">";
				$htmlStr .= "<option value=\"{$value['absPath']}\" selected=\"selected\">$catTitle</option>";					
			}
			elseif ($value['catID'] == $default) 
			{
				$catTitle = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",count(explode(",",$value['catPath']))-1) . "-&nbsp;" .  $value['catTitle'];
				$htmlStr .= "<option value=\"{$value['absPath']}\" selected=\"selected\">$catTitle</option>";
			}
			else
			{
				$catTitle = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",count(explode(",",$value['catPath']))-1) . "-&nbsp;" . $value['catTitle'];
				$htmlStr .= "<option value=\"{$value['absPath']}\">$catTitle</option>";
			}
		}
		$htmlStr .= "</select>";
		return $htmlStr;
	}

	/* ���� listCat($condition)
	** ���� �������������г������¼
	** ���� $condition ��������
	** ���� �����ļ�¼����
	*/
	function listCat($condition) {

		$sql = "SELECT concat(catPath,',',catID) AS absPath, catID, catPath, catTitle, description
				FROM {$this->tblName} ";
		$tmp = "";
		//�����������·������
		if(!empty($condition["catTitle"]))
			$tmp .= "WHERE catTitle Like %{$condition['catTitle']}% ";
		if(!empty($condition['absPath']))
		{
			$tmp .= (empty($tmp)?"WHERE":"AND")." catPath LIKE '{$condition['absPath']}%' ";
		}
		$tmp .=	"ORDER BY absPath ";
		if (isset($condition['start']) && isset($condition['rows']))
		  	$tmp .= "LIMIT ".$condition['start'].", ".$condition['rows'];

		$sql .= $tmp;
		//echo $sql;

		$this->db->query($sql);
		$this->record = $this->db->fetchAll();

		return $this->record;
		
	}

	/* ���� parseTree()
	** ���� ���������ʵ������Ч��,��������������,����������������Ϊ���ַ�
	** ���� $type "cat"Ϊ���·��� "album"Ϊ���
	** ���� ������ļ�¼��
	*/
	function parseTree() {

		if($this->tblName == CMS_PREFIX . "cat")
		{
			$linkURL   = "listArticle.php";
			$hint = "����鿴�÷��������";
		}
		else
		{
			$linkURL   = "listPic.php";
			$hint = "����鿴������ͼƬ";
		}
		$recordList = $this->record;
		$count = count($recordList);
		//û���ӷ���
		if($count == 0)
		{
			return 0;
		}
		//���ݸ������������
		$rootIndent = count(explode(",",$recordList[0]['catPath']))-1;
		for($i=0; $i<$count; $i++)
		{
			$absIndent  = count(explode(",",$recordList[$i]['catPath']))-1;
			//Ӧ�������ĳ���
			$indentLen  = $absIndent - $rootIndent;
			$indentStr  = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$indentLen);
			$indentStr .= "<img src=\"../images/cat_logo.gif\" alt=\"bulletin\" /> ";
			$linkTitle  = "<a href=\"" . $linkURL . "?catPath=". $recordList[$i]['absPath']."\" title=\"".$hint."\">" . $recordList[$i]['catTitle'] . "</a>";
			$recordList[$i]['catTitle'] = $indentStr . $linkTitle;

			//����Ҫ���д�������ʱ����ȥ����������
			if(!empty($recordList[$i]['description'])) 
			{
				$recordList[$i]['description'] = cnString($recordList[$i]['description'], 20);
			}
			else
			{
				$recordList[$i]['description'] = "��";	//ռλ��
			}
		}
		return $recordList;
	}

	/* ���� makeNavigator($admin=false)
	** ���� ���ݼ�¼�����ɵ�������
	** ���� $admin,�ж���ǰ̨���Ǻ�̨��Ҫ���ɵ�����
	** ���� ���ɵ�HTML����
	*/
	function makeNavigator($admin=false)
	{
		if($this->tblName == CMS_PREFIX . "cat") //���·���
		{
			if($admin)
			{
				$url = APP_PATH. "admin/listArticle.php";
			}
			else
			{
				$url = APP_PATH. "listArticle.php";
			}
			
		}
		else
		{
			if($admin)
			{
				$url = APP_PATH. "admin/listPic.php";
			}
			else
			{
				$url = APP_PATH. "listPic.php";
			}
		}
		$htmlStr = "";
		foreach($this->record as $cat)
		{
			$htmlStr .= "<a href=\"$url?catPath={$cat['absPath']}\">" . $cat['catTitle'] . "</a>->\n";
		}

		return $htmlStr;
	}
}
	
?>