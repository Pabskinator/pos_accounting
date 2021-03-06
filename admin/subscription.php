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
		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Classes </h1>

	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('subscriptionflash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('subscriptionflash') . "</div>";
		}
	?>
	<div class="row">
		<div class="col-md-12">


			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">Subscription</div>
				<div class="panel-body">

						<div id="holdersubs"></div>
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
						<h3>Update Information</h3>
						<div class="form-group">
						<strong>Date From</strong>
						<input type="hidden" class='form-control' id='hid_id' >
						<input type="text" class='form-control' id='txtFrom' >
						</div>
						<div class="form-group">
						<strong>Date To</strong>
						<input type="text" class='form-control' id='txtTo' >
						</div>
						<div class="form-group">
							<button class='btn btn-default' id='btnSave'>SAVE</button>
						</div>

					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script>
		$(function(){
			getSubs();
			function getSubs(){
				$.ajax({
					url:'../ajax/ajax_member_service.php',
					type:'POST',
					data: {functionName:'getSubscription'},
					success: function(data){
						$('#holdersubs').html(data);
						$('#tblSubscription').dataTable({
							iDisplayLength: 50
						});
					},
					error:function(){

					}
				});
			}

			$('body').on('click','.btnUpdate',function(){
				var con = $(this);
				var id = con.attr('data-id');
				var date_from = con.attr('data-from');
				var date_to = con.attr('data-to');

				$('#hid_id').val(id);
				$('#txtFrom').val(date_from);
				$('#txtTo').val(date_to);
				$('#myModal').modal('show');
			});
			$('body').on('click','.btnDelete',function(){
				var con = $(this);
				var id = con.attr('data-id');
				alertify.confirm("Are you sure you want to delete this record?", function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_member_service.php',
							type:'POST',
							data: {functionName:'deleteSubscription',id:id},
							success: function(data){
								tempToast('info',data,'Info');
								getSubs();
							},
							error:function(){

							}
						});
					}
				});

			});

			$('body').on('click','#btnSave',function(){
				var id = $('#hid_id').val();
				var date_from = $('#txtFrom').val();
				var date_to = $('#txtTo').val();
				
				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'updateSubscription',id:id,date_from:date_from,date_to:date_to},
				    success: function(data){
					   tempToast('info',data,'Info');
					    $('#myModal').modal('hide');
				          getSubs();
				    },
				    error:function(){
				        
				    }
				});
				
			});
		})
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>