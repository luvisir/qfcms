<?php
//====================================================
//		FileName:fileSystem.inc.php
//		Summary: 文件目录操作类
//		
//====================================================

class fileSystem
{

	/* 函数 removeDir
	** 功能 删除目录下所有文件及子目录
	** 参数 $dirName 目录名称
	** 返回 -1 失败 
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

						if(is_dir($filePath))	//为目录,递归删除
						{
							fileSystem::removeDir($dirName . "/" . $file);
						}
						else		//为文件,直接删除
						{
							if (!@unlink($filePath))
							{
								echo "没有权限删除文件$filePath,程序终止.";
								exit();
							}
						}
					}
				}
				//文件删除完成,删除该目录
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

	/* 函数 listDirTree($dirName)
	** 功能 列出目录下所有文件及子目录
	** 参数 $dirName 目录名称
	** 返回 目录结构数组 false为失败
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
						if(is_dir($filePath))	//为目录,递归
						{
							$tree[$file] = fileSystem::listTree($filePath);
						}
						else	//为文件,添加到当前数组
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
