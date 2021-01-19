
<!-- Page content -->
<div id="page-content-wrapper">

	<!-- Keep all page content within the page-content inset div! -->
	<div class="page-content inset">
		<div class="content-header">
			<h1>
				<span id="menu-toggle" class='glyphicon glyphicon-list'></span>
				<?php echo isset($editid) && !empty($editid) ? "EDIT ACCOUNT TITLE" : "ADD ACCOUNT TITLE"; ?>
			</h1>
		</div>
		<div class="row">
			<div class="col-md-12">

				<?php
					if(isset($editid) && !empty($editid)) {
						// edit
						$id = Encryption::encrypt_decrypt('decrypt', $editid);
						// get the data base on branch id
						$editAcc= new Account_title($id);
					}

					// if submitted
					if (Input::exists()){
						// check token if match to our token
						if(Token::check(Input::get('token'))){
							$validation_list = array(
								'name' => array(
									'required'=> true,
									'max' => 50
								)

							);
							if(!Input::get('edit')) {
								$additionalvalidation = array('unique' => 'account_titles');
								$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
								$validation_list['name'] = $finalvalidation;
							}
							$validate = new Validate();
							$validate->check($_POST, $validation_list);
							if($validate->passed()){
								$newcategory = new Account_title();
								//edit codes
								if(Input::get('edit')){
									$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
									try{
										$parentid=  (Input::get('category_id')=='') ? 0: Input::get('category_id');
										$newcategory->update(array(
											'name' => Input::get('name'),
											'parent_id' =>$parentid,
											'modified' => strtotime(date('Y/m/d H:i:s'))
										), $id);
										Session::flash('flash','Account title information has been successfully updated');
										Redirect::to('account-titles.php');
									} catch(Exception $e) {
										die($e->getMessage());
									}
								} else {
									// insert codes
									try {
										$parentid=  (Input::get('category_id')=='') ? 0: Input::get('category_id');
										$newcategory->create(array(
											'name' => Input::get('name'),
											'parent_id' => $parentid,
											'company_id' => $user->data()->company_id,
											'is_active' => 1,
											'created' => strtotime(date('Y/m/d H:i:s')),
											'modified' => strtotime(date('Y/m/d H:i:s'))
										));
										$lastid = $newcategory->getInsertedId();

									} catch(Exception $e){
										die($e);
									}
									Session::flash('flash','You have successfully added an account title');
									Redirect::to('account-titles.php');
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

				<form class="form-horizontal" action="" method="POST">
					<fieldset>


						<legend>Account Title Information</legend>


						<div class="form-group">
							<label class="col-md-4 control-label" for="name">Account Title</label>
							<div class="col-md-4">
								<input id="name" name="name" placeholder="Account Title" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editAcc->data()->name) : escape(Input::get('name')); ?>">
								<span class="help-block">Account Title Name</span>
							</div>
						</div>



						<div class="form-group">
							<label class="col-md-4 control-label" for="category_id">Parent</label>
							<div class="col-md-4">
								<?php

									$ccc = new Account_title();
									$cc = objectToArray($ccc->getAcc($user->data()->company_id));
									$array = array();
									$a = isset($id) ? $editAcc->data()->parent : escape(Input::get('category_id'));
									function get_nested($array,$child = FALSE,$iischild='',$selectedid=0,$conid=0){

										$str = '';
										$mycateg = new Account_title();
										$thisuser = new User();
										if (count($array)){
											$iischild .= $child == FALSE ? '' : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

											foreach ($array as $item){

												$haschild = $mycateg->hasChild($thisuser->data()->company_id,$item['id']);
												$hideme='';
												if($haschild){
													//	$disabledme = 'disabled';
												}

												if($item['id'] == $conid) {
													$hideme = 'display:none;';
												}
												if($selectedid){
													if($selectedid == $item['id']){
														$selected = 'selected';
														$selectedid=0;
													}

												} else {
													$selected = '';
												}


												if(isset($item['children']) && count($item['children'])){
													$str .= '<option style="'.$hideme.'" value="'.$item['id'].'" '.$selected.'>'.$iischild.$item['name'].'</option>';
													$str .= get_nested($item['children'], true, $iischild,$selectedid,$conid);
												} else {
													if($child == false) $iischild='';
													$str .= '<option style="'.$hideme.'" value="'.$item['id'].'" '.$selected.'>'.$iischild.($item['name']).'</option>';
												}

											}
										}

										return $str;
									}

									function objectToArray ($object) {
										if(!is_object($object) && !is_array($object))
											return $object;

										return array_map('objectToArray', (array) $object);
									}
									function makeRecursive($d, $r = 0, $pk = 'parent_id', $k = 'id', $c = 'children') {
										$m = array();
										foreach ($d as $e) {
											isset($m[$e[$pk]]) ?: $m[$e[$pk]] = array();
											isset($m[$e[$k]]) ?: $m[$e[$k]] = array();
											$m[$e[$pk]][] = array_merge($e, array($c => &$m[$e[$k]]));
										}

										return $m[$r]; // remove [0] if there could be more than one root nodes
									}

								?>
								<select id="category_id" name="category_id" class="form-control hasChild" >
									<option value="">--Choose Category--</option>
									<?php
										if(isset($id)){
											echo get_nested(makeRecursive($cc), FALSE,'',$a,$id);
										} else {
											echo get_nested(makeRecursive($cc));
										}
									?>


								</select>
								<span class="help-block">Choose item here if it belongs to a general category.</span>
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
