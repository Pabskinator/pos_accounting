<?php
	// $user have all the properties and method of the current user
	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('price_group')) {
		// redirect to denied page
		Redirect::to(1);
	}

	if(isset($_GET['edit'])) {
		$editid = $_GET['edit'];
	} else {
		$editid = 0;
	}

	$price_group = new Price_group();
	$price_groups = $price_group->get_active('price_groups',array('company_id','=',$user->data()->company_id));


	$member_id =0;
	$member_name ='';
	$price_group_id =0;
	$price_group_name ='';

	if(isset($editid) && !empty($editid)) {
		// edit
		$id = Encryption::encrypt_decrypt('decrypt', $editid);
		// get the data base on branch id
		$member_price_group = new Member_price_group($id);

		if($member_price_group->data()->member_id){
			$member_id = $member_price_group->data()->member_id;
			$member_details = new Member($member_id);
			$member_name = $member_details->data()->lastname;
		}
		if($member_price_group->data()->price_group_id){
			$price_group_id = $member_price_group->data()->price_group_id;
			$module_details = new Price_group($price_group_id);
			$price_group_name = $module_details->data()->name;
		}
	}

	// if submitted
	if (Input::exists()){
		// check token if match to our token
		if(Token::check(Input::get('token'))){

			$validation_list = array(
				'member_id' => array(
					'required'=> true
				),
				'price_group_id' => array(
					'required'=> true
				)
			);
			// get id in update

			$validate = new Validate();
			$validate->check($_POST, $validation_list);
			if($validate->passed()){
				$member_price_group = new Member_price_group();
				//edit codes
				if(Input::get('edit')){
					$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
					try{
						$arrupdate = array(
							'member_id' => Input::get('member_id'),
							'price_group_id' => Input::get('price_group_id')
						);
						$member_price_group->update($arrupdate, $id);
						Session::flash('flash','Information has been successfully updated');
						Redirect::to('price_group_member.php');
					} catch(Exception $e) {
						die($e->getMessage());
					}
				} else {
					// insert codes
					try {
						$inserarr = array(
							'member_id' => Input::get('member_id'),
							'price_group_id' => Input::get('price_group_id'),
							'created' => strtotime(date('Y/m/d H:i:s')),
							'company_id' => $user->data()->company_id,
							'is_active' => 1
						);


						$member_price_group->create($inserarr);

					} catch(Exception $e){
						die($e);
					}
					Session::flash('flash','You have successfully added a record');
					Redirect::to('price_group_member.php');
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
	<div id="page-content-wrapper">


		<!-- Keep all page content within the page-content inset div! -->
		<div class="page-content inset">
			<div class="content-header">
				<h1>
					<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
					Member Price Group Assignment
				</h1>

			</div>
			<div class="row">
				<div class="col-md-12">
					<form class="form-horizontal" action="" method="POST">
						<fieldset>
							<legend>Information</legend>


							<?php
								if($price_groups > 0){
									?>

									<div class="form-group">
										<label class="col-md-4 control-label" for="price_group_id">Price Group</label>
										<div class="col-md-4">
											<select id="price_group_id" name="price_group_id" class="form-control">
												<option value=''>--Select Price Group--</option>
												<?php
													foreach($price_groups as $b){
														$a = isset($id) ? $member_price_group->data()->price_group_id : escape(Input::get('price_group_id'));

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
									</div>
									<?php
								}
							?>
							<div class="form-group">
								<label class="col-md-4 control-label" for="member_id"><?php echo MEMBER_LABEL; ?></label>
								<div class="col-md-4">
									<input id="member_id" name="member_id" placeholder="<?php echo MEMBER_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($member_price_group->data()->member_id) :  escape(Input::get('member_id')); ?>">
									<span class="help-block"></span>
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

			var mem_select2 = $('#member_id');
			var member_id = '<?php echo $member_id; ?>';
			var member_name = '<?php echo $member_name; ?>';
			var MEMBER_LABEL = $('#MEMBER_LABEL').val();
			mem_select2.select2({
				placeholder: 'Search ' +MEMBER_LABEL,
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
							functionName:'members'
						};
					},
					results: function (data) {
						return {
							results: $.map(data, function (item) {
								return {
									text: item.lastname + ", " + item.firstname + " " + item.middlename,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
			if(member_id != '0'){
				mem_select2.select2('data',{ id: member_id, text: member_name });
			}
		});
	</script>
<?php	require_once '../includes/admin/page_tail2.php';