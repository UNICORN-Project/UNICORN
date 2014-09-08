<?php

require_once dirname(__FILE__)."/testerlib/Cipher.class.php";
require_once dirname(__FILE__)."/testerlib/Utilities.class.php";

$responce = "responseView";

// mcryptが入っているかどうか
if(function_exists(mcrypt_encrypt)){
	exit("PHPの暗号化ライブラリ-mcryptが見当たりませんでした。");
}else{
	if(isset($_GET["mode"]) && isset($_GET["key"]) && isset($_GET["size"]) && isset($_GET["block"]) && isset($_GET["val"]) && isset($_GET["format"])
			&& strlen($_GET["mode"]) > 0 && strlen($_GET["size"]) > 0 && strlen($_GET["block"]) > 0 && strlen($_GET["val"]) > 0 && strlen($_GET["format"]) > 0){
		$iv = null;
		$key = $_GET["key"];
		$size = $_GET["size"];
		$block = $_GET["block"];
		$val = $_GET["val"];
		$params = array(
				'value'		=> $val,
				'algorithm'	=> 'rijndael-'.$size,
				'mode'		=> strtolower($block),
		);
		if(!(isset($_GET["key"]) && strlen($_GET["key"]) > 0)){
			$keySize = mcrypt_get_key_size($params["algorithm"], strtolower($block));
			$key = "";
			$tmpKey = SHA1(uniqid());
			for($loopNum=0; $keySize > $loopNum; $loopNum++){
				$key .= $tmpKey[rand(0,39)];
			}
		}
		$params['key'] = $key;
		if("ECB" != $block && isset($_GET["iv"]) && strlen($_GET["iv"]) > 0){
			$iv = $_GET["iv"];
		}
		if("encrypt" === $_GET["mode"]){
			// 暗号処理
			$decrypt = $val;
			if(null !== $iv){
				if("base64" === $_GET["format"]){
					$iv = base64_decode($iv);
				}
				elseif("hex" === $_GET["format"]){
					$iv = pack("H*", $iv);
				}
			}
			$params['iv'] = $iv;
			$encrypt = Cipher::encrypt($params);
			$iv = Cipher::getNowIV();
			if("base64" === $_GET["format"]){
				$encrypt = base64_encode($encrypt);
				$iv = base64_encode($iv);
			}
			elseif("hex" === $_GET["format"]){
				$encrypt = bin2hex($encrypt);
				$iv = bin2hex($iv);
			}
		}
		elseif("decrypt" === $_GET["mode"]){
			// 復号処理
			$encrypt = $val;
			if("base64" === $_GET["format"]){
				$encrypt = base64_decode($encrypt);
				$iv = base64_decode($iv);
			}
			elseif("hex" === $_GET["format"]){
				$encrypt = pack("H*", $encrypt);
				$iv = pack("H*", $iv);
			}
			$params['value'] = $encrypt;
			$params['iv'] = $iv;
			$decrypt = Cipher::decrypt($params);
			if("base64" === $_GET["format"]){
				$encrypt = base64_encode($encrypt);
				$iv = base64_encode($iv);
			}
			elseif("hex" === $_GET["format"]){
				$encrypt = bin2hex($encrypt);
				$iv = bin2hex($iv);
			}
		}
		$responce = "<br/>";
		$responce .= "入出力フォーマット:".$_GET["format"]."<br/>";
		$responce .= "IV:「".$iv."」<br/>";
		$responce .= "Key:「".$key."」<br/>";
		$responce .= "decrypt:「".$decrypt."」<br/>";
		$responce .= "encrypt:「".$encrypt."」<br/>";
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Cache-Control" content="no-cache" />
<title>AESテストツール</title>
<style type="text/css">
* {
	margin: 0;
	padding: 0;
	font-size: 12px;
}

body {
	width: 100%;
	height: 100%;
}

h1 {
	padding: 10px;
	font-size: 20px;
}

button {
	padding: 3px;
}

div.clear {
	clear: both;
}

div.descriptionView {
	padding: 20px;
}

div#formView {
	padding: 20px;
	background-color: #eeeeee;
}

div#formView div.formLine {
	margin-bottom: 5px;
}

div#formView div.formLine>div.label {
	width: 200px;
	float: left;
}

div#formView div.formLine>button.switch {
	margin-left: 20px;
}

div#formView div#multiParam {
	margin-top: 20px;
}

div#formView div.formLine div.multiParamLine {
	float: left;
}

div#formView div.formLine div.multiParamLine div.label {
	width: 110px;
	height: 25px;
}

div#formView div.formLine div.min div.label {
	width: 100px;
	height: 25px;
}

div#formView div.formLine div.multiParamLine input {
	width: 100px;
	margin-right: 10px;
}

div#formView div.formLine div.multiParamFile {
	width: 200px;
	display: none;
}

div#formView div.formLine div.multiParamFile input {
	width: 190px;
}

div#formView div.formLine button.multiParamLine {
	margin-top: 25px;
}

div#formView div.formLine div.multiParamNum {
	display: none;
}

div#responseView {
	top: 220px;
	margin: 20px;
}

div#responseView button {
	margin: 20px;
}

div#responseView pre#responseMain {
	padding: 10px;
	height: 100%;
	border: solid 2px;
}
</style>
</head>
<body>
	<h1>AESテストツール Ver 1.0</h1>
	<div class="descriptionView">
		<strong>AESで必要な要素</strong> <br /> ・キー(鍵) = 暗号化する際に使用するカギ <br />
		・アルゴリズム = 暗号方式AESを採用 <br /> ・ブロックサイズ = 暗号化するブロックの単位 <br /> ・ブロックモード =
		ブロック間の暗号処理定義 ECB:並列処理(ブロック単位暗号を同時に行う、早い、弱い)
		CBC:直列処理(前のブロックを使用して次のブロックを暗号化していく、遅い、硬い) <br /> ・イニシャライズベクトル(IV) =
		CBCブロックモードの際の最初のブロックの暗号化に利用される
	</div>
	<form method="get" id="crypt">
		<div id="formView">
			<div class="formLine">
				<div class="label">モード</div>
				<select name="mode">
					<option value="encrypt"
					<?php if(isset($_GET["mode"]) && "encrypt" == $_GET["mode"]){ echo " selected=\"selected\""; } ?>>暗号</option>
					<option value="decrypt"
					<?php if(isset($_GET["mode"]) && "decrypt" == $_GET["mode"]){ echo " selected=\"selected\""; } ?>>復号</option>
				</select>
			</div>
			<div class="formLine">
				<div class="label">キー(鍵)</div>
				<input type="text" name="key"
					value="<?php if(isset($_GET["key"])){ echo $_GET["key"]; } ?>" />
			</div>
			<div class="formLine">
				<div class="label">アルゴリズム</div>
				<strong>AES</strong>
			</div>
			<div class="formLine">
				<div class="label">ブロックサイズ</div>
				<select name="size">
					<option
					<?php if(isset($_GET["size"]) && "128" == $_GET["size"]){ echo " selected=\"selected\""; } ?>>128</option>
					<option
					<?php if(isset($_GET["size"]) && "192" == $_GET["size"]){ echo " selected=\"selected\""; } ?>>192</option>
					<option
					<?php if(isset($_GET["size"]) && "256" == $_GET["size"]){ echo " selected=\"selected\""; } ?>>256</option>
				</select>
			</div>
			<div class="formLine">
				<div class="label">ブロックモード</div>
				<select name="block">
					<option
					<?php if(isset($_GET["block"]) && "CBC" == $_GET["block"]){ echo " selected=\"selected\""; } ?>>CBC</option>
					<option
					<?php if(isset($_GET["block"]) && "ECB" == $_GET["block"]){ echo " selected=\"selected\""; } ?>>ECB</option>
				</select>
			</div>
			<div class="formLine">
				<div class="label">イニシャライズベクトル(IV)</div>
				<input type="text" name="iv"
					value="<?php if(isset($_GET["iv"])){ echo $_GET["iv"]; } ?>" />
			</div>
			<div class="formLine">
				<div class="label">対象文字列</div>
				<input type="text" name="val"
					value="<?php if(isset($_GET["val"])){ echo $_GET["val"]; } ?>" />
			</div>
			<div class="formLine">
				<div class="label">入出力フォーマット</div>
				<select name="format">
					<option value="base64"
					<?php if(isset($_GET["format"]) && "base64" == $_GET["format"]){ echo " selected=\"selected\""; } ?>>base64表記</option>
					<option value="hex"
					<?php if(isset($_GET["format"]) && "hex" == $_GET["format"]){ echo " selected=\"selected\""; } ?>>HEX(16進数)表記</option>
					<option value="binary"
					<?php if(isset($_GET["format"]) && "binary" == $_GET["format"]){ echo " selected=\"selected\""; } ?>>バイナリ(プレーン)</option>
				</select>
			</div>
			<div class="formLine">
				<input type="submit" value="execute!" />
			</div>
		</div>
		<div id="responseView">
			<pre id="responseMain">
				<?php echo $responce; ?>
			</pre>
		</div>
	</form>
</body>
</html>
<?php
}
?>