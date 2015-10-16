<?php
//====================================================
//		FileName:sysInfo.php
//		Summary: 显示系统信息
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

//取出MYSQL版本
$mysqlVersion = $db->getVersion();
//取出相关配置信息
if(@ini_get("file_uploads"))
{
    $fileUpload = "允许 | 文件:".ini_get("upload_max_filesize")." | 表单：".ini_get("post_max_size");
}
else
{
    $fileUpload = "<span class=\"red-font\">禁止</span>";
}

//取得系统占用数据库空间大小
$dbsize = $db->getDBSize(DB_NAME, CMS_PREFIX);
$dbsize = $dbsize ? sizeCount($dbsize) : "未知";

$tpl = new SmartTemplate("admin/sysInfo.htm");
$systemInfo = array("webServer"	=> PHP_OS . " | " .$_SERVER["SERVER_SOFTWARE"],
					"domain"	=> $_SERVER["SERVER_NAME"],
					"phpVer"	=> PHP_VERSION,
					"mysqlVer"	=> $mysqlVersion,
					"upload"	=> $fileUpload,
					"dbsize"	=> $dbsize
					);
$tpl->assign($systemInfo);
$tpl->assign("queryTime", $db->getQueryTimes());
$tpl->assign("executeTime", $timer->getExecuteTime());

$tpl->output();
?>

