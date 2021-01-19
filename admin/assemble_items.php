<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	//if(!$user->hasPermission('createorder')) {
	// redirect to denied page
	//	Redirect::to(1);
	//}

?>



	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
	<div class="content-header">
		<h1>
			<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Item list </h1>
	</div>
	<div id="orderholder">

	</div>
	<?php
		// get flash message if add or edited successfully
		if(Session::exists('flash')) {
			echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>" . Session::flash('flash') . "</div>";
		}
	?>
	<?php include 'includes/spare_nav.php'; ?>
	<div class="panel panel-primary">
		<!-- Default panel contents -->
		<div class="panel-heading">
			<div class='row'>
				<div class='col-md-6'>
					Assemble item list
				</div>
				<div class="col-md-6 text-right">
					<button class='btn btn-default' id='btnDownload'><i class='fa fa-download'></i></button>
				</div>

			</div>

		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<select name="status" id="status" class='form-control'>
							<option value="1"><?php echo (Configuration::getValue('a_step1')) ? Configuration::getValue('a_step1') : 'Pending'; ?></option>
							<option value="2"><?php echo (Configuration::getValue('a_step2')) ? Configuration::getValue('a_step2') : 'For Assembly'; ?></option>
							<option value="3"><?php echo (Configuration::getValue('a_step3')) ? Configuration::getValue('a_step3') : 'Assembled'; ?></option>
							<option value="4">Cancelled</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id='dt_from' placeholder='Start Date' class='form-control'>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id='dt_to' placeholder='End Date' class='form-control'>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id='branch_id'  class='form-control'>
					</div>
				</div>
				<div class="col-md-3">
				    <div class="form-group">
				        <input type="text" id='item_id' name='item_id'  class='form-control'>
				    </div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" id='member_id'  class='form-control'>
					</div>
				</div>

			</div>

			<div id="con_list" style='margin-top:10px;'>

			</div>

		</div>
	</div>


	<script>
		$(function() {

			$('body').on('click','#btnDownload',function(){
				var status = $('#status').val();
				if(!status) status = 1;

				var branch_id = $('#branch_id').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var member_id = $('#member_id').val();

				window.open(
					'../ajax/ajax_product.php?functionName=getAssembleItemList&status='+status+'&branch_id='+branch_id+'&dt_from='+dt_from+'&dt_to='+dt_to+'&member_id='+member_id+'&is_dl=1',
					'_blank'
				);


			});

			$('#member_id').select2({
			    placeholder: 'Search Member' , allowClear: true, minimumInputLength: 2,
			    ajax: {
			        url: '../ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
			            return {
			                q: term, functionName: 'members'
			            };
			        }, results: function(data) {
			            return {
			                results: $.map(data, function(item) {

			                    return {
				                    text: item.lastname,
				                    slug: item.lastname,
				                    id: item.id
			                    }
			                })
			            };
			        }
			    }
			});



			$('#branch_id').select2({
				placeholder: 'Branch',
				allowClear: true,
				minimumInputLength: 2,
				ajax: {
					url: '../ajax/ajax_json.php',
					dataType: 'json',
					type: "POST",
					quietMillis: 50,
					data: function (term) {
						return {
							q: term,
							functionName:'branches'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.name ,
									slug: item.name ,
									id: item.id
								}
							})
						};
					}
				}
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
				if($('#dt_to').val() && $('#dt_from').val()){
					getList();
				}
			}

			$('body').on('change','#branch_id,#status',function(){
				getList()
			});

			getList();


			function getList(){

				var status = $('#status').val();
				if(!status) status = 1;

				var branch_id = $('#branch_id').val();
				var dt_from = $('#dt_from').val();
				var dt_to = $('#dt_to').val();
				var member_id = $('#member_id').val();

				$.ajax({
					url:'../ajax/ajax_product.php',
					type:'POST',
					data: {functionName:'getAssembleItemList',status:status,member_id:member_id,branch_id:branch_id,dt_from:dt_from,dt_to:dt_to},
					beforeSend: function(){
						$('.loading').show();
						$('#con_list').html('Loading...');
					},
					success: function(data){
						$('#con_list').html(data);
						$('.loading').hide();
					},
					error:function(){

						$('#con_list').html('Error fetching data.');
						$('.loading').hide();
					}
				})
			}



		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>