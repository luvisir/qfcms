<?php
//====================================================
//		FileName:fileSystem.inc.php
//		Summary: �ļ�Ŀ¼������
//		
//====================================================

class fileSystem
{

	/* ���� removeDir
	** ���� ɾ��Ŀ¼�������ļ�����Ŀ¼
	** ���� $dirName Ŀ¼����
	** ���� -1 ʧ�� 
	*/
	function removeDir($dirName) 
	{
		if(is_dir($dirName))
		{
			if($dh = opendir($dirName))
			{
				while(($file = readdir($dh)) !== false)
				{
					if($file != "." && $file != "..")
					{
						$filePath = $dirName . "/" . $file;

						if(is_dir($filePath))	//ΪĿ¼,�ݹ�ɾ��
						{
							fileSystem::removeDir($dirName . "/" . $file);
						}
						else		//Ϊ�ļ�,ֱ��ɾ��
						{
							if (!@unlink($filePath))
							{
								echo "û��Ȩ��ɾ���ļ�$filePath,������ֹ.";
								exit();
							}
						}
					}
				}
				//�ļ�ɾ�����,ɾ����Ŀ¼
				closedir($dh);
				rmdir($dirName);
				return 1;
				
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/* ���� listDirTree($dirName)
	** ���� �г�Ŀ¼�������ļ�����Ŀ¼
	** ���� $dirName Ŀ¼����
	** ���� Ŀ¼�ṹ���� falseΪʧ��
	*/
	function listDirTree($dirName) 
	{
		if(is_dir($dirName))
		{
			if($dh = opendir($dirName))
			{
				$tree = array();
				while(($file = readdir($dh)) !== false)
				{
					if($file != "." && $file != "..")
					{
						$filePath = $dirName . "/" . $file;
						if(is_dir($filePath))	//ΪĿ¼,�ݹ�
						{
							$tree[$file] = fileSystem::listTree($filePath);
						}
						else	//Ϊ�ļ�,��ӵ���ǰ����
						{
							$tree[] = $file;
						}
					}
				}
				closedir($dh);
				
			}
			else
			{
				return false;
			}
			return $tree;
		}

		else
		{
			return false;
		}
	}

}


?>
