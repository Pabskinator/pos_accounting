<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';


	if(!$user->hasPermission('other_income')) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Other Income
			</h1>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">List</div>
							<div class="col-md-6 text-right">
								<button class='btn btn-default btn-sm' id='btnAdd'><i class='fa fa-plus'></i></button>
							</div>
						</div>

					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='date_from' placeholder='Date From'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='date_to' placeholder='Date To'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' id='member_id_filter' >
								</div>
							</div>
							<div class="col-md-3">
								<button class='btn btn-default' id='btnFilter'>Filter</button>
							</div>
						</div>
						<br>
						<div id="con"></div>
					</div>


				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id='mtitle'>Add Income</h4>
						</div>
						<div class="modal-body" id='mbody'>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<div id='client_con'>
										<strong>Client:</strong>
										<input type="text" class='form-control' id='member_id'>
										</div>
										<div id='other_source_con' style='display:none;'>
											<strong>Enter Source:</strong>
											<input type="text" class='form-control' id='other_source'>
										</div>
										<div>
											<input type="checkbox" id='chkOtherSource'>
											<label for="chkOtherSource">Other Source</label>
										</div>
									</div>

									<div class="form-group">
										<strong>Remarks: </strong>
										<input type="text" class='form-control' id='remarks'>
									</div>
									<div class="form-group">
										<strong>Amount: </strong>
										<input type="text" class='form-control' id='amount'>
									</div>
									<div class="form-group">
										<strong>CR Number: </strong>
										<input type="text" class='form-control' id='cr_number'>
									</div>
									<div class="panel panel-primary">
										<div class="panel-heading">Optional</div>
										<div class="panel-body">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<input type="text" class='form-control' id='item_description' placeholder='Item Description'>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<input type="text" class='form-control' id='item_qty' placeholder='Qty'>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<input type="text" class='form-control' id='item_price' placeholder='Price'>
													</div>
												</div>
												<div class="col-md-2">
													<div class='form-group text-right'>
														<button id='btnAddItem'  class='btn btn-default btn-sm'>Add Item</button>
													</div>
												</div>
											</div>
											<div id='item_con'>
													<table class='table table-bordered' id='tbl_items'>
														<thead>
														<tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th><th></th></tr>
														</thead>
														<tbody>

														</tbody>
													</table>
											</div>

										</div>
									</div>

									<div class="form-group">
										<button class='btn btn-primary' id='btnSave'>Submit</button>
									</div>
								</div>
							</div>

						</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	<script>

		$(document).ready(function(){
			toggleItemCon();
			function toggleItemCon(){

				if($('#tbl_items > tbody  > tr').length > 0)	{
					$('#item_con').show();
				} else {
					$('#item_con').hide();
				}
			}
			function addItem(){
				var item_description = $('#item_description').val();
				var item_qty = $('#item_qty').val();
				var item_price = $('#item_price').val();

				if(item_description && item_qty && item_price){
					$('#tbl_items > tbody').append("<tr><td>"+item_description+"</td><td>"+item_qty+"</td><td>"+item_price+"</td><td>"+( parseFloat(item_price) * parseFloat(item_qty) )+"</td><td><i class='fa fa-remove cpointer removeItem'></i></td></tr>");
					clearItem();
					toggleItemCon();
				}
			}

			function clearItem(){
				$('#item_description').val('');
				$('#item_qty').val('');
				$('#item_price').val('');
			}


			function printData(data) {
				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<html><head><title></title><style></style>');
				mywindow.document.write('<link rel="stylesheet" href="../css/bootstrap.css" type="text/css" />');
				mywindow.document.write('</head><body style="padding:0;margin:0;;font-family: Arial, Helvetica, sans-serif;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');
				setTimeout(function() {
					mywindow.print();
					mywindow.close();

				}, 300);
				return true;
			}
			function getItems(){

				var arr = [];

				if( $('#tbl_items > tbody  > tr').length  >  0 ) {

					$('#tbl_items > tbody > tr').each(function(){

						var row = $(this);

						var item_description = row.children().eq(0).html();

						var item_qty = row.children().eq(1).html();

						var item_price = row.children().eq(2).html();

						arr.push( { item_description:item_description, item_qty:item_qty, item_price:item_price } );

					});

				}

				return arr;

			}

			$('body').on('click','.removeItem',function(){
				$(this).parents('tr').remove();
				toggleItemCon();
			});
			$('body').on('click','#btnAddItem',function(){
				addItem();

			});
			$('body').on('change','#chkOtherSource',function(){
				if($('#chkOtherSource').is(":checked")){
					$('#client_con').hide();
					$('#other_source_con').show();

					$('#member_id').select2('val',null);

				} else {
					$('#client_con').show();
					$('#other_source_con').hide();
					$('#other_source').val('');
				}
			});

			$('#member_id').select2({
				placeholder: 'Search Client' , allowClear: true, minimumInputLength: 2,

				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members',
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname + ", " + item.sales_type_name,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});

			$('#member_id_filter').select2({
				placeholder: 'Search Client' , allowClear: true, minimumInputLength: 2,

				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members',
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname + ", " + item.sales_type_name,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});


			$('body').on('click','#btnSave',function(){

				var remarks = $('#remarks').val();
				var amount = $('#amount').val();
				var member_id = $('#member_id').val();
				var cr_number = $('#cr_number').val();
				var other_source = $('#other_source').val();
				if((member_id || other_source )&& amount && remarks){

					var items = getItems();

					$.ajax(
						{
							url:'../ajax/ajax_deposits.php',
							type:'POST',
							data: {functionName: 'addIncome',items:JSON.stringify(items),other_source:other_source,cr_number:cr_number,member_id:member_id,amount:amount,remarks:remarks},
							success: function(data){
								$('#myModal').modal('hide');
								alert(data);
								getRecord();
							},
							error:function(){

							}
						}
					);
				}


			});

			$('body').on('click','#btnAdd',function(){
				$('#myModal').modal('show');
			});
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

			getRecord();

			$('body').on('click','#btnFilter',function(){
				getRecord();
			});

			$('body').on('click','.btnPrint',function(){

				var con = $(this);

				var list = con.attr('data-list');

				try {

					list = JSON.parse(list);
					console.log(list);
					var member_name = "";
					if(list.member_name){
						member_name = list.member_name;
					} else {
						member_name = list.other_source;
					}
					var print_html = "";
					print_html += "<h3 class='text-center' >Acknowledgement Receipt</h3>";
					print_html +="<div style='width:80%;float:left'>Client: "+member_name+"</div>";
					print_html +="<div style='width:20%;float:left'>Ref #: "+list.id+"</div>";
					print_html +="<div style='width:80%;float:left'>Remarks: "+list.remarks+"</div>";
					print_html +="<div style='width:20%;float:left'>Date: "+list.created_at+"</div>";
					print_html += "<br>";
					print_html += "<br><table  class='table table-bordered'>";
					print_html += "<thead><tr><th>Item Description</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>";
					print_html += "<tbody>";

					if(list.item_list && list.item_list != '[]'){

						var item_list = JSON.parse(list.item_list);
						var ctr =0 ;
						for(var i in item_list){
							print_html += "<tr><td>"+ item_list[i].item_description +"</td><td>"+ item_list[i].item_qty +"</td><td>"+ number_format(item_list[i].item_price,2) +"</td><td>"+ number_format((parseFloat(item_list[i].item_qty) * parseFloat(item_list[i].item_price)),2) +"</td></tr>";
							ctr++;
						}
						if(ctr < 5){
							for(var j=ctr;j<=5;j++){
								print_html += "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

							}
						}

					}

					print_html += "</tbody>";
					print_html += "</table><br>";
					print_html += "<table class='table table-bordered'>";
					print_html += "<tr><th>Prepared By</th><th>Checked By</th><th>Received By</th></tr>"
					print_html +="<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>"
					print_html += "</table>";
					printData(print_html);
				} catch (e){

				}

			});



			function getRecord(){

				var date_from = $('#date_from').val();
				var date_to = $('#date_to').val();
				var member_id = $('#member_id_filter').val();

				$.ajax({
				    url:'../ajax/ajax_deposits.php',
				    type:'POST',
				    data: {functionName:'getOtherIncome',member_id:member_id,date_from:date_from,date_to:date_to },
				    success: function(data){
				        $('#con').html(data);
				    },
				    error:function(){
				        
				    }
				});
			}
		});



	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>