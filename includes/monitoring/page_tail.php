</div>

<script>

	$(function(){
		$('.loading').hide();
		$('#menu-toggle').hide();
		$('#allcontent').show();
		$("#menu-toggle").removeClass("glyphicon glyphicon-list");
		$("#menu-toggle").addClass("glyphicon glyphicon-circle-arrow-right");
		$('#tmenu').click(function(){
			$("#wrapper").toggleClass("active");
			if($("#menu-toggle").hasClass("glyphicon-circle-arrow-right")){
				$("#menu-toggle").removeClass("glyphicon-circle-arrow-right");
				$("#menu-toggle").addClass("glyphicon-circle-arrow-left");
			} else {
				$("#menu-toggle").removeClass("glyphicon-circle-arrow-left");
				$("#menu-toggle").addClass("glyphicon-circle-arrow-right");
			}
		});

		function toggleMenu(){
			if($('#sidebar-wrapper').css('left') == '0px'){
				$('#wrapper').css({"padding-left" : "0px"});
				$('#sidebar-wrapper').css({"left" : "250px"});
				$('#wrapper.active').css({  "position": "relative","left": "0","transition": "left 2s"});
				$('#wrapper.active #sidebar-wrapper').css({  "left": "0","width": "0","transition": "all 0.4s ease 0s"});
				//$('.inset').css({"padding": "15"});

			} else {
				$('#wrapper').css({"padding-left" : "0"});
				$('#sidebar-wrapper').css({"left" : "0"});
				$('#wrapper.active').css({  "position": "relative","left": "250","transition": "left 2s"});
				$('#wrapper.active #sidebar-wrapper').css({  "left": "250","width": "250","transition": "all 0.4s ease 0s"});

				//	$('.inset').css({"padding": "15"});
			}
			/*
			if($('body').width() > 750){
				$('#page-content-wrapper').css({"opacity" : "1"});
			} else {
				if($('#sidebar-wrapper').css('left') == '0px'){
					$('#page-content-wrapper').css({"opacity" : "0.1"});
				}else {
					$('#page-content-wrapper').css({"opacity" : "1"});
				}
			}*/

		}
		$('#page-content-wrapper').click(function(){
			if($('#sidebar-wrapper').css('left') != '0px'){
				toggleMenu();
			}
		});
		$('#navhider').click(function(){

			toggleMenu();

		});
		$("#menu-toggle").click(function(e) {
			e.preventDefault();
			toggleMenu();
			if($("#menu-toggle").hasClass("glyphicon-circle-arrow-right")){
				$("#menu-toggle").removeClass("glyphicon-circle-arrow-right");
				$("#menu-toggle").addClass("glyphicon-circle-arrow-left");

			} else {
				$("#menu-toggle").removeClass("glyphicon-circle-arrow-left");
				$("#menu-toggle").addClass("glyphicon-circle-arrow-right");
			}
		});
		// logout
		$("#logout").click(function(){
			// remove the current_id
			localStorage.removeItem("current_id");
			location.href="../logout.php";
		});
		$("#accordion").on('shown.bs.collapse', function () {
			var active = $("#accordion .in").attr('id');
			$.cookie('activeAccordionGroup', active);
		});
		$("#accordion").on('hidden.bs.collapse', function () {
			$.removeCookie('activeAccordionGroup');
		});
		var last = $.cookie('activeAccordionGroup');
		if (last != null) {

			$("#accordion .panel-collapse").removeClass('in');
			$("#" + last).addClass("in");
		}



		if($(window).width() <= 780){
			$('.inset a').attr('data-toggle','tooltip');
			$('.inset a').attr('data-container','body');
			$('.inset a').attr('data-placement','bottom');
			$('.inset button').attr('data-toggle','tooltip');
			$('.inset button').attr('data-container','body');
			$('.inset button').attr('data-placement','bottom');
			$('[data-toggle="tooltip"]').tooltip();
		}

	});
</script>
</body>
</html>