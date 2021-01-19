<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales')) {
		// redirect to denied page
		Redirect::to(1);
	}

?>


	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">



	<div class="content-header">

		<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span> Item Summary</h1>


	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}
	?>
	<div id="test"></div>
	<div class="row">
		<div class="col-md-12">


			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">List</div>
				<div class="panel-body">


					<div class="row">
						<div class="col-md-12 text-center">
							<input type="hidden" id='ctr' value='0'>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<select class='form-control' name="type" id="type">
									<option value="">All</option>
									<option value="1">Branch To Branch</option>
									<option value="2">Branch To Client</option>
								</select>
								<span class='help-block'>Select Type</span>
							</div>
						</div>
					</div>

					<div id="holder"></div>

				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->


	<script>

		$(function(){

			getSummary();

			$('body').on('click','#btnSubmit',function(){
				getSummary();
			});

			$('body').on('click','#btnPrev',function(){
				var ctr = parseInt($('#ctr').val());
				ctr  = ctr - 1;
				$('#ctr').val(ctr)
				getSummary();
			});

			$('body').on('click','#btnNext',function(){
				var ctr = parseInt($('#ctr').val());
				ctr  = ctr + 1;
				$('#ctr').val(ctr)
				getSummary();
			});

			$('body').on('change','#type',function(){
				getSummary();
			});

			
			function getSummary(){

				var ctr = $('#ctr').val();
				var type = $('#type').val();

				$('#holder').html('Fetching records...');

				$.ajax({
				    url:'../ajax/ajax_inventory.php',
				    type:'POST',
				    data: { functionName:'byItemSummary',ctr: ctr,type:type},
				    success: function(data){
						$('#holder').html(data);
				    },
				    error:function(){
				    }
				});

			}


		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>