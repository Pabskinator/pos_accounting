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
					<?php echo isset($editid) && !empty($editid) ? "Modify adjustment" : "Add  adjustment"; ?>
				</h1>
			</div>
			<div class="row">

				<div class="btn-group hidden-xs" role="group" aria-label="..." style='margin-bottom:10px;'>
					<a class='btn btn-default' href='item_group_adjustment.php' title='List'> <span class='glyphicon glyphicon-list'></span> <span class='hidden-xs'>Group Adjustment</span> </a>
					<a class='btn btn-default' href='add_item_group_adjustment.php' title='Add adjustment'> <span class='glyphicon glyphicon-plus'></span> <span class='hidden-xs'>Add  Group Adjustment</span> </a>
				</div>
				<div class="col-md-12">

					<?php

						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id

						}

						// if submitted
						if (Input::exists()){
							// check token if match to our token
							if(Token::check(Input::get('token'))){
								$items = Input::get('item');
								$price_adj = Input::get('price_adj');
								$group_id = Input::get('group_id');

								if(count($items) > 0){
									$now = time();

									$newAdjustment = new Item_group_adjustment();


											foreach($items as $key => $val){
												$is_exists = $newAdjustment->checkIfExists($val,$group_id);
												if($is_exists->cnt == 0){
													$price_adj[$key] = removeComma($price_adj[$key]);
													if($val && $price_adj[$key] && is_numeric($price_adj[$key])){

														$newAdjustment->create(array(
															'group_adjustment_id' => $group_id,
															'item_id' => $val,
															'adjustment' =>$price_adj[$key],
															'created' => $now,
															'user_id' => $user->data()->id,

														));


													}
												}
										}


								} else {

								}
								Session::flash('flash','You have successfully added a price adjustment');
								Redirect::to('item_group_adjustment.php');

							}
						}
					?>

					<form class="form-horizontal" action="" method="POST">
						<fieldset>


							<legend>Information</legend>
			
							<div class="form-group">
								<div class="row">
									<div class="col-md-1"></div>
									<div class="col-md-3">
										<?php
											$group_adjustment = new Group_adjustment_optional();
											$groups = $group_adjustment->getRecord(1);
											
										?> 
										<select class='form-control' name="group_id" id="group_id">
											<?php if($groups){
												foreach($groups as $gr ){
													?>
													<option value="<?php echo $gr->id; ?>"><?php echo $gr->name; ?></option>
													<?php
												}
											} ?>
										</select>
										<span class='help-block'>Group Adjustment Name</span>
									</div>
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