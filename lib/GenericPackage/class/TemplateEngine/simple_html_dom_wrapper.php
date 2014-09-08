<?php

class simple_html_dom extends simple_html_dom_org {}

class simple_html_dom_node extends simple_html_dom_node_org {

	public $dom = NULL;

	/**
	 * domを上位クラスでも参照出来るようにコンストラクタをオーバーライド
	 */
	function __construct($argDOM) {
		parent::__construct($argDOM);
		$this->dom = & $argDOM;
	}

	/**
	 * #0001の為にメソッドをオーバーライド
	 */
	function innertext() {
		// XXX getDOMメソッドはCOREでaddmethodされている事に注意！
		if (isset($this->_[HDOM_INFO_INNER])) return $this->_[HDOM_INFO_INNER];
		if (isset($this->_[HDOM_INFO_TEXT])) return $this->dom->restore_noise($this->_[HDOM_INFO_TEXT]);

		$ret = '';
		foreach($this->nodes as $n){
			// XXX #0001 S.Ohno modifyed
			if(is_object($n->dom)){
				$ret .= $n->outertext();
			}
		}
		return $ret;
	}

	/**
	 * タグでくくられていない、テキストノードだけを抽出する
	 */
	function textNodes(){
		$textNodes = array();
		for($nodeIndex = 0; count($this->nodes) > $nodeIndex; $nodeIndex++){
			if('text' === strtolower($this->nodes[$nodeIndex]->tag) && strlen(trim($this->nodes[$nodeIndex]->innertext())) > 0){
				$textNodes[] = &$this->nodes[$nodeIndex];
			}
		}
		if(count($textNodes) == 0){
			return NULL;
		}
		return $textNodes;
	}

	function innerHtml($argHtmlText = NULL){
		if(NULL === $argHtmlText){
			// get
			return $this->text();
		}else{
			// set
			$this->setAttribute('innertext', $argHtmlText);
			$this->dom->load($this->dom->flush());
			return TRUE;
		}
	}

	/**
	 * jQueryを真似したアクセサ兼セッター
	 */
	function text($argText = NULL){
		if(NULL === $argText){
			// get
			return $this->innertext();
		}else{
			// set
			$this->setAttribute('innertext', $argText);
			return TRUE;
		}
	}

	/**
	 * jQueryを真似したアクセサ兼セッター
	 */
	function html($argHtmlText = NULL){
		if(NULL === $argHtmlText){
			// get
			return $this->outertext();
		}else{
			// set
			$this->setAttribute('outertext', $argHtmlText);
			$this->dom->load($this->dom->flush());
			return TRUE;
		}
	}

	/**
	 * カラにする
	 */
	public function remove(){
		return $this->__set('outertext', '');
	}

	/**
	 * html文字列にして返却
	 */
	public function flush(){
		return $this->__toString();
	}
}

?>