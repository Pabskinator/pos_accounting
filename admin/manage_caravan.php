<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('caravan_request') && !$user->hasPermission('caravan_manage')) {
		// redirect to denied page
		Redirect::to(1);
	}
	$agent_request = new Agent_request();
	if(!$user->hasPermission('caravan_manage')){
		$agent_request_all = $agent_request->get_active('agent_request', array('user_id', '=', $user->data()->id));
	}else {
		$agent_request_all = $agent_request->get_active('agent_request', array('company_id', '=', $user->data()->company_id));
	}



?>



	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span> My Caravan </h1>

			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('caravan')) {
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('caravan') . "</div>";
				}
			?>
			<div class="row">
				<div class="col-md-12">
					<?php

					?>
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Request</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="searchSales" class='form-control' placeholder='Search..'/>
									</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''>--Select Branch--</option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = isset($id) ? $terminal->data()->branch_id : escape(Input::get('branch_id'));

												if($a==$b->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo $b->name;?> </option>
											<?php
											}
										?>
									</select>
										</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
									<select id="status" name="status" class="form-control">
										<?php if($user->hasPermission('mc_pending')) { ?>
										<option value='1'>For Approval</option>
										<?php } ?>
										<?php if($user->hasPermission('mc_approve')) { ?>
											<!-- singet lng ung 6-->
											<option value='6'>For releasing</option>
										<?php } ?>
										<?php if($user->hasPermission('mc_processed')) { ?>
										<option value='2'>For liquidation</option>
										<?php } ?>

										<?php if($user->hasPermission('mc_liquidate_sales')) { ?>
											<!-- singet lng ung 5-->
											<option value='5'>For collection</option>
										<?php } ?>
										<?php if($user->hasPermission('mc_liquidate_item')) { ?>
											<option value='3'>For inspection</option>
										<?php } ?>
										<?php if($user->hasPermission('mc_verify')) { ?>
										<option value='4'>Caravan History</option>
										<?php } ?>
										<option value='-1'>Decline</option>
									</select>
										</div>
								</div>
							</div>

							<input type="hidden" id="hiddenpage" />
							<div id="holder"></div>
						</div>
					</div>

				</div>
			</div>
			<div id="test"></div>
		</div>
	</div> <!-- end page content wrapper-->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%'>
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
			function formatItem(o) {
				if(!o.id)
					return o.text;
				else {
					var r = o.text.split(':');
					return "<span> " + r[0] + "</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>" + r[2] + "</small></span>";
				}
			}
			$('body').on('click','.getorder',function(){
				var branch_id = localStorage['branch_id'];
				var req_id = $(this).attr('id');
				$('.loading').show();
				$('#mtitle').empty();
				$('#mtitle').append("Request ID # " + req_id);
				$.ajax({
					url: '../ajax/ajax_get_requestDetails.php',
					type:'POST',
					beforeSend: function(){
						$('#mbody').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
					},
					data:{functionName:"getRequestDetails",id:req_id,branch_id:branch_id},
					success:function(data){

						$('#mbody').html(data);
						$("#new_item_id").select2({
							placeholder: 'Item code',
							allowClear: true,
							minimumInputLength: 2,
							formatResult: formatItem,
							formatSelection: formatItem,
							escapeMarkup: function(m) {
								return m;
							},
							ajax: {
								url: '../ajax/ajax_query.php',
								dataType: 'json',
								type: "POST",
								quietMillis: 50,
								data: function(term) {
									return {
										search: term, functionName: 'searchItemJSON'
									};
								},
								results: function(data) {
									return {
										results: $.map(data, function(item) {
											return {
												text: item.barcode + ":" + replaceAll(item.item_code,':','') + ":" + replaceAll(item.description,':','') + ":" + item.price,
												slug: item.description,
												is_bundle: item.is_bundle,
												unit_name: item.unit_name,
												id: item.id
											}
										})
									};
								}

							}
						}).on("select2-close", function(e) {

						}).on("select2-highlight", function(e) {

						});
						$('#myModal').modal('show');
						$('.loading').hide();
					},
					error:function(){
						$('.loading').hide();
					}
				});
			});
			$('body').on('click','.timelog',function(){
				var req_id = $(this).attr('id');
				$('.loading').show();
				$('#mtitle').empty();
				$('#mtitle').append("Request ID # " + req_id);
				$.ajax({
					url: '../ajax/ajax_get_requestDetails.php',
					type:'POST',
					beforeSend: function(){
						$('#mbody').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
					},
					data:{functionName:"getTimelog",id:req_id},
					success:function(data){
						$('#mbody').html(data);
						$('#myModal').modal('show');
						$('.loading').hide();
					},
					error: function(){
						$('.loading').hide();
					}
				});
			});
			if(localStorage['caravan_status'] != null){

				getPage(0,'',0,localStorage['caravan_status']);
				$('#status').val(localStorage['caravan_status']);
			} else {
				getPage(0,'',0,$('#status').val());
			}

			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var status = $('#status').val();
				getPage(page,search,b,status);
			});
			$("#searchSales").keyup(function(){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var status= $('#status').val();
				getPage(0,search,b,status);
			});
			function getPage(p,search,b,status){
				$('.loading').show();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,functionName:'caravanPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,b:b,status:status},
					success: function(data){
						$('#holder').empty();
						$('#holder').append(data);
						$('.loading').hide();
					},
					error:function(){
						$('.loading').hide();
						alertify.alert('Something went wrong. The page will be refresh.',function(){
							location.href='manage_caravan.php';
						});
					}
				});
			}
			$('body').on('change','#branch_id',function(){

				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var status = $('#status').val();
				getPage(0,search,b,status);
			});
			$('body').on('change','#status',function(){
				var search = $('#searchSales').val();
				var b = $('#branch_id').val();
				var status = $('#status').val();
				localStorage['caravan_status'] = status;
				getPage(0,search,b,status);
			});

			function Popup(data)
			{
				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title>');
				/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
				mywindow.document.write('</head><body style="padding:0;margin:10px;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				mywindow.print();
				mywindow.close();
				return true;
			}
			$('body').on('click','#printorder',function(){
				var toprint ='';

				var id = $(this).attr('data-order_id');
				var name = $(this).attr('data-name');
				var witness = $(this).attr('data-witness');
				var remarks = $(this).attr('data-remarks');

				toprint += "<h2 style='text-align: center'>Caravan Request Form</h2>";
				toprint += "<p style='text-align:right'>Caravab ID #"+id+"</p>";
				toprint += "<p style='float:left;width:8%;'>Name:</p><p style='float:left;width:40%;border-bottom: 1px solid'>"+name+"</p>";
				toprint += "<p style='float:left;width:8%;'>Witness:</p><p style='float:left;width:40%;border-bottom: 1px solid'>"+witness+"</p>";
				toprint += "<p style=''>Remarks:<span style='border-bottom: 1px solid;'>"+remarks+"</span></p>";
				toprint += "<table class='table' style='width:100%;border-collapse:collapse;'>";
				toprint +="<tr style='padding:5px;'><th style='text-align: left;border-top : 1px solid black;border-bottom : 1px solid black; border-collapse:collapse;'>Barcode</th><th style='text-align: left;border-top : 1px solid black;border-bottom : 1px solid black; border-collapse:collapse;'>Description</th><th style='text-align: right;border-top : 1px solid black;border-bottom : 1px solid black; border-collapse:collapse;'>Price</th><th style='text-align: right;border-top : 1px solid black;border-bottom : 1px solid black; border-collapse:collapse;'>Quantity</th><th style='text-align: right;border-top : 1px solid black;border-bottom : 1px solid black; border-collapse:collapse;'>Total</th></tr>";
				var length = parseInt($("#tblorder > tbody > tr").length) - 1;
				var grandtotal = 0;
				var totalqty = 0;
				$('#tblorder > tbody > tr').each(function(index){
					var row = $(this);
					var bc = row.children().eq(0).text();
					var itemcode =row.children().eq(1).html();
					var price =  row.children().eq(2).text();
					var qty = row.children().eq(3).find('input').val();

					var total = row.children().eq(6).text();
					total = total.replace(',','');

					if(length > index){
						toprint += "<tr style='padding:5px;'><td style='border-top : 1px solid black;border-bottom : 1px solid black; border-collapse:collapse;'>"+bc+"</td><td style='border-top : 1px solid black;border-bottom : 1px solid black; border-collapse:collapse;'>"+itemcode+"</td><td style='text-align: right;border-top : 1px solid black;border-bottom : 1px solid black; border-collapse:collapse;'>"+price+"</td><td style='text-align: right;border-top : 1px solid black;border-bottom : 1px solid black; border-collapse:collapse;'>"+qty+"</td><td style='text-align: right;border-top : 1px solid black;border-bottom : 1px solid black; border-collapse:collapse;'>"+total+"</td></tr>";
						totalqty = parseFloat(totalqty) + parseFloat(qty);
						grandtotal = parseFloat(grandtotal) + parseFloat(total);
					}
				});
				toprint+= "</table>";
				toprint += "<div style='clear:both;'></div>";
				toprint+= "<p><span style='float:left'><strong>Total Quantity:"+totalqty+"</strong></span><span style='float:right'><strong>Total Amount: "+number_format(grandtotal,2)+"</strong></span></p>";
				toprint += "<br>";
				toprint += "<p style='float:left;width:14%;'>Processed by:</p><p style='float:left;width:33%;border-bottom: 1px solid'> &nbsp;</p>";
				toprint += "<p style='float:left;width:14%;'>Released by:</p><p style='float:left;width:33%;border-bottom: 1px solid'> &nbsp; </p>";
				Popup(toprint);
			});

			$('body').on('change','.rackallocation',function(){
				var row = $(this).parents('tr');
				if($(this).val()){
					var tid = $(this).attr('id');
					var opt = $('#'+tid+ " option:selected");
					var qty = opt.attr('data-qty');
					row.children().eq(5).text(qty);
				} else {
					row.children().eq(5).text(0);
				}

			});

			$('body').on('click','#btnAddNew',function(){
				var  con = $(this);
				var id = con.attr('data-id');
				var qty = $('#new_qty').val();
				var item_id =  $('#new_item_id').val();

				$.ajax({
					url:'../ajax/ajax_caravan.php',
					type:'POST',
					data: {functionName:'addNewItem',id:id,item_id:item_id,qty:qty},
					success: function(data){
						tempToast('info',data);
						getDetails(id);

					},
					error:function(){

					}
				});
			});

			function getDetails(req_id){
				var branch_id = localStorage['branch_id'];
				$.ajax({
					url: '../ajax/ajax_get_requestDetails.php',
					type:'POST',
					beforeSend: function(){
						$('#mbody').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
					},
					data:{functionName:"getRequestDetails",id:req_id,branch_id:branch_id},
					success:function(data){

						$('#mbody').html(data);
						$("#new_item_id").select2({
							placeholder: 'Item code',
							allowClear: true,
							minimumInputLength: 2,
							formatResult: formatItem,
							formatSelection: formatItem,
							escapeMarkup: function(m) {
								return m;
							},
							ajax: {
								url: '../ajax/ajax_query.php',
								dataType: 'json',
								type: "POST",
								quietMillis: 50,
								data: function(term) {
									return {
										search: term, functionName: 'searchItemJSON'
									};
								},
								results: function(data) {
									return {
										results: $.map(data, function(item) {
											return {
												text: item.barcode + ":" + replaceAll(item.item_code,':','') + ":" + replaceAll(item.description,':','') + ":" + item.price,
												slug: item.description,
												is_bundle: item.is_bundle,
												unit_name: item.unit_name,
												id: item.id
											}
										})
									};
								}

							}
						}).on("select2-close", function(e) {

						}).on("select2-highlight", function(e) {

						});
						$('#myModal').modal('show');
						$('.loading').hide();
					},
					error:function(){
						$('.loading').hide();
					}
				});
			}
			
			$('body').on('click','.btnDeleteItem',function(){
				var con = $(this);
				var id = con.attr('data-id');
				var req_id = con.attr('data-req_id');
				alertify.confirm("Are you sure you want to delete this record?",function(e){
					if(e){
						$.ajax({
						    url:'../ajax/ajax_caravan.php',
						    type:'POST',
						    data: {functionName:'deleteCaravanItem',id:id},
						    success: function(data){
						        tempToast('info',data,'Info');
							    getDetails(req_id);
						    },
						    error:function(){
						        
						    }
						});
					}
				});
			});
			
			$('body').on('click','#returnOrder',function(){
				var con = $(this);
				var id = con.attr('data-order_id');
				var status = $('#status').val();
				$.ajax({
				    url:'../ajax/ajax_caravan.php',
				    type:'POST',
				    data: {functionName:'returnOrder',id:id},
				    success: function(data){
					    tempToast('info',data,'Info');
					    $('#myModal').modal('hide');
					    getPage(0,'',0,status);

				    },
				    error:function(){

				    }
				})
			});



		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>