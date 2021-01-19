<?php
	// truck name // amount // total number of PO

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('truck')){
		// redirect to denied page
		Redirect::to(1);
	}

	$truck = new Truck();
	$trucks = $truck->get_active('trucks',array('company_id' ,'=',$user->data()->company_id));
?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Manage Trucks
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">



				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">

						Trucks
					</div>
					<div class="panel-body">
						<div class="row">

							<div class="col-md-3">
								<input type="text" placeholder='From' id='date_from' class='form-control'>
							</div>
							<div class="col-md-3">
								<input type="text" placeholder='To' id='date_to' class='form-control'>
							</div>
							<div class="col-md-6">
								<button id='btnSubmit' class='btn btn-default'>Submit</button>
								<button id='btnDownload' class='btn btn-default'>Download</button>
							</div>
						</div>
						<br>
					<div id="con"></div>
				</div>
			</div>
		</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$('#date_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_from').datepicker('hide');
			});
			$('#date_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#date_to').datepicker('hide');
			});
			$('body').on('click','#btnSubmit',function(){
				getSummary(0);
			});
			$('body').on('click','#btnDownload',function(){
				getSummary(1);
			});
			function getSummary(dl){

				var date_from = $('#date_from').val();
				var date_to = $('#date_to').val();
				if(dl == 1){

					window.open(
						'../ajax/ajax_wh_order.php?functionName=truckSummary&dl='+dl+'&date_from='+date_from+'&date_to='+date_to,
						'_blank' //
					);

				} else {

					$('#con').html("Loading data. Please wait.");

					$.ajax({
						url:'../ajax/ajax_wh_order.php',
						type:'POST',
						data: {functionName:'truckSummary',dl:dl,date_from:date_from,date_to:date_to},
						success: function(data){
							$('#con').html(data);
						},
						error:function(){

						}
					});

				}


			}   
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>