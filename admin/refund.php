<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';


?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Refund</h1>

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

							</div>
						</div>
					</div>
					<div class="panel-body">


						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<select id="branch_id" name="branch_id" class="form-control">
										<option value=''>All</option>
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
									<input type="text" id='dt_from' class='form-control' placeholder='From'>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" id='dt_to' class='form-control' placeholder='To'>
								</div>
							</div>

						</div>



						<input type="hidden" id="hiddenpage" value='0'/>
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
						<input type="hidden" id='update_id' value=''>
						<strong>Amount</strong>
						<input type="text" class='form-control' placeholder='Amount' id='update_amount' >
						<div class='form-group'>
							<br>
							<button id='saveRefund' class='btn btn-primary'>Save</button>
						</div>
					</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {

			getPage(0);

			$('body').on('change','#branch_id',function(){
				getPage(0);
			});

			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
				withDate();
			});

			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
				withDate();
			});

			function withDate(){
				if($('#dt_from').val() && $('#dt_to').val()){
					getPage(0);
				}
			}

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


			function getPage(p){

				var search = $('#search').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var branch_id = $('#branch_id').val();

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{
						page:p,
						functionName:'refunds',
						branch_id:branch_id,
						dt_from:dt_from,
						dt_to:dt_to,
						cid: <?php echo $user->data()->company_id; ?>,
						search:search
					},
					success: function(data){
						$('#holder').html(data);
					}
				});

			}

			$('body').on('click','#saveRefund',function(){

				var id = $('#update_id').val();
				var amount = $('#update_amount').val();

				$.ajax({
				    url:'../ajax/ajax_returnable.php',
				    type:'POST',
				    data: {functionName:'saveRefund',id:id,amount:amount},
				    success: function(data){
					    $('#myModal').modal('hide');
					    getPage(0);
				        alert(data);
				    },
				    error:function(){

				    }
				});

			});

			$('body').on('click','.btnUpdate',function(){
				var con = $(this);
				var id = con.attr('data-id');
				var amount = con.attr('data-amount');

				$('#update_id').val(id);
				$('#update_amount').val(amount);

				$('#myModal').modal('show');

			});

		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>