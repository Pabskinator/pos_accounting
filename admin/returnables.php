<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('mem_equipment')) {
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
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Returnables
		</h1>

	</div>
	<?php include 'includes/member_equipment_nav.php' ?>
	<div class="row">
		<div class="col-md-12">


			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">
					<div class="row">
						<div class="col-md-6">
							List
						</div>
						<div class="col-md-6 text-right">
							<button class='btn btn-default btn-sm' id='btnAdd'><i class='fa fa-plus'></i></button>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div id="holder"></div>
					<?php

					?>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'></h4>
					</div>
					<div class="modal-body" id='mbody'>
						<strong>Item</strong>
						<input type="text" class='form-control selectitem' id='item_id' >
						<strong>Item when return</strong>
						<input type="text" class='form-control selectitem' id='ret_item_id'>
						<strong>Add When Return?</strong>
						<select name="add_inv" id="add_inv" class='form-control'>
							<option value="0">No</option>
							<option value="1">Yes</option>
						</select> <br>
						<button class='btn btn-default' id='btnSave'>Save</button>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script>

		$(function(){

			$('body').on('click','#btnAdd',function(){
				$('#item_id').select2('val',null);
				$('#ret_item_id').select2('val',null);
				$('#add_inv').val('0');
				$('#myModal').modal('show');


			});
			getRecord();
			$('body').on('click','#btnSave',function(){
				var item_id = $('#item_id').val();
				var ret_item_id = $('#ret_item_id').val();
				var add_inv = $('#add_inv').val();

				$.ajax({
				    url:'../ajax/ajax_returnable.php',
					type:'POST',
					data: {functionName:'addNew',item_id:item_id,ret_item_id:ret_item_id,add_inv:add_inv},
					success: function(data){
						tempToast('info',data,'Info');
						$('#myModal').modal('hide');
						getRecord();
					},
					error:function(){

				    }
				});

			});

			function getRecord(){
				$.ajax({
					url:'../ajax/ajax_returnable.php',
					type:'POST',
					data: {functionName:'getRecord'},
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