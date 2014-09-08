<?php

class ImageControllerBase extends APIControllerBase {

	public $httpStatus = 200;
	public $outputType = "";

	/**
	 */
	protected function _getImage($argFilePath, $argWidth=NULL, $argHeight=NULL, $argProportional=NULL, $argMemcacheDSN=NULL){
		$binary = FALSE;
		$key = NULL;
		$DSN = NULL;
		$memcacheExists = FALSE;
		if(NULL === $argMemcacheDSN && class_exists('Config') && NULL !== Configure::constant('MEMCACHE_DSN')){
			$DSN = Configure::MEMCACHE_DSN;
		}
		else {
			$DSN = $argMemcacheDSN;
		}
		if(NULL !== $DSN && class_exists('Memcache', FALSE)){
			try{
				Memcached::start($DSN);
				$memcacheExists = TRUE;
				$key = $argFilePath;
			}
			catch (Exception $Exception){
				logging(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__.PATH_SEPARATOR.$Exception->getMessage(), 'exception');
			}
		}
		$proportional = FALSE;
		if($argProportional){
			$proportional = TRUE;
		}
		if(TRUE === $memcacheExists){
			// まずmemcache内を探す
			if(NULL !== $argWidth || NULL !== $argHeight){
				$key = $key . $argWidth . $argHeight . $proportional;
				$binary  = @Memcached::get($key);
			}
			if(FALSE === $binary){
				// 元画像を探す
				$binary  = @Memcached::get($argFilePath);
			}
		}
		if(FALSE === $binary || strlen((string)$binary) === 0){
			// ファイルの実態を探す
			$binary = @file_get_contents($argFilePath);
			if(strlen((string)$binary) === 0){
				$binary = FALSE;
			}else{
				if(NULL !== $argWidth || NULL !== $argHeight){
					// リサイズ処理
					$binary = ImageUtil::resize($binary, $argWidth, $argHeight, $proportional);
				}
				if(TRUE === $memcacheExists){
					// memcacheに保存する
					if(FALSE !== $binary){
						@Memcached::set($key, $binary);
					}
				}
			}
		}
		return $binary;
	}
}

?>