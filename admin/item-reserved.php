<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head
	require_once '../includes/admin/page_head2.php';
	if(false) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Pending Item
			</h1>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"></div>
					<div class="panel-body">

						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control' placeholder='Search Item' id='txtSearch'>
								</div>

							</div>
							<div class="col-md-3">
								<div class="form-group">
									<select id="branch_id" name="branch_id" class="form-control">

										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = $user->data()->branch_id;
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
							<div class="col-md-3"></div>
							<div class="col-md-3 text-right">
								<button class='btn btn-default' id='btnDownload'>Download</button>
							</div>
						</div>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			$("#branch_id").select2({
				placeholder: 'Choose Branch',
				allowClear: true
			});
			getPage(0);
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			var timer;
			$("#txtSearch").keyup(function(){

				var searchtxt = $("#txtSearch");

				clearTimeout(timer);
				timer = setTimeout(function() {
					if(searchtxt.val()){
						searchtxt.val(searchtxt.val().trim());
					}
					getPage(0);
				}, 1000);
			});
			$('body').on('change','#branch_id',function(){
				getPage(0);
			});
			function getPage(p){
				var s = $('#txtSearch').val();
				var branch_id = $('#branch_id').val();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,s:s,branch_id:branch_id,functionName:'itemReservedPaginate',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}

			function downloadExcel(){
				var s = $('#txtSearch').val();
				var branch_id = $('#branch_id').val();

				window.open(
					'excel_downloader.php?downloadName=pendingItems&s='+s+"&branch_id="+branch_id,
					'_blank' //
				);
			}

			$('body').on('click','#btnDownload',function(){
				downloadExcel();
			});


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>