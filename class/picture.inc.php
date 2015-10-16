<?php
//====================================================
//		FileName:picture.inc.php
//		Summary: 图片数据操作类
//		
//====================================================

class picture extends baseLogic
{
	/*////数据库字段说明
	var $id;	
	var $picTitle;		//图片标题
	var $description;	//图片描述
	var $picName;		//图片实际名称
	var $catPath;		//图片相册路径
	var $hasThumb;		//是否有缩略图
	var $hasMark;		//是否有水印图
	//*/

	/* 函数 picture($db)
	** 功能 构造函数
	** 参数 $db mysql类
	*/
	function picture($db)
	{
		$this->baseLogic($db);
		$this->tblName = CMS_PREFIX . "picture";
		$this->albumTbl = CMS_PREFIX . "album";
		$this->fieldList = array("picTitle", "description", "picName", "catPath", "hasThumb", "hasMark");
	}

	/* 函数 addPic($postList)
	** 功能 在数据库中添加一条图片记录
	** 参数 $postList 字段值数组
	*/
	function addPic($postList)
	{
		return $this->add($postList);
	}

	/* 函数 editPic($postList)
	** 功能 编辑图片信息
	** 参数 $postList 字段值数组
	*/
	function editPic($postList)
	{
		return $this->update($postList);
	}

	/* 函数 deletePic($id)
	** 功能 删除图片在数据库中的记录
	** 参数 
	*/
	function deletePic($id)
	{
		return $this->delete($id);
	}
	
	/* 函数 deletePicByCat($catPath)
	** 功能 删除一个相册的图片
	** 参数 $catPath 相册路径
	*/
	function deletePicByCat($catPath)
	{
		$sql = "DELETE FROM {$this->tblName}
				WHERE catPath LIKE '$catPath%'";

		$this->db->query($sql);

		return $this->db->affectedRows();
	}

	/* 函数 getPicName($id)
	** 功能 根据ID或ID列表取得图片名称
	** 参数 $id 编号或数组
	** 返回 图片名称或数组
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
	/* 函数 getPicCat($catPath)
	** 功能 取得一个相册图片的名称列表
	** 参数 $catPath 相册路径
	** 返回 图片名称数组
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
	/* 函数 getPic($id)
	** 功能 取得图片的数据库信息
	** 参数 $id 图片ID
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

	/* 函数 listPic($condition)
	** 功能 显示图片列表
	** 参数 $condition 条件数组
	*/
	function listPic($condition)
	{
		$sql = "SELECT pic.id, pic.picTitle,
				pic.catPath, pic.hasThumb, pic.hasMark, album.catTitle 
				FROM {$this->tblName} AS pic, {$this->albumTbl} AS album
				WHERE pic.catPath = CONCAT(album.catPath,',',album.catID) ";
		$tmp = "";
		if(!empty($condition['catPath']))	//按相册搜索
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

		if(!empty($condition['page']) && !empty($condition['rows'])) //提取指定条数的记录
		{
			$start = ($condition['page'] - 1) * $condition['rows'];
			$tmp .= "LIMIT " . $start . ", " . $condition['rows'];
		}

		$sql .= $tmp;
		//echo $sql;
		$this->db->query($sql);
		return $this->db->fetchAll();
	}
	/* 函数 listPicThumb($condition)
	** 功能 显示图片缩略图列表
	** 参数 $condition 条件数组
	*/
	function listPicThumb($condition)
	{
		$sql = "SELECT id, picTitle, picName, hasThumb, hasMark
				FROM {$this->tblName} ";
		$tmp = "";
		if(!empty($condition['catPath']))	//按相册搜索
		{
			$tmp .= "WHERE catPath LIKE '{$condition['catPath']}%' ";
		}
		$tmp .= "ORDER BY id DESC ";

		if(!empty($condition['page']) && !empty($condition['rows'])) //提取指定条数的记录
		{
			$start = ($condition['page'] - 1) * $condition['rows'];
			$tmp .= "LIMIT " . $start . ", " . $condition['rows'];
		}

		$sql .= $tmp;
		//echo $sql;
		$this->db->query($sql);
		return $this->db->fetchAll();
	}	
	/* 函数 totalPic($condition)
	** 功能 列出指定查询条件的记录总数
	** 参数 
	*/
	function totalPic($condition)
	{
		$sql = "SELECT count(*)	AS totalPic FROM {$this->tblName} ";
		$tmp = "";
		if(!empty($condition['catPath']))	//按相册搜索
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
