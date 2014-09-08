<?php

/* 環境設定(古き良きCGI的なヤーツ) ココから */
// testerプログラムのURL(絶対パスで指定)
$baseTesterPHPURLs = array();
$baseTesterPHPURLs[] = "http://".$_SERVER["SERVER_NAME"].dirname($_SERVER["PHP_SELF"])."/tester.php";
// testerプログラムのURL(絶対パスで指定)
$baseTargetServerHosts = array();
$baseTargetServerHosts[] = $_SERVER["SERVER_NAME"];
// テストするAPIのサブURL
$apis = array();
$apis[] = 'putCYInfo.json';
/* 環境設定(古き良きCGI的なヤーツ) ココまで */

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Cache-Control" content="no-cache" />
		<title>Web APIテストツール</title>
		<style type="text/css">
		* {
			margin: 0;
			padding:0;
			font-size:12px;
		}
		body {
			width:100%;
			height:100%;
		}
		h1 {
			padding:10px;
			font-size:20px;
		}
		button {
			padding:3px;
		}
		div.clear {
			clear:both;
		}
		div#formView {
			padding:20px;
			background-color:#eeeeee;
		}
		div#formView div.formLine {
			margin-bottom:5px;
		}
		div#formView div.formLine>div.label {
			width:100px;
			float:left;
		}
		div#formView div.formLine .formParts {
			width:200px;
			float:left;
		}
		div#formView div.formLine>button.switch {
			margin-left:20px;
		}
		div#formView div.formLine>input {
			display:none;
		}
		div#formView div#multiParam {
			margin-top:20px;
		}
		div#formView div.formLine div.multiParamLine {
			float:left;
		}
		div#formView div.formLine div.multiParamLine div.label {
			width:110px;
			height:25px;
		}
		div#formView div.formLine div.min div.label {
			width:100px;
			height:25px;
		}
		div#formView div.formLine div.multiParamLine input {
			width:100px;
			margin-right:10px;
		}
		div#formView div.formLine div.multiParamFile {
			width:200px;
			display:none;
		}
		div#formView div.formLine div.multiParamFile input {
			width:190px;
		}
		div#formView div.formLine button.multiParamLine {
			margin-top:25px;
		}
		div#formView div.formLine div.multiParamNum {
			display:none;
		}
		div#responseView {
			position:absolute;
			top:50px;
			left:500px;
		}
		div#responseView button{
			margin:20px;
		}
		div#responseView pre#responseMain {
			background-color:white;
			padding:10px;
			width:100%;
			height:100%;
			border:solid 2px;
		}
		</style>
		<!-- script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript">
			google.load("jquery", "1.3.2");
		</script -->
		<script type="text/javascript" src="./js/jquery.min.js"></script>
		<script type="text/javascript" src="./js/jquery.form.js"></script>
		<script type="text/javascript"><!--
			var mainFormID = "bcd";
			/**
			 * initialize
			 */
			$(function(){

				// ボタン押下のsubmitを全部無効にする
				$("button").click(function (){
					return false;
				});

				// スイッチボタンの動作定義
				$("div#formView div.formLine>button.switch").click(function (){
					// selectボックスだったらinputテキストに、またはその逆に
					var id = $(this).parent().attr("id");
					if("none" == $("div#formView div#"+ id + ".formLine input.formParts").css("display")){
						$("div#formView div#"+ id + ".formLine input.formParts").css("display","block");
						$("div#formView div#"+ id + ".formLine input.formParts").attr("disabled","");
						$("div#formView div#"+ id + ".formLine input.formParts").val($("div#formView div#"+ id + ".formLine select.formParts").val());
						$("div#formView div#"+ id + ".formLine select.formParts").css("display","none");
						$("div#formView div#"+ id + ".formLine select.formParts").attr("disabled","disabled");
					}else{
						$("div#formView div#"+ id + ".formLine input.formParts").css("display","none");
						$("div#formView div#"+ id + ".formLine input.formParts").attr("disabled","disabled");
						$("div#formView div#"+ id + ".formLine select.formParts").css("display","block");
						$("div#formView div#"+ id + ".formLine select.formParts").attr("disabled","");
					}
				});

				// selectボックスの動作定義
				$("div#formView div#multiParam div.multiParamBlock select").change(function (){
					var multiParamNum = Number($("#" + $(this).parent().parent().get(0).id + " div.multiParamNum").text());
					if("file" == $(this).val()){
						$("div#multiParamBlock" + multiParamNum + " div.multiParamVal input").attr("disabled","disabled");
						$("div#multiParamBlock" + multiParamNum + " div.multiParamFile input").attr("disabled","");
						$("div#multiParamBlock" + multiParamNum + " div.multiParamVal").css("display","none");
						$("div#multiParamBlock" + multiParamNum + " div.multiParamFile").css("display","block");
					}else{
						$("div#multiParamBlock" + multiParamNum + " div.multiParamVal input").attr("disabled","");
						$("div#multiParamBlock" + multiParamNum + " div.multiParamFile input").attr("disabled","disabled");
						$("div#multiParamBlock" + multiParamNum + " div.multiParamVal").css("display","block");
						$("div#multiParamBlock" + multiParamNum + " div.multiParamFile").css("display","none");
					}
				});

				// delボタンの動作定義
				$("div#formView div#multiParam div.multiParamBlock button").click(function (){
					if($("div#formView div#multiParam div.multiParamBlock").length > 1){
						$("div#formView div#multiParam div#" + ($(this).parent().attr("id"))).remove();
					}
				});

				// addボタンの動作定義
				$("div#formView div.formLine>button.add").click(function (){
					var newMultiParamNum = Number($("div#formView div#multiParam div.multiParamBlock:first div.multiParamNum").text()) + 1;
					$("div#formView div#multiParam div.multiParamBlock:first").before($("div#formView div#multiParam div.multiParamBlock:first").clone(true));
					$("div#formView div#multiParam div.multiParamBlock:first").attr("id","multiParamBlock" + newMultiParamNum);
					$("div#formView div#multiParam div.multiParamBlock:first div.multiParamType select").attr("name","type[num" + newMultiParamNum + "]");
					$("div#formView div#multiParam div.multiParamBlock:first div.multiParamKey input").attr("name","key[num" + newMultiParamNum + "]");
					$("div#formView div#multiParam div.multiParamBlock:first div.multiParamVal input").attr("name","val[num" + newMultiParamNum + "]");
					$("div#formView div#multiParam div.multiParamBlock:first div.multiParamFile input").attr("name","file[num" + newMultiParamNum + "]");
					$("div#formView div#multiParam div.multiParamBlock:first div.multiParamVal input").attr("disabled","");
					$("div#formView div#multiParam div.multiParamBlock:first div.multiParamFile input").attr("disabled","disabled");
					$("div#formView div#multiParam div.multiParamBlock:first div.multiParamVal").css("display","block");
					$("div#formView div#multiParam div.multiParamBlock:first div.multiParamFile").css("display","none");
					$("div#formView div#multiParam div.multiParamBlock:first div.multiParamNum").text(newMultiParamNum);
				});

				// executeボタンの動作定義
				$("button#execute").click(function (){
					//alert($("")
					$("div#responseView pre#responseMain").text("now tested...");
					// form をAjaxでPOSTする(けど非同期では無い)
					$("form#" + mainFormID).ajaxSubmit({
						url: $("form#" + mainFormID + " #tester .formParts[name=tester][disabled!=true]").val(),
						success: function(response){
							$("div#responseView pre#responseMain").html(response);
						},
						error:function(){
							alert("ajax request error!!");
						}
					});
					/*$("form#" + mainFormID).upload(
						$("form#" + mainFormID + " #tester .formParts[name=tester][disabled!=true]").val(),
						function(response){
							$("div#responseView pre#responseMain").text(response);
						}
					);*/
					/*$.ajax({
						type: "POST",
						url: $("form#" + mainFormID + " #tester .formParts[name=tester][disabled!=true]").val(),
						data: $("form#" + mainFormID).serialize(),
						cache: false,
						async: true,
						success: function(response){
							$("div#responseView pre#responseMain").text(response);
						},
						error:function(){
							alert("ajax request error!!");
						},
					});*/
				});

			});
		--></script>
	</head>
	<body>
		<h1>WebAPIテストツール Ver 2.0</h1>
		<form method="post" id="bcd">
			<div id="formView">
				<div class="formLine" id="tester">
					<div class="label">tester server</div>
					<select class="formParts" name="tester">
						<?php for($index=0; $index<count($baseTesterPHPURLs); $index++){ ?>
						<option value="<?php echo $baseTesterPHPURLs[$index]; ?>"><?php echo $baseTesterPHPURLs[$index]; ?></option>
						<?php } ?>>
					</select>
					<input class="formParts" type="text" name="tester" value="" disabled="disabled"/>
					<button class="switch">switch</button>
				</div>
				<div class="formLine" id="server">
					<div class="label">server</div>
					<select class="formParts" name="server">
						<?php for($index=0; $index<count($baseTargetServerHosts); $index++){ ?>
						<option value="<?php echo $baseTargetServerHosts[$index]; ?>"><?php echo $baseTargetServerHosts[$index]; ?></option>
						<?php } ?>>
					</select>
					<input class="formParts" type="text" name="server" value="" disabled="disabled"/>
					<button class="switch">switch</button>
				</div>
				<div class="formLine" id="protocol">
					<div class="label">protocol</div>
					<select class="formParts" name="protocol">
						<option value="http">http</option>
						<option value="https">https</option>
					</select>
					<input class="formParts" type="text" name="protocol" value="" disabled="disabled"/>
					<button class="switch">switch</button>
				</div>
				<div class="formLine" id="action">
					<div class="label">action</div>
					<select class="formParts" name="action">
					<?php
					for($apiCnt = 0; count($apis) > $apiCnt; $apiCnt++){
						$api = $apis[$apiCnt];
					?>
						<option value="<?php echo $api ?>"><?php echo $api ?></option>
					<?php
					}
					?>
					</select>
					<input class="formParts" type="text" name="action" value="" disabled="disabled"/>
					<button class="switch">switch</button>
				</div>
				<div class="formLine" id="method">
					<div class="label">method</div>
					<select class="formParts" name="method">
						<option value="POST">POST</option>
						<option value="GET">GET</option>
					</select>
					<input class="formParts" type="text" name="method" value="" disabled="disabled"/>
					<button class="switch">switch</button>
				</div>
				<div class="formLine" id="device">
					<div class="label">device</div>
					<select class="formParts" name="device">
						<option value="Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_2 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8H7 Safari/6533.18.5">iPhone(iOS4)</option>
						<option value="Mozilla/5.0 (Macintosh; U; PPC Mac OS X; ja-jp) AppleWebKit/85.7 (KHTML, like Gecko) Safari/85.7">PC(Mac Safari5)</option>
					</select>
					<input class="formParts" type="text" name="device" value="" disabled="disabled"/>
					<button class="switch">switch</button>
				</div>
				<div class="formLine" id="language">
					<div class="label">language</div>
					<select class="formParts" name="language">
						<option value="ja">ja</option>
						<option value="en">en</option>
						<option value="zh">zh</option>
					</select>
					<input class="formParts" type="text" name="language" value="" disabled="disabled"/>
					<button class="switch">switch</button>
				</div>
				<div class="formLine" id="multiParam">
					<div class="label">multiParam</div>
					<button class="add">add</button>
					<div id="multiParamBlock1" class="multiParamBlock">
						<div class="multiParamLine min multiParamType">
							<div class="label">type</div>
							<select name="type[num1]">
								<option value="post">post</option>
								<option value="get">get</option>
								<option value="cookie">cookie</option>
								<option value="file">file</option>
							</select>
						</div>
						<div class="multiParamLine multiParamKey">
							<div class="label">key</div>
							<input type="text" name="key[num1]" value="msg" />
						</div>
						<div class="multiParamLine multiParamVal">
							<div class="label">value</div>
							<input type="text" name="val[num1]" value="スタンプが追加されました" />
						</div>
						<div class="multiParamLine multiParamFile multiParamVal ">
							<div class="label">file</div>
							<input type="file" name="file[num1]" size="15" disabled="disabled" />
						</div>
						<button class="multiParamLine">del</button>
						<div class="multiParamNum">1</div>
						<div class="clear" />
					</div>
				</div>
			</div>
			<div id="responseView">
				<button id="execute">execute!</button>
				<pre id="responseMain">responseView</pre>
			</div>
		</form>
	</body>
</html>
