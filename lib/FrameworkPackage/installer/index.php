<?php



// フレームワークのインストーラー



// ブラウザでアクセス出来る場所にこのファイルを置き、アクセスしてインストールを完了して下さい。
// このファイルは、単独で動作するように作成されています。何処に置いても構いません。インストーラーの指示に従えば、インストールは必ず成功します。
// また、インストーラーにはチュートリアルな内容が若干含まれています。
// 是非フレームワーク利用の参考にして下さい。

// 以下より実行スクリプト開始
define("PROJECT_NAME", "UNICORN");
mb_http_output("UTF-8");

// ダウンロード直後のデフォルトのフレームワークパスを定義しておく
// XXX インストーラによって変更されます。
$frameworkPath = dirname(dirname(dirname(dirname(__FILE__))))."/lib/FrameworkPackage";
// org
//$frameworkPath = dirname(dirname(dirname(dirname(__FILE__))))."/lib/FrameworkPackage";

// ダウンロード直後のデフォルトのフレームワーク管理機能のパスを定義しておく
// XXX インストーラによって変更されます。
$fwmgrPath = dirname(dirname(dirname(dirname(__FILE__))))."/lib/FrameworkManager";
// org
//$fwmgrPath = dirname(dirname(dirname(dirname(__FILE__))))."/lib/FrameworkManager";


// CLI実行かどうかのフラグの初期化
$useCLI = false;

// 以下メイン処理の振り分け
if (isset($_GET["phpinfo"])){
	phpinfo();
	exit;
}
elseif (!isset($_GET["a"]) && (!isset($argv) || null === $argv)){
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta id="viewport" name="viewport"
	content="width=device-width, maximum-scale=1.0, user-scalable=no">
<title><?php echo PROJECT_NAME; ?> installer</title>
<style id="resert_css" type="text/css">

/* reset5 © 2011 opensource.736cs.com MIT */
html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,font,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,audio,canvas,details,figcaption,figure,footer,header,hgroup,mark,menu,meter,nav,output,progress,section,summary,time,video{border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent;margin:0;padding:0;}body{line-height:1;}article,aside,dialog,figure,footer,header,hgroup,nav,section,blockquote{display:block;}nav ul{list-style:none;}ol{list-style:decimal;}ul{list-style:disc;}ul ul{list-style:circle;}blockquote,q{quotes:none;}blockquote:before,blockquote:after,q:before,q:after{content:none;}ins{text-decoration:underline;}del{text-decoration:line-through;}mark{background:none;}abbr[title],dfn[title]{border-bottom:1px dotted #000;cursor:help;}table{border-collapse:collapse;border-spacing:0;}hr{display:block;height:1px;border:0;border-top:1px solid #ccc;margin:1em 0;padding:0;}input[type=submit],input[type=button],button{margin:0!important;padding:0!important;}input,select,a img{vertical-align:middle;}

/* フォントサイズと太さのリセット */
html {
	font-size: 10px;
	font-weight: normal;
}

html, body {
	width: 100%;
	height: 100%;
}

body {
	width: 100%;
	margin: 0;
	padding: 0;
}

* {
	font-family:'Lucida Grande', 'Hiragino Kaku Gothic ProN', 'ヒラギノ角ゴ ProN W3', Meiryo, メイリオ, sans-serif;
	font-size: 10px;
	font-weight: normal;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	-o-box-sizing: border-box;
	-ms-box-sizing: border-box;
	box-sizing: border-box;
}

pre, strong{
	white-space: pre;           /* CSS 2.0 */
	white-space: pre-wrap;      /* CSS 2.1 */
	white-space: pre-line;      /* CSS 3.0 */
	white-space: -pre-wrap;     /* Opera 4-6 */
	white-space: -o-pre-wrap;   /* Opera 7 */
	white-space: -moz-pre-wrap; /* Mozilla */
	white-space: -hp-pre-wrap;  /* HP Printers */
	word-wrap: break-word;      /* IE 5+ */
}

small {
	font-size: 0.8em;
}

strong {
	font-weight: bold;
}

.hidden {
	display: none;
}

.clear {
	clear: both;
}

.leftfloat {
	float: left;
}

.rightfloat {
	float: right;
}

/* リセットここまで */
</style>
<style id="base_css" type="text/css">
/* 専用のスタイルの設定(PC等のグローバル定義) */
html {
	/* style setting */
	/* 基準フォントカラー */
	color: #666;
}

.background {
	/* box setting */
	z-index: -1;
	position: fixed;
	/* position setting */
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	/* style setting */
	background-color: #FF00BF;
	<?php if("UNICORN" === PROJECT_NAME){ ?>
	background-color: black;
	background-repeat: no-repeat;
	background-size: 100% auto;
	background-image:
		url(https://dl.dropboxusercontent.com/u/22810487/UNICORN/image/background1.jpg);
	<?php } ?>
}

.loading.active {
	/* box setting */
	display: block;
	float: left;
	width: 20px;
	height: 20px;
	/* position setting */
	margin-left: -20px;
	/* style setting */
	background-repeat: no-repeat;
	background-size: 100% auto;
	background-image:
		url(data:image/gif;base64,R0lGODlhQABAAIQAAJyanMzOzLS2tOzq7KyqrNze3MTCxPT29KSipNTW1Ly+vPTy9LSytOTm5MzKzPz+/JyenNTS1Ly6vOzu7KyurOTi5MTGxPz6/KSmpNza3AAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQIBwAAACwAAAAAQABAAAAF/uAjjmRpnmiqrmzrvnAsz3Rt33iu73xvX4vDATgUEi/G4OXiY10KBoPD4qhWqdbp1JBYlILN0SVCgCAQ5vRZvYYwBqSJRBKpMJsTBwUC6Pv/gAAUcCMDfRAUEgETTQsOGIGRfoMkDYBuDgc+BxmQkoEUDSSGgW4Vm52fgKGVnxgOdztPFKqTooWqEAo+Fwq1fawjlrUCsTkVfL/BIqS1EsY2A57KtyITAsmqFjkLtL/A1SMHAdOREAU3FwbZvxiEJEwNCuWrjDUF9LXLJwXekQbQXlyQ8EkBNkDuVjTwhxDdjFSRLByQV24figb5+gCU0UuSAE0PBljIZhFFAHa2/mQsNOfwwYUGFjyVPHFAgLkAMiIgiPSxxISYiMKpyICyjwKQLwxIEmot5hsXFximfLHAJiikXxJE8OIiAUsYKwMlUIEExoKiAHC+KEAgkr0dVgFte5Fh5yUfFiLtehGh6F4eFfRiXZHA7p+/O5r9KfaiMCgfBfQGTAER0GAcBAMhZlFBap+WUCeLuJBxrouqPM1G2IoicqSxLw74CpTQRedXXEtkRsh0hQO0m1U0YICAgIW3zAwvzu0CXyRKLToDgGB8wJ2OejnGBRQcxYC206uLqJARwikZJyNhAJ1Cuh8MBgZc83i5xQTPfRj0LjH8DwQMCkig3B9qzfCbR/uNvuCef2gJUp8LBzDwCQEBPBgSfpKcc0MBDU4nQAaTLfhLdzJwmAsDx/En4TcYWAiDOr+YgZyIn0CAHA7bffLOA2HVYpoOF+QYSTjffUNBAaLR0EuHAMyIIW1I9lBZIOHQqAoGGTCXA0VDVrLiNwBgEICWODwxDztVPmkOBQ6QWWYDASiwh42jfBnjfxQkkCQODWiF1QJxWiDooIQOGgAVBlhgRxhkCeHoo5A6ugAYjFZq6aWYZqrpppx26immIQAAIfkECAcAAAAsAAAAAEAAQAAABf7gI45kaYqDFUzX6b5wLKMColTLrO/8YiAQiqNxafGOyNElggEAghFWcsprMJxPjCKzMFK/roUAiyVYcOB06eKAkJ0QhmNwUKsvGcobjpFEBl52UwMCbntZBhkTgoMBeodYQQYVgYwweAYMTZBvCAaLlpcFEgickBABOaFhBpumhwINqy4VFIavexAJdUqzDxmluJCxJRYGlWoJwpwYBSUDpQrIXxXBy3sKoCMSWBLTSROuzLaHFLIkGbcQCmoLV6YCzhPkZKi8Iu6dGWAXBrd7FJyJOGDhHwBzJdoAHPClgLh6Ae6JcFgvQaB8uY5NucDtkC6JKAiQyVZiQqFDGP4qTMnwEIsFFwdOHhRYooLIXNKQXFAwDCSJAKUgWNBWIoBBJxRUHhnw6A0EmicGYIBADOY7pwGQRLBGRoBPEmIIRISR4CgABV9hGIAE9cSFAg6Ium1KhgLDHWLKzbgg10UAj21jNKCLJaugA0dR8chw801fMGP2vNyRLleov9i+ndi6R0CoCofQ7ohwdDKjBqHTngC2B0MoZdhUm2CZS3aSjm/Y7Rh8KPCXCy0BmJ4x4GpXHV1kVN6TgMcBnq0fl1hgwWIM3GSaHVEoOcYSDAykowguQNUOiq3Nn7CCAIFSF2sPWdAMM7LT+S7YBHXwDb1T3zKQhpJvF9QChwSPLfuAXVe2uTAPJAzcRcIE/vDxnhLcOdXcFAKGFshbjQGAQQAXOcBVXeodsQBhLpUkwT/r3FOBTLkAuEMFLVFAFBsPxSHLAK2YohsYmJFh3QgVMHCUFqSYhQUGDeoAnRNQknCAidfkIl4SkUFwjgggZunUl4IUItoIAyjgJC5ernLBUCRcEECIWWKwJSMXDPCimE6c6YuVQPFJwYZ/TlfQNVSNVagJBbayJhYKFBDlLBcsUIACU3GC0KIy8HWpkxgcyal3AwQw5RsWTDrqAQ1Y0JIAEo7a6QGXiqOdrDvwlYBM9uDq3AQOTAXBJ74ewaoFBCRVLBITJBBArEiEAAAh+QQIBwAAACwAAAAAQABAAAAF/uAjjmRpnuh4FdGUvnAsl4tCJdes7/MgAYYBb0gsVSgATORQbO4uEQwAIhjknNjXwQIBABCBRXZ8mvy8AEqFzB4d0VOLuK1bNCqJfCJTmSwSBHBJFVd0KQMZBgIMBAiOCBgUAhIMglMKLoYmF4kUUpaglkqFmisSCKGpoBRCmg8LBp+qs2gQAaRsb7S7aBQFhhmovMMAFkxsBcTKGDhkFV27EAo2xAKZWBOBsxgWvyMTAbKqzFkLlaoCBcclEwq0EBYNWBcGqr4vE+epAgkLuEMFxMGxtQ5FQGiiAsjLcuGMJQgJCp6AggBhrW7/igQLZUHGgXqCpKljc8EdKGsy/iYIQCgtwYSMCwq02tEAyUNvMXRRUShRxIUEEghEGBJBmCABMzhVpNCtp4gGCj4pyPgCpKWFMX5SMJDhGg0DNr1QwCpjgYBVOyYUeIkin8UkOGXUBBWAB1URB4zWGqojg0AvXseEhdOx718IhhyAmqojgV4vSOlUWHzXRIS3ABQYGrDYKQrHligYStDZMCjPTRzC0axDl6C4EyuXuPAXQF0d+R4mSHEBD1kYBTBDfKI6TQCnFwZEEEDAgewRJgVh+B3DATRu1B9cmJDBRsVmMQY8hhw4RkAMCrI/OFABrDAMa7JaFVR4x4EIESc2iMBAFgQG5Z0QECgQwJbURBNE4iABBm8pgNoIZoUiwIM7LJAIAcLdAsMF1hF42zwVWIBhKPDBsIAD44lFoQwDBMBAir3MdEIFK4VSYBYVLEgLJigMEIsqrGEBGi3GzJbBKbNQsGJZg6UCAQYODDAAUJgRqB4RFcCojJVtHBDAltEEiI0+YIIyXSkJ1Famg648YEaVytzTpggZNEnMTkuWwwWBqWAgQFNzmuAaGhYkEIAFAiQqgAKFNpAnHV6Kg0FcB1QaaA/RAVDFpUX8JAubnBIxgAJdQPRoqCSsgIScqA6xABebtjrECgwoMIesdlXQwHNkhAAAIfkECAcAAAAsAAAAAEAAQAAABf7gI45kaZ5oqq5s675wLM90bd94ru987//A1aJSSRgTmcrkAlssf4OMQUAhIK4IDEFgiFQOrIVDUeFdpBQMBMBuuwEQiiQwSS0iGEqFibsUJAhvgoMQDAELJgcRBIUFOQsGGIOTgwgMZSMXi2wUDTgVFGuUo28QAUwXCYxwDJ42GYGksm4QCqkUbp2vs7xvFLhuApgzFaKytQrAvbmuMhMEshgWjiMTAQLGvLoyCwykAg1gKBMWkrzCMhcGoxTULOTZlNswBeaCpuIvBcryzS4XEiaZopHA3qR5LmBNsjDjQgMD8QQhZHFBwSQBdWQMMBCLnT8WDfi1geAOxkY1sv4mrojQMVi+F+QMeoSxbtDHFmKgaRsWRsAgCi9xOpBJCsPNFCEHBZBRgQEEBBCiRvR1FMWFBQMaTGjAdULQFhMiWHDgYKyFABbSKljL1kIEREFUXDhwYC5dMHfn6q0bt6/fv5n0Cq57YXBhPoBJLEhAtmzjx2THlo2QMbEIUFCfRs0sVTPUVjEuDMg6usHoryoyEB1F4FCMApUSwMigcxYGOjICCjIKAxQvaZVfDGjZRgBcF76jWQj+r6Yghr1FElqOWN9qDCVbJKcEwUJVIboFCUCtYvvFcOkcTB0Yo2nR7EKJFydfXrrEAPRNVJAwFQ587fbdI0AB1Z2wkXxuKJvQECj93cOAA8w9cAYgDQIAFA3uLRMVAQ6MFoEAUB0T4QvmLWMiHN8B6AYGCQR4Ii0jRtcGiw8cYMGLlGAQo4wWJpBPQTi+oUB+JDLCYlADKLCaNgUQ+Q9tBCRwHAl+KFlhKQLgx0MFCuCWgkMJJNMgBgJM46QMBwwwpVwNFICWAgLEmVYCTVr2wl1n2qnnnnz26eefgAYqqAohAAAh+QQIBwAAACwAAAAAQABAAAAF/uAjjmRpnmiqrmzrvnAsz3Rt33iu73zv/8DVolJJGBOZyuQSjE0yBgGFgKgiMBSBIVI5NFUXKAMBAZjPaACEIgksmN/RpSBBpO94CMMdfywMGHiCeQIVcEANFIOLeAgBhz0ZdoyUaBAKPwmVm2kCkDgVk5SXCoqbEp82EmWLGBYFJBMJAqyLFjwZGLVoAg1eKBMWgYMQGTwHwrwFvysTCsN4FAM9Dc8YARMxBaZ4Fqkv33MJNBMCghiwMxUFA982FdBpmCcXCykDEgQKGdk8AbtnKDQgcWFAhQC36Dkos8ZCBXs6DnCz9GjBgAwBJDDAgqKBuTMIGDhopyMDQDMU/hJYyEcGAoKEJRYEEHUGgwB+OS5MPAMBA00AAqaZqMBAEAQCBh7euGBhE4YEqf78TLPGgSEZ9Yoo2KSgH8EEO/OEDECSxYUJBSIoEKCrEoV0JQYoOGkUw743KQ4UyEiBzNRBFpjJiUCAk5mjXVMQ/UtJYKoGEgzzZHD1xAIHBOgughBAsIjLjI1SUBBgmYoFBQBpxiPAK4kKYc9RsBCgwZIWZxNIiEcsg2c/BlbXVJDA12+zDQLQYpS4xIUEhRu7pjHHAgXNGCqcwEfV6PQaqOd2g0jiMoTzawRY+HgHJo4DE5TvEridgk3SBXwl4G0GA/mcDVh3WGcnTOAAFxMwynMAe2l400N4o303wgEHpPIPHuj4cFZ+7qgwQWwA0DfDBRFAhUKHK1zImnYxgIaABHDlsEBRglAQwX8rVLBKTQZISEMBq0FwE4oPDGDAVEdFQOQLEQinBzYmhFHHZvPkYEAlR43UQAYChIYGBj7SwKBkjEAwEA8XjEkmhmf2cIF4a0YTZg77xWmJAsf14IxwjAgwTh8PzPEMn5b4mWcTFzSQQCmr2fTKoYAWlABCawmggAIWFAcpoPTAtySnoIYq6qiklmrqqaj+EAIAOw==);
}

/* ボタン */
button {
	border-top: 1px solid #ccc;
	border-right: 1px solid #999;
	border-bottom: 1px solid #999;
	border-left: 1px solid #ccc;
	padding: 5px 20px !important;
	font-weight: bold;
	cursor: pointer;
	color: white;
}

button, input[type=button], input[type=submit],
button:active, input[type=button]:active, input[type=submit]:active {
	background: -moz-linear-gradient(top, #fff, orange 1%, orange 50%, #DFDFDF 99%, #ccc);
	background: -webkit-gradient(linear, left top, left bottom, from(#fff), color-stop(0.01, orange), color-stop(0.5, orange), color-stop(0.99, #FF8000), to(#ccc));
	-moz-box-shadow: 1px 1px 2px #E7E7E7;
	-webkit-box-shadow: 1px 1px 2px #E7E7E7;
}

/*
button:hover, input[type=button]:hover, input[type=submit]:hover {
	background: -moz-linear-gradient(top, #fff, #e1e1e1 1%, #e1e1e1 50%, #cfcfcf 99%, #ccc);
	background: -webkit-gradient(linear, left top, left bottom, from(#fff), color-stop(0.01, #e1e1e1), color-stop(0.5, #e1e1e1), color-stop(0.99, #cfcfcf), to(#ccc));
	color: #666;
}
*/

button:active, input[type=button]:active, input[type=submit]:active {
	background: #ccc;
	padding: 6px 20px 4px !important;
}

button[disabled=disabled], input[disabled=disabled]:active,
input[disabled=disabled]:active, input[disabled=disabled]:active {
	background: -moz-linear-gradient(top, #fff, #e1e1e1 1%, #e1e1e1 50%, #cfcfcf 99%, #ccc);
	background: -webkit-gradient(linear, left top, left bottom, from(#fff), color-stop(0.01, #e1e1e1), color-stop(0.5, #e1e1e1), color-stop(0.99, #cfcfcf), to(#ccc));
	color: #ccc;
}

p {
	/* position setting */
	padding: 2px;
}

.title {
	/* box setting */
	z-index: 1;
	position: absolute;
	width: 900px;
	/* position setting */
	top:0;
	left: 0;
	right: 0;
	margin-left: auto;
	margin-right: auto;
	padding: 5px;
	/* style setting */
	color : white;
	font-size: 4em;
	font-weight: bolder;
}

body {
	/* position setting */
	padding-top: 50px;
}

.orange {
	/* style setting */
	color: darkorange;
}

.green {
	/* style setting */
	color: green;
}

.yellow {
	/* style setting */
	color: orange;
}

.red {
	/* style setting */
	color: red;
}

.errormsg {
	/* style setting */
	font-size: 1.2em;
	font-weight: bold;
}

.page_box {
	/* box setting */
	width: 900px;
	height: auto!important;
	height: 100%;
	min-height: 100%;
	/* position setting */
	left: 0;
	right: 0;
	margin-left: auto;
	margin-right: auto;
	/* style setting */
	background-color: rgba(255, 255, 255, 0.9);
}

.navigation_box {
	/* box setting */
	z-index: 2;
	width: 850px;
	height: 150px;
	/* position setting */
	left: 0;
	right : 0;
	margin-top: -150px;
	margin-left: auto;
	margin-right: auto;
}

.page {
	/* box setting */
	display: block;
	width: 100%;
	height: 100%;
	/* position setting */
	padding: 10px;
	padding-bottom: 200px;
}

.page_title {
	/* position setting */
	padding: 20px;
	/* style setting */
	font-size: 4em;
	font-weight: bold;
}

h2.page_title {
	/* style setting */
	color: #FF00BF;
	font-size: 7em;
}

.page_body {
	/* position setting */
	padding: 20px;
	/* style setting */
	font-size: 1.4em;
}

.page_sub_title {
	/* position setting */
	padding: 5px;
	padding-left: 20px;
	padding-right: 20px;
	/* style setting */
	font-size: 1.4em;
	font-weight: bold;
}

.page_sub_body {
	/* position setting */
	padding: 2px;
	padding-left: 20px;
	padding-right: 20px;
	/* style setting */
	font-size: 1.4em;
}

dl.page_sub_body {
	margin-bottom: 20px;	
}

.page_sub_body dt {
	/* position setting */
	padding-top: 5px;
	padding-bottom: 5px;
	/* style setting */
	font-size: 0.8em;
}

.navigator {
	/* box setting */
	overflow: hidden;
	/* position setting */
	width: 100%;
}

.next_step {
	/* box setting */
	width: 100%;
	/* position setting */
	margin-left: auto;
	margin-right: auto;
	margin-bottom: 20px;
	/* style setting */
	text-align: center;
}

.next_step button {
	/* box setting */
	width: 150px;
	height: 50px;
	top: 0;
	/* style setting */
	font-size: 1.2em;
}

.navigate_step {
	/* box setting */
	width: 25%;
	height: 50px;
	/* position setting */
	margin-left: auto;
}

.navigate_header {
	/* position setting */
	padding: 5px;
	/* style setting */
	color: white;
	background-color: gray;
}

.navigate_header.active {
	/* style setting */
	font-weight: bold;
	background-color: darkorange;
}

.navigate_body {
	/* position setting */
	margin: 1px;
	/* style setting */
	color: gray;
	font-size: 0.8em;
}

.navigate_body.active {
	/* style setting */
	font-weight: bold;
	color: #666;
}

/* デフォルトでは非表示にしていく要素の定義 */
#page1, #page2, #page3, #page4, #page5, #nextstep, #execute, #apply, #endstep, #page1_sub_title4, .errormsg
, #page2_body2, #page2_body3, #page2_body5, #page2_body6, #page2_body7, #page2_body8, #page2_body9, #page2_body10
, #page3_body2, #page3_body3, #page3_body4, #page3_body5, #page3_body6, #page3_body7, #page3_body8, #page3_body9, #page3_body10, #page3_body11, #page3_body12, #page3_body13, #page4_body2
{
	/* box setting */
	display: none;
}

.text-input-form {
	/* position setting */
	margin-bottom: 10px;
	/* style setting */
	border: 1px solid #999;
	background-color: white;
}

#page2 .loading, #page3 .loading {
	margin-top: -30px;
}

#page2_body2 .loading, #page3_body5 .loading, #page3_body10 .loading, #page4_body1 .loading {
	margin-top: 15px;
}

#page2_body3 .loading, #page3_body6 .loading, #page3_body8 .loading {
	margin-top: 30px;
}

.input-text {
	/* box setting */
	width: 800px;
	/* position setting */
	padding-top: 5px;
	padding-bottom: 5px;
	/* style setting */
	border-radius: 0;
	border: 0;
	font-size: 1.2em;
}

.input-reset, .input-reset:active {
	width: 20px;
	height: 20px;
	/* position setting */
	padding: 0;
	padding-bottom: 3px;
	/* style setting */
	border: 0;
	border-radius: 10px;
	color: white;
	font-size: 1.5em;
	font-weight: bold;
	text-align: center;
	line-height: 0.5em;
	background: -moz-linear-gradient(top, #ccc, #ccc 1%, #ccc 50%, #ccc 99%, #ccc);
	background: -webkit-gradient(linear, left top, left bottom, from(#ccc), color-stop(0.01, #ccc), color-stop(0.5, #ccc), color-stop(0.99, #ccc), to(#ccc));
	-moz-box-shadow: 0px 0px 0px #ccc;
	-webkit-box-shadow: 0px 0px 0px #ccc;
}

.input-reset:active {
	background: -moz-linear-gradient(top, gray, gray 1%, gray 50%, gray 99%, gray);
	background: -webkit-gradient(linear, left top, left bottom, from(gray), color-stop(0.01, gray), color-stop(0.5, gray), color-stop(0.99, gray), to(gray));
}

</style>
<style id="base_css_sp" type="text/css">
/* スマフォ用スタイル定義 */
@media screen and (max-width: 640px) {
	html {
		/* position setting */
		width: 320px;
	}

	.title {
		/* box setting */
		width: 300px;
		/* position setting */
		/* style setting */
		font-size: 2em;
	}

	.page_title, .page_sub_title, .page_sub_body {
		/* position setting */
		padding-left: 10px;
		padding-right: 10px;
	}

	.page_box {
		/* box setting */
		width: 300px;
	}

	.page_body {
		/* position setting */
		padding: 10px;
	}

	.navigation_box {
		/* box setting */
		width: 280px;
	}

	.page_title {
		/* style setting */
		font-size: 1.5em;
	}

	h2.page_title {
		/* style setting */
		font-size: 2.5em;
	}

	.input-text {
		width: 230px;
	}
}

</style>
<script type="text/javascript">
/*! jQuery v2.0.0 | (c) 2005, 2013 jQuery Foundation, Inc. | jquery.org/license */
(function(e,undefined){var t,n,r=typeof undefined,i=e.location,o=e.document,s=o.documentElement,a=e.jQuery,u=e.$,l={},c=[],f="2.0.0",p=c.concat,h=c.push,d=c.slice,g=c.indexOf,m=l.toString,y=l.hasOwnProperty,v=f.trim,x=function(e,n){return new x.fn.init(e,n,t)},b=/[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,w=/\S+/g,T=/^(?:(<[\w\W]+>)[^>]*|#([\w-]*))$/,C=/^<(\w+)\s*\/?>(?:<\/\1>|)$/,k=/^-ms-/,N=/-([\da-z])/gi,E=function(e,t){return t.toUpperCase()},S=function(){o.removeEventListener("DOMContentLoaded",S,!1),e.removeEventListener("load",S,!1),x.ready()};x.fn=x.prototype={jquery:f,constructor:x,init:function(e,t,n){var r,i;if(!e)return this;if("string"==typeof e){if(r="<"===e.charAt(0)&&">"===e.charAt(e.length-1)&&e.length>=3?[null,e,null]:T.exec(e),!r||!r[1]&&t)return!t||t.jquery?(t||n).find(e):this.constructor(t).find(e);if(r[1]){if(t=t instanceof x?t[0]:t,x.merge(this,x.parseHTML(r[1],t&&t.nodeType?t.ownerDocument||t:o,!0)),C.test(r[1])&&x.isPlainObject(t))for(r in t)x.isFunction(this[r])?this[r](t[r]):this.attr(r,t[r]);return this}return i=o.getElementById(r[2]),i&&i.parentNode&&(this.length=1,this[0]=i),this.context=o,this.selector=e,this}return e.nodeType?(this.context=this[0]=e,this.length=1,this):x.isFunction(e)?n.ready(e):(e.selector!==undefined&&(this.selector=e.selector,this.context=e.context),x.makeArray(e,this))},selector:"",length:0,toArray:function(){return d.call(this)},get:function(e){return null==e?this.toArray():0>e?this[this.length+e]:this[e]},pushStack:function(e){var t=x.merge(this.constructor(),e);return t.prevObject=this,t.context=this.context,t},each:function(e,t){return x.each(this,e,t)},ready:function(e){return x.ready.promise().done(e),this},slice:function(){return this.pushStack(d.apply(this,arguments))},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},eq:function(e){var t=this.length,n=+e+(0>e?t:0);return this.pushStack(n>=0&&t>n?[this[n]]:[])},map:function(e){return this.pushStack(x.map(this,function(t,n){return e.call(t,n,t)}))},end:function(){return this.prevObject||this.constructor(null)},push:h,sort:[].sort,splice:[].splice},x.fn.init.prototype=x.fn,x.extend=x.fn.extend=function(){var e,t,n,r,i,o,s=arguments[0]||{},a=1,u=arguments.length,l=!1;for("boolean"==typeof s&&(l=s,s=arguments[1]||{},a=2),"object"==typeof s||x.isFunction(s)||(s={}),u===a&&(s=this,--a);u>a;a++)if(null!=(e=arguments[a]))for(t in e)n=s[t],r=e[t],s!==r&&(l&&r&&(x.isPlainObject(r)||(i=x.isArray(r)))?(i?(i=!1,o=n&&x.isArray(n)?n:[]):o=n&&x.isPlainObject(n)?n:{},s[t]=x.extend(l,o,r)):r!==undefined&&(s[t]=r));return s},x.extend({expando:"jQuery"+(f+Math.random()).replace(/\D/g,""),noConflict:function(t){return e.$===x&&(e.$=u),t&&e.jQuery===x&&(e.jQuery=a),x},isReady:!1,readyWait:1,holdReady:function(e){e?x.readyWait++:x.ready(!0)},ready:function(e){(e===!0?--x.readyWait:x.isReady)||(x.isReady=!0,e!==!0&&--x.readyWait>0||(n.resolveWith(o,[x]),x.fn.trigger&&x(o).trigger("ready").off("ready")))},isFunction:function(e){return"function"===x.type(e)},isArray:Array.isArray,isWindow:function(e){return null!=e&&e===e.window},isNumeric:function(e){return!isNaN(parseFloat(e))&&isFinite(e)},type:function(e){return null==e?e+"":"object"==typeof e||"function"==typeof e?l[m.call(e)]||"object":typeof e},isPlainObject:function(e){if("object"!==x.type(e)||e.nodeType||x.isWindow(e))return!1;try{if(e.constructor&&!y.call(e.constructor.prototype,"isPrototypeOf"))return!1}catch(t){return!1}return!0},isEmptyObject:function(e){var t;for(t in e)return!1;return!0},error:function(e){throw Error(e)},parseHTML:function(e,t,n){if(!e||"string"!=typeof e)return null;"boolean"==typeof t&&(n=t,t=!1),t=t||o;var r=C.exec(e),i=!n&&[];return r?[t.createElement(r[1])]:(r=x.buildFragment([e],t,i),i&&x(i).remove(),x.merge([],r.childNodes))},parseJSON:JSON.parse,parseXML:function(e){var t,n;if(!e||"string"!=typeof e)return null;try{n=new DOMParser,t=n.parseFromString(e,"text/xml")}catch(r){t=undefined}return(!t||t.getElementsByTagName("parsererror").length)&&x.error("Invalid XML: "+e),t},noop:function(){},globalEval:function(e){var t,n=eval;e=x.trim(e),e&&(1===e.indexOf("use strict")?(t=o.createElement("script"),t.text=e,o.head.appendChild(t).parentNode.removeChild(t)):n(e))},camelCase:function(e){return e.replace(k,"ms-").replace(N,E)},nodeName:function(e,t){return e.nodeName&&e.nodeName.toLowerCase()===t.toLowerCase()},each:function(e,t,n){var r,i=0,o=e.length,s=j(e);if(n){if(s){for(;o>i;i++)if(r=t.apply(e[i],n),r===!1)break}else for(i in e)if(r=t.apply(e[i],n),r===!1)break}else if(s){for(;o>i;i++)if(r=t.call(e[i],i,e[i]),r===!1)break}else for(i in e)if(r=t.call(e[i],i,e[i]),r===!1)break;return e},trim:function(e){return null==e?"":v.call(e)},makeArray:function(e,t){var n=t||[];return null!=e&&(j(Object(e))?x.merge(n,"string"==typeof e?[e]:e):h.call(n,e)),n},inArray:function(e,t,n){return null==t?-1:g.call(t,e,n)},merge:function(e,t){var n=t.length,r=e.length,i=0;if("number"==typeof n)for(;n>i;i++)e[r++]=t[i];else while(t[i]!==undefined)e[r++]=t[i++];return e.length=r,e},grep:function(e,t,n){var r,i=[],o=0,s=e.length;for(n=!!n;s>o;o++)r=!!t(e[o],o),n!==r&&i.push(e[o]);return i},map:function(e,t,n){var r,i=0,o=e.length,s=j(e),a=[];if(s)for(;o>i;i++)r=t(e[i],i,n),null!=r&&(a[a.length]=r);else for(i in e)r=t(e[i],i,n),null!=r&&(a[a.length]=r);return p.apply([],a)},guid:1,proxy:function(e,t){var n,r,i;return"string"==typeof t&&(n=e[t],t=e,e=n),x.isFunction(e)?(r=d.call(arguments,2),i=function(){return e.apply(t||this,r.concat(d.call(arguments)))},i.guid=e.guid=e.guid||x.guid++,i):undefined},access:function(e,t,n,r,i,o,s){var a=0,u=e.length,l=null==n;if("object"===x.type(n)){i=!0;for(a in n)x.access(e,t,a,n[a],!0,o,s)}else if(r!==undefined&&(i=!0,x.isFunction(r)||(s=!0),l&&(s?(t.call(e,r),t=null):(l=t,t=function(e,t,n){return l.call(x(e),n)})),t))for(;u>a;a++)t(e[a],n,s?r:r.call(e[a],a,t(e[a],n)));return i?e:l?t.call(e):u?t(e[0],n):o},now:Date.now,swap:function(e,t,n,r){var i,o,s={};for(o in t)s[o]=e.style[o],e.style[o]=t[o];i=n.apply(e,r||[]);for(o in t)e.style[o]=s[o];return i}}),x.ready.promise=function(t){return n||(n=x.Deferred(),"complete"===o.readyState?setTimeout(x.ready):(o.addEventListener("DOMContentLoaded",S,!1),e.addEventListener("load",S,!1))),n.promise(t)},x.each("Boolean Number String Function Array Date RegExp Object Error".split(" "),function(e,t){l["[object "+t+"]"]=t.toLowerCase()});function j(e){var t=e.length,n=x.type(e);return x.isWindow(e)?!1:1===e.nodeType&&t?!0:"array"===n||"function"!==n&&(0===t||"number"==typeof t&&t>0&&t-1 in e)}t=x(o),function(e,undefined){var t,n,r,i,o,s,a,u,l,c,f,p,h,d,g,m,y="sizzle"+-new Date,v=e.document,b={},w=0,T=0,C=ot(),k=ot(),N=ot(),E=!1,S=function(){return 0},j=typeof undefined,D=1<<31,A=[],L=A.pop,q=A.push,H=A.push,O=A.slice,F=A.indexOf||function(e){var t=0,n=this.length;for(;n>t;t++)if(this[t]===e)return t;return-1},P="checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",R="[\\x20\\t\\r\\n\\f]",M="(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",W=M.replace("w","w#"),$="\\["+R+"*("+M+")"+R+"*(?:([*^$|!~]?=)"+R+"*(?:(['\"])((?:\\\\.|[^\\\\])*?)\\3|("+W+")|)|)"+R+"*\\]",B=":("+M+")(?:\\(((['\"])((?:\\\\.|[^\\\\])*?)\\3|((?:\\\\.|[^\\\\()[\\]]|"+$.replace(3,8)+")*)|.*)\\)|)",I=RegExp("^"+R+"+|((?:^|[^\\\\])(?:\\\\.)*)"+R+"+$","g"),z=RegExp("^"+R+"*,"+R+"*"),_=RegExp("^"+R+"*([>+~]|"+R+")"+R+"*"),X=RegExp(R+"*[+~]"),U=RegExp("="+R+"*([^\\]'\"]*)"+R+"*\\]","g"),Y=RegExp(B),V=RegExp("^"+W+"$"),G={ID:RegExp("^#("+M+")"),CLASS:RegExp("^\\.("+M+")"),TAG:RegExp("^("+M.replace("w","w*")+")"),ATTR:RegExp("^"+$),PSEUDO:RegExp("^"+B),CHILD:RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\("+R+"*(even|odd|(([+-]|)(\\d*)n|)"+R+"*(?:([+-]|)"+R+"*(\\d+)|))"+R+"*\\)|)","i"),"boolean":RegExp("^(?:"+P+")$","i"),needsContext:RegExp("^"+R+"*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\("+R+"*((?:-\\d)?\\d*)"+R+"*\\)|)(?=[^-]|$)","i")},J=/^[^{]+\{\s*\[native \w/,Q=/^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,K=/^(?:input|select|textarea|button)$/i,Z=/^h\d$/i,et=/'|\\/g,tt=/\\([\da-fA-F]{1,6}[\x20\t\r\n\f]?|.)/g,nt=function(e,t){var n="0x"+t-65536;return n!==n?t:0>n?String.fromCharCode(n+65536):String.fromCharCode(55296|n>>10,56320|1023&n)};try{H.apply(A=O.call(v.childNodes),v.childNodes),A[v.childNodes.length].nodeType}catch(rt){H={apply:A.length?function(e,t){q.apply(e,O.call(t))}:function(e,t){var n=e.length,r=0;while(e[n++]=t[r++]);e.length=n-1}}}function it(e){return J.test(e+"")}function ot(){var e,t=[];return e=function(n,i){return t.push(n+=" ")>r.cacheLength&&delete e[t.shift()],e[n]=i}}function st(e){return e[y]=!0,e}function at(e){var t=c.createElement("div");try{return!!e(t)}catch(n){return!1}finally{t.parentNode&&t.parentNode.removeChild(t),t=null}}function ut(e,t,n,r){var i,o,s,a,u,f,d,g,x,w;if((t?t.ownerDocument||t:v)!==c&&l(t),t=t||c,n=n||[],!e||"string"!=typeof e)return n;if(1!==(a=t.nodeType)&&9!==a)return[];if(p&&!r){if(i=Q.exec(e))if(s=i[1]){if(9===a){if(o=t.getElementById(s),!o||!o.parentNode)return n;if(o.id===s)return n.push(o),n}else if(t.ownerDocument&&(o=t.ownerDocument.getElementById(s))&&m(t,o)&&o.id===s)return n.push(o),n}else{if(i[2])return H.apply(n,t.getElementsByTagName(e)),n;if((s=i[3])&&b.getElementsByClassName&&t.getElementsByClassName)return H.apply(n,t.getElementsByClassName(s)),n}if(b.qsa&&(!h||!h.test(e))){if(g=d=y,x=t,w=9===a&&e,1===a&&"object"!==t.nodeName.toLowerCase()){f=gt(e),(d=t.getAttribute("id"))?g=d.replace(et,"\\$&"):t.setAttribute("id",g),g="[id='"+g+"'] ",u=f.length;while(u--)f[u]=g+mt(f[u]);x=X.test(e)&&t.parentNode||t,w=f.join(",")}if(w)try{return H.apply(n,x.querySelectorAll(w)),n}catch(T){}finally{d||t.removeAttribute("id")}}}return kt(e.replace(I,"$1"),t,n,r)}o=ut.isXML=function(e){var t=e&&(e.ownerDocument||e).documentElement;return t?"HTML"!==t.nodeName:!1},l=ut.setDocument=function(e){var t=e?e.ownerDocument||e:v;return t!==c&&9===t.nodeType&&t.documentElement?(c=t,f=t.documentElement,p=!o(t),b.getElementsByTagName=at(function(e){return e.appendChild(t.createComment("")),!e.getElementsByTagName("*").length}),b.attributes=at(function(e){return e.className="i",!e.getAttribute("className")}),b.getElementsByClassName=at(function(e){return e.innerHTML="<div class='a'></div><div class='a i'></div>",e.firstChild.className="i",2===e.getElementsByClassName("i").length}),b.sortDetached=at(function(e){return 1&e.compareDocumentPosition(c.createElement("div"))}),b.getById=at(function(e){return f.appendChild(e).id=y,!t.getElementsByName||!t.getElementsByName(y).length}),b.getById?(r.find.ID=function(e,t){if(typeof t.getElementById!==j&&p){var n=t.getElementById(e);return n&&n.parentNode?[n]:[]}},r.filter.ID=function(e){var t=e.replace(tt,nt);return function(e){return e.getAttribute("id")===t}}):(r.find.ID=function(e,t){if(typeof t.getElementById!==j&&p){var n=t.getElementById(e);return n?n.id===e||typeof n.getAttributeNode!==j&&n.getAttributeNode("id").value===e?[n]:undefined:[]}},r.filter.ID=function(e){var t=e.replace(tt,nt);return function(e){var n=typeof e.getAttributeNode!==j&&e.getAttributeNode("id");return n&&n.value===t}}),r.find.TAG=b.getElementsByTagName?function(e,t){return typeof t.getElementsByTagName!==j?t.getElementsByTagName(e):undefined}:function(e,t){var n,r=[],i=0,o=t.getElementsByTagName(e);if("*"===e){while(n=o[i++])1===n.nodeType&&r.push(n);return r}return o},r.find.CLASS=b.getElementsByClassName&&function(e,t){return typeof t.getElementsByClassName!==j&&p?t.getElementsByClassName(e):undefined},d=[],h=[],(b.qsa=it(t.querySelectorAll))&&(at(function(e){e.innerHTML="<select><option selected=''></option></select>",e.querySelectorAll("[selected]").length||h.push("\\["+R+"*(?:value|"+P+")"),e.querySelectorAll(":checked").length||h.push(":checked")}),at(function(e){var t=c.createElement("input");t.setAttribute("type","hidden"),e.appendChild(t).setAttribute("t",""),e.querySelectorAll("[t^='']").length&&h.push("[*^$]="+R+"*(?:''|\"\")"),e.querySelectorAll(":enabled").length||h.push(":enabled",":disabled"),e.querySelectorAll("*,:x"),h.push(",.*:")})),(b.matchesSelector=it(g=f.webkitMatchesSelector||f.mozMatchesSelector||f.oMatchesSelector||f.msMatchesSelector))&&at(function(e){b.disconnectedMatch=g.call(e,"div"),g.call(e,"[s!='']:x"),d.push("!=",B)}),h=h.length&&RegExp(h.join("|")),d=d.length&&RegExp(d.join("|")),m=it(f.contains)||f.compareDocumentPosition?function(e,t){var n=9===e.nodeType?e.documentElement:e,r=t&&t.parentNode;return e===r||!(!r||1!==r.nodeType||!(n.contains?n.contains(r):e.compareDocumentPosition&&16&e.compareDocumentPosition(r)))}:function(e,t){if(t)while(t=t.parentNode)if(t===e)return!0;return!1},S=f.compareDocumentPosition?function(e,n){if(e===n)return E=!0,0;var r=n.compareDocumentPosition&&e.compareDocumentPosition&&e.compareDocumentPosition(n);return r?1&r||!b.sortDetached&&n.compareDocumentPosition(e)===r?e===t||m(v,e)?-1:n===t||m(v,n)?1:u?F.call(u,e)-F.call(u,n):0:4&r?-1:1:e.compareDocumentPosition?-1:1}:function(e,n){var r,i=0,o=e.parentNode,s=n.parentNode,a=[e],l=[n];if(e===n)return E=!0,0;if(!o||!s)return e===t?-1:n===t?1:o?-1:s?1:u?F.call(u,e)-F.call(u,n):0;if(o===s)return lt(e,n);r=e;while(r=r.parentNode)a.unshift(r);r=n;while(r=r.parentNode)l.unshift(r);while(a[i]===l[i])i++;return i?lt(a[i],l[i]):a[i]===v?-1:l[i]===v?1:0},c):c},ut.matches=function(e,t){return ut(e,null,null,t)},ut.matchesSelector=function(e,t){if((e.ownerDocument||e)!==c&&l(e),t=t.replace(U,"='$1']"),!(!b.matchesSelector||!p||d&&d.test(t)||h&&h.test(t)))try{var n=g.call(e,t);if(n||b.disconnectedMatch||e.document&&11!==e.document.nodeType)return n}catch(r){}return ut(t,c,null,[e]).length>0},ut.contains=function(e,t){return(e.ownerDocument||e)!==c&&l(e),m(e,t)},ut.attr=function(e,t){(e.ownerDocument||e)!==c&&l(e);var n=r.attrHandle[t.toLowerCase()],i=n&&n(e,t,!p);return i===undefined?b.attributes||!p?e.getAttribute(t):(i=e.getAttributeNode(t))&&i.specified?i.value:null:i},ut.error=function(e){throw Error("Syntax error, unrecognized expression: "+e)},ut.uniqueSort=function(e){var t,n=[],r=0,i=0;if(E=!b.detectDuplicates,u=!b.sortStable&&e.slice(0),e.sort(S),E){while(t=e[i++])t===e[i]&&(r=n.push(i));while(r--)e.splice(n[r],1)}return e};function lt(e,t){var n=t&&e,r=n&&(~t.sourceIndex||D)-(~e.sourceIndex||D);if(r)return r;if(n)while(n=n.nextSibling)if(n===t)return-1;return e?1:-1}function ct(e,t,n){var r;return n?undefined:(r=e.getAttributeNode(t))&&r.specified?r.value:e[t]===!0?t.toLowerCase():null}function ft(e,t,n){var r;return n?undefined:r=e.getAttribute(t,"type"===t.toLowerCase()?1:2)}function pt(e){return function(t){var n=t.nodeName.toLowerCase();return"input"===n&&t.type===e}}function ht(e){return function(t){var n=t.nodeName.toLowerCase();return("input"===n||"button"===n)&&t.type===e}}function dt(e){return st(function(t){return t=+t,st(function(n,r){var i,o=e([],n.length,t),s=o.length;while(s--)n[i=o[s]]&&(n[i]=!(r[i]=n[i]))})})}i=ut.getText=function(e){var t,n="",r=0,o=e.nodeType;if(o){if(1===o||9===o||11===o){if("string"==typeof e.textContent)return e.textContent;for(e=e.firstChild;e;e=e.nextSibling)n+=i(e)}else if(3===o||4===o)return e.nodeValue}else for(;t=e[r];r++)n+=i(t);return n},r=ut.selectors={cacheLength:50,createPseudo:st,match:G,attrHandle:{},find:{},relative:{">":{dir:"parentNode",first:!0}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:!0},"~":{dir:"previousSibling"}},preFilter:{ATTR:function(e){return e[1]=e[1].replace(tt,nt),e[3]=(e[4]||e[5]||"").replace(tt,nt),"~="===e[2]&&(e[3]=" "+e[3]+" "),e.slice(0,4)},CHILD:function(e){return e[1]=e[1].toLowerCase(),"nth"===e[1].slice(0,3)?(e[3]||ut.error(e[0]),e[4]=+(e[4]?e[5]+(e[6]||1):2*("even"===e[3]||"odd"===e[3])),e[5]=+(e[7]+e[8]||"odd"===e[3])):e[3]&&ut.error(e[0]),e},PSEUDO:function(e){var t,n=!e[5]&&e[2];return G.CHILD.test(e[0])?null:(e[4]?e[2]=e[4]:n&&Y.test(n)&&(t=gt(n,!0))&&(t=n.indexOf(")",n.length-t)-n.length)&&(e[0]=e[0].slice(0,t),e[2]=n.slice(0,t)),e.slice(0,3))}},filter:{TAG:function(e){var t=e.replace(tt,nt).toLowerCase();return"*"===e?function(){return!0}:function(e){return e.nodeName&&e.nodeName.toLowerCase()===t}},CLASS:function(e){var t=C[e+" "];return t||(t=RegExp("(^|"+R+")"+e+"("+R+"|$)"))&&C(e,function(e){return t.test("string"==typeof e.className&&e.className||typeof e.getAttribute!==j&&e.getAttribute("class")||"")})},ATTR:function(e,t,n){return function(r){var i=ut.attr(r,e);return null==i?"!="===t:t?(i+="","="===t?i===n:"!="===t?i!==n:"^="===t?n&&0===i.indexOf(n):"*="===t?n&&i.indexOf(n)>-1:"$="===t?n&&i.slice(-n.length)===n:"~="===t?(" "+i+" ").indexOf(n)>-1:"|="===t?i===n||i.slice(0,n.length+1)===n+"-":!1):!0}},CHILD:function(e,t,n,r,i){var o="nth"!==e.slice(0,3),s="last"!==e.slice(-4),a="of-type"===t;return 1===r&&0===i?function(e){return!!e.parentNode}:function(t,n,u){var l,c,f,p,h,d,g=o!==s?"nextSibling":"previousSibling",m=t.parentNode,v=a&&t.nodeName.toLowerCase(),x=!u&&!a;if(m){if(o){while(g){f=t;while(f=f[g])if(a?f.nodeName.toLowerCase()===v:1===f.nodeType)return!1;d=g="only"===e&&!d&&"nextSibling"}return!0}if(d=[s?m.firstChild:m.lastChild],s&&x){c=m[y]||(m[y]={}),l=c[e]||[],h=l[0]===w&&l[1],p=l[0]===w&&l[2],f=h&&m.childNodes[h];while(f=++h&&f&&f[g]||(p=h=0)||d.pop())if(1===f.nodeType&&++p&&f===t){c[e]=[w,h,p];break}}else if(x&&(l=(t[y]||(t[y]={}))[e])&&l[0]===w)p=l[1];else while(f=++h&&f&&f[g]||(p=h=0)||d.pop())if((a?f.nodeName.toLowerCase()===v:1===f.nodeType)&&++p&&(x&&((f[y]||(f[y]={}))[e]=[w,p]),f===t))break;return p-=i,p===r||0===p%r&&p/r>=0}}},PSEUDO:function(e,t){var n,i=r.pseudos[e]||r.setFilters[e.toLowerCase()]||ut.error("unsupported pseudo: "+e);return i[y]?i(t):i.length>1?(n=[e,e,"",t],r.setFilters.hasOwnProperty(e.toLowerCase())?st(function(e,n){var r,o=i(e,t),s=o.length;while(s--)r=F.call(e,o[s]),e[r]=!(n[r]=o[s])}):function(e){return i(e,0,n)}):i}},pseudos:{not:st(function(e){var t=[],n=[],r=s(e.replace(I,"$1"));return r[y]?st(function(e,t,n,i){var o,s=r(e,null,i,[]),a=e.length;while(a--)(o=s[a])&&(e[a]=!(t[a]=o))}):function(e,i,o){return t[0]=e,r(t,null,o,n),!n.pop()}}),has:st(function(e){return function(t){return ut(e,t).length>0}}),contains:st(function(e){return function(t){return(t.textContent||t.innerText||i(t)).indexOf(e)>-1}}),lang:st(function(e){return V.test(e||"")||ut.error("unsupported lang: "+e),e=e.replace(tt,nt).toLowerCase(),function(t){var n;do if(n=p?t.lang:t.getAttribute("xml:lang")||t.getAttribute("lang"))return n=n.toLowerCase(),n===e||0===n.indexOf(e+"-");while((t=t.parentNode)&&1===t.nodeType);return!1}}),target:function(t){var n=e.location&&e.location.hash;return n&&n.slice(1)===t.id},root:function(e){return e===f},focus:function(e){return e===c.activeElement&&(!c.hasFocus||c.hasFocus())&&!!(e.type||e.href||~e.tabIndex)},enabled:function(e){return e.disabled===!1},disabled:function(e){return e.disabled===!0},checked:function(e){var t=e.nodeName.toLowerCase();return"input"===t&&!!e.checked||"option"===t&&!!e.selected},selected:function(e){return e.parentNode&&e.parentNode.selectedIndex,e.selected===!0},empty:function(e){for(e=e.firstChild;e;e=e.nextSibling)if(e.nodeName>"@"||3===e.nodeType||4===e.nodeType)return!1;return!0},parent:function(e){return!r.pseudos.empty(e)},header:function(e){return Z.test(e.nodeName)},input:function(e){return K.test(e.nodeName)},button:function(e){var t=e.nodeName.toLowerCase();return"input"===t&&"button"===e.type||"button"===t},text:function(e){var t;return"input"===e.nodeName.toLowerCase()&&"text"===e.type&&(null==(t=e.getAttribute("type"))||t.toLowerCase()===e.type)},first:dt(function(){return[0]}),last:dt(function(e,t){return[t-1]}),eq:dt(function(e,t,n){return[0>n?n+t:n]}),even:dt(function(e,t){var n=0;for(;t>n;n+=2)e.push(n);return e}),odd:dt(function(e,t){var n=1;for(;t>n;n+=2)e.push(n);return e}),lt:dt(function(e,t,n){var r=0>n?n+t:n;for(;--r>=0;)e.push(r);return e}),gt:dt(function(e,t,n){var r=0>n?n+t:n;for(;t>++r;)e.push(r);return e})}};for(t in{radio:!0,checkbox:!0,file:!0,password:!0,image:!0})r.pseudos[t]=pt(t);for(t in{submit:!0,reset:!0})r.pseudos[t]=ht(t);function gt(e,t){var n,i,o,s,a,u,l,c=k[e+" "];if(c)return t?0:c.slice(0);a=e,u=[],l=r.preFilter;while(a){(!n||(i=z.exec(a)))&&(i&&(a=a.slice(i[0].length)||a),u.push(o=[])),n=!1,(i=_.exec(a))&&(n=i.shift(),o.push({value:n,type:i[0].replace(I," ")}),a=a.slice(n.length));for(s in r.filter)!(i=G[s].exec(a))||l[s]&&!(i=l[s](i))||(n=i.shift(),o.push({value:n,type:s,matches:i}),a=a.slice(n.length));if(!n)break}return t?a.length:a?ut.error(e):k(e,u).slice(0)}function mt(e){var t=0,n=e.length,r="";for(;n>t;t++)r+=e[t].value;return r}function yt(e,t,r){var i=t.dir,o=r&&"parentNode"===i,s=T++;return t.first?function(t,n,r){while(t=t[i])if(1===t.nodeType||o)return e(t,n,r)}:function(t,r,a){var u,l,c,f=w+" "+s;if(a){while(t=t[i])if((1===t.nodeType||o)&&e(t,r,a))return!0}else while(t=t[i])if(1===t.nodeType||o)if(c=t[y]||(t[y]={}),(l=c[i])&&l[0]===f){if((u=l[1])===!0||u===n)return u===!0}else if(l=c[i]=[f],l[1]=e(t,r,a)||n,l[1]===!0)return!0}}function vt(e){return e.length>1?function(t,n,r){var i=e.length;while(i--)if(!e[i](t,n,r))return!1;return!0}:e[0]}function xt(e,t,n,r,i){var o,s=[],a=0,u=e.length,l=null!=t;for(;u>a;a++)(o=e[a])&&(!n||n(o,r,i))&&(s.push(o),l&&t.push(a));return s}function bt(e,t,n,r,i,o){return r&&!r[y]&&(r=bt(r)),i&&!i[y]&&(i=bt(i,o)),st(function(o,s,a,u){var l,c,f,p=[],h=[],d=s.length,g=o||Ct(t||"*",a.nodeType?[a]:a,[]),m=!e||!o&&t?g:xt(g,p,e,a,u),y=n?i||(o?e:d||r)?[]:s:m;if(n&&n(m,y,a,u),r){l=xt(y,h),r(l,[],a,u),c=l.length;while(c--)(f=l[c])&&(y[h[c]]=!(m[h[c]]=f))}if(o){if(i||e){if(i){l=[],c=y.length;while(c--)(f=y[c])&&l.push(m[c]=f);i(null,y=[],l,u)}c=y.length;while(c--)(f=y[c])&&(l=i?F.call(o,f):p[c])>-1&&(o[l]=!(s[l]=f))}}else y=xt(y===s?y.splice(d,y.length):y),i?i(null,s,y,u):H.apply(s,y)})}function wt(e){var t,n,i,o=e.length,s=r.relative[e[0].type],u=s||r.relative[" "],l=s?1:0,c=yt(function(e){return e===t},u,!0),f=yt(function(e){return F.call(t,e)>-1},u,!0),p=[function(e,n,r){return!s&&(r||n!==a)||((t=n).nodeType?c(e,n,r):f(e,n,r))}];for(;o>l;l++)if(n=r.relative[e[l].type])p=[yt(vt(p),n)];else{if(n=r.filter[e[l].type].apply(null,e[l].matches),n[y]){for(i=++l;o>i;i++)if(r.relative[e[i].type])break;return bt(l>1&&vt(p),l>1&&mt(e.slice(0,l-1)).replace(I,"$1"),n,i>l&&wt(e.slice(l,i)),o>i&&wt(e=e.slice(i)),o>i&&mt(e))}p.push(n)}return vt(p)}function Tt(e,t){var i=0,o=t.length>0,s=e.length>0,u=function(u,l,f,p,h){var d,g,m,y=[],v=0,x="0",b=u&&[],T=null!=h,C=a,k=u||s&&r.find.TAG("*",h&&l.parentNode||l),N=w+=null==C?1:Math.random()||.1;for(T&&(a=l!==c&&l,n=i);null!=(d=k[x]);x++){if(s&&d){g=0;while(m=e[g++])if(m(d,l,f)){p.push(d);break}T&&(w=N,n=++i)}o&&((d=!m&&d)&&v--,u&&b.push(d))}if(v+=x,o&&x!==v){g=0;while(m=t[g++])m(b,y,l,f);if(u){if(v>0)while(x--)b[x]||y[x]||(y[x]=L.call(p));y=xt(y)}H.apply(p,y),T&&!u&&y.length>0&&v+t.length>1&&ut.uniqueSort(p)}return T&&(w=N,a=C),b};return o?st(u):u}s=ut.compile=function(e,t){var n,r=[],i=[],o=N[e+" "];if(!o){t||(t=gt(e)),n=t.length;while(n--)o=wt(t[n]),o[y]?r.push(o):i.push(o);o=N(e,Tt(i,r))}return o};function Ct(e,t,n){var r=0,i=t.length;for(;i>r;r++)ut(e,t[r],n);return n}function kt(e,t,n,i){var o,a,u,l,c,f=gt(e);if(!i&&1===f.length){if(a=f[0]=f[0].slice(0),a.length>2&&"ID"===(u=a[0]).type&&9===t.nodeType&&p&&r.relative[a[1].type]){if(t=(r.find.ID(u.matches[0].replace(tt,nt),t)||[])[0],!t)return n;e=e.slice(a.shift().value.length)}o=G.needsContext.test(e)?0:a.length;while(o--){if(u=a[o],r.relative[l=u.type])break;if((c=r.find[l])&&(i=c(u.matches[0].replace(tt,nt),X.test(a[0].type)&&t.parentNode||t))){if(a.splice(o,1),e=i.length&&mt(a),!e)return H.apply(n,i),n;break}}}return s(e,f)(i,t,!p,n,X.test(e)),n}r.pseudos.nth=r.pseudos.eq;function Nt(){}Nt.prototype=r.filters=r.pseudos,r.setFilters=new Nt,b.sortStable=y.split("").sort(S).join("")===y,l(),[0,0].sort(S),b.detectDuplicates=E,at(function(e){if(e.innerHTML="<a href='#'></a>","#"!==e.firstChild.getAttribute("href")){var t="type|href|height|width".split("|"),n=t.length;while(n--)r.attrHandle[t[n]]=ft}}),at(function(e){if(null!=e.getAttribute("disabled")){var t=P.split("|"),n=t.length;while(n--)r.attrHandle[t[n]]=ct}}),x.find=ut,x.expr=ut.selectors,x.expr[":"]=x.expr.pseudos,x.unique=ut.uniqueSort,x.text=ut.getText,x.isXMLDoc=ut.isXML,x.contains=ut.contains}(e);var D={};function A(e){var t=D[e]={};return x.each(e.match(w)||[],function(e,n){t[n]=!0}),t}x.Callbacks=function(e){e="string"==typeof e?D[e]||A(e):x.extend({},e);var t,n,r,i,o,s,a=[],u=!e.once&&[],l=function(f){for(t=e.memory&&f,n=!0,s=i||0,i=0,o=a.length,r=!0;a&&o>s;s++)if(a[s].apply(f[0],f[1])===!1&&e.stopOnFalse){t=!1;break}r=!1,a&&(u?u.length&&l(u.shift()):t?a=[]:c.disable())},c={add:function(){if(a){var n=a.length;(function s(t){x.each(t,function(t,n){var r=x.type(n);"function"===r?e.unique&&c.has(n)||a.push(n):n&&n.length&&"string"!==r&&s(n)})})(arguments),r?o=a.length:t&&(i=n,l(t))}return this},remove:function(){return a&&x.each(arguments,function(e,t){var n;while((n=x.inArray(t,a,n))>-1)a.splice(n,1),r&&(o>=n&&o--,s>=n&&s--)}),this},has:function(e){return e?x.inArray(e,a)>-1:!(!a||!a.length)},empty:function(){return a=[],o=0,this},disable:function(){return a=u=t=undefined,this},disabled:function(){return!a},lock:function(){return u=undefined,t||c.disable(),this},locked:function(){return!u},fireWith:function(e,t){return t=t||[],t=[e,t.slice?t.slice():t],!a||n&&!u||(r?u.push(t):l(t)),this},fire:function(){return c.fireWith(this,arguments),this},fired:function(){return!!n}};return c},x.extend({Deferred:function(e){var t=[["resolve","done",x.Callbacks("once memory"),"resolved"],["reject","fail",x.Callbacks("once memory"),"rejected"],["notify","progress",x.Callbacks("memory")]],n="pending",r={state:function(){return n},always:function(){return i.done(arguments).fail(arguments),this},then:function(){var e=arguments;return x.Deferred(function(n){x.each(t,function(t,o){var s=o[0],a=x.isFunction(e[t])&&e[t];i[o[1]](function(){var e=a&&a.apply(this,arguments);e&&x.isFunction(e.promise)?e.promise().done(n.resolve).fail(n.reject).progress(n.notify):n[s+"With"](this===r?n.promise():this,a?[e]:arguments)})}),e=null}).promise()},promise:function(e){return null!=e?x.extend(e,r):r}},i={};return r.pipe=r.then,x.each(t,function(e,o){var s=o[2],a=o[3];r[o[1]]=s.add,a&&s.add(function(){n=a},t[1^e][2].disable,t[2][2].lock),i[o[0]]=function(){return i[o[0]+"With"](this===i?r:this,arguments),this},i[o[0]+"With"]=s.fireWith}),r.promise(i),e&&e.call(i,i),i},when:function(e){var t=0,n=d.call(arguments),r=n.length,i=1!==r||e&&x.isFunction(e.promise)?r:0,o=1===i?e:x.Deferred(),s=function(e,t,n){return function(r){t[e]=this,n[e]=arguments.length>1?d.call(arguments):r,n===a?o.notifyWith(t,n):--i||o.resolveWith(t,n)}},a,u,l;if(r>1)for(a=Array(r),u=Array(r),l=Array(r);r>t;t++)n[t]&&x.isFunction(n[t].promise)?n[t].promise().done(s(t,l,n)).fail(o.reject).progress(s(t,u,a)):--i;return i||o.resolveWith(l,n),o.promise()}}),x.support=function(t){var n=o.createElement("input"),r=o.createDocumentFragment(),i=o.createElement("div"),s=o.createElement("select"),a=s.appendChild(o.createElement("option"));return n.type?(n.type="checkbox",t.checkOn=""!==n.value,t.optSelected=a.selected,t.reliableMarginRight=!0,t.boxSizingReliable=!0,t.pixelPosition=!1,n.checked=!0,t.noCloneChecked=n.cloneNode(!0).checked,s.disabled=!0,t.optDisabled=!a.disabled,n=o.createElement("input"),n.value="t",n.type="radio",t.radioValue="t"===n.value,n.setAttribute("checked","t"),n.setAttribute("name","t"),r.appendChild(n),t.checkClone=r.cloneNode(!0).cloneNode(!0).lastChild.checked,t.focusinBubbles="onfocusin"in e,i.style.backgroundClip="content-box",i.cloneNode(!0).style.backgroundClip="",t.clearCloneStyle="content-box"===i.style.backgroundClip,x(function(){var n,r,s="padding:0;margin:0;border:0;display:block;-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box",a=o.getElementsByTagName("body")[0];a&&(n=o.createElement("div"),n.style.cssText="border:0;width:0;height:0;position:absolute;top:0;left:-9999px;margin-top:1px",a.appendChild(n).appendChild(i),i.innerHTML="",i.style.cssText="-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;padding:1px;border:1px;display:block;width:4px;margin-top:1%;position:absolute;top:1%",x.swap(a,null!=a.style.zoom?{zoom:1}:{},function(){t.boxSizing=4===i.offsetWidth}),e.getComputedStyle&&(t.pixelPosition="1%"!==(e.getComputedStyle(i,null)||{}).top,t.boxSizingReliable="4px"===(e.getComputedStyle(i,null)||{width:"4px"}).width,r=i.appendChild(o.createElement("div")),r.style.cssText=i.style.cssText=s,r.style.marginRight=r.style.width="0",i.style.width="1px",t.reliableMarginRight=!parseFloat((e.getComputedStyle(r,null)||{}).marginRight)),a.removeChild(n))}),t):t}({});var L,q,H=/(?:\{[\s\S]*\}|\[[\s\S]*\])$/,O=/([A-Z])/g;function F(){Object.defineProperty(this.cache={},0,{get:function(){return{}}}),this.expando=x.expando+Math.random()}F.uid=1,F.accepts=function(e){return e.nodeType?1===e.nodeType||9===e.nodeType:!0},F.prototype={key:function(e){if(!F.accepts(e))return 0;var t={},n=e[this.expando];if(!n){n=F.uid++;try{t[this.expando]={value:n},Object.defineProperties(e,t)}catch(r){t[this.expando]=n,x.extend(e,t)}}return this.cache[n]||(this.cache[n]={}),n},set:function(e,t,n){var r,i=this.key(e),o=this.cache[i];if("string"==typeof t)o[t]=n;else if(x.isEmptyObject(o))this.cache[i]=t;else for(r in t)o[r]=t[r]},get:function(e,t){var n=this.cache[this.key(e)];return t===undefined?n:n[t]},access:function(e,t,n){return t===undefined||t&&"string"==typeof t&&n===undefined?this.get(e,t):(this.set(e,t,n),n!==undefined?n:t)},remove:function(e,t){var n,r,i=this.key(e),o=this.cache[i];if(t===undefined)this.cache[i]={};else{x.isArray(t)?r=t.concat(t.map(x.camelCase)):t in o?r=[t]:(r=x.camelCase(t),r=r in o?[r]:r.match(w)||[]),n=r.length;while(n--)delete o[r[n]]}},hasData:function(e){return!x.isEmptyObject(this.cache[e[this.expando]]||{})},discard:function(e){delete this.cache[this.key(e)]}},L=new F,q=new F,x.extend({acceptData:F.accepts,hasData:function(e){return L.hasData(e)||q.hasData(e)},data:function(e,t,n){return L.access(e,t,n)},removeData:function(e,t){L.remove(e,t)},_data:function(e,t,n){return q.access(e,t,n)},_removeData:function(e,t){q.remove(e,t)}}),x.fn.extend({data:function(e,t){var n,r,i=this[0],o=0,s=null;if(e===undefined){if(this.length&&(s=L.get(i),1===i.nodeType&&!q.get(i,"hasDataAttrs"))){for(n=i.attributes;n.length>o;o++)r=n[o].name,0===r.indexOf("data-")&&(r=x.camelCase(r.substring(5)),P(i,r,s[r]));q.set(i,"hasDataAttrs",!0)}return s}return"object"==typeof e?this.each(function(){L.set(this,e)}):x.access(this,function(t){var n,r=x.camelCase(e);if(i&&t===undefined){if(n=L.get(i,e),n!==undefined)return n;if(n=L.get(i,r),n!==undefined)return n;if(n=P(i,r,undefined),n!==undefined)return n}else this.each(function(){var n=L.get(this,r);L.set(this,r,t),-1!==e.indexOf("-")&&n!==undefined&&L.set(this,e,t)})},null,t,arguments.length>1,null,!0)},removeData:function(e){return this.each(function(){L.remove(this,e)})}});function P(e,t,n){var r;if(n===undefined&&1===e.nodeType)if(r="data-"+t.replace(O,"-$1").toLowerCase(),n=e.getAttribute(r),"string"==typeof n){try{n="true"===n?!0:"false"===n?!1:"null"===n?null:+n+""===n?+n:H.test(n)?JSON.parse(n):n}catch(i){}L.set(e,t,n)}else n=undefined;return n}x.extend({queue:function(e,t,n){var r;return e?(t=(t||"fx")+"queue",r=q.get(e,t),n&&(!r||x.isArray(n)?r=q.access(e,t,x.makeArray(n)):r.push(n)),r||[]):undefined},dequeue:function(e,t){t=t||"fx";var n=x.queue(e,t),r=n.length,i=n.shift(),o=x._queueHooks(e,t),s=function(){x.dequeue(e,t)};"inprogress"===i&&(i=n.shift(),r--),o.cur=i,i&&("fx"===t&&n.unshift("inprogress"),delete o.stop,i.call(e,s,o)),!r&&o&&o.empty.fire()},_queueHooks:function(e,t){var n=t+"queueHooks";return q.get(e,n)||q.access(e,n,{empty:x.Callbacks("once memory").add(function(){q.remove(e,[t+"queue",n])})})}}),x.fn.extend({queue:function(e,t){var n=2;return"string"!=typeof e&&(t=e,e="fx",n--),n>arguments.length?x.queue(this[0],e):t===undefined?this:this.each(function(){var n=x.queue(this,e,t);
x._queueHooks(this,e),"fx"===e&&"inprogress"!==n[0]&&x.dequeue(this,e)})},dequeue:function(e){return this.each(function(){x.dequeue(this,e)})},delay:function(e,t){return e=x.fx?x.fx.speeds[e]||e:e,t=t||"fx",this.queue(t,function(t,n){var r=setTimeout(t,e);n.stop=function(){clearTimeout(r)}})},clearQueue:function(e){return this.queue(e||"fx",[])},promise:function(e,t){var n,r=1,i=x.Deferred(),o=this,s=this.length,a=function(){--r||i.resolveWith(o,[o])};"string"!=typeof e&&(t=e,e=undefined),e=e||"fx";while(s--)n=q.get(o[s],e+"queueHooks"),n&&n.empty&&(r++,n.empty.add(a));return a(),i.promise(t)}});var R,M,W=/[\t\r\n]/g,$=/\r/g,B=/^(?:input|select|textarea|button)$/i;x.fn.extend({attr:function(e,t){return x.access(this,x.attr,e,t,arguments.length>1)},removeAttr:function(e){return this.each(function(){x.removeAttr(this,e)})},prop:function(e,t){return x.access(this,x.prop,e,t,arguments.length>1)},removeProp:function(e){return this.each(function(){delete this[x.propFix[e]||e]})},addClass:function(e){var t,n,r,i,o,s=0,a=this.length,u="string"==typeof e&&e;if(x.isFunction(e))return this.each(function(t){x(this).addClass(e.call(this,t,this.className))});if(u)for(t=(e||"").match(w)||[];a>s;s++)if(n=this[s],r=1===n.nodeType&&(n.className?(" "+n.className+" ").replace(W," "):" ")){o=0;while(i=t[o++])0>r.indexOf(" "+i+" ")&&(r+=i+" ");n.className=x.trim(r)}return this},removeClass:function(e){var t,n,r,i,o,s=0,a=this.length,u=0===arguments.length||"string"==typeof e&&e;if(x.isFunction(e))return this.each(function(t){x(this).removeClass(e.call(this,t,this.className))});if(u)for(t=(e||"").match(w)||[];a>s;s++)if(n=this[s],r=1===n.nodeType&&(n.className?(" "+n.className+" ").replace(W," "):"")){o=0;while(i=t[o++])while(r.indexOf(" "+i+" ")>=0)r=r.replace(" "+i+" "," ");n.className=e?x.trim(r):""}return this},toggleClass:function(e,t){var n=typeof e,i="boolean"==typeof t;return x.isFunction(e)?this.each(function(n){x(this).toggleClass(e.call(this,n,this.className,t),t)}):this.each(function(){if("string"===n){var o,s=0,a=x(this),u=t,l=e.match(w)||[];while(o=l[s++])u=i?u:!a.hasClass(o),a[u?"addClass":"removeClass"](o)}else(n===r||"boolean"===n)&&(this.className&&q.set(this,"__className__",this.className),this.className=this.className||e===!1?"":q.get(this,"__className__")||"")})},hasClass:function(e){var t=" "+e+" ",n=0,r=this.length;for(;r>n;n++)if(1===this[n].nodeType&&(" "+this[n].className+" ").replace(W," ").indexOf(t)>=0)return!0;return!1},val:function(e){var t,n,r,i=this[0];{if(arguments.length)return r=x.isFunction(e),this.each(function(n){var i,o=x(this);1===this.nodeType&&(i=r?e.call(this,n,o.val()):e,null==i?i="":"number"==typeof i?i+="":x.isArray(i)&&(i=x.map(i,function(e){return null==e?"":e+""})),t=x.valHooks[this.type]||x.valHooks[this.nodeName.toLowerCase()],t&&"set"in t&&t.set(this,i,"value")!==undefined||(this.value=i))});if(i)return t=x.valHooks[i.type]||x.valHooks[i.nodeName.toLowerCase()],t&&"get"in t&&(n=t.get(i,"value"))!==undefined?n:(n=i.value,"string"==typeof n?n.replace($,""):null==n?"":n)}}}),x.extend({valHooks:{option:{get:function(e){var t=e.attributes.value;return!t||t.specified?e.value:e.text}},select:{get:function(e){var t,n,r=e.options,i=e.selectedIndex,o="select-one"===e.type||0>i,s=o?null:[],a=o?i+1:r.length,u=0>i?a:o?i:0;for(;a>u;u++)if(n=r[u],!(!n.selected&&u!==i||(x.support.optDisabled?n.disabled:null!==n.getAttribute("disabled"))||n.parentNode.disabled&&x.nodeName(n.parentNode,"optgroup"))){if(t=x(n).val(),o)return t;s.push(t)}return s},set:function(e,t){var n,r,i=e.options,o=x.makeArray(t),s=i.length;while(s--)r=i[s],(r.selected=x.inArray(x(r).val(),o)>=0)&&(n=!0);return n||(e.selectedIndex=-1),o}}},attr:function(e,t,n){var i,o,s=e.nodeType;if(e&&3!==s&&8!==s&&2!==s)return typeof e.getAttribute===r?x.prop(e,t,n):(1===s&&x.isXMLDoc(e)||(t=t.toLowerCase(),i=x.attrHooks[t]||(x.expr.match.boolean.test(t)?M:R)),n===undefined?i&&"get"in i&&null!==(o=i.get(e,t))?o:(o=x.find.attr(e,t),null==o?undefined:o):null!==n?i&&"set"in i&&(o=i.set(e,n,t))!==undefined?o:(e.setAttribute(t,n+""),n):(x.removeAttr(e,t),undefined))},removeAttr:function(e,t){var n,r,i=0,o=t&&t.match(w);if(o&&1===e.nodeType)while(n=o[i++])r=x.propFix[n]||n,x.expr.match.boolean.test(n)&&(e[r]=!1),e.removeAttribute(n)},attrHooks:{type:{set:function(e,t){if(!x.support.radioValue&&"radio"===t&&x.nodeName(e,"input")){var n=e.value;return e.setAttribute("type",t),n&&(e.value=n),t}}}},propFix:{"for":"htmlFor","class":"className"},prop:function(e,t,n){var r,i,o,s=e.nodeType;if(e&&3!==s&&8!==s&&2!==s)return o=1!==s||!x.isXMLDoc(e),o&&(t=x.propFix[t]||t,i=x.propHooks[t]),n!==undefined?i&&"set"in i&&(r=i.set(e,n,t))!==undefined?r:e[t]=n:i&&"get"in i&&null!==(r=i.get(e,t))?r:e[t]},propHooks:{tabIndex:{get:function(e){return e.hasAttribute("tabindex")||B.test(e.nodeName)||e.href?e.tabIndex:-1}}}}),M={set:function(e,t,n){return t===!1?x.removeAttr(e,n):e.setAttribute(n,n),n}},x.each(x.expr.match.boolean.source.match(/\w+/g),function(e,t){var n=x.expr.attrHandle[t]||x.find.attr;x.expr.attrHandle[t]=function(e,t,r){var i=x.expr.attrHandle[t],o=r?undefined:(x.expr.attrHandle[t]=undefined)!=n(e,t,r)?t.toLowerCase():null;return x.expr.attrHandle[t]=i,o}}),x.support.optSelected||(x.propHooks.selected={get:function(e){var t=e.parentNode;return t&&t.parentNode&&t.parentNode.selectedIndex,null}}),x.each(["tabIndex","readOnly","maxLength","cellSpacing","cellPadding","rowSpan","colSpan","useMap","frameBorder","contentEditable"],function(){x.propFix[this.toLowerCase()]=this}),x.each(["radio","checkbox"],function(){x.valHooks[this]={set:function(e,t){return x.isArray(t)?e.checked=x.inArray(x(e).val(),t)>=0:undefined}},x.support.checkOn||(x.valHooks[this].get=function(e){return null===e.getAttribute("value")?"on":e.value})});var I=/^key/,z=/^(?:mouse|contextmenu)|click/,_=/^(?:focusinfocus|focusoutblur)$/,X=/^([^.]*)(?:\.(.+)|)$/;function U(){return!0}function Y(){return!1}function V(){try{return o.activeElement}catch(e){}}x.event={global:{},add:function(e,t,n,i,o){var s,a,u,l,c,f,p,h,d,g,m,y=q.get(e);if(y){n.handler&&(s=n,n=s.handler,o=s.selector),n.guid||(n.guid=x.guid++),(l=y.events)||(l=y.events={}),(a=y.handle)||(a=y.handle=function(e){return typeof x===r||e&&x.event.triggered===e.type?undefined:x.event.dispatch.apply(a.elem,arguments)},a.elem=e),t=(t||"").match(w)||[""],c=t.length;while(c--)u=X.exec(t[c])||[],d=m=u[1],g=(u[2]||"").split(".").sort(),d&&(p=x.event.special[d]||{},d=(o?p.delegateType:p.bindType)||d,p=x.event.special[d]||{},f=x.extend({type:d,origType:m,data:i,handler:n,guid:n.guid,selector:o,needsContext:o&&x.expr.match.needsContext.test(o),namespace:g.join(".")},s),(h=l[d])||(h=l[d]=[],h.delegateCount=0,p.setup&&p.setup.call(e,i,g,a)!==!1||e.addEventListener&&e.addEventListener(d,a,!1)),p.add&&(p.add.call(e,f),f.handler.guid||(f.handler.guid=n.guid)),o?h.splice(h.delegateCount++,0,f):h.push(f),x.event.global[d]=!0);e=null}},remove:function(e,t,n,r,i){var o,s,a,u,l,c,f,p,h,d,g,m=q.hasData(e)&&q.get(e);if(m&&(u=m.events)){t=(t||"").match(w)||[""],l=t.length;while(l--)if(a=X.exec(t[l])||[],h=g=a[1],d=(a[2]||"").split(".").sort(),h){f=x.event.special[h]||{},h=(r?f.delegateType:f.bindType)||h,p=u[h]||[],a=a[2]&&RegExp("(^|\\.)"+d.join("\\.(?:.*\\.|)")+"(\\.|$)"),s=o=p.length;while(o--)c=p[o],!i&&g!==c.origType||n&&n.guid!==c.guid||a&&!a.test(c.namespace)||r&&r!==c.selector&&("**"!==r||!c.selector)||(p.splice(o,1),c.selector&&p.delegateCount--,f.remove&&f.remove.call(e,c));s&&!p.length&&(f.teardown&&f.teardown.call(e,d,m.handle)!==!1||x.removeEvent(e,h,m.handle),delete u[h])}else for(h in u)x.event.remove(e,h+t[l],n,r,!0);x.isEmptyObject(u)&&(delete m.handle,q.remove(e,"events"))}},trigger:function(t,n,r,i){var s,a,u,l,c,f,p,h=[r||o],d=y.call(t,"type")?t.type:t,g=y.call(t,"namespace")?t.namespace.split("."):[];if(a=u=r=r||o,3!==r.nodeType&&8!==r.nodeType&&!_.test(d+x.event.triggered)&&(d.indexOf(".")>=0&&(g=d.split("."),d=g.shift(),g.sort()),c=0>d.indexOf(":")&&"on"+d,t=t[x.expando]?t:new x.Event(d,"object"==typeof t&&t),t.isTrigger=i?2:3,t.namespace=g.join("."),t.namespace_re=t.namespace?RegExp("(^|\\.)"+g.join("\\.(?:.*\\.|)")+"(\\.|$)"):null,t.result=undefined,t.target||(t.target=r),n=null==n?[t]:x.makeArray(n,[t]),p=x.event.special[d]||{},i||!p.trigger||p.trigger.apply(r,n)!==!1)){if(!i&&!p.noBubble&&!x.isWindow(r)){for(l=p.delegateType||d,_.test(l+d)||(a=a.parentNode);a;a=a.parentNode)h.push(a),u=a;u===(r.ownerDocument||o)&&h.push(u.defaultView||u.parentWindow||e)}s=0;while((a=h[s++])&&!t.isPropagationStopped())t.type=s>1?l:p.bindType||d,f=(q.get(a,"events")||{})[t.type]&&q.get(a,"handle"),f&&f.apply(a,n),f=c&&a[c],f&&x.acceptData(a)&&f.apply&&f.apply(a,n)===!1&&t.preventDefault();return t.type=d,i||t.isDefaultPrevented()||p._default&&p._default.apply(h.pop(),n)!==!1||!x.acceptData(r)||c&&x.isFunction(r[d])&&!x.isWindow(r)&&(u=r[c],u&&(r[c]=null),x.event.triggered=d,r[d](),x.event.triggered=undefined,u&&(r[c]=u)),t.result}},dispatch:function(e){e=x.event.fix(e);var t,n,r,i,o,s=[],a=d.call(arguments),u=(q.get(this,"events")||{})[e.type]||[],l=x.event.special[e.type]||{};if(a[0]=e,e.delegateTarget=this,!l.preDispatch||l.preDispatch.call(this,e)!==!1){s=x.event.handlers.call(this,e,u),t=0;while((i=s[t++])&&!e.isPropagationStopped()){e.currentTarget=i.elem,n=0;while((o=i.handlers[n++])&&!e.isImmediatePropagationStopped())(!e.namespace_re||e.namespace_re.test(o.namespace))&&(e.handleObj=o,e.data=o.data,r=((x.event.special[o.origType]||{}).handle||o.handler).apply(i.elem,a),r!==undefined&&(e.result=r)===!1&&(e.preventDefault(),e.stopPropagation()))}return l.postDispatch&&l.postDispatch.call(this,e),e.result}},handlers:function(e,t){var n,r,i,o,s=[],a=t.delegateCount,u=e.target;if(a&&u.nodeType&&(!e.button||"click"!==e.type))for(;u!==this;u=u.parentNode||this)if(u.disabled!==!0||"click"!==e.type){for(r=[],n=0;a>n;n++)o=t[n],i=o.selector+" ",r[i]===undefined&&(r[i]=o.needsContext?x(i,this).index(u)>=0:x.find(i,this,null,[u]).length),r[i]&&r.push(o);r.length&&s.push({elem:u,handlers:r})}return t.length>a&&s.push({elem:this,handlers:t.slice(a)}),s},props:"altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),fixHooks:{},keyHooks:{props:"char charCode key keyCode".split(" "),filter:function(e,t){return null==e.which&&(e.which=null!=t.charCode?t.charCode:t.keyCode),e}},mouseHooks:{props:"button buttons clientX clientY offsetX offsetY pageX pageY screenX screenY toElement".split(" "),filter:function(e,t){var n,r,i,s=t.button;return null==e.pageX&&null!=t.clientX&&(n=e.target.ownerDocument||o,r=n.documentElement,i=n.body,e.pageX=t.clientX+(r&&r.scrollLeft||i&&i.scrollLeft||0)-(r&&r.clientLeft||i&&i.clientLeft||0),e.pageY=t.clientY+(r&&r.scrollTop||i&&i.scrollTop||0)-(r&&r.clientTop||i&&i.clientTop||0)),e.which||s===undefined||(e.which=1&s?1:2&s?3:4&s?2:0),e}},fix:function(e){if(e[x.expando])return e;var t,n,r,i=e.type,o=e,s=this.fixHooks[i];s||(this.fixHooks[i]=s=z.test(i)?this.mouseHooks:I.test(i)?this.keyHooks:{}),r=s.props?this.props.concat(s.props):this.props,e=new x.Event(o),t=r.length;while(t--)n=r[t],e[n]=o[n];return 3===e.target.nodeType&&(e.target=e.target.parentNode),s.filter?s.filter(e,o):e},special:{load:{noBubble:!0},focus:{trigger:function(){return this!==V()&&this.focus?(this.focus(),!1):undefined},delegateType:"focusin"},blur:{trigger:function(){return this===V()&&this.blur?(this.blur(),!1):undefined},delegateType:"focusout"},click:{trigger:function(){return"checkbox"===this.type&&this.click&&x.nodeName(this,"input")?(this.click(),!1):undefined},_default:function(e){return x.nodeName(e.target,"a")}},beforeunload:{postDispatch:function(e){e.result!==undefined&&(e.originalEvent.returnValue=e.result)}}},simulate:function(e,t,n,r){var i=x.extend(new x.Event,n,{type:e,isSimulated:!0,originalEvent:{}});r?x.event.trigger(i,null,t):x.event.dispatch.call(t,i),i.isDefaultPrevented()&&n.preventDefault()}},x.removeEvent=function(e,t,n){e.removeEventListener&&e.removeEventListener(t,n,!1)},x.Event=function(e,t){return this instanceof x.Event?(e&&e.type?(this.originalEvent=e,this.type=e.type,this.isDefaultPrevented=e.defaultPrevented||e.getPreventDefault&&e.getPreventDefault()?U:Y):this.type=e,t&&x.extend(this,t),this.timeStamp=e&&e.timeStamp||x.now(),this[x.expando]=!0,undefined):new x.Event(e,t)},x.Event.prototype={isDefaultPrevented:Y,isPropagationStopped:Y,isImmediatePropagationStopped:Y,preventDefault:function(){var e=this.originalEvent;this.isDefaultPrevented=U,e&&e.preventDefault&&e.preventDefault()},stopPropagation:function(){var e=this.originalEvent;this.isPropagationStopped=U,e&&e.stopPropagation&&e.stopPropagation()},stopImmediatePropagation:function(){this.isImmediatePropagationStopped=U,this.stopPropagation()}},x.each({mouseenter:"mouseover",mouseleave:"mouseout"},function(e,t){x.event.special[e]={delegateType:t,bindType:t,handle:function(e){var n,r=this,i=e.relatedTarget,o=e.handleObj;return(!i||i!==r&&!x.contains(r,i))&&(e.type=o.origType,n=o.handler.apply(this,arguments),e.type=t),n}}}),x.support.focusinBubbles||x.each({focus:"focusin",blur:"focusout"},function(e,t){var n=0,r=function(e){x.event.simulate(t,e.target,x.event.fix(e),!0)};x.event.special[t]={setup:function(){0===n++&&o.addEventListener(e,r,!0)},teardown:function(){0===--n&&o.removeEventListener(e,r,!0)}}}),x.fn.extend({on:function(e,t,n,r,i){var o,s;if("object"==typeof e){"string"!=typeof t&&(n=n||t,t=undefined);for(s in e)this.on(s,t,n,e[s],i);return this}if(null==n&&null==r?(r=t,n=t=undefined):null==r&&("string"==typeof t?(r=n,n=undefined):(r=n,n=t,t=undefined)),r===!1)r=Y;else if(!r)return this;return 1===i&&(o=r,r=function(e){return x().off(e),o.apply(this,arguments)},r.guid=o.guid||(o.guid=x.guid++)),this.each(function(){x.event.add(this,e,r,n,t)})},one:function(e,t,n,r){return this.on(e,t,n,r,1)},off:function(e,t,n){var r,i;if(e&&e.preventDefault&&e.handleObj)return r=e.handleObj,x(e.delegateTarget).off(r.namespace?r.origType+"."+r.namespace:r.origType,r.selector,r.handler),this;if("object"==typeof e){for(i in e)this.off(i,t,e[i]);return this}return(t===!1||"function"==typeof t)&&(n=t,t=undefined),n===!1&&(n=Y),this.each(function(){x.event.remove(this,e,n,t)})},trigger:function(e,t){return this.each(function(){x.event.trigger(e,t,this)})},triggerHandler:function(e,t){var n=this[0];return n?x.event.trigger(e,t,n,!0):undefined}});var G=/^.[^:#\[\.,]*$/,J=x.expr.match.needsContext,Q={children:!0,contents:!0,next:!0,prev:!0};x.fn.extend({find:function(e){var t,n,r,i=this.length;if("string"!=typeof e)return t=this,this.pushStack(x(e).filter(function(){for(r=0;i>r;r++)if(x.contains(t[r],this))return!0}));for(n=[],r=0;i>r;r++)x.find(e,this[r],n);return n=this.pushStack(i>1?x.unique(n):n),n.selector=(this.selector?this.selector+" ":"")+e,n},has:function(e){var t=x(e,this),n=t.length;return this.filter(function(){var e=0;for(;n>e;e++)if(x.contains(this,t[e]))return!0})},not:function(e){return this.pushStack(Z(this,e||[],!0))},filter:function(e){return this.pushStack(Z(this,e||[],!1))},is:function(e){return!!e&&("string"==typeof e?J.test(e)?x(e,this.context).index(this[0])>=0:x.filter(e,this).length>0:this.filter(e).length>0)},closest:function(e,t){var n,r=0,i=this.length,o=[],s=J.test(e)||"string"!=typeof e?x(e,t||this.context):0;for(;i>r;r++)for(n=this[r];n&&n!==t;n=n.parentNode)if(11>n.nodeType&&(s?s.index(n)>-1:1===n.nodeType&&x.find.matchesSelector(n,e))){n=o.push(n);break}return this.pushStack(o.length>1?x.unique(o):o)},index:function(e){return e?"string"==typeof e?g.call(x(e),this[0]):g.call(this,e.jquery?e[0]:e):this[0]&&this[0].parentNode?this.first().prevAll().length:-1},add:function(e,t){var n="string"==typeof e?x(e,t):x.makeArray(e&&e.nodeType?[e]:e),r=x.merge(this.get(),n);return this.pushStack(x.unique(r))},addBack:function(e){return this.add(null==e?this.prevObject:this.prevObject.filter(e))}});function K(e,t){while((e=e[t])&&1!==e.nodeType);return e}x.each({parent:function(e){var t=e.parentNode;return t&&11!==t.nodeType?t:null},parents:function(e){return x.dir(e,"parentNode")},parentsUntil:function(e,t,n){return x.dir(e,"parentNode",n)},next:function(e){return K(e,"nextSibling")},prev:function(e){return K(e,"previousSibling")},nextAll:function(e){return x.dir(e,"nextSibling")},prevAll:function(e){return x.dir(e,"previousSibling")},nextUntil:function(e,t,n){return x.dir(e,"nextSibling",n)},prevUntil:function(e,t,n){return x.dir(e,"previousSibling",n)},siblings:function(e){return x.sibling((e.parentNode||{}).firstChild,e)},children:function(e){return x.sibling(e.firstChild)},contents:function(e){return x.nodeName(e,"iframe")?e.contentDocument||e.contentWindow.document:x.merge([],e.childNodes)}},function(e,t){x.fn[e]=function(n,r){var i=x.map(this,t,n);return"Until"!==e.slice(-5)&&(r=n),r&&"string"==typeof r&&(i=x.filter(r,i)),this.length>1&&(Q[e]||x.unique(i),"p"===e[0]&&i.reverse()),this.pushStack(i)}}),x.extend({filter:function(e,t,n){var r=t[0];return n&&(e=":not("+e+")"),1===t.length&&1===r.nodeType?x.find.matchesSelector(r,e)?[r]:[]:x.find.matches(e,x.grep(t,function(e){return 1===e.nodeType}))},dir:function(e,t,n){var r=[],i=n!==undefined;while((e=e[t])&&9!==e.nodeType)if(1===e.nodeType){if(i&&x(e).is(n))break;r.push(e)}return r},sibling:function(e,t){var n=[];for(;e;e=e.nextSibling)1===e.nodeType&&e!==t&&n.push(e);return n}});function Z(e,t,n){if(x.isFunction(t))return x.grep(e,function(e,r){return!!t.call(e,r,e)!==n});if(t.nodeType)return x.grep(e,function(e){return e===t!==n});if("string"==typeof t){if(G.test(t))return x.filter(t,e,n);t=x.filter(t,e)}return x.grep(e,function(e){return g.call(t,e)>=0!==n})}var et=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,tt=/<([\w:]+)/,nt=/<|&#?\w+;/,rt=/<(?:script|style|link)/i,it=/^(?:checkbox|radio)$/i,ot=/checked\s*(?:[^=]|=\s*.checked.)/i,st=/^$|\/(?:java|ecma)script/i,at=/^true\/(.*)/,ut=/^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,lt={option:[1,"<select multiple='multiple'>","</select>"],thead:[1,"<table>","</table>"],tr:[2,"<table><tbody>","</tbody></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],_default:[0,"",""]};lt.optgroup=lt.option,lt.tbody=lt.tfoot=lt.colgroup=lt.caption=lt.col=lt.thead,lt.th=lt.td,x.fn.extend({text:function(e){return x.access(this,function(e){return e===undefined?x.text(this):this.empty().append((this[0]&&this[0].ownerDocument||o).createTextNode(e))},null,e,arguments.length)},append:function(){return this.domManip(arguments,function(e){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var t=ct(this,e);t.appendChild(e)}})},prepend:function(){return this.domManip(arguments,function(e){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var t=ct(this,e);t.insertBefore(e,t.firstChild)}})},before:function(){return this.domManip(arguments,function(e){this.parentNode&&this.parentNode.insertBefore(e,this)})},after:function(){return this.domManip(arguments,function(e){this.parentNode&&this.parentNode.insertBefore(e,this.nextSibling)})},remove:function(e,t){var n,r=e?x.filter(e,this):this,i=0;for(;null!=(n=r[i]);i++)t||1!==n.nodeType||x.cleanData(gt(n)),n.parentNode&&(t&&x.contains(n.ownerDocument,n)&&ht(gt(n,"script")),n.parentNode.removeChild(n));return this},empty:function(){var e,t=0;for(;null!=(e=this[t]);t++)1===e.nodeType&&(x.cleanData(gt(e,!1)),e.textContent="");return this},clone:function(e,t){return e=null==e?!1:e,t=null==t?e:t,this.map(function(){return x.clone(this,e,t)})},html:function(e){return x.access(this,function(e){var t=this[0]||{},n=0,r=this.length;if(e===undefined&&1===t.nodeType)return t.innerHTML;if("string"==typeof e&&!rt.test(e)&&!lt[(tt.exec(e)||["",""])[1].toLowerCase()]){e=e.replace(et,"<$1></$2>");try{for(;r>n;n++)t=this[n]||{},1===t.nodeType&&(x.cleanData(gt(t,!1)),t.innerHTML=e);t=0}catch(i){}}t&&this.empty().append(e)},null,e,arguments.length)},replaceWith:function(){var e=x.map(this,function(e){return[e.nextSibling,e.parentNode]}),t=0;return this.domManip(arguments,function(n){var r=e[t++],i=e[t++];i&&(x(this).remove(),i.insertBefore(n,r))},!0),t?this:this.remove()},detach:function(e){return this.remove(e,!0)},domManip:function(e,t,n){e=p.apply([],e);var r,i,o,s,a,u,l=0,c=this.length,f=this,h=c-1,d=e[0],g=x.isFunction(d);if(g||!(1>=c||"string"!=typeof d||x.support.checkClone)&&ot.test(d))return this.each(function(r){var i=f.eq(r);g&&(e[0]=d.call(this,r,i.html())),i.domManip(e,t,n)});if(c&&(r=x.buildFragment(e,this[0].ownerDocument,!1,!n&&this),i=r.firstChild,1===r.childNodes.length&&(r=i),i)){for(o=x.map(gt(r,"script"),ft),s=o.length;c>l;l++)a=r,l!==h&&(a=x.clone(a,!0,!0),s&&x.merge(o,gt(a,"script"))),t.call(this[l],a,l);if(s)for(u=o[o.length-1].ownerDocument,x.map(o,pt),l=0;s>l;l++)a=o[l],st.test(a.type||"")&&!q.access(a,"globalEval")&&x.contains(u,a)&&(a.src?x._evalUrl(a.src):x.globalEval(a.textContent.replace(ut,"")))}return this}}),x.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(e,t){x.fn[e]=function(e){var n,r=[],i=x(e),o=i.length-1,s=0;for(;o>=s;s++)n=s===o?this:this.clone(!0),x(i[s])[t](n),h.apply(r,n.get());return this.pushStack(r)}}),x.extend({clone:function(e,t,n){var r,i,o,s,a=e.cloneNode(!0),u=x.contains(e.ownerDocument,e);if(!(x.support.noCloneChecked||1!==e.nodeType&&11!==e.nodeType||x.isXMLDoc(e)))for(s=gt(a),o=gt(e),r=0,i=o.length;i>r;r++)mt(o[r],s[r]);if(t)if(n)for(o=o||gt(e),s=s||gt(a),r=0,i=o.length;i>r;r++)dt(o[r],s[r]);else dt(e,a);return s=gt(a,"script"),s.length>0&&ht(s,!u&&gt(e,"script")),a},buildFragment:function(e,t,n,r){var i,o,s,a,u,l,c=0,f=e.length,p=t.createDocumentFragment(),h=[];for(;f>c;c++)if(i=e[c],i||0===i)if("object"===x.type(i))x.merge(h,i.nodeType?[i]:i);else if(nt.test(i)){o=o||p.appendChild(t.createElement("div")),s=(tt.exec(i)||["",""])[1].toLowerCase(),a=lt[s]||lt._default,o.innerHTML=a[1]+i.replace(et,"<$1></$2>")+a[2],l=a[0];while(l--)o=o.firstChild;x.merge(h,o.childNodes),o=p.firstChild,o.textContent=""}else h.push(t.createTextNode(i));p.textContent="",c=0;while(i=h[c++])if((!r||-1===x.inArray(i,r))&&(u=x.contains(i.ownerDocument,i),o=gt(p.appendChild(i),"script"),u&&ht(o),n)){l=0;while(i=o[l++])st.test(i.type||"")&&n.push(i)}return p},cleanData:function(e){var t,n,r,i=e.length,o=0,s=x.event.special;for(;i>o;o++){if(n=e[o],x.acceptData(n)&&(t=q.access(n)))for(r in t.events)s[r]?x.event.remove(n,r):x.removeEvent(n,r,t.handle);L.discard(n),q.discard(n)}},_evalUrl:function(e){return x.ajax({url:e,type:"GET",dataType:"text",async:!1,global:!1,success:x.globalEval})}});function ct(e,t){return x.nodeName(e,"table")&&x.nodeName(1===t.nodeType?t:t.firstChild,"tr")?e.getElementsByTagName("tbody")[0]||e.appendChild(e.ownerDocument.createElement("tbody")):e}function ft(e){return e.type=(null!==e.getAttribute("type"))+"/"+e.type,e}function pt(e){var t=at.exec(e.type);return t?e.type=t[1]:e.removeAttribute("type"),e}function ht(e,t){var n=e.length,r=0;for(;n>r;r++)q.set(e[r],"globalEval",!t||q.get(t[r],"globalEval"))}function dt(e,t){var n,r,i,o,s,a,u,l;if(1===t.nodeType){if(q.hasData(e)&&(o=q.access(e),s=x.extend({},o),l=o.events,q.set(t,s),l)){delete s.handle,s.events={};for(i in l)for(n=0,r=l[i].length;r>n;n++)x.event.add(t,i,l[i][n])}L.hasData(e)&&(a=L.access(e),u=x.extend({},a),L.set(t,u))}}function gt(e,t){var n=e.getElementsByTagName?e.getElementsByTagName(t||"*"):e.querySelectorAll?e.querySelectorAll(t||"*"):[];return t===undefined||t&&x.nodeName(e,t)?x.merge([e],n):n}function mt(e,t){var n=t.nodeName.toLowerCase();"input"===n&&it.test(e.type)?t.checked=e.checked:("input"===n||"textarea"===n)&&(t.defaultValue=e.defaultValue)}x.fn.extend({wrapAll:function(e){var t;return x.isFunction(e)?this.each(function(t){x(this).wrapAll(e.call(this,t))}):(this[0]&&(t=x(e,this[0].ownerDocument).eq(0).clone(!0),this[0].parentNode&&t.insertBefore(this[0]),t.map(function(){var e=this;while(e.firstElementChild)e=e.firstElementChild;return e}).append(this)),this)},wrapInner:function(e){return x.isFunction(e)?this.each(function(t){x(this).wrapInner(e.call(this,t))}):this.each(function(){var t=x(this),n=t.contents();n.length?n.wrapAll(e):t.append(e)})},wrap:function(e){var t=x.isFunction(e);return this.each(function(n){x(this).wrapAll(t?e.call(this,n):e)})},unwrap:function(){return this.parent().each(function(){x.nodeName(this,"body")||x(this).replaceWith(this.childNodes)}).end()}});var yt,vt,xt=/^(none|table(?!-c[ea]).+)/,bt=/^margin/,wt=RegExp("^("+b+")(.*)$","i"),Tt=RegExp("^("+b+")(?!px)[a-z%]+$","i"),Ct=RegExp("^([+-])=("+b+")","i"),kt={BODY:"block"},Nt={position:"absolute",visibility:"hidden",display:"block"},Et={letterSpacing:0,fontWeight:400},St=["Top","Right","Bottom","Left"],jt=["Webkit","O","Moz","ms"];function Dt(e,t){if(t in e)return t;var n=t.charAt(0).toUpperCase()+t.slice(1),r=t,i=jt.length;while(i--)if(t=jt[i]+n,t in e)return t;return r}function At(e,t){return e=t||e,"none"===x.css(e,"display")||!x.contains(e.ownerDocument,e)}function Lt(t){return e.getComputedStyle(t,null)}function qt(e,t){var n,r,i,o=[],s=0,a=e.length;for(;a>s;s++)r=e[s],r.style&&(o[s]=q.get(r,"olddisplay"),n=r.style.display,t?(o[s]||"none"!==n||(r.style.display=""),""===r.style.display&&At(r)&&(o[s]=q.access(r,"olddisplay",Pt(r.nodeName)))):o[s]||(i=At(r),(n&&"none"!==n||!i)&&q.set(r,"olddisplay",i?n:x.css(r,"display"))));for(s=0;a>s;s++)r=e[s],r.style&&(t&&"none"!==r.style.display&&""!==r.style.display||(r.style.display=t?o[s]||"":"none"));return e}x.fn.extend({css:function(e,t){return x.access(this,function(e,t,n){var r,i,o={},s=0;if(x.isArray(t)){for(r=Lt(e),i=t.length;i>s;s++)o[t[s]]=x.css(e,t[s],!1,r);return o}return n!==undefined?x.style(e,t,n):x.css(e,t)},e,t,arguments.length>1)},show:function(){return qt(this,!0)},hide:function(){return qt(this)},toggle:function(e){var t="boolean"==typeof e;return this.each(function(){(t?e:At(this))?x(this).show():x(this).hide()})}}),x.extend({cssHooks:{opacity:{get:function(e,t){if(t){var n=yt(e,"opacity");return""===n?"1":n}}}},cssNumber:{columnCount:!0,fillOpacity:!0,fontWeight:!0,lineHeight:!0,opacity:!0,orphans:!0,widows:!0,zIndex:!0,zoom:!0},cssProps:{"float":"cssFloat"},style:function(e,t,n,r){if(e&&3!==e.nodeType&&8!==e.nodeType&&e.style){var i,o,s,a=x.camelCase(t),u=e.style;return t=x.cssProps[a]||(x.cssProps[a]=Dt(u,a)),s=x.cssHooks[t]||x.cssHooks[a],n===undefined?s&&"get"in s&&(i=s.get(e,!1,r))!==undefined?i:u[t]:(o=typeof n,"string"===o&&(i=Ct.exec(n))&&(n=(i[1]+1)*i[2]+parseFloat(x.css(e,t)),o="number"),null==n||"number"===o&&isNaN(n)||("number"!==o||x.cssNumber[a]||(n+="px"),x.support.clearCloneStyle||""!==n||0!==t.indexOf("background")||(u[t]="inherit"),s&&"set"in s&&(n=s.set(e,n,r))===undefined||(u[t]=n)),undefined)}},css:function(e,t,n,r){var i,o,s,a=x.camelCase(t);return t=x.cssProps[a]||(x.cssProps[a]=Dt(e.style,a)),s=x.cssHooks[t]||x.cssHooks[a],s&&"get"in s&&(i=s.get(e,!0,n)),i===undefined&&(i=yt(e,t,r)),"normal"===i&&t in Et&&(i=Et[t]),""===n||n?(o=parseFloat(i),n===!0||x.isNumeric(o)?o||0:i):i}}),yt=function(e,t,n){var r,i,o,s=n||Lt(e),a=s?s.getPropertyValue(t)||s[t]:undefined,u=e.style;return s&&(""!==a||x.contains(e.ownerDocument,e)||(a=x.style(e,t)),Tt.test(a)&&bt.test(t)&&(r=u.width,i=u.minWidth,o=u.maxWidth,u.minWidth=u.maxWidth=u.width=a,a=s.width,u.width=r,u.minWidth=i,u.maxWidth=o)),a};function Ht(e,t,n){var r=wt.exec(t);return r?Math.max(0,r[1]-(n||0))+(r[2]||"px"):t}function Ot(e,t,n,r,i){var o=n===(r?"border":"content")?4:"width"===t?1:0,s=0;for(;4>o;o+=2)"margin"===n&&(s+=x.css(e,n+St[o],!0,i)),r?("content"===n&&(s-=x.css(e,"padding"+St[o],!0,i)),"margin"!==n&&(s-=x.css(e,"border"+St[o]+"Width",!0,i))):(s+=x.css(e,"padding"+St[o],!0,i),"padding"!==n&&(s+=x.css(e,"border"+St[o]+"Width",!0,i)));return s}function Ft(e,t,n){var r=!0,i="width"===t?e.offsetWidth:e.offsetHeight,o=Lt(e),s=x.support.boxSizing&&"border-box"===x.css(e,"boxSizing",!1,o);if(0>=i||null==i){if(i=yt(e,t,o),(0>i||null==i)&&(i=e.style[t]),Tt.test(i))return i;r=s&&(x.support.boxSizingReliable||i===e.style[t]),i=parseFloat(i)||0}return i+Ot(e,t,n||(s?"border":"content"),r,o)+"px"}function Pt(e){var t=o,n=kt[e];return n||(n=Rt(e,t),"none"!==n&&n||(vt=(vt||x("<iframe frameborder='0' width='0' height='0'/>").css("cssText","display:block !important")).appendTo(t.documentElement),t=(vt[0].contentWindow||vt[0].contentDocument).document,t.write("<!doctype html><html><body>"),t.close(),n=Rt(e,t),vt.detach()),kt[e]=n),n}function Rt(e,t){var n=x(t.createElement(e)).appendTo(t.body),r=x.css(n[0],"display");return n.remove(),r}x.each(["height","width"],function(e,t){x.cssHooks[t]={get:function(e,n,r){return n?0===e.offsetWidth&&xt.test(x.css(e,"display"))?x.swap(e,Nt,function(){return Ft(e,t,r)}):Ft(e,t,r):undefined},set:function(e,n,r){var i=r&&Lt(e);return Ht(e,n,r?Ot(e,t,r,x.support.boxSizing&&"border-box"===x.css(e,"boxSizing",!1,i),i):0)}}}),x(function(){x.support.reliableMarginRight||(x.cssHooks.marginRight={get:function(e,t){return t?x.swap(e,{display:"inline-block"},yt,[e,"marginRight"]):undefined}}),!x.support.pixelPosition&&x.fn.position&&x.each(["top","left"],function(e,t){x.cssHooks[t]={get:function(e,n){return n?(n=yt(e,t),Tt.test(n)?x(e).position()[t]+"px":n):undefined}}})}),x.expr&&x.expr.filters&&(x.expr.filters.hidden=function(e){return 0>=e.offsetWidth&&0>=e.offsetHeight},x.expr.filters.visible=function(e){return!x.expr.filters.hidden(e)}),x.each({margin:"",padding:"",border:"Width"},function(e,t){x.cssHooks[e+t]={expand:function(n){var r=0,i={},o="string"==typeof n?n.split(" "):[n];for(;4>r;r++)i[e+St[r]+t]=o[r]||o[r-2]||o[0];return i}},bt.test(e)||(x.cssHooks[e+t].set=Ht)});var Mt=/%20/g,Wt=/\[\]$/,$t=/\r?\n/g,Bt=/^(?:submit|button|image|reset|file)$/i,It=/^(?:input|select|textarea|keygen)/i;x.fn.extend({serialize:function(){return x.param(this.serializeArray())},serializeArray:function(){return this.map(function(){var e=x.prop(this,"elements");return e?x.makeArray(e):this}).filter(function(){var e=this.type;return this.name&&!x(this).is(":disabled")&&It.test(this.nodeName)&&!Bt.test(e)&&(this.checked||!it.test(e))}).map(function(e,t){var n=x(this).val();return null==n?null:x.isArray(n)?x.map(n,function(e){return{name:t.name,value:e.replace($t,"\r\n")}}):{name:t.name,value:n.replace($t,"\r\n")}}).get()}}),x.param=function(e,t){var n,r=[],i=function(e,t){t=x.isFunction(t)?t():null==t?"":t,r[r.length]=encodeURIComponent(e)+"="+encodeURIComponent(t)};if(t===undefined&&(t=x.ajaxSettings&&x.ajaxSettings.traditional),x.isArray(e)||e.jquery&&!x.isPlainObject(e))x.each(e,function(){i(this.name,this.value)});else for(n in e)zt(n,e[n],t,i);return r.join("&").replace(Mt,"+")};function zt(e,t,n,r){var i;if(x.isArray(t))x.each(t,function(t,i){n||Wt.test(e)?r(e,i):zt(e+"["+("object"==typeof i?t:"")+"]",i,n,r)});else if(n||"object"!==x.type(t))r(e,t);else for(i in t)zt(e+"["+i+"]",t[i],n,r)}x.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "),function(e,t){x.fn[t]=function(e,n){return arguments.length>0?this.on(t,null,e,n):this.trigger(t)}}),x.fn.extend({hover:function(e,t){return this.mouseenter(e).mouseleave(t||e)},bind:function(e,t,n){return this.on(e,null,t,n)},unbind:function(e,t){return this.off(e,null,t)},delegate:function(e,t,n,r){return this.on(t,e,n,r)},undelegate:function(e,t,n){return 1===arguments.length?this.off(e,"**"):this.off(t,e||"**",n)}});var _t,Xt,Ut=x.now(),Yt=/\?/,Vt=/#.*$/,Gt=/([?&])_=[^&]*/,Jt=/^(.*?):[ \t]*([^\r\n]*)$/gm,Qt=/^(?:about|app|app-storage|.+-extension|file|res|widget):$/,Kt=/^(?:GET|HEAD)$/,Zt=/^\/\//,en=/^([\w.+-]+:)(?:\/\/([^\/?#:]*)(?::(\d+)|)|)/,tn=x.fn.load,nn={},rn={},on="*/".concat("*");try{Xt=i.href}catch(sn){Xt=o.createElement("a"),Xt.href="",Xt=Xt.href}_t=en.exec(Xt.toLowerCase())||[];function an(e){return function(t,n){"string"!=typeof t&&(n=t,t="*");var r,i=0,o=t.toLowerCase().match(w)||[];
if(x.isFunction(n))while(r=o[i++])"+"===r[0]?(r=r.slice(1)||"*",(e[r]=e[r]||[]).unshift(n)):(e[r]=e[r]||[]).push(n)}}function un(e,t,n,r){var i={},o=e===rn;function s(a){var u;return i[a]=!0,x.each(e[a]||[],function(e,a){var l=a(t,n,r);return"string"!=typeof l||o||i[l]?o?!(u=l):undefined:(t.dataTypes.unshift(l),s(l),!1)}),u}return s(t.dataTypes[0])||!i["*"]&&s("*")}function ln(e,t){var n,r,i=x.ajaxSettings.flatOptions||{};for(n in t)t[n]!==undefined&&((i[n]?e:r||(r={}))[n]=t[n]);return r&&x.extend(!0,e,r),e}x.fn.load=function(e,t,n){if("string"!=typeof e&&tn)return tn.apply(this,arguments);var r,i,o,s=this,a=e.indexOf(" ");return a>=0&&(r=e.slice(a),e=e.slice(0,a)),x.isFunction(t)?(n=t,t=undefined):t&&"object"==typeof t&&(i="POST"),s.length>0&&x.ajax({url:e,type:i,dataType:"html",data:t}).done(function(e){o=arguments,s.html(r?x("<div>").append(x.parseHTML(e)).find(r):e)}).complete(n&&function(e,t){s.each(n,o||[e.responseText,t,e])}),this},x.each(["ajaxStart","ajaxStop","ajaxComplete","ajaxError","ajaxSuccess","ajaxSend"],function(e,t){x.fn[t]=function(e){return this.on(t,e)}}),x.extend({active:0,lastModified:{},etag:{},ajaxSettings:{url:Xt,type:"GET",isLocal:Qt.test(_t[1]),global:!0,processData:!0,async:!0,contentType:"application/x-www-form-urlencoded; charset=UTF-8",accepts:{"*":on,text:"text/plain",html:"text/html",xml:"application/xml, text/xml",json:"application/json, text/javascript"},contents:{xml:/xml/,html:/html/,json:/json/},responseFields:{xml:"responseXML",text:"responseText",json:"responseJSON"},converters:{"* text":String,"text html":!0,"text json":x.parseJSON,"text xml":x.parseXML},flatOptions:{url:!0,context:!0}},ajaxSetup:function(e,t){return t?ln(ln(e,x.ajaxSettings),t):ln(x.ajaxSettings,e)},ajaxPrefilter:an(nn),ajaxTransport:an(rn),ajax:function(e,t){"object"==typeof e&&(t=e,e=undefined),t=t||{};var n,r,i,o,s,a,u,l,c=x.ajaxSetup({},t),f=c.context||c,p=c.context&&(f.nodeType||f.jquery)?x(f):x.event,h=x.Deferred(),d=x.Callbacks("once memory"),g=c.statusCode||{},m={},y={},v=0,b="canceled",T={readyState:0,getResponseHeader:function(e){var t;if(2===v){if(!o){o={};while(t=Jt.exec(i))o[t[1].toLowerCase()]=t[2]}t=o[e.toLowerCase()]}return null==t?null:t},getAllResponseHeaders:function(){return 2===v?i:null},setRequestHeader:function(e,t){var n=e.toLowerCase();return v||(e=y[n]=y[n]||e,m[e]=t),this},overrideMimeType:function(e){return v||(c.mimeType=e),this},statusCode:function(e){var t;if(e)if(2>v)for(t in e)g[t]=[g[t],e[t]];else T.always(e[T.status]);return this},abort:function(e){var t=e||b;return n&&n.abort(t),k(0,t),this}};if(h.promise(T).complete=d.add,T.success=T.done,T.error=T.fail,c.url=((e||c.url||Xt)+"").replace(Vt,"").replace(Zt,_t[1]+"//"),c.type=t.method||t.type||c.method||c.type,c.dataTypes=x.trim(c.dataType||"*").toLowerCase().match(w)||[""],null==c.crossDomain&&(a=en.exec(c.url.toLowerCase()),c.crossDomain=!(!a||a[1]===_t[1]&&a[2]===_t[2]&&(a[3]||("http:"===a[1]?"80":"443"))===(_t[3]||("http:"===_t[1]?"80":"443")))),c.data&&c.processData&&"string"!=typeof c.data&&(c.data=x.param(c.data,c.traditional)),un(nn,c,t,T),2===v)return T;u=c.global,u&&0===x.active++&&x.event.trigger("ajaxStart"),c.type=c.type.toUpperCase(),c.hasContent=!Kt.test(c.type),r=c.url,c.hasContent||(c.data&&(r=c.url+=(Yt.test(r)?"&":"?")+c.data,delete c.data),c.cache===!1&&(c.url=Gt.test(r)?r.replace(Gt,"$1_="+Ut++):r+(Yt.test(r)?"&":"?")+"_="+Ut++)),c.ifModified&&(x.lastModified[r]&&T.setRequestHeader("If-Modified-Since",x.lastModified[r]),x.etag[r]&&T.setRequestHeader("If-None-Match",x.etag[r])),(c.data&&c.hasContent&&c.contentType!==!1||t.contentType)&&T.setRequestHeader("Content-Type",c.contentType),T.setRequestHeader("Accept",c.dataTypes[0]&&c.accepts[c.dataTypes[0]]?c.accepts[c.dataTypes[0]]+("*"!==c.dataTypes[0]?", "+on+"; q=0.01":""):c.accepts["*"]);for(l in c.headers)T.setRequestHeader(l,c.headers[l]);if(c.beforeSend&&(c.beforeSend.call(f,T,c)===!1||2===v))return T.abort();b="abort";for(l in{success:1,error:1,complete:1})T[l](c[l]);if(n=un(rn,c,t,T)){T.readyState=1,u&&p.trigger("ajaxSend",[T,c]),c.async&&c.timeout>0&&(s=setTimeout(function(){T.abort("timeout")},c.timeout));try{v=1,n.send(m,k)}catch(C){if(!(2>v))throw C;k(-1,C)}}else k(-1,"No Transport");function k(e,t,o,a){var l,m,y,b,w,C=t;2!==v&&(v=2,s&&clearTimeout(s),n=undefined,i=a||"",T.readyState=e>0?4:0,l=e>=200&&300>e||304===e,o&&(b=cn(c,T,o)),b=fn(c,b,T,l),l?(c.ifModified&&(w=T.getResponseHeader("Last-Modified"),w&&(x.lastModified[r]=w),w=T.getResponseHeader("etag"),w&&(x.etag[r]=w)),204===e?C="nocontent":304===e?C="notmodified":(C=b.state,m=b.data,y=b.error,l=!y)):(y=C,(e||!C)&&(C="error",0>e&&(e=0))),T.status=e,T.statusText=(t||C)+"",l?h.resolveWith(f,[m,C,T]):h.rejectWith(f,[T,C,y]),T.statusCode(g),g=undefined,u&&p.trigger(l?"ajaxSuccess":"ajaxError",[T,c,l?m:y]),d.fireWith(f,[T,C]),u&&(p.trigger("ajaxComplete",[T,c]),--x.active||x.event.trigger("ajaxStop")))}return T},getJSON:function(e,t,n){return x.get(e,t,n,"json")},getScript:function(e,t){return x.get(e,undefined,t,"script")}}),x.each(["get","post"],function(e,t){x[t]=function(e,n,r,i){return x.isFunction(n)&&(i=i||r,r=n,n=undefined),x.ajax({url:e,type:t,dataType:i,data:n,success:r})}});function cn(e,t,n){var r,i,o,s,a=e.contents,u=e.dataTypes;while("*"===u[0])u.shift(),r===undefined&&(r=e.mimeType||t.getResponseHeader("Content-Type"));if(r)for(i in a)if(a[i]&&a[i].test(r)){u.unshift(i);break}if(u[0]in n)o=u[0];else{for(i in n){if(!u[0]||e.converters[i+" "+u[0]]){o=i;break}s||(s=i)}o=o||s}return o?(o!==u[0]&&u.unshift(o),n[o]):undefined}function fn(e,t,n,r){var i,o,s,a,u,l={},c=e.dataTypes.slice();if(c[1])for(s in e.converters)l[s.toLowerCase()]=e.converters[s];o=c.shift();while(o)if(e.responseFields[o]&&(n[e.responseFields[o]]=t),!u&&r&&e.dataFilter&&(t=e.dataFilter(t,e.dataType)),u=o,o=c.shift())if("*"===o)o=u;else if("*"!==u&&u!==o){if(s=l[u+" "+o]||l["* "+o],!s)for(i in l)if(a=i.split(" "),a[1]===o&&(s=l[u+" "+a[0]]||l["* "+a[0]])){s===!0?s=l[i]:l[i]!==!0&&(o=a[0],c.unshift(a[1]));break}if(s!==!0)if(s&&e["throws"])t=s(t);else try{t=s(t)}catch(f){return{state:"parsererror",error:s?f:"No conversion from "+u+" to "+o}}}return{state:"success",data:t}}x.ajaxSetup({accepts:{script:"text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},contents:{script:/(?:java|ecma)script/},converters:{"text script":function(e){return x.globalEval(e),e}}}),x.ajaxPrefilter("script",function(e){e.cache===undefined&&(e.cache=!1),e.crossDomain&&(e.type="GET")}),x.ajaxTransport("script",function(e){if(e.crossDomain){var t,n;return{send:function(r,i){t=x("<script>").prop({async:!0,charset:e.scriptCharset,src:e.url}).on("load error",n=function(e){t.remove(),n=null,e&&i("error"===e.type?404:200,e.type)}),o.head.appendChild(t[0])},abort:function(){n&&n()}}}});var pn=[],hn=/(=)\?(?=&|$)|\?\?/;x.ajaxSetup({jsonp:"callback",jsonpCallback:function(){var e=pn.pop()||x.expando+"_"+Ut++;return this[e]=!0,e}}),x.ajaxPrefilter("json jsonp",function(t,n,r){var i,o,s,a=t.jsonp!==!1&&(hn.test(t.url)?"url":"string"==typeof t.data&&!(t.contentType||"").indexOf("application/x-www-form-urlencoded")&&hn.test(t.data)&&"data");return a||"jsonp"===t.dataTypes[0]?(i=t.jsonpCallback=x.isFunction(t.jsonpCallback)?t.jsonpCallback():t.jsonpCallback,a?t[a]=t[a].replace(hn,"$1"+i):t.jsonp!==!1&&(t.url+=(Yt.test(t.url)?"&":"?")+t.jsonp+"="+i),t.converters["script json"]=function(){return s||x.error(i+" was not called"),s[0]},t.dataTypes[0]="json",o=e[i],e[i]=function(){s=arguments},r.always(function(){e[i]=o,t[i]&&(t.jsonpCallback=n.jsonpCallback,pn.push(i)),s&&x.isFunction(o)&&o(s[0]),s=o=undefined}),"script"):undefined}),x.ajaxSettings.xhr=function(){try{return new XMLHttpRequest}catch(e){}};var dn=x.ajaxSettings.xhr(),gn={0:200,1223:204},mn=0,yn={};e.ActiveXObject&&x(e).on("unload",function(){for(var e in yn)yn[e]();yn=undefined}),x.support.cors=!!dn&&"withCredentials"in dn,x.support.ajax=dn=!!dn,x.ajaxTransport(function(e){var t;return x.support.cors||dn&&!e.crossDomain?{send:function(n,r){var i,o,s=e.xhr();if(s.open(e.type,e.url,e.async,e.username,e.password),e.xhrFields)for(i in e.xhrFields)s[i]=e.xhrFields[i];e.mimeType&&s.overrideMimeType&&s.overrideMimeType(e.mimeType),e.crossDomain||n["X-Requested-With"]||(n["X-Requested-With"]="XMLHttpRequest");for(i in n)s.setRequestHeader(i,n[i]);t=function(e){return function(){t&&(delete yn[o],t=s.onload=s.onerror=null,"abort"===e?s.abort():"error"===e?r(s.status||404,s.statusText):r(gn[s.status]||s.status,s.statusText,"string"==typeof s.responseText?{text:s.responseText}:undefined,s.getAllResponseHeaders()))}},s.onload=t(),s.onerror=t("error"),t=yn[o=mn++]=t("abort"),s.send(e.hasContent&&e.data||null)},abort:function(){t&&t()}}:undefined});var vn,xn,bn=/^(?:toggle|show|hide)$/,wn=RegExp("^(?:([+-])=|)("+b+")([a-z%]*)$","i"),Tn=/queueHooks$/,Cn=[Dn],kn={"*":[function(e,t){var n,r,i=this.createTween(e,t),o=wn.exec(t),s=i.cur(),a=+s||0,u=1,l=20;if(o){if(n=+o[2],r=o[3]||(x.cssNumber[e]?"":"px"),"px"!==r&&a){a=x.css(i.elem,e,!0)||n||1;do u=u||".5",a/=u,x.style(i.elem,e,a+r);while(u!==(u=i.cur()/s)&&1!==u&&--l)}i.unit=r,i.start=a,i.end=o[1]?a+(o[1]+1)*n:n}return i}]};function Nn(){return setTimeout(function(){vn=undefined}),vn=x.now()}function En(e,t){x.each(t,function(t,n){var r=(kn[t]||[]).concat(kn["*"]),i=0,o=r.length;for(;o>i;i++)if(r[i].call(e,t,n))return})}function Sn(e,t,n){var r,i,o=0,s=Cn.length,a=x.Deferred().always(function(){delete u.elem}),u=function(){if(i)return!1;var t=vn||Nn(),n=Math.max(0,l.startTime+l.duration-t),r=n/l.duration||0,o=1-r,s=0,u=l.tweens.length;for(;u>s;s++)l.tweens[s].run(o);return a.notifyWith(e,[l,o,n]),1>o&&u?n:(a.resolveWith(e,[l]),!1)},l=a.promise({elem:e,props:x.extend({},t),opts:x.extend(!0,{specialEasing:{}},n),originalProperties:t,originalOptions:n,startTime:vn||Nn(),duration:n.duration,tweens:[],createTween:function(t,n){var r=x.Tween(e,l.opts,t,n,l.opts.specialEasing[t]||l.opts.easing);return l.tweens.push(r),r},stop:function(t){var n=0,r=t?l.tweens.length:0;if(i)return this;for(i=!0;r>n;n++)l.tweens[n].run(1);return t?a.resolveWith(e,[l,t]):a.rejectWith(e,[l,t]),this}}),c=l.props;for(jn(c,l.opts.specialEasing);s>o;o++)if(r=Cn[o].call(l,e,c,l.opts))return r;return En(l,c),x.isFunction(l.opts.start)&&l.opts.start.call(e,l),x.fx.timer(x.extend(u,{elem:e,anim:l,queue:l.opts.queue})),l.progress(l.opts.progress).done(l.opts.done,l.opts.complete).fail(l.opts.fail).always(l.opts.always)}function jn(e,t){var n,r,i,o,s;for(n in e)if(r=x.camelCase(n),i=t[r],o=e[n],x.isArray(o)&&(i=o[1],o=e[n]=o[0]),n!==r&&(e[r]=o,delete e[n]),s=x.cssHooks[r],s&&"expand"in s){o=s.expand(o),delete e[r];for(n in o)n in e||(e[n]=o[n],t[n]=i)}else t[r]=i}x.Animation=x.extend(Sn,{tweener:function(e,t){x.isFunction(e)?(t=e,e=["*"]):e=e.split(" ");var n,r=0,i=e.length;for(;i>r;r++)n=e[r],kn[n]=kn[n]||[],kn[n].unshift(t)},prefilter:function(e,t){t?Cn.unshift(e):Cn.push(e)}});function Dn(e,t,n){var r,i,o,s,a,u,l,c,f,p=this,h=e.style,d={},g=[],m=e.nodeType&&At(e);n.queue||(c=x._queueHooks(e,"fx"),null==c.unqueued&&(c.unqueued=0,f=c.empty.fire,c.empty.fire=function(){c.unqueued||f()}),c.unqueued++,p.always(function(){p.always(function(){c.unqueued--,x.queue(e,"fx").length||c.empty.fire()})})),1===e.nodeType&&("height"in t||"width"in t)&&(n.overflow=[h.overflow,h.overflowX,h.overflowY],"inline"===x.css(e,"display")&&"none"===x.css(e,"float")&&(h.display="inline-block")),n.overflow&&(h.overflow="hidden",p.always(function(){h.overflow=n.overflow[0],h.overflowX=n.overflow[1],h.overflowY=n.overflow[2]})),a=q.get(e,"fxshow");for(r in t)if(o=t[r],bn.exec(o)){if(delete t[r],u=u||"toggle"===o,o===(m?"hide":"show")){if("show"!==o||a===undefined||a[r]===undefined)continue;m=!0}g.push(r)}if(s=g.length){a=q.get(e,"fxshow")||q.access(e,"fxshow",{}),"hidden"in a&&(m=a.hidden),u&&(a.hidden=!m),m?x(e).show():p.done(function(){x(e).hide()}),p.done(function(){var t;q.remove(e,"fxshow");for(t in d)x.style(e,t,d[t])});for(r=0;s>r;r++)i=g[r],l=p.createTween(i,m?a[i]:0),d[i]=a[i]||x.style(e,i),i in a||(a[i]=l.start,m&&(l.end=l.start,l.start="width"===i||"height"===i?1:0))}}function An(e,t,n,r,i){return new An.prototype.init(e,t,n,r,i)}x.Tween=An,An.prototype={constructor:An,init:function(e,t,n,r,i,o){this.elem=e,this.prop=n,this.easing=i||"swing",this.options=t,this.start=this.now=this.cur(),this.end=r,this.unit=o||(x.cssNumber[n]?"":"px")},cur:function(){var e=An.propHooks[this.prop];return e&&e.get?e.get(this):An.propHooks._default.get(this)},run:function(e){var t,n=An.propHooks[this.prop];return this.pos=t=this.options.duration?x.easing[this.easing](e,this.options.duration*e,0,1,this.options.duration):e,this.now=(this.end-this.start)*t+this.start,this.options.step&&this.options.step.call(this.elem,this.now,this),n&&n.set?n.set(this):An.propHooks._default.set(this),this}},An.prototype.init.prototype=An.prototype,An.propHooks={_default:{get:function(e){var t;return null==e.elem[e.prop]||e.elem.style&&null!=e.elem.style[e.prop]?(t=x.css(e.elem,e.prop,""),t&&"auto"!==t?t:0):e.elem[e.prop]},set:function(e){x.fx.step[e.prop]?x.fx.step[e.prop](e):e.elem.style&&(null!=e.elem.style[x.cssProps[e.prop]]||x.cssHooks[e.prop])?x.style(e.elem,e.prop,e.now+e.unit):e.elem[e.prop]=e.now}}},An.propHooks.scrollTop=An.propHooks.scrollLeft={set:function(e){e.elem.nodeType&&e.elem.parentNode&&(e.elem[e.prop]=e.now)}},x.each(["toggle","show","hide"],function(e,t){var n=x.fn[t];x.fn[t]=function(e,r,i){return null==e||"boolean"==typeof e?n.apply(this,arguments):this.animate(Ln(t,!0),e,r,i)}}),x.fn.extend({fadeTo:function(e,t,n,r){return this.filter(At).css("opacity",0).show().end().animate({opacity:t},e,n,r)},animate:function(e,t,n,r){var i=x.isEmptyObject(e),o=x.speed(t,n,r),s=function(){var t=Sn(this,x.extend({},e),o);s.finish=function(){t.stop(!0)},(i||q.get(this,"finish"))&&t.stop(!0)};return s.finish=s,i||o.queue===!1?this.each(s):this.queue(o.queue,s)},stop:function(e,t,n){var r=function(e){var t=e.stop;delete e.stop,t(n)};return"string"!=typeof e&&(n=t,t=e,e=undefined),t&&e!==!1&&this.queue(e||"fx",[]),this.each(function(){var t=!0,i=null!=e&&e+"queueHooks",o=x.timers,s=q.get(this);if(i)s[i]&&s[i].stop&&r(s[i]);else for(i in s)s[i]&&s[i].stop&&Tn.test(i)&&r(s[i]);for(i=o.length;i--;)o[i].elem!==this||null!=e&&o[i].queue!==e||(o[i].anim.stop(n),t=!1,o.splice(i,1));(t||!n)&&x.dequeue(this,e)})},finish:function(e){return e!==!1&&(e=e||"fx"),this.each(function(){var t,n=q.get(this),r=n[e+"queue"],i=n[e+"queueHooks"],o=x.timers,s=r?r.length:0;for(n.finish=!0,x.queue(this,e,[]),i&&i.cur&&i.cur.finish&&i.cur.finish.call(this),t=o.length;t--;)o[t].elem===this&&o[t].queue===e&&(o[t].anim.stop(!0),o.splice(t,1));for(t=0;s>t;t++)r[t]&&r[t].finish&&r[t].finish.call(this);delete n.finish})}});function Ln(e,t){var n,r={height:e},i=0;for(t=t?1:0;4>i;i+=2-t)n=St[i],r["margin"+n]=r["padding"+n]=e;return t&&(r.opacity=r.width=e),r}x.each({slideDown:Ln("show"),slideUp:Ln("hide"),slideToggle:Ln("toggle"),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(e,t){x.fn[e]=function(e,n,r){return this.animate(t,e,n,r)}}),x.speed=function(e,t,n){var r=e&&"object"==typeof e?x.extend({},e):{complete:n||!n&&t||x.isFunction(e)&&e,duration:e,easing:n&&t||t&&!x.isFunction(t)&&t};return r.duration=x.fx.off?0:"number"==typeof r.duration?r.duration:r.duration in x.fx.speeds?x.fx.speeds[r.duration]:x.fx.speeds._default,(null==r.queue||r.queue===!0)&&(r.queue="fx"),r.old=r.complete,r.complete=function(){x.isFunction(r.old)&&r.old.call(this),r.queue&&x.dequeue(this,r.queue)},r},x.easing={linear:function(e){return e},swing:function(e){return.5-Math.cos(e*Math.PI)/2}},x.timers=[],x.fx=An.prototype.init,x.fx.tick=function(){var e,t=x.timers,n=0;for(vn=x.now();t.length>n;n++)e=t[n],e()||t[n]!==e||t.splice(n--,1);t.length||x.fx.stop(),vn=undefined},x.fx.timer=function(e){e()&&x.timers.push(e)&&x.fx.start()},x.fx.interval=13,x.fx.start=function(){xn||(xn=setInterval(x.fx.tick,x.fx.interval))},x.fx.stop=function(){clearInterval(xn),xn=null},x.fx.speeds={slow:600,fast:200,_default:400},x.fx.step={},x.expr&&x.expr.filters&&(x.expr.filters.animated=function(e){return x.grep(x.timers,function(t){return e===t.elem}).length}),x.fn.offset=function(e){if(arguments.length)return e===undefined?this:this.each(function(t){x.offset.setOffset(this,e,t)});var t,n,i=this[0],o={top:0,left:0},s=i&&i.ownerDocument;if(s)return t=s.documentElement,x.contains(t,i)?(typeof i.getBoundingClientRect!==r&&(o=i.getBoundingClientRect()),n=qn(s),{top:o.top+n.pageYOffset-t.clientTop,left:o.left+n.pageXOffset-t.clientLeft}):o},x.offset={setOffset:function(e,t,n){var r,i,o,s,a,u,l,c=x.css(e,"position"),f=x(e),p={};"static"===c&&(e.style.position="relative"),a=f.offset(),o=x.css(e,"top"),u=x.css(e,"left"),l=("absolute"===c||"fixed"===c)&&(o+u).indexOf("auto")>-1,l?(r=f.position(),s=r.top,i=r.left):(s=parseFloat(o)||0,i=parseFloat(u)||0),x.isFunction(t)&&(t=t.call(e,n,a)),null!=t.top&&(p.top=t.top-a.top+s),null!=t.left&&(p.left=t.left-a.left+i),"using"in t?t.using.call(e,p):f.css(p)}},x.fn.extend({position:function(){if(this[0]){var e,t,n=this[0],r={top:0,left:0};return"fixed"===x.css(n,"position")?t=n.getBoundingClientRect():(e=this.offsetParent(),t=this.offset(),x.nodeName(e[0],"html")||(r=e.offset()),r.top+=x.css(e[0],"borderTopWidth",!0),r.left+=x.css(e[0],"borderLeftWidth",!0)),{top:t.top-r.top-x.css(n,"marginTop",!0),left:t.left-r.left-x.css(n,"marginLeft",!0)}}},offsetParent:function(){return this.map(function(){var e=this.offsetParent||s;while(e&&!x.nodeName(e,"html")&&"static"===x.css(e,"position"))e=e.offsetParent;return e||s})}}),x.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(t,n){var r="pageYOffset"===n;x.fn[t]=function(i){return x.access(this,function(t,i,o){var s=qn(t);return o===undefined?s?s[n]:t[i]:(s?s.scrollTo(r?e.pageXOffset:o,r?o:e.pageYOffset):t[i]=o,undefined)},t,i,arguments.length,null)}});function qn(e){return x.isWindow(e)?e:9===e.nodeType&&e.defaultView}x.each({Height:"height",Width:"width"},function(e,t){x.each({padding:"inner"+e,content:t,"":"outer"+e},function(n,r){x.fn[r]=function(r,i){var o=arguments.length&&(n||"boolean"!=typeof r),s=n||(r===!0||i===!0?"margin":"border");return x.access(this,function(t,n,r){var i;return x.isWindow(t)?t.document.documentElement["client"+e]:9===t.nodeType?(i=t.documentElement,Math.max(t.body["scroll"+e],i["scroll"+e],t.body["offset"+e],i["offset"+e],i["client"+e])):r===undefined?x.css(t,n,s):x.style(t,n,r,s)},t,o?r:undefined,o,null)}})}),x.fn.size=function(){return this.length},x.fn.andSelf=x.fn.addBack,"object"==typeof module&&"object"==typeof module.exports?module.exports=x:"function"==typeof define&&define.amd&&define("jquery",[],function(){return x}),"object"==typeof e&&"object"==typeof e.document&&(e.jQuery=e.$=x)})(window);
</script>
<script type="text/javascript">
$(document).ready(function(){
	/* 初期化 */
	var isDebug = "";

	// ステップを管理する変数の定義
	var step = 0;
	var maxstep = 5;
	var stepApply = 1;

	// 次のステップボタンのクリックイベント
	$("#startstep").click(function(){
		step+=1;
		stepApply = 1;
		next(step);
	});
	$("#nextstep").click(function(){
		step+=1;
		stepApply = 1;
		next(step);
	});
	$("#endstep").click(function(){
		step+=1;
		stepApply = 1;
		next(step);
	});

	// 実行ボタンのクリックイベント
	$("#execute").click(function(){
		execute(step);
	});

	// 設定ボタンのクリックイベント
	$("#apply").click(function(){
		apply(step);
	});

	/* 関数定義 */
	// 次のステップ処理
	function next(argStep){
		// 何はともあれスタートステップボタンはもう不要
		$("#startstep").hide();
		// 表示切り替え
		if(argStep <= maxstep){
			$("#page0").hide();
			$("#page" + (argStep-1)).hide();
			$("#page" + argStep).show();
			$("#navigatestep" + (argStep-1) + " .navigate_header").each(function(){ $(this).removeClass("active"); $(this).addClass("green"); });
			$("#navigatestep" + (argStep-1) + " .navigate_body").each(function(){ $(this).removeClass("active"); $(this).addClass("green"); });
			$("#navigatestep" + argStep + " .navigate_header").each(function(){ $(this).addClass("active"); });
			$("#navigatestep" + argStep + " .navigate_body").each(function(){ $(this).addClass("active"); });
			$("#nextstep").hide();
			if(argStep == 1) {
				$("#execute").show();
			}
			else {
				$("#apply").show();
			}
			if(argStep == maxstep) {
				// インストール完了ページ表示時はナビゲーターはもう不要
				$("#navigatior").hide();
				// 後始末処理
				$.ajax({
					url: "?a=1&step=" + argStep + "&debug=" + isDebug,
					dataType: "json",
					cache: false,
				}).done(function(json) {
					// 何もしない
				});
			}
		}
		// ページの上部へ移動
		inPageLocation("#pagetop");
	}

	// ステップ内の該当の処理を実行
	var step1validate = 1;
	var maxStep1validate = 4;
	function execute(argStep){
		if(argStep == 1){
			if(step1validate <= maxStep1validate){
				// 実行ボタンを無効に
				$("#execute").attr("disabled", "disabled");
				// 項目ローディング表示
				$("#step" + argStep + "validate" + step1validate + " .loading").addClass("active");
				// エラー理由表示初期化
				$("#step" + argStep + "validate" + step1validate).removeClass("red");
				$("#step" + argStep + "validate" + step1validate + " .errormsg").removeClass("red");
				$("#step" + argStep + "validate" + step1validate + " .errormsg").removeClass("yellow");
				$("#step" + argStep + "validate" + step1validate + " .errormsg").text("");
				$("#step" + argStep + "validate" + step1validate + " .errormsg").hide();
				// チェックを始めます文言を飛翔位
				$("#page1_sub_title2").hide();
				// システム要件チェック
				$.ajax({
					url: "?a=1&step=" + argStep + "&validate=" + step1validate + "&debug=" + isDebug,
					dataType: "json",
					cache: false,
				}).done(function(json) {
					// ローディング非表示
					$("#step" + argStep + "validate" + step1validate + " .loading").removeClass("active");
					$("#execute").removeAttr("disabled", "");
					if(true == json.ok || 3 <= step1validate){
						if(true == json.ok){
							// validateが通ったので、greenに
							$("#step" + argStep + "validate" + step1validate).addClass("green");
						}
						else {
							// validateは通らなかったが、必須では無いのでオレンジに
							$("#step" + argStep + "validate" + step1validate).addClass("yellow");
							// エラー理由表示
							$("#step" + argStep + "validate" + step1validate + " .errormsg").text("(!)" + json.error);
							$("#step" + argStep + "validate" + step1validate + " .errormsg").addClass("yellow");
							$("#step" + argStep + "validate" + step1validate + " .errormsg").show();
							// エラーがあったら画面をそこまでスクロールアップ
							inPageLocation("#step" + argStep + "validate" + step1validate);
						}
						// 次のチェック項目へ
						step1validate++;
						// 再帰的実行
						execute(argStep);
					}
					else if(false == json.ok){
						// validateが通らなかったので、redに
						$("#step" + argStep + "validate" + step1validate).addClass("red");
						// エラー理由表示
						$("#step" + argStep + "validate" + step1validate + " .errormsg").text("(!!!)" + json.error);
						$("#step" + argStep + "validate" + step1validate + " .errormsg").addClass("red");
						$("#step" + argStep + "validate" + step1validate + " .errormsg").show();
						// エラーがあったよ表示
						$("#page1_sub_title3").show();
						// エラーがあったら画面をそこまでスクロールアップ
						inPageLocation("#step" + argStep + "validate" + step1validate);
					}
					else {
						// バリデート以外の理由によるエラー
						alert(json.error);
					}
				});
			}
			else {
				// 全てのバリデートが成功で終了
				$("#page1_sub_title3").hide();
				$("#page1_sub_title4").show();
				// 次のステップへ行けるように
				$("#nextstep").show();
				// 実行ボタンは初期化しておく
				$("#execute").hide();
				$("#execute").removeAttr("disabled", "");
			}
		}
	}

	// 各ステップの設定数定義
	var maxStep2apply = 8;
	var maxStep3apply = 12;
	var maxStep4apply = 1;

	// ステップを横断して利用する変数の定義
	var beforeframeworkpath = "";
	var beforegenericpath = "";
	var beforevendorpath = "";
	var frameworkpath = "";
	var genericpath = "";
	var vendorpath = "";
	var packeagepath = "";
	var fwmpath = "";
	var fwmdbuser = "";
	var fwmdbpass = "";
	var fwmdb = "";
	var fwmusername = "";
	var fwmusermail = "";
	var fwmuserpass = "";

	// 通信定義 5分最大で待つ
	var waitLoop = 0;
	var waitMAXLoop = 300000;

	// ステップ内の該当の設定の適用処理を実行
	function apply(argStep){
		if(argStep == 2){
			if(stepApply <= maxStep2apply){
				$("#page" + argStep + " .hlog").text("");
				// ローディング非表示
				$("#step" + argStep + "input" + stepApply + "form_box .loading").removeClass("active");
				// エラーを非表示
				$("#page" + argStep + " .errormsg").hide();
				// 変数初期化
				var inputpath = "";
				// applyステップ毎のバリデーション
				if(1 == stepApply){
					// applyステップ1のバリデート
					if(1 > $("#input-path").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)フレームワークの現在のパスを入力して下さい。");
						// 終了
						return;
					}
					inputpath = $("#input-path").val();
				}
				else if(2 == stepApply){
					// applyステップ2のバリデート
					if(1 > $("#input-genericpath").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)GenericPackageの現在のパスを入力して下さい。");
						// 終了
						return;
					}
					if(1 > $("#input-vendorpath").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)VendorPackageの現在のパスを入力して下さい。");
						// 終了
						return;
					}
					$("#genericpath").text($("#input-genericpath").val());
					$("#vendorpath").text($("#input-vendorpath").val());
				}
				else if(3 == stepApply){
					// applyステップ3のバリデート
					if(1 > $("#input-newframeworkpath").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)フレームワークの移動先のパスを入力して下さい。");
						// 終了
						return;
					}
					if(1 > $("#input-newgenericpath").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)GenericPackageの移動先のパスを入力して下さい。");
						// 終了
						return;
					}
					if(1 > $("#input-newvendorpath").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)VendorPackageの移動先のパスを入力して下さい。");
						// 終了
						return;
					}
					if($("#frameworkpath").text() == $("#input-newframeworkpath").val() && $("#genericpath").text() == $("#input-newgenericpath").val() && $("#vendorpath").text() == $("#input-newvendorpath").val()){
						// 処理をスキップして次の設定へ(ステップは変わらない)
						$("#page" + argStep + "_body" + stepApply).hide();
						// 次のステップをスキップしてさらに次のステップへ自動で移動
						stepApply = 5;
						$("#page" + argStep + "_body" + stepApply).show();
						$("#newframeworkpath").text(frameworkpath);
						$("#input-packagepath").val($("#packagepath").text());
						// ページの上部へ移動
						inPageLocation("#pagetop");
						return;
					}
				}
				else if(4 == stepApply){
					// 5分待っても移動が終わっていないかどうか
					if(waitLoop > waitMAXLoop){
						// 即エラーで終了
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)移動処理に5分以上経っています！\n\n処理に時間が掛かり過ぎています。\n引き続き待つ場合は、再度「設定」ボタンを押して下さい。");
						waitLoop = 0;
						return;
					}
					// 項目ローディング表示
					$("#step" + argStep + "input3form_box .loading").addClass("active");
				}
				else if(5 == stepApply){
					// 何もせず次へ(ステップは変わらない)
					$("#page" + argStep + "_body" + stepApply).hide();
					// 次のステップへ自動で移動
					stepApply++;
					$("#page" + argStep + "_body" + stepApply).show();
					// ページの上部へ移動
					inPageLocation("#pagetop");
					return;
				}
				else if(6 == stepApply){
					// applyステップ6のバリデート
					if(1 > $("#input-packagepath").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)package.xmlの現在のパスを入力して下さい。");
						// 終了
						return;
					}
					inputpath = $("#input-packagepath").val();
				}
				else if(8 == stepApply){
					// 何もせず次へ(ステップは変わらない)
					$("#page" + argStep + "_body" + stepApply).hide();
					stepApply++;
					$("#page" + argStep + "_body" + stepApply).show();
					$("#page" + argStep + " .hlog").text("");
					$("#nextstep").show();
					// 実行ボタンは初期化しておく
					$("#apply").hide();
					$("#apply").removeAttr("disabled", "");
					// ページの上部へ移動
					inPageLocation("#pagetop");
					return;
				}

				var data = { path: inputpath, frameworkpath: $("#frameworkpath").text(), newframeworkpath: $("#input-newframeworkpath").val(), genericpath: $("#genericpath").text(), newgenericpath: $("#input-newgenericpath").val() , vendorpath: $("#vendorpath").text(), newvendorpath: $("#input-newvendorpath").val() };
				if(7 == stepApply){
					// 今まで設定されている物をセット
					data = {
						beforeframeworkpath: beforeframeworkpath,
						beforegenericpath: beforegenericpath,
						beforevendorpath: beforevendorpath,
						frameworkpath: frameworkpath,
						genericpath: genericpath,
						vendorpath: vendorpath,
						packeagepath: packeagepath,
					};
				}

				// 各種フォームパーツの無効化
				$("#input-path").attr("disabled", "disabled");
				$("#input-genericpath").attr("disabled", "disabled");
				$("#input-vendorpath").attr("disabled", "disabled");
				$("#input-newframeworkpath").attr("disabled", "disabled");
				$("#input-newgenericpath").attr("disabled", "disabled");
				$("#input-newvendorpath").attr("disabled", "disabled");
				$("#input-packagepath").attr("disabled", "disabled");
				$("#apply").attr("disabled", "disabled");
				// 項目ローディング表示
				$("#step" + argStep + "input" + stepApply + "form_box .loading").addClass("active");
				$.ajax({
					type: "POST",
					url: "?a=1&step=" + argStep + "&apply=" + stepApply + "&debug=" + isDebug,
					data: data,
					dataType: "json",
					cache: false,
				}).done(function(json) {
					// ローディング非表示
					$("#step" + argStep + "input" + stepApply + "form_box .loading").removeClass("active");
					// 各種disableをハズす
					$("#input-path").removeAttr("disabled", "");
					$("#input-genericpath").removeAttr("disabled", "");
					$("#input-vendorpath").removeAttr("disabled", "");
					$("#input-newframeworkpath").removeAttr("disabled", "");
					$("#input-newgenericpath").removeAttr("disabled", "");
					$("#input-newvendorpath").removeAttr("disabled", "");
					$("#input-packagepath").removeAttr("disabled", "");
					$("#apply").removeAttr("disabled", "");
					if(true == json.ok){
						if(1 == stepApply){
							// フレームワークを発見
							frameworkpath = json.paths[0];
							beforeframeworkpath = frameworkpath;
							$("#frameworkpath").text(frameworkpath);
							// Genericパッケージの確認画面を表示
							$("#paths").text("");
							for(var idx=0; idx < json.paths.length; idx++){
								$("#paths").text($("#paths").text() + json.paths[idx] + "\n");
								if(-1 < json.paths[idx].indexOf("Generic") || -1 < json.paths[idx].indexOf("generic")){
									$("#input-genericpath").val(json.paths[idx]);
								}
								if(-1 < json.paths[idx].indexOf("Vendor") || -1 < json.paths[idx].indexOf("Vendor")){
									$("#input-vendorpath").val(json.paths[idx]);
								}
							}
						}
						else if(2 == stepApply){
							// GenericPackageを発見
							genericpath = $("#genericpath").text();
							// VendorPackageを発見
							vendorpath = $("#vendorpath").text();
							beforegenericpath = genericpath;
							beforevendorpath = vendorpath;
							// フォームに各種パスを初期値として設定
							$("#input-newframeworkpath").val($("#frameworkpath").text());
							$("#input-newgenericpath").val($("#genericpath").text());
							$("#input-newvendorpath").val($("#vendorpath").text());
						}
						else if(3 == stepApply){
							// ローディング強制表示
							$("#step" + argStep + "input" + stepApply + "form_box .loading").addClass("active");
							// 3ステップ目は、1秒後に4ステップ目を実行
							stepApply++;
							waitLoop = 0;
							setTimeout(function() {
								waitLoop++;
								apply(argStep);
							}, 1000);
							return;
						}
						else if(4 == stepApply){
							// 最新の移動ログを画面に表示	
							$("#page" + argStep + " .hlog").text(json.hlog);
							// 4ステップ目は、ディレクトリの移動処理が終わってなければ1秒後にもう一度同じステップを実行する
							if(true == json.wait){
								// 一つ前のステップのローディングを強制表示
								$("#step" + argStep + "input" + (stepApply - 1) + "form_box .loading").addClass("active");
								// 1秒後にもう一度実行
								setTimeout(function() {
									// 同じステップを再度
									waitLoop++;
									apply(argStep);
								}, 1000);
								return;
							}
							// 各種パス情報は次のステップ以降でも使う！
							frameworkpath = $("#input-newframeworkpath").val();
							genericpath = $("#input-newgenericpath").val();
							vendorpath = $("#input-newvendorpath").val();
							$("#newframeworkpath").text(frameworkpath);
							$("#input-packagepath").val($("#packagepath").text());
							$("#page" + argStep + "_body" + (stepApply - 1)).hide();
							// 次のステップへ自動で移動
							stepApply ++;
							$("#page" + argStep + "_body" + stepApply).show();
							// ページの上部へ移動
							inPageLocation("#pagetop");
							return;
						}
						else if(6 == stepApply){
							packeagepath = $("#input-packagepath").val();
							// packeage.xmlの現在の内容を画面に表示
							$("#page" + argStep + " .hlog").text(json.hlog);
						}
						else if(7 == stepApply){
							// 何もしない？？
						}
						// 次の設定へ(ステップは変わらない)
						$("#page" + argStep + "_body" + stepApply).hide();
						$("#page" + argStep + "_body" + (stepApply+1)).show();
						stepApply++;
						// ページの上部へ移動
						inPageLocation("#pagetop");
						return;
					}
					else {
						if(4 == stepApply){
							// 一歩前に戻す
							stepApply--;
						}
						// バリデート以外の理由によるエラー
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)" + json.error);
						// ページの上部へ移動
						inPageLocation("#page" + argStep + " .errormsg");
						return;
					}
				});
			}
			return;
		}
		else if(argStep == 3){
			if(stepApply <= maxStep3apply){
				$("#page" + argStep + " .hlog").text("");
				// ローディング非表示
				$("#step" + argStep + "input" + stepApply + "form_box .loading").removeClass("active");
				// エラーを非表示
				$("#page" + argStep + " .errormsg").hide();
				// applyステップ毎のバリデーション
				if(1 == stepApply){
					// applyステップ1のバリデート
					if(1 > $("#input-fwmpath").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)フレームワークマネージャーの現在のパスを入力して下さい。");
						// 終了
						return;
					}
					fwmpath = $("#input-fwmpath").val();
					var data = { fwmpath: fwmpath };
				}
				else if(2 == stepApply){
					// applyステップ3のバリデート
					if(1 > $("#input-newfwmpath").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)フレームワークマネージャーの移動先のパスを入力して下さい。");
						// 終了
						return;
					}
					if($("#fwmpath").text() == $("#input-newfwmpath").val()){
						// 処理をスキップして次の設定へ(ステップは変わらない)
						$("#page" + argStep + "_body" + stepApply).hide();
						// 次のステップへ自動で移動
						stepApply = 4;
						$("#page" + argStep + " .hlog").text("移動しませんでした。");
						$("#page" + argStep + "_body" + stepApply).show();
						// ページの上部へ移動
						inPageLocation("#pagetop");
						return;
					}
					var data = { fwmpath: fwmpath, newfwmpath: $("#input-newfwmpath").val() };
				}
				else if(3 == stepApply){
					// 5分待っても移動が終わっていないかどうか
					if(waitLoop > waitMAXLoop){
						// 即エラーで終了
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)移動処理に5分以上経っています！\n\n処理に時間が掛かり過ぎています。\n引き続き待つ場合は、再度「設定」ボタンを押して下さい。");
						waitLoop = 0;
						return;
					}
					// 1個前の項目ローディング表示
					$("#step" + argStep + "input" + (stepApply - 1) + "form_box .loading").addClass("active");
					var data = { newfwmpath: $("#input-newfwmpath").val() };
				}
				else if(4 == stepApply){
					// 何もせず次へ(ステップは変わらない)
					$("#page" + argStep + "_body" + stepApply).hide();
					// 次のステップへ自動で移動
					stepApply++;
					$("#page" + argStep + "_body" + stepApply).show();
					// ページの上部へ移動
					inPageLocation("#pagetop");
					return;
				}
				else if(5 == stepApply){
					if(true == $("#input-skipcreatedb").prop("checked")){
						// 自分でDBを設定するを選んだ場合は何もせず次へ(ステップは変わらない)
						$("#page" + argStep + "_body" + stepApply).hide();
						// 次のステップへ自動で移動
						stepApply++;
						$("#page" + argStep + "_body" + stepApply).show();
						// ページの上部へ移動
						inPageLocation("#pagetop");
						return;
					}
					// applyステップ5のバリデート
					if(1 > $("#input-mysqluser").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)データベースユーザー名を入力して下さい。");
						// 終了
						return;
					}
					var data = { fwmpath: fwmpath, mysqluser: $("#input-mysqluser").val(), mysqlpass: $("#input-mysqlpass").val() };
				}
				else if(6 == stepApply){
					// applyステップ6のバリデート
					if(1 > $("#input-fwmdbuser").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)データベースユーザー名を入力して下さい。");
						// 終了
						return;
					}
					if(1 > $("#input-fwmdbpass").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)データベースパスワードを入力して下さい。");
						// 終了
						return;
					}
					if(1 > $("#input-fwmdb").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)データベース名を入力して下さい。");
						// 終了
						return;
					}
					var data = { fwmpath: fwmpath, fwmdbuser: $("#input-fwmdbuser").val(), fwmdbpass: $("#input-fwmdbpass").val(), fwmdb: $("#input-fwmdb").val() };
				}
				else if(7 == stepApply){
					var data = { fwmpath: fwmpath, fwmdbuser: fwmdbuser, fwmdbpass: fwmdbpass, fwmdb: fwmdb };
				}
				else if(8 == stepApply){
					if(1 > $("#input-fwmusername").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)ログインユーザー名を入力して下さい。");
						// 終了
						return;
					}
					if(1 > $("#input-fwmusermail").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)ログインユーザーメールアドレス(ID)を入力して下さい。");
						// 終了
						return;
					}
					if(1 > $("#input-fwmuserpass").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)ログインパスワードを入力して下さい。");
						// 終了
						return;
					}
					var data = { fwmdbuser: fwmdbuser, fwmdbpass: fwmdbpass, fwmdb: fwmdb, fwmusername: $("#input-fwmusername").val(), fwmusermail: $("#input-fwmusername").val(), fwmusermail: $("#input-fwmusermail").val(), fwmuserpass: $("#input-fwmuserpass").val() };
				}
				else if(9 == stepApply){
					// applyステップ9のバリデート
					if(1 > $("#input-fwmdocpath").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)フレームワークマネージャーの公開ディレクトリの現在のパスを入力して下さい。");
						// 終了
						return;
					}
					var data = { fwmpath: fwmpath, fwmdocpath: $("#input-fwmdocpath").val() };
				}
				else if(10 == stepApply){
					// applyステップ10のバリデート
					if(1 > $("#input-fwmbaseurl").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)フレームワークマネージャーの公開ディレクトリのURLを入力して下さい。");
						// 終了
						return;
					}
					if(1 > $("#input-newfwmdocpath").val().length){
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)フレームワークマネージャーの公開ディレクトリの移動先のパスを入力して下さい。");
						// 終了
						return;
					}
					var data = { fwmpath: fwmpath, fwmdocpath: $("#fwmdocpath").text(), newfwmdocpath: $("#input-newfwmdocpath").val(), fwmbaseurl: $("#input-fwmbaseurl").val() };
				}
				else if(11 == stepApply){
					// 5分待っても移動が終わっていないかどうか
					if(waitLoop > waitMAXLoop){
						// 即エラーで終了
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)移動処理に5分以上経っています！\n\n処理に時間が掛かり過ぎています。\n引き続き待つ場合は、再度「設定」ボタンを押して下さい。");
						waitLoop = 0;
						return;
					}
					// 1個前の項目ローディング表示
					$("#step" + argStep + "input" + (stepApply - 1) + "form_box .loading").addClass("active");
					var data = { fwmpath: fwmpath, fwmdocpath: $("#fwmdocpath").text(), newfwmdocpath: $("#input-newfwmdocpath").val(), fwmurl: $("#fwmurldisp").text() };
				}
				else if(12 == stepApply){
					// 何もせず次へ(ステップは変わらない)
					$("#page" + argStep + "_body" + stepApply).hide();
					// 次のステップへ自動で移動
					stepApply++;
					$("#page" + argStep + "_body" + stepApply).show();
					$("#page" + argStep + " .hlog").text("");
					$("#nextstep").show();
					// 実行ボタンは初期化しておく
					$("#apply").hide();
					$("#apply").removeAttr("disabled", "");
					// ページの上部へ移動
					inPageLocation("#pagetop");
					return;
				}

				// 各種フォームパーツの無効化
				$("#input-fwmpath").attr("disabled", "disabled");
				$("#apply").attr("disabled", "disabled");
				// 項目ローディング表示
				$("#step" + argStep + "input" + stepApply + "form_box .loading").addClass("active");
				$.ajax({
					type: "POST",
					url: "?a=1&step=" + argStep + "&apply=" + stepApply + "&debug=" + isDebug,
					data: data,
					dataType: "json",
					cache: false,
				}).done(function(json) {
					// ローディング非表示
					$("#step" + argStep + "input" + stepApply + "form_box .loading").removeClass("active");
					// 各種disableをハズす
					$("#input-fwmpath").removeAttr("disabled", "");
					$("#apply").removeAttr("disabled", "");
					if(true == json.ok){
						if(1 == stepApply){
							$("#fwmpath").text(fwmpath);
							$("#input-newfwmpath").val(fwmpath);
							$("#createdbsql").text(json.createdbsql);
						}
						else if(2 == stepApply){
							// ローディング強制表示
							$("#step" + argStep + "input" + stepApply + "form_box .loading").addClass("active");
							// 3ステップ目は、1秒後に4ステップ目を実行
							stepApply++;
							waitLoop = 0;
							setTimeout(function() {
								waitLoop++;
								apply(argStep);
							}, 1000);
							return;
						}
						else if(3 == stepApply){
							// 最新の移動ログを画面に表示	
							$("#page" + argStep + " .hlog").text(json.hlog);
							// 4ステップ目は、ディレクトリの移動処理が終わってなければ1秒後にもう一度同じステップを実行する
							if(true == json.wait){
								// ローディング強制表示
								$("#step" + argStep + "input" + (stepApply - 1) + "form_box .loading").addClass("active");
								// 1秒後にもう一度実行
								setTimeout(function() {
									// 同じステップを再度
									waitLoop++;
									apply(argStep);
								}, 1000);
								return;
							}
							// 各種パス情報は次のステップ以降でも使う！
							fwmpath = $("#input-newfwmpath").val();
							$("#page" + argStep + "_body" + (stepApply - 1)).hide();
							// 次のステップへ自動で移動
							stepApply++;
							$("#page" + argStep + "_body" + stepApply).show();
							// ページの上部へ移動
							inPageLocation("#pagetop");
							return;
						}
						else if(6 == stepApply){
							fwmdbuser = $("#input-fwmdbuser").val();
							fwmdbpass = $("#input-fwmdbpass").val();
							fwmdb = $("#input-fwmdb").val();
							$("#createtablesql").text(json.createtablesql);
						}
						else if(7 == stepApply){
							$("#fwmdocpath").text(json.fwmdocpath);
							$("#fwmdocpath2").text(json.fwmdocpath);
							$("#fwmdocpath3").text(json.fwmdocpath);
							$("#input-fwmdocpath").val(json.fwmdocpath);
							$("#input-newfwmdocpath").val(json.fwmdocpath);
						}
						else if(8 == stepApply){
							// 何もしない
							fwmusermail = $("#input-fwmusermail").val();
							$("#fwmusermail").text(fwmusermail);
							$("#fwmuserpass").text('********(セキュリティの為表示されません)');
						}
						else if(9 == stepApply){
							// 何もしない
							$("#fwmdocpath2").text($("#input-fwmdocpath").val());
							var basepaths = $("#input-fwmdocpath").val().split("/");
							$("#fwmdocpath3").text(basepaths[basepaths.length-1]);
						}
						else if(10 == stepApply){
							$("#fwmurl").attr("href", json.fwmurl);
							$("#fwmurldisp").text(json.fwmurl);
							// ローディング強制表示
							$("#step" + argStep + "input" + stepApply + "form_box .loading").addClass("active");
							// 8ステップ目は、1秒後に9ステップ目を実行
							stepApply++;
							waitLoop = 0;
							setTimeout(function() {
								waitLoop++;
								apply(argStep);
							}, 1000);
							return;
						}
						else if(11 == stepApply){
							// 最新の移動ログを画面に表示	
							$("#page" + argStep + " .hlog").text(json.hlog);
							// 9ステップ目は、ディレクトリの移動処理が終わってなければ1秒後にもう一度同じステップを実行する
							if(true == json.wait){
								// ローディング強制表示
								$("#step" + argStep + "input" + (stepApply - 1) + "form_box .loading").addClass("active");
								// 1秒後にもう一度実行
								setTimeout(function() {
									// 同じステップを再度
									waitLoop++;
									apply(argStep);
								}, 1000);
								return;
							}
							// 各種パス情報は次のステップ以降でも使う！
							$("#page" + argStep + "_body" + (stepApply - 1)).hide();
							// 次のステップへ自動で移動
							stepApply++;
							$("#page" + argStep + "_body" + stepApply).show();
							// ページの上部へ移動
							inPageLocation("#pagetop");
							return;
						}
						// 次の設定へ(ステップは変わらない)
						$("#page" + argStep + "_body" + stepApply).hide();
						$("#page" + argStep + "_body" + (stepApply+1)).show();
						stepApply++;
						// ページの上部へ移動
						inPageLocation("#pagetop");
						return;
					}
					else {
						if(3 == stepApply){
							// 一歩前に戻す
							stepApply--;
						}
						// バリデート以外の理由によるエラー
						$("#page" + argStep + " .errormsg").show();
						$("#page" + argStep + " .errormsg").text("(!!!)" + json.error);
						// ページの上部へ移動
						inPageLocation("#page" + argStep + " .errormsg");
						return;
					}
				});
			}
			return;
		}
		else if(argStep == 4){
			if(stepApply <= maxStep4apply){
				$("#page" + argStep + " .hlog").text("");
				// ローディング非表示
				$("#step" + argStep + "input" + stepApply + "form_box .loading").removeClass("active");
				// エラーを非表示
				$("#page" + argStep + " .errormsg").hide();
				// applyステップ毎のバリデーション
				if(1 == stepApply){
					// applyステップ1のバリデート
					var data = {};
					var skip = true;
					if(true == $("#input-sslon").prop("checked")){
						skip = false;
						data["sslon"] = "1";
					}
					if(1 < $("#input-ipaddress").val().length){
						skip = false;
						data["ipaddress"] = $("#input-ipaddress").val();
					}
					if(skip){
						// 次の設定へ
						$("#page" + argStep + "_body" + stepApply).hide();
						$("#page" + argStep + "_body" + (stepApply+1)).show();
						stepApply++;
					}
					else {
						data["fwmpath"] = fwmpath;
					}
				}

				if(!skip){
					// 各種フォームパーツの無効化
					$("#apply").attr("disabled", "disabled");
					// 項目ローディング表示
					$("#step" + argStep + "input" + stepApply + "form_box .loading").addClass("active");
					$.ajax({
						type: "POST",
						url: "?a=1&step=" + argStep + "&apply=" + stepApply + "&debug=" + isDebug,
						data: data,
						dataType: "json",
						cache: false,
					}).done(function(json) {
						// ローディング非表示
						$("#step" + argStep + "input" + stepApply + "form_box .loading").removeClass("active");
						if(true == json.ok){
							// 次の設定へ
							$("#page" + argStep + "_body" + stepApply).hide();
							$("#page" + argStep + "_body" + (stepApply+1)).show();
							stepApply++;
						}
						else {
							// バリデート以外の理由によるエラー
							$("#page" + argStep + " .errormsg").show();
							$("#page" + argStep + " .errormsg").text("(!!!)" + json.error);
							// ページの上部へ移動
							inPageLocation("#page" + argStep + " .errormsg");
							return;
						}
					});
				}
			}
		}

		// 全ての設定が成功で終了
		// 次のステップへ行けるように
		$("#nextstep").show();
		// 実行ボタンは初期化しておく
		$("#apply").hide();
		$("#apply").removeAttr("disabled", "");
		// ページの上部へ移動
		inPageLocation("#pagetop");

		// 終了
		return;
	}

	// ページ内リンクへオートスクロール
	// ※ ロケーションさせない！
	function inPageLocation(argLocationName){
		var speed = 500;
		var position = $(argLocationName).offset().top;
		$("html, body").animate({scrollTop:position}, speed, "swing");
	}

	// querystringから指定のkeyの値を返す
	function getParameterByName(argKey) {
		key = argKey.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
		var regex = new RegExp("[\\?&]" + key + "=([^&#]*)"), results = regex.exec(location.search);
		return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}

	// 各種フォームの初期値のセット
	$("#input-path").val("<?php echo $frameworkPath; ?>");
	$("#input-fwmbaseurl").val($("#input-fwmbaseurl").val() + "<?php $urls = explode('?', $_SERVER['REQUEST_URI']); $urls[0] = str_replace('/FrameworkPackage/installer/', '/FrameworkManager/template/', $urls[0]); echo $_SERVER['SERVER_NAME'].$urls[0]; ?>");
	$("#input-fwmpath").val("<?php echo $fwmgrPath; ?>");
	$("#input-mysqluser").val("root");
	$("#input-mysqlpass").val("root");
	$("#input-fwmdbuser").val("fwm");
	$("#input-fwmdbpass").val("fwmpass");
	$("#input-fwmdb").val("fwm");
	fwmpath = "<?php echo $fwmgrPath; ?>";

	// デバッグフラグの設定
	if("" !== getParameterByName("debug")){
		isDebug = parseInt(getParameterByName("debug"));
	}
	// ページ初期表示処理
	if("" !== getParameterByName("step")){
		step = parseInt(getParameterByName("step"));
		next(step);
	}
});
</script>
</head>
<body id="pagetop">
	<h1 id="title" class="title"><?php if("UNICORN" === PROJECT_NAME) { ?><img width="40" height="40" src="https://dl.dropboxusercontent.com/u/22810487/UNICORN/image/logo-mini.png"/><?php } echo PROJECT_NAME; ?></h1>
	<section id="page0" class="page_box">
		<article class="page">
			<h2 id="page1_title" class="page_title"><?php echo PROJECT_NAME; ?>へようこそ。</h2>
			<div id="page1_body" class="page_body">
				<p>
					<strong class="orange">フレームワークのインストールを開始します。</strong><br>
					<br>
					<small>インストールはこのページに従って</small>
					<br>
					<strong>たった4ステップの作業</strong>
					<br>
					<small>を行うだけで完了します。</small>
					<br>
					<strong>しかも、そのうちの3ステップは”任意”です。</strong>
					<br>
					<br>
					<br>
					<strong>4ステップはこうなっています。</strong>
					<br>
					<small>・システム要件のバリデーション(必須)</small>
					<br>
					<small>・フレームワークの各種パスの確認と変更(任意)</small>
					<br>
					<small>・フレームワーク管理機能のインストール(任意)</small>
					<br>
					<small>・フレームワーク管理機能のアクセス制限設定(任意)</small>
					<br>
					<br>
					<br>
					<strong>しかし、このフレームワークに不慣れな人はフレームワーク管理機能をインストールする事を強く薦めます。</strong>
					<br>
					<br>
					<small>フレームワークを</small>
					<br>
					<strong>自由に、思い通りに利用する為の様々な設定や</strong>
					<br>
					<strong>フレームワークのチュートリアル</strong><small>が</small>
					<br>
					<small>フレームワーク管理機能によって提供されています。</small>
					<br>
					<br>
					<strong>是非、それらを利用して下さい。</strong>
					<br>
				</p>
			</div>
		</article>
	</section>
	<section id="page1" class="page_box">
		<article class="page">
			<h3 id="page1_title" class="page_title"><strong class="orange">STEP:1</strong><br>システム要件のバリデーション(必須)</h3>
			<div id="page1_body" class="page_body">
				<p><strong class="orange">このインストーラーを実行しているサーバーが、フレームワークの実行に必要なシステム要件を満たしているかチェックします。</strong></p>
			</div>
			<h4 id="page1_sub_title" class="page_sub_title">フレームワークの実行に必要なシステム要件</h4>
			<dl id="page1_sub_body" class="page_sub_body">
				<dt id="step1validate1">
					1.PHP5.2以上で動する<span class="red">(必須)</span><span class="loading"></span><br>
					<small class="red">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;※5.2未満の場合以下の関数が動作しません！<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Utilities::gmnow<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Utilities::modifyDate
					</small>
					<pre class="errormsg"></pre>
				</dt>
				<dt id="step1validate2">
					2.各種ファイル操作が利用出来る<span class="red">(必須)</span><span class="loading"></span><br>
					<pre class="errormsg"></pre>
				</dt>
				<dt id="step1validate3">
					3.mcryptが利用出来る<span class="yellow">(推奨)</span><span class="loading"></span><br>
					<small class="green">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;※mcryptが利用出来ない場合以下のクラスが動作しません<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cipher<br>
					</small>
					<pre class="errormsg"></pre>
				</dt>
				<dt id="step1validate4">
					4.80番443番で他サーバーにアクセス出来る<span class="yellow">(推奨)</span><span class="loading"></span>
					<pre class="errormsg"></pre>
				</dt>
			</dl>
			<h4 id="page1_sub_title2" class="page_sub_title">
				上記4項目のチェックを行います。
				<br>
				<br>
				よろしければ「実行」ボタンを押して下さい。
			</h4>
			<h4 id="page1_sub_title3" class="page_sub_title errormsg red">
				エラーがあります！
				<br>
				<br>
				インストールを続行する為にはエラーを解決する必要があります！
				<br>
				エラーメッセージを参考にサーバーの設定を変える等して、エラーを解決して下さい。
			</h4>
			<h4 id="page1_sub_title4" class="page_sub_title green">
				おめでとう御座います！！
				<br>
				<br>
				このサーバーはシステム要件を満たしています。
				<br>
				フレームワークは今直ぐにでも開始出来る状態です。
				<br>
				<br>
				次のステップでは、フレームワークの配置の確認と設定を行います。
				<br>
				設定を行う場合は「次のステップへ」ボタンを推して下さい。
			</h4>
		</article>
	</section>
	<section id="page2" class="page_box">
		<article class="page">
			<h3 id="page2_title" class="page_title"><strong class="orange">STEP:2</strong><br>フレームワークの各種パスの確認と変更(任意)</h3>
			<div id="page2_body1" class="page_body">
				<p>
					<strong class="orange">先ず、フレームワークの現在のパスを確認させて下さい。</strong>
					<br>
					<br>
					フレームワークはダウンロード直後
					<br>
					<strong class="orange"><?php echo $frameworkPath; ?></strong>
					<br>
					に、あるハズです。
					<br>
					<br>
					<strong>既に移動している場合は、以下のフォームに移動先のパスを入力して「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
					<strong>移動していない場合は、そのまま「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
				<div id="step2input1form_box" class="text-input-form">
					<form id="step2input1form" name="step2input1form">
						<input id="input-path" class="input-text" type="text" name="path" value="" maxlenght="255" />
						<input id="input-path-reset" class="input-reset" type="reset" value="×" />
					</form><span class="loading"></span>
				</div>
			</div>
			<div id="page2_body2" class="page_body">
				<p>
					<strong class="green">フレームワークは指定のパスで確認出来ました！</strong>
					<br>
					<br>
					<strong id="frameworkpath" class="green"></strong>
					<br>
					<br>
					また、関連パッケージと推測されるディレクトリが以下に表示されています。
					<br>
					<br>
				</p>
				<div>
					<pre id="paths"></pre>
				</div>
				<br>
				<br>
				<p>
					<strong class="orange">”GenericPackage”と"VendorPackage"の現在のパスを確認させて下さい。</strong>
					<br>
					<br>
					フレームワークは<strong>”単独動作する独立性の高いライブラリ群”</strong>を、動作の為に必要とします。
					<br>
					フレームワークではそれを<strong>”GenericPackage”</strong>と呼んでいます。
					<br>
					また、”VendorPackage”には、フレームワークが利用している外部ライブラリが格納されています。
					<br>
					Pear等もその一つとして、”VendorPackage”内に配置されています。
					<br>
					<br>
					上記の発見された関連パッケージ等を参考に<strong>”GenericPackage”</strong>に該当するものを選んで、<strong>以下のフォームに入力して下さい。</strong>
					<br>
					<br>
					上記の発見された関連パッケージ等を参考に<strong>”VendorPackage”</strong>に該当するものを選んで、<strong>以下のフォームに入力して下さい。</strong>
					<br>
					<br>
					<strong>入力後に「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
					<strong>デフォルトでは、推測されたパッケージが以下に既に入力済みとなっています。</strong>
					<br>
					<strong>確認し間違いがない、或いはダウンロードしてから何も変更してなければそのまま「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
				<small>GenericPackageパス</small>
				<div id="step2input2form_box" class="text-input-form">
					<form id="step2input2-1form" name="step2input2-1form">
						<input id="input-genericpath" class="input-text" type="text" name="genericpath" value="" maxlenght="255" />
						<input id="input-genericpath-reset" class="input-reset" type="reset" value="×" />
					</form><span class="loading"></span>
				</div>
				<br>
				<small>VendorPackageパス</small>
				<div id="step2input2-2form_box" class="text-input-form">
					<form id="step2input2-2form" name="step2input2-2form">
						<input id="input-vendorpath" class="input-text" type="text" name="vendorpath" value="" maxlenght="255" />
						<input id="input-vendorpath-reset" class="input-reset" type="reset" value="×" />
					</form>
				</div>
			</div>
			<div id="page2_body3" class="page_body">
				<p>
					<strong class="green">”GenericPackage”は指定のパスで確認出来ました！</strong>
					<br>
					<br>
					<strong id="genericpath" class="green"></strong>
					<br>
					<br>
					<br>
					<strong class="green">”VendorPackage”は指定のパスで確認出来ました！</strong>
					<br>
					<br>
					<strong id="vendorpath" class="green"></strong>
					<br>
					<br>
					<br>
					<strong class="orange">フレームワークを、指定の任意のパスに移動する事が出来ます。</strong>
					<br>
					<br>
					移動する場合は以下のそれぞれのフォームに、<strong>移動先となるパスを入力して「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
					<strong>移動しない場合は、そのまま「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
					<?php if(is_file(dirname(dirname($frameworkPath))."/vendor/UNICORN/composer.lock")){ ?>
					<strong class="red">※フレームワークがcomposerによって配置されています！<br/>その場合、移動しない事をオススメします！！</strong>
					<br>
					<br>
					<?php } ?>
				</p>
				<br>
				<small>フレームワークパス</small>
				<div id="step2input3form_box" class="text-input-form">
					<form id="step2input3-1form" name="step2input3-1form">
						<input id="input-newframeworkpath" class="input-text" type="text" name="newframeworkpath" value="" maxlenght="255" />
						<input id="input-newframeworkpath-reset" class="input-reset" type="reset" value="×" />
					</form><span class="loading"></span>
				</div>
				<br>
				<small>GenericPackageパス</small>
				<div id="step2input3-2form_box" class="text-input-form">
					<form id="step2input3-2form" name="step2input3-2form">
						<input id="input-newgenericpath" class="input-text" type="text" name="newgenericpath" value="" maxlenght="255" />
						<input id="input-newgenericpath-reset" class="input-reset" type="reset" value="×" />
					</form>
				</div>
				<br>
				<small>VendorPackageパス</small>
				<div id="step2input3-3form_box" class="text-input-form">
					<form id="step2input3-3form" name="step2input3-3form">
						<input id="input-newvendorpath" class="input-text" type="text" name="newvendorpath" value="" maxlenght="255" />
						<input id="input-newvendorpath-reset" class="input-reset" type="reset" value="×" />
					</form>
				</div>
			</div>
			<div id="page2_body5" class="page_body">
				<p>
					<strong class="green">フレームワークのパスの設定が無事に完了しました！</strong>
					<br>
					<br>
					関連の他のパッケージの移動は任意です。
					<br>
					<br>
					<strong>下の移動ログを確認し、「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
			</div>
			<div id="page2_body6" class="page_body">
				<p>
					<strong class="orange">フレームワークのパッケージ設定ファイルの中のパス設定状態を確認します。</strong>
					<br>
					<br>
					フレームワークは「package.xml」と言うXMLファイルによって<strong>”パッケージ管理”</strong>を行うようになっています。
					<br>
					<strong>”パッケージ”</strong>とは、オブジェクト・クラス・インスタンスの<strong>”依存関係をまとめて一つの名前を付ける事”</strong>とフレームワークでは定義付けています。
					<br>
					パッケージを管理する事でフレームワークはオートローダから該当のパッケージをXML定義内から探し出し
					<br>
					読み込みを行ったり、さまさばな設定を自動で行ってくれます。
					<br>
					また、XML管理にする事で、パッケージ内でのクラス間の依存関係や、アプリケーション依存した設定といった関心事を一箇所に集中させる意図を持っています。
					<br>
					<br>
					フレームワークのパッケージ設定ファイルは現在
					<br>
					<p id="packagepath" class="orange"><strong id="newframeworkpath"></strong><strong>/core/package.xml</strong></p>
					<br>
					に、あるハズです。
					<br>
					パッケージ設定の読み込みを行います。
					<br>
					<br>
					<strong>既に移動している場合は、以下のフォームに移動先のパスを入力して「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
					<strong>移動していない場合は、そのまま「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
				<div id="step2input6form_box" class="text-input-form">
					<form id="step2input6form" name="step2input6form">
						<input id="input-packagepath" class="input-text" type="text" name="path" value="" maxlenght="255" />
						<input id="input-packagepath-reset" class="input-reset" type="reset" value="×" />
					</form><span class="loading"></span>
				</div>
			</div>
			<div id="page2_body7" class="page_body">
				<p>
					<strong class="green">フレームワークのパッケージ設定ファイルの読み込みが成功しました！</strong>
					<br>
					<br>
					以下に設定ファイルの内容が表示されています。
					<br>
					<strong>「default」節内の「自動走査パスの設定」とコメントされているパス情報が</strong>
					<br>
					<strong>正しく設定されている事がフレームワークの動作に必要</strong>となります。
					<br>
					<br>
					<strong class="orange">先程設定されたパス情報を元に、この設定を書き換えます。</strong>
					<br>
					<br>
					<strong>設定ファイルの内容を良く確認し、「設定」ボタンを押して下さい。</strong>
					<br>
					「設定」ボタンを押すと同時に、内容は自動的に書き換えられます。
					<br>
					<br>
					<br>
					<strong class="red">ちなみに、フレームワーク内のクラスの利用方法は簡単です。</strong>
					<br>
					<br>
					ココに表示されている<strong>"パッケージ名"(例:DBO節などのタグ名)は、そのままクラス名</strong>となります。
					<br>
					<strong>「<?php echo PROJECT_NAME; ?>.php」をincludeして</strong>
					<br>
					<strong>後はクラス名を指定する(例:$DB = new DBO();)</strong>
					<br>
					<br>
					<strong class="red">だけです。</strong>
					<br>
					<br>
					<br>
					<strong>フレームワークの詳しい利用方法は、<a target="_blank" href="http://saimushi.github.io/<?php echo PROJECT_NAME; ?>/">UNICORNのWebサイト</a>を確認して下さい。</strong>
					<br>
					<br>
				</p>
			</div>
			<div id="page2_body8" class="page_body">
				<p>
					<strong class="green">フレームワークのパッケージ設定ファイルの書き換えが成功しました！</strong>
					<br>
					<br>
					以下に<strong>書き換え後の新しい設定ファイルの内容</strong>が表示されています。
					<br>
					<br>
					<strong>設定ファイルの内容を良く確認し、「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
			</div>
			<div id="page2_body9" class="page_body">
				<p>
					<strong class="green">おめでとう御座います！！</strong>
					<br>
					<br>
					<strong class="green">フレームワークの各種パスの確認と変更が完了しました。</strong>
					<br>
					<strong class="green">フレームワークのインストールがが完了しました。</strong>
					<br>
					<strong class="orange">
						※次以降はフレームワーク管理機能のインストール(任意)になります。
						フレームワーク管理機能のインストール(任意)を行う場合は、「次のステップへ」ボタンを押して下さい。
					</strong>
					<br>
					<br>
					<strong>フレームワークの詳しい利用方法は、<a target="_blank" href="http://saimushi.github.io/<?php echo PROJECT_NAME; ?>/">UNICORNのWebサイト</a>を確認して下さい。</strong>
					<br>
					<br>
				</p>
			</div>
			<div class="page_body">
				<pre class="hlog"></pre>
			</div>
			<div class="page_body">
				<pre class="errormsg red"></pre>
			</div>
		</article>
	</section>
	<section id="page3" class="page_box">
		<article class="page">
			<h3 id="page3_title" class="page_title"><strong class="orange">STEP:3</strong><br>フレームワーク管理機能のインストール(任意)</h3>
			<div id="page3_body1" class="page_body">
				<p>
					<strong class="orange">先ず、フレームワーク管理機能の現在のパスを確認させて下さい。</strong>
					<br>
					<br>
					フレームワーク管理機能は”FrameworkManager”(以後、フレームワークマネージャーと呼びます)と言うディレクトリ名で
					<br>
					ダウンロード直後は
					<br>
					<strong class="orange"><?php echo $fwmgrPath; ?></strong>
					<br>
					に、あるハズです。
					<br>
					<br>
					<strong>既に移動している場合は、以下のフォームに移動先のパスを入力して「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
					<strong>移動していない場合は、そのまま「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
				<div id="step3input1form_box" class="text-input-form">
					<form id="step3input1form" name="step3input1form">
						<input id="input-fwmpath" class="input-text" type="text" name="fwmpath" value="" maxlenght="255" />
						<input id="input-fwmpath-reset" class="input-reset" type="reset" value="×" />
					</form><span class="loading"></span>
				</div>
			</div>
			<div id="page3_body2" class="page_body">
				<p>
					<strong class="green">フレームワークマネージャーは指定のパスで確認出来ました！</strong>
					<br>
					<br>
					<strong id="fwmpath" class="green"></strong>
					<br>
					<br>
					<br>
					<strong class="orange">フレームワークマネージャーを、指定の任意のパスに移動する事が出来ます。</strong>
					<br>
					<br>
					移動する場合は以下のフォームに、<strong>移動先となるパスを入力して「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
					<strong>移動しない場合は、そのまま「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
					<?php if(is_file(dirname(dirname($frameworkPath))."/vendor/UNICORN/composer.lock")){ ?>
					<strong class="red">※フレームワークがcomposerによって配置されています！<br/>その場合、移動しない事をオススメします！！</strong>
					<br>
					<br>
					<?php } ?>
				</p>
				<br>
				<div id="step3input2form_box" class="text-input-form">
					<form id="step3input2form" name="step3input2form">
						<input id="input-newfwmpath" class="input-text" type="text" name="newfwmpath" value="" maxlenght="255" />
						<input id="input-newfwmpath-reset" class="input-reset" type="reset" value="×" />
					</form><span class="loading"></span>
				</div>
			</div>
			<div id="page3_body4" class="page_body">
				<p>
					<strong class="green">フレームワークマネージャーのパスの設定が無事に完了しました！</strong>
					<br>
					<br>
					<strong>下の移動ログを確認し、「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
			</div>
			<div id="page3_body5" class="page_body">
				<p>
					<strong class="orange">フレームワークマネージャー用のデータベースを作成します。</strong>
					<br>
					<br>
					<strong class="red">
						(!!!)データベースはMySQLデータベースサーバーを必要とします。
						また、MySQLサーバーはこのインストーラーを実行しているサーバーにインストールされている必要があります。
						MySQLデータベースサーバーがまだインストールされていない場合は
						先ずMySQLデータベースサーバーをインストールして下さい。
					</strong>
					<br>
					以下のSQL文を実行し、データベースの追加とデータベースユーザーの追加を実行します。
					<br>
					<br>
					<pre id="createdbsql" class="strong orange"></pre>
					<br>
					<br>
					<strong>
						create database文とgrant文をmysqlデータベースに対して実行が可能な
						データベースの接続情報(rootユーザー情報等)を
						以下に入力して「設定」ボタンを押して下さい。
					</strong>
					<br>
					<br>
					<br>
					<strong class="red">
					尚、入力されたデータベースのユーザー・パスワードは保存されずそのまま破棄されます！
					</strong>
					<br>
					<br>
				</p>
				<br>
				<small>データベースユーザー名</small>
				<div id="step3input5form_box" class="text-input-form">
					<form id="step3input5-1form" name="step3input5-1form">
						<input id="input-mysqluser" class="input-text" type="text" name="mysqluser" value="" maxlenght="255" />
						<input id="input-mysqluser-reset" class="input-reset" type="reset" value="×" />
					</form><span class="loading"></span>
				</div>
				<br>
				<small>データベースパスワード</small>
				<div id="step3input5-2form_box" class="text-input-form">
					<form id="step3input5-2form" name="step3input5-2form">
						<input id="input-mysqlpass" class="input-text" type="password" name="mysqlpass" value="" maxlenght="255" />
						<input id="input-mysqlpass-reset" class="input-reset" type="reset" value="×" />
					</form>
				</div>
				<br>
				<small>この手順をスキップして、自身でデータベースの設定をする</small>
				<div id="step3input5-4form_box" class="text-checkbox-form">
					<form id="step3input5-4form" name="step3input5-4form">
						<input id="input-skipcreatedb" class="input-checkbox" type="checkbox" name="skipcreatedb" />
					</form>
				</div>
			</div>
			<div id="page3_body6" class="page_body">
				<p>
					<strong class="green">フレームワークマネージャー用のデータベースを作成しました！</strong>
					<br>
					<br>
					<strong class="orange">
						フレームワークマネージャー用のデータベースへテスト接続と
						フレームワークマネージャーの設定ファイルの更新を行います。
					</strong>
					<br>
					<br>
					<strong>作成したフレームワークマネージャー用のデータベースの接続情報を入力して「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
					<strong>以前のステップでインストーラーによってデータベース作成している場合は、そのまま「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
				<br>
				<small>データベースユーザー名</small>
				<div id="step3input6form_box" class="text-input-form">
					<form id="step3input6-1form" name="step3input6-1form">
						<input id="input-fwmdbuser" class="input-text" type="text" name="mysqluser" value="" maxlenght="255" />
						<input id="input-fwmdbuser-reset" class="input-reset" type="reset" value="×" />
					</form><span class="loading"></span>
				</div>
				<br>
				<small>データベースパスワード</small>
				<div id="step3input6-2form_box" class="text-input-form">
					<form id="step3input6-2form" name="step3input6-2form">
						<input id="input-fwmdbpass" class="input-text" type="text" name="fwmdbpass" value="" maxlenght="255" />
						<input id="input-fwmdbpass-reset" class="input-reset" type="reset" value="×" />
					</form>
				</div>
				<br>
				<small>データベース名</small>
				<div id="step3input6-2form_box" class="text-input-form">
					<form id="step3input6-3form" name="step3input6-3form">
						<input id="input-fwmdb" class="input-text" type="text" name="fwmdb" value="" maxlenght="255" />
						<input id="input-fwmdb-reset" class="input-reset" type="reset" value="×" />
					</form>
				</div>
			</div>
			<div id="page3_body7" class="page_body">
				<p>
					<strong class="green">
						フレームワークマネージャー用のデータベースへのテスト接続に成功しました！
						フレームワークマネージャーの設定ファイルを更新しました！
					</strong>
					<br>
					<strong class="orange">
						フレームワークマネージャーが利用する、データベーステーブルの作成を行います。
					</strong>
					<br>
					以下のSQL文を実行し、データベースにテーブルを追加します。
					<br>
					<br>
					<pre id="createtablesql" class="strong orange"></pre>
					<br>
					<br>
					<strong>上記SQL文を確認し「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
			</div>
			<div id="page3_body8" class="page_body">
				<p>
					<strong class="green">
						フレームワークマネージャー用のデータベースへテーブルを追加しました！
					</strong>
					<br>
					<strong class="orange">
						フレームワークマネージャーにログインするデフォルトアカウントを設定します。
					</strong>
					<br>
					ここで設定するデフォルトアカウントはフレームワークマネージャーのフル権限を持ちます。
					<br>
					また、ここで設定する<strong class="red">デフォルトアカウントのIDとパスワードはフレームワークの設定ファイル等には保存されず
					ファイルから探り当てる事等は出来ませんので、忘れないように大切に保管して下さい。</strong>
					<br>
					<br>
					<strong>以下にユーザー名・メールアドレスとパスワードを入力し「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
				<br>
				<small>フレームワークマネージャーのログインユーザー名</small>
				<div id="step3input8form_box" class="text-input-form">
					<form id="step3input8-1form" name="step3input8-1form">
						<input id="input-fwmusername" class="input-text" type="text" name="fwmusername" value="" maxlenght="255" />
						<input id="input-fwmusername-reset" class="input-reset" type="reset" value="×" />
					</form><span class="loading"></span>
				</div>
				<br>
				<small>フレームワークマネージャーのログインユーザーメールアドレス(ID)</small>
				<div id="step3input8-2form_box" class="text-input-form">
					<form id="step3input8-2form" name="step3input8-2form">
						<input id="input-fwmusermail" class="input-text" type="text" name="fwmusermail" value="" maxlenght="255" />
						<input id="input-fwmusermail-reset" class="input-reset" type="reset" value="×" />
					</form>
				</div>
				<br>
				<small>フレームワークマネージャーのログインパスワード</small>
				<div id="step3input8-3form_box" class="text-input-form">
					<form id="step3input8-3form" name="step3input8-3form">
						<input id="input-fwmuserpass" class="input-text" type="password" name="fwmuserpass" value="" maxlenght="255" />
						<input id="input-fwmuserpass-reset" class="input-reset" type="reset" value="×" />
					</form>
				</div>
			</div>
			<div id="page3_body9" class="page_body">
				<p>
					<strong class="green">
						フレームワークマネージャー用のデータベースへテーブルを追加しました！
					</strong>
					<br>
					<strong class="orange">
						最後に、フレームワークマネージャーの公開ディレクトリの移動を行います。
					</strong>
					<br>
					公開ディレクトリを指定して、フレームワークマネージャーを公開しましょう！
					<br>
					公開する事により、フレームワークマネージャーを利用出来るようになります。
					<br>
					また、公開してもIDとパスワードが設定されています。
					<br>
					不要にアクセスされる事は無いので安心して下さい。
					<br>
					<br>
					フレームワークマネージャーの公開ディレクトリは現在
					<br>
					<strong id="fwmdocpath" class="orange"></strong>
					<br>
					に、あるハズです。
					<br>
					<br>
					<strong>既に移動している場合は、以下のフォームに移動先のパスを入力して「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
					<strong>移動していない場合は、そのまま「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
				<br>
				<div id="step3input9form_box" class="text-input-form">
					<form id="step3input9form" name="step3input9form">
						<input id="input-fwmdocpath" class="input-text" type="text" name="fwmdocpath" value="" maxlenght="255" />
						<input id="input-fwmdocpath-reset" class="input-reset" type="reset" value="×" />
					</form><span class="loading"></span>
				</div>
			</div>
			<div id="page3_body10" class="page_body">
				<p>
					<strong class="green">フレームワークマネージャーの公開ディレクトリは指定のパスで確認出来ました！</strong>
					<br>
					<br>
					<strong id="fwmdocpath2" class="green"></strong>
					<br>
					<br>
					<br>
					<strong class="orange">フレームワークマネージャーの公開ディレクトリを、指定の任意のパスに移動する事が出来ます。</strong>
					<br>
					<br>
					<strong>「フレームワークマネージャーの公開ディレクトリのURL」を入力して下さい。</strong>
					<br>
					<strong class="red">※</strong>
					<strong id="fwmdocpath3" class="orange"></strong>
					<strong class="red">をURLに含めないで下さい！</strong>
					<br>
					<br>
					フレームワークマネージャーの公開ディレクトリを移動する場合は以下の<strong>「フレームワークマネージャーの公開ディレクトリの実パス」</strong>フォームに、<strong>移動先となるパスを入力して下さい。</strong>
					<br>
					<br>
					移動しない場合は、<strong>「フレームワークマネージャーの公開ディレクトリのURL」</strong>だけを入力して、<strong>「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
				<br>
				<small>フレームワークマネージャーの公開ディレクトリのURL</small>
				<div id="step3input10form_box" class="text-input-form">
					<form id="step3input10-1form" name="step3input10-1form">
						<input id="input-fwmbaseurl" class="input-text" type="text" name="fwmbaseurl" value="http://" maxlenght="255" />
						<input id="input-fwmbaseurl-reset" class="input-reset" type="reset" value="×" />
					</form><span class="loading"></span>
				</div>
				<br>
				<small>フレームワークマネージャーの公開ディレクトリの実パス</small>
				<div id="step3input10-2form_box" class="text-input-form">
					<form id="step3input10-2form" name="step3input10-2form">
						<input id="input-newfwmdocpath" class="input-text" type="text" name="newfwmdocpath" value="" maxlenght="255" />
						<input id="input-newfwmdocpath-reset" class="input-reset" type="reset" value="×" />
					</form>
				</div>
			</div>
			<div id="page3_body12" class="page_body">
				<p>
					<strong class="green">フレームワークマネージャーの公開ディレクトリのパスの設定が無事に完了しました！</strong>
					<br>
					<br>
					<strong>下の移動ログを確認し、「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
				</p>
			</div>
			<div id="page3_body13" class="page_body">
				<p>
					<strong class="green">おめでとう御座います！！</strong>
					<br>
					<br>
					<strong class="green">フレームワークマネージャーのインストールがが完了しました。</strong>
					<br>
					<br>
					以下のURLより、フレームワークマネージャーにアクセスする事が可能です。
					<br>
					<br>
					<br>
					<a id="fwmurl" href="./lib/FrameworkManager/template/managedocs/" target="_blank" class="green"><strong id="fwmurldisp" class="green">./lib/FrameworkManager/template/managedocs/</strong>&nbsp;&nbsp;(新しいウィンドウ・タブで開きます)</a>
					<br>
					<br>
					<br>
					また、設定されたログインIDとパスワードは
					<br>
					<br>
					ID: <strong class="green" id="fwmusermail">admin@myserver.myadmin</strong>
					<br>
					パスワード: <strong class="green" id="fwmuserpass">abcd1234</strong>
					<br>
					<br>
					と、なっています。
					<br>
					<br>
					<br>
					<strong class="green">フレームワークマネージャーを活用して、より高速で柔軟な開発が出来る事を願っています！</strong>
					<br>
					<br>
					<br>
					<strong class="orange">
						※次以降はフレームワークマネージャーのアクセス制限の確認・設定(任意)が行えます。
						フレームワークマネージャーのアクセス制限の確認・設定(任意)を行う場合は、「次のステップへ」ボタンを押して下さい。
					</strong>
					<br>
					<br>
					<strong>フレームワークマネージャーの詳しい利用方法は、<a target="_blank" href="http://saimushi.github.io/<?php echo PROJECT_NAME; ?>/">UNICORNのWebサイト</a>を確認して下さい。</strong>
				</p>
			</div>
			<div class="page_body">
				<pre class="hlog"></pre>
			</div>
			<div class="page_body">
				<pre class="errormsg red"></pre>
			</div>
		</article>
	</section>
	<section id="page4" class="page_box">
		<article class="page">
			<h3 id="page4_title" class="page_title"><strong class="orange">STEP:4</strong><br>フレームワーク管理機能のアクセス制限の設定(任意)</h3>
			<div id="page4_body1" class="page_body">
				<p>
					<strong class="orange">フレームワーク管理機能へのアクセスを制限する事が出来ます。</strong>
					<br>
					<br>
					・SSL接続のみにする事が出来ます。
					<br>
					・また、IPアドレスをカンマ区切りに複数指定し、該当するIPアドレスからの接続のみにする事が出来ます。
					<br>
					<br>
					<br>
					<strong>以下のフォームにアクセス制限情報を入力して「設定」ボタンを押して下さい。</strong>
					<br>
					<br>
					<strong>アクセス制限をしない場合は、そのまま「設定」ボタンを押して下さい。</strong>
					<br>
					<strong class="red">※アクセス制限をしない場合、ログインIDを知る全てのユーザーが管理機能にログイン出来る事に注意して下さい！</strong>
					<br>
					<br>
					<br>
					<strong>尚、現在の貴方のIPアドレスは<strong class="red"><?php echo $_SERVER['REMOTE_ADDR']; ?></strong>です。</strong>
					<br>
					<br>
					<br>
				</p>
				<br>
				<small>SSL接続のみ許可する</small>
				<div id="step4input11form_box" class="text-checkbox-form">
					<form id="step4input11-1form" name="step4input11-1form">
						<input id="input-sslon" class="input-checkbox" type="checkbox" name="sslon" />
					</form><span class="loading"></span>
				</div>
				<br>
				<small>指定したIPアドレスからの接続のみ許可する(カンマ区切りで複数指定可)</small>
				<div id="step4input11-2form_box" class="text-input-form">
					<form id="step4input11-2form" name="step4input11-2form">
						<input id="input-ipaddress" class="input-text" type="text" name="ipaddress" value="" maxlenght="255" />
						<input id="input-mysqluser-reset" class="input-reset" type="reset" value="×" />
					</form>
				</div>
			</div>
			<div id="page4_body2" class="page_body">
				<p>
					<strong class="green">おめでとう御座います！！</strong>
					<br>
					<br>
					<strong class="green">フレームワーク管理機能へのアクセス制限の設定が完了しました。</strong>
					<br>
					<br>
					<br>
					<strong class="green">
						「次のステップへ」ボタンを押して、インストールを完了しましょう！
					</strong>
					<br>
					<br>
				</p>
			</div>
			<div class="page_body">
				<pre class="errormsg red"></pre>
			</div>
		</article>
	</section>
	<section id="page5" class="page_box">
		<article class="page">
			<h2 id="page5_title" class="page_title">インストールが完了<br/>しました！</h2>
			<div id="page1_body" class="page_body">
				<p>
					<strong class="green">お疲れ様でした。</strong>
					<br>
					<br>
					全てのインストール工程が完了しました。
					<br>
					インストーラーをブラウザから閉じて、インストールを完全に終了する事が出来ます。
					<br>
					<br>
					<strong>フレームワークの詳しい利用方法は、<a target="_blank" href="http://saimushi.github.io/<?php echo PROJECT_NAME; ?>/">UNICORNのWebサイト</a>を確認して下さい。</strong>
					<br>
					<br>
				</p>
			</div>
		</article>
	</section>
	<nav id="navigatior" class="navigation_box">
		<div class="navigator">
			<div class="next_step">
				<p><button id="startstep" type="button">インストールを<br>開始する</button></p>
				<p><button id="nextstep" type="button">次のステップへ</button></p>
				<p><button id="execute" type="button">実行</button></p>
				<p><button id="apply" type="button">設定</button></p>
				<p><button id="endstep" type="button ">インストールを<br>完了する</button></p>
			</div>
			<div id="navigatestep1" class="navigate_step leftfloat">
				<p class="navigate_header">STEP:1</p>
				<div class="navigate_body">システム要件のバリデーション(必須)</div>
			</div>
			<div id="navigatestep2" class="navigate_step leftfloat">
				<p class="navigate_header">STEP:2</p>
				<div class="navigate_body">フレームワークの各種パスの確認と変更(任意)</div>
			</div>
			<div id="navigatestep3" class="navigate_step leftfloat">
				<p class="navigate_header">STEP:3</p>
				<div class="navigate_body">フレームワーク管理機能のインストール(任意)</div>
			</div>
			<div id="navigatestep4" class="navigate_step leftfloat">
				<p class="navigate_header">STEP:4</p>
				<div class="navigate_body">フレームワーク管理機能のアクセス制限の設定(任意)</small></div>
			</div>
		</div>
	</nav>
	<!-- 背景を body タグで指定する代わりに、この div タグに指定する -->
	<div class="background"></div>
</body>
</html>
<?php
}
elseif(isset($_GET["a"])){
	// 以下Ajax処理
	// ステップ1システム要件のバリデーションチェック
	if(isset($_GET["step"]) && 1 === (int)$_GET["step"]) {
		// STEP1、システム要件チェック
		if(!isset($_GET["validate"])) {
			exit ("{\"error\":\"該当するシステム要件チェックがありません。\"}");
		}
		else if(1 === (int)$_GET["validate"]){
			// PHP5.2以上かどうか
			$version = (int)substr(str_replace(".", "", phpversion()), 0, 3);
			if(520 <= $version){
				exit("{\"ok\":true}");
			}
			else {
				exit("{\"ok\":false,\"error\":\"PHPバージョンが" . phpversion() . "です! \\n PHPのバージョンを5.2以上に上げて下さい。\"}");
			}
		}
		else if(2 === (int)$_GET["validate"]){
			// ファイル操作が可能かどうか、ココで調べてしまって、最後にゴミ操作をする
			// 取り敢えずこの時点のゴミは消すのを試みて置く
			@unlink(dirname(__FILE__)."/tests/test.test");
			@rmdir(dirname(__FILE__)."/tests");
			// 先ずはmkdir出来るか
			if(false === @mkdir(dirname(__FILE__)."/tests", 0644)){
				exit("{\"ok\":false,\"error\":\"mkdir('" . dirname(__FILE__) . "/tests', 0644)に失敗しました! \\n 考えられる理由: \\n 1.このファイルを実行しているディレクトリに書込権限がありません。 \\n 2.作成したtestディレクトリのパーミッションを0644に出来ません。 \\n 3.PHPを実行している権限にmkdirする権限がありません。 \\n 4.既に書き込もうとしてるディレクトリが存在しました。\"}");
			}
			// ディレクトリパーミッションの変更が出来るかどうか
			if(false === @chmod(dirname(__FILE__)."/tests", 0777)){
				exit("{\"ok\":false,\"error\":\"chmod('" . dirname(__FILE__) . "/tests', 0777)に失敗しました! \\n 考えられる理由: \\n 1.このファイルを実行しているユーザーにchmodの権限がありません。\"}");
			}
			// ファイル作成出来るか
			if (false === @touch(dirname(__FILE__)."/tests/test.test")) {
				exit("{\"ok\":false,\"error\":\"touch('" . dirname(__FILE__) . "/tests/test.test')に失敗しました! \\n 考えられる理由: \\n 1.このファイルを実行しているディレクトリに書込権限がありません。 \\n 2.PHPを実行している権限にtouchする権限がありません。\"}");
			}
			// ファイル削除出来るか
			if (false === @unlink(dirname(__FILE__)."/tests/test.test")) {
				exit("{\"ok\":false,\"error\":\"unlink('" . dirname(__FILE__) . "/tests/test.test')に失敗しました! \\n 考えられる理由: \\n 1.このファイルを実行しているディレクトリに書込権限がありません。 \\n 2.PHPを実行している権限にunlinkする権限がありません。\"}");
			}
			// ディレクトリ削除出来るか
			if (false === @rmdir(dirname(__FILE__)."/tests")) {
				exit("{\"ok\":false,\"error\":\"rmdir('" . dirname(__FILE__) . "/tests')に失敗しました! \\n 考えられる理由: \\n 1.このファイルを実行しているディレクトリに書込権限がありません。 \\n 2.'" . dirname(__FILE__) . "/tests'の中に既に何かのファイルが存在しています。 \\n 3.PHPを実行している権限にrmdirする権限がありません。\"}");
			}
			exit("{\"ok\":true}");
		}
		else if(3 === (int)$_GET["validate"]){
			// mcrypt関数が利用出来るかどうか
			if(false === function_exists("mcrypt_module_open")){
				exit("{\"ok\":false,\"error\":\"mcrypt_module_open出来ませんでした。 \\n 考えられる理由: \\n 1.PHPのコンパイルオプションに「 --with-mcrypt」が含まれて居ません。\"}");
			}
			exit("{\"ok\":true}");
		}
		else if(4 === (int)$_GET["validate"]){
			// 80番ポートが開いているかどうか
			$res = fsockopen("google.com", 80);
			if(false === $res){
				exit("{\"ok\":false,\"error\":\"fsockopen('google.com', 80)に失敗しました。 \\n 考えられる理由: \\n 1.80番ポートから外部へ出て行く通信がFirewall等の設定で正しく許可されて居ません。\"}");
			}
			// 443番ポートが開いているかどうか
			$res = fsockopen("google.com", 80);
			if(false === $res){
				exit("{\"ok\":false,\"error\":\"fsockopen('google.com', 443)に失敗しました。 \\n 考えられる理由: \\n 1.443番ポートから外部へ出て行く通信がFirewall等の設定で正しく許可されて居ません。\"}");
			}
			exit("{\"ok\":true}");
		}
	}
	// ステップ2フレームワークの各種パスの確認と変更
	else if(isset($_GET["step"]) && 2 === (int)$_GET["step"]) {
		// STEP2、FrameworkPackageとGenericPackageのパス確認
		if(isset($_GET["apply"]) && 1 === (int)$_GET["apply"]) {
			// FrameworkPackageの所在確認
			$path = $_POST["path"];
			// 送られたパスの配下にフレームワークのconfig.xmlがあるかどうかを調べる
			if(false === is_file($path."/core/config.xml")){
				// 存在を確認出来ず
				exit("{\"ok\":false,\"error\":\"指定されたパス「" . $path . "」にフレームワークを見つけられませんでした。\\n正しいパスを指定し治して、「設定」ボタンを押して下さい。\"}");
			}
			$paths = array();
			$paths[] = $path;
			// フレームワーク内の関連のパッケージの一覧を返す
			if ($handle = opendir(dirname($path))) {
				/* ディレクトリをループする際の正しい方法です */
				while (false !== ($file = readdir($handle))) {
					if(is_dir(dirname($path)."/".$file) && "." != $file && ".." != $file){
						$paths[] =  dirname($path)."/".$file;
					}
				}
				closedir($handle);
			}
			exit(json_encode(array("ok"=>true,"paths"=>$paths)));
		}
		else if(isset($_GET["apply"]) && 2 === (int)$_GET["apply"]) {
			// GenericPackageの所在確認
			$genericpath = $_POST["genericpath"];
			if(false === is_dir($genericpath)){
				// 存在を確認出来ず
				exit("{\"ok\":false,\"error\":\"指定されたパス「" . $genericpath . "」にGenericPackageを見つけられませんでした。\\n正しいパスを指定し治して、「設定」ボタンを押して下さい。\"}");
			}
			$vendorpath = $_POST["vendorpath"];
			if(false === is_dir($vendorpath)){
				// 存在を確認出来ず
				exit("{\"ok\":false,\"error\":\"指定されたパス「" . $vendorpath . "」にVendorPackageを見つけられませんでした。\\n正しいパスを指定し治して、「設定」ボタンを押して下さい。\"}");
			}
			exit("{\"ok\":true}");
		}
		else if(isset($_GET["apply"]) && 3 === (int)$_GET["apply"]) {
			@unlink(dirname(__FILE__)."/clog");
			@unlink(dirname(__FILE__)."/flog");
			@unlink(dirname(__FILE__)."/glog");
			@unlink(dirname(__FILE__)."/hlog");
			if($_POST["frameworkpath"] != $_POST["newframeworkpath"]){
				// execが実行出来るかどうかチェック
				exec("php " . __FILE__ . " check=1 2>&1", $res);
				// execのスタート時の戻り値を取っておく
				@file_put_contents(dirname(__FILE__)."/clog", $res);
				// フレームワークディレクトリの移動
				exec("php " . __FILE__ . " f=" . $_POST["frameworkpath"] . "\&nf=" . $_POST["newframeworkpath"]. " > /dev/null &");
			}
			if(10 > filesize(dirname(__FILE__)."/clog") && $_POST["genericpath"] != $_POST["newgenericpath"]){
				// execが実行出来るかどうかチェック
				exec("php " . __FILE__ . " check=1 2>&1", $res);
				// execのスタート時の戻り値を取っておく
				@file_put_contents(dirname(__FILE__)."/clog", $res);
				// GenericPackageの移動
				exec("php " . __FILE__ . " g=" . $_POST["genericpath"] . "\&ng=" . $_POST["newgenericpath"]. " > /dev/null &");
			}
			if(10 > filesize(dirname(__FILE__)."/clog") && $_POST["vendorpath"] != $_POST["newvendorpath"]){
				// execが実行出来るかどうかチェック
				exec("php " . __FILE__ . " check=1 2>&1", $res);
				// execのスタート時の戻り値を取っておく
				@file_put_contents(dirname(__FILE__)."/clog", $res);
				// VendorPackageの移動
				exec("php " . __FILE__ . " g=" . $_POST["vendorpath"] . "\&ng=" . $_POST["newvendorpath"]. " > /dev/null &");
			}
			exit("{\"ok\":true}");
		}
		else if(isset($_GET["apply"]) && 4 === (int)$_GET["apply"]) {
			if(10 <= filesize(dirname(__FILE__)."/clog")){
				// clogが10バイト以上あったらコンソール実行にエラーがあったと言う事
				$error = @file_get_contents(dirname(__FILE__)."/clog");
				if(false !== strpos($error, "dyld: Symbol not found: __cg_jpeg_resync_to_restart  Referenced from:")){
					$error .= "\n\nこの問題は作者がMacOSで実行中に直面しました。\n以下のURLを参考に解決しました。参考にしてみて下さい。\n\nhttp://symfony.jobweb.jp/?p=496#comment-12";
				}
				exit(json_encode(array("ok"=>false,"error"=>"何らかの理由で移動スクリプトのコンソール実行に失敗しています。\n以下のコンソール実行チェックログを確認し、問題を解決し、再度「設定」ボダンを押して下さい。\n\n".$error)));
			}
			// clogはもう不要
			@unlink(dirname(__FILE__)."/clog");
			$res = array("ok"=>true, "hlog"=>file_get_contents(dirname(__FILE__)."/hlog"));
			// 移動コンソール処理が今だに実行中かどうか
			if(false !== is_file(dirname(__FILE__)."/flog") || false !== is_file(dirname(__FILE__)."/glog")){
				$res["wait"] = true;
				exit(json_encode($res));
			}
			// hlogも不要
			@unlink(dirname(__FILE__)."/hlog");
			exit(json_encode($res));
		}
		else if(isset($_GET["apply"]) && 6 === (int)$_GET["apply"]) {
			// フレームワークのpackage.xmlの所在確認
			$packageXMLPath = $_POST["path"];
			if(false === is_file($packageXMLPath)){
				// 存在を確認出来ず
				exit("{\"ok\":false,\"error\":\"指定されたパス「" . $packageXMLPath . "」にpackage.xmlを見つけられませんでした。\\n正しいパスを指定し治して、「設定」ボタンを押して下さい。\"}");
			}
			// 存在が確認出来たので、XMLをそのまま読み込んで帰す
			exit(json_encode(array("ok"=>true,"hlog"=>str_replace("\t","　", file_get_contents($packageXMLPath)))));
		}
		else if(isset($_GET["apply"]) && 7 === (int)$_GET["apply"]) {
			// 設定ファイル(packeage.xmlとconfig.xml)のパス情報を書き換える
			// 現在のフレームワークのルートパス設定を取ってくる
			if(FALSE === strpos($_POST['packeagepath'], ".package.xml")){
				$confPath = dirname($_POST['packeagepath'])."/config.xml";
			}
			else {
				$confPath = dirname($_POST['packeagepath'])."/".basename($_POST['packeagepath'], ".package.xml").".config.xml";
			}
			installerlog("confPath=".$confPath);
			// 下の2つのXMLオブジェクトはこのステップ内でずっと使うよ！
			$confXML = simplexml_load_file($confPath);
			$pkgXML = simplexml_load_file($_POST['packeagepath']);
			$rootPath = (string)$confXML->ROOT_PATH;
			// 設定されているパスを設定に従ってコード上で扱える文字列に復元(デフォルトではコードで書かれているので)
			installerlog("ROOT_PATH=".$confXML->ROOT_PATH);
			$attr = $confXML->ROOT_PATH->attributes();
			installerlog(var_export($attr,true));
			// コードで設定されているかどうか
			if('true' === strtolower($attr["code"])){
				installerlog($attr["code"]);
				$rootPath = str_replace("__FILE__", "\"".$confPath."\"", $rootPath);
				// 復元
				eval("\$rootPath = ".$rootPath.";");
			}

			// 特定のディレクトリのパーミッションを書き換える
			@chmod($_POST["frameworkpath"]."/autogenerate", 0777);
			@chmod($_POST["frameworkpath"]."/automigration", 0777);

			// 以下 beforeframeworkpath、beforegenericpath、beforevendorpathでループ処理(3回固定のループ)
			$pathkeys = array("frameworkpath", "genericpath", "vendorpath");
			for($loopIdx=0; $loopIdx < 3; $loopIdx++){
				// パス設定が変わってなければ処理をスキップ
				if($_POST[$pathkeys[$loopIdx]] === $_POST["before".$pathkeys[$loopIdx]]){
					$pathCode = "exists";
					continue;
				}

				// 各種パス特定の基準パス
//				$confPath = str_replace("//", "/", $confPath);
				$rootPath = str_replace("//", "/", $rootPath);
// 				installerlog($confPath);
				installerlog($rootPath);
// 				$confPaths = explode("/", $confPath);
				$rootPaths = explode("/", $rootPath);
// 				installerlog(var_export($confPaths, true));
				installerlog(var_export($rootPaths, true));

				// 元の配置場所のパスでのROOTの相対パスを特定する
				// 「//」は「/」に戻しておく
				$frameworkPath = str_replace("//", "/", $_POST["before".$pathkeys[$loopIdx]]);
				installerlog($frameworkPath);
				// パスが一致するところまでさかのぼり、それを新たなルートパスとし、そこを基準にFrameworkのパスを設定しなおす
				$tmpPath = "/";
				for($tmpPathIdx=0, $pathIdx=1; $pathIdx <= count($rootPaths); $pathIdx++){
					// 空文字は無視
					if(isset($rootPaths[$pathIdx]) && strlen($rootPaths[$pathIdx]) > 0){
						if(0 === strpos($frameworkPath, $tmpPath.$rootPaths[$pathIdx])){
							$tmpPath .= $rootPaths[$pathIdx]."/";
							$tmpPathIdx++;
							// パスが一致したので次へ
							installerlog($tmpPath);
							continue;
						}
						else{
							// 一致しなかったので、この前までが一致パスとする
							break;
						}
					}
				}
				// 元のフレームワークのパス設定文字列特定した！
				$orgFwLinkPath = substr($frameworkPath, strlen($tmpPath));
				installerlog("orgFwLinkPath=".$orgFwLinkPath);

				// 次は新しいパスではどう設定すべきかをROOTパスから生成
				$frameworkPath = str_replace("//", "/", $_POST[$pathkeys[$loopIdx]]);
				installerlog($frameworkPath);
				// パスが一致するところまでさかのぼり、それを新たなルートパスとし、そこを基準にFrameworkのパスを設定しなおす
				$tmpPath = "/";
				for($tmpPathIdx=0, $pathIdx=1; $pathIdx <= count($rootPaths); $pathIdx++){
					// 空文字は無視
					if(isset($rootPaths[$pathIdx]) && strlen($rootPaths[$pathIdx]) > 0){
						if(0 === strpos($frameworkPath, $tmpPath.$rootPaths[$pathIdx])){
							$tmpPath .= $rootPaths[$pathIdx]."/";
							$tmpPathIdx++;
							// パスが一致したので次へ
							installerlog($tmpPath);
							continue;
						}
						else{
							// 一致しなかったので、この前までが一致パスとする
							break;
						}
					}
				}

				// config.xmlのROOTパス定義を書き換える
				if(!isset($pathCode)){
					// このループの最中にコレをやる羽目になったのはただの設計ミスです・・・
					$frameworkPaths = explode("/", $frameworkPath);
					if(strlen($frameworkPaths[count($frameworkPaths)-1]) <= 0){
						unset($frameworkPaths[count($frameworkPaths)-1]);
					}
					if(strlen($frameworkPaths[0]) <= 0){
						unset($frameworkPaths[0]);
					}
					$fwpathCnt = count($frameworkPaths);
					$depth = $fwpathCnt - $tmpPathIdx + 1;
					installerlog(var_export($frameworkPaths, true));
					installerlog(count($frameworkPaths));
					installerlog($tmpPath);
					installerlog($tmpPathIdx);
					installerlog($depth);
			
					if(1 === $depth){
						// pathが一致しているなら、rootは3つ上で固定
						$depth = 3;
					}
					// configu.xmlのROOTPATHを現在の設定で書き換え
					$pathCode = "dirname(__FILE__)";
					// depth文dirname関数を掛ける
					for($didx=0; $didx < $depth; $didx++){
						$pathCode = "dirname(".$pathCode.")";
					}
					// CDATAで追加追加
					// XXX 空にしとく
					$confXML->ROOT_PATH = "";
					$node = dom_import_simplexml($confXML->ROOT_PATH);
					$node->appendChild($node->ownerDocument->createCDATASection($pathCode.".'/'"));
					$attr = $confXML->ROOT_PATH->attributes();
					$attr = "TRUE";
					// XML文字列を再生成
					$confXML->asXML($confPath);
					installerlog($confXML->asXML());

					// このファイル上の20行目の$frameworkPathを強制的に書き換える
					$targetLineNum = 20;
					// 実行中のこのファイルとframeworkpathの差分からframeworkpathへの相対パスを生成する
					$tmp1Paths = explode('/', $frameworkPath);
					$tmp2Paths = explode('/', dirname(__FILE__));
					installerlog(var_export($tmp1Paths, TRUE));
					installerlog(var_export($tmp2Paths, TRUE));
					$basePaths = $tmp1Paths;
					$targetPath = dirname(__FILE__);
					if(count($tmp1Paths) > count($tmp2Paths)){
						$basePaths = $tmp2Paths;
						$targetPath = $frameworkPath;
					}
					$newFwTmpPath = "/";
					for($newFwTmpPathIdx=0, $pathIdx=1; $pathIdx <= count($basePaths); $pathIdx++){
						// 空文字は無視
						if(isset($basePaths[$pathIdx]) && strlen($basePaths[$pathIdx]) > 0){
							if(0 === strpos($targetPath, $newFwTmpPath.$basePaths[$pathIdx])){
								$newFwTmpPath .= $basePaths[$pathIdx]."/";
								$newFwTmpPathIdx++;
								// パスが一致したので次へ
								installerlog($newFwTmpPath);
								continue;
							}
							else{
								// 一致しなかったので、この前までが一致パスとする
								break;
							}
						}
					}
					$newfwLinkPath = substr($targetPath, strlen($newFwTmpPath));
					installerlog("newfwLinkPath=".$newfwLinkPath);
					installerlog(count($tmp2Paths) - $newFwTmpPathIdx);
					$pathCode = "dirname(__FILE__)";
					// depth分dirname関数を掛ける
					for($didx=0; $didx < count($tmp2Paths) - $newFwTmpPathIdx - 1; $didx++){
						$pathCode = "dirname(".$pathCode.")";
					}
					$replaceStr = "\$frameworkPath = ".$pathCode.".\"/".$newfwLinkPath."\";";
					$handle = fopen(__FILE__, 'r');
					if(FALSE === $handle){
						exit(json_encode(array("ok"=>false,"error"=>"何らかの理由でこのファイルのfopen(r:読込・ポインタはファイル先頭)に失敗しています。 \n 考えられる理由: \n このファイルへの書込権限が設定されていません。\n".$error)));
					}
					$readLine = 0;
					$file="";
					while (($buffer = fgets($handle, 4096)) !== false) {
						$readLine++;
						if($targetLineNum === $readLine){
							// 置換処理
							$file .= $replaceStr . PHP_EOL;
						}
						else {
							$file .= $buffer;
						}
					}
					fclose($handle);
					file_put_contents(__FILE__, $file);
				}

				// package.xmlのdefault->link句を書き換える
				$fwLinkPath = substr($frameworkPath, strlen($tmpPath));
				installerlog("fwLinkPath=".$fwLinkPath);
				installerlog("orgFwLinkPath=".$orgFwLinkPath);
				if(isset($pkgXML->default) && isset($pkgXML->default->link)){
					foreach(get_object_vars($pkgXML->default) as $key => $tmp){
						for($linkIdx=0; $linkIdx < count($tmp); $linkIdx++){
							installerlog("link=".$pkgXML->default->{$key}[$linkIdx]);
							if(false !== strpos($pkgXML->default->{$key}[$linkIdx], $orgFwLinkPath)){
								// パス書き換え
								$pkgXML->default->{$key}[$linkIdx] = str_replace($orgFwLinkPath, $fwLinkPath, $pkgXML->default->{$key}[$linkIdx]);
							}
						}
					}
				}
			}
			// 再設定されたpackage.xmlを生成
			installerlog($pkgXML->asXML($_POST['packeagepath']));
			$pkgXML->asXML($_POST['packeagepath']);
			// 書き換えた新しいXMLをそのまま読み込んで帰す
			exit(json_encode(array("ok"=>true,"hlog"=>str_replace("\t","　", file_get_contents($_POST['packeagepath'])))));
		}
	}
	// ステップ3フレームワークマネージャーのインストール
	else if(isset($_GET["step"]) && 3 === (int)$_GET["step"]) {
		// フレームワークマネージャーのパス確認
		if(isset($_GET["apply"]) && 1 === (int)$_GET["apply"]) {
			$path = $_POST["fwmpath"];
			// 送られたパスの配下にフレームワークマネージャーがあるかどうかを調べる
			if(false === is_file($path."/core/FrameworkManager.config.xml")){
				// 存在を確認出来ず
				exit("{\"ok\":false,\"error\":\"指定されたパス「" . $path . "」にフレームワークマネージャーを見つけられませんでした。\\n正しいパスを指定し治して、「設定」ボタンを押して下さい。\"}");
			}
			// 見つかったら、4設定目で使うcreatedb文を今の内に取っておく
			exit(json_encode(array("ok"=>true,"createdbsql"=>file_get_contents($path."/core/createdb.sql"))));
		}
		else if(isset($_GET["apply"]) && 2 === (int)$_GET["apply"]) {
			@unlink(dirname(__FILE__)."/clog");
			@unlink(dirname(__FILE__)."/flog");
			@unlink(dirname(__FILE__)."/glog");
			@unlink(dirname(__FILE__)."/hlog");
			if(10 > filesize(dirname(__FILE__)."/clog") && $_POST["fwmpath"] != $_POST["newfwmpath"]){
				// execが実行出来るかどうかチェック
				exec("php " . __FILE__ . " check=1 2>&1", $res);
				// execのスタート時の戻り値を取っておく
				@file_put_contents(dirname(__FILE__)."/clog", $res);
				// フレームワークディレクトリの移動
				exec("php " . __FILE__ . " f=" . $_POST["fwmpath"] . "\&nf=" . $_POST["newfwmpath"]. " > /dev/null &");
			}
			exit("{\"ok\":true}");
		}
		else if(isset($_GET["apply"]) && 3 === (int)$_GET["apply"]) {
			if(10 <= filesize(dirname(__FILE__)."/clog")){
				// clogが10バイト以上あったらコンソール実行にエラーがあったと言う事
				$error = @file_get_contents(dirname(__FILE__)."/clog");
				if(false !== strpos($error, "dyld: Symbol not found: __cg_jpeg_resync_to_restart  Referenced from:")){
					$error .= "\n\nこの問題は作者がMacOSで実行中に直面しました。\n以下のURLを参考に解決しました。参考にしてみて下さい。\n\nhttp://symfony.jobweb.jp/?p=496#comment-12";
				}
				exit(json_encode(array("ok"=>false,"error"=>"何らかの理由で移動スクリプトのコンソール実行に失敗しています。\n以下のコンソール実行チェックログを確認し、問題を解決し、再度「設定」ボダンを押して下さい。\n\n".$error)));
			}
			// clogはもう不要
			@unlink(dirname(__FILE__)."/clog");
			$res = array("ok"=>true, "hlog"=>file_get_contents(dirname(__FILE__)."/hlog"));
			// 移動コンソール処理が今だに実行中かどうか
			if(false !== is_file(dirname(__FILE__)."/flog") || false !== is_file(dirname(__FILE__)."/glog")){
				$res["wait"] = true;
				exit(json_encode($res));
			}
			// hlogも不要
			@unlink(dirname(__FILE__)."/hlog");

			// このファイル上の26行目の$fwmgrPathを強制的に書き換える
			$targetLineNum = 26;
			// 実行中のこのファイル上のFrameworkManagerのパス設定を書き換える
			$tmp1Paths = explode('/', $_POST["newfwmpath"]);
			$tmp2Paths = explode('/', dirname(__FILE__));
			installerlog(var_export($tmp1Paths, TRUE));
			installerlog(var_export($tmp2Paths, TRUE));
			$basePaths = $tmp1Paths;
			$targetPath = dirname(__FILE__);
			if(count($tmp1Paths) > count($tmp2Paths)){
				$basePaths = $tmp2Paths;
				$targetPath = $_POST["newfwmpath"];
			}
			$newFwmTmpPath = "/";
			for($newFwmTmpPathIdx=0, $pathIdx=1; $pathIdx <= count($basePaths); $pathIdx++){
				// 空文字は無視
				if(isset($basePaths[$pathIdx]) && strlen($basePaths[$pathIdx]) > 0){
					if(0 === strpos($targetPath, $newFwmTmpPath.$basePaths[$pathIdx])){
						$newFwmTmpPath .= $basePaths[$pathIdx]."/";
						$newFwmTmpPathIdx++;
						// パスが一致したので次へ
						installerlog($newFwmTmpPath);
						continue;
					}
					else{
						// 一致しなかったので、この前までが一致パスとする
						break;
					}
				}
			}
			$newfwmLinkPath = substr($targetPath, strlen($newFwmTmpPath));

			// 特定のディレクトリのパーミッションを書き換える
			@chmod($newfwmLinkPath."/autogenerate", 0777);
			@chmod($newfwmLinkPath."/automigration", 0777);

			installerlog("newfwmLinkPath=".$newfwmLinkPath);
			installerlog(count($tmp2Paths) - $newFwmTmpPathIdx);
			$pathCode = "dirname(__FILE__)";
			// depth分dirname関数を掛ける
			for($didx=0; $didx < count($tmp2Paths) - $newFwmTmpPathIdx - 1; $didx++){
				$pathCode = "dirname(".$pathCode.")";
			}
			$replaceStr = "\$fwmgrPath = ".$pathCode.".\"/".$newfwmLinkPath."\";";
			installerlog("replaceStr=".$replaceStr);
			$handle = fopen(__FILE__, 'r');
			if(FALSE === $handle){
				exit(json_encode(array("ok"=>false,"error"=>"何らかの理由でこのファイルのfopen(r:読込・ポインタはファイル先頭)に失敗しています。 \n 考えられる理由: \n このファイルへの書込権限が設定されていません。\n".$error)));
			}
			$readLine = 0;
			$file="";
			while (($buffer = fgets($handle, 4096)) !== false) {
				$readLine++;
				if($targetLineNum === $readLine){
					// 置換処理
					$file .= $replaceStr . PHP_EOL;
				}
				else {
					$file .= $buffer;
				}
			}
			fclose($handle);
			file_put_contents(__FILE__, $file);

			// フレームワークマネージャーの移動の正常終了
			exit(json_encode($res));
		}
		else if(isset($_GET["apply"]) && 5 === (int)$_GET["apply"]) {
			$path = $_POST["fwmpath"];
			// MySQLに接続してcreate database文とgrant文を実行する
			if(false === is_file($path."/core/createdb.sql")){
				// create文が見つからないエラー！
				exit("{\"ok\":false,\"error\":\"createdb.sqlを見つけられませんでした。\"}");
			}
			$createdb = file_get_contents($path."/core/createdb.sql");
			$connect = mysqli_connect("localhost", $_POST["mysqluser"], $_POST["mysqlpass"], "mysql");
			if (!$connect) {
				exit("{\"ok\":false,\"error\":\"MySQLサーバーへの接続に失敗しました。 \\n " . mysqli_connect_error() . " \\n 考えられる理由: \\n 1.指定されたユーザー・パスワードが間違っています。 \\n 2.指定されたユーザー・パスワードにmysqlデータベースへのアクセス権限が無いかも知れません。 \\n 3.インストーラーを実行しているサーバーにMySQLサーバーがインストールされていません。\"}");
			}
			if (!mysqli_set_charset($connect, "utf8")) {
				exit("{\"ok\":false,\"error\":\"mysqli_set_charsetの実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にmysqli_set_charsetの実行権限が無いかも知れません。\"}");
			}
			if (!mysqli_multi_query($connect, $createdb)) {
				exit("{\"ok\":false,\"error\":\"create文の実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にcreate databaseの実行権限が無いかも知れません。 \\n 2.指定された設定情報にgrantの実行権限が無いかも知れません。\"}");
			}
			mysqli_close($connect);
			exit("{\"ok\":true}");
		}
		else if(isset($_GET["apply"]) && 6 === (int)$_GET["apply"]) {
			// MySQL接続テスト
			$path = $_POST["fwmpath"];
			$connect = mysqli_connect("localhost", $_POST["fwmdbuser"], $_POST["fwmdbpass"], $_POST["fwmdb"]);
			if (!$connect) {
				exit("{\"ok\":false,\"error\":\"フレームワークマネージャー用のデータベースへの接続に失敗しました。 \\n " . mysqli_connect_error() . " \\n 考えられる理由: \\n 1.指定されたユーザー・パスワード・データベース名が間違っています。 \\n 2.指定されたユーザー・パスワードにフレームワークマネージャー用のデータベースへのアクセス権限が無いかも知れません。\"}");
			}
			if (!mysqli_set_charset($connect, "utf8")) {
				exit("{\"ok\":false,\"error\":\"mysqli_set_charsetの実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にmysqli_set_charsetの実行権限が無いかも知れません。\"}");
			}
			// create tableの実行テスト
			if (!mysqli_query($connect, "CREATE TABLE IF NOT EXISTS `test` (`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PKey', PRIMARY KEY(`id`))")) {
				exit("{\"ok\":false,\"error\":\"create文の実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にcreate tableの実行権限が無いかも知れません。\"}");
			}
			// alter tableの実行テスト
			if (!mysqli_query($connect, "ALTER TABLE `test` MODIFY COLUMN `id` int(10)")) {
				exit("{\"ok\":false,\"error\":\"alter文の実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にalter tableの実行権限が無いかも知れません。\"}");
			}
			// drop tableの実行テスト
			if (!mysqli_query($connect, "DROP TABLE `test`")) {
				exit("{\"ok\":false,\"error\":\"drop文の実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にdrop tableの実行権限が無いかも知れません。\"}");
			}
			mysqli_close($connect);
			// 全ての接続テストにクリアしたので、受け取ったデータベース接続情報で、フレームワークマネージャーのDB接続情報設定を書き換える
			$fwmConfXMLPath = $path."/core/FrameworkManager.config.xml";
			if(false === is_file($fwmConfXMLPath)){
				// create文が見つからないエラー！
				exit("{\"ok\":false,\"error\":\"" . $path."/core/FrameworkManager.config.xmlを見つけられませんでした。\"}");
			}
			$fwmConfXML = simplexml_load_file($fwmConfXMLPath);
			$fwmConfXML->FrameworkManager->DB_DSN = "mysqli://" . $_POST["fwmdbuser"] . ":" . $_POST["fwmdbpass"] . "@localhost/" . $_POST["fwmdb"];
			// XML文字列を再生成
			$fwmConfXML->asXML($fwmConfXMLPath);
			installerlog($fwmConfXML->asXML());
			// createtable文も一緒に返す
			exit(json_encode(array("ok"=>true,"createtablesql"=>file_get_contents($path."/core/createtable.sql"))));
		}
		else if(isset($_GET["apply"]) && 7 === (int)$_GET["apply"]) {
			$path = $_POST["fwmpath"];
			// MySQLに接続してcreate table文とinsert文を実行する
			if(false === is_file($path."/core/createtable.sql")){
				// create文が見つからないエラー！
				exit("{\"ok\":false,\"error\":\"createtable.sqlを見つけられませんでした。\"}");
			}
			$createtable = file_get_contents($path."/core/createtable.sql");
			$connect = mysqli_connect("localhost", $_POST["fwmdbuser"], $_POST["fwmdbpass"], $_POST["fwmdb"]);
			if (!$connect) {
				exit("{\"ok\":false,\"error\":\"MySQLサーバーへの接続に失敗しました。 \\n " . mysqli_connect_error() . " \\n 考えられる理由: \\n 1.指定されたユーザー・パスワードが間違っています。 \\n 2.指定されたユーザー・パスワードにmysqlデータベースへのアクセス権限が無いかも知れません。 \\n 3.インストーラーを実行しているサーバーにMySQLサーバーがインストールされていません。\"}");
			}
			if (!mysqli_set_charset($connect, "utf8")) {
				exit("{\"ok\":false,\"error\":\"mysqli_set_charsetの実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にmysqli_set_charsetの実行権限が無いかも知れません。\"}");
			}
			if (!mysqli_multi_query($connect, $createtable)) {
				exit("{\"ok\":false,\"error\":\"create文の実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にcreate tableの実行権限が無いかも知れません。 \\n 2.指定された設定情報にinsertの実行権限が無いかも知れません。\"}");
			}
			mysqli_close($connect);

			// 次のステップの為にフレームワークマネージャーの公開ディレクトリパス情報を返す
			exit("{\"ok\":true, \"fwmdocpath\":\"" . $path."/template/managedocs" . "\"}");
		}
		else if(isset($_GET["apply"]) && 8 === (int)$_GET["apply"]) {
			// MySQLに接続してユーザーテーブルにレコードをインサートする
			$connect = mysqli_connect("localhost", $_POST["fwmdbuser"], $_POST["fwmdbpass"], $_POST["fwmdb"]);
			installerlog("dsn=mysqli://".$_POST["fwmdbuser"].":".$_POST["fwmdbpass"]."@localhost/".$_POST["fwmdb"]);
			if (!$connect) {
				exit("{\"ok\":false,\"error\":\"MySQLサーバーへの接続に失敗しました。 \\n " . mysqli_connect_error() . " \\n 考えられる理由: \\n 1.指定されたユーザー・パスワードが間違っています。 \\n 2.指定されたユーザー・パスワードにmysqlデータベースへのアクセス権限が無いかも知れません。 \\n 3.インストーラーを実行しているサーバーにMySQLサーバーがインストールされていません。\"}");
			}
			if (!mysqli_set_charset($connect, "utf8")) {
				exit("{\"ok\":false,\"error\":\"mysqli_set_charsetの実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にmysqli_set_charsetの実行権限が無いかも知れません。\"}");
			}
			$username = $_POST['fwmusername'];
			$usermail = $_POST['fwmusermail'];
			$userpassHash = hash("sha256", $_POST['fwmuserpass'], FALSE);
			// 先ずは既にユーザーレコードが無いかどうか調べる
			$selectSQL = "SELECT * FROM `user` WHERE `mail` =  '".$usermail."' AND `pass` = '".$userpassHash."'";
			installerlog($selectSQL);
			$result = mysqli_query($connect, $selectSQL);
			if (!$result) {
				exit("{\"ok\":false,\"error\":\"SQL文の実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にselectの実行権限が無いかも知れません。 \\n 2.指定された設定情報にupdateの実行権限が無いかも知れません。 \\n 3.指定された設定情報にinsertの実行権限が無いかも知れません。\"}");
			}
			if(NULL === mysqli_fetch_array($result)){
				// インサートする
				$insertSQL = "INSERT INTO `user` (`name`, `mail`, `pass`) VALUES ('".$username."', '".$usermail."', '".$userpassHash."')";
				installerlog($insertSQL);
				if (!mysqli_query($connect, $insertSQL)) {
					exit("{\"ok\":false,\"error\":\"SQL文の実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にselectの実行権限が無いかも知れません。 \\n 2.指定された設定情報にupdateの実行権限が無いかも知れません。 \\n 3.指定された設定情報にinsertの実行権限が無いかも知れません。\"}");
				}
			}
			else {
				// アップデートする
				$updateSQL = "UPDATE `user` SET `name` = '".$username."', `mail` = '".$usermail."', `pass` = '".$userpassHash."' WHERE `mail` =  '".$usermail."' AND `pass` = '".$userpassHash."'";
				installerlog($updateSQL);
				if (!mysqli_query($connect, $updateSQL)) {
					exit("{\"ok\":false,\"error\":\"SQL文の実行に失敗しました。 \\n " . mysqli_error($connect) . " \\n 考えられる理由: \\n 1.指定された設定情報にselectの実行権限が無いかも知れません。 \\n 2.指定された設定情報にupdateの実行権限が無いかも知れません。 \\n 3.指定された設定情報にinsertの実行権限が無いかも知れません。\"}");
				}
			}
			mysqli_close($connect);
			exit("{\"ok\":true}");
		}
		else if(isset($_GET["apply"]) && 9 === (int)$_GET["apply"]) {
			if(false === is_file($_POST["fwmdocpath"]."/index.php")){
				// 存在を確認出来ず
				exit("{\"ok\":false,\"error\":\"指定されたパス「" . $path . "」にフレームワークマネージャーの公開ディレクトリを見つけられませんでした。\\n正しいパスを指定し治して、「設定」ボタンを押して下さい。\"}");
			}
			exit("{\"ok\":true}");
		}
		else if(isset($_GET["apply"]) && 10 === (int)$_GET["apply"]) {
			// fwmdocの移動
			@unlink(dirname(__FILE__)."/clog");
			@unlink(dirname(__FILE__)."/flog");
			@unlink(dirname(__FILE__)."/glog");
			@unlink(dirname(__FILE__)."/hlog");
			if(10 > filesize(dirname(__FILE__)."/clog") && $_POST["fwmdocpath"] != $_POST["newfwmdocpath"]){
				// execが実行出来るかどうかチェック
				exec("php " . __FILE__ . " check=1 2>&1", $res);
				// execのスタート時の戻り値を取っておく
				@file_put_contents(dirname(__FILE__)."/clog", $res);
				// フレームワークディレクトリの移動
				exec("php " . __FILE__ . " f=" . $_POST["fwmdocpath"] . "\&nf=" . $_POST["newfwmdocpath"]. " > /dev/null &");
			}
			else {
				@file_put_contents(dirname(__FILE__)."/hlog", "移動しませんでした。");
			}
			// URL文字列を移動後を想定して作成して返しといて上げる
			installerlog(str_replace("//", "/", str_replace("//", "/", $_POST["fwmbaseurl"]."/".basename($_POST["newfwmdocpath"])."/")));
			exit("{\"ok\":true,\"fwmurl\":\"" . str_replace(":///", "://",str_replace(":/", "://", str_replace("//", "/", str_replace("//", "/", $_POST["fwmbaseurl"]."/".basename($_POST["newfwmdocpath"])."/")))) . "\"}");
		}
		else if(isset($_GET["apply"]) && 11 === (int)$_GET["apply"]) {
			if(!isset($_POST["fwmpath"])){
				// 存在を確認出来ず
				exit("{\"ok\":false,\"error\":\"指定されたパス「" . $_POST["fwmpath"] . "」にフレームワークを見つけられませんでした。\\n正しいパスを指定し治して、「設定」ボタンを押して下さい。\"}");
			}
			$fwmConfXMLPath = $_POST["fwmpath"]."/core/FrameworkManager.config.xml";;
			if(!is_file($fwmConfXMLPath)){
				// 存在を確認出来ず
				exit("{\"ok\":false,\"error\":\"指定されたパス「" . $fwmConfXMLPath . "」にフレームワークを見つけられませんでした。\\n正しいパスを指定し治して、「設定」ボタンを押して下さい。\"}");
			}
			$fwmConfXML = simplexml_load_file($fwmConfXMLPath);

			if(10 <= filesize(dirname(__FILE__)."/clog")){
				// clogが10バイト以上あったらコンソール実行にエラーがあったと言う事
				$error = @file_get_contents(dirname(__FILE__)."/clog");
				if(false !== strpos($error, "dyld: Symbol not found: __cg_jpeg_resync_to_restart  Referenced from:")){
					$error .= "\n\nこの問題は作者がMacOSで実行中に直面しました。\n以下のURLを参考に解決しました。参考にしてみて下さい。\n\nhttp://symfony.jobweb.jp/?p=496#comment-12";
				}
				exit(json_encode(array("ok"=>false,"error"=>"何らかの理由で移動スクリプトのコンソール実行に失敗しています。\n以下のコンソール実行チェックログを確認し、問題を解決し、再度「設定」ボダンを押して下さい。\n\n".$error)));
			}
			// clogはもう不要
			@unlink(dirname(__FILE__)."/clog");
			$res = array("ok"=>true, "hlog"=>file_get_contents(dirname(__FILE__)."/hlog"));
			// 移動コンソール処理が今だに実行中かどうか
			if(false !== is_file(dirname(__FILE__)."/flog") || false !== is_file(dirname(__FILE__)."/glog")) {
				$res["wait"] = true;
				exit(json_encode($res));
			}
			// hlogも不要
			@unlink(dirname(__FILE__)."/hlog");

			if($_POST["fwmdocpath"] != $_POST["newfwmdocpath"]){
				// 移動が無事に成功したのでindex.phpの中のフレームワークパスなどを正しく設定しなおして上げる
				// 4行目、フレームワークマネージャーのROOTディレクトリの名前を特定して上げる
				$fwmpkgName = basename($fwmgrPath);
				$targetLineNum1 = 4;
				// 5行目、フレームワークパスの特定処理
				$targetLineNum2 = 5;
				// $fwmdocpath中のこのファイル上のFrameworkManagerのパス設定を書き換える
				// パスの文字列形式を揃える
				$tmp1Path = str_replace("//", "/", $frameworkPath."/");
				$tmp2Path = str_replace("//", "/",$_POST["newfwmdocpath"]."/");
				$tmp1Paths = explode('/', $tmp1Path);
				$tmp2Paths = explode('/', $tmp2Path);
				unset($tmp1Paths[count($tmp1Paths)-1]);
				unset($tmp2Paths[count($tmp2Paths)-1]);
				$tmp1Path = implode('/', $tmp1Paths);
				$tmp2Path = implode('/', $tmp2Paths);
				installerlog(var_export($tmp1Paths, TRUE));
				installerlog(var_export($tmp2Paths, TRUE));
				$basePaths = $tmp1Paths;
				$targetPath = $tmp1Path;
				if(count($tmp1Paths) > count($tmp2Paths)){
					$basePaths = $tmp2Paths;
				}
				$newFwmTmpPath = "/";
				for($newFwmTmpPathIdx=0, $pathIdx=1; $pathIdx <= count($basePaths); $pathIdx++){
					// 空文字は無視
					if(isset($basePaths[$pathIdx]) && strlen($basePaths[$pathIdx]) > 0){
						if(0 === strpos($targetPath, $newFwmTmpPath.$basePaths[$pathIdx])){
							$newFwmTmpPath .= $basePaths[$pathIdx]."/";
							$newFwmTmpPathIdx++;
							// パスが一致したので次へ
							installerlog($newFwmTmpPath);
							continue;
						}
						else {
							// 一致しなかったので、この前までが一致パスとする
							break;
						}
					}
				}
				$newfwmLinkPath = substr($targetPath, strlen($newFwmTmpPath));
				installerlog("newfwmLinkPath=".$newfwmLinkPath);
				installerlog(count($tmp2Paths) - $newFwmTmpPathIdx);
				$pathCode = "dirname(__FILE__)";
				// depth分dirname関数を掛ける
				for($didx=0; $didx < count($tmp2Paths) - $newFwmTmpPathIdx - 1; $didx++){
					$pathCode = "dirname(" . $pathCode . ")";
				}
				$replaceStr = "\$fwpath = " . $pathCode . ".\"/" . $newfwmLinkPath . "\";";
				installerlog("replaceStr=".$replaceStr);
				$handle = fopen($_POST["newfwmdocpath"]."/index.php", 'r');
				if(FALSE === $handle){
					exit(json_encode(array("ok"=>false,"error"=>"何らかの理由でこのファイルのfopen(r:読込・ポインタはファイル先頭)に失敗しています。 \n 考えられる理由: \n このファイルへの書込権限が設定されていません。\n".$error)));
				}
				$readLine = 0;
				$file="";
				while (($buffer = fgets($handle, 4096)) !== false) {
					$readLine++;
					if($targetLineNum1 === $readLine){
						$file .= "\$fwmpkgName = \"" . $fwmpkgName . "\";" . PHP_EOL;
					}
					else if($targetLineNum2 === $readLine) {
						// 置換処理
						$file .= $replaceStr . PHP_EOL;
					}
					else {
						$file .= $buffer;
					}
				}
				fclose($handle);
				file_put_contents($_POST["newfwmdocpath"]."/index.php", $file);
			}

			// 移動が終わったらベースURLを書き出す
			$fwmConfXML->FrameworkManager->BASE_URL = array();
			$fwmConfXML->FrameworkManager->BASE_URL[0] = $_POST["fwmurl"];
			$fwmConfXML->FrameworkManager->BASE_URL[0]->addAttribute("stage", "local");
			$fwmConfXML->FrameworkManager->BASE_URL[1] = $_POST["fwmurl"];
			$fwmConfXML->FrameworkManager->BASE_URL[1]->addAttribute("stage", "test");
			$fwmConfXML->FrameworkManager->BASE_URL[2] = $_POST["fwmurl"];
			$fwmConfXML->asXML($fwmConfXMLPath);

			// フレームワークマネージャーの移動の正常終了
			exit(json_encode($res));
		}
	}
	// ステップ4フレームワークマネージャーのインストール
	else if(isset($_GET["step"]) && 4 === (int)$_GET["step"]) {
		// アクセス制限を設定する
		if(isset($_GET["apply"]) && 1 === (int)$_GET["apply"]) {
			installerlog(var_export($_POST,true));
			if(!isset($_POST["fwmpath"])){
				// 存在を確認出来ず
				exit("{\"ok\":false,\"error\":\"指定されたパス「" . $path . "」にフレームワークを見つけられませんでした。\\n正しいパスを指定し治して、「設定」ボタンを押して下さい。\"}");
			}
			$path = $_POST["fwmpath"]."/core/FrameworkManager.config.xml";;
			if(!is_file($path)){
				// 存在を確認出来ず
				exit("{\"ok\":false,\"error\":\"指定されたパス「" . $path . "」にフレームワークを見つけられませんでした。\\n正しいパスを指定し治して、「設定」ボタンを押して下さい。\"}");
			}
			$fwmConfXML = simplexml_load_file($path);
			$modified = false;
			if(isset($_POST["sslon"]) && true == (1 === (int)$_POST["sslon"] || true === $_POST["sslon"] || "1" === $_POST["sslon"])){
				$fwmConfXML->FrameworkManager->DENY_HTTP = true;
				$modified = true;
			}
			if(isset($_POST["ipaddress"]) &&  8 < strlen($_POST["ipaddress"])){
				$fwmConfXML->FrameworkManager->DENY_ALL_IP = $_POST["ipaddress"];
				$modified = true;
			}
			if(true === $modified){
				// XML文字列を再生成
				$fwmConfXML->asXML($path);
				installerlog($fwmConfXML->asXML());
			}
			// アクセス制限設定の正常終了
			exit("{\"ok\":true}");
		}
	}
	// ステップ4フレームワークマネージャーのインストール
	else if(isset($_GET["step"]) && 5 === (int)$_GET["step"]) {
		if(is_file(dirname(__FILE__).'/.copy')){
			// コピーされたインストーラーならまるまる削除して終了する
			dir_delete(dirname(__FILE__));
		}
		// 後始末処理の終了
		exit("{\"ok\":true}");
	}
	exit ("{\"error\":\"該当するシステム要件チェックがありません。\"}");
}
else if(null !== $argv){
	installerlog("Start CLI");
	ob_start();
	$useCLI = true;	
	$param = null;
	if(isset($argv[1])){
		parse_str($argv[1], $param);
		installerlog(var_export($param, true));
	}
	else {
		// 以下Cli処理
		echo PHP_EOL;
		echo "--- ようこそ、".PROJECT_NAME." installer江 ---".PHP_EOL;
		echo PHP_EOL;
		echo "すみません、残念ながらCLIでのインストーラーは現在提供していません。".PHP_EOL;
		echo "提供次期は未定です。".PHP_EOL;
		echo PHP_EOL;
		echo "Webブラウザベースでのインストーラーを利用して下さい。".PHP_EOL;
		echo "http://[あなたのドメイン]/[あなたがフレームワークを配置したworkspaceディレクトリパス]/installer.php".PHP_EOL;
		echo "上記URLを適宜変更し、Webブラウザからアクセスして下さい。".PHP_EOL;
		echo "あなたの望む結果がきっと得られます。".PHP_EOL;
		echo PHP_EOL;
	}
	if(isset($param["f"]) && isset($param["nf"])){
		file_put_contents(dirname(__FILE__)."/flog", date("Ymd H:i:s").PHP_EOL);
		// フレームワークの移動
		dir_move($param["f"], $param["nf"]);
		@unlink(dirname(__FILE__)."/flog");
	}
	if(isset($param["g"]) && isset($param["ng"])){
		file_put_contents(dirname(__FILE__)."/glog", date("Ymd H:i:s").PHP_EOL);
		// GenericPackageの移動
		dir_move($param["g"], $param["ng"]);
		@unlink(dirname(__FILE__)."/glog");
	}
	$output = ob_get_contents();
	ob_end_clean();
	installerlog($output);
	exit();
}

function installerlog($argMsg) {
	// DEBUGモードの時だけログ出力
	if(isset($_GET["debug"]) && true === ("1" === $_GET["debug"] || "true" === $_GET["debug"])){
		@file_put_contents(dirname(__FILE__)."/ilog", date("Ymd H:i:s").":".$argMsg.PHP_EOL, FILE_APPEND);
	}
}

function fileHandlingHistory($path) {
	global $useCLI;
	if(true === $useCLI){
		// CLIの時だけ履歴を書き込む
		@file_put_contents(dirname(__FILE__)."/hlog", $path.PHP_EOL, FILE_APPEND);
	}
}

/**
 * ディレクトリごとコピーする
 */
function dir_copy($dir_name, $new_dir) {
	fileHandlingHistory("ディレクトリの存在チェック:" . $new_dir);
	fileHandlingHistory("ディレクトリの存在チェック:" . var_export(is_dir($new_dir), true));
	if (!is_dir($new_dir)) {
		$res = mkdir($new_dir, 0755, true);
		if(!$res){
			fileHandlingHistory("ディレクトリの作成に失敗しました:" . var_export($res, true));
			@file_put_contents(dirname(__FILE__)."/clog", "ディレクトリの作成に失敗しました:" . $new_dir . ">" . var_export($res, true), FILE_APPEND);
			exit;
		}
		else {
			fileHandlingHistory("ディレクトリの作成に成功しました:" . var_export($res, true));
		}
	}
	if (is_dir($dir_name)) {
		if ($dh = opendir($dir_name)) {
			while (($file = readdir($dh)) !== false) {
				if ($file == "." || $file == "..") {
					continue;
				}
				if (is_dir($dir_name . "/" . $file)) {
					dir_copy($dir_name . "/" . $file, $new_dir . "/" . $file);
				}
				else {
					copy($dir_name . "/" . $file, $new_dir . "/" . $file);
					// ファイル移動ログ
					fileHandlingHistory("移動しました:" . $new_dir . "/" . $file);
				}
			}
			closedir($dh);
		}
	}
	return true;
}

/**
 * ディレクトリごと削除する
 */
function dir_delete($dir_name) {
	if (is_dir($dir_name)) {
		if ($dh = opendir($dir_name)) {
			while (($file = readdir($dh)) !== false) {
				if ($file == "." || $file == "..") {
					continue;
				}
				if (is_dir($dir_name . "/" . $file)) {
					dir_delete($dir_name . "/" . $file);
				}
				else {
					unlink($dir_name . "/" . $file);
					// ファイル削除ログ
					fileHandlingHistory("削除しました:" . $new_dir . "/" . $file);
				}
			}
			closedir($dh);
		}
		rmdir($dir_name);
		// ディレクトリ削除ログ
		fileHandlingHistory("削除しました:" . $dir_name);
	}
	return true;
}

/**
 * ディレクトリごと移動(コピーして削除)する
 */
function dir_move($dir_name, $new_dir) {
	if(true === dir_copy($dir_name, $new_dir)){
		// コピーに成功してから削除する
		// XXX 冗長だが敢えて
		//return dir_delete($dir_name);
		return true;
	}
	return false;
}

?>