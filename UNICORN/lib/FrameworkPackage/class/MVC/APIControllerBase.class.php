<?php

class APIControllerBase extends WebControllerBase {

	public $httpStatus = 200;
	public $outputType = "json";
	public $jsonUnescapedUnicode = true;

	/**
	 */
	protected static function _clearCacheImage($argFilePath, $argMemcacheDSN=NULL){
		$DSN = NULL;
		if(NULL === $argMemcacheDSN && class_exists('Configure') && NULL !== Configure::constant('MEMCACHE_DSN')){
			$DSN = Configure::MEMCACHE_DSN;
		}
		else {
			$DSN = $argMemcacheDSN;
		}
		if(NULL !== $DSN && class_exists('Memcache', FALSE)){
			try{
				Memcached::start($DSN);
				@Memcached::delete($argFilePath);
			}
			catch (Exception $Exception){
				logging(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__.PATH_SEPARATOR.$Exception->getMessage(), 'exception');
			}
		}
		return true;
	}
}

?>