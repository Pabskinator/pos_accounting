<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('sp_forecast')){
		// redirect to denied page
		Redirect::to(1);
	}
	$current = date('F Y');
	$dt1 = strtotime($current . "-1 month");
	$dt2 = strtotime($current . "-1 min");

?>


	<!-- Page content -->
	<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Forecast
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
					<div class="panel-heading"><span class='glyphicon glyphicon-trash'></span> Raw Materials</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<input type="text" class='form-control' value='<?php echo date('m/d/Y',$dt1); ?>' autocomplete="off" placeholder='Date From' id='dt_from'>
							</div>
							<div class="col-md-3">
								<input type="text" class='form-control' value='<?php echo date('m/d/Y',$dt2); ?>' autocomplete="off" placeholder='Date To' id='dt_to'>
							</div>
							<div class="col-md-3">
								<button class='btn btn-default' id='btnSubmit'>Submit</button>
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

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id='mtitle'></h4>
					</div>
					<div class="modal-body" id='mbody'>
						<strong>Item</strong>
						<input type="text" id='item_id' class='form-control selectitem' placeholder='Search Item'> <br>
						<strong>Qty</strong>
						<input type="text" id='item_qty' class='form-control' placeholder='Qty'> <br>
						<button class='btn btn-default' id='btnSubmitMore'>Submit</button>
					</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script>

		$(document).ready(function(){

			$('body').on('click','#btnAddMore',function(){
				$('#item_id').select2('val',null);
				$('#item_qty').val('');
				$('#myModal').modal('show');
			});

			$('body').on('click','#btnSubmitMore',function(){

				var item_id = $('#item_id').val();
				var qty = $('#item_qty').val();

				if(item_id && qty){
					var desc = $('#item_id').select2('data').text;
					var splitted = desc.split(':');
					var item_code = splitted[1];
					var tr = "<tr data-item_id='"+item_id+"'><td style='border-top:1px solid #ccc;'>"+item_code+"</td><td style='border-top:1px solid #ccc;'><input type='text' value='"+qty+"' placeholder='Qty'></td></tr>";
					$('#tblItem > tbody').append(tr);
					$('#myModal').modal('hide');
				} else {
					alert("Enter item and qty first.");
				}

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

			getRawMats();

			$('body').on('click','#btnSubmit',function(){
				getRawMats();
			});

			function getRawMats(){
				$('#holder').html('Loading...');
				var dt1 = $('#dt_from').val();
				var dt2 = $('#dt_to').val();

				$.ajax({
				    url:'../ajax/ajax_inventory.php',
				    type:'POST',
				    data: {functionName:'avgRawConsumption',dt1:dt1,dt2:dt2},
				    success: function(data){
				        $('#holder').html(data);
					    $('#branch_id').select2(
						    {
							    placeholder:'Branch'
						    }
					    );
					    $('#supplier_id').select2(
						    {
							    placeholder:'Supplier'
						    }
					    );
				    },
				    error:function(){

				    }
				}) ;
			}

			$('body').on('click','#btnFinal',function(){
				var arr = [];
				$('#tblItem tbody tr').each(function(){
					var row = $(this);
					var item_id = row.attr('data-item_id');
					var qty = row.children().eq(1).find('input').val();
					var cls = row.attr('class');
					if(cls == 'bg-success'){
						arr.push({item_id:item_id,qty:qty});
					}


				});

				var branch_id = $('#branch_id').val();
				var supplier_id = $('#supplier_id').val();
				
				$.ajax({
				    url:'../ajax/ajax_inventory.php',
				    type:'POST',
				    data: {functionName:'submitSupplierItem',items: JSON.stringify(arr),branch_id:branch_id,supplier_id:supplier_id},
				    success: function(data){
					    alert(data);
					    location.href='supplier_receive_order.php'
				    },
				    error:function(){
				        
				    }
				});
				
				
			});
			$('body').on('click','.btnRemove',function(){
				$(this).parents('tr').remove();
			});

			$('body').on('change','#supplier_id',function(){
				var arr = [];
				
				$('#tblItem tbody tr').each(function(){
					var row = $(this);
					var item_id = row.attr('data-item_id');
					row.removeClass('bg-success');
					arr.push(item_id);
				});
				$.ajax({
				    url:'../ajax/ajax_supplier.php',
				    type:'POST',
				    data: {functionName:'checkSupplierItem',supplier_id: $('#supplier_id').val(),items: JSON.stringify(arr)},
				    success: function(data){
				        try{
					        var data = JSON.parse(data);
					        $('#tblItem tbody tr').each(function(){
						        var row = $(this);
						        var item_id = row.attr('data-item_id');
						        for(var i in data){
							        if(data[i] == item_id){
								        row.addClass('bg-success');
							        }
						        }
					        });
				        }catch(e){
					        console.log(e);
				        }
				    },
				    error:function(){

				    }
				});
			});


		});




	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>