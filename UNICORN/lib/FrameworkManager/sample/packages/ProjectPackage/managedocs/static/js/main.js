$(document).ready(function() {
	// モバイル専用の処理
	if (640 >= $(window).width()) {
		var menuShowed = false;
		// モバイル扱いとして、メニューをデフォルトhiddenに
		$("#contents .leftbox ul").hide();
		$("#contents .leftbox p").click(function() {
			if (false == menuShowed) {
				menuShowed = true;
				$("#contents .leftbox ul").show();
			} else {
				menuShowed = false;
				$("#contents .leftbox ul").hide();
			}
		});
	}
});
