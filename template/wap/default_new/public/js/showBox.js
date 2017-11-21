
function showBox(str) {
	$(".motify").css("opacity",0.9);
	$(".motify").fadeIn("slow");
	$(".motify-inner").text(str);
	setTimeout(function() {
		$(".motify").fadeOut("slow");
	}, 2000);
}