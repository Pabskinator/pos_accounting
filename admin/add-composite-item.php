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
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span> Add <?php echo Configuration::getValue('spare_part')?> </h1>
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


							$composite = new Composite_item();
							$returnString = "";
							$isext = false;
							$isadd = false;
							if(is_array($item_spare)){
								if(count($item_spare) > 0){
									$now = time();

									foreach($item_spare as $key => $val){
										if($val && $item_spare_qty[$key]){
											$exists = $composite->checkIfExists($user->data()->company_id,$item_set,$val);
											if($exists->cnt > 0){
												$isext = true;
											} else {
												$composite->create(array(
													'item_id_raw' => $val,
													'item_id_set' =>$item_set,
													'qty' => $item_spare_qty[$key],
													'created' =>$now,
													'modified' => $now,
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
								$returnString .= "<p>Item added successfully</p>";
							} else {
								$returnString .= "<p>Request failed.</p>";
							}
							if($isext){
								$returnString .= "<p>Some item(s) are already exists</p>";
							}
							Session::flash('flash',$returnString);
							Redirect::to('add-composite-item.php');
						} else {
							echo "not matched";
						}
					}
				?>
				<?php include 'includes/spare_nav.php'; ?>
				<?php
					// get flash message if add or edited successfully
					if(Session::exists('flash')){
						echo "<br/><div class='alert alert-info' style='width:90%;margin:0 auto'>".Session::flash('flash')."</div>";
					}
				?>
				<form class="form-horizontal" action="" method="POST">
					<fieldset>
						<legend>Set Item Information</legend>
						<div class="row">
							<label class="col-md-1 control-label" for="">Set Item</label>
							<div class="col-md-3">
								<div class="form-group">
									<input type="text" class='form-control selectitem' id='item_set' name='item_set' placeholder='Set Item'>
								</div>
							</div>
						</div>
						<div id="content_spare">

							<div class="row">
								<label class="col-md-1 control-label" for="">Item</label>

								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control item_spare' id='item_spare' name='item_spare' placeholder='Item'>
									</div>
								</div>

								<label class="col-md-1 control-label" for="">Qty</label>

								<div class="col-md-3">
									<div class="form-group">
										<input type="text" class='form-control item_spare_qty' id='item_spare_qty'  name='item_spare_qty' placeholder='Qty'>
									</div>
								</div>
								<label class="col-md-1 control-label" for=""></label>
								<div class="col-md-3">
									<div class="form-group">
										<button class='btn btn-default' id='btnAdd'>Add</button>

									</div>
								</div>

							</div>

						</div>

						<div id='tbl'>
							<table id='tblSpare' class='table table-bordered'>
								<thead>
									<tr>
										<th>Item</th>
										<th>Qty</th>
										<th></th>
									</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
							<div id='no-item' class='alert alert-info'>No item yet.</div>
						</div>

						<!-- Button (Double) -->
						<div class="form-group">
							<label class="col-md-1 control-label" for=""></label>
							<div class="col-md-8">
								<input type='submit' class='btn btn-success' name='btnSave' id='btnSave' value='SAVE' />
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
			$('body').on('click','#btnSave',function(e){
				e.preventDefault();
				var item_set = $('#item_set').val();
				var arr=[];
				if(item_set){

					$('#tblSpare tbody tr').each(function(){

						var row = $(this);
						var item_id = row.attr('data-item_id');
						var qty = row.attr('data-qty');

						arr.push( { item_id:item_id, qty:qty } );

					});

					$.ajax({
					    url:'../ajax/ajax_inventory.php',
					    type:'POST',
					    data: { functionName:'addSpare', data:JSON.stringify(arr), item_set:item_set },
					    success: function(data){
						    localStorage.removeItem('spare_local_db');
						    alertify.alert(data,function(){
							    location.href='add-composite-item.php'
						    });

					    },
					    error:function(){ }
					});

				} else {
					alert("Enter set item");
				}


			});

			function formatItem(o) {
				if(!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> " + r[0] + "</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>" + r[2] + "</small></span>";
				}
			}
			loadLocal();
			function loadLocal(){
				if(localStorage['spare_local_db']){
					alertify.confirm("You have unsaved work, do you want to load it?",function(e){
						if(e){
							$('#tblSpare tbody').html(localStorage['spare_local_db']);
							withContent();
						}

					})
				}
			}

			function saveLocal(){
				var local = $('#tblSpare tbody').html();
				localStorage['spare_local_db'] = local;

			}
			var ctr = 1;
			$('body').on('click','#btnAdd',function(e){
				e.preventDefault();
				var item = $('#item_spare');
				var qty = parseFloat($('#item_spare_qty').val());
				var item_id = item.val();

				if(item_id && qty){
					var item_data = item.select2('data').text;
					var split = item_data.split(':');
					var item_code = split[1];
					var description = split[2];
					item_code = item_code + "<small class='span-block'>"+description+"</small>";
					var ex = false;
					$('#tblSpare tbody tr').each(function(){
						var row = $(this);
						var item_id_ex = row.attr('data-item_id');
						if(item_id_ex && item_id_ex == item_id){
							ex = true;
						}
					});
					if(ex){
						alert("Already exists");
					} else {
						$('#tblSpare tbody').append("<tr data-item_id='"+item_id+"' data-qty='"+qty+"'><td>"+item_code+"</td><td>"+qty+"</td><td><button class='btn btn-danger btn-sm btnRemove'><i class='fa fa-remove'></i></button></td></tr>");
						$('#item_spare_qty').val('');
						item.select2('val',null);
						saveLocal();
						withContent();
					}

				} else {
					alert("Invalid Data");
					withContent();
				}
			});
			$('body').on('click','.btnRemove',function(e){
				e.preventDefault();
				$(this).parents('tr').remove();
				withContent();

			});
			withContent();
			function withContent(){
				if($('#tblSpare tbody tr').length > 0){
					$('#tblSpare').show();
					$('#no-item').hide();
					$('#btnSave').show();
				} else {
					$('#tblSpare').hide();
					$('#no-item').show();
					$('#btnSave').hide();
				}
			}

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


		});


	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>