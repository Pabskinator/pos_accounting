<?php

	// OUT GOING


	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('in_out')){
		// redirect to denied page
		Redirect::to(1);
	}



?>


	<!-- Page content -->
	<div id="page-content-wrapper">


		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset" id ='app'>
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					In / Out
				</h1>
			</div>
			<div>
				<a class='btn btn-default' style='margin-bottom: 10px;' href='in-out-assemble.php' >Assembly</a>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-primary">
						<div class="panel-heading">

							<div class="row">
								<div class="col-md-6">List</div>
								<div class="col-md-6 text-right">
									<button id='btnDownload' class='btn btn-default btn-sm'><i class='fa fa-download'></i></button>
								</div>
							</div>
						</div>
						<div class="panel-body">


							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" id='item_id' class='form-control selectitem'>
										<span class='help-block'>Search Item</span>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" id='branch_id' class='form-control' placeholder="Branch" >
										<span class='help-block'>Search Branch</span>
									</div>
								</div>
								<div class="col-md-3">
								    <div class="form-group">
								        <input type="text" id='from' name='from'  placeholder='FROM' class='form-control'>
									    <span class='help-block'>Date From</span>
								    </div>
								</div>
								<div class="col-md-3">
								    <div class="form-group">
								        <input type="text" id='to' name='to'  placeholder='TO'  class='form-control'>
									    <span class='help-block'>Date To</span>
								    </div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<select name="sales_date_type" id="sales_date_type" class='form-control'>
											<option value="1">By Sold Date</option>
											<option value="2">By Delivery Date</option>
										</select>
										<span class='help-block'>Filter Sales</span>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<button class='btn btn-default' id='btnSubmit' name='btnSubmit'>Submit</button>
									</div>
								</div>
							</div>
							<br>
							<div id="holder"></div>
						</div>
					</div>
				</div>
			</div>


		</div> <!-- end page content wrapper-->

		<script>
			$(document).ready(function(){
				inOut();
				$('body').on('click','#btnSubmit',function(){
					inOut(0);
				});
				$('body').on('click','#btnDownload',function(){
					inOut(1);
				});
					$('#from').datepicker({
						autoclose:true
					}).on('changeDate', function(ev){
						$('#from').datepicker('hide');
					});
					$('#to').datepicker({
						autoclose:true
					}).on('changeDate', function(ev){
						$('#to').datepicker('hide');
					});
				var ajax_pending = false;
				function inOut(is_dl){
					var from = $('#from').val();
					var to = $('#to').val();
					var branch_id = $('#branch_id').val();
					var item_id = $('#item_id').val();
					var sales_date_type = $('#sales_date_type').val();

					if(is_dl == 1){
						window.open(
							'../ajax/ajax_inventory.php?functionName=inOut&from='+from+"&to="+to+"&branch_id="+branch_id+"&is_dl="+is_dl+"&item_id="+item_id+"&sales_date_type="+sales_date_type,
							'_blank' // <- This is what makes it open in a new window.
						);
					} else {
						$('#holder').html('Loading...');
						if(ajax_pending) {
							return;
						}
						ajax_pending = true;
						$.ajax({
							url:'../ajax/ajax_inventory.php',
							type:'POST',
							data: {functionName:'inOut',sales_date_type:sales_date_type,from:from,to:to,branch_id:branch_id,is_dl:is_dl,item_id:item_id},
							success: function(data){
								$('#holder').html(data);
								ajax_pending = false;
							},
							error:function(){
								ajax_pending = false;
							}
						});
					}
				}

				$('#branch_id').select2({
				    placeholder: 'Search Branch' , allowClear: true, minimumInputLength: 2,
				    ajax: {
				        url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
				            return {
				                q: term, functionName: 'branches'
				            };
				        }, results: function(data) {
				            return {
				                results: $.map(data, function(item) {

				                    return {
				                        text: item.name,
				                        slug: item.name,
				                        id: item.id
				                    }
				                })
				            };
				        }
				    }
				});

			});
		</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>