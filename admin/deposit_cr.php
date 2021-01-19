<?php
	// $user have all the properties and method of the current user

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('m_terms_request')) {
		Redirect::to(1);
	}

	// details at list
	// may filter ng date
	// client
	//
?>



	<!-- Page content -->
	<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1><span id="menu-toggle" class='glyphicon glyphicon-list'></span> <?php echo MEMBER_LABEL; ?> Deposits Collection Report</h1>

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
					<div class="panel-heading"></div>
					<div class="panel-body">
						<div class='text-right'>
							<div class="btn-group" role="group" aria-label="..." style='margin-bottom:10px;'>
								<a class='btn btn-default btn-sm btn-nav' data-nav="1" title='Pending' href='#' >
									<span class='hidden-xs'>Details <span > </span>
								</a>
								<a class='btn btn-default btn-sm btn-nav' data-nav="2" title='List' href='#' >
									<span class='hidden-xs'>List <span > </span>
								</a>
							</div>
						</div>
						<div id='con1'>

							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' placeholder="Date From" id='dt_from' autocomplete="off">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' placeholder="Date To" id='dt_to' autocomplete="off">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control'  id='member_id'>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<button class='btn btn-default' id='btnFilter'>Filter</button>
									</div>
								</div>
							</div>
							<div id="holder"></div>

							<input type="hidden" id="hiddenpage" />
							<div id="holder"></div>

						</div>
						<!-- con1 -->

						<div id="con2">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' placeholder="Date From" id='dt_from2' autocomplete="off">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control' placeholder="Date To" id='dt_to2' autocomplete="off">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<button class='btn btn-default' id='btnFilterDetails'>Filter</button>
									</div>
								</div>
							</div>
								<div id="con_cr_list"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end page content wrapper-->
	<script>

		$(document).ready(function() {

			showContainer(1);
			getDepCR();

			$('#member_id').select2({
				placeholder: 'Search client', allowClear: true, minimumInputLength: 2,

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

			$('body').on('click','#btnFilterDetails',function(){
				crList();
			});
			$('body').on('click','#btnSaveCr',function(){
				var arr_dep = [];
				var cr_number = $('#cr_number').val();
				$('#tblDep tbody tr').each(function(){
					var row = $(this);
					var id = row.attr('data-id');
					arr_dep.push({id:id});
				});

				$.ajax({
				    url:'../ajax/ajax_deposits.php',
				    type:'POST',
				    data: {functionName:'saveCR', cr_number:cr_number,data:JSON.stringify(arr_dep)},
				    success: function(data){
				        alert(data);
					    location.reload();
				    },
				    error:function(){

				    }
				});

			});

			$('body').on('click','.btn-nav',function(e){
				e.preventDefault();
				var c = $(this).attr('data-nav');
				showContainer(c);
			});

			$('body').on('click','#btnFilter',function(){
				getDepCR();
			});
			$('#dt_from').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from').datepicker('hide');
			});
			$('#dt_to').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to').datepicker('hide');
			});
			$('#dt_from2').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_from2').datepicker('hide');
			});
			$('#dt_to2').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#dt_to2').datepicker('hide');
			});
			function crList(){
				var dt1 = $('#dt_from2').val();
				var dt2 = $('#dt_to2').val();
				$.ajax({
					url:'../ajax/ajax_deposits.php',
					type:'POST',
					data: {functionName:'crList',dt1:dt1,dt2:dt2},
					success: function(data){
						$('#con_cr_list').html(data);
					},
					error:function(){

					}
				});
			}
			function getDepCR(){

				var dt1 = $('#dt_from').val();
				var dt2 = $('#dt_to').val();
				var member_id = $('#member_id').val();

				$.ajax({
				    url:'../ajax/ajax_deposits.php',
				    type:'POST',
				    data: {functionName:'crDeposit',dt1:dt1,dt2:dt2,member_id:member_id},
				    success: function(data){
				        $('#holder').html(data);
				    },
				    error:function(){

				    }
				})
			}

			function showContainer(c){
				hideContainer();

				if(c == 1){
					$('#con1').show();
				} else if(c == 2){
					$('#con2').show();
					crList();
				}

			}
			function hideContainer(){
				$('#con1').hide();
				$('#con2').hide();

			}

			$('body').on('click','.btnDetails',function(){
				var cr_number = $(this).attr('cr_number');

				showContainer(1);
				crDetails(cr_number);
			});

			function crDetails(cr_number){


				$.ajax({
					url:'../ajax/ajax_deposits.php',
					type:'POST',
					data: {functionName:'crDepositDetails',cr_number:cr_number},
					success: function(data){
						$('#holder').html(data);
					},
					error:function(){

					}
				})
			}
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>