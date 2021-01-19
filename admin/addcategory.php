<?php
	// $user have all the properties and method of the current user

	require_once '../includes/admin/page_head2.php';
	if(!$user->hasPermission('category')) {
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
					<?php echo isset($editid) && !empty($editid) ? "EDIT CATEGORY" : "ADD CATEGORY"; ?>
				</h1>
			</div>
			<div class="row">
				<div class="col-md-12">

					<?php
						if(isset($editid) && !empty($editid)) {
							// edit
							$id = Encryption::encrypt_decrypt('decrypt', $editid);
							// get the data base on branch id
							$editCategory = new Category($id);
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
									//$additionalvalidation = array('unique' => 'categories');
									//$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
									//$validation_list['name'] = $finalvalidation;
								}


								$validate = new Validate();
								$validate->check($_POST, $validation_list);

								if(!empty($_FILES['item_img']['name'])){
									if ($_FILES["item_img"]["error"] > 0) {
										$validate->addError("There is a problem in your images");
									}
								}
								if($validate->passed()){
									$newcategory = new Category();
									//edit codes
									if(Input::get('edit')){
										$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
										try{
											if(!empty($_FILES['item_img']['name'])){
												unlink("../uploads/categories/" .$id . ".jpg");
												move_uploaded_file($_FILES["item_img"]["tmp_name"],
													"../uploads/categories/" .$id . ".jpg");
											}
											$parentid=  (Input::get('category_id')=='') ? 0: Input::get('category_id');
											$newcategory->update(array(
												'name' => Input::get('name'),
												'parent' =>$parentid,
												'user_id' =>Input::get('user_id'),
												'modified' => strtotime(date('Y/m/d H:i:s'))
											), $id);
											Log::addLog($user->data()->id,$user->data()->company_id,"Update category ||categories:".$id,'admin/addcategory.php');
											Session::flash('categoryflash','Terminal information has been successfully updated');
											Redirect::to('category.php');
										} catch(Exception $e) {
											die($e->getMessage());
										}
									} else {
										// insert codes
										try {

											$parentid=  (Input::get('category_id')=='') ? 0: Input::get('category_id');
											$newcategory->create(array(
												'name' => Input::get('name'),
												'parent' => $parentid,
												'company_id' => $user->data()->company_id,
												'is_active' => 1,
												'user_id' =>Input::get('user_id'),
												'created' => strtotime(date('Y/m/d H:i:s')),
												'modified' => strtotime(date('Y/m/d H:i:s'))
											));
											$lastid = $newcategory->getInsertedId();
											if(!empty($_FILES['item_img']['name'])) {
												move_uploaded_file($_FILES["item_img"]["tmp_name"], "../uploads/categories/" . $lastid . ".jpg");
											}
											Log::addLog($user->data()->id,$user->data()->company_id,"Insert category ||categories:".$lastid,'admin/addcategory.php');

										} catch(Exception $e){
											die($e);
										}
										Session::flash('categoryflash','You have successfully added a Terminal');
										Redirect::to('category.php');
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

					<form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
						<fieldset>


							<legend>Category Information</legend>


							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Category Name</label>
								<div class="col-md-4">
									<input id="name" name="name" placeholder="Category Name" class="form-control input-md" type="text" value="<?php echo isset($id) ? escape($editCategory->data()->name) : escape(Input::get('name')); ?>">
									<span class="help-block">Name of the category</span>
								</div>
							</div>



							<div class="form-group">
								<label class="col-md-4 control-label" for="category_id">General Category</label>
								<div class="col-md-4">
									<?php

										$ccc = new Category();
										$cc = objectToArray($ccc->getCategory($user->data()->company_id));
										$array = array();
										$a = isset($id) ? $editCategory->data()->parent : escape(Input::get('category_id'));
										function get_nested($array,$child = FALSE,$iischild='',$selectedid=0,$conid=0){

											$str = '';
											$mycateg = new Category();
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
										function makeRecursive($d, $r = 0, $pk = 'parent', $k = 'id', $c = 'children') {
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

							<div class="form-group">
								<label class="col-md-4 control-label" for="name">Assign to</label>
								<div class="col-md-4">
									<select name="user_id" id="user_id" class='form-control'>
										<option value=""></option>
										<?php
											$user_cls = new User();
											$user_list = $user_cls->get_active('users',['company_id','=',$user->data()->company_id]);
											foreach($user_list as $ind_user){
												$a = isset($id) ? $editCategory->data()->user_id : escape(Input::get('user_id'));

												if($a==$ind_user->id){
													$selected='selected';
												} else {
													$selected='';
												}
												echo "<option $selected value='$ind_user->id'>".capitalize($ind_user->lastname . " " . $ind_user->firstname)."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<?php  if(isset($id) && file_exists("../uploads/categories/{$id}.jpg")){

								?>
								<div class='row'>
									<div class="col-md-4"></div>
									<div class="col-md-4">
										<div  class="text-right text-danger">
											<a data-id='<?php echo Encryption::encrypt_decrypt('encrypt',$id); ?>' href="#" id='btnRemoveImage'><span class='fa fa-remove'></span></a></div>
										<img style='width:100%;' src="<?php echo "../uploads/categories/{$id}.jpg"; ?>" alt="">
									</div>

									<div class="col-md-4">
									</div>
								</div>
								<br>
								<?php
							}
							?>

							<div class="form-group">
								<label class="col-md-4 control-label" for="item_img">Image</label>
								<div class="col-md-4">
									<input class="btn bg-info" id="item_img" name="item_img" placeholder="Image"  type="file">
									<span class="help-block">Category Image</span>
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
			$('#user_id').select2({
				placeholder: 'Assign to',
				allowClear: true
			});
		});
	</script>
<?php require_once '../includes/admin/page_tail2.php'; ?>