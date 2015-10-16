<?php
//====================================================
//		FileName:sysInfo.php
//		Summary: ��ʾϵͳ��Ϣ
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");

//ȡ��MYSQL�汾
$mysqlVersion = $db->getVersion();
//ȡ�����������Ϣ
if(@ini_get("file_uploads"))
{
    $fileUpload = "���� | �ļ�:".ini_get("upload_max_filesize")." | ����".ini_get("post_max_size");
}
else
{
    $fileUpload = "<span class=\"red-font\">��ֹ</span>";
}

//ȡ��ϵͳռ�����ݿ�ռ��С
$dbsize = $db->getDBSize(DB_NAME, CMS_PREFIX);
$dbsize = $dbsize ? sizeCount($dbsize) : "δ֪";

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

