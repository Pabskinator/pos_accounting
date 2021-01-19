<?php
	// $user have all the properties and method of the current user
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('item')) {
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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>Bundles</h1>
		</div>
		<div class="row">
			<div class="col-md-12">
				<?php
					if(Input::exists()) {
						// check token if match to our token
						if(Token::check(Input::get('token'))) {
							$item_set = Input::get('item_set');

							$item_spare = Input::get('item_spare');
							$item_spare_qty = Input::get('item_spare_qty');


							$bundle = new Bundle();
							$returnString = "";
							$isext = false;
							$isadd = false;
							if(is_array($item_spare)){
								if(count($item_spare) > 0){
									$now = time();
									foreach($item_spare as $key => $val){
										if($val && $item_spare_qty[$key]){
											$exists = $bundle->checkIfExists($user->data()->company_id,$item_set,$val);
											if($exists->cnt > 0){
												$isext = true;
											} else {
												$bundle->create(array(
													'item_id_child' => $val,
													'item_id_parent' =>$item_set,
													'child_qty' => $item_spare_qty[$key],
													'created' =>$now,
													'company_id' => $user->data()->company_id,
													'is_active' => 1
												));
												$isadd = true;
											}
										}
									}
								}
							}


							if($isadd){
								if($item_set && is_numeric($item_set)){
									$product = new Product();
									$product->update(['is_bundle' => 1],$item_set);
								}

								$returnString .= "<p>Item added successfully</p>";
							} else {
								$returnString .= "<p>Request failed.</p>";
							}
							if($isext){
								$returnString .= "<p>Some item(s) are already exists</p>";
							}
							Session::flash('flash',$returnString);
							Redirect::to('bundle_list.php');
						} else {
							echo "not matched";
						}
					}
				?>

				<?php
					// get flash message if add or edited successfully
					if(Session::exists('flash')){
						echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
					}
				?>
				<form class="form-horizontal" action="" method="POST">
					<fieldset>
						<legend>Bundle Information</legend>
						<div class="row">
							<label class="col-md-1 control-label" for="">Bundle Item</label>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control selectitem' id='item_set' name='item_set' placeholder='Set Item'>
								</div>
							</div>
						</div>
						<?php for($num = 1; $num<= 10; $num++){
							?>
							<div class="row">
								<label class="col-md-1 control-label" for="">Child item</label>

								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control item_spare' name='item_spare[]' placeholder='Item'>
									</div>
								</div>

								<label class="col-md-1 control-label" for="">Qty</label>

								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control item_spare_qty' name='item_spare_qty[]' placeholder='Qty'>
									</div>
								</div>

							</div>
							<?php
						}?>



						<div id="more_content"></div>
						<div class="row">
							<div class="col-md-6"></div>
							<div class='col-md-6'><button class='btn btn-default' id='btnAddMore'>Add more</button></div>
						</div>

						<!-- Button (Double) -->
						<div class="form-group">
							<label class="col-md-1 control-label" for=""></label>
							<div class="col-md-8">
								<input type='submit' class='btn btn-success' name='btnSave' value='SAVE' />
								<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
							</div>
						</div>

					</fieldset>
				</form>
			</div>
		</div>
	</div>
	<!-- end page content wrapper-->
	<script>

		$(document).ready(function() {
			function formatItem(o) {
				if(!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> " + r[0] + "</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>" + r[2] + "</small></span>";
				}
			}
			var ctr = 1;
			$('body').on('click','#btnAddMore',function(e){
				e.preventDefault();
				var id = "sp_" + ctr;
				ctr = parseInt(ctr) + 1;
				var addmorecontent = '<div class="row"><label class="col-md-1 control-label" for="">Item</label><div class="col-md-3"><div class="form-group"><input type="text" id="'+id+'" class="form-control item_spare" name="item_spare[]" placeholder="Item"></div></div><label class="col-md-1 control-label" for="">Qty</label><div class="col-md-3"><div class="form-group"><input type="text" class="form-control item_spare_qty" name="item_spare_qty[]" placeholder="Qty"></div></div></div>';
				$('#more_content').append(addmorecontent);
				$("#"+id).select2({
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
						data: function(term) {
							return {
								search: term, functionName: 'searchItemJSON'
							};
						},
						results: function(data) {
							return {
								results: $.map(data, function(item) {
									return {
										text: item.barcode + ":" + item.item_code + ":" + item.description + ":" + item.price,
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
			});
			$(".item_spare").select2({
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
					data: function(term) {
						return {
							search: term, functionName: 'searchItemJSON'
						};
					},
					results: function(data) {
						return {
							results: $.map(data, function(item) {
								return {
									text: item.barcode + ":" + item.item_code + ":" + item.description + ":" + item.price,
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

			$('body').on('change', '.item_spare', function() {
				var cur_sp = $(this).val();
				var m = 0;
				$('.item_spare').each(function() {
					var sp = $(this);
					if(sp.val() == cur_sp) {
						m = parseInt(m) + 1;
					}
				});
				if(cur_sp){
					if(m > 1) {
						alert('Duplicate spare item');
						$(this).select2('val', null);
					}
				}

			});
		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>