<?php

require_once '../includes/admin/page_head2.php';

if(!$user->hasPermission('department')) {
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

    <div class="page-content inset">

        <div class="content-header">
            <h1>
                <span id="menu-toggle" class='glyphicon glyphicon-list'></span>
                <?php echo isset($editid) && !empty($editid) ? "EDIT DEPARTMENT" : "ADD DEPARTMENT"; ?>
            </h1>
        </div>

				<div class="row">
						<div class="col-md-12">

								<?php
               			 // if edit
										if(isset($editid) && !empty($editid)) {

												//get the id
												$id = Encryption::encrypt_decrypt('decrypt', $editid);

												// get the data base on branch id
												$editdept = new Department();
                        $editdepts = $editdept->getAllDepts($user->data()->company_id,$id);

										}

										// if submitted
										if (Input::exists()){

										    //override the head_id array
										    $_POST['head_id'] = implode(',',$_POST['head_id']);

												// check token if match to our token
												if(Token::check(Input::get('token'))) {

                            $validation_list = array(

                                'name' => array(
                                    'required'=> true,
                                    'max' => 50
                                ),
                                'head_id' => array(
                                    'required'=> true,
                                    'max' => 50
                                ),

                            );

                            if(!Input::get('edit')) {

                                $additionalvalidation = array('unique' => 'acc_departments');
                                $finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
                                $validation_list['name'] = $finalvalidation;

                            }

                            //validate data
                            $validate = new Validate();
                            $validate->check($_POST, $validation_list);

                            //if passed
                            if($validate->passed()){

                                $newDept = new Department();

                                //codes of edit dept
                                if(Input::get('edit')){

                                    $id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));

                                    try{

                                        //get data
                                        $deptInfo = array(
                                            'name' => Input::get('name'),
                                            'head_id' => Input::get('head_id'),
                                            'is_active' => 1,
                                            'company_id' => $user->data()->company_id,
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        );

                                        //add log
                                        Log::addLog($user->data()->id,
																						$user->data()->company_id,
																						"Update Department ". Input::get('name'),
																						"adddepartment.php");

																				//update user
																				if(!$newDept->update($deptInfo, $id)) {
                                            Session::flash('deptflash', 'You have successfully updated a department');
                                            Redirect::to('department.php');
                                        }

																		}catch(Exception $e) {
                                        die($e->getMessage());
                                    }

                                }
                                //codes of add dept
                                else{

                                		try{

                                				//get data
                                        $deptInfo = array(
                                            'name' => Input::get('name'),
                                            'head_id' => Input::get('head_id'),
                                            'is_active' => 1,
                                            'company_id' => $user->data()->company_id,
                                            'created_at' => date('Y-m-d H:i:s'),
                                        );

                                        //add log
                                        Log::addLog($user->data()->id,
																										$user->data()->company_id,
																						"Insert Department ". Input::get('name'),
																							"adddepartment.php");

                                        //add dept
                                        if(!$newDept->create($deptInfo)){

                                            Session::flash('deptflash', 'You have successfully added a department');
                                            Redirect::to('department.php');

																				}

																		}catch(Exception $e){

                                        die($e);

                                    }

																}

                            }else{

                                $el ='';
                                echo "<div class='alert alert-danger'>";
                                foreach($validate->errors() as $error){
                                    $el.=escape($error) . "<br/>" ;
                                }
                                echo "$el</div>";

                            }

                        }

                    }

								?>

								<form class="form-horizontal" method='POST' action=''>
										<fieldset>

												<!-- Form Name -->
												<legend>Department Information</legend>

												<div class="form-group">

														<label class="col-md-1 control-label" for="name" >Name</label>
														<div class="col-md-4">
															<input id="name" name="name" placeholder="Department Name" class="form-control input-md" type="text" value='<?php echo isset($id) ? escape($editdepts->name) : escape(Input::get('name')); ?>'>
															<span class="help-block"></span>
														</div>

														<label class="col-md-1 control-label" for="head_id">Department Head(s)</label>
														<div class="col-md-4">
																<select id="head_id" name="head_id[]" class="form-control" multiple="multiple">

																		<?php

																				$user = new User();
																				$users =  $user->get_active('users',array('company_id' ,'=',$user->data()->company_id));

                                        $a = isset($id) ? explode(',', $editdepts->head_id) : escape(Input::get('head_id'));

                                        foreach($users as $u){

                                            $emp = $u->lastname . ', ' . $u->firstname;

                                            ?>
                                                <option value='<?php echo $u->id ?>'><?php echo $emp;?> </option>
                                            <?php

                                        }

                                        ?>

                                            <script>

                                                if(<?php echo json_encode($a) ?>){

                                                    $("#head_id").val(<?php echo json_encode($a); ?>);

                                                }

                                            </script>

                                        <?php

																		?>
																</select>
														</div>

												</div>

												<div class="form-group">

														<div class="col-md-4">
															<input id="btnSave" name="btnSave" class="btn btn-success" type='submit' value='SAVE'>
															<input type='hidden' name='token' value=<?php echo Token::generate(); ?>>
															<input type='hidden' name='edit' value=<?php echo isset($id) ? escape(Encryption::encrypt_decrypt('encrypt',$id)): 0; ?>>
														</div>

												</div>

										</fieldset>
								</form>

						</div>
				</div>

    </div>

</div>

<script>

    $(document).ready(function(){
        $('#head_id').select2({
            placeholder: 'Select department head',
            allowClear: true,
        });
    });

    // $('body').on('change', '#head_id', function() {
    //
    //     var temp = $('#head_id').val();
    //
    //     console.log(temp);
    //
    // });



</script>

<?php require_once '../includes/admin/page_tail2.php'; ?>
