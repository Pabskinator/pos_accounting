<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('recycle')){
		// redirect to denied page
		Redirect::to(1);
	}
	$arrtables = ['users','items','terminals','branches','members','stations'];
	$default = 'users'
?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Recycle Bin
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

				<div class="panel panel-primary">
					<!-- Default panel contents -->
					<div class="panel-heading"><span class='glyphicon glyphicon-trash'></span> Trash</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<select name="tables" id="tables" class='form-control'>
									<option value=""></option>
									<?php
										foreach($arrtables as $tbl){
											if($tbl == $default){
												$selected='selected';
											} else {
												$selected ='';
											}
											echo "<option value='$tbl' $selected>$tbl</option>";
										}
									?>
								</select>

							</div>
						</div>
						<hr>
						<input type="hidden" id="hiddenpage" />
						<div id="holder"></div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			var tbl = $('#tables').val();
			getPage(0,tbl);
			$('body').on('click','.paging',function(){
				var page = $(this).attr('page');
				tbl = $('#tables').val();
				$('#hiddenpage').val(page);
				getPage(page,tbl);
			});
			$('body').on('change','#tables',function(){
				tbl = $('#tables').val();
				getPage(0,tbl);
			});
			function getPage(p,tbl){

				$('.loading').show();
				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					data:{page:p,functionName:'recycleBinPaginate',cid: <?php echo $user->data()->company_id; ?>,tbl:tbl},
					success: function(data){
						$('#holder').empty();
						$('#holder').append(data);
						$('.loading').hide();
					},
					error: function(){
						alert('Something went wrong. The page will be refresh.');
						location.href='recycle_bin.php';
						$('.loading').hide();
					}
				});
			}
			$('body').on('click','.btnRestore',function(){
				var id = $(this).attr('data-id');
				var tbl = $(this).attr('data-table');
				$.ajax({
				    url:'../ajax/ajax_query.php',
				    type:'POST',
				    data: {id:id,tbl:tbl,functionName:'restoreFromRecycleBin'},
				    success: function(data){
				        alertify.alert(data,function(){
					        var page = 	$('#hiddenpage').val();
					        tbl = $('#tables').val();
					        getPage(page,tbl);
				        });
				    },
				    error:function(){

				    }
				})
			});
		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>