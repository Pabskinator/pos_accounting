<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_swap')){
		// redirect to denied page
		Redirect::to(1);
	}

	$branch = new Branch();
	$branches = $branch->branchJSON($user->data()->company_id,'');


	if (Input::exists()) {

		// check token if match to our token
		if(Token::check(Input::get('token'))) {
			$swapFrom = Input::get('swapFrom');
			$rackFrom = Input::get('rackFrom');
			$qtyFrom = Input::get('availQtyHid');
			$swapTo = Input::get('swapTo');
			$rackTo= Input::get('rackTo');
			$qtyTo = Input::get('qtyTo');
			$inv_mon = new Inventory_monitoring();
			$inventory = new Inventory();
			$inserted = false;

			$branch_id = Input::get('branch_id');
			$remarks = Input::get('remarks');

			if(!$branch_id){
				$branch_id = $user->data()->branch_id;
			}

			for($j =0; $j< 10;$j++){
				$i_item_from =  $swapFrom[$j];
				$i_rack_from =  explode(',',$rackFrom[$j]);
				$i_qty_from = $qtyFrom[$j];
				$i_item_to = $swapTo[$j];
				$i_rack_to = $rackTo[$j];
				$i_qty_to = $qtyTo[$j];
				if($i_item_from && isset($i_rack_from[0]) && $i_qty_from && $i_item_to && $i_rack_to && $i_qty_to){

					if($inventory->checkIfItemExist($i_item_to,$branch_id,$user->data()->company_id,$i_rack_to)){

						//	echo "UPDATE";
						$curinventoryDis = $inventory->getQty($i_item_to,$branch_id,$i_rack_to);
						$inventory->addInventory($i_item_to,$branch_id,$i_qty_to,false,$i_rack_to);
						// monitoring

						$newqtyDis = $curinventoryDis->qty + $i_qty_to;
						$inv_mon->create(array(
							'item_id' => $i_item_to,
							'rack_id' => $i_rack_to,
							'branch_id' => $branch_id,
							'page' => 'admin/swap.php',
							'action' => 'Update',
							'prev_qty' => $curinventoryDis->qty,
							'qty_di' => 1,
							'qty' => $i_qty_to,
							'new_qty' => $newqtyDis,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Swap Item (' . $remarks . ')',
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));

						$curinventoryFrom = $inventory->getQty($i_item_from,$branch_id,$i_rack_from[0]);

						$inventory->subtractInventory($i_item_from,$branch_id,$i_qty_to,$i_rack_from[0]);

						// monitoring
						$newqtyFrom = $curinventoryFrom->qty - $i_qty_to;

						$inv_mon->create(array(
							'item_id' => $i_item_from,
							'rack_id' => $i_rack_from[0],
							'branch_id' => $branch_id,
							'page' => 'admin/swap.php',
							'action' => 'Update',
							'prev_qty' => $curinventoryFrom->qty,
							'qty_di' => 2,
							'qty' => $i_qty_to,
							'new_qty' => $newqtyFrom,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Swap Item (' . $remarks . ')',
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));

					} else {

						$curinventoryDis = 0;
						$inventory->addInventory($i_item_to,$branch_id,$i_qty_to,true,$i_rack_to);
						//monitoring
						$newqtyDis = $curinventoryDis + $i_qty_to;
						$inv_mon->create(array(
							'item_id' => $i_item_to,
							'rack_id' => $i_rack_to,
							'branch_id' => $branch_id,
							'page' => 'admin/swap.php',
							'action' => 'Insert',
							'prev_qty' => $curinventoryDis,
							'qty_di' => 1,
							'qty' => $i_qty_to,
							'new_qty' => $newqtyDis,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Swap Item(' . $remarks . ')',
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));


						$curinventoryFrom = $inventory->getQty($i_item_from,$branch_id,$i_rack_from[0]);
						$inventory->subtractInventory($i_item_from,$branch_id,$i_qty_to,$i_rack_from[0]);
						// monitoring
						$newqtyFrom = $curinventoryFrom->qty - $i_qty_to;
						$inv_mon->create(array(
							'item_id' => $i_item_from,
							'rack_id' => $i_rack_from[0],
							'branch_id' => $branch_id,
							'page' => 'admin/swap.php',
							'action' => 'Insert',
							'prev_qty' => $curinventoryFrom->qty,
							'qty_di' => 2,
							'qty' => $i_qty_to,
							'new_qty' => $newqtyFrom,
							'created' => time(),
							'user_id' => $user->data()->id,
							'remarks' => 'Swap Item(' . $remarks . ')',
							'is_active' => 1,
							'company_id' => $user->data()->company_id
						));
					}
					$inserted = true;
				}
			}
			if($inserted){
				$msg = "You have successfully swapped the item(s)";
			} else {
				$msg = "No item was inserted";
			}
			Session::flash('flash',$msg);
			Redirect::to('swap.php');
		}
	}
?>



	<!-- Page content -->
	<div id="page-content-wrapper">




	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				Swap Item
			</h1>

		</div>
		<?php
			// get flash message if add or edited successfully
			if(Session::exists('flash')){
				echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div><br/>";
			}
		?>

		<form id='formSwap' action="" method="POST">

				<div class="row">
					<div class="col-md-3">
					<div class="form-group">
						<select name="branch_id" id="branch_id" class='form-control'>
					<?php if($branches){
						foreach($branches as $b){
							if($b->id == $user->data()->branch_id){
								$selected='selected';
							} else {
								$selected='';
							}
							echo "<option value='$b->id' $selected>$b->name</option>";
						}

					} ?>
						</select>
						<span class='help-block'>Choose Branch</span>
					</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" placeholder='Remarks' class='form-control' id='remarks' name='remarks' required>
							<span class='help-block'>Enter remarks</span>
						</div>
					</div>
				</div>

			<?php for($i = 1; $i<=10; $i++){
				?>
				<div class='row'>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" data-id='<?php echo $i; ?>' class='form-control selectitem itemfrom' name='swapFrom[]'>
						<span class='help-block'>Swap from</span>

					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<select name="rackFrom[]"  data-id='<?php echo $i; ?>' id="rackFrom<?php echo $i; ?>" class='form-control rackFrom'>
						</select>
						<span class='help-block'>Rack from</span>
					</div>
				</div>

					<div class="col-md-3">
						<div class="form-group">
							<input type="hidden"  id="availQtyHid<?php echo $i; ?>" class='form-control' name='availQtyHid[]'>
							<input type="text" disabled id="availQty<?php echo $i; ?>" class='form-control selectqtyavail' name='availQty[]'>
							<span class='help-block'>Stocks available</span>
						</div>
					</div>
				</div>
				<div class='row'>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" class='form-control selectitem' name='swapTo[]'>
						<span class='help-block'>Swap to</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" class='form-control selectrack' name='rackTo[]'>
						<span class='help-block'>Rack To</span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" data-id='<?php echo $i; ?>' id="qtyTo<?php echo $i; ?>" class='form-control selectqty' name='qtyTo[]'>
						<span class='help-block'>Quantity to swap</span>
					</div>
				</div>
				</div>
				<hr>
				<?php
			}?>
			<div class='text-right'>
				<input type="submit" class='btn btn-primary float-button' id='btnSubmit' name='btnSubmit'>
				<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
			</div>

		</form>
	</div> <!-- end page content wrapper-->
	<script>

		$(document).ready(function(){
			$('.selectrack').select2({
				placeholder: 'Search rack',
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
							functionName:'racks',
							branch_id: $('#branch_id').val()
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.rack,
									slug: item.rack,
									id: item.id
								}
							})
						};
					}
				}
			});

			$('body').on('click','#btnSubmit',function(e){
				e.preventDefault();
				var con = $(this);
				var oldval = con.val();
				con.attr('disabled',true);
				con.val('Loading...');
				$('#formSwap').submit();
			});

			$('body').on('change','.itemfrom',function(){

				var id = $(this).attr('data-id');
				var v = $(this).val();
				var branch_id = $('#branch_id').val();

				$.ajax({
					url: "../ajax/ajax_get_rack.php",
					type: "POST",
					data: {item_id: v, branch_id: branch_id,rack_id:0},
					success: function(data) {
						$("#rackFrom" + id).html(data);
						displayStock(id);
					}
				});

			});

			$('body').on('keyup','.selectqty',function(){
				var qty = $(this);
				var id = qty.attr('data-id');
				var fromqty =  $("#availQtyHid" + id).val();
				if(!qty.val() || isNaN(qty.val()) || parseFloat(qty.val()) < 0){
					tempToast('error',"<p>Invalid quantity.</p>","<h4>Error!</h4>");
					qty.val(1);
					return;
				}
				console.log(qty.val() + " " + fromqty);
				if(parseFloat(qty.val()) > parseFloat(fromqty)){
					tempToast('error',"<p>Invalid quantity.</p>","<h4>Error!</h4>");
					qty.val(1);
					return;
				}
			});

			$('body').on('change','.rackFrom',function(){
				var id = $(this).attr('data-id');
				displayStock(id);
			});

			function displayStock(id){
				var rack = $("#rackFrom" + id);
				var qty = $("#availQty" + id);
				var hid = $("#availQtyHid" + id);
				var rackVal = rack.val();
				var splitted = [];
				if(rackVal.indexOf(',') > 0){
					splitted = rackVal.split(',');
				}
				if(splitted.length > 0){
					qty.val(splitted[1]);
					hid.val(splitted[1]);
				}
			}
		});

	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>