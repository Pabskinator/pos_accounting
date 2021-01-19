<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('wo_mod')) {
		// redirect to denied page
		Redirect::to(1);
	}

	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}
	$item_code = '';
	$item_id = '0';
	if(isset($editid) && !empty($editid)) {
		// edit
		$id = Encryption::encrypt_decrypt('decrypt', $editid);
		// get the data base on branch id
		$assembly = new Assemble_item_for_order($id);
		if($assembly->data()->item_id){
			$prod = new Product($assembly->data()->item_id);
			$item_code = $prod->data()->barcode . ":" . $prod->data()->item_code . ":" .$prod->data()->description;
			$item_id = $prod->data()->id;
		}
	}

	// if submitted
	if (Input::exists()){
		// check token if match to our token
		if(Token::check(Input::get('token'))){

			$validation_list = array(
				'item_id' => array(
					'required'=> true,
					'max' => 50
				),
				'min_qty' => array(
					'required'=> true,
					'number' => true
				)
			);
			// get id in update

			if(!Input::get('edit')) {
				$additionalvalidation = array('unique' => 'assemble_item_for_orders');
				$finalvalidation=array_merge($validation_list['item_id'],$additionalvalidation);
				$validation_list['item_id'] = $finalvalidation;
			}


			$validate = new Validate();
			$validate->check($_POST, $validation_list);
			if($validate->passed()){
				$assembly = new Assemble_item_for_order();
				//edit codes
				if(Input::get('edit')){
					$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
					try{
						$arrupdate = array(
							'item_id' => Input::get('item_id'),
							'min_qty' => Input::get('min_qty')
						);

						$assembly->update($arrupdate, $id);
						Session::flash('flash','Item information has been successfully updated');
						Redirect::to('assembly_item_for_orders.php');
					} catch(Exception $e) {
						die($e->getMessage());
					}
				} else {
					// insert codes
					try {
						$inserarr = array(
							'item_id' => Input::get('item_id'),
							'min_qty' => Input::get('min_qty'),
							'is_active' => 1,
							'created' => time(),
							'company_id' => $user->data()->company_id,
						);
						$assembly->create($inserarr);
					} catch(Exception $e){
						die($e);
					}
					Session::flash('flash','You have successfully added an entry');
					Redirect::to('assembly_item_for_orders.php');
				}
			} else {
				$el ='';
				echo "<div class='alert alert-danger'>";
				foreach($validate->errors() as $error){
					$el.= escape($error) . "<br/>" ;
				}
				echo "$el</div>";
			}
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT ITEM" : "ADD ITEM"; ?>
				</h1>
			</div>

			<div class="row">
				<div class="col-md-12">
					<form class="form-horizontal" action="" method="POST">
						<fieldset>
							<legend>Item Information</legend>
							<div class="form-group">
								<label class="col-md-3 control-label" for="item_id">Item</label>
								<div class="col-md-6">
									<input id="item_id" name="item_id"  class="form-control input-md" type="text" >
									<span class="help-block">Enter item name</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label" for="min_qty">Minimun Qty</label>
								<div class="col-md-6">
									<input id="min_qty" name="min_qty"  class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($assembly->data()->min_qty) : escape(Input::get('min_qty')); ?>">
									<span class="help-block">Minimum qty item for assembly</span>
								</div>
							</div>
							<!-- Button (Double) -->
							<div class="form-group">
								<label class="col-md-4 control-label" for="button1id"></label>
								<div class="col-md-8">
									<input type='submit' class='btn btn-success' name='btnSave' value='SAVE'/>
									<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
									<input type='hidden' name='edit' value=<?php echo isset($id) ? escape(Encryption::encrypt_decrypt('encrypt',$id)): 0; ?>>

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
			var item_id_sel = $('#item_id');
			var item_id = '<?php echo isset($id) ? escape($assembly->data()->item_id) : escape(Input::get('item_id')); ?>';
			var item_code = '<?php  echo $item_code; ?>';

			function formatItem(o) {

				if(!o.id)
					return o.text; // optgroup
				else {
					var r = o.text.split(':');
					return "<span> " + r[0] + "</span> <span style='margin-left:10px'>" + r[1] + "</span><span style='display:block;margin-top:5px;'  class='text-danger'><small class='testspanclass'>" + r[2] + "</small></span>";
				}
			}


			$("#item_id").select2({
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
									is_bundle: item.is_bundle,
									unit_name: item.unit_name,
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

			if(item_id){
				item_id_sel.select2('data',{ id: item_id, text: item_code });
			}
		});
	</script>
<?php
	require_once '../includes/admin/page_tail2.php';