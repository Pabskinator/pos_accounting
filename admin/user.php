<?php
	// $user have all the properties and method of the current user
	
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('user')){
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
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Manage Users
				</h1>
			</div>
			<?php
				// get flash message if add or edited successfully
				if(Session::exists('userflash')){
					echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('userflash')."</div>";
				}

			?>
			<div class="row">
				<div class="col-md-12">
						<?php 	if($user->hasPermission('user_m')){ ?>
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='adduser.php' title='Add a User'>
						<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>Add a User</span>
						</a>
					</div>
					<?php } ?>
					<div class="panel panel-primary">
						<!-- Default panel contents -->
						<div class="panel-heading">Users</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-9"></div>
							<div class="col-md-3">
								<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon" id="sizing-addon1"><i class='fa fa-search'></i></span>
									<input id='search' type="text" class="form-control" placeholder="Search User" aria-describedby="sizing-addon1">
								</div>
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
	<script>
		$(document).ready(function(){

		/*	$(".deleteUser").click(function(){
				if(confirm("Are you sure you want to delete this record?")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'users'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			}); */
			$('body').on('click','.deleteUser',function(){
				if(confirm("Are you sure you want to delete this record?")){
					var id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'users'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
			$('#tblUsers').dataTable({
				iDisplayLength: 50
			});

			$('body').on('click','.paging',function(e){
				e.preventDefault();
				var page = $(this).attr('page');
				$('#hiddenpage').val(page);
				var search =$('#search').val();
				getPage(page,search);
			});
			var timer;
			$('body').on('keyup','#search',function(e){
				e.preventDefault();
				var search =$('#search');
				clearTimeout(timer);
				timer = setTimeout(function() {
					if(search.val()){
						search.val(search.val().trim());
					}
					getPage(0, search.val());
				}, 1000);
			});

			getPage(0,'');

			function getPage(p,search){
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'userPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}

			$('body').on('click','.btnReset',function(){
				var con = $(this);
				var id = con.attr('data-id');
				alertify.confirm("Are you sure you want to reset the password of this user? The default is 'password'.",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_member_service.php',
							type:'POST',
							data: {functionName:'resetPassword',id:id},
							success: function(data){
								tempToast('success',data,'Info');
							},
							error:function(){

							}
						});
					}

				});
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>