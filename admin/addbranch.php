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
	$sub_company_class = new Sub_company();
	$sub_companies = $sub_company_class->get_active('sub_companies',array('company_id','=',$user->data()->company_id));
	$count_sub_companies = count($sub_companies);
	$member_id =0;
	$member_name ='';
	if(isset($editid) && !empty($editid)) {
		// edit
		$id = Encryption::encrypt_decrypt('decrypt', $editid);
		// get the data base on branch id
		$branch = new Branch($id);

		if($branch->data()->member_id){
			$member_id = $branch->data()->member_id;
			$member_details = new Member($member_id);
			$member_name = $member_details->data()->lastname;
		}
	}
	// if submitted
	if (Input::exists()){
		// check token if match to our token
		if(Token::check(Input::get('token'))){

			$validation_list = array(
				'name' => array(
					'required'=> true,
					'max' => 50
				),
				'description' => array(
					'required'=> true,
					'min' => 2,
					'max'=>200
				),
				'address' => array(
					'required'=> true,
					'min' => 6,
					'max'=>200
				),
				'sub_company' => array(
					'max'=>200
				)
			);
			// get id in update

			if(!Input::get('edit')) {
				$additionalvalidation = array('unique' => 'branches');
				$finalvalidation=array_merge($validation_list['name'],$additionalvalidation);
				$validation_list['name'] = $finalvalidation;
			}
			if($count_sub_companies > 0){
				$additionalvalidation = array('required' => true);
				$finalvalidation=array_merge($validation_list['sub_company'],$additionalvalidation);
				$validation_list['sub_company'] = $finalvalidation;
			}

			$validate = new Validate();
			$validate->check($_POST, $validation_list);
			if($validate->passed()){
				$branch = new Branch();
				//edit codes
				if(Input::get('edit')){
					$id = Encryption::encrypt_decrypt('decrypt',Input::get('edit'));
					try{
						$arrupdate = array(
							'name' => Input::get('name'),
							'description' => Input::get('description'),
							'address' => Input::get('address'),
							'member_id' => Input::get('member_id')
						);
						if($count_sub_companies > 0){
							$arrupdate['sub_company'] = Input::get('sub_company');
						}
						if(Input::get('member_id')){
							$arrupdate['member_id'] = Input::get('member_id');
						}
						if(Configuration::getValue('branch_tag') == 1){
							$arrupdate['branch_tag'] = Input::get('branch_tag');
							$arrupdate['branch_tag_order'] = implode(',',Input::get('branch_tag_order'));
						}

						$branch->update($arrupdate, $id);
						Log::addLog($user->data()->id,$user->data()->company_id,"Update Branch ".Input::get('name'),"addbranch.php");

						Session::flash('branchflash','Branch information has been successfully updated');
						Redirect::to('branch.php');
					} catch(Exception $e) {
						die($e->getMessage());
					}
				} else {
					// insert codes
					try {
						$inserarr = array(
							'name' => Input::get('name'),
							'description' => Input::get('description'),
							'address' => Input::get('address'),
							'created' => strtotime(date('Y/m/d H:i:s')),
							'modified' => strtotime(date('Y/m/d H:i:s')),
							'company_id' => $user->data()->company_id,
							'member_id' => Input::get('member_id')
						);
						if($count_sub_companies > 0){
							$inserarr['sub_company'] = Input::get('sub_company');
						}
						if(Configuration::getValue('branch_tag') == 1){
							$inserarr['branch_tag'] = Input::get('branch_tag');
							$inserarr['branch_tag_order'] = (Input::get('branch_tag_order')) ? implode(',',Input::get('branch_tag_order')) : '' ;
						}
						Log::addLog($user->data()->id,$user->data()->company_id,"Insert Branch ".Input::get('name'),"addbranch.php");
						$branch->create($inserarr);
					} catch(Exception $e){
						die($e);
					}
					Session::flash('branchflash','You have successfully added a branch');
					Redirect::to('branch.php');
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
 require_once 'views/branch/addbranch.view.php';
 require_once '../includes/admin/page_tail2.php';