<?php
//====================================================
//		FileName:IBinit.php
//		Summary: ϵͳ��ʼ��,�����������ļ�
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

//��ʼ�����ݿ�����
$db	 = new mysql(DB_HOST,DB_USER,DB_PWD,DB_NAME);
//��ʼ����ʱ��
$timer = new timer;
?>
