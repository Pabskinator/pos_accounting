<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('branch')) {
		// redirect to denied page
		Redirect::to(1);
	}

	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

	$edit_item_id = 0;
	$edit_item_code = "";
	$edit_agent_id= 0;
	$edit_agent_name= "";
	if(isset($editid) && !empty($editid)) {
		// edit
		$id = Encryption::encrypt_decrypt('decrypt', $editid);
		// get the data base on branch id
		$ci = new Commission_item($id);
		$edit_prod = new Product($ci->data()->item_id);
		$edit_item_id = $edit_prod->data()->id;
		$edit_item_code = $edit_prod->data()->barcode. ":".$edit_prod->data()->item_code. ":".$edit_prod->data()->description;

		if($ci->data()->agent_id){
			$agent = new User($ci->data()->agent_id);
			$edit_agent_name= $agent->data()->firstname . " " . $agent->data()->lastname;
			$edit_agent_id= $agent->data()->id;
		}

	}
	// if submitted
	if (Input::exists()){
		// check token if match to our token
		dump(Input::get('item_id'));
		dump(Input::get('agent_id'));


		if(Token::check(Input::get('token'))){

			$validation_list = array(

			);


			if(Input::get('amount') || Input::get('perc')){
				$validate = new Validate();
				$validate->check($_POST, $validation_list);
				if($validate->passed()){
					$os = new Commission_item();
					$amount = Input::get('amount') ?  Input::get('amount') : 0;
					$perc = Input::get('perc') ?  Input::get('perc') : 0;

					//edit codes


						$agent_ids = Input::get('agent_id');
						$item_ids = Input::get('item_id');


							if($agent_ids && count($agent_ids)){
								foreach($agent_ids as $aid){
									try {
										if($item_ids && count($item_ids)){
											foreach($item_ids as $item_id){
												$comlist = new Commission_item();
												$checker = $comlist->hasComission($item_id,$aid);
												if(isset($checker->id) && $checker->id){
													$arrupdate = array(
														'item_id' => $item_id,
														'amount' => $amount,
														'perc' => $perc,
														'agent_id' => $aid
													);

													$os->update($arrupdate, $checker->id);
												} else {

													$inserarr = array(
														'item_id' => $item_id,
														'amount' => $amount,
														'perc' => $perc,
														'created' => strtotime(date('Y/m/d H:i:s')),
														'is_active' => 1,
														'company_id' => $user->data()->company_id,
														'agent_id' => $aid
													);

													$os->create($inserarr);

												}

											}

										}
									} catch(Exception $e){
										die($e);
									}
								}
							} else {
								if($item_ids && count($item_ids)){
									foreach($item_ids as $item_id){
										$comlist = new Commission_item();
										$checker = $comlist->hasComission($item_id,0);
										if(isset($checker->id) && $checker->id){
											$arrupdate = array(
												'item_id' => $item_id,
												'amount' => $amount,
												'perc' => $perc,
												'agent_id' => 0
											);

											$os->update($arrupdate, $checker->id);
										} else {

											$inserarr = array(
												'item_id' => $item_id,
												'amount' => $amount,
												'perc' => $perc,
												'created' => strtotime(date('Y/m/d H:i:s')),
												'is_active' => 1,
												'company_id' => $user->data()->company_id,
												'agent_id' => 0
											);

											$os->create($inserarr);

										}

									}

								}
							}



						Session::flash('flash','You have successfully added a record');
						Redirect::to('commission_item.php');

				} else {
					$el ='';
					echo "<div class='alert alert-danger'>";
					foreach($validate->errors() as $error){
						$el.= escape($error) . "<br/>" ;
					}
					echo "$el</div>";
				}
			} else {

				echo "<div class='alert alert-danger'>";
				echo "Enter amount or percentage";
				echo "</div>";
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT RECORD" : "ADD RECORD"; ?>
				</h1>
			</div>

			<div class="row">
				<div class="col-md-12">
					<form class="form-horizontal" action="" method="POST">
						<fieldset>
							<legend>Details</legend>
							<div class="form-group">
								<label class="col-md-4 control-label" for="item_id">Item</label>
								<div class="col-md-4">
									<select class='form-control' name="item_id[]" id="item_id" multiple>
										<option value=""></option>
										<?php
											$item_cls = new Product();
											$items = $item_cls->get_active('items',[1,'=',1]);
											if($items){
												foreach($items as $ii){
													echo "<option value='$ii->id'>". $ii->item_code . " " . $ii->description. "</option>";
												}
											}
										?>
									</select>
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="amount">Amount</label>
								<div class="col-md-4">
									<input autocomplete="off" id="amount" name="amount" placeholder="Enter Exact Amount" class="form-control input-md" type="number" value="<?php echo isset($id) ? escape($ci->data()->amount) : escape(Input::get('amount')); ?>">
									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label"></label>
								<div class="col-md-4 text-center" ><strong>OR</strong></div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="perc">Percentage</label>
								<div class="col-md-4">
									<div class="input-group">
										<input autocomplete="off" value="<?php echo isset($id) ? escape($ci->data()->perc) : escape(Input::get('perc')); ?>" type="number" id="perc" name="perc" class="form-control" placeholder="Enter Percentage" aria-describedby="basic-addon2">
										<span class="input-group-addon" id="basic-addon2">%</span>
									</div>

									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-4 control-label" for="agent_id">Name</label>
								<div class="col-md-4">

									<select class='form-control' name="agent_id[]" id="agent_id" multiple>
										<option value=""></option>
										<?php
											$user_list = new User();
											$users = $user_list->get_active('users',[1,'=',1]);
											if($users){
												foreach($users as $uu){
													echo "<option value='$uu->id'>". $uu->firstname . " " . $uu->lastname. "</option>";
												}
											}
										?>
									</select>
									<span class="help-block">For specific agent only </span>
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

			var agentcon = $("#agent_id");
			var itemcon = $("#item_id");

			var item_id = '<?php echo $edit_item_id; ?>';
			var item_code = '<?php echo $edit_item_code; ?>';
			var agent_id = '<?php echo $edit_agent_id; ?>';
			var agent_name = '<?php echo $edit_agent_name; ?>';

			$('#amount').keyup(function(){
				var v = $(this).val();
				if(v){
					$('#perc').attr('disabled',true);
				} else {
					$('#perc').attr('disabled',false);
				}
				$('#perc').val('');
			});

			$('#perc').keyup(function(){
				var v = $(this).val();

				if(parseFloat(v) > 100){
					alert("Invalid number");
					$('#perc').val('');
				}

				if($('#perc').val()){
					$('#amount').attr('disabled',true);
				} else {
					$('#amount').attr('disabled',false);
				}

				$('#amount').val('');
			});

			agentcon.select2({
				placeholder: 'Select Agent',
				allowClear: true
			});

			itemcon.select2({
				placeholder: 'Select Item',
				allowClear: true
			});

			if(item_id != '0'){
				$('#item_id').select2('val',[item_id]);
			}

			if(agent_id != '0'){
				$('#agent_id').select2('val',[agent_id]);
			}

			$('body').on('click','#addAllAgent',function(e){
				e.preventDefault();
				var arr = [];
				$('#agent_id option').each(function(){
					if($(this).val()){
						arr.push($(this).val());
					}
				});
				$('#agent_id').select2('val',arr);
			});

		});
	</script>
<?php
	require_once '../includes/admin/page_tail2.php';