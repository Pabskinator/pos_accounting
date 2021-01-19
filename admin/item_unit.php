<?php
	// $user have all the properties and method of the current user
	// this variable is located in admin/page_head

	require_once '../includes/admin/page_head2.php';

	$unit = new Unit();
	$units = $unit->get_active('units',['1','=','1']);
?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Item Units</h1>

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
							<div class='col-md-6'>list</div>
							<div class='col-md-6 text-right'>
								<button id='addItem' class='btn btn-default btn-sm'><i class='fa fa-plus'></i></button>
							</div>
						</div>
					</div>
					<div class="panel-body">


						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><span class='glyphicon glyphicon-search'></span></span>
										<input type="text" id="search" class='form-control' placeholder='Search..'/>
									</div>
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
					<div class="form-group">
						<strong>Item:</strong>
						<input type="text" class='form-control selectitem' id='item_id' >
					</div>
					<div class="form-group">
						<strong>Unit:</strong>

						<select name="unit_name" id="unit_name" class='form-control'>
							<option value="">Select Units</option>
							<?php
								foreach($units as $u){
									echo "<option value='".$u->id."'>" .$u->name. "</option>";
								}
							?>
						</select>
					</div>	<div class="form-group">
						<strong>Qty:</strong>
						<input type="text" class='form-control' id='unit_qty' placeholder='Enter Qty'>
					</div>

					<div class="form-group">
						<button class='btn btn-default' id='btnSubmit'>Submit</button>
					</div>

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>

		$(document).ready(function() {
			$('body').on('click','#addItem',function(){
				$('#item_id').val('');
				$('#unit_name').val('');
				$('#unit_qty').val('');
				$('#myModal').modal('show');
			});
			$('body').on('click','#btnSubmit',function(){

				var item_id  = $('#item_id').val();
				var unit_name  = $('#unit_name').val();
				var unit_qty  = $('#unit_qty').val();

				if(item_id && unit_name && unit_qty){
					$.ajax({
					    url:'../ajax/ajax_service_item.php',
					    type:'POST',
					    data: {functionName:'addUnitTime',item_id:item_id,unit_name:unit_name,unit_qty:unit_qty},
					    success: function(data){
					        if(data == '1'){
						        tempToast('info','Added successfully','Info');
						        $('#myModal').modal('hide');
						        getPage($('#hiddenpage').val());
					        } else {
						        tempToast('error','Invalid request.','Error');
					        }
					    },
					    error:function(){
					        
					    }
					});
				} else {
					tempToast('error','Invalid request.','Error');
				}

			});

			getPage(0);

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
			$('body').on('change','#type',function(){
				getPage(0);
			});
			function getPage(p){
				var search = $('#search').val();


				$.ajax({
					url: '../ajax/ajax_paging.php',
					type:'post',
					beforeSend:function(){
						$('#holder').html("<p class='text-center'><i class='fa fa-spinner fa-spin'></i> Loading...</p>");
					},
					data:{page:p,functionName:'itemUnits',cid: <?php echo $user->data()->company_id; ?>,search:search},
					success: function(data){
						$('#holder').html(data);
					}
				});
			}

			$('body').on('click', '.deleteItemUnit', function() {
				if(confirm("Are you sure you want to delete this record?")) {

					var id = $(this).prop('id');

					$.post('../ajax/ajax_delete.php', {id: id, table: 'item_units'}, function(data) {
						if(data == "true") {
							location.reload();
						}
					});

				}
			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>