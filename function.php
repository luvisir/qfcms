<?php
//====================================================
//		FileName:vfunction.php
//		Summary: 包含系统经常使用的一些函数
//		
//====================================================

/* 函数 cnString($text, $length)
** 功能 从文本中截取指定长度字符串
** 参数 $text 要截取的文本
** 参数 $length 要截取的字符串长度
*/
function cnString($text, $length)
{
	if(strlen($text) <= $length) 
	{
		return $text;
	}

	$str = substr($text, 0, $length) . chr(0) . "…"; 
	return $str;
}
/* 函数 textFilter($text)
** 功能 将文本中的特殊字符进行过滤,如HTML标记和换行符
** 参数 要进行过滤的文本
*/
function textFilter($text)
{
	$text = htmlspecialchars($text);
	$text = nl2br($text);
	return $text;
}

//==========================================
// 函数: alert
// 功能: JavaScript提示
// 参数: $title 要提示的内容
// 参数: $action 提示后的动作
//		back 返回 close 关闭窗口
//		replace 替换页面 redirect 跳转
// 参数: $href 当action为redirect时的URL
//==========================================
function alert($title,$action='back',$href=null) 
{
	$htmlStr  = "<script language='javascript'>";
	$htmlStr .= "alert('$title');";
	switch ($action) {
		case 'back':
			$htmlStr .= "history.back();";
			break;
		case 'close':
			$htmlStr .= "window.close();";
			break;
		case 'replace':
			$htmlStr .= "location.replace(location.href);";
			break;
		case 'redirect':
			if (!empty($href)) 
			{
				$htmlStr .= "location.href='$href'";
			}
			break;
		default:
			break;
	}
	$htmlStr .= "</script>";
	echo $htmlStr;
}

/* 函数: function sizeCount($byte)
** 功能: 根据字节数转换成相应的单位
** 参数: $byte 字节数字
** 返回: 转换后单位的字符串(如1.34K,2.30M)
*/
function sizeCount($byte)
{
    if($byte >= 1073741824)
    {
        $byte = round($byte / 1073741824, 2) . " G";
    }
    elseif($byte >= 1048576)
    {
        $byte = round($byte / 1048576, 2) . " M";
    }
    elseif($byte >= 1024)
    {
        $byte = round($byte / 1024, 2) . " K";
    }
    else
    {
        $byte = $byte . " bytes";
    }
    return $byte;
}

/* 函数: checkAction($value)
** 功能: 检查action的值
** 参数: $value 值
*/
function checkAction($value)
{
	if (!empty($_REQUEST['action']) && $_REQUEST['action'] == $value)
		return true;
	else
		return false;
}
/* 函数: forward($url,$param)
** 功能: 跳转到其它页面
** 参数: $url 页面地址
** 参数: $param 关联数组,可选
*/
function forward($url, $param=null)
{
	$headerStr = "Location: $url";
	$paramStr = "";
	if($param != null && is_array($param))
	{
		$paramStr = "?";
		$flag = 0;
		foreach($param as $key=>$val)
		{
			if($flag == 0)
			{
				$paramStr .= "$key=$val";
				$flag = 1;
			}
			else
				$paramStr .= "&$key=$val";
		}

	}

	header($headerStr . $paramStr);
}

/* 函数: showMessage()
** 功能: 显示信息页面
** 参数: 无
*/
function showMessage()
{
	global $errorList, $successList;
	//处理转向操作
	if(!empty($errorList))
	{
		$param['msgList'] = serialize($errorList);
		$param["msgType"] = "error-msg";
	}
	else
	{
		$param['msgList'] = serialize($successList);
		$param["msgType"] = "success-msg";
	}
	forward("message.php", $param);
	exit();
}

/* 函数 makePage($pageParam)
** 功能 显示分页列表
** 参数 $pageParam 实现分页需要的参数数组
**      $pageParam['currentPage'] 当前页
**      $pageParam['pageCount'] 总页数
**      $pageParam['recordCount'] 记录总数
** 返回 生成的HTML代码
*/
function makePage($pageParam)
{
	$prePage	= $pageParam['currentPage'] - 1;
	$nextPage	= $pageParam['currentPage'] + 1;
	$firstPage	= 1;
	$lastPage	= $pageParam['pageCount'];
	$currentPage= $pageParam['currentPage'];
	$totalPage	= $pageParam['pageCount'];
	$pageStr	= $pageParam['recordCount'] . " 条记录 ";
	$pageStr   .= $pageParam['currentPage'] . "/" . $totalPage . " ";
	//根据当前URL生成新的URL链接
	if(empty($_SERVER['QUERY_STRING']))
	{
		$url = $_SERVER['REQUEST_URI'] . "?page=";
	}
	else
	{
		if(isset($_GET['page']))
		{
			$url = preg_replace("|page.+|", "page=", $_SERVER['REQUEST_URI']);
		}
		else
		{
			$url = $_SERVER['REQUEST_URI'] . "&page="; 
		}
	}
	//生成HTML代码
	if($currentPage > 1)
	{
		$pageStr .= "<a href=\"".$url.$firstPage."\"><img src=\"".APP_PATH."images/first.gif\" alt=\"首页\" /></a>\n";
		$pageStr .= "<a href=\"".$url.$prePage."\"><img src=\"".APP_PATH."images/previous.gif\" alt=\"上一页\" /></a>\n";
	}
	else
	{
		$pageStr .= "<img src=\"".APP_PATH."images/first.gif\" alt=\"首页\" />\n";
		$pageStr .= "<img src=\"".APP_PATH."images/previous.gif\" alt=\"上一页\" />\n";
	}
	if($currentPage < $totalPage)
	{
		$pageStr .= "<a href=\"".$url.$nextPage."\"><img src=\"".APP_PATH."images/next.gif\" alt=\"下一页\" /></a>\n";
		$pageStr .= "<a href=\"".$url.$lastPage."\"><img src=\"".APP_PATH."images/last.gif\" alt=\"未页\" /></a>\n";
	}
	else
	{
		$pageStr .= "<img src=\"".APP_PATH."images/next.gif\" alt=\"下一页\" />\n";
		$pageStr .= "<img src=\"".APP_PATH."images/last.gif\" alt=\"未页\" />\n";
	}

	return $pageStr;
}
?>