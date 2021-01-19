<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('tblast')) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Number Group</h1>

		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class='row'>
							<div class='col-md-6'>Number Group</div>
							<div class='col-md-6 text-right'>
								<button id='addNewItem' class='btn btn-default btn-sm'><i class='fa fa-plus'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">

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
					<div class="form-group">
						<strong>Number:</strong>
						<input type="text" class='form-control' id='txtGroup' placeholder='Enter Number'>
					</div>
					<div class="form-group">
						<strong>Group Name:</strong>
						<input type="text" class='form-control' id='txtNumber' placeholder='Enter Message'>
					</div>

					<div class="form-group">
						<button class='btn btn-default' id='btnSubmit'>Submit</button>
					</div>

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script>

		$(document).ready(function() {

			$('body').on('click','#addItem',function(){
				$('#txtGroup').val('');
				$('#txtNumber').val('');
				$('#myModal').modal('show');
			});

			$('body').on('click','#btnSubmit',function(){

				var group_name = $('#txtGroup').val();
				var  number = $('#txtNumber').val();
				if(group_name && number){

					$.ajax({
						url:'../ajax/ajax_sms.php',
						type:'POST',
						data: {functionName:'insertNumberInGroup',group_name:group_name,number:number},
						success: function(data){
							tempToast('info',data,'Info');
							$('#myModal').modal('hide');
							getList();
						},
						error:function(){

						}
					});

				} else {
					tempToast('error','Invalid request.','Error');
				}

			});

			getList();

			function getList(){

			}
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>