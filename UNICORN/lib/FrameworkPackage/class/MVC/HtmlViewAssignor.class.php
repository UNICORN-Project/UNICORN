<?php

class HtmlViewAssignor {

	const REMOVE_NODE_KEY = 'remove-node';
	const REPLACE_ATTR_KEY = 'replace-attribute';
	const PART_REPLACE_ATTR_KEY = 'part-replace-attribute';
	const LOOP_NODE_KEY = 'loop-node';
	const PART_REPLACE_NODE_KEY = 'part-replace-node';
	const APPEND_NODE_KEY = 'append-node';
	const PREPEND_NODE_KEY = 'prepend-node';
	const ASSIGN_RESET = 'initialize and reset';

	protected $_orgHtmlHint;
	protected $_orgHtmlKey;
	public $Templates = array();

	public function __construct($argHtmlHint=NULL, $argKey='main'){
		// コンストラクタですっ飛ばさない為に一旦しまってそれでコンストラクタは終わり
		$this->_orgHtmlHint = $argHtmlHint;
		$this->_orgHtmlKey = $argKey;
	}

	public function addTemplate($argHtmlHinst, $argKey='main'){
		if(is_object($argHtmlHinst)){
			// テンプレートエンジンインスタンスが渡ってきていると判定
			$this->Templates[$argKey] = $argHtmlHinst;
		}
		else {
			// テンプレートファイルパスないし、html文字列が渡ってきていると判定し
			// テンプレートエンジンインスタンス生成
			$this->Templates[$argKey] = new HtmlTemplate($argHtmlHinst);
		}
	}

	public function execute($argHtmlHint=NULL, $argParams=NULL, $argKey=NULL){

		if(NULL === $argKey){
			$argKey = $this->_orgHtmlKey;
			// 一度使ったら不要
			$this->_orgHtmlKey = NULL;
		}

		// ベースとなるテンプレートエンジンインスタンスを生成
		if(NULL !== $this->_orgHtmlHint){
			$this->addTemplate($this->_orgHtmlHint, $argKey);
			// 一度使ったら不要
			$this->_orgHtmlHint = NULL;
		}
		if(NULL !== $argHtmlHint){
			$this->addTemplate($argHtmlHint, $argKey);
		}

		// assignの実行
		$html = '<html templatepartsid="main"></html>';
		$htmls = array();
		if(count($this->Templates) > 0){
			$templates = array_reverse($this->Templates);
		}
		else{
			$templates = $this->Templates;
		}
		foreach($templates as $key => $val){
			$tmpHtml = self::assign($val, $argParams);
			if(is_array($tmpHtml)){
				if(isset($tmpHtml['param'])){
					$argParams = $tmpHtml['param'];
				}
				if(isset($tmpHtml['html'])){
					$tmpHtml = $tmpHtml['html'];
				}
			}
			if('base' === $key){
				$html = $tmpHtml;
			}
			else {
				$htmls[$key] = $tmpHtml;
			}
			// リセットしておく
			self::assign(self::ASSIGN_RESET);
		}
		unset($templates);

		// 複数のテンプレートhtmlをガッチャンコ
		if(count($htmls) >0){
			$BaseTemplate = new HtmlTemplate($html);
			foreach($htmls as $key => $val){
				$key = '[templatepartsid=' . $key . ']';
				// XXX アウターで置き換える！！
				// TODO 文字コードの変換指定はそのうちちゃんとやる
				$BaseTemplate->addSource($key, $val, NULL, NULL, TRUE);
			}
			// 書き戻し
			$html = $BaseTemplate->flush();
			unset($htmls);
		}

		return $html;
	}

	public static function assign($argTemplateHint, $argParams=NULL, $argKey=NULL, $argDepth = 0){
		static $Template = array();
		static $varGetten = FALSE;
		if(self::ASSIGN_RESET === $argTemplateHint){
			// staticを初期化
			$Template = array();
			$varGetten = FALSE;
			return;
		}
		if (NULL !== $argTemplateHint && !isset($Template[$argDepth])){
			if(is_object($argTemplateHint)){
				// テンプレートエンジンインスタンスが渡ってきていると判定
				$Template[$argDepth] = $argTemplateHint;
			}
			else {
				// テンプレートファイルパスないし、html文字列が渡ってきていると判定し
				// テンプレートエンジンインスタンス生成
				$Template[$argDepth] = new HtmlTemplate($argTemplateHint);
			}
		}

		// 何はともあれ、テンプレートでの変数宣言があったら持ってくる
		$newParamGetten = FALSE;
		if(0 === $argDepth && FALSE === $varGetten){
			$varGetten = TRUE;
			$varNodes = $Template[$argDepth]->find('var');
			if(is_array($varNodes) && count($varNodes) > 0){
				for($varNodesIdx=0; $varNodesIdx < count($varNodes); $varNodesIdx++){
					if(NULL === $argParams){
						$argParams = array();
					}
					eval('$argParams[count($argParams)] = array(\'' . $varNodes[$varNodesIdx]->getAttribute('selector') . '\' => ' . $varNodes[$varNodesIdx]->getAttribute('value') . ');');
					$newParamGetten = TRUE;
					$varNodes[$varNodesIdx]->remove();
				}
			}
		}
		// アサイン処理の実行
		if(NULL !== $argParams && is_array($argParams)){
			foreach($argParams as $key => $val){
				if(is_numeric($key)){
					// 単純な再帰処理
					self::assign(NULL, $val, NULL, $argDepth);
				}
				else{
					// ノードの削除を処理
					if(NULL !== $argKey && self::REMOVE_NODE_KEY === $key){
						$dom = $Template[$argDepth]->find($argKey);
						if(isset($dom) && is_array($dom) && isset($dom[0])){
							for ($domIdx = 0; count($dom) > $domIdx; $domIdx++) {
								// 削除
								$dom[$domIdx]->remove();
							}
						}
						unset($dom);
					}
					// 属性の置換を処理
					elseif(NULL !== $argKey && self::REPLACE_ATTR_KEY === $key){
						$dom = $Template[$argDepth]->find($argKey);
						if(isset($dom) && is_array($dom) && isset($dom[0])){
							for ($domIdx = 0; count($dom) > $domIdx; $domIdx++) {
								foreach($val as $attrKey => $attrVal){
									// 置き換え
									$dom[$domIdx]->setAttribute($attrKey, $attrVal);
								}
							}
						}
						unset($dom);
					}
					// 属性の部分置換を処理
					elseif(NULL !== $argKey && self::PART_REPLACE_ATTR_KEY === $key){
						$dom = $Template[$argDepth]->find($argKey);
						if(isset($dom) && is_array($dom) && isset($dom[0])){
							for ($domIdx = 0; count($dom) > $domIdx; $domIdx++) {
								foreach($val as $attrKey => $part){
									// 部分置換
									$attrVal = $dom[$domIdx]->getAttribute($attrKey);
									foreach($part as $partKey => $partVal){
										$attrVal = str_replace($partKey, $partVal, $attrVal);
									}
									// 置き換え
									$dom[$domIdx]->setAttribute($attrKey, $attrVal);
								}
							}
						}
						unset($dom);
					}
					// NODEの部分置換を処理
					elseif(NULL !== $argKey && self::PART_REPLACE_NODE_KEY === $key){
						$dom = $Template[$argDepth]->find($argKey);
						if(isset($dom) && is_array($dom) && isset($dom[0])){
							for ($domIdx = 0; count($dom) > $domIdx; $domIdx++) {
								// 部分置換
								$nodeVal = $dom[$domIdx]->text();
								foreach($val as $partKey => $partVal){
									$nodeVal = str_replace($partKey, $partVal, $nodeVal);
								}
								// 置き換え
								$dom[$domIdx]->text($nodeVal);
							}
						}
						unset($dom);
					}
					// NODEの最後にNODEを追加する処理
					elseif(NULL !== $argKey && self::APPEND_NODE_KEY === $key){
						$dom = $Template[$argDepth]->find($argKey);
						if(isset($dom) && is_array($dom) && isset($dom[0])){
							for ($domIdx = 0; count($dom) > $domIdx; $domIdx++) {
								// 置き換え
								$dom[$domIdx]->innerHtml($dom[$domIdx]->innerHtml().$val);
							}
						}
						unset($dom);
					}
					// NODEの最初にNODEを追加する処理
					elseif(NULL !== $argKey && self::PREPEND_NODE_KEY === $key){
						$dom = $Template[$argDepth]->find($argKey);
						if(isset($dom) && is_array($dom) && isset($dom[0])){
							for ($domIdx = 0; count($dom) > $domIdx; $domIdx++) {
								// 置き換え
								$dom[$domIdx]->innerHtml($val.$dom[$domIdx]->innerHtml());
							}
						}
						unset($dom);
					}
					// 同じタグを繰り返し処理して描画する
					elseif(NULL !== $argKey && self::LOOP_NODE_KEY === $key){
						if(is_array($val)){
							$newDomHtml = '';
							$dom = $Template[$argDepth]->find($argKey);
							if(isset($dom) && is_array($dom) && isset($dom[0])){
								$outerhtml = $dom[0]->outertext();
								foreach($val as $lKey => $lval){
									if(is_numeric($lKey)){
										$lKey = NULL;
									}
									$newDomHtml .= self::assign($outerhtml, $lval, $lKey, $argDepth+1);
								}
								$dom[0]->setAttribute('outertext', $newDomHtml);
							}
						}
					}
					// 再帰処理
					elseif(is_array($val)){
						if(NULL !== $argKey){
							$key = $argKey . '-' . $key;
						}
						self::assign(NULL, $val, $key, $argDepth);
					}
					// ノード内のテキスト(html)の単純置換
					else {
						// ただのキーに紐づく値(innerHTML)の置換
						if(NULL !== $argKey){
							$key = $argKey . '-' . $key;
						}
						// ループの時用のkey自動走査対象の追加処理
						if(0 < $argDepth && FALSE === strpos($key, '#') && FALSE === strpos($key, '.') && !is_object($Template[$argDepth]->find($key))){
							// 対応のキーに値が無い時、自動でclass扱いしてみる
							// XXX class以外は対象外！理由は書くのが面倒くさい
							$key = '.' . $key;
						}
						$Template[$argDepth]->assignHtml($key, $val);
					}
				}
			}
		}
		if (NULL !== $argTemplateHint){
			// 処理結果html文字列を返却
			if(TRUE === $newParamGetten){
				return array('html' => $Template[$argDepth]->flush(), 'param' => $argParams);
			}
			return $Template[$argDepth]->flush();
		}
	}
}

?>