<?php
//====================================================
//		FileName:config.php
//		Summary: 整个IB系统的配置文件,一些参数的设置. 
//		
//====================================================

//数据库部分参数设置
@define("DB_HOST", "localhost");	//数据库主机名
@define("DB_USER", "stone");		//数据库用户名
@define("DB_PWD", "format999");		//数据库密码
@define("DB_NAME", "CMS_news");		//数据库名称

//框架路径设置
@define("CMS_ROOT", dirname(__FILE__) . "/");			//系统根目录
@define("CMS_CLASS_PATH", CMS_ROOT . "class/");			//系统核心CLASS路径
@define("CMS_TEMPLATE_PATH", CMS_ROOT . "templates/");	//系统模板路径
@define("CMS_UPLOAD_PATH", CMS_ROOT . "uploadFiles/");	//系统上传文件路径
@define("CMS_TEMP", CMS_ROOT . "temp/");					//系统临时文件路径
@define("CMS_PREFIX", "CMS_");							//前缀

//定义smartTemplate相关参数		
@define("USE_SMT", true);									//是否使用smartTemplate
if (defined("USE_SMT")) 
{
	$_CONFIG['template_dir']			= CMS_TEMPLATE_PATH;
	$_CONFIG['smarttemplate_compiled']	= CMS_TEMP;
	$_CONFIG['smarttemplate_cache']		= CMS_TEMP;
	$_CONFIG['cache_lifetime']			= 60;//60*60*24; //缓存一天
}

//应用程序相关设置

//应用程序名称
@define("APP_NAME", "CMS发布系统");
//新闻系统的安装路径
@define("APP_PATH", "/");
//新闻的放置目录
@define("ARTICLE_PATH", APP_PATH . "article/");
//新闻的物理路径
@define("ARTICLE_REAL_PATH", CMS_ROOT . "article/");
//图片相册物理路径
@define("GALLERY_REAL_PATH", CMS_ROOT . "gallery/");
//图片相册存放目录
@define("GALLERY_PATH", APP_PATH . "gallery/");
//系统风格路径
@define("STYLE_PATH", APP_PATH . "style/");
//系统当前风格
@define("APP_STYLE", "default");
//后台文章每页显示的数目
@define("ARTICLE_PAGE_SIZE", 20);
//后台图片每页显示的数目
@define("PICTURE_PAGE_SIZE", 20);
//后台图片显示的方式 list 列表 thumb缩略图
@define("PICTURE_SHOW_TYPE", "list");
//系统风格数组
$styleList = array("default" => "默认风格",
				   "blue"	 => "蓝色经典");
//$定义加水印的文字
$waterText = array('cms', 'all rights reserved');
//定义生成后的大小
$pictureSize = array('maxWidth' => 500, 'maxHeight' => 500);
//定义缩略图的大小
$thumbSize = array('width' => 100, 'height' => 100);

?>