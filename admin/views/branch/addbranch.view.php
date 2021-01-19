<!-- Page content -->
<div id="page-content-wrapper">
	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				<?php echo isset($editid) && !empty($editid) ? "EDIT BRANCH" : "ADD BRANCH"; ?>
			</h1>
		</div>
		<?php 	include "includes/branch_nav.php"; ?>
		<div class="row">
			<div class="col-md-12">
				<form class="form-horizontal" action="" method="POST">
					<fieldset>
						<legend>Branch Information</legend>
						<div class="form-group">
							<label class="col-md-4 control-label" for="name">Branch</label>
							<div class="col-md-4">
								<input id="branchName" name="name" placeholder="Branch Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($branch->data()->name) : escape(Input::get('name')); ?>">
								<span class="help-block">The name of the branch can consists of letters and numbers</span>
							</div>
						</div>
						<!-- Text input-->
						<div class="form-group">
							<label class="col-md-4 control-label" for="d">Branch Description</label>
							<div class="col-md-4">
								<input id="branchDescription" name="description" placeholder="Description" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($branch->data()->description) :  escape(Input::get('description')); ?>">
								<span class="help-block">Description for your Branch</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="address">Branch Address</label>
							<div class="col-md-4">
								<input id="address" name="address" placeholder="Address" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($branch->data()->address) :  escape(Input::get('address')); ?>">
								<span class="help-block">Address of your Branch</span>
							</div>
						</div>
						<?php
							if($count_sub_companies > 0){
								?>

								<div class="form-group">
									<label class="col-md-4 control-label" for="sub_company">Company</label>
									<div class="col-md-4">
										<select id="sub_company" name="sub_company" class="form-control">
											<option value=''>--Select Company--</option>
											<?php
												foreach($sub_companies as $b){
													$a = isset($id) ? $branch->data()->sub_company : escape(Input::get('sub_company'));

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
										<span class="help-block">Company where this branch belong</span>
									</div>
								</div>
								<?php
							}
						?>
						<div class="form-group">
							<label class="col-md-4 control-label" for="member_id"><?php echo MEMBER_LABEL; ?></label>
							<div class="col-md-4">
								<input id="member_id" name="member_id" placeholder="<?php echo MEMBER_LABEL; ?>" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($branch->data()->member_id) :  escape(Input::get('member_id')); ?>">
								<span class="help-block">Name of the franchisee</span>
							</div>
						</div>
						<?php if(Configuration::getValue('branch_tag') == 1 ){
							$branch_tag = new Branch_tag();
							$branch_tags = $branch_tag->get_active('branch_tags',['company_id','=',$user->data()->company_id]);
							?>
							<div class="form-group">

									<?php
										if($branch_tags){
											?>
								<label class="col-md-4 control-label" for="branch_tag">Branch Tag</label>
								<div class="col-md-4">
											<select name="branch_tag" id="branch_tag" class='form-control'>
												<?php
													foreach($branch_tags as $btag){
														$selectedtag = '';
														if(isset($id) && $branch->data()->branch_tag == $btag->id){
															$selectedtag ='selected';
														}
														?>
														<option value="<?php echo $btag->id; ?>" <?php echo $selectedtag; ?>><?php echo $btag->name; ?></option>
														<?php
													}
												?>
											</select>
											<?php
										}
									?>
								</div>
							</div>
							<div class="form-group">

									<?php
										if($branch_tags){
											?>
								<label class="col-md-4 control-label" for="branch_tag_order">Branch Tag Order</label>
								<div class="col-md-4">
											<select name="branch_tag_order[]" id="branch_tag_order" class='form-control' multiple>
												<option value=""></option>
												<?php
													foreach($branch_tags as $btag){
														?>
														<option  value="<?php echo $btag->id; ?>" ><?php echo $btag->name; ?></option>
														<?php
													}
												?>
											</select>
											<?php
										}
									?>
								</div>
							</div>
							<?php
						}?>

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
		$('#branch_tag_order').select2({
			allowClear: true,
			placeholder: 'Enter Tag'
		});
		<?php if(isset($id)){
			?>
		var branch_tag = "<?php echo $branch->data()->branch_tag_order ?>";
		var bsp = [];
		if(branch_tag.indexOf(',') >0){
			bsp = branch_tag.split(',');
		} else {
			if(branch_tag){
				bsp.push(branch_tag);
			}
		}
		$('#branch_tag_order').select2({
			allowClear: true
		}).select2('val',bsp);
			<?php
		}?>
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