<?php

class WebFlowControllerBase extends WebControllerBase {

	public $action='';
	public $section='';
	public $target='';
	public static $flowpostformsectionUsed = FALSE;

	protected function _reverseRewriteURL($argAction=NULL, $argQuery=''){
		$action = $this->action;
		if(NULL !== $argAction){
			$action= $argAction;
		}
		return Flow::reverseRewriteURL($action, $argQuery);
	}

	protected function _initWebFlow(){
		// Flowパラムの初期化
		if(NULL === Flow::$params){
			Flow::$params = array();
		}

		// GETパラメータの各種自動処理
		if(isset($_GET) && count($_GET) > 0){
			Flow::$params['get'] = array();
			foreach($_GET as $key => $val){
				// Flow用としてPOSTパラメータをしまっておく
				Flow::$params['get'][$key] = $val;
				if(NULL === Flow::$params['view']){
					Flow::$params['view'] = array();
				}
				Flow::$params['view'][] = array('[frowparamsection=' . $key . ']' => array(HtmlViewAssignor::PART_REPLACE_NODE_KEY => array('_flow_'.$key.'_' => $val)));
				Flow::$params['view'][] = array('[frowparamsection=' . $key . ']' => array(HtmlViewAssignor::PART_REPLACE_ATTR_KEY => array('href' => array('_flow_'.$key.'_' => $val), 'value' => array('_flow_'.$key.'_' => $val), 'src' => array('_flow_'.$key.'_' => $val))));
			}
		}

		self::$flowpostformsectionUsed = FALSE;

		if(isset($_POST['flowpostformsection']) && count($_POST) > 0){
			Flow::$params['post'] = array();
			foreach($_POST as $key => $val){
				$executed = FALSE;
				// Flow用としてPOSTパラメータをしまっておく
				Flow::$params['post'][$key] = $val;
				// flowFormでPOSTされていたらbackfrowの処理をしておく
				if($_GET['_c_'] === $_POST['flowpostformsection']){
					// backflowがポストされてきたらそれをviewのformに自動APPEND
					if($key === 'flowpostformsection-backflow-section'){
						Flow::$params['view'][] = array('form[flowpostformsection]' => array(HtmlViewAssignor::APPEND_NODE_KEY => '<input type="hidden" name="flowpostformsection-backflow-section" value="' . $val . '"/>'));
						self::$flowpostformsectionUsed = TRUE;
						$executed = TRUE;
					}
					elseif($key === 'flowpostformsection-backflow-section-query'){
						Flow::$params['view'][] = array('form[flowpostformsection]' => array(HtmlViewAssignor::APPEND_NODE_KEY => '<input type="hidden" name="flowpostformsection-backflow-section-query" value="' . $val . '"/>'));
						$executed = TRUE;
					}
				}
				// パスワード以外はREPLACE ATTRIBUTEを自動でして上げる
				if(0 !== strpos($key, 'pass') && $key !== 'flowpostformsection-backflow-section' && $key !== 'flowpostformsection-backflow-section-query'){
					if(NULL === Flow::$params['view']){
						Flow::$params['view'] = array();
					}
					Flow::$params['view'][] = array('input[name=' . $key . ']' => array(HtmlViewAssignor::REPLACE_ATTR_KEY => array('value'=>htmlspecialchars($val))));
				}
				if($this->target.str_replace('_', '-', strtolower(get_class($this))) !== $_POST['flowpostformsection'] && FALSE === $executed && 0 !== strpos($key, 'pass')){
					// それ以外はformにhiddenで埋め込む
					Flow::$params['view'][]=  array('form[flowpostformsection]' => array(HtmlViewAssignor::APPEND_NODE_KEY => '<input type="hidden" name="'.$key.'" value="' . htmlspecialchars($val) . '"/>'));
				}
				// auto validate
				// flowFormでPOSTされていたら自動的にバリデートする
				if($_GET['_c_'] === $_POST['flowpostformsection']){
					try{
						if(FALSE !== strpos($key, 'mail')){
							// メールアドレスのオートバリデート
							Validations::isEmail($val);
						}
						if(FALSE !== strpos($key, '_must') && 0 === strlen($val)){
							debug('must exception');
							// 必須パラメータの存在チェック
							throw new Exception();
						}
					}
					catch (Exception $Exception){
						// 最後のエラーメッセージを取っておく
						$validateError = TRUE;
						if(NULL === Flow::$params['view']){
							Flow::$params['view'] = array();
						}
						// XXX メッセージの固定化いるか？？
						Flow::$params['view'][] = array('div[flowpostformsectionerror=' . $_POST['flowpostformsection'] . ']' => 'メールアドレスの形式が違います');
					}
				}
			}
			if(isset($validateError)){
				// オートバリデートでエラー
				debug('$validateError');
				return FALSE;
			}
		}

		// Backflowの初期化
		if(NULL === Flow::$params['backflow']){
			Flow::$params['backflow'] = array();
		}

		// 一つ前の画面のbackflowをflowpostformsectionに自動で挿入
		if(count(Flow::$params['backflow']) > 0){
			$backFrowID = Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['target'] . '/' . Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['section'];
			if('' === Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['target']){
				$backFrowID = $this->section;
			}
			else {
				$backFrowID = str_replace('//', '/', $backFrowID);
			}
			// Viewの初期化
			if(NULL === Flow::$params['view']){
				Flow::$params['view'] = array();
			}
			Flow::$params['view'][] = array('form[flowpostformsection]' => array(HtmlViewAssignor::APPEND_NODE_KEY => '<input type="hidden" name="flowpostformsection-backflow-section" value="' . $backFrowID . '"/>'));
			Flow::$params['view'][] = array('form[flowpostformsection]' => array(HtmlViewAssignor::APPEND_NODE_KEY => '<input type="hidden" name="flowpostformsection-backflow-section-query" value="' . Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['query'] . '"/>'));
			self::$flowpostformsectionUsed = TRUE;
		}

		// 現在実行中のFlowをBackflowとして登録しておく
		$query = '';
		foreach($_GET as $key => $val){
			if('_c_' !== $key && '_a_' !== $key && '_o_' !== $key){
				if(strlen($query) > 0){
					$query .= '&';
				}
				$query .= $key.'='.$val;
			}
		}
		Flow::$params['backflow'][] = array('section' => $this->section, 'target' => $this->target, 'query' => htmlspecialchars($query));
		debug('backflows=');
		debug(Flow::$params['backflow']);

		// flowpostformsectionに現在の画面をBackFlowとして登録する
		if(NULL === Flow::$params['view'] && FALSE === self::$flowpostformsectionUsed){
			$backFrowID = Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['target'] . '/' . Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['section'];
			if('' === Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['target']){
				$backFrowID = Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['section'];
			}
			else {
				$backFrowID = str_replace('//', '/', $backFrowID);
			}
			Flow::$params['view'][] = array('form[flowpostformsection]' => array(HtmlViewAssignor::APPEND_NODE_KEY => '<input type="hidden" name="flowpostformsection-backflow-section" value="' . $backFrowID . '"/>'));
			Flow::$params['view'][] = array('form[flowpostformsection]' => array(HtmlViewAssignor::APPEND_NODE_KEY => '<input type="hidden" name="flowpostformsection-backflow-section-query" value="' . Flow::$params['backflow'][count(Flow::$params['backflow']) -1]['query'] . '"/>'));
		}

		return TRUE;
	}
}

?>