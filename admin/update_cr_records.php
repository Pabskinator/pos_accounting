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
				Update CR Details
			</h1>
		</div>

		<a href='accounting.php' class='btn btn-default mt10'>Back To CR List</a>
		<div class="panel panel-primary">
			<div class="panel-heading">

			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" class='form-control' id='cr_number' placeholder='Enter CR'>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<button class='btn btn-default' id='btnSave'>Submit</button>
						</div>
					</div>
				</div>
				<br>
				<table id='tblCr' class='table-border-top table table-bordered table-condensed'>
					<thead>
						<tr>
							<th>Delivery Date</th>
							<th>DR</th>
							<th>Invoice</th>
							<th>Client</th>
							<th>Amount</th>
							<th></th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
				<div id='info' class="alert alert-info">Search Records by CR Number</div>
			</div>
		</div>

	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){

			$('body').on('click','#btnSave',function(){
				getCR();
			});

			function getCR(){
				var cr_number = $('#cr_number').val();
				$.ajax({
				    url:'../ajax/ajax_accounting.php',
				    type:'POST',
				    data: {functionName:'getCRContent',cr_number:cr_number},
					dataType:'json',
				    success: function(data){
					    if(data.length){
						    displayCR(data);
					    } else {
						    alert("No record found.");
					    }

				    },
				    error:function(){

				    }
				});
			}
			toggleCon();
			function toggleCon(){
				if($('#tblCr tbody tr').length){
					$('#tblCr').show();
					$('#info').hide();
				} else {
					$('#tblCr').hide();
					$('#info').show();
				}
			}

			$('body').on('click','.removeCR',function(){

				var con = $(this);
				var id = con.attr('data-id');

				if(confirm("Are you sure you want to delete this record?")){
					deleteRecord(id);
				}


			});
			
			function deleteRecord(id){
				if(id){
					$.ajax({
					    url:'../ajax/ajax_accounting.php',
					    type:'POST',
					    data: {functionName:'deleteCRContent',id:id},
					    success: function(data){
							tempToast('info',data,'Info');
						    getCR();
					    },
					    error:function(){
					        
					    }
					});
				}
			}

			function displayCR(data){
				if(data.length){
					$('#tblCr tbody').html("");
					for(var i in data){
						$('#tblCr tbody').append("<tr><td>"+data[i].delivery_date+"</td><td>"+data[i].delivery_receipt+"</td><td>"+data[i].sales_invoice+"</td><td>"+data[i].client_name+"</td><td>"+data[i].paid_amount+"</td><td><button class='btn btn-danger btn-sm removeCR' data-id='"+data[i].id+"'><i class='fa fa-trash'></i></button></td></tr>")
					}
					toggleCon();
				}
			}

		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>