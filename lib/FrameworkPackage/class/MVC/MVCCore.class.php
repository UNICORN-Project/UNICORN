<?php

/**
 * MVCモデルをフレームワークとして提供するクラス
 */
class MVCCore {

	public static $appVersion = NULL;
	public static $deviceType = NULL;
	public static $appleReviewd = FALSE;
	public static $mustAppVersioned = TRUE;
	public static $CurrentController;
	public static $flowXMLBasePath = '';
	public static $flowXMLPaths;

	/**
	 * WebインターフェースでのMVCのメイン処理
	 *
	 * @param boolean DIコンテナで実行するかどうか
	 * @throws Exception
	 */
	public static function webmain($argFlowXMLBasePath=''){

		self::$flowXMLBasePath = $argFlowXMLBasePath;

		logging($_REQUEST, 'post');
		logging($_COOKIE, 'cookie');
		logging($_SERVER, 'server');
		debug('requestParam='.var_export($_REQUEST, TRUE));
		debug('cookie='.var_export($_COOKIE, TRUE));

		$actionMethodName = 'execute';
		if(isset($_GET['_a_']) && strlen($_GET['_a_']) > 0){
			$actionMethodName = $_GET['_a_'];
		}
		// $_GET['_o_']がコントローラで消されてしまうかも知れないので一回取っておく
		// 正式なOutputType定義はコントローラ処理終了後
		$outputType = 'html';
		if(isset($_GET['_o_']) && strlen($_GET['_o_']) > 0){
			$outputType = $_GET['_o_'];
		}
		// アプリケーション情報の取得
		$serverUserAgent = $_SERVER['HTTP_USER_AGENT'];
		$appleReviewd = FALSE;
		$deviceType = 'PC';
		if(false != strpos(strtolower($serverUserAgent), 'iphone')){
			$deviceType = 'iPhone';
		}elseif(false != strpos(strtolower($serverUserAgent), 'ipad')){
			$deviceType = 'iPad';
		}elseif(false != strpos(strtolower($serverUserAgent), 'ipod')){
			$deviceType = 'iPod';
		}elseif(false != strpos(strtolower($serverUserAgent), 'android')){
			$deviceType = 'Android';
		}
		debug('deviceType='.$deviceType);

		// アプリの必須バージョンチェック
		if(isset($_GET['_v_'])){
			if(TRUE === Configure::constant('MUST_IOSAPP_VERSION_FLAG_FILE') && ('iPhone' === $deviceType || 'iPad' === $deviceType || 'iPod' === $deviceType)){
				if(TRUE === is_file(Configure::MUST_IOSAPP_VERSION_FLAG_FILE)){
					debug(Configure::MUST_IOSAPP_VERSION_FLAG_FILE);
					$mustVirsionStr = @file_get_contents(Configure::MUST_IOSAPP_VERSION_FLAG_FILE);
					$matches = NULL;
					if(preg_match('/([0-9.]+)/', $mustVirsionStr, $matches)){
						$mustVirsionNum = (int)str_replace('.','', $matches[1]);
						debug('mustVirsionNum='. $mustVirsionNum);
						debug('nowversion=' . (int)str_replace('.', '', $_GET['_v_']));
						if($mustVirsionNum > (int)str_replace('.', '', $_GET['_v_'])){
							self::$mustAppVersioned = FALSE;
						}
					}
				}
			}
			else if(TRUE === Configure::constant('MUST_ANDROIDAPP_VERSION_FLAG_FILE') && ('android' === $deviceType || 'Android' === $deviceType)){
				if(TRUE === is_file(Configure::MUST_ANDROIDAPP_VERSION_FLAG_FILE)){
					debug(Configure::MUST_ANDROIDAPP_VERSION_FLAG_FILE);
					$mustVirsionStr = @file_get_contents(Configure::MUST_ANDROIDAPP_VERSION_FLAG_FILE);
					$matches = null;
					if(preg_match('/([0-9.]+)/', $mustVirsionStr, $matches)){
						$mustVirsionNum = (int)str_replace('.','', $matches[1]);
						if($mustVirsionNum > (int)str_replace('.', '', $_GET['_v_'])){
							self::$mustAppVersioned = FALSE;
						}
					}
				}
			}
		}

		// アップルレビューバージョンの存在チェック
		if('iPhone' === $deviceType || 'iPad' === $deviceType || 'iPod' === $deviceType){
			if(TRUE === Configure::constant('APPLE_REVIEW_FLAG_FILE') && isset($_GET['_v_'])){
				if(TRUE === is_file(Configure::APPLE_REVIEW_FLAG_FILE.$_GET['_v_'])){
					debug(Configure::APPLE_REVIEW_FLAG_FILE.$_GET['_v_']);
					$appleReviewd = TRUE;
					debug('isAppleReview');
				}
			}
		}

		// アプリバージョン
		$version = NULL;
		if(isset($_GET['_v_']) && strlen($_GET['_v_']) > 0){
			$version = $_GET['_v_'];
			debug('version=' . $version);
		}

		self::$appVersion = $version;
		self::$deviceType = $deviceType;
		self::$appleReviewd = $appleReviewd;

		$res = FALSE;

		// 実行
		try{
			$httpStatus = 200;
			// コントロール対象を取得
			$res = self::loadMVCModule();
			if(FALSE === $res){
				// フィルター処理
				if(self::loadMVCFilter('StaticPrependFilter')){
					$PrependFilter = new StaticPrependFilter();
					$filtered = $PrependFilter->execute();
					if (FALSE === $filtered){
						// XXX フィルターエラー
						throw new Exception('access denied.');
					}
				}
				// ただのhtml表示かも知れないのを調べる
				if(is_file($_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'])){
					// そのままスタティックファイルとして表示
					$res = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI']);
				}
				else{
					// エラー
					throw new Exception('controller class faild.');
				}
				// フィルター処理
				if(self::loadMVCFilter('StaticAppendFilter')){
					$AppendFilter = new StaticAppendFilter();
					$filtered = $AppendFilter->execute();
					if (FALSE === $filtered){
						// XXX フィルターエラー
						throw new Exception('access denied.');
					}
				}
			}
			else{
				$controlerClassName = $res;
				// フィルター処理
				$filres = self::loadMVCFilter('MVCPrependFilter');
				debug($filres);
				if(self::loadMVCFilter('MVCPrependFilter')){
					debug('s??');
					$PrependFilter = new MVCPrependFilter();
					$filtered = $PrependFilter->execute();
					if (FALSE === $filtered){
						// XXX フィルターエラー
						throw new Exception('access denied.');
					}
				}
				self::$CurrentController = new $controlerClassName();
				if(isset($_SERVER['REQUEST_METHOD'])){
					self::$CurrentController->requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
				}
				self::$CurrentController->controlerClassName = $controlerClassName;
				self::$CurrentController->outputType = $outputType;
				self::$CurrentController->deviceType = self::$deviceType;
				self::$CurrentController->appVersion = self::$appVersion;
				self::$CurrentController->appleReviewd = self::$appleReviewd;
				self::$CurrentController->mustAppVersioned = self::$mustAppVersioned;
				$res = self::$CurrentController->$actionMethodName();
				if(FALSE === $res){
					throw new Exception($actionMethodName . ' executed faild.');
				}
				// フィルター処理
				if(self::loadMVCFilter('MVCAppendFilter')){
					$AppendFilter = new MVCAppendFilter();
					$filtered = $AppendFilter->execute();
					if (FALSE === $filtered){
						// XXX フィルターエラー
						throw new Exception('access denied.');
					}
				}
			}
		}
		catch (Exception $Exception){
			// リターンは強制的にFALSE
			$res = FALSE;
			// statusコードがアレバそれを使う
			if(isset(self::$CurrentController->httpStatus) && $httpStatus != self::$CurrentController->httpStatus){
				$httpStatus = self::$CurrentController->httpStatus;
			}
			else{
				// インターナルサーバエラー
				$httpStatus = 500;
			}
		}

		// Output
		try{
			if(200 !== $httpStatus && 201 !== $httpStatus && 202 !== $httpStatus){
				// 200版以外のステータスコードの場合の出力処理
				header('HTTP', TRUE, $httpStatus);
				if(FALSE === $res && isset($Exception)){
					$html = '';
					if('json' === $outputType){
						// exceptionのログ出力
						if(!class_exists('PHPUnit_Framework_TestCase', FALSE)){
							logging($Exception->getMessage().PATH_SEPARATOR.var_export(debug_backtrace(),TRUE),'exception');
						}
						// jsonでエラーメッセージを出力
						header('Content-type: text/javascript; charset=UTF-8');
						$res = json_encode(array('error' => $Exception->getMessage()));
						if(TRUE == self::$CurrentController->jsonUnescapedUnicode){
							$res = unicode_encode($res);
							// スラッシュのエスケープをアンエスケープする
							$res = preg_replace('/\\\\\//', '/', $res);
						}
						debug($res);
						exit($res);
					}
					elseif('xml' === $outputType){
						// exceptionのログ出力
						if(!class_exists('PHPUnit_Framework_TestCase', FALSE)){
							logging($Exception->getMessage().PATH_SEPARATOR.var_export(debug_backtrace(),TRUE),'exception');
						}
						// XMLでエラーメッセージを出力
						header('Content-type:Content- type: application/xml; charset=UTF-8');
						exit('<?xml version="1.0" encoding="UTF-8" ?>' . convertObjectToXML(array('error' => $Exception->getMessage())));
					}
					elseif('html' === $outputType){
						$Tpl = self::loadTemplate('error');
						if(is_object($Tpl)){
							$dispatch = false;
							$html = $Tpl->execute();
						}
					}
					_systemError('Exception :' . $Exception->getMessage(), $httpStatus, $html);
				}
			}
			else{
				$isBinary = FALSE;
				if(isset(self::$CurrentController->outputType)){
					$outputType = self::$CurrentController->outputType;
				}
				if('html' === $outputType){
					// htmlヘッダー出力
					header('Content-type: text/html; charset=UTF-8');
					if(is_array($res)){
						// html出力なのに配列は出力テンプレートの自動判別を試みる

					}
				}
				elseif('txt' === $outputType){
					// textヘッダー出力
					header('Content-type: text/plain; charset=UTF-8');
					if(is_array($res)){
						$res = var_export($res, TRUE);
					}
				}
				elseif('json' === $outputType){
					// jsonヘッダー出力
					header('Content-type: text/javascript; charset=UTF-8');
					if(is_array($res)){
						$res = json_encode($res);
					}
					if(TRUE == self::$CurrentController->jsonUnescapedUnicode){
						$res = unicode_encode($res);
						// スラッシュのエスケープをアンエスケープする
						$res = preg_replace('/\\\\\//', '/', $res);
					}
				}
				elseif('xml' === $outputType){
					// jsonヘッダー出力
					header('Content-type:Content- type: application/xml; charset=UTF-8');
					if(is_array($res)){
						$res = '<?xml version="1.0" encoding="UTF-8" ?>' . convertObjectToXML($res);
					}
				}
				elseif('csv' === $outputType){
					// csvヘッダー出力
					header('Content-type: application/octet-stream; charset=SJIS');
					if(is_array($res)){
						// XXX csvといいつつtsvを吐き出す
						$res = mb_convert_encoding(convertObjectToCSV($res, PHP_TAB), 'SJIS', 'UTF-8');
					}
				}
				elseif('jpg' === $outputType || 'jpeg' === $outputType){
					// jpgヘッダー出力
					header('Content-type: image/jpeg');
					$isBinary = TRUE;
				}
				elseif('png' === $outputType){
					// pngヘッダー出力
					header('Content-type: image/png');
					$isBinary = TRUE;
				}
				elseif('gif' === strtolower($outputType)){
					// gifヘッダー出力
					header('Content-type: image/gif');
					$isBinary = TRUE;
				}
				elseif('bmp' === strtolower($outputType)){
					// bmpヘッダー出力
					header('Content-type: image/bmp');
					$isBinary = TRUE;
				}
				// 描画処理
				if(TRUE === $isBinary && is_string($res)){
					header('Content-length: ' . strlen($res));
				}
				debug('lastRES=');
				debug($res);
				echo $res;
			}
		}
		catch (Exception $Exception){
			// かなりのイレギュラー！ 普通はココを通らない！！
			_systemError('Exception :' . $Exception->getMessage());
		}

		// 明示的終了
		exit;

	}

	public static function batch(){
	}

	/**
	 * MVCクラスモジュールの読み込み処理
	 * @param string クラス名
	 * @param string クラスの読み込事にエラーが在る場合にbooleanを返すかどうか
	 * @param string クラスの読み込事にエラーが在る場合にbooleanを返すかどうか
	 * @return mixed 成功時は対象のクラス名 失敗した場合はFALSEを返す
	 */
	public static function loadMVCModule($argClassName = NULL, $argClassExistsCalled = FALSE, $argTargetPath = ''){

		static $currentTargetPath = '';

		$targetPath = '';
		if(NULL !== $argClassName){
			$controlerClassName = $argClassName;
		}
		else {
			// コントロール対象を自動特定
			$controlerClassName = 'Index';
			if(isset($_GET['_c_']) && strlen($_GET['_c_']) > 0){
				$controlerClassName = str_replace('-', '_', ucfirst($_GET['_c_']));
				if(FALSE !== strpos($_GET['_c_'], '/') && strlen($_GET['_c_']) > 1){
					$matches = NULL;
					if(preg_match('/(.*)\/([^\/]*)$/', $_GET['_c_'], $matches) && is_array($matches) && isset($matches[2])){
						$controlerClassName = str_replace('-', '_', ucfirst($matches[2]));
						if(isset($matches[1]) && strlen($matches[1]) > 0){
							$targetPath = $matches[1].'/';
							if('' === $currentTargetPath){
								$currentTargetPath = $targetPath;
							}
						}
					}
				}
			}
		}
		if('' !== $argTargetPath){
			$targetPath = $argTargetPath;
		}
		if('' === $targetPath){
			$targetPath = $currentTargetPath;
		}
		$version = '';
		if(isset($_GET['_v_']) && strlen($_GET['_v_']) > 0){
			$version = $_GET['_v_'];
		}
		debug('path='.$targetPath);
		debug('class='.$controlerClassName);

		if(!class_exists($controlerClassName, FALSE)){
			// コントローラを読み込み
			if('' !== $version){
				// バージョン一致のファイルを先ず走査する
				loadModule('default.controlmain.' . $targetPath . $version . '/' . $controlerClassName, TRUE);
			}
			if(!class_exists($controlerClassName, FALSE)){
				loadModule('default.controlmain.' . $targetPath . $controlerClassName, TRUE);
			}
			if(!class_exists($controlerClassName, FALSE)){
				loadModule('default.controlmain.' . $controlerClassName, TRUE);
			}
			if(class_exists($controlerClassName, FALSE)){
				// FlowGenerateする必要がなさそうなのでココで終了
				return $controlerClassName;
			}
			else if('' === self::$flowXMLBasePath){
				// エラー終了
				return FALSE;
			}
			else{
				// ココからはFlow処理
				if(TRUE === self::$flowXMLBasePath){
					// self::$flowXMLBasePathがTRUEとなっていた場合はConfigureにFLOWXML_PATH定義が無いか調べる
					if(class_exists('Configure', FALSE) && NULL !== Configure::constant('FLOWXML_PATH')){
						self::$flowXMLBasePath = Configure::FLOWXML_PATH;
					}
				}
				// Flow出来ない！
				if('' === self::$flowXMLBasePath){
					// エラー終了
					return FALSE;
				}
				// XML定義の存在チェック
				// クラス名は分解しておく
				$classHint  = explode('_', $controlerClassName);
				debug($targetPath);
				debug($classHint);
				$classXMLName = $classHint[0];
				debug($classXMLName);
				$flowXMLPath = '';
				if('' !== $version){
					// バージョン一致のファイルを先ず走査する
					if(file_exists_ip(self::$flowXMLBasePath . '/' . $targetPath . $version . '/' . $classXMLName . '.flow.xml')){
						$flowXMLPath = self::$flowXMLBasePath . '/' . $targetPath . $version . '/' . $classXMLName . '.flow.xml';
					}
				}
				if('' === $flowXMLPath){
					// バージョン関係ナシのファイルを走査する
					if(file_exists_ip(self::$flowXMLBasePath . '/' . $targetPath . $classXMLName . '.flow.xml')){
						$flowXMLPath = self::$flowXMLBasePath . '/' . $targetPath . $classXMLName . '.flow.xml';
					}
				}
				debug($flowXMLPath);
				if('' === $flowXMLPath){
					// エラー終了
					return FALSE;
				}
				// flowファイルの履歴を残しておく
				self::$flowXMLPaths[] = array('class' => $controlerClassName, 'xml' => $flowXMLPath);
				// Flowに応じたクラス定義の自動生成を委任
				loadModule('Flow');
				if(FALSE === Flow::generate($flowXMLPath, $controlerClassName, $targetPath)){
					// エラー終了
					return FALSE;
				}
				if(!class_exists($controlerClassName, FALSE)){
					// エラー終了
					return FALSE;
				}
			}
		}

		return $controlerClassName;
	}

	/**
	 * MVCフィルターモジュールの読み込み処理
	 * @param string クラス名
	 * @param string クラスの読み込事にエラーが在る場合にbooleanを返すかどうか
	 * @param string クラスの読み込事にエラーが在る場合にbooleanを返すかどうか
	 * @return mixed 成功時は対象のクラス名 失敗した場合はFALSEを返す
	 */
	public static function loadMVCFilter($argFilterName, $argTargetPath = ''){
		$filterClassName = $argFilterName;
		if(!class_exists($filterClassName, FALSE)){
			$targetPath = '';
			if('' !== $argTargetPath){
				$targetPath = $argTargetPath;
			}
			$version = '';
			if(isset($_GET['_v_']) && strlen($_GET['_v_']) > 0){
				$version = $_GET['_v_'];
			}
			// コントローラを読み込み
			if('' !== $version){
				// バージョン一致のファイルを先ず走査する
				loadModule('default.controlmain.Filter/' . $targetPath . $version . '/' . $filterClassName, TRUE);
			}
			if(!class_exists($filterClassName, FALSE)){
				loadModule('default.controlmain.Filter/' . $targetPath . $filterClassName, TRUE);
			}
			if(!class_exists($filterClassName, FALSE)){
				loadModule('default.controlmain.Filter/' . $filterClassName, TRUE);
			}
			if(!class_exists($filterClassName)){
				return FALSE;
			}
		}
		return $filterClassName;
	}

	/**
	 * クラス名に該当するhtmlを探しだして指定のテンプレートクラスに詰めて返す
	 * @param string クラス名
	 * @param string htmlの読み込事にエラーが在る場合にbooleanを返すかどうか
	 * @return boolean
	 */
	public static function loadTemplate($argClassName = NULL, $argFileExistsCalled = FALSE, $argTargetPath = '', $argViewType = FALSE, $argTemplateEngine = 'HtmlViewAssignor'){

		static $currentTargetPath = '';

		if(FALSE === $argViewType){
			$argViewType = '.html';
		}
		if(NULL === $argViewType){
			// XXX 拡張子未指定と判断！
			$argViewType = '';
		}

		$targetPath = '';
		if(NULL !== $argClassName){
			$controlerClassName = $argClassName;
		}
		else{
			// コントロール対象を自動特定
			$controlerClassName = 'Index';
			debug('_c_='.$_GET['_c_']);
			if(isset($_GET['_c_']) && strlen($_GET['_c_']) > 0){
				$controlerClassName = ucfirst($_GET['_c_']);
				if(FALSE !== strpos($_GET['_c_'], '/') && strlen($_GET['_c_']) > 1){
					$matches = NULL;
					if(preg_match('/(.*)\/([^\/]*)$/', $_GET['_c_'], $matches) && is_array($matches) && isset($matches[2])){
						$controlerClassName = ucfirst($matches[2]);
						if(isset($matches[1]) && strlen($matches[1]) > 0){
							$targetPath = $matches[1].'/';
							if('' === $currentTargetPath){
								$currentTargetPath = $targetPath;
							}
						}
					}
				}
			}
		}
		if('' !== $argTargetPath){
			$targetPath = $argTargetPath;
		}
		if('' === $targetPath){
			$targetPath = $currentTargetPath;
		}

		$version = NULL;
		if(isset($_GET['_v_']) && strlen($_GET['_v_']) > 0){
			$version = $_GET['_v_'];
		}

		$HtmlView = NULL;

		// htmlを読み込み
		if(NULL !== $version){
			$basePath = $targetPath . $version . '/';
			if('' === $targetPath && '/' === $basePath){
				$basePath = $targetPath;
			}
			if(TRUE === file_exists_ip($basePath . $controlerClassName . $argViewType)){
				if(TRUE === $argFileExistsCalled){
					return $basePath . $controlerClassName . $argViewType;
				}
				// Viewインスタンスの生成
				$HtmlView = new $argTemplateEngine($basePath . $controlerClassName . $argViewType);
			}
			elseif(TRUE === file_exists_ip($basePath . strtolower($controlerClassName) . $argViewType)){
				if(TRUE === $argFileExistsCalled){
					return $basePath . strtolower($controlerClassName) . $argViewType;
				}
				// Viewインスタンスの生成
				$HtmlView = new $argTemplateEngine($basePath . strtolower($controlerClassName) . $argViewType);
			}
		}

		if(NULL === $HtmlView){
			$basePath = $targetPath . '/';
			if('' === $targetPath && '/' === $basePath){
				$basePath = $targetPath;
			}
			// バージョンを抜いてインクルード
			if(TRUE === file_exists_ip($basePath . $controlerClassName . $argViewType)){
				if(TRUE === $argFileExistsCalled){
					return $basePath . $controlerClassName . $argViewType;
				}
				// Viewインスタンスの生成
				$HtmlView = new $argTemplateEngine($basePath . $controlerClassName . $argViewType);
			}
			elseif(TRUE === file_exists_ip($basePath . strtolower($controlerClassName) . $argViewType)){
				if(TRUE === $argFileExistsCalled){
					return $basePath . strtolower($controlerClassName) . $argViewType;
				}
				// Viewインスタンスの生成
				$HtmlView = new $argTemplateEngine($basePath . strtolower($controlerClassName) . $argViewType);
			}
			else{
				// エラー終了
				return FALSE;
			}
		}

		return $HtmlView;
	}

	/**
	 * クラス名に該当するhtmlを探しだしてViewクラスに詰めて返す
	 * @param string クラス名
	 * @param string htmlの読み込事にエラーが在る場合にbooleanを返すかどうか
	 * @return boolean
	 */
	public static function loadView($argClassName = NULL, $argFileExistsCalled = FALSE, $argTargetPath = '', $argViewType = FALSE){
		return self::loadTemplate($argClassName, $argFileExistsCalled, $argTargetPath, $argViewType);
	}
}

?>