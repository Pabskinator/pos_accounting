<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item_adj_m')) {
		// redirect to denied page
		Redirect::to(1);
	}
	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

?>

	<!-- Page content -->
	<div id="page-content-wrapper">

		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					<?php echo isset($editid) && !empty($editid) ? "Modify price adjustment" : "Add price adjustment"; ?>
				</h1>
			</div>
			<div class="row">
				<?php include 'includes/pricelist_nav.php'; ?>
				<div class="col-md-12">

					<?php

						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$branch = new Branch($id);
						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){
									$items = Input::get('item');
									$price_adj = Input::get('price_adj');
									if(count($items) > 0){
										$branch_ids = Input::get('branch_id');
										$batch_dt = Input::get('batch_dt');
										$remarks = Input::get('remarks');
										if($branch_ids){

											$newAdjustment = new Item_price_adjustment();
											$newAdjustmentLog = new Item_price_adjustment_log();
											foreach($branch_ids as $branch_id){
												$now = time();
												foreach($items as $key => $val){
													$is_exists = $newAdjustment->checkIfExists($branch_id,$val);
													if($is_exists->cnt == 0){
														if($val && $price_adj[$key] && is_numeric($price_adj[$key])){

															$newAdjustment->create(array(
																'branch_id' => $branch_id,
																'item_id' => $val,
																'adjustment' =>$price_adj[$key],
																'created' => $now,
																'modified' => $now,
																'company_id' => $user->data()->company_id,
																'is_active' => 1,
																'batch_dt' => $batch_dt,
																'remarks' => $remarks

															));
															$newAdjustmentLog->create(array(
																'branch_id' => $branch_id,
																'item_id' => $val,
																'from_price' => 0,
																'to_price' => $price_adj[$key],
																'company_id' => $user->data()->company_id,
																'user_id' => $user->data()->id,
																'created' => $now,
																'is_active' => 1
															));
														}
													}

												}
											}
										}

									} else {

									}
									//Session::flash('flash','You have successfully added a price adjustment');
									//Redirect::to('item-price-adjustment.php');

							}
						}
					?>

					<form class="form-horizontal" action="" method="POST">
						<fieldset>


							<legend>Information</legend>

							<div class="form-group">
								<label class="col-md-1 control-label" for="branch_id">Branch</label>
								<div class="col-md-3">
									<select id="branch_id" name="branch_id[]" class="form-control" multiple>
										<option value=''></option>
										<?php
											$branch = new Branch();
											$branches =  $branch->get_active('branches',array('company_id' ,'=',$user->data()->company_id));
											foreach($branches as $b){
												$a = isset($id) ? $terminal->data()->branch_id : escape(Input::get('branch_id'));

												if($a==$b->id){
													$selected='selected';
												} else {
													$selected='';
												}
												?>
												<option value='<?php echo $b->id ?>' <?php echo $selected ?>><?php echo $b->name;?> </option>
												<?php
											}
										?>
									</select>
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="batch_dt">Date</label>
								<div class="col-md-3">
									<input type="text" id='batch_dt' name='batch_dt' placeholder='Enter Date' class='form-control'>
									<span class="help-block"></span>
								</div>
								<label class="col-md-1 control-label" for="remarks">Remarks</label>
								<div class="col-md-3">
									<input type="text" id='remarks' name='remarks' placeholder='Enter Remarks' class='form-control'>
									<span class="help-block"></span>
								</div>

							</div>
							<?php for($i = 1 ; $i <= 5; $i++){
								?>

							<div class="row">
								<label class="col-md-1 control-label" for="">Item</label>

								<div class="col-md-3">
									<div class="form-group">
										<input type="text" data-i='<?php echo $i; ?>' class='form-control selectitem changeitem' name='item[]' placeholder='Item'>
									</div>
								</div>
								<label class="col-md-1 control-label" for="">Price</label>

								<div class="col-md-2">
									<div class="form-group">
										<input disabled class='form-control' id='price_<?php echo $i; ?>' value='0.00'>
									</div>
								</div>

								<label class="col-md-1 control-label" for="">Adjustment</label>

								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control price_adj' name='price_adj[]' placeholder='Price Adjustment'>
									</div>
								</div>
								<div class="col-md-4"></div>
							</div>
								<?php
							}?>
							<div id='container_adj'></div>
							<br><br>
							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-1 control-label" for="button1id"></label>
								<div class="col-md-3">
									<input type='submit' class='btn btn-success' name='btnSave' value='SAVE'/>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
									<input type='hidden' name='edit' value=<?php echo isset($id) ? escape(Encryption::encrypt_decrypt('encrypt',$id)): 0; ?>>
								</div>
								<div class="col-md-4">
									<div class="text-right">
										<button id='btnAddMore'  class='btn btn-default'>Add More</button>
									</div>
								</div>
							</div>

						</fieldset>
					</form>
				</div>

			</div>
		</div>
	</div> <!-- end page content wrapper-->

	<script>
		$(function(){
			function formatItem(o) {
				if (!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> "+r[0]+"</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>"+r[2]+"</small></span>";
				}
			}
			var ctrAddmore  = 6;
			$('body').on('click','#btnAddMore',function(e){
				e.preventDefault();
				var  item_id = "selectitem"+ctrAddmore;
				$('#container_adj').append("<div class='row'><label class='col-md-1 control-label' for=''>Item</label>	<div class='col-md-3'><div class='form-group'><input type='text' data-i='"+ctrAddmore+"' id='"+item_id+"' class='form-control selectitem changeitem' name='item[]' placeholder='Item'></div></div><label class='col-md-1 control-label' for=''>Price</label><div class='col-md-2'><div class='form-group'><input disabled class='form-control' id='price_"+ctrAddmore+"' value='0.00'></div></div><label class='col-md-1 control-label' for=''>Adjustment</label>	<div class='col-md-3'><div class='form-group price_adj'><input type='text' class='form-control' name='price_adj[]' placeholder='Price Adjustment'></div></div></div>")
				$("#"+item_id).select2({
					placeholder: 'Item code',
					allowClear: true,
					minimumInputLength: 2,
					formatResult: formatItem,
					formatSelection: formatItem,
					escapeMarkup: function(m) {
						return m;
					},
					ajax: {
						url: '../ajax/ajax_query.php',
						dataType: 'json',
						type: "POST",
						quietMillis: 50,
						data: function (term) {
							return {
								search: term,
								functionName:'searchItemJSON'
							};
						},
						results: function (data) {
							return {
								results: $.map(data, function (item) {
									return {
										text: item.barcode + ":" + item.item_code + ":" +item.description+ ":" + item.price,
										slug: item.description,
										id: item.id
									}
								})
							};
						}

					}
				}).on("select2-close", function(e) {
					// fired to the original element when the dropdown closes

				}).on("select2-highlight", function(e) {

				});
				ctrAddmore = parseInt(ctrAddmore) + 1;


			});
			$('body').on('change','.changeitem',function(){
				var item = $(this);
				var v = item.select2('data').text;
				var sp = v.split(':');
				var i = item.attr('data-i');

				$('#price_'+i).val(number_format(sp[3],2));
			});
			$('#branch_id').select2({allowClear:true,placeholder:'Select Branch'});

			$('#batch_dt').datepicker({
				autoclose:true
			}).on('changeDate', function(ev){
				$('#batch_dt').datepicker('hide');
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>