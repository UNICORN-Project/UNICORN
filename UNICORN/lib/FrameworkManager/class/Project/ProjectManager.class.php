<?php

class ProjectManager
{
	public static function createProject($argProjectName=''){
		$conName = PROJECT_NAME."Configure";
		debug('$argProjectName='.$argProjectName);
		$samplePackage = $conName::SAMPLE_PROJECT_PACKAGE_PATH;
		$newProjectName = str_replace('Package', '', ucfirst($argProjectName.basename($samplePackage)));
		debug('$newProjectName='.$newProjectName);
		// 移動先のパス
		$movePath = dirname($conName::PROJECT_ROOT_PATH).'/'.$newProjectName.'Package';
		debug('$movePath='.$movePath);
		if(!dir_copy($samplePackage, $movePath)){
			return FALSE;
		}
		// プロジェクト名が指定されている場合は、デフォルトの定義を書き換えて上げる為の処理
		if('' !== $argProjectName){
			// config.xmlのファイル名を書き換える
			$newConfigXMLPath = $movePath.'/core/' . $newProjectName . '.config.xml';
			rename($movePath . '/core/Project.config.xml', $newConfigXMLPath);
			// package.xmlのファイル名を書き換える
			rename($movePath . '/core/Project.package.xml', $movePath.'/core/' . $newProjectName . '.package.xml');
			// config.xml内のプロジェクト名を書き換える
			$configXMLStr = file_get_contents($newConfigXMLPath);
			$configXMLStr = str_replace(array('<Project>', '</Project>'), array('<'.$newProjectName.'>', '</'.$newProjectName.'>'), $configXMLStr);
			// 新しい定義で書き換え
			file_put_contents($newConfigXMLPath, $configXMLStr);
			// 重いのでコマメにunset
			unset($configXMLStr);
			// installer内のプロジェクト名を書き換える
			$installerStr = file_get_contents($movePath.'/installer.php');
			$installerStr = str_replace('$projectpkgName = "Project";', '$projectpkgName = "'.ucfirst($newProjectName).'";', $installerStr);
			// 新しい定義で書き換え
			file_put_contents($movePath.'/installer.php', $installerStr);
			// 重いのでコマメにunset
			unset($installerStr);
			// RESRAPI-index内のプロジェクト名を書き換える
			$apidocPath = $movePath.'/apidocs';
			$apiIndexStr = file_get_contents($apidocPath.'/index.php');
			$apiIndexStr = str_replace('$projectpkgName = "Project";', '$projectpkgName = "'.ucfirst($newProjectName).'";', $apiIndexStr);
			// 新しい定義で書き換え
			file_put_contents($movePath.'/apidocs/index.php', $apiIndexStr);
			// 重いのでコマメにunset
			unset($apiIndexStr);
			// iOSサンプル内のプロジェクト内のRESTfulAPIの向け先を帰る
			$iosdefineStr = file_get_contents($movePath.'/iOSSample/Project/SupportingFiles/define.h');
			// レビュー用の暫定処理
			$basePath = str_replace('/'.PROJECT_NAME.'/', '|', $_SERVER["REQUEST_URI"]);
			$basePaths = explode('|', $basePath);
			$iosdefineStr = str_replace('# define URL_BASE @"/workspace/UNICORN/src/lib/FrameworkManager/template/managedocs/"', '# define URL_BASE @"'.$basePaths[0].'/'.$newProjectName.'Package/apidocs/'.'"', $iosdefineStr);
			// 新しい定義で書き換え
			file_put_contents($movePath.'/iOSSample/Project/SupportingFiles/define.h', $iosdefineStr);
			// 重いのでコマメにunset
			unset($iosdefineStr);
		}
		return TRUE;
	}

	public static function modifyProject($argTargetProjectName=''){
	}
}

?>