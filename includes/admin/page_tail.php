
</div>
</div>

<script>
	$(document).ready(function () {
		var nextPage ='index.php';
		if(localStorage['lastPage']){
			 nextPage = localStorage['lastPage'];
		}
		$('#conPage').attr('src',nextPage);

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
			// remove the current_id
			localStorage.removeItem("current_id");
			localStorage.removeItem("lastPage");

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

		if(localStorage['terminal_id'] == 0) {
			$('#mainposnav').hide();
			$('#mainposlink').attr('href','#');
		}
		getCountShouts();
		function getCountShouts(){

				$.ajax({
					url: "../ajax/ajax_count_shouts.php",
					type:"POST",
					data:{},
					success: function(data){
						localStorage["count_shouts"] = data;
						var last_shout = localStorage['count_shouts_last'];
						var pending = parseInt(data) - parseInt(last_shout);
						if(!pending) pending = 0;
						$('#shoutcount').html(pending);
					}
				});
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
		$('.navPage').click(function(){
			var loc = $(this).attr('data-loc');
			toggleMenu();
			localStorage['lastPage'] = loc;
			console.log(loc);
			$("#conPage").attr("src", loc);
		});




		$('#conPage').load(function (e) {
			 var iframe = $("#conPage")[0];

			if($('#sidebar-wrapper').css('left') != '0px'){
				toggleMenu();
			}
			try {
				var ifTitle = iframe.contentDocument.title;
				$('#errorIframe').hide();
				$('#conPage').contents().find('body').click(function(){
					if($('#sidebar-wrapper').css('left') != '0px'){
						toggleMenu();
					}
				});
			}
			catch(err) {
				$('.loading').fadeOut();
				$('#conPage').hide();
				$('#errorIframe').fadeIn();
			}
		});



	});
	$('body').on('click','#btnIframeRefresh',function(e){
		e.preventDefault();
		location.reload();
		//$("#conPage").attr("src", localStorage['lastPage']);
	});

	function init_content_monitor() {
		// The user did navigate away from the currently displayed iframe page. Show an animation
		var content_start_loading = function () {
			$('.loading').show();
		};

		// the iframe is done loading a new page. Hide the animation again

		// Listen to messages sent from the content iframe
		var receiveMessage = function receiveMessage(e){
			var url = window.location.href,
				url_parts = url.split("/"),
				allowed = url_parts[0] + "//" + url_parts[2];

			// Only react to messages from same domain as current document
			if (e.origin !== allowed) return;
			// Handle the message

			switch (e.data) {
				case 'iframe_change': content_start_loading(); break;
			}
		};
		window.addEventListener("message", receiveMessage, false);

		// This will be triggered when the iframe is completely loaded

		// content.on('load', content_finished_loading);
		// fires even before complete load -> changed to page_tail2 $('.loading', window.parent.document).fadeOut();
	}
	init_content_monitor();

</script>
</body>
</html>