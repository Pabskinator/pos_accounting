<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('caravan_manage')) {
		// redirect to denied page
		Redirect::to(1);
	}

?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
	<div style='margin:5px;' class="btn-group" role="group" aria-label="...">
		<?php if($user->hasPermission('caravan_manage')) {?>
			<a class='btn btn-default' href='#' title='Caravan Monitoring'>
				<span class='glyphicon glyphicon-inbox'></span>
		<span class='hidden-xs'>
		Caravan Sales
		</span>
			</a>
		<?php } ?>

	</div>
	<div class="content-header">

		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Caravan </h1>


	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('salesflash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('salesflash') . "</div>";
		}
	?>
	<div id="test"></div>
	<div class="row">
		<div class="col-md-12">
			<input type="hidden" id='sort_by' />
			<input type="hidden" id='ascdesc' value='1' />

			<div class="panel panel-primary">
				<!-- Default panel contents -->
				<div class="panel-heading">Caravan</div>
				<div class="panel-body">
					<input type="hidden" id="hiddenpage" />
					<div id="holder"></div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style='width:95%;'>
			<div class="modal-content" >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Payment Details</h4>
				</div>
				<div class="modal-body" id='mbody'>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(function(){


			getPage(0);
			function getPage(p){
				$('.loading').show();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,sortby:sortby,functionName:'salesPaginate',cid: <?php echo $user->data()->company_id; ?>,search:search,b:b,t:t,type:type},
					success: function(data){
						$('#holder').empty();
						$('#holder').append(data);
						$('.loading').hide();
					},
					error:function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='sales.php';
						$('.loading').hide();
					}
				});
			}

		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>