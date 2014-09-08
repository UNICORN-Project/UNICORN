<?php
/**
 * Bitly.inc.php
 *
 * Bitly連携Utility
 *
 * 利用条件：
 *   - BCD基板処理を通していること（特にコンフィグの読み込み）
 *   - php.iniにてallow_url_fopenを有効にしていること
 *
 * @author t.matsuba
 * @version $Id$
 * @copyright cybird.co.jp
 */

// Bitly定数定義
define('BITLY_SHORTEN_URL', 'http://api.bitly.com/v3/shorten');


/**
 * bitly
 *
 * 短縮URL取得メソッド
 *
 * author t.matsuba
 */
function bitlyGetShortURL( $longURL ){

	// バリデーションチェック
	if( strlen($longURL) == 0 ){
		return null;
	}

	// 短縮元URLの設定
	$longURL = rawurlencode( $longURL );

	// リクエストURLの設定
	$request = BITLY_SHORTEN_URL . "?login=" . BCDConfigure::BITLY_LOGIN_ACCOUNT ."&apiKey=" . BCDConfigure::BITLY_API_KEY . "&longUrl=" . $longURL;
	$response = @file_get_contents($request);
    if( isset($response) && $response !== False ) {
        $url = json_decode($response, true);
    }else{
    	// 取得できない場合は処理終了
    	return null;
    }
	// 短縮URLを戻る
	return $url['data']['url'];

}
