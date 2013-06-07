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
	
	var slideopen = false;
	
	$('#helpicon').click(function(event) {
		
		$('#helptext').slideToggle(); event.stopPropagation();

		if (slideopen == false) { 
						
			slideopen = true;
			
			$('#helpimg').attr('src', "<?php echo $p; ?>styling/images/help-icon-sm-up.png")
		}
		
		else {
			
			slideopen = false;
			$('#helpimg').attr('src', "<?php echo $p; ?>styling/images/help-icon-sm.png");
		}
	})
	
	$('#helptextclick2').click(function() { $('#helptext').slideUp(); slideopen = false; $('#helpimg').attr('src', "<?php echo $p; ?>styling/images/help-icon-sm.png") })
	
	$('html').click(function(event) {	if (!$(event.target).is('#helptext') && !$(event.target).parents("#helptext").is("#helptext")) { $('#helptext').slideUp(); slideopen = false; $('#helpimg').attr('src', "<?php echo $p; ?>styling/images/help-icon-sm.png") } }) })

$(document).ready(function() { //Assign Compatibility	
	
  $("#accordion, #accordion2").accordion({ collapsible: true })
  
  $("#tabs-veh, #tabs-update").tabs({ collapsible: true }) })
  
function processtext() { //Processing Button
	
	$("#continueclick").html("Processing...")
	$("#process").val("Processing...") }
	
function getfilename() { //Get File Name
	var gotfilename = $("#submitbutton").val().replace("C:\\fakepath\\", "");
	$("#filefield").html(gotfilename);
	if (gotfilename == '') gotfilename = "Choose File";
}

</script>