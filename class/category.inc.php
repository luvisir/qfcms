<?php
//====================================================
//		FileName:		category.inc.php
//		Summary:		category business logic layer
//		Author:			
//		CreateTime:		2004-10-08     
//		LastModifed:	2004-11-04
//		
//		======== ★★原理说明★★ =======================
//		分类使用catID,catPath,catTitle,description四个字段	
//		catID为结点ID，catPath为结点的路径
//		catTitle为结点标题,description为结点描述
//		$homeID为根结点，在数据库中有且只有一个catPath为0的结点
//		为根结点,树型结构如下:
//		+-------+---------+----------+
//		| catID | catPath | catTitle |
//		+-------+---------+----------+
//		|     1 | 0       | 栏目首页  |
//		|     2 | 0,1     | 新闻		 |
//		|     3 | 0,1     | 论坛		 |	
//		|     4 | 0,1     | 教育		 |
//		|     5 | 0,1,2   | 国内新闻  |
//		|     6 | 0,1,2   | 国际新闻  |
//		|     7 | 0,1,2   | 企业新闻  |
//		+-------+---------+----------+
//		排序时使用concat(catPath,',',catID) AS absPath 即为如下
//		+---------+-------+---------+----------+
//		| absPath | catID | catPath | catTitle |
//		|---------+-------+---------+----------+
//		| 0,1     |     1 | 0       | 栏目首页	|
//		| 0,1,2   |     2 | 0,1     | 新闻		|
//		| 0,1,2,5 |     5 | 0,1,2   | 国内新闻	|
//		| 0,1,2,6 |     6 | 0,1,2   | 国际新闻	|
//		| 0,1,2,7 |     7 | 0,1,2   | 企业新闻	|
//		| 0,1,3   |     3 | 0,1     | 论坛		|
//		| 0,1,4   |     4 | 0,1     | 教育		|
//		+---------+-------+---------+-----------+
//		对结点的各种操作都使用absPath,可减少数据库的查询
//==================================================== 

class category
{
/*///数据库字段描述
	var $catID;					//分类ID
	var $catPath;				//分类路径
	var $catTitle;				//分类标题
	var $description;			//分类描述
	var $catImage;				//分类的图片
//*/
	var $homeAbsPath = "0,1";	//根的绝对路径
	var $record;				//数据结果集
	var $tblName;				//数据表的名称
	var $db;					//mysql 类的实例
	
	/* 函数 category($db)
	** 功能 构造函数
	** 参数 $db mysql类的实例
	*/
	function category($db, $type="cat")
	{
		$this->db = $db;
		$this->tblName = CMS_PREFIX . $type;
	}
	
	/* 函数 add($parentAbsPath, $catTitle, $description, $catImage)
	** 功能 添加一个结点
	** 参数 $parentAbsPath 要添加子结点的绝对路径（为catPath+catID)
	** 参数 $catTitle 节点标题
	** 参数 $description 节点描述信息
	** 返回 刚添加的结点的absPath
	*/
	function add($parentAbsPath, $catTitle, $description, $catImage)
	{	
		//将特殊字符转义
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

	/* 函数 remove($absPath)
	** 功能 删除结点及所有该节点以下的子节点
	** 参数 $absPath 节点的绝对路径
	** 返回 -1 删除的为根分类,0失败 成功返回删除的分类个数
	*/
	function remove($absPath)
	{
		//判断是否为根
		if($absPath == $this->homeAbsPath)
			return -1;
		//根据absPath取出catPath
		$catID = $this->getCatID($absPath);
		$sql = "DELETE FROM {$this->tblName}
				WHERE catID = '$catID' OR catPath LIKE '$absPath%'";
		//echo $sql;
		$this->db->query($sql);

		return $this->db->affectedRows();
	}

	/* 函数 
	($absPath, $catTitle, $description)
	** 功能 更改节点信息
	** 参数 $absPath 节点的绝对路径
	** 参数 $catTitle 节点标题
	** 参数 $description 节点描述
	** 返回 -1 失败 成功返回修改的记录数
	*/
	function setNode($absPath, $catTitle, $description, $catImage)
	{
		//将特殊字符转义
		if (!get_magic_quotes_gpc())
		{
			$catTitle	 = addslashes($catTitle);
			$description = addslashes($description);
			$catImage	 = addslashes($catImage);
		}

		//根据absPath取出catID
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

	/* 函数 moveTo($fromAbsPath, $toAbsPath) 
	** 功能 移动节点
	** 参数 $fromAbsPath 要移动的节点的绝对路径
	** 参数 $toAbsPath 要移到的位置
	** 返回 -1 目标结点为源结点的子节点
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

	/* 函数 getNode($absPath)
	** 功能 取得结点的信息(标题,描述等)
	** 参数 $absPath 节点的绝对路径
	** 返回 array 结点信息数组
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
	
	/* 函数 getParent($absPath)
	** 功能 取得兄弟结点的信息
	** 参数 $absPath 节点的绝对路径
	** 返回 兄弟结点数组
	*/
	function getSibling($absPath)
	{
		//根据absPath取出catPath
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
	/* 函数 getParent($absPath)
	** 功能 取得父结点的信息
	** 参数 $absPath 节点的绝对路径
	** 返回 父结点的ID
	*/
	function getParent($absPath) 
	{
		//根据absPath取出catPath
		$catPath = $this->getCatPath($absPath);
		//父结点的absPath即为子节点的catPath
		$parentID = $this->getCatID($catPath);
		return $parentID;
	}

	/* 函数 getAllParent($absPath)
	** 功能 取得节点的所有父结点
	** 参数 $absPath 节点的绝对路径
	*/
	function getAllParent($absPath)
	{
		//根据absPath取出catPath
		$catPath = $this->getCatPath($absPath);
		$sql = "SELECT concat(catPath,',',catID) AS absPath, catID, catPath, catTitle
				FROM {$this->tblName}
				WHERE catID in ($catPath)";
		$this->db->query($sql);
		$this->record = $this->db->fetchAll();

		return $this->record;
	}

	/* 函数 getChild($absPath)
	** 功能 取得节点的所有子结点
	** 参数 $absPath 节点的绝对路径
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

	/* 函数 getTree($absPath)
	** 功能 取得节点的所有子孙结点
	** 参数 $absPath 节点的绝对路径
	*/
	function getTree($absPath=null)
	{
		if($absPath == null)
			$absPath = $this->homeAbsPath;
		//取得catPath
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

	/* 函数 getHome()
	** 功能 取得根节点信息
	** 参数 无
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

	/* 函数 getCatID($absPath)
	** 功能 根据结点的绝对路径取得节点ID
	** 参数 $absPath 节点的绝对路径
	*/
	function getCatID($absPath) 
	{
		return 	substr($absPath, strrpos($absPath, ',')+1);
	}
	/* 函数 getCatPath($absPath)
	** 功能 根据结点的绝对路径取得节点catPath
	** 参数 $absPath 节点的绝对路径
	*/
	function getCatPath($absPath) 
	{
		return 	substr($absPath, 0, strrpos($absPath, ','));
	}

	/* 函数 buildSelect($name,$default)
	** 功能 根据数据构建分类的下拉框
	** 参数 $name: select的名称
	** 参数 $default: 默认选中的选项
	** 参数 $attrArray 属性数组
	** 返回 html代码
	** 说明 数据将使用SQL查出的数据
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

	/* 函数 listCat($condition)
	** 功能 根据条件数组列出分类记录
	** 参数 $condition 条件数组
	** 返回 排序后的记录数组
	*/
	function listCat($condition) {

		$sql = "SELECT concat(catPath,',',catID) AS absPath, catID, catPath, catTitle, description
				FROM {$this->tblName} ";
		$tmp = "";
		//用于搜索文章分类标题
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

	/* 函数 parseTree()
	** 功能 给分类标题实现缩进效果,给标题增加链接,并将分类描述处理为短字符
	** 参数 $type "cat"为文章分类 "album"为相册
	** 返回 缩进后的记录集
	*/
	function parseTree() {

		if($this->tblName == CMS_PREFIX . "cat")
		{
			$linkURL   = "listArticle.php";
			$hint = "点击查看该分类的文章";
		}
		else
		{
			$linkURL   = "listPic.php";
			$hint = "点击查看该相册的图片";
		}
		$recordList = $this->record;
		$count = count($recordList);
		//没有子分类
		if($count == 0)
		{
			return 0;
		}
		//根据根分类进行缩进
		$rootIndent = count(explode(",",$recordList[0]['catPath']))-1;
		for($i=0; $i<$count; $i++)
		{
			$absIndent  = count(explode(",",$recordList[$i]['catPath']))-1;
			//应该缩进的长度
			$indentLen  = $absIndent - $rootIndent;
			$indentStr  = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$indentLen);
			$indentStr .= "<img src=\"../images/cat_logo.gif\" alt=\"bulletin\" /> ";
			$linkTitle  = "<a href=\"" . $linkURL . "?catPath=". $recordList[$i]['absPath']."\" title=\"".$hint."\">" . $recordList[$i]['catTitle'] . "</a>";
			$recordList[$i]['catTitle'] = $indentStr . $linkTitle;

			//不需要进行处理描述时可以去掉下面两行
			if(!empty($recordList[$i]['description'])) 
			{
				$recordList[$i]['description'] = cnString($recordList[$i]['description'], 20);
			}
			else
			{
				$recordList[$i]['description'] = "无";	//占位符
			}
		}
		return $recordList;
	}

	/* 函数 makeNavigator($admin=false)
	** 功能 根据记录集生成导航链接
	** 参数 $admin,判断是前台还是后台需要生成导航条
	** 返回 生成的HTML代码
	*/
	function makeNavigator($admin=false)
	{
		if($this->tblName == CMS_PREFIX . "cat") //文章分类
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