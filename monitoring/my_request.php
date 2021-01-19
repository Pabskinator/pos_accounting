<?php
	require_once '../includes/monitoring/page_head.php';
	if(!$user->hasPermission('dashboard')) {
		// redirect to denied page
		//	Redirect::to(1);
	}
	if(isset($_GET['process'])) {
		$process_id = $_GET['process'];
	} else {
		$process_id = 0;
	}
	function reArrayFiles(&$file_post) {
		$file_ary = array();
		$file_count = count($file_post['name']);
		$file_keys = array_keys($file_post);

		for($i = 0; $i < $file_count; $i++) {
			foreach($file_keys as $key) {
				$file_ary[$i][$key] = $file_post[$key][$i];
			}
		}

		return $file_ary;
	}

	if(isset($_POST['sendfile'])) {

		$file_ary = reArrayFiles($_FILES['mon_img']);
		$mon_id = $_POST['monitoring_id'];
		$step_id = $_POST['step_id'];
		$att = new Attachment();

		foreach($file_ary as $file) {

			if($file['type'] == 'image/jpeg' || $file['type'] == 'image/jpg' || $file['type'] == 'image/png' || $file['type'] == 'application/pdf') {
				$file_location = $mon_id . '_' . $step_id . '_' . time() . '_' . $file["name"];
				move_uploaded_file($file["tmp_name"], "attachments/" . $file_location);
				$att->create(array('filename' => $file_location, 'step_id' => $step_id, 'monitoring_id' => $mon_id, 'created' => time(), 'modified' => time(), 'is_active' => 1, 'company_id' => $user->data()->company_id));


			}
		}
	}

?>


	<!-- Sidebar --><?php include_once '../includes/monitoring/sidebar.php'; ?>	<!-- Page content -->
	<div id="page-content-wrapper" style='padding-top:20px;'>

		<!-- Keep all page content within the page-content inset div! -->
		<div class="container-fluid">
			<?php
				$myCurRequest = new Monitoring();
				$mreq = $myCurRequest->getMyRequest($user->data()->id);
				if($mreq) {
					?>					<h3>My Request</h3>
					<div class="panel panel-primary">
						<div class="panel-heading">Request Monitoring</div>
						<div class="panel-body">
							<table class="table table-bordered">
								<tr>
									<th>PROCESS</th>
									<th>REQUEST ID</th>
									<th>DATE REQUESTED</th>
									<th>PENDING AT</th>
									<th>Attachments</th>
									<th>Reports</th>
									<th>Action</th>
								</tr> <?php
									foreach ($mreq as $req) {
									$mprocess = new Process($req->process_id);

								?>
								<tr>
									<td><?php echo  strtoupper($mprocess->data()->name); ?></td>
									<td><span class='badge'><?php echo $req->id; ?></span></td>
									<?php
										$step = new Steps();
										$stepname = $step->getStepName($req->process_id,$req->current_step);
									?>
									<td><?php echo date('m/d/Y H:i:s A',$req->created); ?> </td>
									<td><?php echo $stepname->name; ?></td>
									<td>
										<?php
											$att = new Attachment();
											$req_attach = $att->getAttachments($req->id);
											if($req_attach){
												echo "<table class='table'>";
												foreach ($req_attach as $value) {

													?>
													<tr>
													<td>
														<a  class='btn btn-default btn-sm' href="attachments/<?php echo $value->filename; ?>" target='_blank'>
															<span class='glyphicon glyphicon-paperclip'></span>
															<?php echo substr($value->filename,17); ?>
														</a>
													</td>
													<td>
													<?php
												if($stepname->has_attachment == 1){
													?>
														<button class='btn btn-danger btn-sm deteletAttachment' data-id='<?php echo $value->id; ?>' >
															<i class='fa fa-remove'></i>
														</button>
														<?php
												}
												?>
													</td>
													</tr>
													<?php
												}
												echo "</table>";
											} else {
												?>
												No Attachment
												<?php
											}
										?>
									</td>
									<td>
										<?php
											$remarks_list = new Remarks_list();
											$remarks = $remarks_list->getServices($req->id,'monitoring',$user->data()->company_id);
											if($remarks){
												echo "<table class='table'>";
												$ctrrep = 1;
												foreach($remarks as $rem){

												echo "<tr>";
												echo "<td>";
													echo "<button class='btn btn-default btn-sm btnShowReport' data-text='$rem->remarks'><i class='fa fa-paperclip'></i> Report $ctrrep</button> ";
												echo "</td>";
												echo "<td>";
												?>
												<button class='btn btn-danger btn-sm deleteReport' data-id='<?php echo $rem->id; ?>' >
															<i class='fa fa-remove'></i>
												</button>
												<?php
												echo "</td></tr>";
												$ctrrep++;
												}
												echo "</table>";
											}
										?>
									</td>
									<td>



										<button style='margin:3px;' type='button' data-step='<?php echo $req->id; ?>'  class='btn btn-default btn-sm showData'>
											<span class='glyphicon glyphicon-list-alt'></span> Details
										</button>
										<button style='margin:3px;' type='button' data-step='<?php echo $req->id; ?>'  class='btn btn-default btn-sm cancelRequest'>
											<span class='glyphicon glyphicon-remove'></span> Cancel
										</button>
										<?php
											if($stepname->has_attachment == 1){
												?>


										<!-- Modal Start-->

									<button style='margin:3px;' type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal<?php echo $req->id; ?>">
										<i class='fa fa-upload'></i> Upload
									</button>
										<!-- Modal -->
										<div class="modal fade" id="myModal<?php echo $req->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										  <div class="modal-dialog">
											<div class="modal-content">
											  <div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title">Upload</h4>
											  </div>
											  <div class="modal-body">
													<form action='' method='post' enctype='multipart/form-data'>
												<input type='hidden' name='monitoring_id' value='<?php echo $req->id; ?>'>
												<input type='hidden' name='step_id' value='<?php echo $stepname->id; ?>'>
												<div  id='file_container<?php echo $req->id; ?>mon'>
													<input name='mon_img[]' type='file' class='btn btn-default'  style='display:block' required />
												</div>
												<input type='button' style='margin-top:3px;' class='btn btn-default' mon_id='<?php echo $req->id; ?>' f='mon' value='Add more' id='addmoreimg'>
												<hr />
												<input type='submit' class='btn btn-default'  name='sendfile' value='Upload' />
												</form>
											  </div>
											</div>
										  </div>
										</div>
										<!-- Modal End-->
												<?php
											}
										?>
										<?php
										if($stepname->has_report == 1){
											?>
										<button style='margin:3px;' type='button' data-step='<?php echo $req->id; ?>'  class='btn btn-default btn-sm addReport'>
											<span class='glyphicon glyphicon-pencil'></span> Report
										</button>
											<?php
										}
										?>
									</td>
									<?php
										}
								?>
							</table>
						</div>
					</div>				<?php
				} else {
					?>
					<div class="container-fluid">
						<div class="alert alert-info">
							No Request Yet.
						</div>
					</div>				<?php
				}
			?>
		</div>
	</div>	<!-- Modal bootstap -->	<!-- Modal bootstap -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Modal title</h4>
				</div>
				<div class="modal-body" id="mbody">
					...
				</div>
				<div class="modal-footer" id='mfooter'>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="myModalReport" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Details</h4>
				</div>
				<div class="modal-body" id="rbody">

				</div>
			</div>
		</div>
	</div>
	<div id='tempHolder'></div>

	<!-- Modal end -->
	<script type="text/javascript">

		$(function() {

			$('.showData').click(function() {
				// get data via ajax

				var step = $(this).attr('data-step');
				$('#myModalLabel').html("Details");
				$.ajax({
					url: '../ajax/ajax_showData.php', type: 'POST', beforeSend: function() {
						$("#mbody").html('Loading...');
					}, data: {mon_id: step}, success: function(data) {
						$("#mbody").html(data);
					}
				});
				$('#myModal').modal('show');
			});

			$('.cancelRequest').click(function() {
				// get data via ajax

				var step = $(this).attr('data-step');
				$.ajax({
					url: 'query.php',
					type: 'POST',
					data: {mon_id: step, functionName: 'cancelRequest'},
					success: function(data) {
						alert(data);
						location.href = 'my_request.php';
					}
				});
				$('#myModal').modal('show');
			});
			$('body').on('click', '#addmoreimg', function() {
				var mon_id = $(this).attr('mon_id');
				var from = $(this).attr('f');

				$("#file_container" + mon_id + from).append("<input style='margin-top:3px;' name='mon_img[]' class='btn btn-default' type='file' style='display:block' required /> <input type='button' style='margin-top:3px;' class='btn btn-default removeUpload' value='remove'>");
			});
			$('body').on('click', '.removeUpload', function() {
				$(this).prev().remove();
				$(this).remove();
			});
			$('body').on('click','.deteletAttachment',function(){
				var con = $(this);
				var id = con.attr('data-id');
				alertify.confirm("Are you sure you want to delete this record?",function(e){
					if(e){
						$.ajax({
							url:'query.php',
							type:'POST',
							data: {functionName:'deleteAttachment',id:id},
							success: function(data){
								location.href='my_request.php';
							},
							error:function(){
								location.href='my_request.php';
							}
						});
					}
				});
			});

			$('body').on('click','.addReport',function(){
				var id = $(this).attr('data-step');
				$('#myModalLabel').html("Add Report");
				var textArea = "<textarea class='form-control' id='txtReport'></textarea>";
				var hid_id = "<input type='hidden' value='"+id+"' id='hid_id_report'>";
				var saveBtn = "<button class='btn btn-default' id='saveReports'>Save</button>";
				if($("#txtReport").length) $("#txtReport").tinymce().remove();
				$('#mbody').html(textArea+ hid_id) ;
				$('#mfooter').html(saveBtn);
				$('#txtReport').html('').tinymce({
					height: 250
				});
				$('#myModal').modal('show');
			});

			$('body').on('click','#saveReports',function(){
				var remarks = $('#txtReport').val();
				var id = $('#hid_id_report').val();
				if(remarks){
					$.ajax({
						url:'query.php',
						type:'POST',
						data: {functionName:'saveReports',id:id,remarks:remarks},
						success: function(data){
							alert(data);
							location.href='my_request.php';
						},
						error:function(){

						}
					});
				}
			});
			$('body').on('click','.btnShowReport',function(){
				var rep = $(this).attr('data-text');
				$('#rbody').html(rep);
				$('#myModalReport').modal('show');
				/*$('#tempHolder').html(rep);
				var
					form = $('#tempHolder'),
					cache_width = form.width(),
					a4  =[ 595.28,  841.89];  // for a4 size paper width and height

				$('body').scrollTop(0);
				createPDF();
				//create pdf
				function createPDF(){
					getCanvas().then(function(canvas){
						var
							img = canvas.toDataURL("image/png"),
							doc = new jsPDF({
								unit:'px',
								format:'a4'
							});
						doc.addImage(img, 'JPEG', 20, 20);
						doc.save('techumber-html-to-pdf.pdf');
						form.width(cache_width);
					});
				}

				// create canvas object
				function getCanvas(){
					form.width((a4[0]*1.33333) -80).css('max-width','none');
					return html2canvas(form,{
						imageTimeout:3000,
						removeContainer:true
					});
				} */
			});
			$('body').on('click','.deleteReport',function(){
				var id = $(this).attr('data-id');
				alertify.confirm("Are you sure you want to delete this report?",function(e){
					if(e){
						$.ajax({
							url:'query.php',
							type:'POST',
							data: {functionName:'deleteReport',id:id},
							success: function(data){
								location.href='my_request.php';
							},
							error:function(){
								location.href='my_request.php';
							}
						});
					}
				});
			});
		});

	</script>


<?php require_once '../includes/monitoring/page_tail.php'; ?>