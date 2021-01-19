<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('consumable_admin')){
		//redirect to denied page
		Redirect::to(1);
	}


?>


	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Consumable list
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
			}
		?>
		<div class="row">
			<div class="col-md-12">

				<div class="text-right" style='margin-bottom: 3px;'><button class='btn btn-default' id='btnAddConsumables'><span class='glyphicon glyphicon-plus'></span> Add Consumable</button></div>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Consumable list</div>
					<div class="panel-body">
						<p class='text-muted'><i class='fa fa-info'></i> Choose Client To Batch Update Records</p>
						<div class="row">
							<div class="col-md-3">
								<input type="text" id="member_id_search"  class='form-control' />
							</div>

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
						<h4 class="modal-title" id='mtitle'>Add Consumables</h4>
					</div>
					<div class="modal-body" id='mbody'>
						<div class="form-group">
							<strong>Member</strong>

							<input  name="member_id" id="member_id" class='form-control' />

						</div>
						<div class="form-group">
							<strong>Amount</strong> <input type="text" id='amount' class='form-control'>
						</div>
						<div class="form-group">
							<strong>Override Date</strong> <input autocomplete="off" type="text" id='override_date' class='form-control'>
						</div>
						<div class="form-group">
							<button class='btn btn-primary' id='btnSubmit'> <span class='glyphicon glyphicon-save'></span> Submit</button>
						</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<div class="modal fade" id="myModalBatch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Batch Update</h4>
					</div>
					<div class="modal-body">

						<div class="form-group">
							<strong>Amount</strong> <input  type='text' class='form-control' id='btAmount' >
							<input type="hidden" id='btIDS'>
						</div>
						<div class="form-group">
							<button class='btn btn-primary' id='btSave'>SAVE</button>
							<p class='text-muted'>Verify the records before saving.</p>
						</div>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<button class='btn btn-primary' id='batchUpdateButton' style='position:fixed;bottom: 30px;right: 10px;display: none;'>Batch Update</button>

	<script>
		$(document).ready(function(){
			$('#member_id_search').select2({
				placeholder: 'Search Client' , allowClear: true, minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname + ", ",
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});

			$('#override_date').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#override_date').datepicker('hide');
			});

			$('#member_id').select2({
				placeholder: 'Search Client' ,
				allowClear: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname + ", " + item.sales_type_name,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
			
			$('body').on('change','#member_id_search',function(){
				getPage(0);
			});
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				getPage(page);
			});


			$('body').on('change','.chkIds',function(){
				var arr = [];
				$('.chkIds').each(function(){
					var chk = $(this);

					if(chk.is(':checked')){
						arr.push(chk.attr('data-id'))
					}
				})
				if(arr.length > 0){
					$('#batchUpdateButton').show();
				} else {
					$('#batchUpdateButton').hide();
				}
			});

			$('body').on('click','#batchUpdateButton',function(){
				var arr = [];
				$('.chkIds').each(function(){
					var chk = $(this);

					if(chk.is(':checked')){
						arr.push(chk.attr('data-id'))
					}
				});
				if(arr.length > 0){
					$('#btAmount').val('');
					$('#btIDS').val(JSON.stringify(arr));
					$('#myModalBatch').modal('show');
				} else {
					alert("Please choose transaction first.");
				}
			});

			$('body').on('click','#btSave',function(){
				var ids= $('#btIDS').val();
				var amount= $('#btAmount').val();

				alertify.confirm("Are you sure you want to batch update these records?", function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_query2.php',
							type:'POST',
							data: {functionName:'updateConsumableAmountBatch',amount:amount,ids:ids},
							success: function(data){
								tempToast('info',data,'INFO');
								$('#myModalBatch').modal('hide');
								getPage(0);
							},
							error:function(){

							}
						})
					}
				})

			});

			getPage(0);

			function getPage(p){
				var member_id = $('#member_id_search').val();
				$('#batchUpdateButton').hide();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,member_id:member_id,functionName:'consumablesListPaginate',cid: <?php echo $user->data()->company_id; ?>},
					success: function(data){
						$('#holder').html(data);

					}
				});
			}
			$('body').on('click','.btnUpdate',function(){
				var row = $(this).parents('tr');
				var id = $(this).attr('data-id');
				var amount = row.children().eq(3).find('input').val();
				$.ajax({
				    url:'../ajax/ajax_query2.php',
				    type:'POST',
				    data: {id:id,functionName:'updateConsumableAmount',amount:amount},
				    success: function(data){
				        alertify.alert(data,function(){
					        location.href='consumable-admin.php';
				        });

				    },
				    error:function(){

				    }
				})

			});

			$('body').on('click','#btnAddConsumables',function(){
				$('#myModal').modal('show');
			});
			$('body').on('click','#btnSubmit',function(){
				var member_id = $('#member_id').val();
				var amount = $('#amount').val();
				var override_date = $('#override_date').val();
				if(member_id && amount){
					var btncon = $(this);
					var btnoldval = btncon.html();
					btncon.html('Loading...');
					btncon.attr('disabled',true);
					$.ajax({
					    url:'../ajax/ajax_query2.php',
					    type:'POST',
					    data: {functionName:'addMemberConsumable', override_date:override_date,member_id:member_id,amount:amount},
					    success: function(data){
					        alertify.alert(data,function(){
						        btncon.html(btnoldval);
						        btncon.attr('disabled',false);

						        getPage(0);
						        $('#myModal').modal('hide');
					        });
					    },
					    error:function(){

					    }
					})
				} else {
					alertify.alert('Invalid form data');
				}
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>