// crud.jsを動かす為の前準備のDOM操作を担う
var crudEnabled = true;
$(document).ready(function() {
	if(true == crudEnabled){
		$("#contents .leftbox p").text("TABLE LIST");
		$("#projectlist").remove();
		$("#contents .leftbox").append("<div id=\"tablelist\">").ready(function(){
			crud("api/", function(mode){
				// crudJS終了後に処理をしたい場合はココに記述
				//alert('crudend! ' + mode);
				if("tablelist" == mode){
					$("#tablelist h2").remove();
					if (640 >= $(window).width()) {
						// モバイルは標準では隠しておく
						$("#tablelist ul").hide();
					}
				}
				if("submit" == mode){
					// 元の一覧にリダイレクトする
					if($(".list-link a").attr("href").length > 0){
						location.href = $(".list-link a").attr("href");
					}
				}
			});
		});
	}
});

