<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sms_log')) {
		// redirect to denied page
		Redirect::to(1);
	}
?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Report </h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>Log</div>
							<div class='col-md-6 text-right'>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div id='test2'></div>
						<div class="row">

							<div class="col-md-3" >
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Date From' id='dt_from'>
								</div>
							</div>
							<div class="col-md-3" >
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Date To' id='dt_to'>
								</div>
							</div>
							<div class="col-md-3" >
								<div class="form-group">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''>Search Branch</option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){

												?>
												<option value='<?php echo $b->id ?>'><?php echo $b->name;?> </option>
												<?php
											}
										?>
									</select>
								</div>
							</div>

						</div>
						<div class="row">

						</div>

						<br>
						<div id="holder"></div>

					</div>


				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='mtitle'></h4>
				</div>
				<div class="modal-body" id='mbody'>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {

			$('#branch_id').select2({
				placeholder:'Choose branch',
				allowClear:true
			});

			$('body').on('click','.btnGetOrderDetails',function(e){
				e.preventDefault();
				var con  = $(this);
				var id = con.text();
				var ret_html = '';

				$('#myModal').modal('show');
				var total = 0;
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					beforeSend:function(){
						$('#mbody').html('Loading...');
					},
					dataType:'json',
					data: {functionName:'getWhOrdersDetails',order_id:id},
					success: function(data){

						var order = JSON.parse(data.order);
						ret_html += "<table class='table'>";
						ret_html += "<thead>";
						ret_html += "<tr>";
						ret_html += "<th>Item</th><th>Price</th><th>Qty</th><th>Total</th><th></th>";
						ret_html += "</tr>";
						ret_html += "</thead>";
						ret_html += "<tbody>";
						for(var i in order){
							var tmpTotal = replaceAll(order[i].total,",","");
							total = parseFloat(tmpTotal) +  parseFloat(total);
							ret_html += "<tr>";
							ret_html += "<td>"+order[i].item_code+" <span class='span-block text-danger'>"+order[i].description+"</span></td>";
							ret_html += "<td>"+order[i].adjusted_price+"</td>";
							ret_html += "<td>"+order[i].qty+"</td>";
							ret_html += "<td>"+order[i].total+"</td>";
							ret_html += "<td></td>";
							ret_html += "</tr>";
						}
						ret_html += "</tbody>";
						ret_html += "<tr><th colspan='5'>Total: " +number_format(total,2)+" </th></tr>";
						ret_html += "</html>";
						$('#mbody').html(ret_html);


					},
					error:function(){

					}
				});
			});

			getSummary();

			$('body').on('change','#branch_id',function(){
				getSummary();
			});

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				if($('#dt_from').val() && $('#dt_to').val()){
					getSummary();
				}
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				if($('#dt_from').val() && $('#dt_to').val()){
					getSummary();
				}
			});

			function getSummary(){

				var b = $('#branch_id').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();

				$.ajax({
					url: '../ajax/ajax_sms.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{functionName:'smsSummary',branch_id:b,dt_from:dt_from,dt_to:dt_to},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}
			updateInventory("2 1 3 3 4 7",6,23);
			function updateInventory(orig,item_id,qty){
				var sp = orig.split(" ");
				if(sp.length > 0){
					var ctr = 1;
					var tmp = 0;
					var arr= [];

					for(var i=0;i<= sp.length;i++){

						if(ctr % 2 == 0){
							arr[tmp] = sp[i];
						} else {
							tmp = sp[i];
						}
						ctr++;
					}
					console.log(orig);
					if(arr[item_id]){
						arr[item_id] = qty;
					}

					var ret_val = "";
					for(var i in arr){
						ret_val += " " + i + " " + arr[i];
					}
					console.log(ret_val.trim());
				}
			}


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>