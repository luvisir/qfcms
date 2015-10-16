<?php
//====================================================
//		FileName:article.php
//		Summary: ���Ź������(���,ɾ��,�޸�)
//		
//====================================================
require_once("login.php");
require_once("../IBinit.php");
require_once("parseArticle.php");	//���������õĺ���

$cat	 = new category($db, "cat");
$article = new article($db);

//����������������ɹ���Ϣ������
$errorList	 = array();
$successList = array();

if(!empty($_GET['action']))	//������ʾ��
{
	switch($_GET['action'])
	{
		case 'addArticle':		//�������

			//ȡ�����з����б�
			$cat->getTree();
			$attrArray['class'] = "text-box";
			//$attrArray['disabled'] = "true";
			$catPath = $cat->buildSelect("catPath", null, $attrArray);
			$tpl = new SmartTemplate("admin/article.htm");
			$varList = array(
								"title"			=> "�������",
								"catPath"		=> $catPath,
								"artTitle"		=> "",
								"postTime"		=> date("Y-m-d"),
								"summary"		=> "",
								"author"		=> "",
								"comeFrom"		=> "",
								"keyword"		=> "",
								"isImg"			=> 0,
								"imgShow"		=> "not-display",
								"imgName"		=> GALLERY_PATH . "no_image.gif",
								"recommend"		=> 0,
								"artContent"	=> "",
								"action"		=> "addArticle",
								"artID"			=> "",
								"linkPath"		=> "",
								"oldPath"		=> "",
								"buttonValue"	=> "�� ��"
							);
			//����ģ��
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();
			break;

		case 'editArticle':		//�����޸�
			//ȡ��������Ϣ
			$editArt = $article->getArticle($_GET['id']);
			//ȡ�����з����б�
			$cat->getTree();
			$catSelect = $cat->getCatID($editArt['catPath']);
			$attrArray['class'] = "text-box";
			
			$catPath = $cat->buildSelect("catPath", $catSelect , $attrArray);
			$tpl = new SmartTemplate("admin/article.htm");
			$imgShow = empty($editArt['isImg']) ? "not-display" : "light-row";
			$varList = array(
								"title"			=> "�����޸�",
								"catPath"		=> $catPath,
								"artTitle"		=> $editArt['title'],
								"postTime"		=> $editArt['postTime'],
								"summary"		=> $editArt['summary'],
								"author"		=> $editArt['author'],
								"comeFrom"		=> $editArt['comeFrom'],
								"keyword"		=> $editArt['keyword'],
								"isImg"			=> $editArt['isImg'],
								"imgShow"		=> $imgShow,
								"imgName"		=> $editArt['imgName'],
								"recommend"		=> $editArt['recommend'],
								"artContent"	=> transferStr($editArt['content']),
								"action"		=> "editArticle",
								"artID"			=> $editArt['id'],
								"linkPath"		=> $editArt['linkPath'],
								"oldPath"		=> $editArt['catPath'],
								"buttonValue"	=> "�� ��"
							);
			//����ģ��
			$tpl->assign($varList);
			$tpl->assign("queryTime", $db->getQueryTimes());
			$tpl->assign("executeTime", $timer->getExecuteTime());

			$tpl->output();
			break;			
		case 'auditArticle':
			$auditID = !empty($_GET['id']) ? $_GET['id'] : $_GET['idList'];
			if(empty($auditID))
			{
				$errorList[] = array("message" => "û��ѡ��Ҫ��˵�����.");
				showMessage();
			}
			$res = $article->auditArticle($auditID);
			if($res != -1) 
			{
				$successList[] = array("message" => "������˳ɹ�.");
				$successList[] = array("message" => "$res �����±����.");
			}
			else
			{
				$errorList[] = array("message" => "�������ʧ��,������.");
			}

			showMessage();
			break;
		case 'lockArticle':
			$lockID = !empty($_GET['id']) ? $_GET['id'] : $_GET['idList'];
			if(empty($lockID))
			{
				$errorList[] = array("message" => "û��ѡ��Ҫ����������.");
				showMessage();
			}
			$res = $article->lockArticle($lockID);
			if($res != -1) 
			{
				$successList[] = array("message" => "���������ɹ�.");
				$successList[] = array("message" => "$res �����±�����.");
			}
			else
			{
				$errorList[] = array("message" => "��������ʧ��,������.");
			}

			showMessage();
			break;
		case 'deleteArticle':	//����ɾ��
			//ɾ����һ���»������б�
			$delID = !empty($_GET['id']) ? $_GET['id'] : $_GET['idList'];
			if(empty($delID))
			{
				$errorList[] = array("message" => "û��ѡ��Ҫɾ��������.");
				showMessage();
			}		
			$pathList = $article->getPathByID($delID);
			//print_r($pathList);exit();
			
			//������ѭ��ɾ��
			if(is_array($pathList))
			{
				foreach($pathList as $val)
				{
					//������·��ת��������·���ٽ���ɾ��
					$linkPath = str_replace(ARTICLE_PATH, ARTICLE_REAL_PATH, $val['linkPath']);
					unlink($linkPath);
				}
			}
			else
			{
				//������·��ת��������·���ٽ���ɾ��
				$linkPath = str_replace(ARTICLE_PATH, ARTICLE_REAL_PATH, $pathList);
				unlink($linkPath);
			}
			$res = $article->delArticle($delID);

			if($res != -1) 
			{
				$successList[] = array("message" => "����ɾ���ɹ�.");
				$successList[] = array("message" => "$res �����±�ɾ��.");
			}
			else
			{
				$errorList[] = array("message" => "����ɾ��ʧ��,������.");
			}	
			
			showMessage();
			break;

		default:
			$param["message"] = "��������,������.";
			forward("error.php", $param);			
			break;
	}
}
elseif(!empty($_POST['action']))	//������ύ������ݴ���
{
	switch($_POST['action'])
	{
		case 'addArticle':	//����������µĲ���
	
			//��֤��
			validateForm();

			//���ɾ�̬ҳ��
			$linkPath = HTMLPage($_POST['catPath']); 
			//������·��ת��������·��
			$_POST['linkPath'] = str_replace(ARTICLE_REAL_PATH, ARTICLE_PATH, $linkPath);
			$_POST['audit'] = 0;
			$artID = $article->addArticle($_POST);
			if($artID)
			{
				$successList = array("message" => "������ӳɹ�.");
			}
			else
			{
				$errorList = array("message" => "�������ʧ��.");
			}
			//������ʾ��Ϣ
			showMessage();
			break;

		case 'editArticle':	//�����޸����µĲ���

			//��֤��
			validateForm();
			
			//ɾ��ԭ����ҳ��
			$oldPage = str_replace(ARTICLE_PATH, ARTICLE_REAL_PATH, $_POST['linkPath']);
			@unlink($oldPage);

			//�����µľ�̬ҳ��
			$linkPath = HTMLPage($_POST['catPath'], $oldPage); 
			//������·��ת��������·��
			$_POST['linkPath'] = str_replace(ARTICLE_REAL_PATH, ARTICLE_PATH, $linkPath);
			$res = $article->editArticle($_POST);

			if($res)
			{
				$successList = array("message" => "�����޸ĳɹ�.");
			}
			else
			{
				$errorList = array("message" => "�����޸�ʧ��.");
			}

			//��ʾ��Ϣ
			showMessage();

			break;

		default:

			$param["message"] = "��������,������.";
			forward("error.php", $param);			
			break;
	}
}
else
{
	$param["message"] = "��������,������.";
	forward("error.php", $param);	
}

//===========һЩ��װ�ĺ���============

//��֤��ҳ���ĺ���
function validateForm()
{
	//����ȫ�ֱ��� 
	global $errorList, $successList;
	if(!validate::required($_POST['title']))
	{
		$errorList[] = array("message" => "���±��ⲻ��Ϊ��.");
	}
	if(!validate::checkLength($_POST['title'], 50))
	{
		$errorList[] = array("message" => "���±��ⲻ�ܳ���50���ַ�.");
	}
	if(!validate::required($_POST['postTime']))
	{
		$errorList[] = array("message" => "����ʱ�䲻��Ϊ��.");
	}
	if(!validate::match($_POST['postTime'], "|^\d{4}-\d{2}-\d{2}$|"))
	{
		$errorList[] = array("message" => "����ʱ���ʽ����ȷ.");		
	}
	if(!validate::checkLength($_POST['summary'], 200))
	{
		$errorList[] = array("message" => "����ժҪ���ܳ���200���ַ�.");
	}
	if(!validate::required($_POST['author']))
	{
		$errorList[] = array("message" => "�������߲���Ϊ��.");
	}
	if(!validate::checkLength($_POST['author'], 30))
	{
		$errorList[] = array("message" => "�������߲��ܳ���30���ַ�.");
	}
	if(!validate::required($_POST['keyword']))
	{
		$errorList[] = array("message" => "�ؼ��ֲ���Ϊ��.");
	}
	if(!validate::checkLength($_POST['keyword'], 20))
	{
		$errorList[] = array("message" => "�ؼ��ֲ��ܳ���20���ַ�.");
	}
	if(!validate::required($_POST['content']))
	{
		$errorList[] = array("message" => "�������ݲ���Ϊ��.");
	}
	if(!empty($errorList))	//�������
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}	
}
/* ���� HTMLPage($path, $oldPage)
** ���� ��ָ��Ŀ¼���ɾ�̬ҳ��
** ���� $path Ҫ�����ļ���·��
** ���� $oldPage ��ҳ�������·��
** ���� ���ɵ��ļ�·��
*/
function HTMLPage($path, $oldPage=null)
{
	//����ȫ�ֱ��� 
	global $successList, $errorList, $db,$timer;

	if(!empty($path))
	{
		//ԭ·��Ϊ0,1,5,14,50 ȥ��0,1,����ĸ�Ŀ¼,����5,14,50
		//���Ǹ�Ŀ¼
		if(strlen($path) > 3) 
		{		
			$path = substr($path, 4);	
			//��5,14,50ת����5/14/50
			$path = str_replace(",", "/", $path);
			
			//�жϱ���Ŀ¼�Ƿ����,�������򴴽�
			$dirName = date("Y-m");
			$path = $path . "/" . $dirName;			
		}
		else
		{
			$path = date("Y-m");;
		}
		//���ʱʹ������·��
		if(!is_dir(ARTICLE_REAL_PATH . $path))
		{
			if(!@mkdir(ARTICLE_REAL_PATH . $path))
			{
				exit("��������Ŀ¼ʧ��.");
			}
		}
		//parseArticle.php��Ҫһ��������Ϣ������$parseArt;
		$parseArt = $_POST;
		$styleName= APP_STYLE;

		//�õ�HTML�ַ���
		$htmlStr = parseArticle($_POST, $styleName);

		//ʹ��ԭ�����ļ���
		if($oldPage != null && $_POST['catPath'] == $_POST['oldPath'])
		{
			$fileName = $oldPage;
		}
		else
		{
			$prefix = date("Ymd-His");
			//�ļ�����ʽΪ20041027-122101.htm
			$fileName = ARTICLE_REAL_PATH . $path . "/" . $prefix . ".htm";
		}
		$fp = fopen($fileName, "w");
		fwrite($fp, $htmlStr);
		fclose($fp);

		return $fileName;
	}
	else
	{
		$errorList[] = array("message" => "����ҳ��ʱ��������.");
	}
	if(!empty($errorList))	//�������
	{
		$param["msgList"] = serialize($errorList);
		forward("message.php", $param);
		exit();
	}	
}
/* ���� getRealPath($absPath)
** ���� ���ݽ��·����Ϣ�����������·��
** ���� $absPath ���·����Ϣ
*/
function getRealPath($absPath)
{
	if(!empty($absPath))
	{
		//ԭ·��Ϊ0,1,5,14,50 ȥ��0,1,����ĸ�Ŀ¼,����5,14,50
		$path = substr($absPath, 4);	
		//��5,14,50ת����5/14/50
		$path = str_replace(",", "/", $path);

		return ARTICLE_PATH . $path;	
	}
	return false;
}
/* ���� transferStr($str)
** ���� ���������ݽ���ת��,�Ա㱣����JS������
** ���� $str Ҫת������������
*/
function transferStr($str)
{
	return str_replace("\r\n","",str_replace("/","\/",addslashes($str)));
}

?>
