<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';

	if(!$user->hasPermission('consumablefree_admin')){
		//redirect to denied page
		Redirect::to(1);
	}
	$member = new Member();
	$members = $member->get_active('members',array('company_id','=',$user->data()->company_id));

?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Consumable Freebies list
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
					<div class="panel-heading">Consumable Freebies list</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="input-group">
									<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
									<input type="text" id="search" class='form-control' placeholder='Search..'/>
								</div>
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
						<?php if($members){
							?>
							<select name="member_id" id="member_id" class='form-control'>
								<option value=""></option>
								<?php foreach($members as $mem ){
									?>
									<option value="<?php echo $mem->id; ?>"><?php echo ucwords($mem->lastname . ", " . $mem->firstname. " " . $mem->middlename); ?></option>
									<?php
								}?>
							</select>
							<?php
						}?>
					</div>
					<div class="form-group">
						<strong>Amount</strong> <input type="text" id='amount' class='form-control'>
					</div>
					<div class="form-group">
						<button class='btn btn-primary' id='btnSubmit'> <span class='glyphicon glyphicon-save'></span> Submit</button>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>
		$(document).ready(function(){
			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search =$('#search').val();
				getPage(page,search);
			});
			$('body').on('keyup','#search',function(e){
				e.preventDefault();
				var page = 0;
				var search =$('#search').val();
				getPage(page,search);
			});
			getPage(0,'');
			function getPage(p,search){

				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'consumablesFreebiesListPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}
			$('body').on('click','.btnUpdate',function(){
				var row = $(this).parents('tr');
				var id = $(this).attr('data-id');
				var amount = row.children().eq(1).find('input').val();
				$.ajax({
					url:'../ajax/ajax_query2.php',
					type:'POST',
					data: {id:id,functionName:'updateConsumableFreebiesAmount',amount:amount},
					success: function(data){
						alertify.alert(data,function(){
							location.href='consumablefree-admin.php';
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
				if(member_id && amount){
					var btncon = $(this);
					var btnoldval = btncon.html();
					btncon.html('Loading...');
					btncon.attr('disabled',true);
					$.ajax({
						url:'../ajax/ajax_query2.php',
						type:'POST',
						data: {functionName:'addMemberConsumableFreebies', member_id:member_id,amount:amount},
						success: function(data){
							alertify.alert(data,function(){
								btncon.html(btnoldval);
								btncon.attr('disabled',false);
								var search =$('#search').val();
								getPage(0,search);
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