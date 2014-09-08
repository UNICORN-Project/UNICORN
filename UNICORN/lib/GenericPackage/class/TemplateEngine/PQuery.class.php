<?php


/**
 * JQueryを真似した、simple_html_domを利用したSelector
 * PQueryのPはPHPのP
 * XMLないしHtmlをSelectorでごにょごにょするクラス
 */
class PQuery extends simple_html_dom {

	public function __construct($argTarget, $argStringEnabled=NULL, $argFileEncoding=NULL, $argConvertEncoding=NULL) {
		if(TRUE === $argStringEnabled){
			// $argTargetをそのままテンプレートとして使う
			$buffer = &self::_convertBuffer($argTarget, $argFileEncoding, $argConvertEncoding);
		}else{
			if(1024 >= strlen($argTarget) && TRUE === file_exists_ip($argTarget)){
				$buffer = &self::_readBuffer($argTarget, $argFileEncoding, $argConvertEncoding);
			}
			else {
				// html文字列扱い
				$buffer = &self::_convertBuffer($argTarget, $argFileEncoding, $argConvertEncoding);
			}
		}
		$this->load($buffer);
	}

	protected static function & _convertBuffer(&$argBuffer, $argFileEncoding=NULL, $argConvertEncoding=NULL){
		// テンプレートファイルの文字コード変換処理
		if(NULL === $argFileEncoding){
			// XXX エンコードの自動判定は非推奨
			$argFileEncoding = mb_detect_encoding($argBuffer, 'utf-8,sjis,euc-jp');
		}
		if(NULL === $argConvertEncoding){
			$argConvertEncoding = mb_internal_encoding();
		}
		if($argFileEncoding != $argConvertEncoding){
			$argBuffer = mb_convert_encoding($argBuffer, $argConvertEncoding, $argFileEncoding);
		}
		return $argBuffer;
	}

	protected static function & _readBuffer($argTarget, $argStringEnabled=NULL, $argFileEncoding=NULL, $argConvertEncoding=NULL){
		ob_start();
		@include $argTarget;
		$buffer = ob_get_contents();
		ob_end_clean();
		if (strlen($buffer) == 0){
			throw new Exception('Failed opening \''.$argTarget.'\': No such file.');
		}
		return self::_convertBuffer($buffer, $argFileEncoding, $argConvertEncoding);
	}

	/**
	 * ソースを追加する
	 */
	public function addSource($argHint, $argTarget, $argFileEncoding=NULL, $argConvertEncoding=NULL, $argOuterEnabled=FALSE){
		if(1024 >= strlen($argTarget) && TRUE === file_exists_ip($argTarget)){
			$buffer = &self::_readBuffer($argTarget, $argFileEncoding, $argConvertEncoding);
		}
		else {
			// html文字列扱い
			$buffer = &self::_convertBuffer($argTarget, $argFileEncoding, $argConvertEncoding);
		}
		$this->assignHtml($argHint, $buffer, $argOuterEnabled);
		// parseし直し
		$this->refresh();
	}

	/**
	 * パースし直し
	 */
	public function refresh(){
		$this->load($this->flush());
	}

	/**
	 * html文字列にして返却
	 */
	public function flush(){
		return $this->__toString();
	}

	/**
	 * 該当textノードの一括置換
	 */
	public function assignText($argNodes, $argText, $argOuterEnabled=FALSE){
		if(is_array($argNodes) && count($argNodes) > 0){
			for($nodeIndex=0; count($argNodes)>$nodeIndex; $nodeIndex++){
				$this->assignText($argNodes[$nodeIndex], $argText, $argOuterEnabled);
			}
		}elseif(is_object($argNodes)){
			if(FALSE === $argOuterEnabled){
				$argNodes->setAttribute('innertext', $argText);
			}
			else {
				$argNodes->setAttribute('outertext', $argText);
			}
		}elseif(is_string($argNodes)){
			$argNodes = $this->find($argNodes);
			for($nodeIndex=0; count($argNodes)>$nodeIndex; $nodeIndex++){
				if(FALSE === $argOuterEnabled){
					$argNodes[$nodeIndex]->setAttribute('innertext', $argText);
				}
				else {
					$argNodes[$nodeIndex]->setAttribute('outertext', $argText);
				}
			}
		}
	}

	/**
	 * 該当htmlノードの一括置換
	 */
	public function assignHtml($argNodes, $argText, $argOuterEnabled=FALSE){
		$this->assignText($argNodes, $argText, $argOuterEnabled);
		// parseし直し
		$this->refresh();
	}

// 	/**
// 	 * domStoneを作って保持する
// 	 */
// 	private function _content($argTargetTPLHint,$argStoneName=NULL, $argFileFlag=TRUE){

// 		try {

// 			if(NULL === $argStoneName){
// 				$argStoneName = count($this->_stones);
// 			}
// 			$this->_stones[$argStoneName] = new Rune_Stone();

// 			$templateFile = $this->_templateDirectory . '/' . $argTargetTPLHint . $this->_templateSuffix;
// 			if(TRUE === $argFileFlag && is_file($argTargetTPLHint)){
// 				$this->_stones[$argStoneName]->setTemplate($argTargetTPLHint);
// 			}elseif(TRUE === $argFileFlag && is_file($this->_templateDirectory.'/'.$argTargetTPLHint)){
// 				$this->_stones[$argStoneName]->setTemplate($this->_templateDirectory.'/'.$argTargetTPLHint);
// 			}elseif(TRUE === $argFileFlag && is_file($templateFile)){
// 				$this->_stones[$argStoneName]->setTemplate($templateFile);
// 			}else{
// 				$this->_stones[$argStoneName]->setContent($argTargetTPLHint);
// 			}
// 			$content = $this->_stones[$argStoneName]->getDom();

// 			// template上のargument定義をすいとる
// 			foreach($content->getElementsByTagName('argument') as $idx => $child){
// 				$argument = $child->innertext;
// 				if('true' == strtolower($child->getAttribute('eval')) || '1' === $child->getAttribute('eval') || 1 === $child->getAttribute('eval')){
// 					eval('$argument = '.$argument.';');
// 				}
// 				$this->contentArguments[$child->getAttribute('name')] = $argument;
// 				$child->clear();
// 			}

// 			// template上のsubtemplate定義をすいとる
// 			foreach($content->getElementsByTagName('subtemplate') as $idx => $child){
// 				if(strlen($child->getAttribute('path')) > 0){
// 					$path = $child->getAttribute('path');
// 					if(is_file($path) || is_file($this->_templateDirectory . '/' . $path)){
// 						$this->_content($path, $child->getAttribute('name'));
// 					}
// 				}else{
// 					$this->_content($child->innertext, $child->getAttribute('name'), FALSE);
// 				}
// 				$child->clear();
// 			}

// 		} catch (Exception $e) {
// 			throw new Exception($e->getMessage());
// 		}
// 	}

	/**
	 * assignまでの処理をラップして簡単にしたもの
	 */
	function put($argTarget, $argValue) {
		$this->attributes[$argTarget] = $argValue;
	}

	/**
	 * form値セットを簡単にしたもの
	 * XXX コレを利用したい場合は自動セットさせたいformのネームをテンプレートファイルのベースネームと一致させて置くこと！
	 */
	function setForm($argumets){
		$this->setFormValue(pathinfo($this->tplName, PATHINFO_BASENAME),$argumets);
	}

	/**
	 * setAttributeまでの処理をラップして簡単にしたもの
	 */
	function getAttr($argTarget, $argTargetAttr) {
		//$attribute = new stdClass ();
		//$attribute->$argTargetAttr = $argValue;
		$node = $this->find($argTarget);
		$attr = $node[0]->getAttribute($argTargetAttr);
		//unset ($attribute);
		unset($node);
		return $attr;
	}

	/**
	 * setAttributeまでの処理をラップして簡単にしたもの
	 */
	function setAttr($argTarget, $argTargetAttr, $argValue) {
		//$attribute = new stdClass ();
		//$attribute->$argTargetAttr = $argValue;
		$node = $this->find($argTarget);
		$node[0]->setAttribute($argTargetAttr, $argValue);
		//unset ($attribute);
		unset($node);
	}
}

?>