<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sales')) {
		// redirect to denied page
		Redirect::to(1);
	}

	$citymun = new City_mun();
	$provinces = $citymun->getProvinces();

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Cities and municipalities </h1>

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
							<div class='col-md-6'>List</div>
							<div class='col-md-6 text-right'>
								<button class='btn btn-default' id='btnDownload'><i class='fa fa-download'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">

						<div id='test2'></div>
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
									<select class='form-control' name="province" id="province">
										<option value=""></option>
										<?php
											if($provinces){
												foreach($provinces as $p){
													echo "<option value='$p->provDesc'>$p->provDesc</option>";
												}
											}
										?>
									</select>
								</div>
							</div>
							</div>
						<div>
							<h4>Note:</h4>
							<ul>
								<li>Enter -1 if the payment method is not available in that location</li>
								<li>Enter 0 if it's free</li>
								<li>Enter the desired amount if it has delivery charge.</li>
							</ul>
						</div>

						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>

					</div>


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
					<div class="row">
						<div class="col-md-12">
							<input type="hidden" id='cityID'>
							<div class="form-group">
								<h4 id='cityName'></h4>
							</div>
						</div>
						<div class="col-md-12">

							<div class="form-group">
								<strong>Charge Cash</strong>
								<input type="text" class='form-control' id="chargeCash">
							</div>
						</div>
						<div class="col-md-12">

							<div class="form-group">
								<strong>Charge BT</strong>
								<input type="text" class='form-control' id="chargeBT">
							</div>
						</div>
						<div class="col-md-12">

							<div class="form-group">

								<button class='btn btn-primary' id='btnSave'>SAVE</button>
							</div>
						</div>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {
			getPage(0);
			function getPage(p){
				var search = $('#search').val();
				var province = $('#province').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,province:province,functionName:'cityMun',cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}
			$('body').on('click','.btnEditCharge',function(){
				var  con = $(this);
				var id = con.attr('data-id');
				var chargeCash = con.attr('data-chargeCash');
				var chargeBT = con.attr('data-chargeBT');
				var cityname = con.attr('data-cityname');
				$('#cityID').val(id);
				$('#cityName').html(cityname);
				$('#chargeCash').val(chargeCash);
				$('#chargeBT').val(chargeBT);
				$('#myModal').modal('show');
			});
			$('body').on('click','#btnSave',function(){
				var id = $('#cityID').val();
				var chargeCash = $('#chargeCash').val();
				var chargeBT = $('#chargeBT').val();

				$.ajax({
				    url:'../ajax/ajax_member_service.php',
				    type:'POST',
				    data: {functionName:'saveCity',id:id,chargeCash:chargeCash,chargeBT:chargeBT},
				    success: function(data){
					    if(data == '1'){
						    tempToast('info','Record updated successfully','Info');
						    $('#myModal').modal('hide');
						    var r = $('#row'+id);
						    r.children().eq(1).html(number_format(chargeCash,2));
						    r.children().eq(2).html(number_format(chargeBT,2));
					    } else {
						    tempToast('error','Invalid request','Info');
					    }

				    },
				    error:function(){
				        
				    }
				});
			});

			$('body').on('change','#province',function(){
				getPage(0);
			});
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});
			$("#search").keyup(function(){
				getPage(0);
			});
			$('#province').select2({
				placeholder:"Select Province",
				allowClear: true
			})


			$('body').on('click','#btnDownload',function(){
				window.open(
					'excel_downloader.php?downloadName=city',
					'_blank' //
				);
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>