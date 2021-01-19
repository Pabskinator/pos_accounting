<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head
	require_once '../libs/phpexcel/Classes/PHPExcel.php';
	require_once '../includes/admin/page_head2.php';

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> SMS module </h1>

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
								<button id='addItem' class='btn btn-default btn-sm'><i class='fa fa-plus'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">
						<div id='test2'></div>
						<p class='text-muted'>When uploading file, you should enter the cell phone number in column 1 and message in column 2.</p>
						<p class='text-muted'>Example of valid numbers: 09221234567, 9221234567, 639221234567</p>
						<div class="row">
							<form action="" method="POST" enctype="multipart/form-data">
								<div class="col-md-3">
									<div class="form-group">
									<input type="file" class='btn btn-default' name='file' id='file' required>
										<span class='help-block'>Enter excel file to upload</span>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
									<input type='submit' class='btn btn-primary' name='btnUpload' value='UPLOAD'>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
									</div>
								</div>
							</form>
						</div>
						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="search" class='form-control' placeholder='Search..'/>
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<select id="type" name="type" class="form-control">
										<option value='0'>Pending</option>
										<option value='1'>Sent</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">

							</div>
						</div>

						<?php


							if (Input::exists()){
								if(true) {

									$allowedExts = array("xls", "xlsx");
									$temp = explode(".", $_FILES["file"]["name"]);

									$extension = end($temp);

									if($_FILES["file"]["type"] == "application/vnd.ms-excel" || $_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" && in_array($extension, $allowedExts)) {
										$uploads_dir = "../tmp_files/";
										$uniqid = uniqid();
										$filename = $uniqid .  $_FILES["file"]["name"];

										$isUploaded = move_uploaded_file($_FILES["file"]["tmp_name"], $uploads_dir . $filename);
										if($isUploaded) {

											if(!file_exists($uploads_dir . $filename)) {
												exit("FILE NOT FOUND!." . PHP_EOL);
											}


											$objPHPExcel = PHPExcel_IOFactory::load($uploads_dir . $filename);

											$sheetNames = $objPHPExcel->getSheetNames();
											$smstosend = new Sms_to_send();

											echo "<h3>Upload Complete</h3>";
											echo "<ul class='list-group'>";
											foreach($sheetNames as $index => $name) {
												$objPHPExcel->setActiveSheetIndex($index);
												$lastRow = $objPHPExcel->setActiveSheetIndex($index)->getHighestRow();
												$lastColumn = $objPHPExcel->setActiveSheetIndex($index)->getHighestColumn();
												$startRow = 1;
												if($lastRow > 1000){
													$lastRow = 1000;
												}
												for($row = $startRow; $row <= $lastRow; $row++) {
													$colNum= "A";
													$colMsg = "B";
													$number = $objPHPExcel->getActiveSheet()->getCell($colNum.$row)->getValue();
													$msg = $objPHPExcel->getActiveSheet()->getCell($colMsg.$row)->getValue();
													if(substr($number,0,1) != "0" && strlen($number) == 10){
														$number = "0" . $number;

													}

													if(substr($number,0,2) == "63"){
														$number = "0" . substr($number ,2);
													}

													if(substr($number,0,1) == "0" && strlen($number) == 11 && $msg){

														echo "<li class='list-group-item'>$number <br> $msg <span class='text-success pull-right'>Valid</span></li>";

														$smstosend->create([
															'msg' => $msg,
															'number' => $number,
															'status' => 0,
															'created' => time()
														]);

													} else {
														echo "<li class='list-group-item'>$number <br> $msg  <span class='text-danger  pull-right'>Invalid</span></li>";
													}
												}
											}
											echo "</ul>";
										} else {
											echo "<div class='alert alert-info'>Upload failed.</div>";
										}
									}else {
										echo "<div class='alert alert-info'>Invalid file type.</div>";
									}
								}else {
									echo "<div class='alert alert-info'>Invalid token.</div>";
								}
							}
						?>

						<input type="hidden" id="hiddenpage" value='0'/>
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
					<div class="form-group">
						<strong>Number:</strong>
						<input type="text" class='form-control' id='txtNumber' placeholder='Enter Number'>
					</div>
					<div class="form-group">
						<strong>Message:</strong>
						<input type="text" class='form-control' id='txtMsg' placeholder='Enter Message'>
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
				 $('#txtNumber').val('');
				 $('#txtMsg').val('');
				$('#myModal').modal('show');
			});
			$('body').on('click','#btnSubmit',function(){
				var num = $('#txtNumber').val();
				var msg = $('#txtMsg').val();
				if(num && msg){
					$.ajax({
						url:'../ajax/ajax_sms.php',
						type:'POST',
						data: {functionName:'insertToSendMessage',num:num,msg:msg},
						success: function(data){
							tempToast('info',data,'Info');
							$('#myModal').modal('hide');
							getPage($('#hiddenpage').val());
						},
						error:function(){

						}
					});
				} else {
					tempToast('error','Invalid request.','Error');
				}

			});

			getPage(0);
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});

			var timer;
			$("#search").keyup(function(){
				var searchtxt = $("#search");
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);
			});

			$('body').on('change','#type',function(){
				getPage(0);
			});

			function getPage(p){

				var search = $('#search').val();
				var type = $('#type').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'smsToSend',cid: <?php echo $user->data()->company_id; ?>,search:search,type:type},
					success: function(data){
						$('#holder').html(data);
					}
				});

			}


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>