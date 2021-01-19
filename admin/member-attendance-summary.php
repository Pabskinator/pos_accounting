<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('member')) {
		// redirect to denied page
		Redirect::to(1);
	}

?>


	<div id='member_util' >
	<!-- Page content -->
	<div id="page-content-wrapper" >

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Member attendance history
				</h1>

			</div>
			<div class="row">
				<div class="col-md-12">

				</div>
				<div class="col-md-12">

					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">
							<div class='row'>
								<div class='col-md-6'>List</div>
								<div class='col-md-6 text-right'>

								</div>
							</div>
						</div>
						<div class="panel-body">
							<form action="" method="POST">
							<div class='row'>
								 <div class="col-md-3">
									 <input type="text" class='form-control' id='member_id' name='member_id'>
								 </div>
								<div class='col-md-3'>
									<input type="submit" name='btnSubmit' value='Submit' class='btn btn-primary'>
								</div>
							</div>
							</form>
							<?php
								if(Input::exists()){
									$member_id = Input::get('member_id');
									$member_cls = new Service_attendance();

									$result = $member_cls->getAttendanceByMember($member_id);
									if($result){
										$member_name = "";
										$data = "<table class='table table-bordered'>";
										$data.= "<thead><tr><th>Time In</th><th>Time Out</th></tr></thead>";
										echo "<tbody>";
										foreach($result as $res){
											$data.= "<tr>";
											$member_name = $res->member_name;
											$data.= "<td style='border-top: 1px solid #ccc;'>".date('m/d/Y H:i:s A',$res->time_in)."</td>";
											$data.= "<td style='border-top: 1px solid #ccc;'>".date('m/d/Y H:i:s A',$res->time_out)."</td>";

											$data.= "</tr>";
										}
										$data.= "</tbody>";

										$data.= "</table>";
										echo "<p class='text-right'><button id='print' class='btn btn-default btn-sm'><i class='fa fa-print'></i> Print</button></p>";
										echo "<div id='printtable'>";
										echo "<br><h4>Client: $member_name</h4>";
										echo $data;
										echo "</div>";

									} else {
										echo "<br><div class='alert alert-danger'>No attendance</div>";
									}


								} else {
									echo "<br><div class='alert alert-info'>Choose member</div>";
								}
							?>
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
	</div>

	<script>
	$(function(){
		$('#member_id').select2({
			placeholder: 'Search client', allowClear: true, minimumInputLength: 2,

			ajax: {
				url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
					return {
						q: term, functionName: 'members'
					};
				}, results: function(data) {
					return {
						results: $.map(data, function(item) {

							return {
								text: item.lastname ,
								slug: item.lastname + ", " + item.firstname + " " + item.middlename,
								id: item.id
							}
						})
					};
				}
			}
		});
		$('body').on('click','#print',function(){
			var img = "<img src='http://safehouse.apollosystems.ph/css/img/logo.jpg'  style='width:35px;height: 35px' />";
			var html = "<h3 class='text-center'> "+img+"  Safehouse Fight Academy</h3>";
			html += "<p class='text-center'>Attendance Monitoring</p>";
			html += $('#printtable').html();
			printWithStyle(html);
		});
		
		function printWithStyle(data){
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
	});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>