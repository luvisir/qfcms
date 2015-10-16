<?php
//====================================================
//		FileName:IBinit.php
//		Summary: 系统初始化,包含核心类文件
//		
//====================================================
require_once(dirname(__FILE__) . "/config.php");
require_once(CMS_ROOT . "function.php");
require_once(CMS_CLASS_PATH . "mysql.inc.php");
require_once(CMS_CLASS_PATH . "GDImage.inc.php");
require_once(CMS_CLASS_PATH . "fileSystem.inc.php");
require_once(CMS_CLASS_PATH . "class.smarttemplate.php");
require_once(CMS_CLASS_PATH . "baseLogic.inc.php");
require_once(CMS_CLASS_PATH . "category.inc.php");
require_once(CMS_CLASS_PATH . "user.inc.php");
require_once(CMS_CLASS_PATH . "article.inc.php");
require_once(CMS_CLASS_PATH . "picture.inc.php");
require_once(CMS_CLASS_PATH . "validate.inc.php");
require_once(CMS_CLASS_PATH . "timer.inc.php");

//初始化数据库连接
$db	 = new mysql(DB_HOST,DB_USER,DB_PWD,DB_NAME);
//初始化计时器
$timer = new timer;
?>
