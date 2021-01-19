<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	//if(!$user->hasPermission('createorder')) {
	// redirect to denied page
	//	Redirect::to(1);
	//}

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
	<div class="content-header">
		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Item list </h1>
	</div>
	<div id="orderholder">

	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}
	?>
	<?php include 'includes/spare_nav.php'; ?>
	<div class="panel panel-primary">
		<!-- Default panel contents -->
		<div class="panel-heading">Disassemble item list</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-3">
					<select name="status" id="status" class='form-control'>
						<option value="1">Pending</option>
						<option value="2">Processed</option>
						<option value="3">Cancelled</option>
					</select>
				</div>
			</div>
			<div id="con_list" style='margin-top:10px;'>

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
	      <!-- end page content wrapper-->


	<script>
		$(function() {
			getList(1);
			$('body').on('change','#status',function(){
				var s = $(this).val();
				getList(s);
			});
			function getList(status){
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					data: {functionName:'getDisassembleList',status:status},
					beforeSend: function(){
						$('.loading').show();
						$('#con_list').html('Loading...');
					},
					success: function(data){
						$('#con_list').html(data);
						$('.loading').hide();
					},
					error:function(){

						$('#con_list').html('Error fetching data.');
						$('.loading').hide();
					}
				})
			}
			$('body').on('click','#btnCancel',function(){
				var con = $(this);
				var oldval = con.html();
				alertify.confirm("Are you sure you want to cancel this transaction?",function(e){
					var id = con.attr('data-id');
					con.html('Loading...');
					con.attr('disabled',true);
					$.ajax({
						url:'../ajax/ajax_query2.php',
						type:'POST',
						data: {functionName:'disassembleListCancel',id:id},
						success: function(data){

							$('#myModal').modal('hide');
							alertify.alert(data);
							var s = $('#status').val();
							getList(s);
						},
						error:function(){

						}
					});


				});

			});
			$('body').on('click','.btnDetails',function(){
				var id = $(this).attr('data-id');
				$('#myModal').modal('show');
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					beforeSend: function(){
						$('#mbody').html('Loading...');
					},
					data: {functionName:'getDisassembleDetails',id:id },
					success: function(data){
						$('#mbody').html(data);
						$('.rack_class').select2();
					},
					error:function(){

					}
				});
			});
			$('body').on('click','#btnConvert',function(){
				var id = $(this).attr('data-id');

				alertify.confirm('Are you sure you want to convert raw materials to set items?',function(e){
					if(e){
						var lst = [];
						$('#tblDetails tbody > tr').each(function(){
							var row = $(this);
							var item_id = row.attr('data-item_id');
							var qty = row.attr('data-qty');
							var tblParts = row.children().eq(4).find('.disassembleParts');
							var partsTblId= tblParts.attr('id');
							$('#'+partsTblId+ ' tr').each(function(){
								var rpart = $(this);
								var item_part = rpart.attr('data-item_id');
								var qty = rpart.attr('data-qty');
								var rack_id = rpart.children().eq(2).find('select').val();
								lst.push({item_part:item_part,qty:qty,rack_id:rack_id});
							});

						});
						lst = JSON.stringify(lst);
						$.ajax({
							url:'../ajax/ajax_query2.php',
							type:'POST',
							data: {functionName:'disassembleSpareparts',id:id,lst:lst},
							success: function(data){
								alertify.alert(data,function(){
									location.href='disassemble-list.php';
								});
							},
							error:function(){

							}
						});
					}

				});
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>