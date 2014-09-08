var crudEnabled = false;
$(document).ready(function() {
	// ログインユーザーの名前を取得
	$.ajax({
		url: "api/me/user.json",
		dataType: "json",
		cache: false,
	}).done(function(json) {
		$("#username").text(json[0].name);
	});
	if(true != crudEnabled){
		// プロジェクトの一覧を取得
		$.ajax({
			url: "api/project.json",
			dataType: "json",
			cache: false,
		}).done(function(json) {
			var projectdombase = $("#projectlist .project").parent().html();
			var projectlist = "";
			for(var idx=0; idx < json.length; idx++){
				projectlist += projectdombase.replace("project name", json[idx]).replace("_project_", json[idx]);
			}
			$("#projectlist").html(projectlist);
		});
	}
	// モバイル専用の処理
	if (640 >= $(window).width()) {
		var baseHeight = 0;
		var menuShowed = false;
		// モバイル扱いとして、メニューをデフォルトhiddenに
		$("#contents .leftbox ul").hide();
		baseHeight = $("#contents.container").height();
		$("#contents .leftbox p").click(function() {
			if (false == menuShowed) {
				menuShowed = true;
				$("#contents .leftbox ul").show();
				$("#contents.container").height(baseHeight+130);
			} else {
				menuShowed = false;
				$("#contents .leftbox ul").hide();
				$("#contents.container").height(baseHeight);
			}
		});
	}
});
