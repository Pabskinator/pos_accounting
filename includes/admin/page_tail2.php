<br><br>
</div>
</div>

<script>
	$(document).ready(function () {
		//parent.document.getElementsByClassName("loading")[0].style.display = 'none';
	//	Feedback({h2cPath:'../js/html2canvas.js',url:'../ajax/feedback.php'});
		$('.loading', window.parent.document).fadeOut();
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
		$('body').on('click','#btnRemoveSecondNavigationContainer',function(){
			$('#secondNavigationContainer').hide();
		});
		$('body').on('click','#btnShowNavigationContainer',function(){
			$('#secondNavigationContainer').show();
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
			$('#bs-example-navbar-collapse-1', window.parent.document).removeClass('in');
			//$('.right-panel-pane').fadeOut(100);
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
			/*$('.inset a').attr('data-toggle','tooltip');
			$('.inset a').attr('data-container','body');
			$('.inset a').attr('data-placement','bottom');
			$('.inset button').attr('data-toggle','tooltip');
			$('.inset button').attr('data-container','body');
			$('.inset button').attr('data-placement','bottom');
			$('[data-toggle="tooltip"]').tooltip();*/
		}

	});
	window.onbeforeunload = function(e) {
		window.top.postMessage('iframe_change', '*');
	};
	<?php if($user->hasPermission('inventory_receive')) { ?>
		function goToReceivingOrder(){
			location.href='transfer_monitoring.php?logistics=1';
		}
		//setInterval(getUnreadOrder, 90000);
		//getUnreadOrder();
		function getUnreadOrder(){
			$.ajax({
				url:'../ajax/ajax_query.php',
				type:'POST',
				data: {functionName:'getUnreadOrder'},
				success: function(data){
					var options ={"extendedTimeOut": "0","timeOut": "0", "onclick": goToReceivingOrder};
					if(data != '0'){
						tempToast('info',"<p>You have "+data+" new order</p>","<h4>Notification!</h4>",options);
					}
				},
				error:function(){

				}
			});
		}



	<?php } ?>
</script>
</body>
</html>