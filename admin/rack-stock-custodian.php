<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('del_helper')){
		// redirect to denied page
		Redirect::to(1);
	}

	// get all branch base on company
	$driver = new Driver();
	$drivers = $driver->get_active('drivers',array('company_id' ,'=',$user->data()->company_id));

?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Update Rack Stockman
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
					<div class="panel-heading">Rack Assignment</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='branch_id'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='txtRack' placeholder='Search Rack Group'>
									<span class='help-block'>Search for rack group (1A,1B,1C, etc)</span>
								</div>
							</div>
						</div>
						<div id="holder">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){

			$('#branch_id').select2({
				placeholder: 'Branch',
				allowClear: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'branches'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.name ,
									slug: item.name ,
									id: item.id
								}
							})
						};
					}
				}
			});

			$('body').on('change','#branch_id',function(){
				getData();
			});

			var timer;
			$("#txtRack").keyup(function(){

				var searchtxt = $("#txtRack");

				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getData();

				}, 1000);

			});

			$('body').on('click','#btnUpdate',function(){
				var con = $(this);
				button_action.start_loading(con);
				var stock_man = $('#txtStockMan').val();
				if(!stock_man){
					alert("Please add stock man first.");
					button_action.end_loading(con);
					return;
				}
				alertify.confirm("Are you sure you want to update this rack group?", function(e){
					if(e){
						var arr = [];
						$('#tblRacks > tbody > tr').each(function(){
							var row = $(this);
							var id = row.attr('data-id');
							if(id){
								arr.push(id);
							}
						});

						$.ajax({
						    url:'../ajax/ajax_inventory.php',
						    type:'POST',
						    data: {functionName:'updateRackGroup', ids: JSON.stringify(arr),stock_man:stock_man},
						    success: function(data){
						        alert(data);
							    button_action.end_loading(con);
							    getData();

						    },
						    error:function(){
							    button_action.end_loading(con);
						    }
						});
					} else {
						button_action.end_loading(con);
					}
				})
			});

			function getData(){

				var grp = $('#txtRack').val();
				var branch_id = $('#branch_id').val();

				if(grp && branch_id){
					$('#holder').html('Fetching records...');
					$.ajax({
						url:'../ajax/ajax_inventory.php',
						type:'POST',
						data: {functionName:'getRackByGroup', branch_id:branch_id, grp: grp},
						success: function(data){
							$('#holder').html(data)
						},
						error:function(){

						}
					});
				}

			}
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>