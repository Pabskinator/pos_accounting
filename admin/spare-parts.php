<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('spare_part')) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> <?php echo Configuration::getValue('spare_part')?> </h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')) {
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
			}
		?>
		<?php include 'includes/spare_nav.php'; ?>
		<div class="row">
			<div class="col-md-12">

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<div class="row">
							<div class="col-md-6">
								<?php echo Configuration::getValue('spare_part')?>
							</div>
							<div class="col-md-6 text-right">
								<button class='btn btn-default' id='btnDownload'><i class='fa fa-download'></i></button>
							</div>
						</div>

					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<input type="text" class='form-control' placeholder='SEARCH...' id='txtSearchSpare'>
							</div>
							<div class="col-md-3">
								<select  id="branch_id" name="branch_id" class="form-control">
									<option value=''></option>
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
							<div class="col-md-3">

							</div>
							<div class="col-md-3">

							</div>
						</div>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'>Edit Spare</h4>
					</div>
					<div class="modal-body" id='mbody'>
						<div class="form-group">
							<input type="hidden" id='edit_id' >
							<strong>Item: </strong>
							<p id='edit_set_item'></p>
						</div>
						<div class="form-group">
							<strong>Raw: </strong>
							<p id='edit_raw_item'></p>
						</div>
						<div class="form-group">
							<strong>Qty:</strong> <input type="text" id='edit_qty' class='form-control'>
						</div>
						<div class="form-group">
							<button class='btn btn-default' id='btnSave'>Save</button>
						</div>
					</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			$('#branch_id').select2({placeholder: 'Search Branch' ,allowClear: true});
			$('body').on('click','#btnDownload',function(){
				var search = $('#txtSearchSpare').val();
				window.open(
					'excel_downloader.php?downloadName=spareparts&search='+search,
					'_blank' //
				);

			});

			$('body').on('click','.btnDownloadDetail',function(){
				var id = $(this).attr('data-id');
				window.open(
					'excel_downloader_2.php?downloadName=sparepartsDetail&id='+id,
					'_blank' //
				);

			});
			
			$('body').on('click','.btnEdit',function(){
				var row = $(this).parents('tr');
				var id = row.attr('data-id');

				var item_code= row.children().eq(0).text();
				var raw = row.children().eq(1).text();
				var qty = row.children().eq(2).text();
				console.log(item_code);
				$('#edit_set_item').html(item_code);
				$('#edit_raw_item').html(raw);
				$('#edit_qty').val(qty);
				$('#edit_id').val(id);
				$('#myModal').modal('show');
			});

			$('body').on('click','.btnDelete',function(){
				var row = $(this).parents('tr');
				var id = row.attr('data-id');
				var qty = row.children().eq(2).text();
				var screenTop = $(document).scrollTop();


				alertify.confirm('Are you sure you want to delete this record?',function(e){
					if(e){
						$.post('../ajax/ajax_delete.php', {id: id, table: 'composite_items'}, function(data) {
							if(data == "true") {
								getPage($('#hiddenpage').val(),screenTop);
							}
						});
					}
				});

			});


			$('body').on('click','#btnSave',function(){
				var qty = $('#edit_qty').val();
				var id = $('#edit_id').val();
				if(isNaN(qty) || parseFloat(qty) < 0)
				{
					alertify.alert('Should be a valid number');
					$('#edit_qty').val(1);
					return;
				}
				var screenTop = $(document).scrollTop();
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {id:id,qty:qty, functionName:'updateSparePart'},
				    success: function(data){

					    $('#myModal').modal('hide');

					    alertify.alert(data,function(){
					        getPage($('#hiddenpage').val(),screenTop);
				        });

				    },
				    error:function(){

				    }
				});
			});

			getPage(0);

			$('body').on('click','.paging',function(){

				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);

			});

			var timer;
			$('body').on('keyup','#txtSearchSpare',function(){
				clearTimeout(timer);
				timer = setTimeout(function() {
					getPage(0);
				}, 1000);
			});
			$('body').on('change','#branch_id',function(){
				getPage(0);
			});
			function getPage(p,screenTop){
				$('.loading').show();
				var search = $('#txtSearchSpare').val();
				var branch_id = $('#branch_id').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend: function(){
						$('#holder').html('<p class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
					},
					data:{page:p,search:search,functionName:'sparepartsList',branch_id:branch_id,cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').html(data);
						$('.loading').hide();
						if(screenTop){

							setTimeout(function(){
								$('html, body').animate({
									scrollTop: screenTop
								}, 1000);
							},300);
						}

					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='spare-parts.php';
						$('.loading').hide();
					}
				});
			}

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>