<?php
//====================================================
//		FileName:config.php
//		Summary: ����IBϵͳ�������ļ�,һЩ����������. 
//		
//====================================================

//���ݿⲿ�ֲ�������
@define("DB_HOST", "localhost");	//���ݿ�������
@define("DB_USER", "stone");		//���ݿ��û���
@define("DB_PWD", "format999");		//���ݿ�����
@define("DB_NAME", "CMS_news");		//���ݿ�����

//���·������
@define("CMS_ROOT", dirname(__FILE__) . "/");			//ϵͳ��Ŀ¼
@define("CMS_CLASS_PATH", CMS_ROOT . "class/");			//ϵͳ����CLASS·��
@define("CMS_TEMPLATE_PATH", CMS_ROOT . "templates/");	//ϵͳģ��·��
@define("CMS_UPLOAD_PATH", CMS_ROOT . "uploadFiles/");	//ϵͳ�ϴ��ļ�·��
@define("CMS_TEMP", CMS_ROOT . "temp/");					//ϵͳ��ʱ�ļ�·��
@define("CMS_PREFIX", "CMS_");							//ǰ׺

//����smartTemplate��ز���		
@define("USE_SMT", true);									//�Ƿ�ʹ��smartTemplate
if (defined("USE_SMT")) 
{
	$_CONFIG['template_dir']			= CMS_TEMPLATE_PATH;
	$_CONFIG['smarttemplate_compiled']	= CMS_TEMP;
	$_CONFIG['smarttemplate_cache']		= CMS_TEMP;
	$_CONFIG['cache_lifetime']			= 60;//60*60*24; //����һ��
}

//Ӧ�ó����������

//Ӧ�ó�������
@define("APP_NAME", "CMS����ϵͳ");
//����ϵͳ�İ�װ·��
@define("APP_PATH", "/");
//���ŵķ���Ŀ¼
@define("ARTICLE_PATH", APP_PATH . "article/");
//���ŵ�����·��
@define("ARTICLE_REAL_PATH", CMS_ROOT . "article/");
//ͼƬ�������·��
@define("GALLERY_REAL_PATH", CMS_ROOT . "gallery/");
//ͼƬ�����Ŀ¼
@define("GALLERY_PATH", APP_PATH . "gallery/");
//ϵͳ���·��
@define("STYLE_PATH", APP_PATH . "style/");
//ϵͳ��ǰ���
@define("APP_STYLE", "default");
//��̨����ÿҳ��ʾ����Ŀ
@define("ARTICLE_PAGE_SIZE", 20);
//��̨ͼƬÿҳ��ʾ����Ŀ
@define("PICTURE_PAGE_SIZE", 20);
//��̨ͼƬ��ʾ�ķ�ʽ list �б� thumb����ͼ
@define("PICTURE_SHOW_TYPE", "list");
//ϵͳ�������
$styleList = array("default" => "Ĭ�Ϸ��",
				   "blue"	 => "��ɫ����");
//$�����ˮӡ������
$waterText = array('cms', 'all rights reserved');
//�������ɺ�Ĵ�С
$pictureSize = array('maxWidth' => 500, 'maxHeight' => 500);
//��������ͼ�Ĵ�С
$thumbSize = array('width' => 100, 'height' => 100);

?>