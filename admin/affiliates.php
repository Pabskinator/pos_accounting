<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('affiliate')){
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
				Affiliates
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
				<?php if($user->hasPermission('branch_m')) { ?>
					<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
						<a class='btn btn-default' href='addaffiliate.php' title='Add Affiliate'>
							<span class='glyphicon glyphicon-plus'></span>
							<span class='hidden-xs'>Add Affiliate</span>
						</a>
					</div>
				<?php } ?>
				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading">Affiliates</div>
					<div class="panel-body">
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			getPage(0);
			$(".deleteAffiliate").click(function(){
				if(confirm("Are you sure you want to delete this record? \n ")){
					id = $(this).prop('id');
					$.post('../ajax/ajax_delete.php',{id:id,table:'affiliates'},function(data){
						if(data == "true"){
							location.reload();
						}
					});
				}
			});
			$('body').on('click','.generateNewCode',function(e){
				e.preventDefault();
				var con = $(this);
				var id =con.attr('id');
				var row = con.parents('tr');

				alertify.confirm("Are you sure you want to generate new codes?",function(e){
					if(e){
						$.ajax({
							url:'../ajax/ajax_affiliates.php',
							type:'POST',
							dataType:'json',
							data: {functionName:'generateNewCode',id:id},
							success: function(data){
								tempToast('info',data.message,data.title);
								row.children().eq(1).html("<strong class='text-danger'>"+data.title+"</strong>");
							},
							error:function(){

							}
						});
					}
				});


			});
			function getPage(p){
				var search = $('#search').val();

				$.ajax({
					url: '../ajax/ajax_affiliates.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'getAffiliates',search:search},
					success: function(data){

						$('#holder').html(data);

					}
				});
			}
		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>