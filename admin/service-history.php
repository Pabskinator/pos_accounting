<?php
	include_once '../core/admininit.php';

?>

<?php include_once 'includes/service/page_head.php'; ?>

<?php include_once 'includes/service/sidebar.php'; ?>

<main>

	<div id="main">
		<h3>History</h3>
		<form class="col s12">
			<div class="row">
				<div class="input-field col s12 m4">
				</div>
				<div class="input-field col s12 m4">
					<i class="material-icons prefix">perm_contact_calendar</i>
					<input id="search_date" placeholder="Enter Date" type="date" class="datepicker">
				</div>
				<div class="input-field col s12 m4">
					<a id="btnSearch" class="waves-effect waves-light btn grey darken-2">Search</a>
				</div>
			</div>
		</form>

			<input type="hidden" id="hiddenpage" />
			<div id="holder"></div>

	</div>
</main>
<footer>

</footer>


<script src="../js/jquery.js"></script>
<script src="../js/materialize.min.js"></script>
<script>
	$(function(){
		$('.button-collapse').sideNav();
		$('.datepicker').pickadate({
			selectMonths: true,
			selectYears: 15,
			format: 'mm/dd/yyyy',
			closeOnSelect: true,
			onSet: function (ele) {
				if(ele.select){
					this.close();
				}
			}
		});
		$('body').on('click','#btnSearch',function(e){
			e.preventDefault()
			getPage(0);
		});
		getPage(0);
		function getPage(p){
			var dt = $("#search_date").val();

			$.ajax({
				url: '../ajax/ajax_paging.php',
				type:'post',
				beforeSend:function(){
					$('#holder').html('<div class="progress"><div class="indeterminate"></div></div>');
				},
				data:{page:p,functionName:'serviceAttendancePaginate', dt:dt},
				success: function(data){

					setTimeout(function(){$('#holder').html(data)},1000);

				}
			});
		}


	});
</script>


<?php include_once 'includes/service/page_tail.php';  ?>
