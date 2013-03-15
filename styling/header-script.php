<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="<?php echo $p; ?>styling/jquery/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo $p; ?>styling/css.css">
<script src="<?php echo $p; ?>js/jquery-1.9.1.js"></script>
<script src="<?php echo $p; ?>js/jquery-ui.js"></script>
<script src="<?php echo $p; ?>styling/sticky/jquery.sticky.js"></script>
<script>

$(document).ready(function() { //Sticky
	
	$("nav").sticky({topSpacing:0})
	
	$("#reloadpos").sticky({topSpacing:6})

	$("#helpicon").sticky({topSpacing:6})

	$("#tabs-list").sticky({topSpacing:30})
	
	/*$(".helpbar").addClass("unhide");*/ })

$(document).ready(function() { //Help menu
	
	$('#helpicon').click(function(event) { $('#helptext').slideToggle(); event.stopPropagation() })
	
	$('#helptextclick2').click(function() { $('#helptext').slideToggle() })
	
	$('html').click(function(event) {	if (!$(event.target).is('#helptext') && !$(event.target).parents("#helptext").is("#helptext")) { $('#helptext').slideUp() } }) })

$("#accordion").accordion()

</script>