<?php
//====================================================
//		FileName:article.inc.php
//		Summary: 文章操作类
//		Author: 
//		CreateTime: 2004-10-27     
//		LastModifed:2004-11-04
//		
//====================================================

class article extends baseLogic
{
/*	//数据库字段描述
	var $id;			//文章ID
	var $title;			//文章标题
	var $summary;		//文章摘要
	var $postTime;		//发布时间
	var $author;		//作者
	var $comeFrom;		//来源
	var $content;		//内容
	var $keyword;		//关键字
	var $catPath;		//分类路径
	var $isImg;			//是否为图片新闻 0 不是 1是
	var $imgName;		//图片新闻的缩略图名称
	var $linkPath;		//新闻页面的链接地址
	var $audit;			//是否审核
	var $recommend;		//是否推荐
*/

	/* 函数 article($db)
	** 功能 构造函数
	** 参数 $db mysql类
	*/
	function article($db)
	{
		$this->baseLogic($db);
		//文章表名及文章分类表名
		$this->tblName = CMS_PREFIX . "article";
		$this->catTbl  = CMS_PREFIX . "cat";
		$this->fieldList = array("title","summary","postTime","author","comeFrom",
								 "content","keyword","catPath","isImg","imgName","linkPath","audit","recommend");
	}

	/* 函数 addArticle($postList)
	** 功能 添加文章
	** 参数 $$postList 字段和值的关联数组
	*/
	function addArticle($postList)
	{
		return $this->add($postList);	
	}

	/* 函数 editArticle($postList)
	** 功能 修改文章
	** 参数 $postList 字段和值的关联数组
	*/
	function editArticle($postList)
	{
		return $this->update($postList);
	}

	/* 函数 delArticle($id)
	** 功能 删除文章/支持多条
	** 参数 $id 文章ID或ID列表数组
	*/	
	function delArticle($id)
	{
		return $this->delete($id);	
	}

	/* 函数 delArticleByCat($catPath)
	** 功能 删除一个分类的文章
	** 参数 $catPath 文章分类路径
	*/	
	function delArticleByCat($catPath)
	{
		$sql = "DELETE FROM {$this->tblName}
				WHERE catPath LIKE '$catPath%'";
		$this->db->query($sql);
		//echo $sql;
		return $this->db->affectedRows();
	}

	/* 函数 getArticle($id)
	** 功能 根据ID取出文章内容
	** 参数 $id 文章ID
	** 返回 文章信息数组
	*/
	function getArticle($id)
	{
		$sql = "SELECT * FROM {$this->tblName} 
				WHERE id = '$id'";
		$this->db->query($sql);

		return $this->db->fetchRow();
	
	}

	/* 函数 listArticle($condition)
	** 功能 根据条件取出文章列表
	** 参数 $condition 条件数组
	** 返回 文章列表数组
	*/
	function listArticle($condition)
	{

		$sql = "SELECT art.id,art.title,art.postTime,art.catPath,
				art.summary,art.linkPath,art.audit,cat.catTitle
				FROM {$this->tblName} AS art, {$this->catTbl} AS cat
				WHERE art.catPath = CONCAT(cat.catPath,',',cat.catID) ";

		$tmp = "";
		if(isset($condition['audit']))	//按是否审核搜索
		{
			$tmp .= "AND art.audit = {$condition['audit']} ";
		}
		if(!empty($condition['catPath']))	//按分类搜索
		{
			$tmp .= "AND art.catPath LIKE '{$condition['catPath']}%' ";
		}
		if(!empty($condition['title']))	//按分类搜索
		{
			$tmp .= "AND art.title LIKE '%{$condition['title']}%' ";
		}
		if(!empty($condition['content']))	//按分类搜索
		{
			$tmp .= "AND art.content LIKE '%{$condition['content']}%' ";
		}
		if(!empty($condition['keyword']))	//按关键字搜索
		{
			$tmp .= "AND art.keyword LIKE '%{$condition['keyword']}%' ";
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
	/* 函数 auditArticle($id)
	** 功能 审核文章
	** 参数 $id 文章的ID或ID数组
	** 返回 -1失败 
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
	/* 函数 lockArticle($id)
	** 功能 锁定文章
	** 参数 $id 文章的ID或ID数组
	** 返回 0失败 
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
	/* 函数 getRecommend($catPath)
	** 功能 取出该类文章中的推荐文章
	** 参数 $catPath 文章的分类路径
	** 参数 $number 取出的文章条数
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
	/* 函数 getRelLink($keyword,$number)
	** 功能 根据关键字取得文章的相关链接
	** 参数 $keyword关键字 （多个为用,隔开的字符串)
	** 参数 $number 取出的文章条数
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
	/* 函数 listAllArticle($catPath)
	** 功能 列出一个分类的所有文章的所有信息,用于批量更新程序
	** 参数 $catPath,要列出的分类
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
	/* 函数 totalArticle($condition)
	** 功能 取得指定查询条件的记录总数
	** 参数 $condition查询条件数组
	*/
	function totalArticle($condition)
	{

		$sql = "SELECT count(*) AS totalArticle
				FROM {$this->tblName} ";

		$tmp = "";
		if(isset($condition['audit']))	//按是否审核搜索
		{
			$tmp .= "WHERE audit = {$condition['audit']} ";
		}
		if(!empty($condition['catPath']))	//按分类搜索
		{
			$tmp .= (empty($tmp)?"WHERE":"AND")." catPath LIKE '{$condition['catPath']}%' ";
		}
		if(!empty($condition['keyword']))	//按关键字搜索
		{
			$tmp .= (empty($tmp)?"WHERE":"AND")." keyword LIKE '%{$condition['keyword']}%' ";
		}

		$sql .= $tmp;
		//echo $sql;
		$this->db->query($sql);
		$this->db->fetchRow();	

		return $this->db->getValue("totalArticle");	
	}
	/* 函数 getPathByID($id)
	** 功能 根据文章的ID或ID数组取出文章的物理路径
	** 参数 $id 文章的ID或ID数组
	** 返回 包含路径的字符串或数组
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
