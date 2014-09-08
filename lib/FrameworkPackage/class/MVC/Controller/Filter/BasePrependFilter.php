<?php

/**
 * フィルター
 * @author saimushi
 */
class BasePrependFilter {
	public function execute($argRequestParams=NULL){
		$allow = TRUE;
		$denyHTTP = FALSE;
		$denyALLIP = FALSE;
		if(class_exists('Configure') && NULL !== Configure::constant('DENY_HTTP')){
			$denyHTTP = Configure::DENY_HTTP;
		}
		if(class_exists('Configure') && NULL !== Configure::constant('DENY_ALL_IP')){
			$denyALLIP = Configure::DENY_ALL_IP;
		}
		if(defined('PROJECT_NAME') && strlen(PROJECT_NAME) > 0 && class_exists(PROJECT_NAME . 'Configure')){
			$ProjectConfigure = PROJECT_NAME . 'Configure';
			if(NULL !== $ProjectConfigure::constant('DENY_HTTP')){
				$denyHTTP = $ProjectConfigure::DENY_HTTP;
			}
			if(NULL !== $ProjectConfigure::constant('DENY_ALL_IP')){
				$denyALLIP = $ProjectConfigure::DENY_ALL_IP;
			}
		}
		// SSLチェック
		if(FALSE !== $denyHTTP && 0 !== $denyHTTP && "0" !== $denyHTTP){
			if(!(isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'])){
				$allow = FALSE;
			}
		}
		// IPアドレスチェック
		if(FALSE !== $denyALLIP && 0 !== $denyALLIP && "0" !== $denyALLIP){
			// ローカルIPでは無い
			// XXX ネットマスクで許可設定をしたい場合はこの辺りを拡張する
			if('::1' !== $_SERVER['REMOTE_ADDR'] && FALSE === strpos($denyALLIP, $_SERVER['REMOTE_ADDR'])){
				$allow = FALSE;
			}
		}
		debug('allow='.$allow);
		return $allow;
	}
}

?>