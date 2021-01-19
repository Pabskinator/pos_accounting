<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
?>

	<div id="page-content-wrapper">
		<div class="page-content inset">
			<div class="btn-group" role="group" aria-label="...">
				<button type="button" id='nav1' class="btn btn-default">Pending</button>
				<button type="button" id='nav2'  class="btn btn-default">Processed</button>
			</div>
			<br><br>
			<div id="con_pending">
				<div id="holder"></div>
			</div>
			<div id="con_processed">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<input type="text" class='form-control' id='txtSearch' placeholder='Search...'>
						</div>
					</div>
					<div class="col-md-4">

					</div>
					<div class="col-md-4">

					</div>
				</div>
				<input type="hidden" id="hiddenpage" />
				<div id="holder2"></div>
			</div>

		</div>
	</div>
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
	<div class="modal fade" id="myModalSerial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id='serialtitle'>Add Serial</h4>
				</div>
				<div class="modal-body" id='serialbody'>

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>
		$(document).ready(function() {
			refreshPage();
			var refresh  =  true;
			var time_interval = 10000;
			var payment_id = 0;
			$('#con_processed').hide();
			setInterval(function(){
				if(refresh){
				//	refreshPage();
				}
			},time_interval);

			$('body').on('click','#btnAddItem',function(){
				var item_id = $('#txtAddItem').val();
				var qty = $('#txtAddQty').val();
				var payment_id = $('#update_payment_id').val();
				$.ajax({
				    url:'../ajax/ajax_inventory.php',
				    type:'POST',
				    data: {functionName:'addReleasing',qty:qty,item_id:item_id,payment_id:payment_id},
				    success: function(data){
						alertify.alert(data);
					    getStockForReleasing(payment_id);
				    },
				    error:function(){

				    }
				});
			});

			$('body').on('click','.btnDeleteItem',function(){
				var con= $(this);
				var id = con.attr('data-id');
				var payment_id =  $('#update_payment_id').val();
				$.ajax({
					url:'../ajax/ajax_inventory.php',
					type:'POST',
					data: {functionName:'deleteReleasing',id:id,payment_id:payment_id},
					success: function(data){
						alertify.alert(data);
						getStockForReleasing(payment_id);
					},
					error:function(){

					}
				});
			});
			function formatItem(o) {

				if(!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> " + r[0] + "</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>" + r[2] + "</small></span>";
				}
			}
			function refreshPage(){
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {functionName:'forReleasing'},
				    success: function(data){
					    $('#holder').html(data);
				    },
				    error:function(){

				    }
				})
			}
			$('#myModal').on('hidden.bs.modal', function () {
				payment_id =0;
				refresh = true;
			});
			$('body').on('click','.getStocks',function(){
				payment_id = $(this).attr('data-payment_id');
				refresh = false;
				getStockForReleasing(payment_id);
			});
			function getStockForReleasing(){
				$('#myModal').modal('show');
				$('#mbody').html('Loading...');
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					data: {functionName:'getStockForReleasing', payment_id: payment_id},
					success: function(data){
						$('#mbody').html(data);
						$(".selectitem").select2({
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
												text: item.barcode + ":" + item.item_code + ":" + item.description + ":" + item.price,
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
					},
					error:function(){
						alert('Error in your connection. The page will be refresh.');
						location.reload();
					}
				});
			}
			$('body').on('click','.rackSelection',function(){
				var con = $(this);

			});
			$('body').on('click','#btnAssemble',function(){
				var con = $(this);
				var items = con.attr('data-items');
				localStorage['get_order_id_assemble_from_releasing'] = items;
				location.href='assemble-composite-item.php';
			});
			$('body').on('click','#btnRelease',function(){
				refresh = false;
				var btncon = $('#btnRelease');
				var btnoldval = btncon.html();
				btncon.html('Loading...');
				btncon.attr('disabled',true);
				if(payment_id){
					$.ajax({
						url:'../ajax/ajax_query2.php',
						type:'POST',
						data: {functionName:'processedStockForReleasing', payment_id: payment_id},
						success: function(data){
							alertify.alert(data);
							$('#myModal').modal('hide');
							refreshPage();
							payment_id =0;
							refresh = false;
							btncon.html(btnoldval);
							btncon.attr('disabled',false);
						},
						error:function(){
							alert('Error in your connection. The page will be refresh.');
							location.reload();
						}
					});
				}
			});

			$('body').on('click','#nav1',function(){
				payment_id = 0;
				refresh = true;

				refreshPage();
				$('#con_processed').hide();
				$('#con_pending').fadeIn(300);
			});
			$('body').on('click','#nav2',function(){
				payment_id = 0;
				refresh = false;
				$('#con_pending').hide();
				$('#con_processed').fadeIn(300);
				getPage(0);
			});
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			var timer;

			$("#txtSearch").keyup(function() {
				var searchtxt = $("#txtSearch");


				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);

			});

			function getPage(p){
				var search = $('#txtSearch').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder2').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'forReleasingPaging',search:search,cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder2').html(data);
					}
				});
			}

			$('body').on('click','.btnAddSerial',function(){
				var con = $(this);
				var item_id = con.attr('data-item_id');
				var qty = con.attr('data-qty');
				var payment_id = con.attr('data-payment_id');

				$.ajax({
					url: '../ajax/ajax_product.php',
					type: 'POST',
					data: {functionName: 'selectSerials', payment_id:payment_id, qty: qty, item_id:item_id},
					dataType: 'json',
					success: function(data) {
						$('#myModalSerial').modal('show');
						var ret = "";
						if(data.length){
							ret = "<table class='table' id='tblSerial'>";
							ret += "<thead><tr><th>#</th><th>Serial</th></tr></thead>";
							ret += "<tbody>";
							for(var i in data){
								ret += "<tr data-id='"+data[i].id+"' data-item_id='"+data[i].item_id+"'><td>"+(parseFloat(i) + parseFloat(1))+"</td><td><input class='form-control' placeholder='Serial' type='text' value='"+data[i].serial_no+"'></td></tr>"
							}
							ret += "</tbody>";
							ret += "</table>";
							ret += "<div class='text-right'><button data-payment_id='"+payment_id+"' class='btn btn-default' id='btnSaveSerial'>Save</button></div>"
						} else {
							ret = "Invalid request.";
						}
						$('#serialbody').html(ret);
					},
					error: function() {

					}
				});

			});
			$('body').on('click','#btnSaveSerial',function(){
				var con = $(this);
				var payment_id = con.attr('data-payment_id');
				button_action.start_loading(con);
				var arr = [];
				$('#tblSerial > tbody tr').each(function(){
					var row = $(this);
					var item_id = row.attr('data-item_id');
					var id = row.attr('data-id');
					var serial_no = row.children().eq(1).find('input').val();
					arr.push({
						id:id,
						item_id:item_id,
						serial_no:serial_no
					});
				});
				$.ajax({
					url: '../ajax/ajax_product.php',
					type: 'POST',
					data: {
						functionName: 'saveSerials',
						payment_id: payment_id,
						details: JSON.stringify(arr)
					},
					success: function(data) {
						alertify.alert(data);
						button_action.end_loading(con);
						$('#myModalSerial').modal('hide');
					},
					error: function() {
						button_action.end_loading(con);
					}
				})
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>